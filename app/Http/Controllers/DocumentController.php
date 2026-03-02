<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\EImzoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DocumentController extends Controller
{
    protected EImzoService $eimzoService;

    public function __construct(EImzoService $eimzoService)
    {
        $this->eimzoService = $eimzoService;
    }

    public function index(): View
    {
        $documents = Auth::user()->documents()->latest()->paginate(10);
        return view('documents.index', compact('documents'));
    }

    public function create(): View
    {
        return view('documents.create');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $document = Auth::user()->documents()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Document created successfully',
            'document' => $document,
            'redirect' => route('documents.show', $document),
        ]);
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);
        return view('documents.show', compact('document'));
    }

    public function sign(Request $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $request->validate([
            'pkcs7' => 'required|string',
        ]);

        $pkcs7b64 = $request->input('pkcs7');

        try {
            // Decode PKCS7 and extract certificate info using OpenSSL (no server needed)
            $pkcs7Data = base64_decode($pkcs7b64);
            $certInfo = $this->extractSignerFromPKCS7($pkcs7Data);

            if (!$certInfo) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Imzo sertifikatini o\'qishda xatolik',
                ], 400);
            }

            // Use authenticated user's name (from PFX alias, already correct)
            $signerName = Auth::user()->name;
            $signerPinfl = $certInfo['pinfl'] ?? Auth::user()->pinfl;
            $signerInn = $certInfo['inn'] ?? Auth::user()->inn;
            $signerOrg = Auth::user()->organization;

            $document->update([
                'pkcs7_signature' => $pkcs7b64,
                'signer_name' => $signerName,
                'signer_pinfl' => $signerPinfl,
                'signer_inn' => $signerInn,
                'signer_organization' => $signerOrg,
                'signed_at' => now(),
                'signature_info' => json_encode([
                    'cn' => $certInfo['cn'],
                    'pinfl' => $certInfo['pinfl'],
                    'inn' => $certInfo['inn'],
                    'valid_from' => $certInfo['valid_from'],
                    'valid_to' => $certInfo['valid_to'],
                    'serial' => $certInfo['serial_number'],
                ]),
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Hujjat muvaffaqiyatli imzolandi',
                'document' => $document->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Document sign error: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Imzolashda xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function extractSignerFromPKCS7(string $pkcs7Data): ?array
    {
        try {
            $tempPkcs7 = tempnam(sys_get_temp_dir(), 'pkcs7_');
            file_put_contents($tempPkcs7, $pkcs7Data);

            $tempAllCerts = tempnam(sys_get_temp_dir(), 'certs_');
            exec("openssl pkcs7 -print_certs -in {$tempPkcs7} -inform DER -out {$tempAllCerts} 2>&1", $out, $ret);
            if ($ret !== 0) {
                exec("openssl pkcs7 -print_certs -in {$tempPkcs7} -inform PEM -out {$tempAllCerts} 2>&1", $out, $ret);
            }

            @unlink($tempPkcs7);

            if ($ret !== 0) { @unlink($tempAllCerts); return null; }

            $allCerts = file_get_contents($tempAllCerts);
            @unlink($tempAllCerts);

            preg_match_all('/(-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----)/s', $allCerts, $matches);

            foreach ($matches[1] as $pem) {
                $tempCert = tempnam(sys_get_temp_dir(), 'cert_');
                file_put_contents($tempCert, $pem);

                $textOut = [];
                exec("openssl x509 -in {$tempCert} -text -noout 2>&1", $textOut);
                $text = implode("\n", $textOut);

                // Only process user certificates (have PINFL)
                if (!preg_match('/1\.2\.860\.3\.16\.1\.2\s*=\s*([A-Z0-9]+)/i', $text, $pinflMatch)) {
                    @unlink($tempCert); continue;
                }

                $certData = openssl_x509_parse(file_get_contents($tempCert));
                @unlink($tempCert);

                $subject = $certData['subject'] ?? [];
                $pinfl = $pinflMatch[1];
                $inn = null;
                if (preg_match('/1\.2\.860\.3\.16\.1\.1\s*=\s*([A-Z0-9]+)/i', $text, $innMatch)) {
                    $inn = $innMatch[1];
                }
                if (!$inn) $inn = $subject['UID'] ?? null;

                $validFrom = isset($certData['validFrom_time_t']) ? date('Y-m-d', $certData['validFrom_time_t']) : null;
                $validTo = isset($certData['validTo_time_t']) ? date('Y-m-d', $certData['validTo_time_t']) : null;

                return [
                    'cn' => $subject['CN'] ?? null,
                    'pinfl' => $pinfl,
                    'inn' => $inn,
                    'valid_from' => $validFrom,
                    'valid_to' => $validTo,
                    'serial_number' => $certData['serialNumber'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('extractSignerFromPKCS7 error: ' . $e->getMessage());
            return null;
        }
    }

    public function verify(string $qrCode): View
    {
        $document = Document::where('qr_code', $qrCode)->firstOrFail();
        return view('documents.verify', compact('document'));
    }

    public function verifyApi(string $qrCode): JsonResponse
    {
        $document = Document::where('qr_code', $qrCode)->first();

        if (!$document) {
            return response()->json([
                'status' => 0,
                'message' => 'Document not found',
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'document' => [
                'title' => $document->title,
                'content' => $document->content,
                'is_signed' => $document->isSigned(),
                'signer_name' => $document->signer_name,
                'signer_organization' => $document->signer_organization,
                'signed_at' => $document->signed_at?->format('Y-m-d H:i:s'),
                'created_at' => $document->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function getDocumentData(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        return response()->json([
            'status' => 1,
            'data' => base64_encode($document->content),
        ]);
    }
}

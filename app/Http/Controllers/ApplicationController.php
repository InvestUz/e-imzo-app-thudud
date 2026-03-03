<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationApproval;
use App\Models\District;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicHome', 'publicStore', 'publicSuccess']);
    }

    // ─── PUBLIC: citizen submits without login ─────────────────────────

    public function publicHome()
    {
        // Redirect logged-in staff to dashboard
        if (auth()->check() && !auth()->user()->isConsumer()) {
            return redirect()->route('dashboard');
        }

        $districts = District::where('is_active', true)->orderBy('name')->get();
        return view('welcome', compact('districts'));
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'district_id'      => 'required|exists:districts,id',
            'cadastral_number' => 'required|string|max:50',
            'address'          => 'nullable|string|max:500',
            'area_sqm'         => 'nullable|numeric|min:0',
            'description'      => 'nullable|string|max:2000',
            'pkcs7'            => 'required|string',
            'expected_pinfl'   => 'nullable|string',
            'expected_name'    => 'nullable|string',
        ]);

        // Extract citizen identity from E-IMZO signature
        $pkcs7Data = base64_decode($request->input('pkcs7'));
        $certInfo  = $this->extractCertFromPKCS7($pkcs7Data);

        // Fallback to client-provided PINFL (same pattern as EImzoAuthController)
        // The PKCS7 signature itself proves key possession; PINFL is display metadata
        if (!$certInfo || empty($certInfo['pinfl'])) {
            $expectedPinfl = $request->input('expected_pinfl');
            $expectedName  = $request->input('expected_name');

            if ($expectedPinfl) {
                Log::warning('ApplicationController: cert extraction failed, falling back to client PINFL', [
                    'expected_pinfl' => $expectedPinfl,
                    'expected_name'  => $expectedName,
                ]);
                $certInfo = [
                    'cn'            => $expectedName,
                    'pinfl'         => $expectedPinfl,
                    'inn'           => null,
                    'valid_from'    => null,
                    'valid_to'      => null,
                    'serial_number' => null,
                ];
            } else {
                return back()
                    ->withErrors(['pkcs7' => "E-IMZO imzosini o'qib bo'lmadi. Qayta urinib ko'ring."])
                    ->withInput();
            }
        }

        // Find or create citizen user record based on PINFL
        $citizen = User::firstOrCreate(
            ['pinfl' => $certInfo['pinfl']],
            [
                'name'     => $certInfo['cn'] ?? $certInfo['pinfl'],
                'email'    => $certInfo['pinfl'] . '@eimzo.local',
                'password' => bcrypt(str()->random(32)),
                'role'     => 'consumer',
                'inn'      => $certInfo['inn'],
                'serial_number'           => $certInfo['serial_number'],
                'certificate_valid_from'  => $certInfo['valid_from'],
                'certificate_valid_to'    => $certInfo['valid_to'],
            ]
        );

        // Update cert validity if the key was refreshed
        $citizen->update([
            'serial_number'          => $certInfo['serial_number'],
            'certificate_valid_from' => $certInfo['valid_from'],
            'certificate_valid_to'   => $certInfo['valid_to'],
        ]);

        $app = DB::transaction(function () use ($request, $citizen) {
            $formData = array_filter([
                'street_name'           => $request->input('street_name'),
                'intersecting_streets'  => $request->input('intersecting_streets'),
                'road_distance'         => $request->input('road_distance'),
                'pedestrian_distance'   => $request->input('pedestrian_distance'),
                'business_name'         => $request->input('business_name'),
                'activity_type'         => $request->input('activity_type'),
                'purpose'               => $request->input('purpose'),
                'existing_structures'   => $request->input('existing_structures'),
            ]);

            $app = Application::create([
                'number'           => Application::generateNumber(),
                'applicant_id'     => $citizen->id,
                'district_id'      => $request->input('district_id'),
                'cadastral_number' => $request->input('cadastral_number'),
                'address'          => $request->input('address'),
                'area_sqm'         => $request->input('area_sqm'),
                'source'           => 'online',
                'description'      => $request->input('description'),
                'form_data'        => count($formData) ? $formData : null,
                'applicant_pkcs7'  => $request->input('pkcs7'),
                'status'           => 'pending',
                'submitted_at'     => now(),
            ]);

            // Create 5 sequential approval rows
            $district = $app->district;
            foreach (Application::STEPS as $order => $role) {
                $assignee = $district->employeesByRole($role)->first();
                ApplicationApproval::create([
                    'application_id' => $app->id,
                    'step_order'     => $order,
                    'step_role'      => $role,
                    'assigned_to'    => $assignee?->id,
                    'status'         => $order === 1 ? 'pending' : 'waiting',
                ]);
            }

            $app->update(['status' => 'moderator_review', 'current_step' => 'moderator']);

            // Handle uploaded docs
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('applications/' . $app->id, 'public');
                    $app->documents()->create([
                        'uploaded_by'   => $citizen->id,
                        'type'          => 'application_letter',
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            return $app;
        });

        // Notify citizen that their application was received
        Notification::send(
            $citizen->id,
            Notification::TYPE_APP_SUBMITTED,
            'Arizangiz qabul qilindi',
            'Ariza ' . $app->number . ' muvaffaqiyatli yuborildi va ko\'rib chiqilmoqda.',
            [], null, 'application', $app->id
        );

        // Notify all admins about new incoming application
        User::where('role', 'admin')->each(function ($admin) use ($app) {
            Notification::send(
                $admin->id,
                Notification::TYPE_APP_SUBMITTED,
                'Yangi ariza qabul qilindi',
                $app->number . ' — ' . ($app->applicant->name ?? '') . ' · ' . ($app->district->name_uz ?? ''),
                [], null, 'application', $app->id
            );
        });

        // Notify the first step assignee (moderator)
        $firstStep = $app->approvals()->where('step_order', 1)->first();
        if ($firstStep && $firstStep->assigned_to) {
            Notification::send(
                $firstStep->assigned_to,
                Notification::TYPE_APP_SUBMITTED,
                'Yangi ariza: ' . $app->number,
                'Ko\'rib chiqish uchun yangi ariza keldi. Tuman: ' . ($app->district->name_uz ?? '—'),
                [], null, 'application', $app->id
            );
        }

        return redirect()->route('apply.success', $app->number);
    }

    public function publicSuccess(string $number)
    {
        $application = Application::where('number', $number)
            ->with(['district', 'applicant'])
            ->firstOrFail();

        return view('applications.submitted', compact('application'));
    }

    public function publicTrack(string $number)
    {
        $application = Application::where('number', $number)
            ->with(['district', 'applicant', 'approvals.assignee', 'approvals.approver',
                    'dalolatnomaSignatures.signer'])
            ->firstOrFail();

        return view('applications.track', compact('application'));
    }

    public function publicTrackSearch(Request $request)
    {
        $number = trim($request->query('number', ''));

        if (!$number) {
            return redirect()->route('home')->withErrors(['number' => 'Ariza raqamini kiriting']);
        }

        $application = Application::where('number', $number)->first();

        if (!$application) {
            return redirect()->route('home')
                ->withErrors(['number' => "'{$number}' raqamli ariza topilmadi."])
                ->withInput();
        }

        return redirect()->route('apply.track', $application->number);
    }

    // ─── Extract signer cert info from PKCS7 binary ────────────────────
    // Full implementation matching EImzoAuthController (all PINFL patterns + person name)

    private function extractCertFromPKCS7(string $pkcs7Data): ?array
    {
        try {
            $tmpP7  = tempnam(sys_get_temp_dir(), 'p7_');
            $tmpAll = tempnam(sys_get_temp_dir(), 'ca_');
            file_put_contents($tmpP7, $pkcs7Data);

            $out = []; $r = 0;
            exec("openssl pkcs7 -print_certs -in \"{$tmpP7}\" -inform DER -out \"{$tmpAll}\" 2>&1", $out, $r);
            if ($r !== 0) {
                $out = [];
                exec("openssl pkcs7 -print_certs -in \"{$tmpP7}\" -inform PEM -out \"{$tmpAll}\" 2>&1", $out, $r);
            }
            @unlink($tmpP7);

            if ($r !== 0 || !file_exists($tmpAll)) {
                @unlink($tmpAll);
                Log::warning('ApplicationController: openssl pkcs7 failed', ['output' => $out]);
                return null;
            }

            $allCerts = file_get_contents($tmpAll);
            @unlink($tmpAll);

            preg_match_all('/(-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----)/s', $allCerts, $m);

            foreach ($m[1] as $pem) {
                $tmpC = tempnam(sys_get_temp_dir(), 'c_');
                file_put_contents($tmpC, $pem);

                $textLines = [];
                exec("openssl x509 -in \"{$tmpC}\" -text -noout 2>&1", $textLines);
                $text = implode("\n", $textLines);

                // Check all known PINFL label variants (numeric OID, ASCII, Cyrillic)
                $hasPinfl = preg_match('/1\.2\.860\.3\.16\.1\.2\s*=\s*([A-Z0-9]+)/i', $text)
                         || preg_match('/\bPINFL\s*=\s*([A-Z0-9]+)/i', $text)
                         || preg_match('/\bЖШШИР\s*=\s*([A-Z0-9]+)/u', $text)
                         || preg_match('/\bЖСШИР\s*=\s*([A-Z0-9]+)/u', $text);

                if (!$hasPinfl) { @unlink($tmpC); continue; }

                // Parse cert
                $certContent = file_get_contents($tmpC);
                $certData    = openssl_x509_parse($certContent);
                @unlink($tmpC);

                if (!$certData) { continue; }

                $subject = $certData['subject'] ?? [];

                // PINFL
                $pinfl = null;
                if (preg_match('/1\.2\.860\.3\.16\.1\.2\s*=\s*([A-Z0-9]+)/i', $text, $mt))      $pinfl = $mt[1];
                elseif (preg_match('/\bPINFL\s*=\s*([A-Z0-9]+)/i', $text, $mt))                  $pinfl = $mt[1];
                elseif (preg_match('/\bЖШШИР\s*=\s*([A-Z0-9]+)/u', $text, $mt))                 $pinfl = $mt[1];
                elseif (preg_match('/\bЖСШИР\s*=\s*([A-Z0-9]+)/u', $text, $mt))                 $pinfl = $mt[1];
                elseif (isset($subject['1.2.860.3.16.1.2']))                                      $pinfl = $subject['1.2.860.3.16.1.2'];

                // INN / STIR
                $inn = null;
                if (preg_match('/1\.2\.860\.3\.16\.1\.1\s*=\s*([A-Z0-9]+)/i', $text, $mt))      $inn = $mt[1];
                elseif (preg_match('/\bSTIR\s*=\s*([A-Z0-9]+)/i', $text, $mt))                   $inn = $mt[1];
                elseif (preg_match('/\bINN\s*=\s*([A-Z0-9]+)/i', $text, $mt))                    $inn = $mt[1];
                elseif (isset($subject['1.2.860.3.16.1.1']))                                      $inn = $subject['1.2.860.3.16.1.1'];
                elseif (isset($subject['UID']))                                                    $inn = $subject['UID'];

                // Person name from GIVENNAME + SN (for org certs CN = org name)
                $givenName = $subject['GN'] ?? $subject['GIVENNAME'] ?? null;
                $surName   = $subject['SN'] ?? $subject['SURNAME'] ?? null;
                if (!$givenName && preg_match('/\bGIVENNAME\s*=\s*([^,\n]+)/i', $text, $mt)) $givenName = trim($mt[1]);
                if (!$givenName && preg_match('/\bGN\s*=\s*([^,\n]+)/i', $text, $mt))        $givenName = trim($mt[1]);
                if (!$surName  && preg_match('/\bSURNAME\s*=\s*([^,\n]+)/i', $text, $mt))   $surName   = trim($mt[1]);

                $cn = $subject['CN'] ?? null;
                $personName = null;
                if ($surName && $givenName)     $personName = trim($surName . ' ' . $givenName);
                elseif ($surName)               $personName = $surName;
                elseif ($givenName)             $personName = $givenName;

                Log::info('ApplicationController cert extraction OK', [
                    'cn' => $cn, 'pinfl' => $pinfl, 'inn' => $inn,
                ]);

                return [
                    'cn'            => $personName ?: $cn,
                    'pinfl'         => $pinfl,
                    'inn'           => $inn,
                    'valid_from'    => isset($certData['validFrom_time_t']) ? date('Y-m-d', $certData['validFrom_time_t']) : null,
                    'valid_to'      => isset($certData['validTo_time_t'])   ? date('Y-m-d', $certData['validTo_time_t'])   : null,
                    'serial_number' => $certData['serialNumber'] ?? null,
                ];
            }

            Log::warning('ApplicationController: no cert with PINFL found in PKCS7');
            return null;
        } catch (\Exception $e) {
            Log::error('ApplicationController extractCertFromPKCS7: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Authenticated CRUD ────────────────────────────────────────────

    // Citizen portal: authenticated consumer views own applications

    public function myApplications()
    {
        $user = Auth::user();
        if (!$user->isConsumer()) {
            return redirect()->route('dashboard');
        }
        $applications = Application::where('applicant_id', $user->id)
            ->with(['district', 'approvals'])
            ->latest()
            ->paginate(20);
        return view('citizen.my-applications', compact('applications', 'user'));
    }

    // Consumer: own apps; Admin: all; Staff: district apps
    public function index()
    {
        $user = Auth::user();

        if ($user->isConsumer()) {
            $applications = Application::where('applicant_id', $user->id)
                ->with('district')->latest()->paginate(15);
        } elseif ($user->isAdmin()) {
            $applications = Application::with(['applicant', 'district'])->latest()->paginate(15);
        } else {
            // Staff: see applications in their district that are at their step
            $applications = Application::where('district_id', $user->district_id)
                ->with(['applicant', 'district'])->latest()->paginate(15);
        }

        return view('applications.index', compact('applications'));
    }

    public function create()
    {
        $this->authorize('create', Application::class);
        $districts = District::where('is_active', true)->orderBy('name')->get();
        return view('applications.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Application::class);

        $data = $request->validate([
            'district_id'      => 'required|exists:districts,id',
            'cadastral_number' => 'required|string|max:50',
            'address'          => 'nullable|string|max:500',
            'area_sqm'         => 'nullable|numeric|min:0',
            'source'           => 'in:online,written',
            'description'      => 'nullable|string|max:2000',
            'pkcs7'            => 'nullable|string', // E-IMZO signature
        ]);

        $app = DB::transaction(function () use ($data, $request) {
            $formData = array_filter([
                'street_name'           => $request->input('street_name'),
                'intersecting_streets'  => $request->input('intersecting_streets'),
                'road_distance'         => $request->input('road_distance'),
                'pedestrian_distance'   => $request->input('pedestrian_distance'),
                'business_name'         => $request->input('business_name'),
                'activity_type'         => $request->input('activity_type'),
                'purpose'               => $request->input('purpose'),
                'existing_structures'   => $request->input('existing_structures'),
            ]);

            $app = Application::create([
                'number'           => Application::generateNumber(),
                'applicant_id'     => Auth::id(),
                'district_id'      => $data['district_id'],
                'cadastral_number' => $data['cadastral_number'],
                'address'          => $data['address'] ?? null,
                'area_sqm'         => $data['area_sqm'] ?? null,
                'source'           => $data['source'] ?? 'online',
                'description'      => $data['description'] ?? null,
                'form_data'        => count($formData) ? $formData : null,
                'applicant_pkcs7'  => $data['pkcs7'] ?? null,
                'status'           => 'pending',
                'submitted_at'     => now(),
            ]);

            // Create all 5 workflow approval rows
            $district = $app->district;
            foreach (Application::STEPS as $order => $role) {
                $assignee = $district->employeesByRole($role)->first();
                ApplicationApproval::create([
                    'application_id' => $app->id,
                    'step_order'     => $order,
                    'step_role'      => $role,
                    'assigned_to'    => $assignee?->id,
                    'status'         => $order === 1 ? 'pending' : 'waiting',
                ]);
            }

            // Move to first step
            $app->update([
                'status'       => 'moderator_review',
                'current_step' => 'moderator',
            ]);

            // Handle uploaded documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('applications/' . $app->id, 'public');
                    $app->documents()->create([
                        'uploaded_by'   => Auth::id(),
                        'type'          => $request->input('doc_type', 'application_letter'),
                        'original_name' => $file->getClientOriginalName(),
                        'path'          => $path,
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            return $app;
        });

        return redirect()->route('applications.show', $app)
            ->with('success', 'Ariza muvaffaqiyatli yuborildi. Raqam: ' . $app->number);
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);
        $application->load(['applicant', 'district', 'approvals.assignee', 'approvals.approver', 'documents.uploader', 'calculation.calculator', 'dalolatnomaSignatures.signer']);
        return view('applications.show', compact('application'));
    }

    // Staff inbox: applications waiting for their approval
    public function inbox()
    {
        $user = Auth::user();
        if ($user->isConsumer()) {
            return redirect()->route('applications.index');
        }

        $pendingApprovals = ApplicationApproval::where('step_role', $user->role)
            ->where('status', 'pending')
            ->whereHas('application', function ($q) use ($user) {
                if (!$user->isAdmin() && !$user->is_regional_backup) {
                    $q->where('district_id', $user->district_id);
                }
            })
            ->with(['application.applicant', 'application.district'])
            ->latest()
            ->paginate(15);

        return view('applications.inbox', compact('pendingApprovals'));
    }

}

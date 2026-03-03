<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DalolatnomaSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DalolatnomaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('sign');
    }

    /**
     * Commission member signs their slot on the dalolatnoma with E-IMZO.
     * Signing requires an active E-IMZO signature (PKCS7).
     * Can re-sign to update the signature.
     */
    public function sign(Request $request, Application $application)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Only commission members with an assigned position may sign
        if (!$user->isCommission() || !$user->commission_position) {
            abort(403, 'Faqat komissiya a\'zolari imzolashi mumkin.');
        }

        $request->validate([
            'pkcs7' => 'required|string|min:10',
        ]);

        // Upsert: one signature per position per application
        DalolatnomaSignature::updateOrCreate(
            [
                'application_id'     => $application->id,
                'commission_position' => $user->commission_position,
            ],
            [
                'signed_by'       => $user->id,
                'pkcs7_signature' => $request->input('pkcs7'),
                'qr_code'         => (string) Str::uuid(),
                'signed_at'       => now(),
            ]
        );

        return back()->with('dalo_success',
            "Dalolatnoma imzolandi: {$user->commission_position} — " . now()->format('d.m.Y H:i')
        );
    }

    /**
     * Public QR-code verification page — no auth required.
     */
    public function verify(string $qrCode)
    {
        $sig = DalolatnomaSignature::where('qr_code', $qrCode)
            ->with(['application.district', 'application.applicant', 'signer'])
            ->firstOrFail();

        return view('dalolatnoma.verify', compact('sig'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationApproval;
use App\Models\Calculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Approve or reject a workflow step.
     * Steps are sequential: each step activates only after previous is approved.
     * Regional backup can approve if district employee is unavailable.
     */
    public function approve(Request $request, ApplicationApproval $approval)
    {
        $user = Auth::user();
        $application = $approval->application;

        // Permission: must match role, and district OR be regional backup
        abort_unless(
            $user->canApproveStep($approval->step_role, $application->district_id),
            403, 'Bu amalni bajarishga ruxsat yo\'q'
        );

        abort_unless($approval->status === 'pending', 422, 'Bu qadam allaqachon bajarilgan');

        $data = $request->validate([
            'action'   => 'required|in:approve,reject',
            'comments' => 'nullable|string|max:1000',
            'pkcs7'    => 'nullable|string',
            // Executor-only calculation fields
            'payer_name'       => 'nullable|string',
            'payer_pinfl'      => 'nullable|string',
            'area_sqm'         => 'nullable|numeric|min:0',
            'rate_per_sqm'     => 'nullable|numeric|min:0',
            'penalty_amount'   => 'nullable|numeric|min:0',
            'paid_amount'      => 'nullable|numeric|min:0',
            'payment_deadline' => 'nullable|date',
            'payment_period'   => 'nullable|string',
            'calc_notes'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $approval, $application, $user) {
            $isApproved = $data['action'] === 'approve';

            $approval->update([
                'status'            => $isApproved ? 'approved' : 'rejected',
                'approved_by'       => $user->id,
                'is_backup_approval'=> $user->district_id !== $application->district_id,
                'comments'          => $data['comments'] ?? null,
                'pkcs7_signature'   => $data['pkcs7'] ?? null,
                'approved_at'       => now(),
            ]);

            // If executor is approving, save calculation
            if ($approval->step_role === 'executor' && $isApproved) {
                $totalAmount = ($data['area_sqm'] ?? 0) * ($data['rate_per_sqm'] ?? 0);
                Calculation::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'calculated_by'  => $user->id,
                        'payer_name'     => $data['payer_name'] ?? null,
                        'payer_pinfl'    => $data['payer_pinfl'] ?? null,
                        'area_sqm'       => $data['area_sqm'] ?? null,
                        'rate_per_sqm'   => $data['rate_per_sqm'] ?? null,
                        'total_amount'   => $totalAmount ?: null,
                        'penalty_amount' => $data['penalty_amount'] ?? 0,
                        'paid_amount'    => $data['paid_amount'] ?? 0,
                        'payment_deadline'=> $data['payment_deadline'] ?? null,
                        'payment_period' => $data['payment_period'] ?? null,
                        'notes'          => $data['calc_notes'] ?? null,
                    ]
                );
            }

            if (!$isApproved) {
                // Rejected — whole application rejected
                $application->update(['status' => 'rejected', 'current_step' => null]);
                return;
            }

            // Find next step
            $nextApproval = ApplicationApproval::where('application_id', $application->id)
                ->where('step_order', $approval->step_order + 1)
                ->first();

            if ($nextApproval) {
                // Activate next step
                $nextApproval->update(['status' => 'pending']);
                $application->update([
                    'status'       => $this->statusForStep($nextApproval->step_role),
                    'current_step' => $nextApproval->step_role,
                ]);
            } else {
                // All steps done — approved
                $application->update(['status' => 'approved', 'current_step' => null]);
            }
        });

        return back()->with('success',
            $data['action'] === 'approve' ? 'Tasdiqlandi' : 'Rad etildi'
        );
    }

    private function statusForStep(string $role): string
    {
        return match ($role) {
            'moderator'         => 'moderator_review',
            'complaint_officer' => 'complaint_review',
            'lawyer'            => 'legal_review',
            'executor'          => 'executor_review',
            'district_head'     => 'head_review',
            default             => 'pending',
        };
    }
}

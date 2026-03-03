<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationApproval;
use App\Models\Calculation;
use App\Models\Notification;
use App\Models\User;
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
                $roleLabel = ApplicationApproval::ROLE_LABELS[$approval->step_role] ?? $approval->step_role;
                // Notify applicant
                if ($application->applicant_id) {
                    Notification::send(
                        $application->applicant_id,
                        Notification::TYPE_APP_REJECTED,
                        'Arizangiz rad etildi',
                        'Ariza ' . $approval->step_role . ' bosqichida rad etildi' . ($data['comments'] ? ': ' . $data['comments'] : '.'),
                        [], $user->id, 'application', $application->id
                    );
                }
                // Notify all admins
                User::where('role', 'admin')->each(function ($admin) use ($application, $user, $roleLabel, $data) {
                    Notification::send(
                        $admin->id,
                        Notification::TYPE_APP_REJECTED,
                        $application->number . ': Rad etildi',
                        $roleLabel . ' tomonidan rad etildi' . ($data['comments'] ? ': ' . $data['comments'] : '.'),
                        [], $user->id, 'application', $application->id
                    );
                });
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
                $roleLabel = ApplicationApproval::ROLE_LABELS[$approval->step_role] ?? $approval->step_role;
                $nextLabel = ApplicationApproval::ROLE_LABELS[$nextApproval->step_role] ?? $nextApproval->step_role;
                // Notify applicant of progress
                if ($application->applicant_id) {
                    Notification::send(
                        $application->applicant_id,
                        Notification::TYPE_STEP_APPROVED,
                        'Ariza bosqichdan o\'tdi',
                        $roleLabel . ' bosqichi tasdiqlandi. Ariza keyingi bosqichga o\'tdi.',
                        [], $user->id, 'application', $application->id
                    );
                }
                // Notify next step assignee
                if ($nextApproval->assigned_to) {
                    Notification::send(
                        $nextApproval->assigned_to,
                        Notification::TYPE_STEP_APPROVED,
                        'Ko\'rib chiqish navbati sizda: ' . $application->number,
                        $application->number . ' ariza ' . $nextLabel . ' bosqichi uchun sizga yuklandi.',
                        [], $user->id, 'application', $application->id
                    );
                }
                // Notify admins of step progress
                User::where('role', 'admin')->each(function ($admin) use ($application, $user, $roleLabel, $nextLabel) {
                    Notification::send(
                        $admin->id,
                        Notification::TYPE_STEP_APPROVED,
                        $application->number . ': ' . $roleLabel . ' → ' . $nextLabel,
                        'Bosqich tasdiqlandi. Keyingi: ' . $nextLabel,
                        [], $user->id, 'application', $application->id
                    );
                });
            } else {
                // All steps done — approved
                $application->update(['status' => 'approved', 'current_step' => null]);
                // Notify applicant of final approval
                if ($application->applicant_id) {
                    Notification::send(
                        $application->applicant_id,
                        Notification::TYPE_APP_APPROVED,
                        'Arizangiz tasdiqlandi! 🎉',
                        'Ariza ' . $application->number . ' barcha bosqichlardan muvaffaqiyatli o\'tdi va tasdiqlandi.',
                        [], $user->id, 'application', $application->id
                    );
                }
                // Notify all admins of final approval
                User::where('role', 'admin')->each(function ($admin) use ($application, $user) {
                    Notification::send(
                        $admin->id,
                        Notification::TYPE_APP_APPROVED,
                        $application->number . ': Tasdiqlandi ✅',
                        'Ariza barcha bosqichlardan o\'tdi va yakunlandi.',
                        [], $user->id, 'application', $application->id
                    );
                });
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

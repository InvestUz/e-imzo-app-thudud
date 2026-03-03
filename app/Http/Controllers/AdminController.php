<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationApproval;
use App\Models\District;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // â”€â”€â”€ Dashboard overview â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function index()
    {
        $stats = [
            'total'    => Application::count(),
            'pending'  => Application::whereNotIn('status', ['approved', 'rejected'])->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'users'    => User::count(),
            'sessions' => UserSession::where('is_active', true)->count(),
        ];

        $recent = Application::with(['district', 'applicant'])
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        $activeSessions = UserSession::with('user')
            ->where('is_active', true)
            ->latest('logged_in_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent', 'activeSessions'));
    }

    // â”€â”€â”€ All applications with filters â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function applications(Request $request)
    {
        $query = Application::with(['district', 'applicant', 'approvals.assignee'])
            ->latest('submitted_at');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('cadastral_number', 'like', "%{$search}%")
                  ->orWhereHas('applicant', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('pinfl', 'like', "%{$search}%")
                  );
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($district = $request->input('district_id')) {
            $query->where('district_id', $district);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('submitted_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('submitted_at', '<=', $to);
        }

        $applications = $query->paginate(20)->withQueryString();
        $districts    = District::where('is_active', true)->orderBy('name')->get();

        return view('admin.applications', compact('applications', 'districts'));
    }

    // â”€â”€â”€ Reassign a pending approval step to another user â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function reassignApproval(Request $request, ApplicationApproval $approval)
    {
        abort_unless($approval->status === 'pending', 422, 'Bu bosqich faol emas');

        $data = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $newUser = User::findOrFail($data['assigned_to']);
        $approval->update(['assigned_to' => $newUser->id]);

        // Notify the new assignee
        Notification::send(
            $newUser->id,
            Notification::TYPE_SYSTEM,
            'Sizga yangi ariza yuklandi',
            'Ariza ' . $approval->application->number . ' ko\'rib chiqish sizga belgilandi.',
            [],
            Auth::id(),
            'application',
            $approval->application_id
        );

        return back()->with('success', 'Bosqich ' . $newUser->name . ' ga o\'tkazildi.');
    }

    // â”€â”€â”€ User management list â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function users(Request $request)
    {
        $query = User::with(['district', 'activeSessions'])->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('pinfl', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users     = $query->paginate(20)->withQueryString();
        $districts = District::where('is_active', true)->orderBy('name')->get();
        $allRoles  = self::roleOptions();

        return view('admin.users', compact('users', 'districts', 'allRoles'));
    }

    // â”€â”€â”€ Create user (POST) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'nullable|email|unique:users,email',
            'pinfl'              => 'nullable|string|max:20|unique:users,pinfl',
            'password'           => 'required|string|min:6',
            'role'               => 'required|in:' . implode(',', array_keys(self::roleOptions())),
            'district_id'        => 'nullable|exists:districts,id',
            'commission_position'=> 'nullable|string|max:100',
            'is_regional_backup' => 'boolean',
        ]);

        $data['password']           = Hash::make($data['password']);
        $data['is_regional_backup'] = $request->boolean('is_regional_backup');

        User::create($data);

        return back()->with('success', 'Foydalanuvchi yaratildi.');
    }

    // â”€â”€â”€ Edit user (GET) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function editUser(User $user)
    {
        $districts = District::where('is_active', true)->orderBy('name')->get();
        $allRoles  = self::roleOptions();
        return view('admin.user-edit', compact('user', 'districts', 'allRoles'));
    }

    // â”€â”€â”€ Update user (PATCH) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'nullable|email|unique:users,email,' . $user->id,
            'pinfl'              => 'nullable|string|max:20|unique:users,pinfl,' . $user->id,
            'password'           => 'nullable|string|min:6',
            'role'               => 'required|in:' . implode(',', array_keys(self::roleOptions())),
            'district_id'        => 'nullable|exists:districts,id',
            'commission_position'=> 'nullable|string|max:100',
            'is_regional_backup' => 'boolean',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_regional_backup'] = $request->boolean('is_regional_backup');

        $user->update($data);

        // Force logout if role changed
        if ($user->wasChanged('role')) {
            UserSession::where('user_id', $user->id)->where('is_active', true)
                ->get()->each(fn($s) => $s->terminate());
            Notification::send($user->id, Notification::TYPE_SYSTEM,
                'Rolingiz o\'zgartirildi',
                'Tizim administratori sizning rolingizni o\'zgartirdi. Qayta kiring.',
                [], Auth::id()
            );
        }

        return redirect()->route('admin.users')->with('success', $user->name . ' yangilandi.');
    }

    // â”€â”€â”€ Delete user â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function destroyUser(User $user)
    {
        abort_if($user->id === Auth::id(), 403, 'O\'zingizni o\'chira olmaysiz');

        UserSession::where('user_id', $user->id)->where('is_active', true)
            ->get()->each(fn($s) => $s->terminate());

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', $name . ' o\'chirildi.');
    }

    // â”€â”€â”€ Toggle regional backup flag â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function toggleBackup(User $user)
    {
        $user->update(['is_regional_backup' => !$user->is_regional_backup]);
        return back()->with('success',
            $user->name . ': mintaqaviy zaxira ' . ($user->is_regional_backup ? 'yoqildi' : 'o\'chirildi') . '.');
    }

    // â”€â”€â”€ Roles & permissions overview page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function roles()
    {
        $roleCounts = User::selectRaw('role, count(*) as cnt')->groupBy('role')->pluck('cnt', 'role');
        $rolePerms  = self::rolePermissions();
        $allRoles   = self::roleOptions();
        return view('admin.roles', compact('roleCounts', 'rolePerms', 'allRoles'));
    }

    // â”€â”€â”€ Sessions monitor â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function sessions(Request $request)
    {
        $query = UserSession::with('user')->latest('logged_in_at');

        if ($request->input('active_only')) {
            $query->where('is_active', true);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('pinfl', 'like', "%{$search}%")
                  );
            });
        }

        $sessions = $query->paginate(30)->withQueryString();

        return view('admin.sessions', compact('sessions'));
    }

    // â”€â”€â”€ Force logout a user â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function forceLogout(User $user)
    {
        UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->each(fn($s) => $s->terminate());

        Notification::send(
            $user->id, Notification::TYPE_SYSTEM,
            'Sessiya tugatildi',
            'Sizning sessiyangiz administrator tomonidan tugatildi.',
            [], Auth::id()
        );

        return back()->with('success', $user->name . ' foydalanuvchisi tizimdan chiqarildi.');
    }

    // â”€â”€â”€ Send system notification to user â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function sendNotification(Request $request, User $user)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string|max:1000',
        ]);

        Notification::send(
            $user->id, Notification::TYPE_SYSTEM,
            $request->title,
            $request->body ?? '',
            [], Auth::id()
        );

        return back()->with('success', 'Xabar yuborildi.');
    }

    // â”€â”€â”€ Shared helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public static function roleOptions(): array
    {
        return [
            'admin'          => 'Administrator (IT)',
            'devon'          => 'Devon (Qabul xodimi)',
            'executor'       => 'Ijrochi',
            'director'       => 'Rahbar (Topshiriq + Yakuniy tasdiq)',
            'district_rep'   => 'Tuman Vakili',
            'lawyer'         => 'Yurist',
            'compliance'     => 'Komplayans xodimi',
            'commission'     => 'Komissiya a\'zosi (Dalolatnoma)',
            'consumer'       => 'Fuqaro (ariza beruvchi)',
        ];
    }

    public static function rolePermissions(): array
    {
        return [
            'admin' => [
                'label'       => 'Administrator (IT)',
                'color'       => 'sbadge-purple',
                'description' => 'Tizimni to\'liq boshqarish: foydalanuvchilar, sessiyalar, barcha arizalar.',
                'can' => [
                    'Barcha arizalarni ko\'rish',
                    'Foydalanuvchilarni yaratish va tahrirlash',
                    'Rol va huquq berish',
                    'Sessiyalarni tugatish (force logout)',
                    'Bildirishnoma yuborish',
                    'Bosqich mas\'ulini o\'zgartirish (qayta tayinlash)',
                    'IT Admin panelga kirish',
                ],
                'cannot' => ['Arizani tasdiqlash/rad etish (bu workflow rollar uchun)'],
            ],
            'devon' => [
                'label'       => 'Devon (Qabul)',
                'color'       => 'sbadge-info',
                'description' => '1-bosqich: Devon ariza qabul qiladi va ro\'yxatga kiritadi.',
                'can' => [
                    '1-bosqich: Ariza qabul qilish',
                    'Tasdiqlash yoki Rad etish (izoh bilan)',
                    'Arizani navbatga qo\'yish',
                ],
                'cannot' => ['Boshqa bosqichlarni tasdiqlash', 'Admin panelga kirish'],
            ],
            'executor' => [
                'label'       => 'Ijrochi',
                'color'       => 'sbadge-warning',
                'description' => '2-bosqich: Ijrochi arizani o\'rganadi va Rahbarga yo\'naltiradi (+/−).',
                'can' => [
                    '2-bosqich: Tasdiqlash (+) yoki Rad etish (−)',
                    'Hisob-kitob ma\'lumotlarini kiritish',
                    'Rad etilganda javob xatini izoh sifatida kiritish',
                ],
                'cannot' => ['1, 3-7-bosqichlarni tasdiqlash'],
            ],
            'director' => [
                'label'       => 'Rahbar (Topshiriq + Yakuniy)',
                'color'       => 'sbadge-blue',
                'description' => '3-bosqich: Topshiriq beradi. 7-bosqich (yakuniy): Shartnoma tuzilishiga ruxsat beradi.',
                'can' => [
                    '3-bosqich: Topshiriq berish (tasdiqlash yoki rad etish)',
                    '7-bosqich (yakuniy): Barcha bosqichlardan o\'tgan arizani tasdiqlash',
                    'Shartnoma tuzilishiga yakuniy ruxsat',
                ],
                'cannot' => ['2, 4-6-bosqichlarni tasdiqlash'],
            ],
            'district_rep' => [
                'label'       => 'Tuman Vakili',
                'color'       => 'sbadge-success',
                'description' => '4-bosqich: Tuman vakili arizani o\'rganadi va tutash hududga yo\'naltiradi (+/−).',
                'can' => [
                    '4-bosqich: Tasdiqlash yoki Rad etish',
                    'Tutash hudud bo\'yicha xulosa bildirish',
                    'Qayta yo\'naltirish uchun izoh qoldirish',
                ],
                'cannot' => ['1-3, 5-7-bosqichlarni tasdiqlash'],
            ],
            'lawyer' => [
                'label'       => 'Yurist',
                'color'       => 'sbadge-purple',
                'description' => '5-bosqich: Yurist huquqiy ekspertiza o\'tkazadi va OK bosadi.',
                'can' => [
                    '5-bosqich: Tasdiqlash yoki Rad etish (E-IMZO bilan)',
                    'Huquqiy xulosani qayd etish',
                ],
                'cannot' => ['1-4, 6-7-bosqichlarni tasdiqlash'],
            ],
            'compliance' => [
                'label'       => 'Komplayans xodimi',
                'color'       => 'sbadge-blue',
                'description' => '6-bosqich: Komplayans muvofiqligini tekshiradi va OK bosadi.',
                'can' => [
                    '6-bosqich: Tasdiqlash yoki Rad etish (E-IMZO bilan)',
                    'Muvofiqlik xulosasini qayd etish',
                ],
                'cannot' => ['1-5, 7-bosqichlarni tasdiqlash'],
            ],
            'commission' => [
                'label'       => 'Komissiya a\'zosi',
                'color'       => 'sbadge-blue',
                'description' => 'Dalolatnomani E-IMZO bilan imzolash (workflow bosqichlari bilan bog\'liq emas).',
                'can' => [
                    'Dalolatnomani E-IMZO bilan imzolash',
                    'O\'z lavozimi bo\'yicha imzo qo\'yish',
                ],
                'cannot' => ['Workflow bosqichlarini tasdiqlash', 'Admin panelga kirish'],
            ],
            'consumer' => [
                'label'       => 'Fuqaro',
                'color'       => 'sbadge-gray',
                'description' => 'Ariza berish va holatini kuzatish.',
                'can' => [
                    'E-IMZO orqali shartnoma arizasi yuborish',
                    'O\'z arizalarini kuzatish',
                    'Bildirishnomalar olish',
                ],
                'cannot' => ['Staff panelga kirish', 'Boshqa arizalarni ko\'rish'],
            ],
        ];
    }
}

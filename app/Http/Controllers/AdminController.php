<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    const NAV_MODULES = ['manage_users', 'manage_admins'];

    // ─── Dashboard ───────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $queryFilter = function ($q) use ($filter) {
            $now = \Carbon\Carbon::now();
            switch ($filter) {
                case 'today':
                    $q->whereDate('created_at', $now->toDateString());
                    break;
                case 'yesterday':
                    $q->whereDate('created_at', $now->subDay()->toDateString());
                    break;
                case 'this_week':
                    $q->whereBetween('created_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
                    break;
                case 'this_month':
                    $q->whereBetween('created_at', [$now->startOfMonth()->toDateTimeString(), $now->endOfMonth()->toDateTimeString()]);
                    break;
                case 'previous_month':
                    $start = $now->copy()->subMonth()->startOfMonth();
                    $end = $now->copy()->subMonth()->endOfMonth();
                    $q->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
                    break;
                case 'this_year':
                    $q->whereBetween('created_at', [$now->startOfYear()->toDateTimeString(), $now->endOfYear()->toDateTimeString()]);
                    break;
                case 'previous_year':
                    $start = $now->copy()->subYear()->startOfYear();
                    $end = $now->copy()->subYear()->endOfYear();
                    $q->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
                    break;
            }
        };

        $totalUsers    = User::where('role', 'user')->where($queryFilter)->count();
        $totalAdmins   = User::where('role', 'admin')->where($queryFilter)->count();
        $totalSubAdmins = User::where('role', 'sub_admin')->where($queryFilter)->count();
        $totalAll      = $totalUsers + $totalAdmins + $totalSubAdmins;

        $recentUsers = User::where($queryFilter)->latest()->take(5)->get();

        $totalRooms = \App\Models\Room::where($queryFilter)->count();
        $liveRooms = \App\Models\Room::where('status', 'approved')->where($queryFilter)->count();
        
        $totalReservations = \App\Models\Reservation::where($queryFilter)->count();
        $reservationRevenue = \App\Models\Reservation::where('status', 'success')->where($queryFilter)->sum('total_amount');
        
        $activeSubscriptions = \App\Models\UserSubscription::where('status', 'active')->where($queryFilter)->count();
        $subscriptionRevenue = \App\Models\UserSubscription::where('status', '!=', 'pending')->where($queryFilter)->sum('price');
        
        $totalEarnings = $reservationRevenue + $subscriptionRevenue;

        $defaultCurrencyCode = \App\Models\SiteSetting::get('default_currency', 'USD');
        $currencySymbol = \App\Models\Currency::where('currency_code', $defaultCurrencyCode)->value('symbol') ?? '$';

        // Chart Data (Last 6 Months)
        $months = [];
        $userGrowth = [];
        $reservationData = [];
        $subscriptionData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $months[] = $monthLabel;
            
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            $userGrowth[] = User::whereBetween('created_at', [$start, $end])->count();
            $reservationData[] = \App\Models\Reservation::where('status', 'success')
                                            ->whereBetween('created_at', [$start, $end])
                                            ->sum('total_amount');
            $subscriptionData[] = \App\Models\UserSubscription::where('status', '!=', 'pending')
                                                  ->whereBetween('created_at', [$start, $end])
                                                  ->sum('price');
        }

        if ($request->ajax()) {
            return response()->json([
                'totalUsers' => number_format($totalUsers),
                'totalRooms' => number_format($totalRooms),
                'liveRooms' => number_format($liveRooms),
                'totalReservations' => number_format($totalReservations),
                'reservationRevenue' => number_format($reservationRevenue, 2),
                'activeSubscriptions' => number_format($activeSubscriptions),
                'subscriptionRevenue' => number_format($subscriptionRevenue, 2),
                'totalEarnings' => number_format($totalEarnings, 2),
                'currencySymbol' => $currencySymbol
            ]);
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalSubAdmins', 'totalAll', 'recentUsers',
            'totalRooms', 'liveRooms', 'totalReservations', 'reservationRevenue',
            'activeSubscriptions', 'subscriptionRevenue', 'totalEarnings',
            'months', 'userGrowth', 'reservationData', 'subscriptionData', 'filter', 'currencySymbol'
        ));
    }

    // ─── Users ───────────────────────────────────────────────────────────────

    public function users()
    {
        $this->authorizePermission('manage_users');
        $users = User::with('countryRelation')->where('role', 'user')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $this->authorizePermission('manage_users', 'add');
        $countries = Country::orderBy('country_name')->get();
        return view('admin.users.create', compact('countries'));
    }

    public function storeUser(Request $request)
    {
        $this->authorizePermission('manage_users', 'add');

        $validated = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'email'         => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone'         => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/', 'unique:users,phone'],
            'password'      => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'gender'        => ['required', 'in:male,female,other'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
        ], [
            'name.regex'           => 'Name may only contain letters, spaces, and hyphens.',
            'phone.regex'          => 'Please enter a valid phone number.',
            'date_of_birth.before' => 'User must be at least 18 years old.',
            'country_id.exists'    => 'Please select a valid country.',
        ]);

        $country = Country::findOrFail($validated['country_id']);

        User::create([
            'name'          => trim($validated['name']),
            'email'         => strtolower(trim($validated['email'])),
            'phone'         => trim($validated['phone']),
            'password'      => Hash::make($validated['password']),
            'date_of_birth' => $validated['date_of_birth'],
            'gender'        => $validated['gender'],
            'country'       => $country->short_name,
            'country_id'    => $country->id,
            'role'          => 'user',
            'profile_image' => 'images/image.png',
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        $this->authorizePermission('manage_users', 'edit');
        $countries = Country::orderBy('country_name')->get();
        return view('admin.users.edit', compact('user', 'countries'));
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorizePermission('manage_users', 'edit');

        $validated = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'email'         => ['required', 'email:rfc', 'max:255', 'unique:users,email,' . $user->id],
            'phone'         => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/', 'unique:users,phone,' . $user->id],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'gender'        => ['required', 'in:male,female,other'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
            'password'      => ['nullable', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $country = Country::findOrFail($validated['country_id']);

        $data = [
            'name'          => trim($validated['name']),
            'email'         => strtolower(trim($validated['email'])),
            'phone'         => trim($validated['phone']),
            'date_of_birth' => $validated['date_of_birth'],
            'gender'        => $validated['gender'],
            'country'       => $country->short_name,
            'country_id'    => $country->id,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        $this->authorizePermission('manage_users', 'delete');

        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete an admin account.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // ─── Admins ──────────────────────────────────────────────────────────────

    public function admins()
    {
        $this->authorizePermission('manage_admins');
        $admins = User::latest()->get();
        return view('admin.admins.index', compact('admins'));
    }

    public function updateAdminRole(Request $request, User $user)
    {
        $this->authorizePermission('manage_admins', 'edit');

        $validated = $request->validate([
            'role'        => ['required', 'in:user,admin,sub_admin'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['in:' . implode(',', self::NAV_MODULES)],
        ]);

        $permissions = null;
        if ($validated['role'] === 'admin') {
            $permissions = self::NAV_MODULES;
        } elseif ($validated['role'] === 'sub_admin') {
            $permissions = $validated['permissions'] ?? [];
        }

        $user->update([
            'role'        => $validated['role'],
            'permissions' => $permissions,
        ]);

        return back()->with('success', 'Role updated for ' . $user->name . '.');
    }

    // ─── Profile ─────────────────────────────────────────────────────────────

    public function profile()
    {
        $user      = User::findOrFail(session('admin_id'));
        $countries = Country::orderBy('country_name')->get();
        return view('admin.profile', compact('user', 'countries'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(session('admin_id'));

        $validated = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'email'         => ['required', 'email:rfc', 'max:255', 'unique:users,email,' . $user->id],
            'phone'         => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/', 'unique:users,phone,' . $user->id],
            'date_of_birth' => ['required', 'date', 'before:' . now()->subYears(18)->toDateString()],
            'gender'        => ['required', 'in:male,female,other'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
            'password'      => ['nullable', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.regex'           => 'Name may only contain letters, spaces, and hyphens.',
            'phone.regex'          => 'Please enter a valid phone number.',
            'date_of_birth.before' => 'You must be at least 18 years old.',
        ]);

        $country = Country::findOrFail($validated['country_id']);

        $data = [
            'name'          => trim($validated['name']),
            'email'         => strtolower(trim($validated['email'])),
            'phone'         => trim($validated['phone']),
            'date_of_birth' => $validated['date_of_birth'],
            'gender'        => $validated['gender'],
            'country'       => $country->short_name,
            'country_id'    => $country->id,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = 'admin_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['profile_image'] = 'images/' . $filename;
        }

        $user->update($data);
        $fresh = $user->fresh();
        session()->put('admin_user', (object) $fresh->only([
            'id','name','email','phone','role','profile_image','gender','country','country_id','date_of_birth','permissions'
        ]));

        return back()->with('success', 'Profile updated successfully.');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function authorizePermission(string $module, string $action = 'view'): void
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            abort(401);
        }

        $sessionUser = User::find($adminId);

        if (!$sessionUser || !in_array($sessionUser->role, ['admin', 'sub_admin'])) {
            abort(403);
        }

        if ($action !== 'view' && !$sessionUser->isAdmin() && !$sessionUser->hasPermission($module)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}

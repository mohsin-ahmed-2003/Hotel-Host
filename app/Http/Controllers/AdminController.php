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

    public function dashboard()
    {
        $totalUsers    = User::where('role', 'user')->count();
        $totalAdmins   = User::where('role', 'admin')->count();
        $totalSubAdmins = User::where('role', 'sub_admin')->count();
        $totalAll      = $totalUsers + $totalAdmins + $totalSubAdmins;

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalSubAdmins', 'totalAll', 'recentUsers'
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

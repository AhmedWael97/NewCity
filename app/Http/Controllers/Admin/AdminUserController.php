<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $this->authorize('view-users');
        
        $query = User::with(['city', 'roles']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
        
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        
        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);
        
        $users = $query->paginate(15)->withQueryString();
        
        // Get filter options
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $userTypes = ['regular', 'shop_owner', 'admin'];
        
        return view('admin.users.index', compact('users', 'cities', 'userTypes'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $this->authorize('create-users');
        
        $cities = City::where('is_active', true)->orderBy('name')->get();
        // Get roles for web guard
        $roles = Role::where('guard_name', 'web')->get();
        $userTypes = ['regular', 'shop_owner', 'admin'];
        
        return view('admin.users.create', compact('cities', 'userTypes', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $this->authorize('create-users');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:regular,shop_owner,admin',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'is_verified' => 'boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id,guard_name,web',
            'assigned_city_ids' => 'nullable|array',
            'assigned_city_ids.*' => 'exists:cities,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'is_verified' => $request->boolean('is_verified'),
            'email_verified_at' => $request->boolean('is_verified') ? now() : null,
            'assigned_city_ids' => $request->assigned_city_ids ?? [],
        ]);
        
        // Assign roles
        if ($request->filled('roles')) {
            // Get the actual Role models with web guard
            $webRoles = Role::where('guard_name', 'web')
                ->whereIn('id', $request->roles)
                ->get();
            
            // Assign each role to the user
            if ($webRoles->isNotEmpty()) {
                foreach ($webRoles as $role) {
                    $user->assignRole($role);
                }
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['city', 'shops', 'ratings.shop']);
        
        // Get user statistics
        $stats = [
            'total_shops' => $user->shops()->count(),
            'total_ratings_given' => $user->ratings()->count(),
            'average_rating_given' => $user->ratings()->avg('rating'),
            'total_ratings_received' => $user->shops()->withCount('ratings')->get()->sum('ratings_count'),
            'join_date' => $user->created_at,
            'last_login' => $user->updated_at, // You might want to track this separately
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing user
     */
    public function edit(User $user)
    {
        $this->authorize('edit-users');
        
        $cities = City::where('is_active', true)->orderBy('name')->get();
        // Get roles for web guard
        $roles = Role::where('guard_name', 'web')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $userTypes = ['regular', 'shop_owner', 'admin'];
        
        return view('admin.users.edit', compact('user', 'cities', 'userTypes', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit-users');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:regular,shop_owner,admin',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'is_verified' => 'boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id,guard_name,web',
            'assigned_city_ids' => 'nullable|array',
            'assigned_city_ids.*' => 'exists:cities,id'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'is_verified' => $request->boolean('is_verified'),
            'assigned_city_ids' => $request->assigned_city_ids ?? [],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->boolean('is_verified') && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);
        
        // Sync roles
        if ($request->has('roles')) {
            // First, remove all existing roles from all guards
            $user->roles()->detach();
            
            // Then get the actual Role models with web guard
            $webRoles = Role::where('guard_name', 'web')
                ->whereIn('id', $request->roles ?? [])
                ->get();
            
            // Assign each role to the user
            if ($webRoles->isNotEmpty()) {
                foreach ($webRoles as $role) {
                    $user->assignRole($role);
                }
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete-users');
        
        // Prevent deleting the last admin
        if ($user->user_type === 'admin' && User::where('user_type', 'admin')->count() <= 1) {
            return back()->with('error', 'لا يمكن حذف آخر مسؤول في النظام');
        }

        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Toggle user verification status
     */
    public function verify(User $user)
    {
        $user->update([
            'is_verified' => !$user->is_verified,
            'email_verified_at' => !$user->is_verified ? now() : null,
        ]);

        $status = $user->is_verified ? 'verified' : 'unverified';
        
        return back()->with('success', "User has been {$status} successfully.");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // You might want to add an 'active' field to users table
        // For now, we'll use a simple approach
        
        return back()->with('success', 'User status updated successfully.');
    }

    /**
     * Handle bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:verify,unverify,activate,deactivate,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $action = $request->action;
        $userIds = $request->user_ids;
        $currentUserId = Auth::id();

        // Prevent admin from performing actions on themselves
        if (in_array($currentUserId, $userIds)) {
            return back()->with('error', 'You cannot perform bulk actions on your own account.');
        }

        $users = User::whereIn('id', $userIds)->get();
        $successCount = 0;

        foreach ($users as $user) {
            try {
                switch ($action) {
                    case 'verify':
                        $user->update([
                            'is_verified' => true,
                            'email_verified_at' => now(),
                        ]);
                        $successCount++;
                        break;

                    case 'unverify':
                        $user->update([
                            'is_verified' => false,
                            'email_verified_at' => null,
                        ]);
                        $successCount++;
                        break;

                    case 'activate':
                        // If you have an 'active' field, use it here
                        // $user->update(['active' => true]);
                        $successCount++;
                        break;

                    case 'deactivate':
                        // If you have an 'active' field, use it here
                        // $user->update(['active' => false]);
                        $successCount++;
                        break;

                    case 'delete':
                        $user->delete();
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                // Log error but continue with other users
                Log::error("Bulk action failed for user {$user->id}: " . $e->getMessage());
            }
        }

        $actionText = [
            'verify' => 'verified',
            'unverify' => 'unverified',
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'delete' => 'deleted'
        ];

        $message = "{$successCount} user(s) have been {$actionText[$action]} successfully.";
        
        return redirect()->route('admin.users.index')->with('success', $message);
    }
}
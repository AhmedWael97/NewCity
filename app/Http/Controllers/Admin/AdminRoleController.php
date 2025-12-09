<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRoleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-roles');
        
        // Only show admin guard roles in admin panel
        $roles = Role::where('guard_name', 'admin')->with('permissions')->withCount('users')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create-roles');
        
        // Only show admin guard permissions in admin panel
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function($permission) {
            // Group permissions by module (e.g., "view-shops" -> "shops")
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? $parts[1] : 'other';
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-roles');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id,guard_name,admin'
        ]);
        
        // Always create admin guard roles in admin panel
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'admin'
        ]);
        
        if (!empty($validated['permissions'])) {
            // Filter to only sync permissions that belong to admin guard
            $adminPermissionIds = Permission::where('guard_name', 'admin')
                ->whereIn('id', $validated['permissions'])
                ->pluck('id')
                ->toArray();
            $role->syncPermissions($adminPermissionIds);
        }
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $this->authorize('view-roles');
        
        $role->load('permissions', 'users');
        
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $this->authorize('edit-roles');
        
        // Only show admin guard permissions in admin panel
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function($permission) {
            $parts = explode('-', $permission->name);
            return count($parts) > 1 ? $parts[1] : 'other';
        });
        
        // Only get permission IDs that match the admin guard
        $rolePermissions = $role->permissions()
            ->where('guard_name', 'admin')
            ->pluck('permissions.id')
            ->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('edit-roles');
        
        // Prevent editing super_admin role
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل دور المسؤول الرئيسي');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id,guard_name,admin'
        ]);
        
        $role->update([
            'name' => $validated['name']
        ]);
        
        // Filter to only sync permissions that belong to admin guard
        $permissionIds = $validated['permissions'] ?? [];
        $adminPermissionIds = Permission::where('guard_name', 'admin')
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->toArray();
        
        $role->syncPermissions($adminPermissionIds);
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete-roles');
        
        // Prevent deleting super_admin and admin roles
        if (in_array($role->name, ['super_admin', 'admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف الأدوار الأساسية للنظام');
        }
        
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف دور مرتبط بمستخدمين');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
}

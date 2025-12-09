@extends('layouts.admin')

@section('title', 'Debug Permissions')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Permission Debug Information</h3>
        </div>
        <div class="card-body">
            <h4>User Information</h4>
            <p><strong>ID:</strong> {{ auth()->id() }}</p>
            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Current Guard:</strong> {{ auth()->getDefaultDriver() }}</p>
            
            <hr>
            
            <h4>Roles</h4>
            <ul>
                @forelse(auth()->user()->roles as $role)
                    <li>{{ $role->name }} (Guard: {{ $role->guard_name }})</li>
                @empty
                    <li>No roles assigned</li>
                @endforelse
            </ul>
            
            <hr>
            
            <h4>Direct Permissions</h4>
            <ul>
                @forelse(auth()->user()->permissions as $permission)
                    <li>{{ $permission->name }}</li>
                @empty
                    <li>No direct permissions</li>
                @endforelse
            </ul>
            
            <hr>
            
            <h4>All Permissions (via Roles)</h4>
            <ul>
                @forelse(auth()->user()->getAllPermissions() as $permission)
                    <li>{{ $permission->name }}</li>
                @empty
                    <li>No permissions</li>
                @endforelse
            </ul>
            
            <hr>
            
            <h4>Permission Tests</h4>
            <p><strong>Can view-dashboard:</strong> {{ auth()->user()->can('view-dashboard') ? 'YES' : 'NO' }}</p>
            <p><strong>Can view-users:</strong> {{ auth()->user()->can('view-users') ? 'YES' : 'NO' }}</p>
            <p><strong>Can view-shops:</strong> {{ auth()->user()->can('view-shops') ? 'YES' : 'NO' }}</p>
            <p><strong>Can view-news:</strong> {{ auth()->user()->can('view-news') ? 'YES' : 'NO' }}</p>
            <p><strong>Has super_admin role:</strong> {{ auth()->user()->hasRole('super_admin') ? 'YES' : 'NO' }}</p>
            <p><strong>Has admin role:</strong> {{ auth()->user()->hasRole('admin') ? 'YES' : 'NO' }}</p>
            <p><strong>Has super_admin role (admin guard):</strong> {{ auth()->user()->hasRole('super_admin', 'admin') ? 'YES' : 'NO' }}</p>
            <p><strong>Has admin role (admin guard):</strong> {{ auth()->user()->hasRole('admin', 'admin') ? 'YES' : 'NO' }}</p>
            <p><strong>Can view-dashboard (via Gate):</strong> {{ \Illuminate\Support\Facades\Gate::allows('view-dashboard') ? 'YES' : 'NO' }}</p>
            <p><strong>Spatie Guard Name Method:</strong> {{ method_exists(auth()->user(), 'getDefaultGuardName') ? auth()->user()->getDefaultGuardName() : 'method not found' }}</p>
            <p><strong>Has Permission Direct Check:</strong> {{ auth()->user()->hasPermissionTo('view-dashboard', 'admin') ? 'YES' : 'NO' }}</p>
            <p><strong>Permission Check (no guard):</strong> {{ auth()->user()->hasPermissionTo('view-dashboard') ? 'YES' : 'NO' }}</p>
            
            <hr>
            
            <h4>Guard Check</h4>
            <p><strong>Auth Guard Name:</strong> {{ config('auth.defaults.guard') }}</p>
            <p><strong>Session Guard:</strong> {{ request()->session()->get('_guard') ?? 'default (web)' }}</p>
        </div>
    </div>
</div>
@endsection

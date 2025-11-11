@extends('layouts.admin')

@section('title', 'User Favorites Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Favorites Management</h1>
        <a href="{{ route('admin.favorites.statistics') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar"></i> View Statistics
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Favorites</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.favorites.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="User or Shop name">
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-control" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="shop_id" class="form-label">Shop</label>
                    <select class="form-control" id="shop_id" name="shop_id">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.favorites.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Favorites Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Favorites ({{ $favorites->total() }})</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Shop</th>
                            <th>Shop Status</th>
                            <th>Favorited At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($favorites as $favorite)
                            <tr>
                                <td>{{ $favorite->id }}</td>
                                <td>
                                    <strong>{{ $favorite->user_name }}</strong><br>
                                    <small class="text-muted">{{ $favorite->user_email }}</small><br>
                                    <a href="{{ route('admin.users.show', $favorite->user_id) }}" 
                                       class="btn btn-sm btn-link p-0">
                                        View Profile
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $favorite->shop_name }}</strong><br>
                                    <a href="{{ route('admin.shops.show', $favorite->shop_id) }}" 
                                       class="btn btn-sm btn-link p-0">
                                        View Shop
                                    </a>
                                </td>
                                <td>
                                    @if($favorite->shop_status === 'approved')
                                        <span class="badge bg-success text-white">Approved</span>
                                    @elseif($favorite->shop_status === 'pending')
                                        <span class="badge bg-warning text-white">Pending</span>
                                    @elseif($favorite->shop_status === 'rejected')
                                        <span class="badge bg-danger text-white">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ ucfirst($favorite->shop_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($favorite->created_at)->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($favorite->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.favorites.destroy') }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to remove this favorite?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $favorite->user_id }}">
                                        <input type="hidden" name="shop_id" value="{{ $favorite->shop_id }}">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remove Favorite">
                                            <i class="fas fa-trash text-white"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No favorites found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $favorites->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    #dataTable tbody td {
        color: #000 !important;
        vertical-align: middle;
    }
    .btn-close {
        background-color: transparent;
        border: none;
        font-size: 1.5rem;
        opacity: 0.5;
    }
    .btn-close:hover {
        opacity: 1;
    }
</style>
@endsection

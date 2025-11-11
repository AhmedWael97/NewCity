@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Details</h1>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reviews
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Review Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Review Information</h6>
                    <div>
                        @if(!($review->is_approved ?? true))
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve Review
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.reviews.reject', $review->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="fas fa-times"></i> Reject Review
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Delete Review
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Rating</h5>
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star fa-2x {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }} me-1"></i>
                            @endfor
                            <span class="ms-3 h4 mb-0 badge bg-primary text-white">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Review Comment</h5>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $review->review ?? 'No comment provided' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Status</h5>
                        @if($review->is_approved ?? true)
                            <span class="badge bg-success text-white fs-6 px-3 py-2">
                                <i class="fas fa-check-circle"></i> Approved
                            </span>
                        @else
                            <span class="badge bg-warning text-white fs-6 px-3 py-2">
                                <i class="fas fa-clock"></i> Pending Approval
                            </span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Created Date</h5>
                            <p>{{ $review->created_at->format('F d, Y h:i A') }}</p>
                            <p class="text-muted">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Last Updated</h5>
                            <p>{{ $review->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-muted">{{ $review->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User & Shop Information -->
        <div class="col-lg-4">
            <!-- User Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($review->user->avatar)
                            <img src="{{ asset('storage/' . $review->user->avatar) }}" 
                                 alt="{{ $review->user->name }}"
                                 class="rounded-circle"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <p class="mb-2">
                        <strong>Name:</strong><br>
                        {{ $review->user->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $review->user->email }}">{{ $review->user->email }}</a>
                    </p>
                    @if($review->user->phone)
                        <p class="mb-2">
                            <strong>Phone:</strong><br>
                            {{ $review->user->phone }}
                        </p>
                    @endif
                    <p class="mb-2">
                        <strong>User Type:</strong><br>
                        <span class="badge bg-info text-white">{{ ucfirst($review->user->user_type) }}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Member Since:</strong><br>
                        {{ $review->user->created_at->format('M d, Y') }}
                    </p>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.users.show', $review->user->id) }}" 
                           class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-user"></i> View User Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Shop Info -->
            @if($review->reviewable)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Shop Information</h6>
                    </div>
                    <div class="card-body">
                        @if($review->reviewable->images_array && count($review->reviewable->images_array) > 0)
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . $review->reviewable->images_array[0]) }}" 
                                     alt="{{ $review->reviewable->name }}"
                                     class="img-fluid rounded"
                                     style="max-height: 150px; object-fit: cover;">
                            </div>
                        @endif
                        
                        <p class="mb-2">
                            <strong>Shop Name:</strong><br>
                            {{ $review->reviewable->name }}
                        </p>
                        <p class="mb-2">
                            <strong>Average Rating:</strong><br>
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($review->reviewable->rating) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-2">{{ number_format($review->reviewable->rating, 1) }}/5</span>
                            </div>
                        </p>
                        <p class="mb-2">
                            <strong>Total Reviews:</strong><br>
                            {{ $review->reviewable->review_count }} reviews
                        </p>
                        <p class="mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $review->reviewable->status === 'approved' ? 'success' : 'warning' }} text-white">
                                {{ ucfirst($review->reviewable->status) }}
                            </span>
                        </p>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.shops.show', $review->reviewable->id) }}" 
                               class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-store"></i> View Shop Details
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p class="text-muted">The shop associated with this review has been deleted.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
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

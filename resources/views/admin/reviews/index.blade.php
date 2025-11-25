@extends('layouts.admin')

@section('title', 'إدارة التقييمات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة التقييمات</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة التقييمات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">البحث (المستخدم)</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="اسم المستخدم أو البريد الإلكتروني">
                </div>
                <div class="col-md-2">
                    <label for="rating" class="form-label">التقييم</label>
                    <select class="form-control" id="rating" name="rating">
                        <option value="">جميع التقييمات</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} نجوم
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="shop_id" class="form-label">المتجر</label>
                    <select class="form-control" id="shop_id" name="shop_id">
                        <option value="">جميع المتاجر</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="is_verified" class="form-label">الحالة</label>
                    <select class="form-control" id="is_verified" name="is_verified">
                        <option value="">الجميع</option>
                        <option value="1" {{ request('is_verified') === '1' ? 'selected' : '' }}>محقق</option>
                        <option value="0" {{ request('is_verified') === '0' ? 'selected' : '' }}>غير محقق</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> فلترة
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع التقييمات ({{ $reviews->total() }})</h6>
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
                            <th>الرقم</th>
                            <th>المستخدم</th>
                            <th>المتجر</th>
                            <th>التقييم</th>
                            <th>التعليق</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $review->id }}</td>
                                <td>
                                    <strong>{{ $review->user->name }}</strong><br>
                                    <small class="text-muted">{{ $review->user->email }}</small>
                                </td>
                                <td>
                                    @if($review->shop)
                                        <a href="{{ route('admin.shops.show', $review->shop->id) }}" 
                                           class="text-decoration-none">
                                            {{ $review->shop->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Shop Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="ms-2 badge bg-primary text-white">{{ $review->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $review->comment ?? 'لا يوجد تعليق' }}
                                    </div>
                                </td>
                                <td>
                                    @if($review->is_verified)
                                        <span class="badge bg-success text-white">محقق</span>
                                    @else
                                        <span class="badge bg-secondary text-white">غير محقق</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $review->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.reviews.show', $review->id) }}" 
                                       class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye text-white"></i>
                                    </a>
                                    
                                    @if(!$review->is_verified)
                                        <form action="{{ route('admin.reviews.verify', $review->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="تحقيق">
                                                <i class="fas fa-check text-white"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.reviews.unverify', $review->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning" title="إلغاء التحقيق">
                                                <i class="fas fa-times text-white"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash text-white"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد تقييمات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links() }}
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

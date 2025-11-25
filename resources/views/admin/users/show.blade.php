@extends('layouts.admin')

@section('title', 'عرض المستخدم: ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user"></i> عرض المستخدم: {{ $user->name }}
        </h1>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Info Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المستخدم</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>الاسم:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>البريد الإلكتروني:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهاتف:</strong></td>
                                    <td>{{ $user->phone ?? 'غير محدد' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>نوع المستخدم:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'shop_owner' ? 'warning' : 'info') }}">
                                            @if($user->user_type == 'admin') مدير
                                            @elseif($user->user_type == 'shop_owner') صاحب متجر
                                            @else عميل
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>المدينة:</strong></td>
                                    <td>{{ $user->city->name ?? 'غير محدد' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>الحالة:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ التسجيل:</strong></td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>آخر تحديث:</strong></td>
                                    <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>آخر تسجيل دخول:</strong></td>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>الجنس:</strong></td>
                                    <td>{{ $user->gender == 'male' ? 'ذكر' : ($user->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($user->address)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>العنوان:</strong>
                            <p class="text-muted">{{ $user->address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->bio)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>النبذة الشخصية:</strong>
                            <p class="text-muted">{{ $user->bio }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">إحصائيات المستخدم</h6>
                </div>
                <div class="card-body">
                    @if($user->user_type == 'shop_owner')
                        <div class="text-center mb-3">
                            <h4 class="text-primary">{{ $user->shops->count() }}</h4>
                            <small class="text-muted">المتاجر المملوكة</small>
                        </div>
                        <div class="text-center mb-3">
                            <h4 class="text-success">{{ $user->shops->where('is_active', true)->count() }}</h4>
                            <small class="text-muted">المتاجر النشطة</small>
                        </div>
                    @endif

                    @if($user->user_type == 'customer')
                        <div class="text-center mb-3">
                            <h4 class="text-info">{{ $user->ratings->count() }}</h4>
                            <small class="text-muted">التقييمات المكتوبة</small>
                        </div>
                    @endif

                    <div class="text-center mb-3">
                        <h4 class="text-warning">{{ $user->created_at->diffInDays() }}</h4>
                        <small class="text-muted">أيام منذ التسجيل</small>
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            @if($user->profile_image)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">صورة الملف الشخصي</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $user->profile_image }}" 
                         alt="{{ $user->name }}" 
                         class="img-fluid rounded-circle" 
                         style="max-width: 200px; max-height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- User's Shops (if shop owner) -->
    @if($user->user_type == 'shop_owner' && $user->shops->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المتاجر المملوكة</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>اسم المتجر</th>
                                    <th>الفئة</th>
                                    <th>المدينة</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->shops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td>{{ $shop->category->name ?? 'غير محدد' }}</td>
                                    <td>{{ $shop->city->name ?? 'غير محدد' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $shop->is_active ? 'success' : 'danger' }}">
                                            {{ $shop->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>{{ $shop->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User's Ratings (if customer) -->
    @if($user->user_type == 'customer' && $user->ratings->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">التقييمات المكتوبة</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>المتجر</th>
                                    <th>التقييم</th>
                                    <th>التعليق</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->ratings->take(10) as $rating)
                                <tr>
                                    <td>{{ $rating->shop->name ?? 'غير محدد' }}</td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>{{ Str::limit($rating->comment, 50) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $rating->status == 'approved' ? 'success' : ($rating->status == 'pending' ? 'warning' : 'danger') }}">
                                            @if($rating->status == 'approved') موافق عليه
                                            @elseif($rating->status == 'pending') في الانتظار
                                            @else مرفوض
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $rating->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.ratings.show', $rating) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ratings.edit', $rating) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Admin Notes -->
    @if($user->notes && auth()->user()->user_type == 'admin')
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">ملاحظات إدارية</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $user->notes }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
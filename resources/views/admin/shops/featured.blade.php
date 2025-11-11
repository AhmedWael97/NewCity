@extends('layouts.admin')

@section('title', 'إدارة متجر مميز - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star"></i> إدارة متجر مميز: {{ $shop->name }}
        </h1>
        <div>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
            <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i> تعديل المتجر
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إعدادات المتجر المميز</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shops.featured.update', $shop) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>ملاحظة:</strong> المتاجر المميزة تظهر في قسم خاص في الصفحة الرئيسية لكل مدينة وتحصل على أولوية أعلى في نتائج البحث.
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch custom-control-lg">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" 
                                       {{ old('is_featured', $shop->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_featured">
                                    <strong>متجر مميز</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                فعّل هذا الخيار لجعل المتجر يظهر في قائمة المتاجر المميزة
                            </small>
                        </div>

                        <hr>

                        <div id="featuredOptions" style="{{ old('is_featured', $shop->is_featured) ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="featured_priority">الأولوية <span class="text-danger">*</span></label>
                                <input type="number" name="featured_priority" id="featured_priority" 
                                       class="form-control @error('featured_priority') is-invalid @enderror" 
                                       value="{{ old('featured_priority', $shop->featured_priority ?? 10) }}" 
                                       min="0" max="100">
                                @error('featured_priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    المتاجر ذات الأولوية الأعلى تظهر أولاً (0-100). القيمة الافتراضية: 10
                                </small>
                                
                                <!-- Priority Guide -->
                                <div class="mt-2 p-2 bg-light rounded">
                                    <strong class="text-muted">دليل الأولويات:</strong>
                                    <ul class="small mb-0 mt-1">
                                        <li><strong>80-100:</strong> أولوية قصوى (عروض خاصة، شراكات)</li>
                                        <li><strong>50-79:</strong> أولوية عالية (متاجر مدفوعة)</li>
                                        <li><strong>20-49:</strong> أولوية متوسطة (متاجر نشطة)</li>
                                        <li><strong>0-19:</strong> أولوية منخفضة (متاجر جديدة)</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="featured_until">صالح حتى تاريخ</label>
                                <input type="date" name="featured_until" id="featured_until" 
                                       class="form-control @error('featured_until') is-invalid @enderror" 
                                       value="{{ old('featured_until', $shop->featured_until ? $shop->featured_until->format('Y-m-d') : '') }}"
                                       min="{{ date('Y-m-d') }}">
                                @error('featured_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    اتركه فارغاً لجعل الميزة دائمة. أو حدد تاريخ انتهاء للميزة.
                                </small>
                            </div>

                            <!-- Quick Duration Buttons -->
                            <div class="mb-3">
                                <label class="d-block mb-2"><strong>أو اختر مدة سريعة:</strong></label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setFeaturedDuration(7)">
                                        7 أيام
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setFeaturedDuration(14)">
                                        14 يوم
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setFeaturedDuration(30)">
                                        30 يوم
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setFeaturedDuration(60)">
                                        60 يوم
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setFeaturedDuration(90)">
                                        90 يوم
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFeaturedDuration(0)">
                                        دائم
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعدادات
                            </button>
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Shop Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-store"></i> معلومات المتجر
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>الاسم:</strong><br>{{ $shop->name }}</p>
                    <p><strong>المدينة:</strong><br>
                        <span class="badge badge-info">{{ $shop->city->name }}</span>
                    </p>
                    <p><strong>التصنيف:</strong><br>
                        <span class="badge badge-secondary">{{ $shop->category->name_ar ?? 'N/A' }}</span>
                    </p>
                    <p><strong>الحالة:</strong><br>
                        <span class="badge badge-{{ $shop->status === 'active' ? 'success' : 'danger' }}">
                            {{ $shop->status === 'active' ? 'نشط' : 'غير نشط' }}
                        </span>
                    </p>
                    <p><strong>التحقق:</strong><br>
                        <span class="badge badge-{{ $shop->is_verified ? 'success' : 'warning' }}">
                            {{ $shop->is_verified ? 'محقق' : 'غير محقق' }}
                        </span>
                    </p>
                    <p><strong>التقييم:</strong><br>
                        @if($shop->rating)
                            <span class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $shop->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </span>
                            <small>({{ $shop->rating }} - {{ $shop->review_count }} تقييم)</small>
                        @else
                            <span class="text-muted">لا توجد تقييمات</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Featured Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-{{ $shop->is_featured ? 'success' : 'secondary' }}">
                        <i class="fas fa-star"></i> حالة الميزة
                    </h6>
                </div>
                <div class="card-body">
                    @if($shop->is_featured)
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle"></i>
                            <strong>متجر مميز حالياً</strong>
                        </div>
                        
                        <p><strong>الأولوية:</strong><br>
                            <span class="badge badge-warning" style="font-size: 16px;">
                                {{ $shop->featured_priority }}
                            </span>
                        </p>
                        
                        <p><strong>صالح حتى:</strong><br>
                            @if($shop->featured_until)
                                {{ $shop->featured_until->format('Y-m-d') }}
                                <br>
                                <small class="text-muted">
                                    ({{ $shop->featured_until->diffForHumans() }})
                                </small>
                            @else
                                <span class="badge badge-info">دائم</span>
                            @endif
                        </p>

                        <p><strong>الحالة:</strong><br>
                            @if($shop->isFeatured())
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">منتهي</span>
                            @endif
                        </p>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle"></i>
                            <strong>المتجر غير مميز حالياً</strong>
                            <p class="small mb-0 mt-2">قم بتفعيل خيار "متجر مميز" لإضافة المتجر للقائمة المميزة.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> إحصائيات
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>عدد المنتجات:</strong> {{ $shop->products()->count() }}</p>
                    <p><strong>عدد الخدمات:</strong> {{ $shop->services()->count() }}</p>
                    <p><strong>عدد التقييمات:</strong> {{ $shop->review_count }}</p>
                    <p><strong>تاريخ الإضافة:</strong><br>
                        <small>{{ $shop->created_at->format('Y-m-d H:i') }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle featured options
    document.getElementById('is_featured').addEventListener('change', function() {
        const featuredOptions = document.getElementById('featuredOptions');
        if (this.checked) {
            featuredOptions.style.display = 'block';
        } else {
            featuredOptions.style.display = 'none';
        }
    });

    // Set featured duration
    function setFeaturedDuration(days) {
        const input = document.getElementById('featured_until');
        if (days === 0) {
            input.value = '';
        } else {
            const date = new Date();
            date.setDate(date.getDate() + days);
            input.value = date.toISOString().split('T')[0];
        }
    }
</script>
@endpush
@endsection

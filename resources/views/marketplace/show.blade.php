@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if($item->images && count($item->images) > 0)
                    <div id="itemGallery" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach($item->images as $index => $image)
                            <button type="button" data-bs-target="#itemGallery" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach($item->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ $image }}" class="d-block w-100" alt="{{ $item->title }}" 
                                     style="height: 400px; object-fit: contain; background: #f8f9fa;">
                            </div>
                            @endforeach
                        </div>
                        @if(count($item->images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#itemGallery" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#itemGallery" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Item Details -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Title and Price -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="mb-2">{{ $item->title }}</h2>
                            <div class="mb-2">
                                @if($item->is_sponsored && $item->sponsored_until > now())
                                <span class="badge bg-gradient-warning">
                                    <i class="fas fa-star"></i> إعلان مميز
                                </span>
                                @endif
                                @switch($item->condition)
                                    @case('new')
                                        <span class="badge bg-success">جديد</span>
                                        @break
                                    @case('like_new')
                                        <span class="badge bg-info">شبه جديد</span>
                                        @break
                                    @case('good')
                                        <span class="badge bg-primary">جيد</span>
                                        @break
                                    @case('fair')
                                        <span class="badge bg-warning">مقبول</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="text-primary mb-0">{{ number_format($item->price, 0) }} جنيه</h3>
                            @if($item->is_negotiable)
                            <small class="text-muted">السعر قابل للتفاوض</small>
                            @endif
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <strong>المدينة:</strong> {{ $item->city->name }}
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-tag text-success"></i>
                            <strong>التصنيف:</strong> {{ $item->category->name }}
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-clock text-info"></i>
                            <strong>منذ:</strong> {{ $item->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <hr>

                    <!-- Description -->
                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> الوصف</h5>
                    <p class="text-justify" style="white-space: pre-wrap;">{{ $item->description }}</p>

                    <hr>

                    <!-- Statistics -->
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-0">{{ $item->view_count }}</h4>
                                <small class="text-muted"><i class="fas fa-eye"></i> مشاهدة</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-0">{{ $item->contact_count }}</h4>
                                <small class="text-muted"><i class="fas fa-phone"></i> اتصال</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Seller Contact Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> معلومات البائع</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>الاسم:</strong> {{ $item->user->name }}
                    </div>
                    
                    @if($item->contact_phone)
                    <a href="tel:{{ $item->contact_phone }}" class="btn btn-success w-100 mb-2" 
                       onclick="recordContact({{ $item->id }})">
                        <i class="fas fa-phone"></i> اتصال: {{ $item->contact_phone }}
                    </a>
                    @endif

                    @if($item->contact_whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->contact_whatsapp) }}" 
                       target="_blank" class="btn btn-success w-100" style="background: #25D366;"
                       onclick="recordContact({{ $item->id }})">
                        <i class="fab fa-whatsapp"></i> واتساب: {{ $item->contact_whatsapp }}
                    </a>
                    @endif

                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle"></i> اضغط للاتصال بالبائع
                    </small>
                </div>
            </div>

            <!-- Owner Actions (if owner) -->
            @auth
                @if($item->isOwnedBy(Auth::user()))
                <div class="card shadow mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-tools"></i> إدارة الإعلان</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>المشاهدات المتبقية:</strong>
                            <div class="progress" style="height: 20px;">
                                @php
                                    $remaining = $item->remainingViews();
                                    $percentage = ($remaining / ($item->max_views + $item->sponsored_views_boost)) * 100;
                                @endphp
                                <div class="progress-bar bg-{{ $percentage < 20 ? 'danger' : ($percentage < 40 ? 'warning' : 'success') }}" 
                                     style="width: {{ $percentage }}%">
                                    {{ $remaining }} / {{ $item->max_views + $item->sponsored_views_boost }}
                                </div>
                            </div>
                        </div>

                        @if($item->status === 'active')
                        <a href="{{ route('marketplace.edit', $item->id) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit"></i> تعديل الإعلان
                        </a>

                        @if($remaining < 20 || ($item->is_sponsored && $item->sponsored_until < now()->addDays(3)))
                        <a href="{{ route('marketplace.sponsor', $item->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-rocket"></i> رعاية الإعلان
                        </a>
                        @endif

                        <form action="{{ route('marketplace.mark-sold', $item->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100"
                                    onclick="return confirm('هل تم بيع هذا المنتج؟')">
                                <i class="fas fa-check"></i> وضع علامة "مباع"
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('marketplace.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                <i class="fas fa-trash"></i> حذف الإعلان
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- QR Code Card -->
            <div class="card shadow mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-qrcode"></i> رمز QR للإعلان</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ route('marketplace.qr', $item->id) }}" 
                         alt="QR Code" 
                         class="img-fluid mb-3" 
                         style="max-width: 200px;">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-info-circle"></i> امسح الرمز للوصول السريع للإعلان
                    </p>
                    @auth
                        @if($item->isOwnedBy(Auth::user()))
                        <a href="{{ route('marketplace.qr.download', $item->id) }}" 
                           class="btn btn-outline-info btn-sm w-100" download>
                            <i class="fas fa-download"></i> تحميل رمز QR
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Safety Tips -->
            <div class="card shadow mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-shield-alt"></i> نصائح للأمان</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>التقي بالبائع في مكان عام آمن</li>
                        <li>تفحص المنتج جيداً قبل الشراء</li>
                        <li>لا تدفع مقدماً قبل استلام المنتج</li>
                        <li>كن حذراً من الأسعار المنخفضة جداً</li>
                        <li>تجنب مشاركة معلومات شخصية حساسة</li>
                    </ul>
                </div>
            </div>

            <!-- Report (if logged in) -->
            @auth
                @if(!$item->isOwnedBy(Auth::user()))
                <div class="card shadow border-danger">
                    <div class="card-body text-center">
                        <button class="btn btn-outline-danger btn-sm" onclick="alert('سيتم إضافة نظام الإبلاغ قريباً')">
                            <i class="fas fa-flag"></i> الإبلاغ عن إعلان مخالف
                        </button>
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Related Items -->
    @if($relatedItems->count() > 0)
    <div class="mt-5">
        <h4 class="mb-4"><i class="fas fa-list"></i> إعلانات مشابهة</h4>
        <div class="row g-4">
            @foreach($relatedItems as $related)
            <div class="col-md-3">
                <div class="card h-100 shadow-sm hover-card">
                    <a href="{{ route('marketplace.show', $related->id) }}" class="text-decoration-none text-dark">
                        @if($related->images && count($related->images) > 0)
                        <div style="position: relative;">
                            <img src="{{ $related->images[0] }}" class="card-img-top" alt="{{ $related->title }}"
                                 style="height: 150px; object-fit: cover;">
                            @if($related->is_sponsored && $related->sponsored_until > now())
                            <span class="badge bg-gradient-warning position-absolute top-0 start-0 m-2">
                                <i class="fas fa-star"></i> مميز
                            </span>
                            @endif
                        </div>
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($related->title, 40) }}</h6>
                            <p class="card-text text-primary fw-bold mb-1">
                                {{ number_format($related->price, 0) }} جنيه
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $related->city->name }}
                            </small>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.hover-card {
    transition: all 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15) !important;
}
.text-justify {
    text-align: justify;
}
</style>

<script>
function recordContact(itemId) {
    fetch(`/marketplace/${itemId}/contact`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
}
</script>
@endsection

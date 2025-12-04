@extends('layouts.app')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}"><i class="fas fa-store"></i> السوق</a></li>
                <li class="breadcrumb-item"><a href="{{ route('marketplace.index', ['category_id' => $item->category_id]) }}">{{ $item->category->name }}</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($item->title, 50) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container my-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('marketplace.index') }}" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للإعلانات</span>
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <div class="image-gallery-card">
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
                            <img src="{{ $image }}" class="d-block w-100" alt="{{ $item->title }}">
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
                @else
                <div class="no-image-placeholder">
                    <i class="fas fa-image"></i>
                    <p>لا توجد صور</p>
                </div>
                @endif
                
                @if($item->is_sponsored && $item->sponsored_until > now())
                <div class="sponsored-badge-large">
                    <i class="fas fa-star"></i>
                    <span>إعلان مميز</span>
                </div>
                @endif
            </div>

            <!-- Item Details -->
            <div class="item-details-card">
                <!-- Title and Price Header -->
                <div class="item-header">
                    <div class="item-header-top">
                        <div class="item-title-section">
                            <h1 class="item-title">{{ $item->title }}</h1>
                            <div class="item-badges">
                                @switch($item->condition)
                                    @case('new')
                                        <span class="condition-badge new">🆕 جديد</span>
                                        @break
                                    @case('like_new')
                                        <span class="condition-badge like-new">✨ شبه جديد</span>
                                        @break
                                    @case('good')
                                        <span class="condition-badge good">👍 جيد</span>
                                        @break
                                    @case('fair')
                                        <span class="condition-badge fair">⭐ مقبول</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="item-price-section">
                            <div class="price-tag">
                                <span class="price-amount">{{ number_format($item->price, 0) }}</span>
                                <span class="price-currency">جنيه</span>
                            </div>
                            @if($item->is_negotiable)
                            <span class="price-negotiable">
                                <i class="fas fa-handshake"></i> قابل للتفاوض
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="quick-info">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">المدينة</span>
                                <span class="info-value">{{ $item->city->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">الفئة</span>
                                <span class="info-value">{{ $item->category->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">تم النشر</span>
                                <span class="info-value">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">البائع</span>
                                <span class="info-value">{{ $item->user->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="description-section">
                    <h3 class="section-title">
                        <i class="fas fa-align-right"></i>
                        <span>الوصف</span>
                    </h3>
                    <div class="description-content">{{ $item->description }}</div>
                </div>

                <!-- Statistics Section -->
                <div class="statistics-section">
                    <h3 class="section-title">
                        <i class="fas fa-chart-line"></i>
                        <span>إحصائيات الإعلان</span>
                    </h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon views">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($item->view_count) }}</div>
                                <div class="stat-label">مشاهدة</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon contacts">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($item->contact_count) }}</div>
                                <div class="stat-label">اتصال</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon time">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ $item->created_at->diffInDays(now()) }}</div>
                                <div class="stat-label">يوم</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Seller Contact Card -->
            <div class="seller-card sticky-sidebar">
                <div class="seller-header">
                    <div class="seller-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="seller-info">
                        <h4 class="seller-name">{{ $item->user->name }}</h4>
                        <p class="seller-label">البائع</p>
                    </div>
                </div>
                
                <div class="contact-actions">
                    @if($item->contact_phone)
                    <a href="tel:{{ $item->contact_phone }}" class="contact-btn phone" 
                       onclick="recordContact({{ $item->id }})">
                        <i class="fas fa-phone-alt"></i>
                        <span>{{ $item->contact_phone }}</span>
                    </a>
                    @endif

                    @if($item->contact_whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->contact_whatsapp) }}" 
                       target="_blank" class="contact-btn whatsapp"
                       onclick="recordContact({{ $item->id }})">
                        <i class="fab fa-whatsapp"></i>
                        <span>{{ $item->contact_whatsapp }}</span>
                    </a>
                    @endif
                </div>

                <div class="contact-note">
                    <i class="fas fa-info-circle"></i>
                    <span>سيتم تسجيل محاولات الاتصال تلقائياً</span>
                </div>
            </div>

            <!-- Owner Actions (if owner) -->
            @auth
                @if($item->isOwnedBy(Auth::user()))
                <div class="management-card">
                    <div class="management-header">
                        <i class="fas fa-tools"></i>
                        <span>إدارة الإعلان</span>
                    </div>
                    <div class="management-body">
                        <div class="views-progress">
                            <div class="progress-label">
                                <span>المشاهدات المتبقية</span>
                                @php
                                    $remaining = $item->remainingViews();
                                    $total = $item->max_views + $item->sponsored_views_boost;
                                    $percentage = ($remaining / $total) * 100;
                                @endphp
                                <span class="progress-count">{{ $remaining }}/{{ $total }}</span>
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill {{ $percentage < 20 ? 'danger' : ($percentage < 40 ? 'warning' : 'success') }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        @if($item->status === 'active')
                        <a href="{{ route('marketplace.edit', $item->id) }}" class="management-btn edit">
                            <i class="fas fa-edit"></i>
                            <span>تعديل الإعلان</span>
                        </a>

                        @if($remaining < 20 || ($item->is_sponsored && $item->sponsored_until < now()->addDays(3)))
                        <a href="{{ route('marketplace.sponsor', $item->id) }}" class="management-btn sponsor">
                            <i class="fas fa-rocket"></i>
                            <span>رعاية الإعلان</span>
                        </a>
                        @endif

                        <form action="{{ route('marketplace.mark-sold', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="management-btn sold"
                                    onclick="return confirm('هل تم بيع هذا المنتج؟')">
                                <i class="fas fa-check-circle"></i>
                                <span>تم البيع</span>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('marketplace.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="management-btn delete"
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                <i class="fas fa-trash-alt"></i>
                                <span>حذف الإعلان</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- QR Code Card -->
            <div class="qr-card">
                <div class="qr-header">
                    <i class="fas fa-qrcode"></i>
                    <span>مشاركة الإعلان</span>
                </div>
                <div class="qr-body">
                    <img src="{{ route('marketplace.qr', $item->id) }}" 
                         alt="QR Code" 
                         class="qr-image">
                    <p class="qr-hint">
                        <i class="fas fa-mobile-alt"></i>
                        امسح الرمز للوصول السريع
                    </p>
                    @auth
                        @if($item->isOwnedBy(Auth::user()))
                        <a href="{{ route('marketplace.qr.download', $item->id) }}" 
                           class="qr-download-btn" download>
                            <i class="fas fa-download"></i>
                            <span>تحميل رمز QR</span>
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Safety Tips -->
            <div class="safety-card">
                <div class="safety-header">
                    <i class="fas fa-shield-alt"></i>
                    <span>نصائح للأمان</span>
                </div>
                <div class="safety-body">
                    <div class="safety-tip">
                        <i class="fas fa-check-circle"></i>
                        <span>التقي بالبائع في مكان عام آمن</span>
                    </div>
                    <div class="safety-tip">
                        <i class="fas fa-check-circle"></i>
                        <span>تفحص المنتج جيداً قبل الشراء</span>
                    </div>
                    <div class="safety-tip">
                        <i class="fas fa-check-circle"></i>
                        <span>لا تدفع مقدماً قبل استلام المنتج</span>
                    </div>
                    <div class="safety-tip">
                        <i class="fas fa-check-circle"></i>
                        <span>كن حذراً من الأسعار المنخفضة جداً</span>
                    </div>
                    <div class="safety-tip">
                        <i class="fas fa-check-circle"></i>
                        <span>تجنب مشاركة معلومات شخصية حساسة</span>
                    </div>
                </div>
            </div>

            <!-- Report Card (if logged in and not owner) -->
            @auth
                @if(!$item->isOwnedBy(Auth::user()))
                <div class="report-card">
                    <button class="report-btn" onclick="alert('سيتم إضافة نظام الإبلاغ قريباً')">
                        <i class="fas fa-flag"></i>
                        <span>الإبلاغ عن إعلان مخالف</span>
                    </button>
                </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Related Items -->
    @if($relatedItems->count() > 0)
    <div class="related-section">
        <h2 class="related-title">
            <i class="fas fa-boxes"></i>
            <span>إعلانات مشابهة</span>
        </h2>
        <div class="related-grid">
            @foreach($relatedItems as $related)
            <div class="related-item">
                <a href="{{ route('marketplace.show', $related->id) }}" class="related-link">
                    @if($related->is_sponsored && $related->sponsored_until > now())
                    <div class="related-sponsored-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    @endif
                    
                    <div class="related-image">
                        @if($related->images && count($related->images) > 0)
                        <img src="{{ $related->images[0] }}" alt="{{ $related->title }}" loading="lazy">
                        @else
                        <div class="related-no-image">
                            <i class="fas fa-image"></i>
                        </div>
                        @endif
                    </div>
                    
                    <div class="related-body">
                        <h3 class="related-item-title">{{ Str::limit($related->title, 45) }}</h3>
                        <div class="related-price">
                            <span class="price">{{ number_format($related->price, 0) }}</span>
                            <span class="currency">جنيه</span>
                        </div>
                        <div class="related-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $related->city->name }}</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
<style>
/* Breadcrumb */
.breadcrumb-section {
    background: #f8f9fa;
    padding: 15px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid #e9ecef;
}

.breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0;
}

.breadcrumb-item a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s;
}

.breadcrumb-item a:hover {
    color: #764ba2;
}

.breadcrumb-item.active {
    color: #666;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #999;
}

/* Back Button */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    color: #333;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateX(5px);
}

/* Image Gallery */
.image-gallery-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
    position: relative;
}

.carousel-inner {
    height: 500px;
}

.carousel-item img {
    height: 100%;
    object-fit: contain;
    background: #f8f9fa;
}

.no-image-placeholder {
    height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    color: #999;
}

.no-image-placeholder i {
    font-size: 5rem;
    margin-bottom: 15px;
}

.sponsored-badge-large {
    position: absolute;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1rem;
    z-index: 10;
    box-shadow: 0 4px 20px rgba(245, 87, 108, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Item Details Card */
.item-details-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 25px;
}

.item-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
}

.item-header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    gap: 20px;
}

.item-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 15px;
    line-height: 1.3;
}

.item-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.condition-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
}

.condition-badge.new {
    background: rgba(40, 167, 69, 0.9);
    color: white;
}

.condition-badge.like-new {
    background: rgba(23, 162, 184, 0.9);
    color: white;
}

.condition-badge.good {
    background: rgba(255, 193, 7, 0.9);
    color: #000;
}

.condition-badge.fair {
    background: rgba(108, 117, 125, 0.9);
    color: white;
}

.price-tag {
    text-align: left;
    background: rgba(255, 255, 255, 0.15);
    padding: 15px 25px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 900;
    display: block;
    line-height: 1;
}

.price-currency {
    font-size: 1.2rem;
    font-weight: 600;
    opacity: 0.9;
}

.price-negotiable {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 6px 12px;
    background: rgba(40, 167, 69, 0.2);
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.quick-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.1);
    padding: 12px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.info-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.info-content {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.8rem;
    opacity: 0.9;
}

.info-value {
    font-size: 1rem;
    font-weight: 700;
}

/* Description Section */
.description-section,
.statistics-section {
    padding: 30px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
}

.section-title i {
    color: #667eea;
}

.description-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #555;
    white-space: pre-wrap;
}

/* Statistics */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
}

.stat-icon.views {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.contacts {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-icon.time {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: #333;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

/* Sidebar Cards */
.sticky-sidebar {
    position: sticky;
    top: 20px;
}

.seller-card,
.management-card,
.qr-card,
.safety-card,
.report-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    overflow: hidden;
}

/* Seller Card */
.seller-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.seller-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    backdrop-filter: blur(10px);
}

.seller-name {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
}

.seller-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
}

.contact-actions {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
}

.contact-btn.phone {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.contact-btn.phone:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
}

.contact-btn.whatsapp {
    background: #25D366;
    color: white;
}

.contact-btn.whatsapp:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
}

.contact-note {
    padding: 0 20px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #666;
}

/* Management Card */
.management-header {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 1.1rem;
}

.management-body {
    padding: 20px;
}

.views-progress {
    margin-bottom: 20px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
}

.progress-bar-custom {
    height: 12px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progress-fill.success {
    background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
}

.progress-fill.warning {
    background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
}

.progress-fill.danger {
    background: linear-gradient(90deg, #ff6b6b 0%, #ee5a6f 100%);
}

.management-btn {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.management-btn.edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.management-btn.sponsor {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.management-btn.sold {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.management-btn.delete {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
}

.management-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

/* QR Card */
.qr-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 1.1rem;
}

.qr-body {
    padding: 30px;
    text-align: center;
}

.qr-image {
    max-width: 200px;
    border-radius: 12px;
    border: 3px solid #e9ecef;
    margin-bottom: 15px;
}

.qr-hint {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.qr-download-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.qr-download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Safety Card */
.safety-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 1.1rem;
}

.safety-body {
    padding: 20px;
}

.safety-tip {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f5;
    font-size: 0.95rem;
    color: #555;
}

.safety-tip:last-child {
    border-bottom: none;
}

.safety-tip i {
    color: #28a745;
    margin-top: 2px;
}

/* Report Card */
.report-card {
    padding: 20px;
}

.report-btn {
    width: 100%;
    padding: 12px;
    background: white;
    border: 2px solid #dc3545;
    border-radius: 10px;
    color: #dc3545;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.report-btn:hover {
    background: #dc3545;
    color: white;
}

/* Related Section */
.related-section {
    margin-top: 60px;
}

.related-title {
    font-size: 2rem;
    font-weight: 800;
    color: #333;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.related-title i {
    color: #667eea;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.related-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
}

.related-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.related-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.related-sponsored-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
}

.related-image {
    height: 180px;
    overflow: hidden;
    background: #f8f9fa;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.related-item:hover .related-image img {
    transform: scale(1.1);
}

.related-no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #dee2e6;
}

.related-body {
    padding: 20px;
}

.related-item-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.4;
}

.related-price {
    margin-bottom: 10px;
}

.related-price .price {
    font-size: 1.5rem;
    font-weight: 800;
    color: #667eea;
}

.related-price .currency {
    font-size: 1rem;
    font-weight: 600;
    color: #764ba2;
}

.related-location {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
    font-size: 0.9rem;
}

.related-location i {
    color: #667eea;
}

/* Responsive */
@media (max-width: 991px) {
    .sticky-sidebar {
        position: static;
    }
    
    .item-header-top {
        flex-direction: column;
    }
    
    .price-tag {
        width: 100%;
        text-align: center;
    }
    
    .item-price-section {
        width: 100%;
    }
    
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 768px) {
    /* Breadcrumb Mobile */
    .breadcrumb-section {
        padding: 10px 0;
    }
    
    .breadcrumb {
        font-size: 0.85rem;
        flex-wrap: wrap;
    }
    
    .breadcrumb-item {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Back Button Mobile */
    .back-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    
    /* Image Gallery Mobile */
    .carousel-inner {
        height: 280px;
    }
    
    .no-image-placeholder {
        height: 280px;
    }
    
    .no-image-placeholder i {
        font-size: 3.5rem;
    }
    
    .sponsored-badge-large {
        top: 10px;
        right: 10px;
        padding: 8px 16px;
        font-size: 0.85rem;
    }
    
    /* Item Header Mobile */
    .item-header {
        padding: 20px;
    }
    
    .item-header-top {
        margin-bottom: 20px;
    }
    
    .item-title {
        font-size: 1.4rem;
        margin-bottom: 12px;
    }
    
    .condition-badge {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
    
    .price-tag {
        padding: 12px 20px;
    }
    
    .price-amount {
        font-size: 2rem;
    }
    
    .price-currency {
        font-size: 1rem;
    }
    
    .price-negotiable {
        font-size: 0.8rem;
        padding: 5px 10px;
    }
    
    /* Quick Info Mobile */
    .quick-info {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .info-item {
        padding: 10px;
    }
    
    .info-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
    
    .info-label {
        font-size: 0.75rem;
    }
    
    .info-value {
        font-size: 0.9rem;
    }
    
    /* Description Section Mobile */
    .description-section,
    .statistics-section {
        padding: 20px;
    }
    
    .section-title {
        font-size: 1.2rem;
        gap: 10px;
        margin-bottom: 15px;
        padding-bottom: 12px;
    }
    
    .section-title i {
        font-size: 1.1rem;
    }
    
    .description-content {
        font-size: 0.95rem;
        line-height: 1.7;
    }
    
    /* Statistics Mobile */
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    
    .stat-card {
        padding: 15px 10px;
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    /* Seller Card Mobile */
    .seller-header {
        padding: 20px;
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .seller-avatar {
        width: 55px;
        height: 55px;
        font-size: 1.6rem;
    }
    
    .seller-name {
        font-size: 1.2rem;
    }
    
    .seller-label {
        font-size: 0.85rem;
    }
    
    .contact-actions {
        padding: 15px;
        gap: 10px;
    }
    
    .contact-btn {
        padding: 12px;
        font-size: 0.95rem;
    }
    
    .contact-note {
        padding: 0 15px 15px;
        font-size: 0.8rem;
    }
    
    /* Management Card Mobile */
    .management-header,
    .qr-header,
    .safety-header {
        padding: 12px 15px;
        font-size: 1rem;
    }
    
    .management-body,
    .qr-body,
    .safety-body {
        padding: 15px;
    }
    
    .views-progress {
        margin-bottom: 15px;
    }
    
    .progress-label {
        font-size: 0.85rem;
    }
    
    .management-btn {
        padding: 10px;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    /* QR Card Mobile */
    .qr-image {
        max-width: 160px;
        margin-bottom: 12px;
    }
    
    .qr-hint {
        font-size: 0.85rem;
        margin-bottom: 12px;
    }
    
    .qr-download-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    
    /* Safety Tips Mobile */
    .safety-tip {
        padding: 10px 0;
        font-size: 0.85rem;
        gap: 10px;
    }
    
    .safety-tip i {
        font-size: 0.9rem;
    }
    
    /* Report Card Mobile */
    .report-card {
        padding: 15px;
    }
    
    .report-btn {
        padding: 10px;
        font-size: 0.9rem;
    }
    
    /* Related Section Mobile */
    .related-section {
        margin-top: 40px;
    }
    
    .related-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
        gap: 10px;
    }
    
    .related-title i {
        font-size: 1.4rem;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .related-image {
        height: 160px;
    }
    
    .related-body {
        padding: 16px;
    }
    
    .related-item-title {
        font-size: 1rem;
    }
    
    .related-price .price {
        font-size: 1.3rem;
    }
    
    .related-price .currency {
        font-size: 0.9rem;
    }
    
    .related-location {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    /* Extra Small Mobile */
    .breadcrumb {
        font-size: 0.75rem;
    }
    
    .back-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
    
    .carousel-inner,
    .no-image-placeholder {
        height: 240px;
    }
    
    .item-header {
        padding: 15px;
    }
    
    .item-title {
        font-size: 1.2rem;
    }
    
    .price-amount {
        font-size: 1.7rem;
    }
    
    .quick-info {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        justify-content: space-between;
    }
    
    .description-section,
    .statistics-section {
        padding: 15px;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
    
    .description-content {
        font-size: 0.9rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .stat-card {
        flex-direction: row;
        justify-content: flex-start;
        text-align: right;
    }
    
    .seller-header {
        padding: 15px;
    }
    
    .seller-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.4rem;
    }
    
    .seller-name {
        font-size: 1.1rem;
    }
    
    .contact-btn {
        font-size: 0.9rem;
    }
    
    .related-title {
        font-size: 1.3rem;
    }
    
    .related-image {
        height: 180px;
    }
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {
    .contact-btn,
    .management-btn,
    .item-view-btn,
    .action-btn {
        min-height: 44px;
    }
    
    .filter-input,
    .filter-select {
        min-height: 44px;
    }
    
    .back-btn {
        min-height: 40px;
    }
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

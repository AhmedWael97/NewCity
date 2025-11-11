@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1 class="dashboard-title">
                    <i class="title-icon">🏪</i>
                    لوحة تحكم صاحب المتجر
                </h1>
                <p class="dashboard-subtitle">إدارة متاجرك ومراقبة أدائها</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('shop-owner.create-shop') }}" class="btn btn-primary">
                    <i class="btn-icon">➕</i>
                    <span>إضافة متجر جديد</span>
                </a>
                <a href="{{ route('profile') }}" class="btn btn-outline">
                    <i class="btn-icon">👤</i>
                    <span>الملف الشخصي</span>
                </a>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h2>مرحباً، {{ Auth::user()->name }}! 👋</h2>
                <p>{{ $shops->count() > 0 ? 'إليك نظرة سريعة على متاجرك' : 'ابدأ بإضافة متجرك الأول' }}</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $shops->count() }}</div>
                    <div class="stat-label">متاجر مسجلة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $shops->where('status', 'approved')->count() }}</div>
                    <div class="stat-label">متاجر موافق عليها</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $shops->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">قيد المراجعة</div>
                </div>
            </div>
        </div>

        @if($shops->count() > 0)
            <!-- Shops Grid -->
            <div class="shops-section">
                <h3 class="section-title">متاجرك</h3>
                <div class="shops-grid">
                    @foreach($shops as $shop)
                        <div class="shop-card {{ $shop->status }}">
                            <!-- Shop Status Badge -->
                            <div class="shop-status-badge status-{{ $shop->status }}">
                                @switch($shop->status)
                                    @case('pending')
                                        <i class="status-icon">⏳</i>
                                        <span>قيد المراجعة</span>
                                        @break
                                    @case('approved')
                                        <i class="status-icon">✅</i>
                                        <span>موافق عليه</span>
                                        @break
                                    @case('rejected')
                                        <i class="status-icon">❌</i>
                                        <span>مرفوض</span>
                                        @break
                                    @case('suspended')
                                        <i class="status-icon">⏸️</i>
                                        <span>معلق</span>
                                        @break
                                @endswitch
                            </div>

                            <!-- Shop Image -->
                            <div class="shop-image-container">
                                @if($shop->images && is_array($shop->images) && count($shop->images) > 0)
                                    <img src="{{ asset('storage/' . $shop->images[0]) }}" 
                                         alt="{{ $shop->name }}" 
                                         class="shop-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <div class="shop-placeholder" style="{{ $shop->images && count($shop->images) > 0 ? 'display: none;' : '' }}">
                                    <i class="placeholder-icon">🏪</i>
                                    <span>{{ $shop->name }}</span>
                                </div>
                            </div>

                            <!-- Shop Info -->
                            <div class="shop-info">
                                <h4 class="shop-name">{{ $shop->name }}</h4>
                                <p class="shop-category">
                                    <i class="info-icon">📂</i>
                                    {{ $shop->category->name ?? 'غير محدد' }}
                                </p>
                                <p class="shop-location">
                                    <i class="info-icon">📍</i>
                                    {{ $shop->city->name ?? 'غير محدد' }}
                                </p>
                                <p class="shop-created">
                                    <i class="info-icon">📅</i>
                                    تم الإنشاء {{ $shop->created_at->diffForHumans() }}
                                </p>

                                @if($shop->verification_notes)
                                    <div class="verification-notes">
                                        <p class="notes-title">ملاحظات الإدارة:</p>
                                        <p class="notes-content">{{ $shop->verification_notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Shop Actions -->
                            <div class="shop-actions">
                                @if($shop->status === 'approved')
                                    <a href="{{ route('shop.show', $shop->slug) }}" class="action-btn view-btn" target="_blank">
                                        <i class="btn-icon">👁️</i>
                                        <span>عرض المتجر</span>
                                    </a>
                                @endif
                                
                                <a href="{{ route('shop-owner.shops.edit', $shop) }}" class="action-btn edit-btn">
                                    <i class="btn-icon">✏️</i>
                                    <span>تعديل</span>
                                </a>

                                @if($shop->status === 'pending')
                                    <div class="action-btn pending-btn" title="في انتظار موافقة الإدارة">
                                        <i class="btn-icon">⏳</i>
                                        <span>قيد المراجعة</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">🏪</div>
                <h3 class="empty-title">لم تقم بإضافة أي متجر بعد</h3>
                <p class="empty-description">ابدأ رحلتك معنا بإضافة متجرك الأول واحصل على المزيد من العملاء</p>
                <a href="{{ route('shop-owner.create-shop') }}" class="btn btn-primary btn-lg">
                    <i class="btn-icon">🚀</i>
                    <span>إضافة متجرك الأول</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboard-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px 0;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.header-content {
    flex: 1;
}

.dashboard-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.title-icon {
    font-size: 2.2rem;
}

.dashboard-subtitle {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 20px;
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-content h2 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.welcome-content p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.welcome-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 5px;
}

.section-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.shops-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.shop-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
}

.shop-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.shop-status-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 2;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.status-pending {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.status-approved {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
}

.status-rejected {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.status-suspended {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    color: white;
}

.shop-image-container {
    height: 200px;
    position: relative;
    overflow: hidden;
}

.shop-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.shop-placeholder {
    height: 100%;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #1976d2;
    font-weight: 600;
}

.placeholder-icon {
    font-size: 3rem;
    margin-bottom: 8px;
}

.shop-info {
    padding: 24px;
}

.shop-name {
    font-size: 1.25rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 16px;
}

.shop-info p {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: #666;
    font-size: 0.95rem;
}

.info-icon {
    font-size: 1rem;
    width: 20px;
}

.verification-notes {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 12px;
    margin-top: 16px;
}

.notes-title {
    font-weight: 600;
    color: #856404;
    margin-bottom: 4px !important;
}

.notes-content {
    color: #856404;
    margin: 0 !important;
    font-size: 0.9rem;
}

.shop-actions {
    padding: 0 24px 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.action-btn {
    flex: 1;
    min-width: 120px;
    padding: 12px 16px;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.view-btn {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.view-btn:hover {
    background: linear-gradient(135deg, #2980b9, #1f618d);
    transform: translateY(-2px);
}

.edit-btn {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.edit-btn:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
    transform: translateY(-2px);
}

.pending-btn {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
    border: 2px dashed #dee2e6;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 24px;
    opacity: 0.6;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 12px;
}

.empty-description {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 32px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.btn-lg {
    padding: 16px 32px;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
        padding: 20px;
    }
    
    .header-actions {
        flex-direction: column;
    }
    
    .welcome-card {
        flex-direction: column;
        gap: 20px;
        padding: 30px 20px;
        text-align: center;
    }
    
    .welcome-stats {
        justify-content: center;
        gap: 20px;
    }
    
    .shops-grid {
        grid-template-columns: 1fr;
    }
    
    .shop-actions {
        flex-direction: column;
    }
    
    .action-btn {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .dashboard-container {
        padding: 10px 0;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .welcome-stats {
        flex-direction: column;
        gap: 16px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
}
</style>
@endpush
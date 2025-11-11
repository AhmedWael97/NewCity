@extends('layouts.app')

@section('content')
<div class="profile-container">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <a href="{{ route('shop-owner.dashboard') }}" class="back-btn">
                    <i class="back-icon">←</i>
                    <span>العودة للوحة التحكم</span>
                </a>
                <h1 class="page-title">
                    <i class="title-icon">👤</i>
                    الملف الشخصي
                </h1>
                <p class="page-subtitle">إدارة معلوماتك الشخصية وإعدادات الحساب</p>
            </div>
        </div>

        <div class="profile-grid">
            <!-- User Info Card -->
            <div class="profile-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="card-icon">📋</i>
                        المعلومات الشخصية
                    </h3>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name" class="form-label">
                            <span class="label-text">الاسم الكامل</span>
                            <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', Auth::user()->name) }}"
                               class="form-input @error('name') error @enderror"
                               required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <span class="label-text">البريد الإلكتروني</span>
                            <span class="required">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', Auth::user()->email) }}"
                               class="form-input @error('email') error @enderror"
                               required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <span class="label-text">رقم الهاتف</span>
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', Auth::user()->phone) }}"
                               class="form-input @error('phone') error @enderror"
                               placeholder="مثال: 0501234567">
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city_id" class="form-label">
                            <span class="label-text">المدينة</span>
                            <span class="required">*</span>
                        </label>
                        <select id="city_id" 
                                name="city_id" 
                                class="form-select @error('city_id') error @enderror"
                                required>
                            <option value="">اختر المدينة</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id', Auth::user()->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="btn-icon">💾</i>
                            <span>حفظ التغييرات</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Type & Status Card -->
            <div class="status-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="card-icon">🏷️</i>
                        نوع الحساب والحالة
                    </h3>
                </div>
                
                <div class="status-content">
                    <!-- User Type -->
                    <div class="status-item">
                        <div class="status-label">نوع الحساب:</div>
                        <div class="status-value user-type-{{ Auth::user()->user_type }}">
                            @switch(Auth::user()->user_type)
                                @case('regular')
                                    <i class="type-icon">👤</i>
                                    <span>مستخدم عادي</span>
                                    @break
                                @case('shop_owner')
                                    <i class="type-icon">🏪</i>
                                    <span>صاحب متجر</span>
                                    @break
                                @case('admin')
                                    <i class="type-icon">👨‍💼</i>
                                    <span>مدير النظام</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Verification Status -->
                    <div class="status-item">
                        <div class="status-label">حالة التحقق:</div>
                        <div class="status-value verification-{{ Auth::user()->is_verified ? 'verified' : 'pending' }}">
                            @if(Auth::user()->is_verified)
                                <i class="verification-icon">✅</i>
                                <span>تم التحقق</span>
                            @else
                                <i class="verification-icon">⏳</i>
                                <span>في انتظار التحقق</span>
                            @endif
                        </div>
                    </div>

                    <!-- Member Since -->
                    <div class="status-item">
                        <div class="status-label">عضو منذ:</div>
                        <div class="status-value">
                            <i class="date-icon">📅</i>
                            <span>{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- City -->
                    <div class="status-item">
                        <div class="status-label">المدينة:</div>
                        <div class="status-value">
                            <i class="location-icon">📍</i>
                            <span>{{ Auth::user()->city->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="password-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="card-icon">🔒</i>
                        تغيير كلمة المرور
                    </h3>
                </div>
                
                <form action="{{ route('profile.password.update') }}" method="POST" class="password-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label">
                            <span class="label-text">كلمة المرور الحالية</span>
                            <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-input @error('current_password') error @enderror"
                               required>
                        @error('current_password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <span class="label-text">كلمة المرور الجديدة</span>
                            <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input @error('password') error @enderror"
                               required>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <span class="label-text">تأكيد كلمة المرور الجديدة</span>
                            <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-input"
                               required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning">
                            <i class="btn-icon">🔄</i>
                            <span>تغيير كلمة المرور</span>
                        </button>
                    </div>
                </form>
            </div>

            @if(Auth::user()->isShopOwner())
            <!-- Shop Owner Stats Card -->
            <div class="stats-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="card-icon">📊</i>
                        إحصائياتك
                    </h3>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ Auth::user()->shops()->count() }}</div>
                        <div class="stat-label">إجمالي المتاجر</div>
                        <div class="stat-icon">🏪</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">{{ Auth::user()->shops()->approved()->count() }}</div>
                        <div class="stat-label">متاجر موافق عليها</div>
                        <div class="stat-icon">✅</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">{{ Auth::user()->shops()->pending()->count() }}</div>
                        <div class="stat-label">قيد المراجعة</div>
                        <div class="stat-icon">⏳</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">{{ Auth::user()->shops()->rejected()->count() }}</div>
                        <div class="stat-label">مرفوضة</div>
                        <div class="stat-icon">❌</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions Card -->
            <div class="actions-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="card-icon">⚡</i>
                        إجراءات سريعة
                    </h3>
                </div>
                
                <div class="actions-grid">
                    <!-- User Services Action -->
                    <a href="{{ route('user.services.index') }}" class="action-btn services">
                        <i class="action-icon">🛠️</i>
                        <span class="action-text">خدماتي</span>
                    </a>
                    
                    @if(Auth::user()->isShopOwner())
                        <a href="{{ route('shop-owner.create-shop') }}" class="action-btn create-shop">
                            <i class="action-icon">➕</i>
                            <span class="action-text">إضافة متجر جديد</span>
                        </a>
                        
                        <a href="{{ route('shop-owner.dashboard') }}" class="action-btn dashboard">
                            <i class="action-icon">📊</i>
                            <span class="action-text">لوحة التحكم</span>
                        </a>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="action-btn logout" onclick="return confirm('هل تريد تسجيل الخروج؟')">
                            <i class="action-icon">🚪</i>
                            <span class="action-text">تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px 0;
}

.page-header {
    margin-bottom: 30px;
}

.header-content {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #666;
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 16px;
    transition: color 0.3s ease;
}

.back-btn:hover {
    color: #333;
}

.back-icon {
    font-size: 1.2rem;
}

.page-title {
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

.page-subtitle {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

.profile-card,
.status-card,
.password-card,
.stats-card,
.actions-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease;
}

.profile-card:hover,
.status-card:hover,
.password-card:hover,
.stats-card:hover,
.actions-card:hover {
    transform: translateY(-2px);
}

.card-header {
    padding: 24px 24px 0;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

.card-icon {
    font-size: 1.4rem;
}

.profile-form,
.password-form {
    padding: 0 24px 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.required {
    color: #e74c3c;
    font-weight: bold;
}

.form-input,
.form-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-input.error,
.form-select.error {
    border-color: #e74c3c;
}

.error-message {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 6px;
    font-weight: 500;
}

.form-actions {
    margin-top: 24px;
}

.status-content {
    padding: 0 24px 24px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #f1f3f4;
}

.status-item:last-child {
    border-bottom: none;
}

.status-label {
    font-weight: 600;
    color: #666;
}

.status-value {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.user-type-regular {
    color: #3498db;
}

.user-type-shop_owner {
    color: #f39c12;
}

.user-type-admin {
    color: #9b59b6;
}

.verification-verified {
    color: #27ae60;
}

.verification-pending {
    color: #f39c12;
}

.type-icon,
.verification-icon,
.date-icon,
.location-icon {
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    padding: 0 24px 24px;
}

.stat-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 6px;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.stat-icon {
    position: absolute;
    top: 12px;
    right: 12px;
    font-size: 1.5rem;
    opacity: 0.3;
}

.actions-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 24px 24px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
    font-size: 1rem;
}

.action-btn.create-shop {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
}

.action-btn.dashboard {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.action-btn.logout {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-icon {
    font-size: 1.2rem;
}

.btn {
    padding: 14px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    min-width: 160px;
}

.btn-primary {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9, #1f618d);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

.btn-icon {
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 10px 0;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .card-header,
    .profile-form,
    .password-form,
    .status-content,
    .stats-grid,
    .actions-grid {
        padding-left: 16px;
        padding-right: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .header-content {
        padding: 20px;
    }
    
    .card-header {
        padding: 16px 16px 0;
    }
    
    .profile-form,
    .password-form {
        padding: 0 16px 16px;
    }
    
    .status-content,
    .actions-grid {
        padding: 0 16px 16px;
    }
    
    .stats-grid {
        padding: 0 16px 16px;
    }
    
    .status-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .btn {
        min-width: auto;
        width: 100%;
    }
}
</style>
@endpush
@endsection
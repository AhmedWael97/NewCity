@extends('layouts.minimal')

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <h1 class="auth-title">تسجيل الدخول</h1>
                    <p class="auth-subtitle">ادخل إلى حسابك للمتابعة</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <i class="input-icon">📧</i>
                            <input id="email" 
                                   type="email" 
                                   class="form-input @error('email') error @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="أدخل بريدك الإلكتروني">
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <div class="input-wrapper">
                            <i class="input-icon">🔒</i>
                            <input id="password" 
                                   type="password" 
                                   class="form-input @error('password') error @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="أدخل كلمة المرور">
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="checkbox-text">تذكرني</span>
                        </label>
                        
                        <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="btn-icon">🔓</i>
                        <span>تسجيل الدخول</span>
                    </button>
                </form>

                <div class="auth-footer">
                    <p class="auth-switch">
                        ليس لديك حساب؟ 
                        <a href="{{ route('register') }}" class="auth-link">إنشاء حساب جديد</a>
                    </p>
                    <div class="other-logins" style="margin-top: 20px; text-align: center;">
                        <p style="color: #666; font-size: 0.9rem;">أو سجل دخولك كـ:</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                            <a href="{{ route('admin.login') }}" style="color: #4e73df; text-decoration: none; padding: 8px 15px; border: 1px solid #4e73df; border-radius: 5px; font-size: 0.85rem; transition: all 0.3s;">
                                <i class="fas fa-user-shield"></i> مدير
                            </a>
                            <a href="{{ route('shop-owner.login') }}" style="color: #1cc88a; text-decoration: none; padding: 8px 15px; border: 1px solid #1cc88a; border-radius: 5px; font-size: 0.85rem; transition: all 0.3s;">
                                <i class="fas fa-store"></i> صاحب متجر
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-image">
                <div class="auth-image-content">
                    <h3>مرحباً بعودتك!</h3>
                    <p>اكتشف أفضل المتاجر والخدمات في مدينتك</p>
                    <div class="auth-features">
                        <div class="feature-item">
                            <i class="feature-icon">🏪</i>
                            <span>آلاف المتاجر</span>
                        </div>
                        <div class="feature-item">
                            <i class="feature-icon">🎯</i>
                            <span>خدمات متنوعة</span>
                        </div>
                        <div class="feature-item">
                            <i class="feature-icon">📍</i>
                            <span>في مدينتك</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 0;
}

.auth-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
}

.auth-card {
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.auth-header {
    text-align: center;
    margin-bottom: 40px;
}

.auth-title {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.auth-subtitle {
    color: #666;
    font-size: 1rem;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 16px;
    z-index: 2;
    font-size: 1.1rem;
    color: #666;
}

.form-input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
    border-color: #e53e3e;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.error-message {
    color: #e53e3e;
    font-size: 0.875rem;
    font-weight: 500;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 0;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}

.checkbox-wrapper input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-text {
    color: #666;
    font-size: 0.9rem;
}

.forgot-password {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

.btn-full {
    width: 100%;
    padding: 16px;
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 10px;
}

.auth-footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e1e5e9;
}

.auth-switch {
    color: #666;
    font-size: 0.95rem;
}

.auth-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.auth-link:hover {
    text-decoration: underline;
}

.auth-image {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    padding: 60px 40px;
}

.auth-image-content {
    text-align: center;
}

.auth-image-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 15px;
}

.auth-image-content p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 40px;
    line-height: 1.6;
}

.auth-features {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1rem;
    font-weight: 500;
}

.feature-icon {
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .auth-wrapper {
        grid-template-columns: 1fr;
        margin: 0 20px;
    }
    
    .auth-image {
        order: -1;
        padding: 40px 30px;
    }
    
    .auth-card {
        padding: 40px 30px;
    }
    
    .auth-features {
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .auth-card {
        padding: 30px 20px;
    }
    
    .auth-image {
        padding: 30px 20px;
    }
    
    .form-options {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
        text-align: center;
    }
}
</style>
@endpush
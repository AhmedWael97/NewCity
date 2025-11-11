@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9 col-md-10">
                <div class="auth-card">
                    <!-- Header Section -->
                    <div class="auth-header text-center mb-5">
                        <div class="auth-logo mb-3">
                            <div class="logo-circle">
                                <i class="logo-icon">🏪</i>
                            </div>
                        </div>
                        <h1 class="auth-title">إنشاء حساب جديد</h1>
                        <p class="auth-subtitle">انضم إلى منصة اكتشف المدن واستمتع بتجربة تسوق فريدة</p>
                        
                        <!-- Progress Indicator -->
                        <div class="progress-indicator mt-4">
                            <div class="progress-step active" data-step="1">
                                <div class="step-circle">1</div>
                                <div class="step-label">نوع الحساب</div>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step" data-step="2">
                                <div class="step-circle">2</div>
                                <div class="step-label">المعلومات الشخصية</div>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">كلمة المرور</div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                        @csrf

                        <!-- Step 1: User Type Selection -->
                        <div class="form-step active" data-step="1">
                            <div class="step-header text-center mb-4">
                                <h3 class="step-title">اختر نوع حسابك</h3>
                                <p class="step-subtitle">حدد نوع الحساب الذي يناسب احتياجاتك</p>
                            </div>

                            <div class="user-type-selector">
                                <label class="user-type-option" for="regular">
                                    <input type="radio" id="regular" name="user_type" value="regular" {{ old('user_type', 'regular') == 'regular' ? 'checked' : '' }}>
                                    <div class="user-type-card">
                                        <div class="card-icon">
                                            <i class="type-icon">👤</i>
                                        </div>
                                        <h4 class="card-title">مستخدم عادي</h4>
                                        <p class="card-description">للبحث عن المتاجر والخدمات المحلية</p>
                                        <ul class="card-features">
                                            <li>تصفح المتاجر والخدمات</li>
                                            <li>البحث المتقدم</li>
                                            <li>حفظ المفضلات</li>
                                            <li>تقييم المتاجر</li>
                                        </ul>
                                        <div class="card-badge">مجاني</div>
                                    </div>
                                </label>
                                
                                <label class="user-type-option" for="shop_owner">
                                    <input type="radio" id="shop_owner" name="user_type" value="shop_owner" {{ old('user_type') == 'shop_owner' ? 'checked' : '' }}>
                                    <div class="user-type-card business-card">
                                        <div class="card-icon">
                                            <i class="type-icon">🏪</i>
                                        </div>
                                        <h4 class="card-title">صاحب متجر</h4>
                                        <p class="card-description">لإضافة وإدارة متجرك والوصول للعملاء</p>
                                        <ul class="card-features">
                                            <li>إضافة متجرك</li>
                                            <li>إدارة المنتجات</li>
                                            <li>تحليلات المبيعات</li>
                                            <li>تواصل مع العملاء</li>
                                        </ul>
                                        <div class="card-badge premium">للأعمال</div>
                                    </div>
                                </label>
                            </div>
                            @error('user_type')
                                <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Step 2: Personal Information -->
                        <div class="form-step" data-step="2">
                            <div class="step-header text-center mb-4">
                                <h3 class="step-title">المعلومات الشخصية</h3>
                                <p class="step-subtitle">أدخل بياناتك الأساسية</p>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="name" 
                                               type="text" 
                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               required 
                                               autocomplete="name" 
                                               placeholder="أدخل اسمك الكامل">
                                        <label for="name"><i class="me-2">👤</i>الاسم الكامل</label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="email" 
                                               type="email" 
                                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               required 
                                               autocomplete="email"
                                               placeholder="أدخل بريدك الإلكتروني">
                                        <label for="email"><i class="me-2">📧</i>البريد الإلكتروني</label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="phone" 
                                               type="tel" 
                                               class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                               name="phone" 
                                               value="{{ old('phone') }}" 
                                               required
                                               placeholder="أدخل رقم هاتفك">
                                        <label for="phone"><i class="me-2">📞</i>رقم الهاتف</label>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select id="city_id" 
                                                class="form-select form-select-lg @error('city_id') is-invalid @enderror" 
                                                name="city_id" 
                                                required>
                                            <option value="">اختر مدينتك</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="city_id"><i class="me-2">📍</i>المدينة</label>
                                        @error('city_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input id="address" 
                                               type="text" 
                                               class="form-control form-control-lg @error('address') is-invalid @enderror" 
                                               name="address" 
                                               value="{{ old('address') }}"
                                               placeholder="أدخل عنوانك">
                                        <label for="address"><i class="me-2">🏠</i>العنوان <span class="text-muted small">(اختياري)</span></label>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="date_of_birth" 
                                               type="date" 
                                               class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror" 
                                               name="date_of_birth" 
                                               value="{{ old('date_of_birth') }}">
                                        <label for="date_of_birth"><i class="me-2">📅</i>تاريخ الميلاد <span class="text-muted small">(اختياري)</span></label>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Password -->
                        <div class="form-step" data-step="3">
                            <div class="step-header text-center mb-4">
                                <h3 class="step-title">إنشاء كلمة مرور آمنة</h3>
                                <p class="step-subtitle">اختر كلمة مرور قوية لحماية حسابك</p>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="password" 
                                               type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="أدخل كلمة المرور">
                                        <label for="password"><i class="me-2">🔒</i>كلمة المرور</label>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Password Strength Indicator -->
                                    <div class="password-strength mt-2">
                                        <div class="strength-meter">
                                            <div class="strength-bar"></div>
                                        </div>
                                        <div class="strength-text">قوة كلمة المرور: <span class="strength-level">ضعيف</span></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input id="password_confirmation" 
                                               type="password" 
                                               class="form-control form-control-lg" 
                                               name="password_confirmation" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="أعد إدخال كلمة المرور">
                                        <label for="password_confirmation"><i class="me-2">🔒</i>تأكيد كلمة المرور</label>
                                    </div>
                                    
                                    <!-- Password Match Indicator -->
                                    <div class="password-match mt-2" style="display: none;">
                                        <div class="match-status">
                                            <i class="match-icon"></i>
                                            <span class="match-text"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="password-requirements">
                                        <h6 class="requirements-title">متطلبات كلمة المرور:</h6>
                                        <ul class="requirements-list">
                                            <li class="requirement" data-requirement="length">
                                                <i class="requirement-icon">❌</i>
                                                <span>8 أحرف على الأقل</span>
                                            </li>
                                            <li class="requirement" data-requirement="uppercase">
                                                <i class="requirement-icon">❌</i>
                                                <span>حرف كبير واحد على الأقل</span>
                                            </li>
                                            <li class="requirement" data-requirement="lowercase">
                                                <i class="requirement-icon">❌</i>
                                                <span>حرف صغير واحد على الأقل</span>
                                            </li>
                                            <li class="requirement" data-requirement="number">
                                                <i class="requirement-icon">❌</i>
                                                <span>رقم واحد على الأقل</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="terms-section mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        أوافق على <a href="#" class="text-decoration-none">الشروط والأحكام</a> و <a href="#" class="text-decoration-none">سياسة الخصوصية</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="form-navigation mt-5">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-lg nav-btn" id="prevBtn" style="display: none;">
                                    <i class="me-2">←</i>السابق
                                </button>
                                
                                <div class="flex-grow-1"></div>
                                
                                <button type="button" class="btn btn-primary btn-lg nav-btn" id="nextBtn">
                                    التالي<i class="ms-2">→</i>
                                </button>
                                
                                <button type="submit" class="btn btn-success btn-lg nav-btn" id="submitBtn" style="display: none;">
                                    <i class="me-2">🎯</i>إنشاء الحساب
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="auth-footer text-center mt-5">
                        <div class="divider-with-text">
                            <span>أو</span>
                        </div>
                        <p class="mt-3 mb-0">
                            لديك حساب بالفعل؟ 
                            <a href="{{ route('login') }}" class="auth-link text-decoration-none fw-bold">تسجيل الدخول</a>
                        </p>
                        
                        <!-- Security Badge -->
                        <div class="security-badge mt-4">
                            <i class="security-icon">🔐</i>
                            <span class="security-text">محمي بتشفير SSL 256-bit</span>
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
/* === MAIN CONTAINER === */
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0;
    display: flex;
    align-items: center;
    position: relative;
}

/* === AUTH CARD === */
.auth-card {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.1),
        0 5px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(230, 230, 230, 0.8);
    position: relative;
    overflow: hidden;
}

.auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    border-radius: 30px 30px 0 0;
}

/* === HEADER SECTION === */
.auth-logo {
    position: relative;
}

.logo-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    animation: logoFloat 3s ease-in-out infinite;
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.logo-icon {
    font-size: 2.5rem;
    color: white;
}

.auth-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 15px;
    letter-spacing: -0.5px;
}

.auth-subtitle {
    color: #6c757d;
    font-size: 1.2rem;
    font-weight: 400;
    line-height: 1.6;
    margin-bottom: 0;
}

/* === PROGRESS INDICATOR === */
.progress-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 0 0 auto;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: 3px solid #e9ecef;
}

.progress-step.active .step-circle {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: #667eea;
    transform: scale(1.1);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.step-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 8px;
    font-weight: 500;
    text-align: center;
}

.progress-step.active .step-label {
    color: #667eea;
    font-weight: 600;
}

.progress-line {
    width: 80px;
    height: 3px;
    background: #e9ecef;
    margin: 0 20px;
    border-radius: 2px;
    position: relative;
    overflow: hidden;
}

/* === FORM STEPS === */
.auth-form .form-step {
    display: none;
    animation: fadeInUp 0.5s ease;
    min-height: 400px; /* Debug: ensure content has space */
}

.auth-form .form-step.active {
    display: block !important;
}

/* Ensure first step is visible by default */
.auth-form .form-step[data-step="1"] {
    display: block !important;
}

/* Debug: Force visibility of step content */
.auth-form .form-step[data-step="1"] .user-type-selector {
    display: grid !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Force all form content to be visible */
.auth-form .form-step .step-header {
    display: block !important;
}

.auth-form .form-step .user-type-option {
    display: block !important;
}

.auth-form .form-step .user-type-card {
    display: flex !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-header {
    margin-bottom: 30px;
}

.step-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.step-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 0;
}

/* === USER TYPE CARDS === */
.user-type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 20px;
}

.user-type-option {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.user-type-card {
    padding: 35px 25px;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.user-type-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.user-type-option:hover .user-type-card {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.user-type-option:hover .user-type-card::before {
    opacity: 1;
}

.user-type-option input[type="radio"]:checked + .user-type-card {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
}

.business-card {
    border-color: #28a745 !important;
}

.user-type-option input[type="radio"]:checked + .business-card {
    border-color: #28a745;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(34, 139, 34, 0.05));
    box-shadow: 0 20px 40px rgba(40, 167, 69, 0.2);
}

.card-icon {
    margin-bottom: 20px;
}

.type-icon {
    font-size: 3.5rem;
    display: block;
    filter: grayscale(1);
    transition: filter 0.3s ease;
}

.user-type-option:hover .type-icon,
.user-type-option input[type="radio"]:checked + .user-type-card .type-icon {
    filter: grayscale(0);
}

.card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.card-description {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 20px;
    line-height: 1.5;
}

.card-features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    flex-grow: 1;
}

.card-features li {
    padding: 8px 0;
    color: #495057;
    font-size: 0.95rem;
    position: relative;
    padding-right: 20px;
}

.card-features li::before {
    content: '✓';
    position: absolute;
    right: 0;
    color: #28a745;
    font-weight: bold;
}

.card-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-badge.premium {
    background: linear-gradient(135deg, #28a745, #20c997);
}

/* === FLOATING LABELS === */
.form-floating {
    position: relative;
}

.form-floating > label {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    padding: 1rem 0.75rem;
    pointer-events: none;
    border: 1px solid transparent;
    transform-origin: 0 0;
    transition: opacity .1s ease-in-out,transform .1s ease-in-out;
    color: #6c757d;
    font-weight: 500;
}

.form-control:focus ~ label,
.form-control:not(:placeholder-shown) ~ label,
.form-select:focus ~ label,
.form-select:not([value=""]) ~ label {
    opacity: .65;
    transform: scale(.85) translateY(-0.5rem) translateX(.15rem);
    color: #667eea;
    font-weight: 600;
}

.form-control,
.form-select {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.2rem 0.75rem 0.6rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.95);
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    background: white;
}

/* === PASSWORD STRENGTH === */
.password-strength {
    margin-top: 10px;
}

.strength-meter {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-text {
    font-size: 0.9rem;
    color: #6c757d;
}

.strength-level {
    font-weight: 600;
}

/* Password strength levels */
.strength-weak { background: #dc3545; width: 25%; }
.strength-fair { background: #fd7e14; width: 50%; }
.strength-good { background: #ffc107; width: 75%; }
.strength-strong { background: #28a745; width: 100%; }

/* === PASSWORD MATCH === */
.password-match {
    font-size: 0.9rem;
}

.match-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.match-icon {
    font-size: 1rem;
}

.match-success { color: #28a745; }
.match-error { color: #dc3545; }

/* === PASSWORD REQUIREMENTS === */
.password-requirements {
    background: rgba(102, 126, 234, 0.05);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 15px;
    padding: 20px;
}

.requirements-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirement {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    font-size: 0.95rem;
    color: #6c757d;
    transition: color 0.3s ease;
}

.requirement.valid {
    color: #28a745;
}

.requirement-icon {
    font-size: 1rem;
    transition: all 0.3s ease;
}

.requirement.valid .requirement-icon {
    color: #28a745;
}

/* === TERMS SECTION === */
.terms-section {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 15px;
    padding: 20px;
}

.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.2em;
    border-radius: 6px;
    border: 2px solid #667eea;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-label {
    font-size: 1rem;
    color: #495057;
    line-height: 1.5;
}

/* === NAVIGATION BUTTONS === */
.form-navigation {
    padding-top: 30px;
    border-top: 1px solid #e9ecef;
}

.nav-btn {
    border-radius: 15px;
    padding: 15px 30px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    min-width: 140px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1aa085);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

/* === FOOTER === */
.auth-footer {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #e9ecef;
}

.divider-with-text {
    position: relative;
    text-align: center;
    margin: 30px 0;
}

.divider-with-text::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #e9ecef 20%, #e9ecef 80%, transparent);
}

.divider-with-text span {
    background: rgba(255, 255, 255, 0.95);
    padding: 0 20px;
    color: #6c757d;
    font-weight: 500;
    position: relative;
}

.auth-link {
    color: #667eea !important;
    font-weight: 600;
    transition: all 0.3s ease;
}

.auth-link:hover {
    color: #5a6fd8 !important;
    text-decoration: underline !important;
}

.security-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.security-icon {
    font-size: 1.2rem;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 992px) {
    .auth-card {
        padding: 40px 30px;
    }
    
    .user-type-card {
        padding: 25px 20px;
    }
    
    .type-icon {
        font-size: 3rem;
    }
}

@media (max-width: 768px) {
    .auth-container {
        padding: 20px 0;
    }
    
    .auth-card {
        padding: 30px 20px;
        margin: 0 15px;
        border-radius: 25px;
    }
    
    .user-type-selector {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .user-type-card {
        padding: 25px 20px;
    }
    
    .auth-title {
        font-size: 2rem;
    }
    
    .auth-subtitle {
        font-size: 1.1rem;
    }
    
    .progress-indicator {
        flex-direction: column;
        gap: 15px;
    }
    
    .progress-line {
        width: 3px;
        height: 30px;
        margin: 0;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .form-navigation .d-flex {
        flex-direction: column;
        gap: 15px;
    }
    
    .nav-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .auth-card {
        padding: 25px 15px;
    }
    
    .logo-circle {
        width: 60px;
        height: 60px;
    }
    
    .logo-icon {
        font-size: 2rem;
    }
    
    .user-type-card {
        padding: 20px 15px;
    }
    
    .type-icon {
        font-size: 2.5rem;
    }
    
    .card-features {
        font-size: 0.9rem;
    }
    
    .form-control,
    .form-select {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* === LOADING ANIMATION === */
.btn-loading {
    pointer-events: none;
    opacity: 0.6;
}

.btn-loading::after {
    content: '';
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === ACCESSIBILITY IMPROVEMENTS === */
.form-control:focus,
.form-select:focus,
.form-check-input:focus,
.nav-btn:focus {
    outline: 3px solid rgba(102, 126, 234, 0.3);
    outline-offset: 2px;
}

/* === PRINT STYLES === */
@media print {
    .auth-container {
        background: white;
    }
    
    .auth-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Registration form script loaded'); // Debug log
    
    let currentStep = 1;
    const totalSteps = 3;
    
    // Elements
    const form = document.getElementById('registerForm');
    const steps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.progress-step');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Check if essential elements exist
    if (!form || steps.length === 0) {
        console.error('Essential form elements not found');
        return;
    }
    
    // Password elements
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const strengthMeter = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-level');
    const passwordMatch = document.querySelector('.password-match');
    const requirements = document.querySelectorAll('.requirement');
    
    // Initialize form
    showStep(currentStep);
    
    // Next button click
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }
    
    // Previous button click
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });
    }
    
    // Show specific step
    function showStep(step) {
        // Hide all steps
        steps.forEach(s => s.classList.remove('active'));
        progressSteps.forEach(s => s.classList.remove('active'));
        
        // Show current step
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        const currentProgressStep = document.querySelector(`.progress-step[data-step="${step}"]`);
        
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }
        if (currentProgressStep) {
            currentProgressStep.classList.add('active');
        }
        
        // Update navigation buttons
        if (prevBtn) {
            prevBtn.style.display = step === 1 ? 'none' : 'block';
        }
        if (nextBtn) {
            nextBtn.style.display = step === totalSteps ? 'none' : 'block';
        }
        if (submitBtn) {
            submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        }
        
        // Animate progress line
        updateProgressLine();
        
        // Focus first input in step
        setTimeout(() => {
            const firstInput = document.querySelector(`[data-step="${step}"] input, [data-step="${step}"] select`);
            if (firstInput) firstInput.focus();
        }, 300);
    }
    
    // Update progress line animation
    function updateProgressLine() {
        const progressLines = document.querySelectorAll('.progress-line');
        progressLines.forEach((line, index) => {
            if (index < currentStep - 1) {
                line.style.background = 'linear-gradient(90deg, #667eea, #764ba2)';
            } else {
                line.style.background = '#e9ecef';
            }
        });
    }
    
    // Validate current step
    function validateStep(step) {
        const currentStepElement = document.querySelector(`[data-step="${step}"]`);
        const requiredInputs = currentStepElement.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                showFieldError(input, 'هذا الحقل مطلوب');
                isValid = false;
            } else {
                clearFieldError(input);
            }
        });
        
        // Additional validation for specific steps
        if (step === 1) {
            const userType = document.querySelector('input[name="user_type"]:checked');
            if (!userType) {
                showStepError('يرجى اختيار نوع الحساب');
                isValid = false;
            }
        }
        
        if (step === 2) {
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            
            if (email.value && !isValidEmail(email.value)) {
                showFieldError(email, 'يرجى إدخال بريد إلكتروني صحيح');
                isValid = false;
            }
            
            if (phone.value && !isValidPhone(phone.value)) {
                showFieldError(phone, 'يرجى إدخال رقم هاتف صحيح');
                isValid = false;
            }
        }
        
        if (step === 3) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const terms = document.getElementById('terms');
            
            if (password.value.length < 8) {
                showFieldError(password, 'كلمة المرور يجب أن تكون 8 أحرف على الأقل');
                isValid = false;
            }
            
            if (password.value !== confirmPassword.value) {
                showFieldError(confirmPassword, 'كلمة المرور غير متطابقة');
                isValid = false;
            }
            
            if (!terms.checked) {
                showFieldError(terms, 'يجب الموافقة على الشروط والأحكام');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Show field error
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        clearFieldError(field);
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        field.parentNode.appendChild(feedback);
    }
    
    // Clear field error
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) feedback.remove();
    }
    
    // Show step error
    function showStepError(message) {
        // Remove existing error
        const existingError = document.querySelector('.step-error');
        if (existingError) existingError.remove();
        
        const error = document.createElement('div');
        error.className = 'alert alert-danger step-error mt-3';
        error.textContent = message;
        
        const currentStepElement = document.querySelector(`[data-step="${currentStep}"]`);
        currentStepElement.appendChild(error);
        
        setTimeout(() => error.remove(), 5000);
    }
    
    // Email validation
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Phone validation
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }
    
    // Enhanced user type selection
    const userTypeOptions = document.querySelectorAll('input[name="user_type"]');
    userTypeOptions.forEach(option => {
        option.addEventListener('change', function() {
            userTypeOptions.forEach(opt => {
                opt.closest('.user-type-option').classList.remove('selected');
            });
            
            if (this.checked) {
                this.closest('.user-type-option').classList.add('selected');
                
                // Add selection animation
                const card = this.nextElementSibling;
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = '';
                }, 150);
            }
        });
    });
    
    // Initialize selected state
    const selectedOption = document.querySelector('input[name="user_type"]:checked');
    if (selectedOption) {
        selectedOption.closest('.user-type-option').classList.add('selected');
    }
    
    // Password strength checker
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updatePasswordStrength(strength);
            checkPasswordRequirements(password);
        });
    }
    
    // Password confirmation checker
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }
    
    // Calculate password strength
    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score += 25;
        if (password.length >= 12) score += 25;
        if (/[a-z]/.test(password)) score += 12.5;
        if (/[A-Z]/.test(password)) score += 12.5;
        if (/[0-9]/.test(password)) score += 12.5;
        if (/[^A-Za-z0-9]/.test(password)) score += 12.5;
        
        return Math.min(score, 100);
    }
    
    // Update password strength display
    function updatePasswordStrength(strength) {
        if (!strengthMeter || !strengthText) return;
        
        strengthMeter.style.width = strength + '%';
        
        if (strength < 25) {
            strengthMeter.className = 'strength-bar strength-weak';
            strengthText.textContent = 'ضعيف';
            strengthText.style.color = '#dc3545';
        } else if (strength < 50) {
            strengthMeter.className = 'strength-bar strength-fair';
            strengthText.textContent = 'مقبول';
            strengthText.style.color = '#fd7e14';
        } else if (strength < 75) {
            strengthMeter.className = 'strength-bar strength-good';
            strengthText.textContent = 'جيد';
            strengthText.style.color = '#ffc107';
        } else {
            strengthMeter.className = 'strength-bar strength-strong';
            strengthText.textContent = 'قوي';
            strengthText.style.color = '#28a745';
        }
    }
    
    // Check password requirements
    function checkPasswordRequirements(password) {
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };
        
        requirements.forEach(req => {
            const requirement = req.dataset.requirement;
            const icon = req.querySelector('.requirement-icon');
            
            if (checks[requirement]) {
                req.classList.add('valid');
                icon.textContent = '✅';
            } else {
                req.classList.remove('valid');
                icon.textContent = '❌';
            }
        });
    }
    
    // Check password match
    function checkPasswordMatch() {
        if (!passwordMatch) return;
        
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!confirmPassword) {
            passwordMatch.style.display = 'none';
            return;
        }
        
        passwordMatch.style.display = 'block';
        const status = passwordMatch.querySelector('.match-status');
        const icon = passwordMatch.querySelector('.match-icon');
        const text = passwordMatch.querySelector('.match-text');
        
        if (password === confirmPassword) {
            status.className = 'match-status match-success';
            icon.textContent = '✅';
            text.textContent = 'كلمة المرور متطابقة';
        } else {
            status.className = 'match-status match-error';
            icon.textContent = '❌';
            text.textContent = 'كلمة المرور غير متطابقة';
        }
    }
    
    // Form submission with loading state
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateStep(3)) {
                e.preventDefault();
                return;
            }
            
            // Add loading state
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري إنشاء الحساب...';
                submitBtn.disabled = true;
            }
            
            // Form will submit normally
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            if (currentStep < totalSteps && nextBtn) {
                nextBtn.click();
            } else if (submitBtn) {
                submitBtn.click();
            }
        }
    });
    
    // Auto-advance on user type selection
    userTypeOptions.forEach(option => {
        option.addEventListener('change', function() {
            setTimeout(() => {
                if (currentStep === 1 && nextBtn) {
                    nextBtn.click();
                }
            }, 500);
        });
    });
    
    // Floating label enhancements
    const floatingInputs = document.querySelectorAll('.form-floating input, .form-floating select');
    floatingInputs.forEach(input => {
        // Check if field has value on load
        if (input.value) {
            input.classList.add('has-value');
        }
        
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });
        
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });
    
    // Add smooth scrolling to top when step changes
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    // Scroll to top on step change
    nextBtn.addEventListener('click', () => setTimeout(scrollToTop, 100));
    prevBtn.addEventListener('click', () => setTimeout(scrollToTop, 100));
});
</script>
@endpush
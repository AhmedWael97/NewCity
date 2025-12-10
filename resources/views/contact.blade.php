@extends('layouts.app')

@section('title', 'اتصل بنا - الدعم الفني')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 mb-3" style="color: #2d3748; font-weight: bold;">
                    <i class="fas fa-headset me-2" style="color: #3182ce;"></i>
                    كيف يمكننا مساعدتك؟
                </h1>
                <p class="lead text-muted">نحن هنا للإجابة على أسئلتك وحل مشاكلك</p>
            </div>

            <div class="row g-4 mb-5">
                <!-- Contact Options -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fab fa-whatsapp fa-4x mb-3" style="color: #25D366;"></i>
                            <h4 class="mb-3">تواصل عبر واتساب</h4>
                            <p class="text-muted mb-3">للرد السريع والدعم الفوري</p>
                            <a href="https://wa.me/201060863230?text=مرحبا اريد مساعدة" 
                               class="btn btn-success btn-lg" target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>
                                +201060863230
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-ticket-alt fa-4x mb-3" style="color: #3182ce;"></i>
                            <h4 class="mb-3">إنشاء تذكرة دعم</h4>
                            <p class="text-muted mb-3">لطلبات الدعم المفصلة والمتابعة</p>
                            <button class="btn btn-primary btn-lg" onclick="document.getElementById('ticketFormCard').scrollIntoView({behavior: 'smooth'})">
                                <i class="fas fa-plus me-2"></i>
                                إنشاء تذكرة جديدة
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Ticket Form -->
            <div class="card shadow-sm" id="ticketFormCard">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        إنشاء تذكرة دعم
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>يرجى تصحيح الأخطاء التالية:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div id="successMessage" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="successText"></span>
                        <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
                    </div>

                    <div id="errorMessage" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul id="errorList" class="mb-0 mt-2"></ul>
                        <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
                    </div>

                    <form id="ticketForm" action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            @guest
                            <!-- Guest Information -->
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    يرجى ملء بياناتك الشخصية للتواصل معك
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="guest_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    الاسم الكامل <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="guest_name" name="guest_name" 
                                       value="{{ old('guest_name') }}" required maxlength="255"
                                       placeholder="أدخل اسمك الكامل">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="guest_phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>
                                    رقم الهاتف <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="guest_phone" name="guest_phone" 
                                       value="{{ old('guest_phone') }}" required maxlength="20"
                                       placeholder="01xxxxxxxxx">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="guest_email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    البريد الإلكتروني (اختياري)
                                </label>
                                <input type="email" class="form-control" id="guest_email" name="guest_email" 
                                       value="{{ old('guest_email') }}" maxlength="255"
                                       placeholder="example@email.com">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="guest_address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    العنوان (اختياري)
                                </label>
                                <input type="text" class="form-control" id="guest_address" name="guest_address" 
                                       value="{{ old('guest_address') }}" maxlength="500"
                                       placeholder="المدينة، المنطقة">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12"><hr></div>
                            @endguest

                            <!-- Subject -->
                            <div class="col-md-12">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-heading me-1"></i>
                                    عنوان المشكلة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="subject" name="subject" 
                                       value="{{ old('subject') }}" required maxlength="255"
                                       placeholder="مثال: مشكلة في تسجيل الدخول">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tag me-1"></i>
                                    نوع المشكلة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="category" name="category" required>
                                    <option value="">اختر نوع المشكلة</option>
                                    <option value="technical_issue" {{ old('category') == 'technical_issue' ? 'selected' : '' }}>
                                        مشكلة تقنية
                                    </option>
                                    <option value="shop_complaint" {{ old('category') == 'shop_complaint' ? 'selected' : '' }}>
                                        شكوى متجر
                                    </option>
                                    <option value="payment_issue" {{ old('category') == 'payment_issue' ? 'selected' : '' }}>
                                        مشكلة دفع
                                    </option>
                                    <option value="account_problem" {{ old('category') == 'account_problem' ? 'selected' : '' }}>
                                        مشكلة حساب
                                    </option>
                                    <option value="feature_request" {{ old('category') == 'feature_request' ? 'selected' : '' }}>
                                        طلب ميزة جديدة
                                    </option>
                                    <option value="bug_report" {{ old('category') == 'bug_report' ? 'selected' : '' }}>
                                        بلاغ خطأ برمجي
                                    </option>
                                    <option value="content_issue" {{ old('category') == 'content_issue' ? 'selected' : '' }}>
                                        مشكلة محتوى
                                    </option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>
                                        أخرى
                                    </option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label for="priority" class="form-label">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    الأولوية <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="priority" name="priority" required>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                        متوسطة
                                    </option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        منخفضة
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        عالية
                                    </option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                        عاجلة
                                    </option>
                                </select>
                            </div>

                            <!-- City (Optional) -->
                            <div class="col-md-6">
                                <label for="city_id" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    المدينة (اختياري)
                                </label>
                                <select class="form-select" id="city_id" name="city_id">
                                    <option value="">اختر المدينة</option>
                                    @foreach(\App\Models\City::where('is_active', true)->orderBy('name')->get() as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Shop (Optional) -->
                            <div class="col-md-6">
                                <label for="shop_id" class="form-label">
                                    <i class="fas fa-store me-1"></i>
                                    المتجر المتعلق (اختياري)
                                </label>
                                <select class="form-select" id="shop_id" name="shop_id">
                                    <option value="">اختر المتجر</option>
                                    @foreach(\App\Models\Shop::where('is_active', true)->where('is_verified', true)->orderBy('name')->get() as $shop)
                                        <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }} - {{ $shop->city->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">إذا كانت المشكلة متعلقة بمتجر معين</small>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    وصف المشكلة بالتفصيل <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="6" 
                                          required placeholder="اشرح المشكلة بالتفصيل..." minlength="10">{{ old('description') }}</textarea>
                                <small class="text-muted">كلما كان الوصف أكثر تفصيلاً، كلما كان بإمكاننا مساعدتك بشكل أفضل</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Attachments -->
                            <div class="col-md-12">
                                <label for="attachments" class="form-label">
                                    <i class="fas fa-paperclip me-1"></i>
                                    إرفاق ملفات (اختياري)
                                </label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" 
                                       multiple accept="image/*,.pdf,.doc,.docx">
                                <small class="text-muted">يمكنك إرفاق صور أو ملفات توضح المشكلة (حد أقصى 5 ملفات، 10MB لكل ملف)</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <span id="btnText">إرسال التذكرة</span>
                                    <span id="btnLoader" class="spinner-border spinner-border-sm me-2" style="display: none;"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card shadow-sm mt-5">
                <div class="card-header bg-light py-3">
                    <h3 class="mb-0">
                        <i class="fas fa-question-circle me-2" style="color: #3182ce;"></i>
                        الأسئلة الشائعة
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    كيف يمكنني إنشاء حساب جديد؟
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك إنشاء حساب جديد من خلال الضغط على زر "تسجيل" في أعلى الصفحة، ثم ملء النموذج بمعلوماتك الأساسية.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    كيف يمكنني إضافة متجري إلى المنصة؟
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك اقتراح متجر من خلال نموذج "اقترح متجر" في الصفحة الرئيسية، وسيتم مراجعته من قبل فريقنا.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    كيف يمكنني نشر خدماتي؟
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    بعد تسجيل الدخول، اذهب إلى لوحة التحكم واختر "خدماتي" ثم "إضافة خدمة جديدة".
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    هل استخدام المنصة مجاني؟
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نعم، جميع الميزات الأساسية مجانية تماماً للمستخدمين وأصحاب المحلات.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3182ce;
        box-shadow: 0 0 0 0.2rem rgba(49, 130, 206, 0.25);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: #e6f2ff;
        color: #3182ce;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: #3182ce;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ticketForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const successText = document.getElementById('successText');
    const errorList = document.getElementById('errorList');

    // Client-side validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';

        // Basic validation
        let isValid = true;
        const formData = new FormData(form);

        // Validate required fields
        @guest
        if (!formData.get('guest_name')?.trim()) {
            showFieldError('guest_name', 'الاسم مطلوب');
            isValid = false;
        }
        if (!formData.get('guest_phone')?.trim()) {
            showFieldError('guest_phone', 'رقم الهاتف مطلوب');
            isValid = false;
        } else if (!/^([0-9\s\-\+\(\)]*)$/.test(formData.get('guest_phone'))) {
            showFieldError('guest_phone', 'رقم الهاتف غير صحيح');
            isValid = false;
        }
        if (formData.get('guest_email')?.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.get('guest_email'))) {
            showFieldError('guest_email', 'البريد الإلكتروني غير صحيح');
            isValid = false;
        }
        @endguest

        if (!formData.get('subject')?.trim()) {
            showFieldError('subject', 'عنوان المشكلة مطلوب');
            isValid = false;
        }
        if (!formData.get('category')) {
            showFieldError('category', 'نوع المشكلة مطلوب');
            isValid = false;
        }
        if (!formData.get('description')?.trim()) {
            showFieldError('description', 'وصف المشكلة مطلوب');
            isValid = false;
        } else if (formData.get('description').trim().length < 10) {
            showFieldError('description', 'وصف المشكلة يجب أن يكون 10 أحرف على الأقل');
            isValid = false;
        }

        if (!isValid) {
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';

        // Submit via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';

            if (data.success) {
                // Show success message
                successText.innerHTML = `${data.message}<br><strong>رقم التذكرة: ${data.ticket_number}</strong>`;
                successMessage.style.display = 'block';
                successMessage.classList.add('show');
                
                // Reset form
                form.reset();
                
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Hide message after 10 seconds
                setTimeout(() => {
                    successMessage.classList.remove('show');
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 150);
                }, 10000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Reset button state
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';

            if (error.response) {
                error.response.json().then(data => {
                    if (data.errors) {
                        // Show validation errors
                        errorList.innerHTML = '';
                        Object.keys(data.errors).forEach(field => {
                            const errors = data.errors[field];
                            errors.forEach(errorMsg => {
                                const li = document.createElement('li');
                                li.textContent = errorMsg;
                                errorList.appendChild(li);
                                
                                // Highlight field
                                showFieldError(field, errorMsg);
                            });
                        });
                        errorMessage.style.display = 'block';
                        errorMessage.classList.add('show');
                        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            } else {
                // Show generic error
                errorList.innerHTML = '<li>حدث خطأ أثناء إرسال التذكرة. يرجى المحاولة مرة أخرى.</li>';
                errorMessage.style.display = 'block';
                errorMessage.classList.add('show');
                errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    function showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            const feedback = field.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
        }
    }
});
</script>
@endsection

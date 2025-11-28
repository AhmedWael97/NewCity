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
                            <button class="btn btn-primary btn-lg" onclick="document.getElementById('ticketForm').scrollIntoView({behavior: 'smooth'})">
                                <i class="fas fa-plus me-2"></i>
                                إنشاء تذكرة جديدة
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Ticket Form -->
            <div class="card shadow-sm" id="ticketForm">
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

                    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Subject -->
                            <div class="col-md-12">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-heading me-1"></i>
                                    عنوان المشكلة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="subject" name="subject" 
                                       value="{{ old('subject') }}" required maxlength="255"
                                       placeholder="مثال: مشكلة في تسجيل الدخول">
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
                                <input type="number" class="form-control" id="shop_id" name="shop_id" 
                                       value="{{ old('shop_id') }}" placeholder="رقم المتجر">
                                <small class="text-muted">إذا كانت المشكلة متعلقة بمتجر معين</small>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    وصف المشكلة بالتفصيل <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="6" 
                                          required placeholder="اشرح المشكلة بالتفصيل...">{{ old('description') }}</textarea>
                                <small class="text-muted">كلما كان الوصف أكثر تفصيلاً، كلما كان بإمكاننا مساعدتك بشكل أفضل</small>
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
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    إرسال التذكرة
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
@endsection

@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة')

@push('styles')
<style>
    .form-step {
        display: none;
    }
    
    .form-step.active {
        display: block;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }
    
    .step-indicator .step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 10px;
        font-weight: bold;
        position: relative;
    }
    
    .step-indicator .step.active {
        background: #007bff;
        color: white;
    }
    
    .step-indicator .step.completed {
        background: #28a745;
        color: white;
    }
    
    .step-indicator .step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 2px;
        background: #e9ecef;
        margin-left: 10px;
    }
    
    .step-indicator .step.completed:not(:last-child)::after {
        background: #28a745;
    }
    
    .image-preview {
        position: relative;
        display: inline-block;
        margin: 5px;
    }
    
    .image-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
    }
    
    .image-preview .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 12px;
        cursor: pointer;
    }
    
    .pricing-option {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .pricing-option:hover,
    .pricing-option.selected {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .availability-day {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .availability-day.active {
        background: #e3f2fd;
        border-color: #2196f3;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <h1 class="h3 mb-2">إضافة خدمة جديدة</h1>
                <p class="text-muted">أضف خدمتك ووصل إلى آلاف العملاء في مدينتك</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" data-step="1">1</div>
                <div class="step" data-step="2">2</div>
                <div class="step" data-step="3">3</div>
                <div class="step" data-step="4">4</div>
            </div>

            <!-- Form -->
            <form id="serviceForm" action="{{ route('user.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Step 1: Basic Information -->
                <div class="form-step active" data-step="1">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                المعلومات الأساسية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="service_category_id" class="form-label">فئة الخدمة <span class="text-danger">*</span></label>
                                    <select class="form-select @error('service_category_id') is-invalid @enderror" 
                                            id="service_category_id" name="service_category_id" required>
                                        <option value="">اختر فئة الخدمة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city_id" class="form-label">المدينة <span class="text-danger">*</span></label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                            id="city_id" name="city_id" required>
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="title" class="form-label">عنوان الخدمة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="مثال: خدمة توصيل سريع في جميع أنحاء المدينة" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">وصف الخدمة <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="اكتب وصفاً مفصلاً عن خدمتك..." required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="experience_years" class="form-label">سنوات الخبرة</label>
                                    <select class="form-select" id="experience_years" name="experience_years">
                                        <option value="">اختر سنوات الخبرة</option>
                                        <option value="0" {{ old('experience_years') == '0' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="1" {{ old('experience_years') == '1' ? 'selected' : '' }}>سنة واحدة</option>
                                        @for($i = 2; $i <= 20; $i++)
                                            <option value="{{ $i }}" {{ old('experience_years') == $i ? 'selected' : '' }}>
                                                {{ $i }} سنوات
                                            </option>
                                        @endfor
                                        <option value="20" {{ old('experience_years') == '20' ? 'selected' : '' }}>أكثر من 20 سنة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Pricing -->
                <div class="form-step" data-step="2">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-dollar-sign me-2"></i>
                                التسعير
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label">نوع التسعير <span class="text-danger">*</span></label>
                                
                                <div class="pricing-option" data-type="fixed">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="pricing_type" value="fixed" id="pricing_fixed" class="form-check-input me-3">
                                        <div>
                                            <h6 class="mb-1">سعر ثابت</h6>
                                            <small class="text-muted">سعر واحد لجميع الخدمات</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-option" data-type="hourly">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="pricing_type" value="hourly" id="pricing_hourly" class="form-check-input me-3">
                                        <div>
                                            <h6 class="mb-1">تسعير بالساعة</h6>
                                            <small class="text-muted">سعر محدد لكل ساعة عمل</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-option" data-type="distance">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="pricing_type" value="distance" id="pricing_distance" class="form-check-input me-3">
                                        <div>
                                            <h6 class="mb-1">تسعير بالمسافة</h6>
                                            <small class="text-muted">سعر محدد لكل كيلومتر</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="pricing-option" data-type="negotiable">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="pricing_type" value="negotiable" id="pricing_negotiable" class="form-check-input me-3">
                                        <div>
                                            <h6 class="mb-1">قابل للتفاوض</h6>
                                            <small class="text-muted">يتم تحديد السعر حسب طبيعة الخدمة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Fields -->
                            <div class="row">
                                <div class="col-md-6 mb-3" id="base_price_field" style="display: none;">
                                    <label for="base_price" class="form-label">السعر الأساسي (جنيه)</label>
                                    <input type="number" class="form-control" id="base_price" name="base_price" 
                                           value="{{ old('base_price') }}" min="0" step="0.01">
                                </div>

                                <div class="col-md-6 mb-3" id="hourly_rate_field" style="display: none;">
                                    <label for="hourly_rate" class="form-label">السعر بالساعة (جنيه)</label>
                                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                           value="{{ old('hourly_rate') }}" min="0" step="0.01">
                                </div>

                                <div class="col-md-6 mb-3" id="distance_rate_field" style="display: none;">
                                    <label for="distance_rate" class="form-label">السعر بالكيلومتر (جنيه)</label>
                                    <input type="number" class="form-control" id="distance_rate" name="distance_rate" 
                                           value="{{ old('distance_rate') }}" min="0" step="0.01">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="minimum_charge" class="form-label">الحد الأدنى للتحصيل (جنيه)</label>
                                    <input type="number" class="form-control" id="minimum_charge" name="minimum_charge" 
                                           value="{{ old('minimum_charge') }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Contact & Images -->
                <div class="form-step" data-step="3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-images me-2"></i>
                                التواصل والصور
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror" 
                                           id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" 
                                           placeholder="01xxxxxxxxx" required>
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_whatsapp" class="form-label">واتساب</label>
                                    <input type="tel" class="form-control" id="contact_whatsapp" name="contact_whatsapp" 
                                           value="{{ old('contact_whatsapp') }}" placeholder="01xxxxxxxxx">
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="images" class="form-label">صور الخدمة</label>
                                <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                       id="images" name="images[]" multiple accept="image/*">
                                <div class="form-text">يمكنك رفع حتى 5 صور. الحد الأقصى لحجم كل صورة 2 ميجابايت.</div>
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Image Preview Container -->
                                <div id="imagePreview" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Subscription & Submit -->
                <div class="form-step" data-step="4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-crown me-2"></i>
                                خطة الاشتراك
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label">اختر خطة الاشتراك <span class="text-danger">*</span></label>
                                
                                <div class="row">
                                    @foreach($subscriptionPlans as $plan)
                                        <div class="col-md-4 mb-3">
                                            <div class="card subscription-plan h-100" data-plan="{{ $plan->id }}">
                                                <div class="card-body text-center">
                                                    <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}" 
                                                           id="plan_{{ $plan->id }}" class="form-check-input mb-3" required>
                                                    
                                                    <h5 class="card-title">{{ $plan->name }}</h5>
                                                    <div class="h3 text-primary mb-2">{{ number_format($plan->price) }} جنيه</div>
                                                    <p class="text-muted">{{ $plan->duration_value }} {{ $plan->duration_type == 'monthly' ? 'شهر' : 'سنة' }}</p>
                                                    
                                                    <hr>
                                                    
                                                    <ul class="list-unstyled text-start">
                                                        @if($plan->features)
                                                            @foreach(json_decode($plan->features, true) as $feature)
                                                                <li class="mb-1">
                                                                    <i class="fas fa-check text-success me-2"></i>
                                                                    {{ $feature }}
                                                                </li>
                                                            @endforeach
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    أوافق على <a href="#" class="text-primary">الشروط والأحكام</a> و <a href="#" class="text-primary">سياسة الخصوصية</a>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    إرسال الخدمة للمراجعة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="prevBtn" class="btn btn-outline-secondary" style="display: none;">
                        <i class="fas fa-arrow-right me-2"></i>السابق
                    </button>
                    <button type="button" id="nextBtn" class="btn btn-primary">
                        التالي<i class="fas fa-arrow-left ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;

    // Step navigation
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(stepDiv => {
            stepDiv.classList.remove('active');
        });

        // Show current step
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

        // Update step indicators
        document.querySelectorAll('.step-indicator .step').forEach((stepIndicator, index) => {
            stepIndicator.classList.remove('active', 'completed');
            if (index + 1 < step) {
                stepIndicator.classList.add('completed');
            } else if (index + 1 === step) {
                stepIndicator.classList.add('active');
            }
        });

        // Update navigation buttons
        prevBtn.style.display = step > 1 ? 'block' : 'none';
        nextBtn.textContent = step === totalSteps ? 'إرسال' : 'التالي';
        
        if (step === totalSteps) {
            nextBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>إرسال الخدمة للمراجعة';
            nextBtn.onclick = function() {
                document.getElementById('serviceForm').submit();
            };
        } else {
            nextBtn.innerHTML = 'التالي<i class="fas fa-arrow-left ms-2"></i>';
        }
    }

    function validateStep(step) {
        const currentStepDiv = document.querySelector(`.form-step[data-step="${step}"]`);
        const requiredFields = currentStepDiv.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // Pricing type selection
    document.querySelectorAll('.pricing-option').forEach(option => {
        option.addEventListener('click', function() {
            const type = this.dataset.type;
            const radio = this.querySelector('input[type="radio"]');
            
            // Remove selected class from all options
            document.querySelectorAll('.pricing-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
            radio.checked = true;
            
            // Show/hide pricing fields
            document.querySelectorAll('[id$="_field"]').forEach(field => {
                field.style.display = 'none';
            });
            
            if (type === 'fixed') {
                document.getElementById('base_price_field').style.display = 'block';
            } else if (type === 'hourly') {
                document.getElementById('hourly_rate_field').style.display = 'block';
            } else if (type === 'distance') {
                document.getElementById('distance_rate_field').style.display = 'block';
            }
        });
    });

    // Image preview
    document.getElementById('images').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Remove image
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            e.target.closest('.image-preview').remove();
        }
    });

    // Subscription plan selection
    document.querySelectorAll('.subscription-plan').forEach(plan => {
        plan.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            
            // Remove selected class from all plans
            document.querySelectorAll('.subscription-plan').forEach(p => {
                p.classList.remove('border-primary');
            });
            
            // Add selected class to clicked plan
            this.classList.add('border-primary');
            radio.checked = true;
        });
    });
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة')

@section('content')
<div class="container py-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="fw-bold">إضافة خدمة جديدة</h2>
                <p class="text-muted">قم بإضافة خدمتك وابدأ في الوصول للعملاء</p>
            </div>

            <!-- Progress Steps -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="step-indicator d-flex align-items-center justify-content-between mb-0">
                        <div class="step-item active" data-step="1">
                            <div class="step-circle">1</div>
                            <div class="step-label">معلومات أساسية</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item" data-step="2">
                            <div class="step-circle">2</div>
                            <div class="step-label">التسعير والتواصل</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item" data-step="3">
                            <div class="step-circle">3</div>
                            <div class="step-label">الصور والتفاصيل</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('user.services.store') }}" method="POST" enctype="multipart/form-data" id="serviceForm">
                @csrf

                <!-- Step 1: Basic Information -->
                <div class="form-step active" data-step="1">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">المعلومات الأساسية</h4>

                            <div class="row g-3">
                                <!-- Service Category -->
                                <div class="col-md-6">
                                    <label for="service_category_id" class="form-label">
                                        نوع الخدمة <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('service_category_id') is-invalid @enderror" 
                                            id="service_category_id" 
                                            name="service_category_id" 
                                            required>
                                        <option value="">اختر نوع الخدمة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- City -->
                                <div class="col-md-6">
                                    <label for="city_id" class="form-label">
                                        المدينة <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ Auth::user()->city->name ?? 'غير محدد' }}" 
                                           readonly 
                                           style="background-color: #e9ecef;">
                                    <input type="hidden" 
                                           name="city_id" 
                                           value="{{ Auth::user()->city_id }}" 
                                           required>
                                    <small class="text-muted">يمكنك إضافة الخدمة في مدينتك فقط</small>
                                    @error('city_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Title -->
                                <div class="col-12">
                                    <label for="title" class="form-label">
                                        عنوان الخدمة <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="مثال: صيانة مكيفات سبليت - خدمة سريعة"
                                           maxlength="255"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label">
                                        وصف الخدمة <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              maxlength="1000"
                                              placeholder="اكتب وصفاً تفصيلياً للخدمة التي تقدمها..."
                                              required>{{ old('description') }}</textarea>
                                    <div class="form-text">الحد الأقصى 1000 حرف</div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">
                                    التالي <i class="bi bi-arrow-left me-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Pricing & Contact -->
                <div class="form-step" data-step="2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">التسعير ومعلومات التواصل</h4>

                            <div class="row g-3">
                                <!-- Pricing Type -->
                                <div class="col-12">
                                    <label class="form-label">
                                        نوع التسعير <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="pricing-option">
                                                <input type="radio" class="btn-check" name="pricing_type" id="pricing_fixed" value="fixed" {{ old('pricing_type') == 'fixed' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100" for="pricing_fixed">
                                                    <i class="bi bi-tag-fill d-block fs-4 mb-2"></i>
                                                    سعر ثابت
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="pricing-option">
                                                <input type="radio" class="btn-check" name="pricing_type" id="pricing_hourly" value="hourly" {{ old('pricing_type') == 'hourly' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100" for="pricing_hourly">
                                                    <i class="bi bi-clock-fill d-block fs-4 mb-2"></i>
                                                    بالساعة
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="pricing-option">
                                                <input type="radio" class="btn-check" name="pricing_type" id="pricing_per_km" value="per_km" {{ old('pricing_type') == 'per_km' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100" for="pricing_per_km">
                                                    <i class="bi bi-speedometer2 d-block fs-4 mb-2"></i>
                                                    بالكيلومتر
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="pricing-option">
                                                <input type="radio" class="btn-check" name="pricing_type" id="pricing_negotiable" value="negotiable" {{ old('pricing_type', 'negotiable') == 'negotiable' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100" for="pricing_negotiable">
                                                    <i class="bi bi-chat-dots-fill d-block fs-4 mb-2"></i>
                                                    تفاوض
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('pricing_type')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Price Range -->
                                <div class="col-md-6">
                                    <label for="price_from" class="form-label">السعر من (جنيه مصري)</label>
                                    <input type="number" 
                                           class="form-control @error('price_from') is-invalid @enderror" 
                                           id="price_from" 
                                           name="price_from" 
                                           value="{{ old('price_from') }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="100">
                                    @error('price_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="price_to" class="form-label">السعر إلى (جنيه مصري)</label>
                                    <input type="number" 
                                           class="form-control @error('price_to') is-invalid @enderror" 
                                           id="price_to" 
                                           name="price_to" 
                                           value="{{ old('price_to') }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="500">
                                    @error('price_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">
                                        رقم الهاتف <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', Auth::user()->phone) }}" 
                                           placeholder="01XXXXXXXXX"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="whatsapp" class="form-label">رقم الواتساب</label>
                                    <input type="tel" 
                                           class="form-control @error('whatsapp') is-invalid @enderror" 
                                           id="whatsapp" 
                                           name="whatsapp" 
                                           value="{{ old('whatsapp') }}" 
                                           placeholder="01XXXXXXXXX">
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label for="address" class="form-label">العنوان</label>
                                    <input type="text" 
                                           class="form-control @error('address') is-invalid @enderror" 
                                           id="address" 
                                           name="address" 
                                           value="{{ old('address') }}" 
                                           placeholder="الحي - الشارع"
                                           maxlength="500">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(1)">
                                    <i class="bi bi-arrow-right me-2"></i> السابق
                                </button>
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(3)">
                                    التالي <i class="bi bi-arrow-left me-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Images & Details -->
                <div class="form-step" data-step="3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">الصور والتفاصيل الإضافية</h4>

                            <div class="row g-3">
                                <!-- Images -->
                                <div class="col-12">
                                    <label class="form-label">صور الخدمة</label>
                                    <div class="border-2 border-dashed rounded p-4 text-center" style="border-style: dashed !important;">
                                        <input type="file" 
                                               class="form-control d-none" 
                                               id="images" 
                                               name="images[]" 
                                               accept="image/jpeg,image/png,image/jpg"
                                               multiple
                                               onchange="previewImages(this)">
                                        <label for="images" class="d-block cursor-pointer" style="cursor: pointer;">
                                            <i class="bi bi-cloud-upload fs-1 text-primary d-block mb-3"></i>
                                            <p class="mb-2">اضغط لاختيار الصور</p>
                                            <p class="text-muted small">يمكنك رفع عدة صور (JPEG, PNG, JPG - حد أقصى 2MB لكل صورة)</p>
                                        </label>
                                    </div>
                                    @error('images.*')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="row g-2 mt-3"></div>
                                </div>

                                <!-- Availability -->
                                <div class="col-12">
                                    <label class="form-label">أوقات العمل</label>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="sunday" id="day_sunday" {{ in_array('sunday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_sunday">الأحد</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="monday" id="day_monday" {{ in_array('monday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_monday">الإثنين</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="tuesday" id="day_tuesday" {{ in_array('tuesday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_tuesday">الثلاثاء</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="wednesday" id="day_wednesday" {{ in_array('wednesday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_wednesday">الأربعاء</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="thursday" id="day_thursday" {{ in_array('thursday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_thursday">الخميس</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="friday" id="day_friday" {{ in_array('friday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_friday">الجمعة</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability_days[]" value="saturday" id="day_saturday" {{ in_array('saturday', old('availability_days', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_saturday">السبت</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="hours_from" class="form-label">من الساعة</label>
                                    <input type="time" class="form-control" id="hours_from" name="hours_from" value="{{ old('hours_from', '09:00') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="hours_to" class="form-label">إلى الساعة</label>
                                    <input type="time" class="form-control" id="hours_to" name="hours_to" value="{{ old('hours_to', '17:00') }}">
                                </div>

                                <!-- Service Areas -->
                                <div class="col-12">
                                    <label class="form-label">مناطق تقديم الخدمة</label>
                                    <div class="row g-2">
                                        @foreach($cities as $city)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="service_areas[]" 
                                                           value="{{ $city->id }}" 
                                                           id="area_{{ $city->id }}"
                                                           {{ in_array($city->id, old('service_areas', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="area_{{ $city->id }}">
                                                        {{ $city->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Requirements -->
                                <div class="col-12">
                                    <label for="requirements" class="form-label">متطلبات أو ملاحظات إضافية</label>
                                    <textarea class="form-control" 
                                              id="requirements" 
                                              name="requirements" 
                                              rows="3" 
                                              maxlength="1000"
                                              placeholder="اذكر أي متطلبات خاصة أو ملاحظات تود إضافتها...">{{ old('requirements') }}</textarea>
                                    <div class="form-text">الحد الأقصى 1000 حرف</div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)">
                                    <i class="bi bi-arrow-right me-2"></i> السابق
                                </button>
                                <button type="submit" class="btn btn-success px-5">
                                    <i class="bi bi-check-circle me-2"></i> نشر الخدمة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.step-indicator {
    position: relative;
}

.step-item {
    text-align: center;
    flex: 1;
    position: relative;
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
    margin: 0 auto 10px;
    transition: all 0.3s;
}

.step-item.active .step-circle {
    background: #0d6efd;
    color: white;
}

.step-item.completed .step-circle {
    background: #198754;
    color: white;
}

.step-label {
    font-size: 14px;
    color: #6c757d;
}

.step-item.active .step-label {
    color: #0d6efd;
    font-weight: 600;
}

.step-line {
    height: 2px;
    background: #e9ecef;
    flex: 1;
    margin: 0 -10px;
    position: relative;
    top: -35px;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pricing-option .btn-check:checked + .btn {
    background-color: #0d6efd;
    color: white;
}

.image-preview-item {
    position: relative;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.image-preview-item .remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.image-preview-item .remove-image:hover {
    background: #dc3545;
    transform: scale(1.1);
}
</style>

<script>
let currentStep = 1;

function nextStep(step) {
    // Validate current step before moving
    if (!validateStep(currentStep)) {
        return;
    }

    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.add('completed');
    
    currentStep = step;
    
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    document.querySelector(`.step-item[data-step="${step}"]`).classList.add('active');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.remove('active');
    
    currentStep = step;
    
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    let isValid = true;
    const currentForm = document.querySelector(`.form-step[data-step="${step}"]`);
    
    // Get all required fields in current step
    const requiredFields = currentForm.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value || field.value.trim() === '') {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        alert('الرجاء ملء جميع الحقول المطلوبة');
    }
    
    return isValid;
}

function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="image-preview-item">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                preview.appendChild(col);
            }
            
            reader.readAsDataURL(file);
        });
    }
}

function removeImage(index) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    previewImages(input);
}

// Remove validation error on input
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'تعديل الخدمة')

@section('content')
<div class="container py-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="fw-bold">تعديل الخدمة</h2>
                <p class="text-muted">قم بتحديث معلومات خدمتك</p>
            </div>

            <!-- Form -->
            <form action="{{ route('user.services.update', $service) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4">
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
                                        <option value="{{ $category->id }}" 
                                                {{ old('service_category_id', $service->service_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- City (Read-only) -->
                            <div class="col-md-6">
                                <label for="city_id" class="form-label">
                                    المدينة <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $service->city->name }}" 
                                       readonly 
                                       style="background-color: #e9ecef;">
                                <small class="text-muted">لا يمكن تغيير المدينة</small>
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
                                       value="{{ old('title', $service->title) }}" 
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
                                          required>{{ old('description', $service->description) }}</textarea>
                                <div class="form-text">الحد الأقصى 1000 حرف</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">التسعير ومعلومات التواصل</h4>

                        <div class="row g-3">
                            <!-- Pricing Type -->
                            <div class="col-12">
                                <label class="form-label">نوع التسعير <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="radio" class="btn-check" name="pricing_type" id="pricing_fixed" value="fixed" 
                                               {{ old('pricing_type', $service->pricing_type) == 'fixed' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100" for="pricing_fixed">
                                            <i class="bi bi-tag-fill d-block fs-4 mb-2"></i>
                                            سعر ثابت
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="radio" class="btn-check" name="pricing_type" id="pricing_hourly" value="hourly"
                                               {{ old('pricing_type', $service->pricing_type) == 'hourly' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100" for="pricing_hourly">
                                            <i class="bi bi-clock-fill d-block fs-4 mb-2"></i>
                                            بالساعة
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="radio" class="btn-check" name="pricing_type" id="pricing_per_km" value="per_km"
                                               {{ old('pricing_type', $service->pricing_type) == 'per_km' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100" for="pricing_per_km">
                                            <i class="bi bi-speedometer2 d-block fs-4 mb-2"></i>
                                            بالكيلومتر
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <input type="radio" class="btn-check" name="pricing_type" id="pricing_negotiable" value="negotiable"
                                               {{ old('pricing_type', $service->pricing_type) == 'negotiable' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100" for="pricing_negotiable">
                                            <i class="bi bi-chat-dots-fill d-block fs-4 mb-2"></i>
                                            تفاوض
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="col-md-6">
                                <label for="price_from" class="form-label">السعر من (جنيه مصري)</label>
                                <input type="number" class="form-control" id="price_from" name="price_from" 
                                       value="{{ old('price_from', $service->price_from) }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label for="price_to" class="form-label">السعر إلى (جنيه مصري)</label>
                                <input type="number" class="form-control" id="price_to" name="price_to" 
                                       value="{{ old('price_to', $service->price_to) }}" min="0" step="0.01">
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $service->phone) }}" 
                                       placeholder="01XXXXXXXXX" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp" class="form-label">رقم الواتساب</label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" 
                                       value="{{ old('whatsapp', $service->whatsapp) }}" placeholder="01XXXXXXXXX">
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label">العنوان</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="{{ old('address', $service->address) }}" maxlength="500">
                            </div>

                            <!-- Requirements -->
                            <div class="col-12">
                                <label for="requirements" class="form-label">متطلبات أو ملاحظات إضافية</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="3" 
                                          maxlength="1000">{{ old('requirements', $service->requirements) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Existing Images -->
                @if($service->images && count($service->images) > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">الصور الحالية</h5>
                            <div class="row g-3">
                                @foreach($service->images as $image)
                                    <div class="col-md-3">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" alt="Service Image">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('user.services.show', $service) }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-check-circle me-2"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.pricing-option .btn-check:checked + .btn {
    background-color: #0d6efd;
    color: white;
}
</style>
@endsection

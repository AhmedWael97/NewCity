@extends('layouts.admin')

@section('title', 'إضافة إعلان جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-image"></i> إضافة إعلان جديد
        </h1>
        <a href="{{ route('admin.city-banners.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الإعلان</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.city-banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="city_id">المدينة <span class="text-danger">*</span></label>
                            <select name="city_id" id="city_id" class="form-control @error('city_id') is-invalid @enderror" required>
                                <option value="">-- اختر المدينة --</option>
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

                        <div class="form-group">
                            <label for="title">عنوان الإعلان <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">الوصف</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">وصف مختصر للإعلان (اختياري)</small>
                        </div>

                        <div class="form-group">
                            <label for="image">صورة الإعلان</label>
                            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الحجم الأقصى: 2MB | الصيغ المدعومة: JPEG, PNG, GIF, WebP</small>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" class="img-thumbnail" style="max-width: 300px;">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="link_type">نوع الرابط <span class="text-danger">*</span></label>
                            <select name="link_type" id="link_type" class="form-control @error('link_type') is-invalid @enderror" required>
                                <option value="none" {{ old('link_type') == 'none' ? 'selected' : '' }}>بدون رابط</option>
                                <option value="internal" {{ old('link_type', 'internal') == 'internal' ? 'selected' : '' }}>رابط داخلي</option>
                                <option value="external" {{ old('link_type') == 'external' ? 'selected' : '' }}>رابط خارجي</option>
                            </select>
                            @error('link_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="linkUrlGroup">
                            <label for="link_url">الرابط</label>
                            <input type="text" name="link_url" id="link_url" class="form-control @error('link_url') is-invalid @enderror" 
                                   value="{{ old('link_url') }}" placeholder="/shops/featured أو https://example.com">
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="linkHint">
                                رابط داخلي: /shops/featured | رابط خارجي: https://example.com
                            </small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">تاريخ البداية <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية</label>
                                    <input type="date" name="end_date" id="end_date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركه فارغاً للإعلان الدائم</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="priority">الأولوية <span class="text-danger">*</span></label>
                            <input type="number" name="priority" id="priority" 
                                   class="form-control @error('priority') is-invalid @enderror" 
                                   value="{{ old('priority', 10) }}" min="0" max="100" required>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الأولوية الأعلى تظهر أولاً (0-100)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    تفعيل الإعلان
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعلان
                            </button>
                            <a href="{{ route('admin.city-banners.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> معلومات مفيدة
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">نوع الرابط:</h6>
                    <ul class="small">
                        <li><strong>داخلي:</strong> لربط الإعلان بصفحة داخل التطبيق (مثل: /shops/featured)</li>
                        <li><strong>خارجي:</strong> لربط الإعلان بموقع خارجي (مثل: https://example.com)</li>
                        <li><strong>بدون رابط:</strong> الإعلان للعرض فقط دون رابط</li>
                    </ul>

                    <hr>

                    <h6 class="font-weight-bold">الأولوية:</h6>
                    <p class="small">
                        الإعلانات ذات الأولوية الأعلى تظهر أولاً في قائمة الإعلانات. 
                        استخدم قيمة من 0 إلى 100 (الافتراضي: 10).
                    </p>

                    <hr>

                    <h6 class="font-weight-bold">الفترة الزمنية:</h6>
                    <p class="small">
                        حدد تاريخ البداية والنهاية للإعلان. إذا تركت تاريخ النهاية فارغاً، 
                        سيكون الإعلان دائماً حتى يتم تعطيله يدوياً.
                    </p>

                    <hr>

                    <h6 class="font-weight-bold">مقاسات الصورة الموصى بها:</h6>
                    <ul class="small">
                        <li>العرض: 800-1200 بكسل</li>
                        <li>الارتفاع: 400-600 بكسل</li>
                        <li>النسبة: 2:1 (أفقي)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Link type change handler
    document.getElementById('link_type').addEventListener('change', function() {
        const linkUrlGroup = document.getElementById('linkUrlGroup');
        const linkUrl = document.getElementById('link_url');
        const linkHint = document.getElementById('linkHint');
        
        if (this.value === 'none') {
            linkUrlGroup.style.display = 'none';
            linkUrl.required = false;
        } else {
            linkUrlGroup.style.display = 'block';
            linkUrl.required = true;
            
            if (this.value === 'internal') {
                linkHint.textContent = 'مثال: /shops/featured أو /categories/restaurants';
            } else {
                linkHint.textContent = 'مثال: https://example.com';
            }
        }
    });

    // Trigger on page load
    document.getElementById('link_type').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

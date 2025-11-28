@extends('layouts.admin')

@section('title', 'إضافة خبر جديد')

@push('styles')
<style>
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
    }
    
    .images-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .images-preview img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 8px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-newspaper"></i> إضافة خبر جديد
        </h1>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Title -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">العنوان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Slug -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="slug">الرابط (Slug)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" 
                                   placeholder="يتم إنشاؤه تلقائياً">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">الوصف المختصر <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    <small class="form-text text-muted">وصف مختصر يظهر في قوائم الأخبار (حتى 500 حرف)</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label for="content">المحتوى <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <!-- Category -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="category_id">التصنيف</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                <option value="">-- بدون تصنيف --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- City -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city_id">المدينة</label>
                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                    id="city_id" name="city_id">
                                <option value="">-- عام (جميع المدن) --</option>
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
                    </div>

                    <!-- Published Date -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="published_at">تاريخ النشر</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                   id="published_at" name="published_at" value="{{ old('published_at') }}">
                            <small class="form-text text-muted">اتركه فارغاً للنشر الآن</small>
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div class="form-group">
                    <label for="thumbnail">الصورة المصغرة</label>
                    <input type="file" class="form-control-file @error('thumbnail') is-invalid @enderror" 
                           id="thumbnail" name="thumbnail" accept="image/*">
                    <small class="form-text text-muted">الصورة الرئيسية للخبر (يُنصح بحجم 1200x630 بكسل)</small>
                    @error('thumbnail')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div id="thumbnailPreview"></div>
                </div>

                <!-- Images -->
                <div class="form-group">
                    <label for="images">صور إضافية</label>
                    <input type="file" class="form-control-file @error('images.*') is-invalid @enderror" 
                           id="images" name="images[]" accept="image/*" multiple>
                    <small class="form-text text-muted">يمكنك رفع عدة صور للخبر</small>
                    @error('images.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div id="imagesPreview" class="images-preview"></div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" value="1" class="custom-control-input" id="is_active" name="is_active" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">نشر الخبر</label>
                    </div>
                </div>

                <!-- Send Notification -->
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" value="1" class="custom-control-input" id="send_notification" name="send_notification" 
                               {{ old('send_notification') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="send_notification">
                            <i class="fas fa-bell me-1"></i>
                            إرسال إشعار لجميع المستخدمين
                        </label>
                    </div>
                    <small class="form-text text-muted">سيتم إرسال إشعار فوري بعنوان الخبر لجميع المستخدمين</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ الخبر
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content', {
        language: 'ar',
        height: 400
    });

    // Auto-generate slug from title
    $('#title').on('blur', function() {
        if ($('#slug').val() == '') {
            var title = $(this).val();
            var slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            $('#slug').val(slug);
        }
    });

    // Thumbnail preview
    $('#thumbnail').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnailPreview').html('<img src="' + e.target.result + '" class="image-preview">');
            }
            reader.readAsDataURL(file);
        }
    });

    // Multiple images preview
    $('#images').on('change', function() {
        $('#imagesPreview').empty();
        var files = this.files;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagesPreview').append('<img src="' + e.target.result + '">');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

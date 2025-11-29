@extends('layouts.admin')

@section('title', 'تعديل الخبر')

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
    
    .image-item {
        position: relative;
        display: inline-block;
    }
    
    .image-item img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .image-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-newspaper"></i> تعديل الخبر
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
            <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Title -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">العنوان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $news->title) }}" required>
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
                                   id="slug" name="slug" value="{{ old('slug', $news->slug) }}">
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
                              id="description" name="description" rows="3" required>{{ old('description', $news->description) }}</textarea>
                    <small class="form-text text-muted">وصف مختصر يظهر في قوائم الأخبار (حتى 500 حرف)</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label for="content">المحتوى <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" name="content" rows="15" required>{{ old('content', $news->content) }}</textarea>
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
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $city->id }}" 
                                        {{ old('city_id', $news->city_id) == $city->id ? 'selected' : '' }}>
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
                                   id="published_at" name="published_at" 
                                   value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Thumbnail -->
                @if($news->thumbnail)
                <div class="form-group">
                    <label>الصورة المصغرة الحالية</label>
                    <div>
                        <img src="{{ $news->thumbnail_url }}" class="image-preview" alt="Thumbnail">
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" id="remove_thumbnail" name="remove_thumbnail">
                            <label class="custom-control-label text-danger" for="remove_thumbnail">
                                حذف الصورة المصغرة
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- New Thumbnail -->
                <div class="form-group">
                    <label for="thumbnail">{{ $news->thumbnail ? 'تغيير الصورة المصغرة' : 'الصورة المصغرة' }}</label>
                    <input type="file" class="form-control-file @error('thumbnail') is-invalid @enderror" 
                           id="thumbnail" name="thumbnail" accept="image/*">
                    <small class="form-text text-muted">الصورة الرئيسية للخبر (يُنصح بحجم 1200x630 بكسل)</small>
                    @error('thumbnail')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div id="thumbnailPreview"></div>
                </div>

                <!-- Current Images -->
                @if($news->images && count($news->images) > 0)
                <div class="form-group">
                    <label>الصور الإضافية الحالية</label>
                    <div class="images-preview">
                        @foreach($news->images as $image)
                        <div class="image-item">
                            <img src="{{ Storage::url($image) }}" alt="Image">
                            <button type="button" class="remove-btn" onclick="markImageForRemoval('{{ $image }}', this)">×</button>
                            <input type="checkbox" name="remove_images[]" value="{{ $image }}" style="display:none;">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- New Images -->
                <div class="form-group">
                    <label for="images">{{ $news->images ? 'إضافة صور جديدة' : 'صور إضافية' }}</label>
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
                               {{ old('is_active', $news->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">نشر الخبر</label>
                    </div>
                </div>

                <!-- Send Notification -->
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="send_notification" name="send_notification" 
                               {{ old('send_notification') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="send_notification">
                            <i class="fas fa-bell me-1"></i>
                            إرسال إشعار لجميع المستخدمين عن هذا التحديث
                        </label>
                    </div>
                    <small class="form-text text-muted">سيتم إرسال إشعار فوري بعنوان الخبر لجميع المستخدمين</small>
                </div>

                <!-- Stats -->
                <div class="alert alert-info">
                    <strong>إحصائيات:</strong> 
                    المشاهدات: {{ number_format($news->views_count) }} | 
                    تاريخ الإنشاء: {{ $news->created_at->format('Y-m-d H:i') }}
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التعديلات
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

    function markImageForRemoval(imagePath, button) {
        var imageItem = button.closest('.image-item');
        var checkbox = imageItem.querySelector('input[type="checkbox"]');
        
        if (checkbox.checked) {
            checkbox.checked = false;
            imageItem.style.opacity = '1';
            button.textContent = '×';
            button.style.background = 'rgba(220, 53, 69, 0.9)';
        } else {
            checkbox.checked = true;
            imageItem.style.opacity = '0.3';
            button.textContent = '↺';
            button.style.background = 'rgba(40, 167, 69, 0.9)';
        }
    }

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
                $('#imagesPreview').append('<div class="image-item"><img src="' + e.target.result + '"></div>');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

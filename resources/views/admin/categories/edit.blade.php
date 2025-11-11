@extends('layouts.admin')

@section('title', 'تعديل الفئة: ' . $category->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل الفئة: {{ $category->name }}
        </h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الفئة</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">اسم الفئة بالعربية <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_en">اسم الفئة بالإنجليزية</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en', $category->name_en) }}">
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">الرابط (Slug) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">يُستخدم في الروابط (مثال: restaurants, cafes)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">الفئة الأب (اختياري)</label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror" 
                                            id="parent_id" name="parent_id">
                                        <option value="">-- اختر الفئة الأب --</option>
                                        @foreach(\App\Models\Category::where('id', '!=', $category->id)->whereNull('parent_id')->get() as $cat)
                                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($category->parent)
                                        <small class="text-muted">الفئة الأب الحالية: {{ $category->parent->name }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">وصف الفئة بالعربية</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description_en">وصف الفئة بالإنجليزية</label>
                                    <textarea class="form-control @error('description_en') is-invalid @enderror" 
                                              id="description_en" name="description_en" rows="4">{{ old('description_en', $category->description_en) }}</textarea>
                                    @error('description_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Media Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">الصور والأيقونات</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="image">صورة الفئة</label>
                                            <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                                                   id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($category->image)
                                                <div class="mt-2">
                                                    <small class="text-muted">الصورة الحالية:</small>
                                                    <br>
                                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                                                         class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="icon">أيقونة الفئة (Font Awesome)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i id="icon-preview" class="{{ $category->icon ?? 'fas fa-store' }}"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                       id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fas fa-store') }}" 
                                                       placeholder="fas fa-store">
                                            </div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">أدخل كلاس Font Awesome (مثال: fas fa-utensils)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="color">لون الفئة</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control @error('color') is-invalid @enderror" 
                                                       id="color" name="color" value="{{ old('color', $category->color ?? '#007bff') }}" 
                                                       style="height: 38px;">
                                                <div class="input-group-append">
                                                    <input type="text" class="form-control" id="color-text" 
                                                           value="{{ old('color', $category->color ?? '#007bff') }}" readonly>
                                                </div>
                                            </div>
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">تحسين محركات البحث (SEO)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meta_title">عنوان الصفحة (Meta Title)</label>
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                                   id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" 
                                                   maxlength="60">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">الطول المناسب: 50-60 حرف</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meta_keywords">الكلمات المفتاحية</label>
                                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                                   id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $category->meta_keywords) }}" 
                                                   placeholder="مطاعم، كافيهات، متاجر">
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">افصل بين الكلمات بفاصلة</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="meta_description">وصف الصفحة (Meta Description)</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                      id="meta_description" name="meta_description" rows="3" 
                                                      maxlength="160">{{ old('meta_description', $category->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">الطول المناسب: 120-160 حرف</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">الإعدادات</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                الفئة نشطة
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                   {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                فئة مميزة
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_order">ترتيب العرض</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_in_menu" name="show_in_menu" value="1" 
                                                   {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_in_menu">
                                                ظهور في القائمة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        @if($category->exists)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">إحصائيات الفئة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $category->shops->count() }}</h4>
                                            <small class="text-muted">المتاجر</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ $category->children->count() }}</h4>
                                            <small class="text-muted">الفئات الفرعية</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-info">{{ $category->created_at->format('Y-m-d') }}</h4>
                                            <small class="text-muted">تاريخ الإنشاء</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning">{{ $category->updated_at->format('Y-m-d') }}</h4>
                                            <small class="text-muted">آخر تحديث</small>
                                        </div>
                                    </div>
                                </div>

                                @if($category->children->count() > 0)
                                <div class="mt-3">
                                    <h6 class="font-weight-bold">الفئات الفرعية:</h6>
                                    <div class="row">
                                        @foreach($category->children as $child)
                                        <div class="col-md-3 mb-2">
                                            <span class="badge badge-info">{{ $child->name }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> عرض الفئة
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
                      .replace(/[^\w\s-]/g, '') // Remove special characters
                      .replace(/\s+/g, '-');    // Replace spaces with hyphens
        $('#slug').val(slug);
    });

    // Auto-generate English name from Arabic (basic transliteration)
    $('#name').on('input', function() {
        var arabicName = $(this).val();
        // This is a basic example - you might want to use a proper transliteration library
        var englishName = arabicName.replace(/ا/g, 'a')
                                   .replace(/ب/g, 'b')
                                   .replace(/ت/g, 't')
                                   .replace(/ث/g, 'th')
                                   .replace(/ج/g, 'j')
                                   .replace(/ح/g, 'h')
                                   .replace(/خ/g, 'kh')
                                   .replace(/د/g, 'd')
                                   .replace(/ذ/g, 'dh')
                                   .replace(/ر/g, 'r')
                                   .replace(/ز/g, 'z')
                                   .replace(/س/g, 's')
                                   .replace(/ش/g, 'sh')
                                   .replace(/ص/g, 's')
                                   .replace(/ض/g, 'd')
                                   .replace(/ط/g, 't')
                                   .replace(/ظ/g, 'z')
                                   .replace(/ع/g, 'a')
                                   .replace(/غ/g, 'gh')
                                   .replace(/ف/g, 'f')
                                   .replace(/ق/g, 'q')
                                   .replace(/ك/g, 'k')
                                   .replace(/ل/g, 'l')
                                   .replace(/م/g, 'm')
                                   .replace(/ن/g, 'n')
                                   .replace(/ه/g, 'h')
                                   .replace(/و/g, 'w')
                                   .replace(/ي/g, 'y');
        
        if ($('#name_en').val() === '{{ $category->name_en }}') {
            $('#name_en').val(englishName);
        }
    });

    // Update icon preview
    $('#icon').on('input', function() {
        var iconClass = $(this).val();
        $('#icon-preview').attr('class', iconClass);
    });

    // Update color text field
    $('#color').on('input', function() {
        $('#color-text').val($(this).val());
    });

    // Auto-generate meta title from name
    $('#name').on('input', function() {
        if ($('#meta_title').val() === '{{ $category->meta_title }}' || $('#meta_title').val() === '') {
            $('#meta_title').val($(this).val() + ' - متاجر المدينة');
        }
    });

    // Auto-generate meta description from description
    $('#description').on('input', function() {
        if ($('#meta_description').val() === '{{ $category->meta_description }}' || $('#meta_description').val() === '') {
            var desc = $(this).val();
            if (desc.length > 150) {
                desc = desc.substring(0, 150) + '...';
            }
            $('#meta_description').val(desc);
        }
    });
});
</script>
@endpush
@endsection
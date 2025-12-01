@extends('layouts.app')

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">المنتدى</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.category', $category) }}">{{ $category->name }}</a></li>
            <li class="breadcrumb-item active">موضوع جديد</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> إنشاء موضوع جديد في {{ $category->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('forum.storeThread', $category) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الموضوع <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   placeholder="اختر عنواناً واضحاً ومختصراً للموضوع" 
                                   required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">اكتب عنواناً واضحاً يعبر عن محتوى الموضوع</small>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">محتوى الموضوع <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" 
                                      name="body" 
                                      rows="10" 
                                      placeholder="اكتب تفاصيل موضوعك هنا..." 
                                      required>{{ old('body') }}</textarea>
                            @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">اشرح موضوعك بالتفصيل، يمكنك استخدام أسطر متعددة</small>
                        </div>

                        @if($category->city_id === null)
                        <div class="mb-3">
                            <label for="city_id" class="form-label">المدينة (اختياري)</label>
                            <select class="form-select @error('city_id') is-invalid @enderror" 
                                    id="city_id" 
                                    name="city_id">
                                <option value="">جميع المدن</option>
                                @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">حدد المدينة إذا كان الموضوع خاصاً بمنطقة معينة</small>
                        </div>
                        @endif

                        @if($category->requires_approval)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            ملاحظة: المواضيع في هذا القسم تحتاج إلى موافقة المشرفين قبل نشرها.
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('forum.category', $category) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> نشر الموضوع
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> إرشادات كتابة موضوع جيد</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>استخدم عنواناً واضحاً يعبر عن محتوى الموضوع</li>
                        <li>اشرح موضوعك بالتفصيل واستخدم فقرات منظمة</li>
                        <li>كن محترماً ولبقاً في التعبير عن آرائك</li>
                        <li>تحقق من عدم وجود مواضيع مشابهة قبل النشر</li>
                        <li>اختر القسم والمدينة المناسبين للموضوع</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'إضافة تصنيف جديد')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tag"></i> إضافة تصنيف جديد
        </h1>
        <a href="{{ route('admin.news.categories') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> العودة
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
            <form action="{{ route('admin.news.categories.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">الرابط (Slug)</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="يتم إنشاؤه تلقائياً">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="order">الترتيب</label>
                            <input type="number" class="form-control" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">نشط</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('admin.news.categories') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

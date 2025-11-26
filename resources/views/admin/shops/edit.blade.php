@extends('layouts.admin')

@section('title', 'تعديل المتجر - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل المتجر: {{ $shop->name }}
        </h1>
        <div>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> عرض التفاصيل
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المتجر</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shops.update', $shop) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">اسم المتجر <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $shop->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">الرابط (Slug)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $shop->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">المالك <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">اختر المالك</option>
                                        @foreach($users ?? [] as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $shop->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city_id">المدينة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $shop->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category_id">التصنيف <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $shop->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $shop->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address', $shop->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">خط العرض</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $shop->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">خط الطول</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $shop->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $shop->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $shop->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="website">الموقع الإلكتروني</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $shop->website) }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Images -->
                        @if($shop->images && count($shop->images) > 0)
                            <div class="form-group">
                                <label>الصور الحالية</label>
                                <div class="row">
                                    @foreach($shop->images as $index => $image)
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="{{ $image }}" class="card-img-top" alt="Shop Image" style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $index }}" id="remove_{{ $index }}">
                                                        <label class="form-check-label" for="remove_{{ $index }}">
                                                            حذف هذه الصورة
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="images">إضافة صور جديدة</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <small class="form-text text-muted">يمكنك اختيار عدة صور</small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', $shop->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                        <option value="approved" {{ old('status', $shop->status) == 'approved' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ old('status', $shop->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                        <option value="suspended" {{ old('status', $shop->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>الخيارات</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" {{ old('is_verified', $shop->is_verified) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">محقق</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $shop->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">مميز</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $shop->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">نشط</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="verification_notes">ملاحظات التحقق</label>
                            <textarea class="form-control @error('verification_notes') is-invalid @enderror" 
                                      id="verification_notes" name="verification_notes" rows="3">{{ old('verification_notes', $shop->verification_notes) }}</textarea>
                            @error('verification_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    let name = this.value;
    let slug = name.toLowerCase()
                   .replace(/[^\w\s-]/g, '') // Remove special characters
                   .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with -
                   .replace(/^-+|-+$/g, ''); // Remove leading/trailing dashes
    document.getElementById('slug').value = slug;
});
</script>
@endsection
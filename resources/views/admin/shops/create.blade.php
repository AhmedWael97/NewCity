@extends('layouts.admin')

@section('title', 'إضافة متجر جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> إضافة متجر جديد
        </h1>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المتجر</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.shops.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input 
                                    name="name" 
                                    label="اسم المتجر" 
                                    :value="old('name')"
                                    icon="fas fa-store"
                                    :required="true"
                                    placeholder="أدخل اسم المتجر"
                                />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input 
                                    name="slug" 
                                    label="الرابط (Slug)" 
                                    :value="old('slug')"
                                    icon="fas fa-link"
                                    placeholder="سيتم إنشاؤه تلقائياً من الاسم"
                                    helpText="اتركه فارغاً للإنشاء التلقائي"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.select 
                                    name="user_id" 
                                    label="المالك" 
                                    :options="$users->pluck('name', 'id')->map(fn($name, $id) => $name . ' (' . $users->find($id)->email . ')')->toArray()"
                                    :value="old('user_id')"
                                    icon="fas fa-user"
                                    :required="true"
                                    placeholder="اختر المالك"
                                />
                            </div>

                            <div class="col-md-3">
                                <x-form.select 
                                    name="city_id" 
                                    label="المدينة" 
                                    :options="$cities->pluck('name', 'id')->toArray()"
                                    :value="old('city_id')"
                                    icon="fas fa-city"
                                    :required="true"
                                    placeholder="اختر المدينة"
                                />
                            </div>

                            <div class="col-md-3">
                                <x-form.select 
                                    name="category_id" 
                                    label="التصنيف" 
                                    :options="$categories->pluck('name', 'id')->toArray()"
                                    :value="old('category_id')"
                                    icon="fas fa-tags"
                                    :required="true"
                                    placeholder="اختر التصنيف"
                                />
                            </div>
                        </div>

                        <x-form.textarea 
                            name="description" 
                            label="الوصف" 
                            :value="old('description')"
                            icon="fas fa-align-right"
                            rows="4"
                            placeholder="وصف تفصيلي عن المتجر"
                        />

                        <x-google-maps-picker 
                            addressId="address"
                            latitudeId="latitude"
                            longitudeId="longitude"
                            :addressValue="old('address', '')"
                            :latitudeValue="old('latitude', '')"
                            :longitudeValue="old('longitude', '')"
                            height="450px"
                        />

                        <div class="row">
                            <div class="col-md-12">
                                <x-form.input 
                                    name="address" 
                                    label="العنوان" 
                                    :value="old('address')"
                                    icon="fas fa-map-marker-alt"
                                    placeholder="عنوان المتجر الكامل"
                                    helpText="سيتم ملؤه تلقائياً عند تحديد الموقع على الخريطة"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input 
                                    name="latitude" 
                                    type="number"
                                    label="خط العرض" 
                                    :value="old('latitude')"
                                    icon="fas fa-map-pin"
                                    placeholder="24.774265"
                                    helpText="يتم ملؤه تلقائياً من الخريطة"
                                    step="any"
                                    readonly
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input 
                                    name="longitude" 
                                    type="number"
                                    label="خط الطول" 
                                    :value="old('longitude')"
                                    icon="fas fa-map-pin"
                                    placeholder="46.738586"
                                    helpText="يتم ملؤه تلقائياً من الخريطة"
                                    step="any"
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <x-form.input 
                                    name="phone" 
                                    type="tel"
                                    label="رقم الهاتف" 
                                    :value="old('phone')"
                                    icon="fas fa-phone"
                                    placeholder="+966 50 123 4567"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input 
                                    name="email" 
                                    type="email"
                                    label="البريد الإلكتروني" 
                                    :value="old('email')"
                                    icon="fas fa-envelope"
                                    placeholder="shop@example.com"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input 
                                    name="website" 
                                    type="url"
                                    label="الموقع الإلكتروني" 
                                    :value="old('website')"
                                    icon="fas fa-globe"
                                    placeholder="https://example.com"
                                />
                            </div>
                        </div>

                        <!-- Image Upload Component -->
                        <x-dropzone-uploader 
                            name="images"
                            :showCurrentImages="false"
                            dropzoneId="shop-images-dropzone"
                        />

                        <div class="row">
                            <div class="col-md-4">
                                <x-form.select 
                                    name="status" 
                                    label="الحالة" 
                                    :options="[
                                        'pending' => 'في الانتظار',
                                        'approved' => 'مقبول',
                                        'rejected' => 'مرفوض',
                                        'suspended' => 'معلق'
                                    ]"
                                    :value="old('status', 'pending')"
                                    icon="fas fa-flag"
                                />
                            </div>
                            <div class="col-md-8">
                                <label class="d-block mb-2">الخيارات</label>
                                <div class="d-flex align-items-center" style="gap: 2rem; flex-wrap: wrap;">
                                    <x-form.checkbox 
                                        name="is_verified" 
                                        label="محقق" 
                                        :checked="old('is_verified', false)"
                                    />
                                    <x-form.checkbox 
                                        name="is_featured" 
                                        label="مميز" 
                                        :checked="old('is_featured', false)"
                                    />
                                    <x-form.checkbox 
                                        name="is_active" 
                                        label="نشط" 
                                        :checked="old('is_active', true)"
                                    />
                                </div>
                            </div>
                        </div>

                        <x-form.textarea 
                            name="verification_notes" 
                            label="ملاحظات التحقق" 
                            :value="old('verification_notes')"
                            icon="fas fa-sticky-note"
                            rows="3"
                            placeholder="ملاحظات خاصة بعملية التحقق من المتجر"
                            helpText="ملاحظات داخلية للإدارة"
                        />

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ المتجر
                            </button>
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

<style>
#image-upload-container {
    transition: all 0.3s ease;
}
#image-upload-container:hover {
    border-color: #4e73df !important;
    background-color: #f8f9fc !important;
}
#image-upload-container.drag-over {
    border: 2px dashed #4e73df !important;
    background-color: #e3ebff !important;
}
.image-preview-item {
    position: relative;
    border: 2px solid #dee2e6;
}
</style>

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
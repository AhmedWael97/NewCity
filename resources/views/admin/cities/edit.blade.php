@extends('layouts.admin')

@section('title', 'تعديل المدينة: ' . $city->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل المدينة: {{ $city->name }}
        </h1>
        <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المدينة</h6>
                </div>
                <div class="card-body">
                    {{-- Display validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle"></i> يوجد أخطاء في البيانات المدخلة
                            </h5>
                            <hr>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.cities.update', $city) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4">
                                <x-form.input
                                    name="name"
                                    label="اسم المدينة"
                                    icon="city"
                                    :required="true"
                                    :value="$city->name"
                                    placeholder="مثال: القاهرة"
                                />
                            </div>
                            
                            <div class="col-md-4">
                                <x-form.input
                                    name="slug"
                                    label="الرابط (Slug)"
                                    icon="link"
                                    :required="true"
                                    :value="$city->slug"
                                    placeholder="cairo"
                                    help-text="يُستخدم في الروابط"
                                />
                            </div>

                            <div class="col-md-4">
                                <x-form.input
                                    name="country"
                                    label="الدولة"
                                    icon="flag"
                                    :required="true"
                                    :value="$city->country"
                                    placeholder="مثال: مصر"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    name="state"
                                    label="الولاية / المحافظة"
                                    icon="map-marker-alt"
                                    :value="$city->state"
                                    placeholder="مثال: محافظة القاهرة"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-form.checkbox
                                    name="is_active"
                                    label="المدينة نشطة"
                                    :checked="$city->is_active"
                                    help-text="تفعيل/تعطيل ظهور المدينة"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-form.textarea
                                    name="description"
                                    label="وصف المدينة"
                                    :rows="4"
                                    :value="$city->description"
                                    placeholder="أدخل وصفاً تفصيلياً للمدينة..."
                                />
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-map-marked-alt"></i> المعلومات الجغرافية
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="latitude"
                                            type="number"
                                            label="خط العرض (Latitude)"
                                            icon="compass"
                                            :value="$city->latitude"
                                            placeholder="30.0444"
                                            step="0.000001"
                                            help-text="قيمة من -90 إلى 90"
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <x-form.input
                                            name="longitude"
                                            type="number"
                                            label="خط الطول (Longitude)"
                                            icon="compass"
                                            :value="$city->longitude"
                                            placeholder="31.2357"
                                            step="0.000001"
                                            help-text="قيمة من -180 إلى 180"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-images"></i> الصور والوسائط
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-form.file
                                            name="image"
                                            label="صورة المدينة"
                                            accept="image/*"
                                            :preview="true"
                                            :current-file="$city->image"
                                            help-text="اختر صورة بصيغة JPG, PNG, GIF (حد أقصى 2 ميجابايت)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        @if($city->exists)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">إحصائيات المدينة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $city->shops->count() }}</h4>
                                            <small class="text-muted">المتاجر</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ $city->users->count() }}</h4>
                                            <small class="text-muted">المستخدمين</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-info">{{ $city->created_at->format('Y-m-d') }}</h4>
                                            <small class="text-muted">تاريخ الإنشاء</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning">{{ $city->updated_at->format('Y-m-d') }}</h4>
                                            <small class="text-muted">آخر تحديث</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> عرض المدينة
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
        
        if ($('#name_en').val() === '{{ $city->name_en }}') {
            $('#name_en').val(englishName);
        }
    });
});
</script>
@endpush
@endsection
@extends('layouts.admin')

@section('title', 'إضافة إعلان جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة إعلان جديد</h1>
        <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل الإعلان</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">عنوان الإعلان <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف الإعلان</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الحد الأقصى 1000 حرف</small>
                        </div>

                        <div class="form-group">
                            <label for="image">صورة الإعلان</label>
                            <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الحد الأقصى 2 ميجابايت. الصيغ المدعومة: JPEG, PNG, JPG, GIF</small>
                        </div>

                        <div class="form-group">
                            <label for="click_url">رابط الإعلان <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('click_url') is-invalid @enderror" 
                                   id="click_url" name="click_url" value="{{ old('click_url') }}" required>
                            @error('click_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الرابط الذي سيتم توجيه المستخدمين إليه عند النقر على الإعلان</small>
                        </div>

                        <!-- Ad Type and Placement -->
                        <hr>
                        <h5 class="mb-3">نوع ومكان عرض الإعلان</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">نوع الإعلان <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">اختر نوع الإعلان</option>
                                        <option value="hero" {{ old('type') == 'hero' ? 'selected' : '' }}>إعلان رئيسي (Hero)</option>
                                        <option value="banner" {{ old('type') == 'banner' ? 'selected' : '' }}>بانر</option>
                                        <option value="sidebar" {{ old('type') == 'sidebar' ? 'selected' : '' }}>إعلان جانبي</option>
                                        <option value="sponsored_listing" {{ old('type') == 'sponsored_listing' ? 'selected' : '' }}>قائمة مموّلة</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scope">نطاق العرض <span class="text-danger">*</span></label>
                                    <select class="form-control @error('scope') is-invalid @enderror" 
                                            id="scope" name="scope" required>
                                        <option value="">اختر نطاق العرض</option>
                                        <option value="global" {{ old('scope') == 'global' ? 'selected' : '' }}>عرض عالمي (جميع المدن)</option>
                                        <option value="city_specific" {{ old('scope') == 'city_specific' ? 'selected' : '' }}>مدينة محددة</option>
                                    </select>
                                    @error('scope')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- City Selection (shown when city_specific is selected) -->
                        <div class="form-group" id="city-selection" style="display: none;">
                            <label for="city_id">المدينة المستهدفة</label>
                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                    id="city_id" name="city_id">
                                <option value="">اختر المدينة</option>
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

                        <!-- Category Targeting -->
                        <div class="form-group">
                            <label for="target_categories">الفئات المستهدفة (اختياري)</label>
                            <select class="form-control @error('target_categories') is-invalid @enderror" 
                                    id="target_categories" name="target_categories[]" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('target_categories', [])) ? 'selected' : '' }}>
                                        {{ $category->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_categories')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">اختر الفئات التي تريد استهدافها. اتركها فارغة لاستهداف جميع الفئات</small>
                        </div>

                        <!-- Pricing -->
                        <hr>
                        <h5 class="mb-3">التسعير والميزانية</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pricing_model">نموذج التسعير <span class="text-danger">*</span></label>
                                    <select class="form-control @error('pricing_model') is-invalid @enderror" 
                                            id="pricing_model" name="pricing_model" required>
                                        <option value="">اختر نموذج التسعير</option>
                                        <option value="cpm" {{ old('pricing_model') == 'cpm' ? 'selected' : '' }}>CPM - التكلفة لكل ألف مشاهدة</option>
                                        <option value="cpc" {{ old('pricing_model') == 'cpc' ? 'selected' : '' }}>CPC - التكلفة لكل نقرة</option>
                                        <option value="cpa" {{ old('pricing_model') == 'cpa' ? 'selected' : '' }}>CPA - التكلفة لكل إجراء</option>
                                    </select>
                                    @error('pricing_model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_amount">السعر (بالدولار) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price_amount') is-invalid @enderror" 
                                           id="price_amount" name="price_amount" value="{{ old('price_amount') }}" 
                                           min="0.01" step="0.01" required>
                                    @error('price_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted" id="pricing-help">
                                        <!-- Dynamic pricing help text -->
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="budget_limit">الميزانية الإجمالية (اختياري)</label>
                                    <input type="number" class="form-control @error('budget_limit') is-invalid @enderror" 
                                           id="budget_limit" name="budget_limit" value="{{ old('budget_limit') }}" 
                                           min="0" step="0.01">
                                    @error('budget_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركها فارغة للميزانية اللامحدودة</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_budget_limit">الميزانية اليومية (اختياري)</label>
                                    <input type="number" class="form-control @error('daily_budget_limit') is-invalid @enderror" 
                                           id="daily_budget_limit" name="daily_budget_limit" value="{{ old('daily_budget_limit') }}" 
                                           min="0" step="0.01">
                                    @error('daily_budget_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركها فارغة لعدم وضع حد يومي</small>
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <hr>
                        <h5 class="mb-3">جدولة الإعلان</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">تاريخ البداية (اختياري)</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                           min="{{ date('Y-m-d') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركها فارغة للبدء فوراً</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية (اختياري)</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">اتركها فارغة للاستمرار إلى ما لا نهاية</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status">حالة الإعلان <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="pending_review" {{ old('status') == 'pending_review' ? 'selected' : '' }}>في المراجعة</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>متوقف</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعلان
                            </button>
                            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Pricing Guide -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">دليل التسعير</h6>
                </div>
                <div class="card-body">
                    @foreach($pricingTiers as $model => $tiers)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">{{ strtoupper($model) }}</h6>
                            @foreach($tiers as $tier => $data)
                                <div class="small mb-2">
                                    <strong>{{ $data['name'] }}:</strong> ${{ $data['min'] }} - ${{ $data['max'] }}<br>
                                    <span class="text-muted">{{ $data['description'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Preview -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معاينة الإعلان</h6>
                </div>
                <div class="card-body">
                    <div id="ad-preview" class="text-center text-muted">
                        <i class="fas fa-eye fa-3x mb-3"></i>
                        <p>املأ التفاصيل لمعاينة الإعلان</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeSelect = document.getElementById('scope');
    const citySelection = document.getElementById('city-selection');
    const pricingModelSelect = document.getElementById('pricing_model');
    const pricingHelp = document.getElementById('pricing-help');
    
    // Show/hide city selection based on scope
    scopeSelect.addEventListener('change', function() {
        if (this.value === 'city_specific') {
            citySelection.style.display = 'block';
            document.getElementById('city_id').required = true;
        } else {
            citySelection.style.display = 'none';
            document.getElementById('city_id').required = false;
        }
    });
    
    // Update pricing help text
    pricingModelSelect.addEventListener('change', function() {
        const model = this.value;
        let helpText = '';
        
        switch(model) {
            case 'cpm':
                helpText = 'التكلفة لكل 1000 مشاهدة للإعلان';
                break;
            case 'cpc':
                helpText = 'التكلفة لكل نقرة على الإعلان';
                break;
            case 'cpa':
                helpText = 'التكلفة لكل إجراء مُكتمل (شراء، تسجيل، إلخ)';
                break;
        }
        
        pricingHelp.textContent = helpText;
    });
    
    // Initialize on page load
    if (scopeSelect.value === 'city_specific') {
        citySelection.style.display = 'block';
    }
    
    if (pricingModelSelect.value) {
        pricingModelSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
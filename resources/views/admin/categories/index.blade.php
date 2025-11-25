@extends('layouts.admin')

@section('title', 'إدارة التصنيفات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags"></i> إدارة التصنيفات
        </h1>
        <div>
            <a href="{{ route('admin.categories.hierarchy') }}" class="btn btn-info btn-sm">
                <i class="fas fa-sitemap"></i> عرض الهيكل الشجري
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة تصنيف جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة التصنيفات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="اسم التصنيف، الوصف..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label>التصنيف الأب</label>
                        <select name="parent_id" class="form-control">
                            <option value="">جميع التصنيفات</option>
                            <option value="0" {{ request('parent_id') === '0' ? 'selected' : '' }}>
                                التصنيفات الرئيسية
                            </option>
                            @foreach($parentCategories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('parent_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>الحالة</label>
                        <select name="is_active" class="form-control">
                            <option value="">الجميع</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                                نشط
                            </option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                غير نشط
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>النوع</label>
                        <select name="has_children" class="form-control">
                            <option value="">الجميع</option>
                            <option value="1" {{ request('has_children') == '1' ? 'selected' : '' }}>
                                تصنيفات أب
                            </option>
                            <option value="0" {{ request('has_children') == '0' ? 'selected' : '' }}>
                                تصنيفات فرعية
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة التصنيفات ({{ $categories->total() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>الأيقونة</th>
                            <th>الاسم بالعربية</th>
                            <th>الاسم بالإنجليزية</th>
                            <th>التصنيف الأب</th>
                            <th>الترتيب</th>
                            <th>عدد المتاجر</th>
                            <th>التصنيفات الفرعية</th>
                            <th>الحالة</th>
                            <th class="no-sort">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} fa-lg text-primary"></i>
                                    @else
                                        <i class="fas fa-tag text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($category->parent_id)
                                        <span class="text-muted">└─</span>
                                    @endif
                                    <strong>{{ $category->name_ar }}</strong>
                                    @if($category->slug)
                                        <br><small class="text-muted">{{ $category->slug }}</small>
                                    @endif
                                </td>
                                <td>{{ $category->name_en ?: 'غير محدد' }}</td>
                                <td>
                                    @if($category->parent)
                                        <span class="badge bg-info text-white">{{ $category->parent->name_ar }}</span>
                                    @else
                                        <span class="badge bg-primary text-white">تصنيف رئيسي</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-white">{{ $category->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary text-white">{{ $category->shops_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($category->children_count > 0)
                                        <span class="badge bg-warning text-dark">{{ $category->children_count }}</span>
                                    @else
                                        <span class="text-muted">لا يوجد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success text-white">
                                            <i class="fas fa-check"></i> نشط
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">
                                            <i class="fas fa-pause"></i> غير نشط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} btn-sm"
                                                    title="{{ $category->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>

                                        @if($category->shops_count == 0 && $category->children_count == 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد تصنيفات</h5>
                                        <p class="text-muted">لم يتم العثور على تصنيفات مطابقة لمعايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-submit search form on filter change
document.querySelectorAll('select[name="parent_id"], select[name="is_active"], select[name="has_children"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection
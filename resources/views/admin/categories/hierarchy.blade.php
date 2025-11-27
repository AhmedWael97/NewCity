@extends('layouts.admin')

@section('title', 'التسلسل الهرمي للتصنيفات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sitemap"></i> التسلسل الهرمي للتصنيفات
        </h1>
        <div>
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" style="display: none;">
                <i class="fas fa-trash"></i> حذف المحدد (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> عرض القائمة
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة تصنيف
            </a>
        </div>
    </div>

    <!-- Categories Tree -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tree"></i> شجرة التصنيفات
                    </h6>
                </div>
                <div class="card-body">
                    @if($tree->count() > 0)
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                                <strong>تحديد الكل</strong>
                            </label>
                        </div>
                        <form id="bulkDeleteForm" action="{{ route('admin.categories.bulk-delete') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="category-tree">
                                @foreach($tree as $category)
                                    @include('admin.categories.partials.tree-item', ['category' => $category, 'level' => 0])
                                @endforeach
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">لا توجد تصنيفات</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> إضافة أول تصنيف
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-tree {
    font-family: Arial, sans-serif;
}
.tree-item {
    padding: 12px 15px;
    margin-bottom: 8px;
    background: #f8f9fa;
    border-right: 4px solid #4e73df;
    border-radius: 5px;
    transition: all 0.3s;
}
.tree-item:hover {
    background: #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.tree-item.level-1 {
    margin-right: 30px;
    border-color: #1cc88a;
}
.tree-item.level-2 {
    margin-right: 60px;
    border-color: #36b9cc;
}
.tree-item.level-3 {
    margin-right: 90px;
    border-color: #f6c23e;
}
.tree-item.level-4 {
    margin-right: 120px;
    border-color: #e74a3b;
}
.category-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.category-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}
.category-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}
.badge-shops {
    background: #4e73df;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
}
.badge-children {
    background: #1cc88a;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
}
.tree-actions {
    display: flex;
    gap: 5px;
}
.tree-actions .btn {
    padding: 4px 10px;
    font-size: 0.85rem;
}
.category-checkbox {
    margin-left: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    // Individual checkbox change
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkDeleteButton();
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        if (!selectAllCheckbox) return;
        const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === categoryCheckboxes.length && checkedCount > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < categoryCheckboxes.length;
    }

    // Update bulk delete button visibility and count
    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            selectedCountSpan.textContent = checkedCount;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Bulk delete button click
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('الرجاء تحديد تصنيف واحد على الأقل للحذف');
                return;
            }

            if (confirm(`هل أنت متأكد من حذف ${checkedCount} تصنيف؟\n\nتنبيه: سيتم حذف جميع التصنيفات الفرعية أيضاً!`)) {
                bulkDeleteForm.submit();
            }
        });
    }
});
</script>
@endsection

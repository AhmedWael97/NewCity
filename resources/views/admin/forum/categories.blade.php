@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-folder"></i> إدارة الأقسام</h2>
        <a href="{{ route('admin.forum.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة قسم جديد
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>القسم</th>
                            <th>المدينة</th>
                            <th class="text-center">المواضيع</th>
                            <th class="text-center">الردود</th>
                            <th class="text-center">يتطلب موافقة</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الترتيب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="forum-icon-sm" style="background: {{ $category->color }}20;">
                                    <i class="{{ $category->icon }}" style="color: {{ $category->color }};"></i>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($category->description, 80) }}</small>
                            </td>
                            <td>
                                @if($category->city)
                                <span class="badge bg-info">{{ $category->city->name }}</span>
                                @else
                                <span class="badge bg-secondary">جميع المدن</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $category->threads_count }}</td>
                            <td class="text-center">{{ $category->posts_count }}</td>
                            <td class="text-center">
                                @if($category->requires_approval)
                                <span class="badge bg-warning">نعم</span>
                                @else
                                <span class="badge bg-success">لا</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                <span class="badge bg-success">نشط</span>
                                @else
                                <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $category->order }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.forum.categories.edit', $category) }}" 
                                       class="btn btn-outline-primary" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.forum.categories.destroy', $category) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم حذف جميع المواضيع والردود!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.forum-icon-sm {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@extends('layouts.admin')

@section('title', 'إدارة تصنيفات الأخبار')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tags"></i> إدارة تصنيفات الأخبار
        </h1>
        <div>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للأخبار
            </a>
            <a href="{{ route('admin.news.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة تصنيف جديد
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الرابط</th>
                            <th>عدد الأخبار</th>
                            <th>الترتيب</th>
                            <th>الحالة</th>
                            <th width="150">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ $category->news_count }}</td>
                            <td>{{ $category->order }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.news.categories.edit', $category->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.news.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('هل أنت متأكد؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">لا توجد تصنيفات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection

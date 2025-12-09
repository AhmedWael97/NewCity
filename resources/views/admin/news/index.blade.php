@extends('layouts.admin')

@section('title', 'إدارة الأخبار')

@push('styles')
<style>
    .news-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .table-responsive {
        min-height: 70vh;
    }
    
    #dataTable tbody td,
    #dataTable tbody td span,
    #dataTable tbody td strong,
    #dataTable tbody td small,
    #dataTable tbody td div,
    #dataTable tbody td a {
        color: #000 !important;
    }
    
    #dataTable tbody td .badge {
        color: #fff !important;
    }
    
    #dataTable thead th {
        color: #000 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-newspaper"></i> إدارة الأخبار
        </h1>
        <div>
            @can('view-news')
            <a href="{{ route('admin.news.categories') }}" class="btn btn-info btn-sm">
                <i class="fas fa-tags"></i> إدارة التصنيفات
            </a>
            @endcan
            
            @can('create-news')
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة خبر جديد
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    <!-- News Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الأخبار</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%">
                    <thead>
                        <tr>
                            <th width="80">الصورة</th>
                            <th>العنوان</th>
                            <th>التصنيف</th>
                            <th>المدينة</th>
                            <th>المشاهدات</th>
                            <th>الحالة</th>
                            <th>تاريخ النشر</th>
                            <th width="150">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                        <tr>
                            <td>
                                @if($item->thumbnail)
                                    <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="news-thumbnail">
                                @else
                                    <div class="news-thumbnail bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->title }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($item->description, 80) }}</small>
                            </td>
                            <td>
                                @if($item->category)
                                    <span class="badge badge-info">{{ $item->category->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->city)
                                    {{ $item->city->name }}
                                @else
                                    <span class="text-muted">عام</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-eye"></i> {{ number_format($item->views_count) }}
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                @if($item->published_at)
                                    {{ $item->published_at->format('Y-m-d') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @can('view-news')
                                <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-info" target="_blank" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('edit-news')
                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete-news')
                                <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <p class="text-muted my-4">لا توجد أخبار حالياً</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "order": [[6, "desc"]],
        "pageLength": 25
    });
});
</script>
@endpush

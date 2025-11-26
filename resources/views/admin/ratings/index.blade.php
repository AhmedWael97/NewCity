@extends('layouts.admin')

@section('title', 'إدارة التقييمات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star"></i> إدارة التقييمات
        </h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة التقييمات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ratings.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="اسم المستخدم، المتجر، التعليق..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label>التقييم</label>
                        <select name="rating" class="form-control">
                            <option value="">جميع التقييمات</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>
                                5 نجوم
                            </option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>
                                4 نجوم
                            </option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>
                                3 نجوم
                            </option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>
                                نجمتان
                            </option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>
                                نجمة واحدة
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>
                                محقق
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                في الانتظار
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                مرفوض
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>نوع التقييم</label>
                        <select name="has_comment" class="form-control">
                            <option value="">الجميع</option>
                            <option value="1" {{ request('has_comment') == '1' ? 'selected' : '' }}>
                                مع تعليق
                            </option>
                            <option value="0" {{ request('has_comment') == '0' ? 'selected' : '' }}>
                                بدون تعليق
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>تاريخ التقييم</label>
                        <select name="date_filter" class="form-control">
                            <option value="">جميع التواريخ</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>
                                اليوم
                            </option>
                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>
                                هذا الأسبوع
                            </option>
                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>
                                هذا الشهر
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

    <!-- Ratings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة التقييمات ({{ $ratings->total() }})</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square"></i> تحديد الكل
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                    <i class="fas fa-square"></i> إلغاء التحديد
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i> حذف المحدد
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>ID</th>
                            <th>المستخدم</th>
                            <th>المتجر</th>
                            <th>التقييم</th>
                            <th>التعليق</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th class="no-sort">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratings as $rating)
                            <tr>
                                <td>
                                    <input type="checkbox" name="rating_ids[]" value="{{ $rating->id }}" class="rating-checkbox">
                                </td>
                                <td>{{ $rating->id }}</td>
                                <td>
                                    @if($rating->user)
                                        <strong>{{ $rating->user->name }}</strong>
                                        <br><small class="text-muted">{{ $rating->user->email }}</small>
                                    @else
                                        <span class="text-muted">مستخدم محذوف</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rating->shop)
                                        <strong>{{ $rating->shop->name }}</strong>
                                        <br><small class="text-muted">{{ $rating->shop->city->name_ar ?? '' }}</small>
                                    @else
                                        <span class="text-muted">متجر محذوف</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <br>
                                        <span class="badge bg-{{ $rating->rating >= 4 ? 'success' : ($rating->rating >= 3 ? 'warning' : 'danger') }}">
                                            {{ $rating->rating }}/5
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($rating->comment)
                                        <div class="comment-preview">
                                            {{ Str::limit($rating->comment, 100) }}
                                            @if(strlen($rating->comment) > 100)
                                                <button class="btn btn-link btn-sm p-0" onclick="showFullComment('{{ $rating->id }}')">
                                                    عرض المزيد
                                                </button>
                                            @endif
                                        </div>
                                        <div id="full-comment-{{ $rating->id }}" class="d-none">
                                            {{ $rating->comment }}
                                        </div>
                                    @else
                                        <span class="text-muted">لا يوجد تعليق</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($rating->status ?? 'pending')
                                        @case('verified')
                                            <span class="badge bg-success text-white">
                                                <i class="fas fa-check"></i> محقق
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger text-white">
                                                <i class="fas fa-times"></i> مرفوض
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock"></i> في الانتظار
                                            </span>
                                    @endswitch
                                </td>
                                <td>
                                    {{ $rating->created_at->format('Y-m-d') }}
                                    <br><small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.ratings.show', $rating) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ratings.edit', $rating) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if($rating->status !== 'verified')
                                            <form method="POST" action="{{ route('admin.ratings.verify', $rating) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="تحقق">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.ratings.destroy', $rating) }}" class="d-inline" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد تقييمات</h5>
                                        <p class="text-muted">لم يتم العثور على تقييمات مطابقة لمعايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($ratings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $ratings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.rating-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.rating-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
}

function bulkDelete() {
    const selectedRatings = document.querySelectorAll('.rating-checkbox:checked');
    if (selectedRatings.length === 0) {
        alert('يرجى تحديد تقييمات للحذف');
        return;
    }
    
    if (confirm(`هل أنت متأكد من حذف ${selectedRatings.length} تقييم؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.ratings.bulk-delete") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        selectedRatings.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rating_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function showFullComment(ratingId) {
    const preview = document.querySelector(`#full-comment-${ratingId}`);
    preview.classList.toggle('d-none');
}

document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.rating-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

// Auto-submit search form on filter change
document.querySelectorAll('select[name="rating"], select[name="status"], select[name="has_comment"], select[name="date_filter"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection
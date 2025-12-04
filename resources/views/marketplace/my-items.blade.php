@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-list"></i> إعلاناتي</h4>
            <a href="{{ route('marketplace.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> إضافة إعلان جديد
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الإعلان</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>المشاهدات</th>
                            <th>الرعاية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->images && count($item->images) > 0)
                                    <img src="{{ $item->images[0] }}" alt="{{ $item->title }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3">
                                    @endif
                                    <div>
                                        <strong>{{ $item->title }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> {{ $item->city->name }} •
                                            <i class="fas fa-tag"></i> {{ $item->category->name }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($item->price, 0) }} جنيه</strong><br>
                                @if($item->is_negotiable)
                                <small class="text-muted">قابل للتفاوض</small>
                                @endif
                            </td>
                            <td>
                                @switch($item->status)
                                    @case('active')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> نشط</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning"><i class="fas fa-clock"></i> قيد المراجعة</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> مرفوض</span>
                                        @break
                                    @case('sold')
                                        <span class="badge bg-secondary"><i class="fas fa-check"></i> مباع</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $item->view_count }}</strong> / {{ $item->max_views + $item->sponsored_views_boost }}
                                    <div class="progress" style="height: 5px;">
                                        @php
                                            $percentage = ($item->view_count / ($item->max_views + $item->sponsored_views_boost)) * 100;
                                        @endphp
                                        <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : 'success') }}" 
                                             style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                </div>
                                @if($item->remainingViews() < 10 && $item->remainingViews() > 0)
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $item->remainingViews() }} متبقية
                                </small>
                                @elseif($item->remainingViews() == 0)
                                <small class="text-danger"><i class="fas fa-eye-slash"></i> نفذت المشاهدات</small>
                                @endif
                            </td>
                            <td>
                                @if($item->is_sponsored && $item->sponsored_until > now())
                                    <span class="badge bg-gradient-primary">
                                        <i class="fas fa-star"></i> مميز
                                    </span><br>
                                    <small class="text-muted">
                                        متبقي {{ now()->diffInDays($item->sponsored_until) }} يوم
                                    </small>
                                @else
                                    <span class="text-muted">غير مميز</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('marketplace.show', $item->slug) }}" class="btn btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->status !== 'sold')
                                    <a href="{{ route('marketplace.edit', $item->slug) }}" class="btn btn-primary" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                   
                                </div>
                                <div class="btn-group btn-group-sm mt-1">
                                    @if($item->status === 'active' && $item->remainingViews() < 20)
                                    <a href="{{ route('marketplace.sponsor', $item->id) }}" class="btn btn-warning btn-sm" title="رعاية">
                                        <i class="fas fa-rocket"></i> رعاية
                                    </a>
                                    @endif
                                    @if($item->status === 'active')
                                    <form action="{{ route('marketplace.mark-sold', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" title="وضع علامة مباع"
                                                onclick="return confirm('هل تريد وضع علامة مباع على هذا الإعلان؟')">
                                            <i class="fas fa-check"></i> مباع
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <form action="{{ route('marketplace.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100" 
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $items->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-5x text-muted mb-3"></i>
                <h4>لا توجد إعلانات</h4>
                <p class="text-muted">ابدأ بإضافة إعلانك الأول</p>
                <a href="{{ route('marketplace.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة إعلان جديد
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $items->where('status', 'active')->count() }}</h3>
                    <p class="text-muted mb-0">إعلانات نشطة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $items->where('status', 'pending')->count() }}</h3>
                    <p class="text-muted mb-0">قيد المراجعة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $items->where('is_sponsored', true)->where('sponsored_until', '>', now())->count() }}</h3>
                    <p class="text-muted mb-0">إعلانات مميزة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $items->sum('view_count') }}</h3>
                    <p class="text-muted mb-0">إجمالي المشاهدات</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-qrcode"></i> رمز QR للإعلان</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h6 id="qrModalTitle" class="mb-3"></h6>
                <img id="qrModalImage" src="" alt="QR Code" class="img-fluid mb-3" style="max-width: 300px;">
                <p class="text-muted small">
                    <i class="fas fa-info-circle"></i> يمكنك مشاركة هذا الرمز ليتمكن الآخرون من الوصول لإعلانك مباشرة
                </p>
            </div>
            <div class="modal-footer">
                <a id="qrDownloadLink" href="" download class="btn btn-primary">
                    <i class="fas fa-download"></i> تحميل رمز QR
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
function showQrModal(itemId, itemTitle) {
    document.getElementById('qrModalTitle').textContent = itemTitle;
    document.getElementById('qrModalImage').src = '/marketplace/' + itemId + '/qr';
    document.getElementById('qrDownloadLink').href = '/marketplace/' + itemId + '/qr/download';
    new bootstrap.Modal(document.getElementById('qrModal')).show();
}
</script>
@endsection

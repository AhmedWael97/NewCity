@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-paper-plane"></i> طلباتي</h2>
    
    <div id="myRequestsContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>
    </div>
</div>

@push('styles')
<style>
.request-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.request-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
}

.status-pending { background: #ffc107; color: #000; }
.status-accepted { background: #28a745; color: white; }
.status-rejected { background: #dc3545; color: white; }
.status-cancelled { background: #6c757d; color: white; }
</style>
@endpush

@push('scripts')
<script>
async function loadMyRequests() {
    try {
        const response = await fetch('/api/v1/tawsela/my-requests', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderRequests(data.data.data);
        }
    } catch (error) {
        console.error('Error loading requests:', error);
        document.getElementById('myRequestsContainer').innerHTML = `
            <div class="alert alert-danger">حدث خطأ في تحميل الطلبات</div>
        `;
    }
}

function renderRequests(requests) {
    const container = document.getElementById('myRequestsContainer');
    
    if (requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4>لم تقم بإرسال أي طلبات بعد</h4>
                <p class="text-muted">ابحث عن رحلة وقم بإرسال طلب للانضمام</p>
                <a href="{{ route('tawsela.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-search"></i> ابحث عن رحلة
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = requests.map(request => `
        <div class="request-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-2">طلب انضمام للرحلة</h5>
                        <p class="text-muted mb-1">
                            <strong>السائق:</strong> ${request.ride.user.name}
                        </p>
                        <p class="text-muted mb-0">
                            <i class="fas fa-route"></i>
                            ${request.ride.start_address} → ${request.ride.destination_address}
                        </p>
                    </div>
                    <span class="status-badge status-${request.status}">
                        ${getStatusText(request.status)}
                    </span>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">نقطة الصعود</small>
                        <div>${request.pickup_address}</div>
                    </div>
                    ${request.dropoff_address ? `
                        <div class="col-md-6">
                            <small class="text-muted">نقطة النزول</small>
                            <div>${request.dropoff_address}</div>
                        </div>
                    ` : ''}
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">عدد الركاب</small>
                        <div>${request.passengers_count}</div>
                    </div>
                    ${request.offered_price ? `
                        <div class="col-md-4">
                            <small class="text-muted">السعر المعروض</small>
                            <div>${request.offered_price} جنيه</div>
                        </div>
                    ` : ''}
                    <div class="col-md-4">
                        <small class="text-muted">تاريخ الطلب</small>
                        <div>${new Date(request.created_at).toLocaleDateString('ar-EG')}</div>
                    </div>
                </div>
                
                ${request.message ? `
                    <div class="alert alert-light mb-3">
                        <strong>رسالتك:</strong><br>
                        ${request.message}
                    </div>
                ` : ''}
                
                <div class="d-flex gap-2">
                    <a href="/tawsela/${request.ride_id}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> عرض الرحلة
                    </a>
                    ${request.status === 'accepted' ? `
                        <a href="tel:${request.ride.user.phone}" class="btn btn-sm btn-success">
                            <i class="fas fa-phone"></i> اتصل بالسائق
                        </a>
                        <a href="/tawsela/messages?ride_id=${request.ride_id}&user_id=${request.ride.user_id}" class="btn btn-sm btn-info">
                            <i class="fas fa-comments"></i> المراسلة
                        </a>
                    ` : ''}
                    ${request.status === 'pending' ? `
                        <button onclick="cancelRequest(${request.id})" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i> إلغاء الطلب
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'قيد الانتظار',
        'accepted': 'مقبول',
        'rejected': 'مرفوض',
        'cancelled': 'ملغي'
    };
    return statusMap[status] || status;
}

async function cancelRequest(requestId) {
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) return;
    
    try {
        const response = await fetch(`/api/v1/tawsela/requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('تم إلغاء الطلب بنجاح');
            loadMyRequests();
        }
    } catch (error) {
        console.error('Error cancelling request:', error);
        alert('حدث خطأ في إلغاء الطلب');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadMyRequests();
});
</script>
@endpush
@endsection

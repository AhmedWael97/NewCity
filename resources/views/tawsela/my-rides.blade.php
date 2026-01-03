@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-car"></i> رحلاتي</h2>
        <a href="{{ route('fe-tare2k.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> إضافة رحلة جديدة
        </a>
    </div>
    
    <div id="myRidesContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>
    </div>
</div>

@push('styles')
<style>
.ride-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.ride-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
}

.status-active { background: #28a745; color: white; }
.status-pending { background: #ffc107; color: #000; }
.status-completed { background: #6c757d; color: white; }
.status-cancelled { background: #dc3545; color: white; }
</style>
@endpush

@push('scripts')
<script>
async function loadMyRides() {
    try {
        const response = await fetch('/api/v1/fe-tare2k/my-rides', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderRides(data.data.data);
        }
    } catch (error) {
        console.error('Error loading rides:', error);
        document.getElementById('myRidesContainer').innerHTML = `
            <div class="alert alert-danger">حدث خطأ في تحميل الرحلات</div>
        `;
    }
}

function renderRides(rides) {
    const container = document.getElementById('myRidesContainer');
    
    if (rides.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                <h4>لم تقم بإضافة أي رحلات بعد</h4>
                <p class="text-muted">ابدأ بإضافة رحلتك الأولى</p>
                <a href="{{ route('fe-tare2k.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle"></i> إضافة رحلة
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = rides.map(ride => `
        <div class="ride-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-2">
                            ${ride.start_address} → ${ride.destination_address}
                        </h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-clock"></i>
                            ${new Date(ride.departure_time).toLocaleString('ar-EG')}
                        </p>
                    </div>
                    <span class="status-badge status-${ride.status}">
                        ${getStatusText(ride.status)}
                    </span>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <small class="text-muted">السيارة</small>
                        <div>${ride.car_model}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">المقاعد المتاحة</small>
                        <div class="text-success fw-bold">${ride.remaining_seats} / ${ride.available_seats}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">السعر</small>
                        <div>${ride.price} جنيه ${ride.price_unit === 'per_person' ? 'للشخص' : 'للرحلة'}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">الطلبات</small>
                        <div>${ride.requests_count} طلب</div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="/fe-tare2k/${ride.id}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <button onclick="viewRequests(${ride.id})" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-users"></i> الطلبات (${ride.requests_count})
                    </button>
                    ${ride.status === 'active' ? `
                        <button onclick="editRide(${ride.id})" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </button>
                        <button onclick="cancelRide(${ride.id})" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function getStatusText(status) {
    const statusMap = {
        'active': 'نشط',
        'pending': 'قيد الانتظار',
        'completed': 'مكتمل',
        'cancelled': 'ملغي'
    };
    return statusMap[status] || status;
}

async function viewRequests(rideId) {
    window.location.href = `/fe-tare2k/rides/${rideId}/requests`;
}

async function cancelRide(rideId) {
    if (!confirm('هل أنت متأكد من إلغاء هذه الرحلة؟')) return;
    
    try {
        const response = await fetch(`/api/v1/fe-tare2k/rides/${rideId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: 'cancelled' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('تم إلغاء الرحلة بنجاح');
            loadMyRides();
        }
    } catch (error) {
        console.error('Error cancelling ride:', error);
        alert('حدث خطأ في إلغاء الرحلة');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadMyRides();
});
</script>
@endpush
@endsection

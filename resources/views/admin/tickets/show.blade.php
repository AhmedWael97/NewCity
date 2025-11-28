@extends('layouts.admin')

@section('title', 'تفاصيل التذكرة #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">تذكرة دعم #{{ $ticket->ticket_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">التذاكر</a></li>
                    <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Ticket Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        <div class="d-flex gap-2">
                            {!! $ticket->status_badge !!}
                            {!! $ticket->priority_badge !!}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="ticket-meta mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">الفئة</small>
                                <strong>{{ $ticket->category_name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">تاريخ الإنشاء</small>
                                <strong>{{ $ticket->created_at->format('Y-m-d H:i') }}</strong>
                            </div>
                            @if($ticket->city)
                            <div class="col-md-6">
                                <small class="text-muted d-block">المدينة</small>
                                <strong>{{ $ticket->city->name }}</strong>
                            </div>
                            @endif
                            @if($ticket->shop)
                            <div class="col-md-6">
                                <small class="text-muted d-block">المتجر المتعلق</small>
                                <strong>
                                    <a href="{{ route('admin.shops.edit', $ticket->shop_id) }}" target="_blank">
                                        {{ $ticket->shop->name }}
                                    </a>
                                </strong>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="ticket-description">
                        <h6 class="mb-3">وصف المشكلة:</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $ticket->description }}
                        </div>
                    </div>

                    @if($ticket->attachments && count($ticket->attachments) > 0)
                    <div class="ticket-attachments mt-4">
                        <h6 class="mb-3">المرفقات:</h6>
                        <div class="row g-2">
                            @foreach($ticket->attachments as $attachment)
                            <div class="col-md-4">
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-file me-2"></i>
                                    {{ $attachment['name'] }}
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Replies Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">الردود والملاحظات</h5>
                </div>
                <div class="card-body">
                    @if($ticket->replies && $ticket->replies->count() > 0)
                        @foreach($ticket->replies as $reply)
                        <div class="reply-item mb-3 p-3 {{ $reply->is_internal_note ? 'bg-warning bg-opacity-10' : 'bg-light' }} rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2">
                                        {{ substr($reply->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $reply->user->name ?? 'Admin' }}</strong>
                                        @if($reply->is_internal_note)
                                            <span class="badge bg-warning ms-2">ملاحظة داخلية</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="reply-content">
                                {{ $reply->message }}
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">لا توجد ردود بعد</p>
                    @endif

                    <!-- Add Reply Form -->
                    <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">إضافة رد</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_internal_note" name="is_internal_note" value="1">
                            <label class="form-check-label" for="is_internal_note">
                                ملاحظة داخلية (لن يراها المستخدم)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            إرسال الرد
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- User Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">معلومات المستخدم</h6>
                </div>
                <div class="card-body">
                    @if($ticket->user)
                        <div class="text-center mb-3">
                            <div class="avatar-circle-lg bg-primary text-white mx-auto mb-2">
                                {{ substr($ticket->user->name, 0, 1) }}
                            </div>
                            <h6>{{ $ticket->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $ticket->user->email }}</p>
                        </div>
                        <hr>
                        <div class="user-stats">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">التذاكر السابقة:</span>
                                <strong>{{ $ticket->user->supportTickets()->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">عضو منذ:</span>
                                <strong>{{ $ticket->user->created_at->diffForHumans() }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>تذكرة من زائر (غير مسجل)</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">إجراءات</h6>
                </div>
                <div class="card-body">
                    <!-- Update Status -->
                    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <label for="status" class="form-label">تغيير الحالة</label>
                        <select class="form-select mb-2" id="status" name="status">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="waiting_user" {{ $ticket->status == 'waiting_user' ? 'selected' : '' }}>في انتظار المستخدم</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>تم الحل</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>مغلق</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-check me-2"></i>
                            تحديث الحالة
                        </button>
                    </form>

                    <hr>

                    <!-- Assign To -->
                    <form action="{{ route('admin.tickets.assign', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <label for="assigned_admin_id" class="form-label">تعيين لـ</label>
                        <select class="form-select mb-2" id="assigned_admin_id" name="assigned_admin_id">
                            <option value="">غير معين</option>
                            @foreach(\App\Models\User::where('user_type', 'admin')->get() as $admin)
                                <option value="{{ $admin->id }}" {{ $ticket->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary w-100">
                            <i class="fas fa-user-check me-2"></i>
                            تعيين
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">السجل الزمني</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <div>
                                <strong>تم الإنشاء</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                        @if($ticket->assigned_admin_id)
                        <div class="timeline-item">
                            <i class="fas fa-user-check text-info"></i>
                            <div>
                                <strong>تم التعيين</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->assignedTo->name ?? 'Admin' }}</small>
                            </div>
                        </div>
                        @endif
                        @if($ticket->resolved_at)
                        <div class="timeline-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>تم الحل</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->resolved_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                        @endif
                        @if($ticket->closed_at)
                        <div class="timeline-item">
                            <i class="fas fa-times-circle text-secondary"></i>
                            <div>
                                <strong>تم الإغلاق</strong>
                                <br>
                                <small class="text-muted">{{ $ticket->closed_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.avatar-circle-lg {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
}

.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    align-items: start;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.timeline-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.timeline-item i {
    font-size: 1.2rem;
    margin-top: 2px;
}

.reply-item {
    border-left: 3px solid #0d6efd;
}
</style>
@endsection

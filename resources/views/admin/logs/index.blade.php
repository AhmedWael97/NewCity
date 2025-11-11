@extends('layouts.admin')

@section('title', 'سجلات النظام')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt"></i> سجلات النظام
        </h1>
    </div>

    <div class="row">
        <!-- Log Files -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ملفات السجلات</h6>
                </div>
                <div class="card-body">
                    @if(count($logFiles) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($logFiles as $file)
                                <a href="{{ route('admin.logs.index', ['log' => $file['name']]) }}" 
                                   class="list-group-item list-group-item-action {{ $selectedLog === $file['name'] ? 'active' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $file['name'] }}</h6>
                                        <small>{{ number_format($file['size'] / 1024, 2) }} KB</small>
                                    </div>
                                    <small class="text-muted">
                                        آخر تعديل: {{ date('Y-m-d H:i', $file['modified']) }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد ملفات سجلات</h5>
                        </div>
                    @endif
                </div>
            </div>

            @if($selectedLog)
                <!-- Log Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">إجراءات الملف</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.logs.download', ['file' => $selectedLog]) }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> تحميل الملف
                            </a>
                            
                            <form method="POST" action="{{ route('admin.logs.clear') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="file" value="{{ $selectedLog }}">
                                <button type="submit" class="btn btn-warning btn-sm w-100"
                                        onclick="return confirm('هل أنت متأكد من مسح محتوى هذا الملف؟')">
                                    <i class="fas fa-eraser"></i> مسح المحتوى
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.logs.delete') }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="file" value="{{ $selectedLog }}">
                                <button type="submit" class="btn btn-danger btn-sm w-100"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا الملف نهائياً؟')">
                                    <i class="fas fa-trash"></i> حذف الملف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Log Content -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        محتوى السجل: {{ $selectedLog ?: 'لم يتم اختيار ملف' }}
                    </h6>
                    @if($selectedLog)
                        <small class="text-muted">آخر {{ count($logLines) }} سطر</small>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedLog && count($logLines) > 0)
                        <div class="log-content" style="max-height: 600px; overflow-y: auto;">
                            @foreach($logLines as $index => $line)
                                @if(trim($line))
                                    @php
                                        $logLevel = 'info';
                                        $logClass = 'bg-light';
                                        $logColor = '#6c757d';
                                        
                                        if (str_contains($line, '[ERROR]') || str_contains($line, 'ERROR:')) {
                                            $logLevel = 'error';
                                            $logClass = 'bg-danger-light border-danger';
                                            $logColor = '#dc3545';
                                        } elseif (str_contains($line, '[WARNING]') || str_contains($line, 'WARNING:')) {
                                            $logLevel = 'warning';
                                            $logClass = 'bg-warning-light border-warning';
                                            $logColor = '#ffc107';
                                        } elseif (str_contains($line, '[DEBUG]') || str_contains($line, 'DEBUG:')) {
                                            $logLevel = 'debug';
                                            $logClass = 'bg-secondary-light border-secondary';
                                            $logColor = '#6c757d';
                                        } elseif (str_contains($line, '[INFO]') || str_contains($line, 'INFO:')) {
                                            $logLevel = 'info';
                                            $logClass = 'bg-info-light border-info';
                                            $logColor = '#17a2b8';
                                        }
                                    @endphp
                                    <div class="log-line mb-2 p-2 rounded {{ $logClass }}" 
                                         style="font-family: 'Courier New', monospace; font-size: 0.85rem; border-left: 3px solid {{ $logColor }};">
                                        <small class="text-muted">#{{ count($logLines) - $index }}</small>
                                        <span class="log-content">{{ $line }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-secondary btn-sm" onclick="refreshLogs()">
                                <i class="fas fa-sync-alt"></i> تحديث
                            </button>
                            <button class="btn btn-info btn-sm" onclick="scrollToTop()">
                                <i class="fas fa-arrow-up"></i> أعلى الصفحة
                            </button>
                            <button class="btn btn-info btn-sm" onclick="scrollToBottom()">
                                <i class="fas fa-arrow-down"></i> أسفل الصفحة
                            </button>
                        </div>
                    @elseif($selectedLog)
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">الملف فارغ</h5>
                            <p class="text-muted">لا يحتوي هذا الملف على أي سجلات</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">اختر ملف سجل</h5>
                            <p class="text-muted">اختر ملف سجل من القائمة الجانبية لعرض محتواه</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.log-content {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.log-line {
    word-wrap: break-word;
    white-space: pre-wrap;
}

.log-error {
    background-color: #f8d7da;
    color: #721c24;
}

.log-warning {
    background-color: #fff3cd;
    color: #856404;
}

.log-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.log-debug {
    background-color: #e2e3e5;
    color: #383d41;
}

.log-default {
    background-color: #ffffff;
    color: #495057;
}
</style>

<script>
function refreshLogs() {
    location.reload();
}

function scrollToTop() {
    document.querySelector('.log-content').scrollTop = 0;
}

function scrollToBottom() {
    const logContent = document.querySelector('.log-content');
    logContent.scrollTop = logContent.scrollHeight;
}

// Auto-scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    const logContent = document.querySelector('.log-content');
    if (logContent) {
        logContent.scrollTop = logContent.scrollHeight;
    }
});
</script>

@push('styles')
<style>
.bg-danger-light {
    background-color: #f8d7da !important;
}
.bg-warning-light {
    background-color: #fff3cd !important;
}
.bg-info-light {
    background-color: #d1ecf1 !important;
}
.bg-secondary-light {
    background-color: #e2e3e5 !important;
}
.log-line {
    word-wrap: break-word;
    word-break: break-all;
}
.log-content {
    font-family: 'Courier New', Monaco, monospace;
}
</style>
@endpush
@endsection
@extends('layouts.admin')

@section('title', 'SMTP Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إعدادات SMTP</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- SMTP Configuration Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope"></i> إعدادات خادم البريد الإلكتروني
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.smtp.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="host">خادم SMTP *</label>
                            <input type="text" 
                                   class="form-control @error('host') is-invalid @enderror" 
                                   id="host" 
                                   name="host" 
                                   value="{{ old('host', $settings?->host ?? 'smtp.gmail.com') }}"
                                   placeholder="smtp.gmail.com" 
                                   required>
                            @error('host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">مثال: smtp.gmail.com, smtp.office365.com</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="port">المنفذ *</label>
                                    <input type="number" 
                                           class="form-control @error('port') is-invalid @enderror" 
                                           id="port" 
                                           name="port" 
                                           value="{{ old('port', $settings?->port ?? 587) }}"
                                           required>
                                    @error('port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">587 (TLS), 465 (SSL), 25 (None)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="encryption">التشفير *</label>
                                    <select class="form-control @error('encryption') is-invalid @enderror" 
                                            id="encryption" 
                                            name="encryption" 
                                            required>
                                        <option value="tls" {{ old('encryption', $settings?->encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('encryption', $settings?->encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ old('encryption', $settings?->encryption) == null ? 'selected' : '' }}>None</option>
                                    </select>
                                    @error('encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username">اسم المستخدم *</label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $settings?->username ?? '') }}"
                                   placeholder="your-email@example.com" 
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">كلمة المرور *</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="********" 
                                   {{ $settings ? '' : 'required' }}>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($settings)
                                <small class="form-text text-muted">اتركه فارغاً للاحتفاظ بكلمة المرور الحالية</small>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="from_address">عنوان البريد الإلكتروني للمرسل *</label>
                            <input type="email" 
                                   class="form-control @error('from_address') is-invalid @enderror" 
                                   id="from_address" 
                                   name="from_address" 
                                   value="{{ old('from_address', $settings?->from_address ?? '') }}"
                                   placeholder="noreply@example.com" 
                                   required>
                            @error('from_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="from_name">اسم المرسل *</label>
                            <input type="text" 
                                   class="form-control @error('from_name') is-invalid @enderror" 
                                   id="from_name" 
                                   name="from_name" 
                                   value="{{ old('from_name', $settings?->from_name ?? 'City App') }}"
                                   placeholder="City App" 
                                   required>
                            @error('from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="notification_emails">رسائل الإشعارات (البريد الإلكتروني) *</label>
                            <textarea class="form-control @error('notification_emails') is-invalid @enderror" 
                                      id="notification_emails" 
                                      name="notification_emails" 
                                      rows="3"
                                      placeholder="admin@example.com&#10;manager@example.com&#10;supervisor@example.com"
                                      required>{{ old('notification_emails', $settings?->notification_emails ? implode("\n", $settings->notification_emails) : '') }}</textarea>
                            @error('notification_emails')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">أدخل بريد إلكتروني واحد في كل سطر. سيتم إرسال جميع الإشعارات إلى هذه العناوين.</small>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Test Email Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-paper-plane"></i> اختبار البريد الإلكتروني
                    </h6>
                </div>
                <div class="card-body">
                    @if($settings)
                        <form id="test-email-form">
                            @csrf
                            <div class="form-group">
                                <label for="test_email">البريد الإلكتروني للاختبار</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="test_email" 
                                       name="test_email" 
                                       placeholder="test@example.com" 
                                       required>
                            </div>
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="fas fa-paper-plane"></i> إرسال بريد تجريبي
                            </button>
                        </form>

                        <div id="test-result" class="mt-3" style="display: none;">
                            <div class="alert" role="alert"></div>
                        </div>

                        @if($settings?->last_tested_at)
                            <hr>
                            <div class="small">
                                <p class="mb-1">
                                    <strong>آخر اختبار:</strong><br>
                                    {{ $settings->last_tested_at->format('Y-m-d H:i:s') }}
                                </p>
                                <p class="mb-0">
                                    <strong>النتيجة:</strong><br>
                                    @if($settings->test_successful)
                                        <span class="badge badge-success">نجح</span>
                                    @else
                                        <span class="badge badge-danger">فشل</span>
                                        @if($settings->test_error)
                                            <br><small class="text-danger">{{ $settings->test_error }}</small>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle"></i> 
                            يرجى حفظ إعدادات SMTP أولاً قبل إرسال بريد تجريبي.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-question-circle"></i> إعدادات شائعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Gmail:</strong>
                        <ul class="small mb-0">
                            <li>Host: smtp.gmail.com</li>
                            <li>Port: 587</li>
                            <li>Encryption: TLS</li>
                            <li>ملاحظة: استخدم App Password</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <strong>Outlook/Office365:</strong>
                        <ul class="small mb-0">
                            <li>Host: smtp.office365.com</li>
                            <li>Port: 587</li>
                            <li>Encryption: TLS</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Yahoo:</strong>
                        <ul class="small mb-0">
                            <li>Host: smtp.mail.yahoo.com</li>
                            <li>Port: 587</li>
                            <li>Encryption: TLS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($settings)
<script>
const testForm = document.getElementById('test-email-form');
if (testForm) {
    testForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        
        const testEmail = document.getElementById('test_email').value;
        const resultDiv = document.getElementById('test-result');
        const alertDiv = resultDiv.querySelector('.alert');
        
        fetch('{{ route("admin.smtp.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ test_email: testEmail })
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.style.display = 'block';
            if (data.success) {
                alertDiv.className = 'alert alert-success';
                alertDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            } else {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
        })
        .catch(error => {
            resultDiv.style.display = 'block';
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ: ' + error.message;
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
}
</script>
@endif
@endsection

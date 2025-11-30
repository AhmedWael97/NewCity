@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-qrcode"></i> مولد رمز QR</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> أدخل أي نص أو رابط لإنشاء رمز QR له. يمكنك استخدام هذا لإنشاء أكواد QR لروابط الموقع، معلومات الاتصال، أو أي نص آخر.
                    </p>
                    
                    <div class="row">
                        <!-- Input Form -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">إدخال البيانات</h6>
                                </div>
                                <div class="card-body">
                                    <form id="qrGeneratorForm">
                                        <div class="mb-3">
                                            <label for="content" class="form-label">النص أو الرابط <span class="text-danger">*</span></label>
                                            <textarea 
                                                class="form-control" 
                                                id="content" 
                                                name="content" 
                                                rows="5" 
                                                placeholder="أدخل الرابط أو النص هنا..."
                                                required></textarea>
                                            <small class="text-muted">مثال: https://example.com أو أي نص تريده</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="size" class="form-label">حجم رمز QR (بكسل)</label>
                                            <input 
                                                type="range" 
                                                class="form-range" 
                                                id="size" 
                                                name="size" 
                                                min="100" 
                                                max="1000" 
                                                step="50" 
                                                value="300">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">100px</small>
                                                <small class="text-muted"><strong id="sizeValue">300</strong>px</small>
                                                <small class="text-muted">1000px</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">قوالب سريعة</label>
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillTemplate('{{ url('/') }}')">
                                                    <i class="fas fa-home"></i> الصفحة الرئيسية
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillTemplate('{{ url('/select-city') }}')">
                                                    <i class="fas fa-map-marker-alt"></i> اختيار المدينة
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillTemplate('{{ url('/marketplace') }}')">
                                                    <i class="fas fa-shopping-cart"></i> السوق المفتوح
                                                </button>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-magic"></i> إنشاء رمز QR
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                                <i class="fas fa-eraser"></i> مسح النموذج
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Display -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">رمز QR</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div id="qrPlaceholder" class="p-5">
                                        <i class="fas fa-qrcode fa-5x text-muted mb-3"></i>
                                        <p class="text-muted">سيظهر رمز QR هنا بعد إنشائه</p>
                                    </div>
                                    
                                    <div id="qrResult" class="d-none">
                                        <div class="mb-3">
                                            <img id="qrImage" src="" alt="QR Code" class="img-fluid" style="max-width: 400px; border: 2px solid #ddd; padding: 10px; border-radius: 8px;">
                                        </div>
                                        
                                        <div class="alert alert-info" id="qrContent">
                                            <strong><i class="fas fa-info-circle"></i> المحتوى:</strong>
                                            <p class="mb-0 mt-2" id="contentDisplay" style="word-break: break-all;"></p>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success btn-lg" onclick="downloadQR()">
                                                <i class="fas fa-download"></i> تحميل رمز QR
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" onclick="copyToClipboard()">
                                                <i class="fas fa-copy"></i> نسخ الصورة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Examples -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> أمثلة استخدام رمز QR</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h6><i class="fas fa-link text-primary"></i> روابط المواقع</h6>
                                <p class="small text-muted mb-0">
                                    استخدم رمز QR لتوجيه المستخدمين مباشرة إلى صفحات معينة في موقعك
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h6><i class="fas fa-mobile-alt text-success"></i> معلومات الاتصال</h6>
                                <p class="small text-muted mb-0">
                                    أنشئ رمز QR يحتوي على رقم هاتف أو بريد إلكتروني للتواصل السريع
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h6><i class="fas fa-wifi text-warning"></i> شبكات WiFi</h6>
                                <p class="small text-muted mb-0">
                                    استخدم التنسيق: WIFI:T:WPA;S:network_name;P:password;;
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#qrImage {
    transition: all 0.3s ease;
}

#qrImage:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.form-range::-webkit-slider-thumb {
    background: #0d6efd;
}

.form-range::-moz-range-thumb {
    background: #0d6efd;
}
</style>

<script>
// Update size display
document.getElementById('size').addEventListener('input', function() {
    document.getElementById('sizeValue').textContent = this.value;
});

// Handle form submission
document.getElementById('qrGeneratorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const content = document.getElementById('content').value;
    const size = document.getElementById('size').value;
    
    if (!content.trim()) {
        alert('الرجاء إدخال نص أو رابط');
        return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإنشاء...';
    submitBtn.disabled = true;
    
    // Generate QR code
    fetch('{{ route("admin.qr-generator.generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            content: content,
            size: size
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug log
        
        if (data.success && data.qrCode) {
            // Hide placeholder and show result
            document.getElementById('qrPlaceholder').classList.add('d-none');
            document.getElementById('qrResult').classList.remove('d-none');
            
            // Update QR image - SVG can be used directly in img src
            document.getElementById('qrImage').src = data.qrCode;
            document.getElementById('contentDisplay').textContent = data.content;
            
            // Store data for download
            window.qrData = {
                content: data.content,
                size: data.size
            };
        } else {
            console.error('Invalid response:', data);
            alert('حدث خطأ أثناء إنشاء رمز QR: استجابة غير صحيحة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إنشاء رمز QR: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Fill template
function fillTemplate(url) {
    document.getElementById('content').value = url;
}

// Clear form
function clearForm() {
    document.getElementById('qrGeneratorForm').reset();
    document.getElementById('sizeValue').textContent = '300';
    document.getElementById('qrPlaceholder').classList.remove('d-none');
    document.getElementById('qrResult').classList.add('d-none');
}

// Download QR code
function downloadQR() {
    if (!window.qrData) {
        alert('الرجاء إنشاء رمز QR أولاً');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.qr-generator.download") }}';
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'content';
    contentInput.value = window.qrData.content;
    form.appendChild(contentInput);
    
    const sizeInput = document.createElement('input');
    sizeInput.type = 'hidden';
    sizeInput.name = 'size';
    sizeInput.value = 500; // Higher resolution for download
    form.appendChild(sizeInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Copy image to clipboard
function copyToClipboard() {
    const imgSrc = document.getElementById('qrImage').src;
    
    // For SVG data URI, we need to convert to blob first
    if (imgSrc.startsWith('data:image/svg+xml')) {
        // Decode base64
        const base64Data = imgSrc.split(',')[1];
        const svgData = atob(base64Data);
        const blob = new Blob([svgData], { type: 'image/svg+xml' });
        
        // Try to copy as text (SVG)
        navigator.clipboard.writeText(svgData).then(function() {
            // Show success message
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> تم نسخ SVG!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(function(error) {
            console.error('Error copying to clipboard:', error);
            alert('فشل النسخ. يمكنك تحميل الصورة بدلاً من ذلك.');
        });
    } else {
        alert('فشل النسخ. يرجى استخدام زر التحميل.');
    }
}
</script>
@endsection

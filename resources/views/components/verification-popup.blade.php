<!-- User Verification Popup -->
<div id="verificationPopup" class="verification-popup" style="display: none;">
    <div class="verification-overlay"></div>
    <div class="verification-modal">
        <div class="verification-header">
            <div class="verification-icon">
                <i class="fas fa-shield-check"></i>
            </div>
            <h3>مرحباً بك! 👋</h3>
            <p>للتأكد من أنك زائر حقيقي، الرجاء كتابة أي شيء</p>
        </div>

        <form id="verificationForm" class="verification-form">
            @csrf
            <div class="form-group">
                <label for="verificationMessage">
                    <i class="fas fa-comment-dots"></i>
                    اكتب أي شيء للمتابعة <span class="required">*</span>
                </label>
                <textarea 
                    id="verificationMessage" 
                    name="message" 
                    class="form-control" 
                    rows="3" 
                    placeholder="مثال: مرحبا، أريد استكشاف الموقع..."
                    required
                    minlength="3"
                    maxlength="500"></textarea>
                <small class="char-counter">
                    <span id="charCount">0</span>/500 حرف
                </small>
                <div class="error-message" id="messageError"></div>
            </div>

            <div class="form-group optional-section">
                <div class="optional-header" onclick="toggleOptionalFields()">
                    <i class="fas fa-chevron-down"></i>
                    <span>معلومات إضافية (اختياري)</span>
                </div>
                <div id="optionalFields" style="display: none;">
                    <div class="form-group">
                        <label for="verificationName">
                            <i class="fas fa-user"></i>
                            الاسم (اختياري)
                        </label>
                        <input 
                            type="text" 
                            id="verificationName" 
                            name="name" 
                            class="form-control" 
                            placeholder="اسمك"
                            maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="verificationEmail">
                            <i class="fas fa-envelope"></i>
                            البريد الإلكتروني (اختياري)
                        </label>
                        <input 
                            type="email" 
                            id="verificationEmail" 
                            name="email" 
                            class="form-control" 
                            placeholder="email@example.com"
                            maxlength="255">
                    </div>
                </div>
            </div>

            <div class="verification-info">
                <i class="fas fa-info-circle"></i>
                <small>نستخدم هذا للتحقق من الزوار الحقيقيين ومنع البوتات. معلوماتك آمنة معنا.</small>
            </div>

            <button type="submit" class="btn-verify" id="submitBtn">
                <i class="fas fa-check-circle"></i>
                تحقق ومتابعة
            </button>

            <div class="verification-loading" id="verificationLoading" style="display: none;">
                <div class="spinner"></div>
                <span>جاري التحقق...</span>
            </div>
        </form>

        <div class="verification-footer">
            <i class="fas fa-lock"></i>
            <small>معلوماتك محمية وآمنة</small>
        </div>
    </div>
</div>

<style>
.verification-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-in-out;
}

.verification-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(5px);
}

.verification-modal {
    position: relative;
    background: #fff;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease-out;
}

.verification-header {
    text-align: center;
    padding: 30px 30px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
}

.verification-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 15px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 35px;
}

.verification-header h3 {
    margin: 0 0 10px;
    font-size: 24px;
    font-weight: bold;
}

.verification-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 15px;
}

.verification-form {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group label i {
    margin-left: 5px;
    color: #667eea;
}

.required {
    color: #e74c3c;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.char-counter {
    display: block;
    text-align: left;
    margin-top: 5px;
    color: #999;
    font-size: 12px;
}

.error-message {
    color: #e74c3c;
    font-size: 13px;
    margin-top: 5px;
    display: none;
}

.optional-section {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
}

.optional-header {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #667eea;
    user-select: none;
}

.optional-header i {
    transition: transform 0.3s;
}

.optional-header.active i {
    transform: rotate(180deg);
}

#optionalFields {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.verification-info {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: start;
    gap: 10px;
}

.verification-info i {
    color: #667eea;
    margin-top: 2px;
}

.verification-info small {
    color: #666;
    line-height: 1.5;
    font-size: 12px;
}

.btn-verify {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-verify:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-verify:active {
    transform: translateY(0);
}

.btn-verify:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.verification-loading {
    text-align: center;
    margin-top: 20px;
    color: #667eea;
    font-weight: 600;
}

.spinner {
    width: 40px;
    height: 40px;
    margin: 0 auto 10px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.verification-footer {
    text-align: center;
    padding: 15px 30px 25px;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.verification-footer i {
    color: #667eea;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 576px) {
    .verification-modal {
        width: 95%;
        max-height: 95vh;
    }

    .verification-header {
        padding: 25px 20px 15px;
    }

    .verification-icon {
        width: 60px;
        height: 60px;
        font-size: 30px;
    }

    .verification-header h3 {
        font-size: 20px;
    }

    .verification-form {
        padding: 20px;
    }
}
</style>

<script>
// Verification Popup Script
(function() {
    const VERIFICATION_DELAY = 2000; // 2 seconds
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Check if verification is needed
    function checkVerification() {
        fetch('/api/verification/check', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.needs_verification) {
                setTimeout(() => {
                    showVerificationPopup();
                }, VERIFICATION_DELAY);
            }
        })
        .catch(error => {
            console.error('Verification check error:', error);
        });
    }

    // Show popup
    function showVerificationPopup() {
        const popup = document.getElementById('verificationPopup');
        if (popup) {
            popup.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Focus on message field
            setTimeout(() => {
                document.getElementById('verificationMessage')?.focus();
            }, 400);
        }
    }

    // Hide popup
    function hideVerificationPopup() {
        const popup = document.getElementById('verificationPopup');
        if (popup) {
            popup.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Toggle optional fields
    window.toggleOptionalFields = function() {
        const fields = document.getElementById('optionalFields');
        const header = document.querySelector('.optional-header');
        
        if (fields.style.display === 'none') {
            fields.style.display = 'block';
            header.classList.add('active');
        } else {
            fields.style.display = 'none';
            header.classList.remove('active');
        }
    };

    // Character counter
    const messageField = document.getElementById('verificationMessage');
    if (messageField) {
        messageField.addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('charCount').textContent = count;
            
            if (count >= 500) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
    }

    // Form submission
    const form = document.getElementById('verificationForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('verificationLoading');
            const messageError = document.getElementById('messageError');

            // Clear previous errors
            messageError.style.display = 'none';
            messageError.textContent = '';

            // Get form data
            const formData = new FormData(this);
            const message = formData.get('message')?.trim();

            // Validate
            if (!message || message.length < 3) {
                messageError.textContent = 'الرجاء كتابة رسالة مكونة من 3 أحرف على الأقل';
                messageError.style.display = 'block';
                return;
            }

            // Show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';

            try {
                const response = await fetch('/api/verification/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({
                        message: formData.get('message'),
                        email: formData.get('email'),
                        name: formData.get('name'),
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> تم التحقق بنجاح!';
                    submitBtn.style.background = '#27ae60';
                    
                    // Hide popup after 1 second
                    setTimeout(() => {
                        hideVerificationPopup();
                        
                        // Optional: Show thank you notification
                        showThankYouNotification();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'حدث خطأ');
                }
            } catch (error) {
                messageError.textContent = error.message || 'حدث خطأ. الرجاء المحاولة مرة أخرى';
                messageError.style.display = 'block';
                submitBtn.disabled = false;
                loading.style.display = 'none';
            }
        });
    }

    // Thank you notification
    function showThankYouNotification() {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 999999;
            animation: slideInRight 0.5s ease-out;
        `;
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <div>
                    <strong style="display: block;">شكراً لك! 🎉</strong>
                    <small>تم التحقق بنجاح. استمتع بتصفح الموقع</small>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease-in';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    // Prevent closing popup by clicking outside or ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const popup = document.getElementById('verificationPopup');
            if (popup && popup.style.display === 'flex') {
                e.preventDefault();
                // Shake animation to indicate it can't be closed
                popup.querySelector('.verification-modal').style.animation = 'shake 0.5s';
            }
        }
    });

    // Prevent closing by clicking overlay
    const overlay = document.querySelector('.verification-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            // Shake animation
            const modal = document.querySelector('.verification-modal');
            modal.style.animation = 'shake 0.5s';
            setTimeout(() => {
                modal.style.animation = '';
            }, 500);
        });
    }

    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        @keyframes slideInRight {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Start verification check on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkVerification);
    } else {
        checkVerification();
    }
})();
</script>

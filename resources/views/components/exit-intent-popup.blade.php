<!-- Exit Intent Popup Component -->
<style>
    #exitIntentModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }

    #exitIntentModal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .exit-modal-content {
        background: white;
        border-radius: 20px;
        max-width: 500px;
        width: 90%;
        position: relative;
        animation: slideDown 0.4s ease;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .exit-modal-header {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .exit-modal-header h2 {
        font-size: 28px;
        margin: 0 0 10px 0;
        font-weight: bold;
    }

    .exit-modal-header p {
        font-size: 16px;
        margin: 0;
        opacity: 0.9;
    }

    .exit-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.3);
        border: none;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .exit-modal-close:hover {
        background: rgba(255,255,255,0.5);
        transform: rotate(90deg);
    }

    .exit-modal-body {
        padding: 40px 30px;
        text-align: center;
    }

    .exit-offer-badge {
        display: inline-block;
        background: #ffd93d;
        color: #2c3e50;
        padding: 10px 25px;
        border-radius: 50px;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(255, 217, 61, 0.4);
    }

    .exit-modal-body h3 {
        font-size: 22px;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .exit-modal-body p {
        color: #7f8c8d;
        font-size: 15px;
        margin-bottom: 25px;
    }

    .exit-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .exit-form input {
        flex: 1;
        padding: 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .exit-form input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .exit-form button {
        padding: 15px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .exit-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .exit-benefits {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        text-align: right;
        margin-top: 25px;
    }

    .exit-benefit {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #2c3e50;
    }

    .exit-benefit i {
        color: #10ac84;
        font-size: 18px;
    }

    .exit-timer {
        display: inline-block;
        background: #ff6b6b;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        margin-top: 15px;
        animation: pulse 1s infinite;
    }

    .exit-social-proof {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        font-size: 13px;
        color: #7f8c8d;
    }

    .exit-social-proof strong {
        color: #2c3e50;
        font-size: 18px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .exit-modal-content {
            width: 95%;
            margin: 20px;
        }

        .exit-modal-header h2 {
            font-size: 24px;
        }

        .exit-form {
            flex-direction: column;
        }

        .exit-benefits {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }
</style>

<div id="exitIntentModal">
    <div class="exit-modal-content">
        <div class="exit-modal-header">
            <button class="exit-modal-close" onclick="closeExitIntent()">×</button>
            <h2>⏰ انتظر! لا تفوّت هذا العرض</h2>
            <p>عرض حصري لزوارنا الجدد فقط</p>
        </div>

        <div class="exit-modal-body">
            <div class="exit-offer-badge">خصم 10% 🎁</div>
            
            <h3>اشترك الآن واحصل على خصم فوري!</h3>
            <p>انضم إلى أكثر من <strong>5,000 مشترك</strong> يستفيدون من العروض الحصرية أسبوعياً</p>

            <form class="exit-form" onsubmit="submitExitIntent(event)">
                <input type="email" 
                       id="exitEmail" 
                       placeholder="✉️ بريدك الإلكتروني" 
                       required 
                       autocomplete="email">
                <button type="submit">
                    <i class="fas fa-gift"></i> احصل على الخصم
                </button>
            </form>

            <div class="exit-benefits">
                <div class="exit-benefit">
                    <i class="fas fa-check-circle"></i>
                    <span>عروض حصرية أسبوعياً</span>
                </div>
                <div class="exit-benefit">
                    <i class="fas fa-check-circle"></i>
                    <span>أولوية الوصول لمتاجر جديدة</span>
                </div>
                <div class="exit-benefit">
                    <i class="fas fa-check-circle"></i>
                    <span>كوبونات خصم مجانية</span>
                </div>
                <div class="exit-benefit">
                    <i class="fas fa-check-circle"></i>
                    <span>إلغاء الاشتراك بأي وقت</span>
                </div>
            </div>

            <div class="exit-timer" id="exitTimer">
                ⏰ العرض ينتهي خلال: <span id="timerCountdown">5:00</span>
            </div>

            <div class="exit-social-proof">
                <i class="fas fa-users"></i> 
                <strong>247</strong> شخص اشتركوا في آخر 24 ساعة
            </div>
        </div>
    </div>
</div>

<script>
    let exitIntentShown = false;
    let exitIntentTimer = null;
    let countdownSeconds = 300; // 5 minutes

    // Detect exit intent
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 0 && !exitIntentShown && !sessionStorage.getItem('exit_intent_shown')) {
            showExitIntent();
        }
    });

    // Also trigger on back button (mobile)
    if ('onpopstate' in window) {
        window.addEventListener('popstate', function() {
            if (!exitIntentShown && !sessionStorage.getItem('exit_intent_shown')) {
                showExitIntent();
                history.pushState(null, null, window.location.href);
            }
        });
    }

    function showExitIntent() {
        exitIntentShown = true;
        document.getElementById('exitIntentModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        sessionStorage.setItem('exit_intent_shown', 'true');
        
        // Start countdown timer
        startCountdown();

        // Track event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exit_intent_shown', {
                'event_category': 'engagement',
                'event_label': 'exit_popup'
            });
        }
    }

    function closeExitIntent() {
        document.getElementById('exitIntentModal').classList.remove('active');
        document.body.style.overflow = '';
        if (exitIntentTimer) clearInterval(exitIntentTimer);

        // Track dismissal
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exit_intent_dismissed', {
                'event_category': 'engagement'
            });
        }
    }

    function startCountdown() {
        const timerElement = document.getElementById('timerCountdown');
        
        exitIntentTimer = setInterval(() => {
            countdownSeconds--;
            const minutes = Math.floor(countdownSeconds / 60);
            const seconds = countdownSeconds % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (countdownSeconds <= 0) {
                clearInterval(exitIntentTimer);
                timerElement.textContent = 'انتهى العرض!';
            }
        }, 1000);
    }

    async function submitExitIntent(event) {
        event.preventDefault();
        
        const email = document.getElementById('exitEmail').value;
        const submitButton = event.target.querySelector('button');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التسجيل...';

        try {
            const response = await fetch('/api/v1/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();

            if (response.ok) {
                // Success
                document.querySelector('.exit-modal-body').innerHTML = `
                    <div style="padding: 40px 0;">
                        <i class="fas fa-check-circle" style="font-size: 64px; color: #10ac84;"></i>
                        <h3 style="margin-top: 20px; color: #2c3e50;">🎉 تم! مرحباً بك معنا</h3>
                        <p style="font-size: 16px; color: #7f8c8d;">
                            تحقق من بريدك الإلكتروني للحصول على كود الخصم الخاص بك
                        </p>
                        <div style="background: #d4edda; padding: 15px; border-radius: 10px; margin: 20px 0;">
                            <strong style="color: #155724;">كود الخصم: WELCOME10</strong>
                        </div>
                        <button onclick="closeExitIntent()" 
                                style="padding: 12px 30px; background: #10ac84; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px;">
                            متابعة التصفح
                        </button>
                    </div>
                `;

                // Track conversion
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_signup', {
                        'event_category': 'conversion',
                        'event_label': 'exit_intent',
                        'value': 1
                    });
                }

                // Auto close after 5 seconds
                setTimeout(closeExitIntent, 5000);
            } else {
                throw new Error(data.message || 'فشل التسجيل');
            }
        } catch (error) {
            alert(error.message || 'حدث خطأ. يرجى المحاولة مرة أخرى.');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    // Close on outside click
    document.getElementById('exitIntentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeExitIntent();
        }
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && exitIntentShown) {
            closeExitIntent();
        }
    });
</script>

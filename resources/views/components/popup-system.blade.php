{{-- Smart Popup System Component --}}
<div id="popup-system">
    {{-- Newsletter Popup --}}
    <div id="newsletter-popup" class="popup-overlay" style="display: none;">
        <div class="popup-container">
            <button class="popup-close" onclick="closePopup('newsletter')">&times;</button>
            <div class="popup-content">
                <div class="popup-icon">
                    <i class="fas fa-envelope-open-text fa-3x text-primary"></i>
                </div>
                <h3 class="popup-title">اشترك في نشرتنا البريدية</h3>
                <p class="popup-description">
                 أحدث العروض والمتاجر مباشرة إلى بريدك
                </p>
                <form id="newsletter-form" class="popup-form">
                    <input type="text" name="name" placeholder="الاسم (اختياري)" class="form-control mb-3">
                    <input type="email" name="email" placeholder="البريد الإلكتروني" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-primary btn-block w-100">
                        <i class="fas fa-paper-plane me-2"></i>
                        اشترك الآن
                    </button>
                </form>
                <p class="popup-footer-text">يمكنك إلغاء الاشتراك في أي وقت</p>
            </div>
        </div>
    </div>

    {{-- Exit Intent Popup --}}
    <div id="exit-popup" class="popup-overlay" style="display: none;">
        <div class="popup-container">
            <button class="popup-close" onclick="closePopup('exit')">&times;</button>
            <div class="popup-content">
                <div class="popup-icon">
                    <i class="fas fa-hand-paper fa-3x text-warning"></i>
                </div>
                <h3 class="popup-title">انتظر! لا تغادر بدون اكتشاف</h3>
                <p class="popup-description">
                    لديك <strong>{{ $totalShops ?? 45 }} متجر</strong> لم تستكشفهم بعد!<br>
                    اشترك الآن واحصل على إشعارات بأحدث المتاجر والعروض
                </p>
                <div class="popup-actions">
                    <button onclick="showNewsletterPopup()" class="btn btn-primary btn-lg">
                        <i class="fas fa-bell me-2"></i>
                        نعم، أريد الاشتراك
                    </button>
                    <button onclick="closePopup('exit')" class="btn btn-link text-muted">
                        لا شكراً، سأتصفح فقط
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Smart Engagement Popup --}}
    <div id="engagement-popup" class="popup-overlay" style="display: none;">
        <div class="popup-container">
            <button class="popup-close" onclick="closePopup('engagement')">&times;</button>
            <div class="popup-content">
                <div class="popup-icon">
                    <i class="fas fa-star fa-3x text-success"></i>
                </div>
                <h3 class="popup-title">نراك مهتم بالمتاجر لدينا!</h3>
                <p class="popup-description">
                    هل ترغب في حفظ المتاجر المفضلة لديك والحصول على تنبيهات بالعروض الجديدة؟
                </p>
                <div class="popup-actions">
                    <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus me-2"></i>
                        إنشاء حساب مجاني
                    </a>
                    <button onclick="showNewsletterPopup()" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>
                        اشتراك بالبريد فقط
                    </button>
                </div>
                <button onclick="closePopup('engagement')" class="btn btn-link text-muted mt-2">
                    لاحقاً
                </button>
            </div>
        </div>
    </div>

    {{-- Feedback Widget --}}
    <div id="feedback-widget" class="feedback-widget">
        <button id="feedback-toggle" class="feedback-toggle" onclick="toggleFeedback()">
            <i class="fas fa-comment-dots"></i>
            <span class="feedback-badge">تقييم</span>
        </button>
        
        <div id="feedback-panel" class="feedback-panel" style="display: none;">
            <div class="feedback-header">
                <h5>كيف كانت تجربتك؟</h5>
                <button class="feedback-close" onclick="toggleFeedback()">&times;</button>
            </div>
            <form id="feedback-form" class="feedback-form">
                <input type="hidden" name="page_url" id="feedback-page-url">
                
                <div class="rating-stars mb-3">
                    <i class="fas fa-star" data-rating="1"></i>
                    <i class="fas fa-star" data-rating="2"></i>
                    <i class="fas fa-star" data-rating="3"></i>
                    <i class="fas fa-star" data-rating="4"></i>
                    <i class="fas fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" name="rating" id="feedback-rating" required>
                
                <textarea name="message" class="form-control mb-2" rows="3" 
                          placeholder="أخبرنا برأيك... (اختياري)"></textarea>
                
                <input type="email" name="email" class="form-control mb-2" 
                       placeholder="بريدك الإلكتروني (اختياري)">
                
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-paper-plane me-1"></i>
                    إرسال
                </button>
            </form>
            <div id="feedback-success" style="display: none;" class="text-center p-3">
                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                <p>شكراً لك! تقييمك مهم بالنسبة لنا</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Popup Overlay */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Popup Container */
.popup-container {
    background: white;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Close Button */
.popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #f0f0f0;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10;
}

.popup-close:hover {
    background: #e0e0e0;
    transform: rotate(90deg);
}

/* Popup Content */
.popup-content {
    padding: 40px 30px;
    text-align: center;
}

.popup-icon {
    margin-bottom: 20px;
}

.popup-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.popup-description {
    font-size: 16px;
    color: #666;
    margin-bottom: 25px;
    line-height: 1.6;
}

.popup-footer-text {
    font-size: 12px;
    color: #999;
    margin-top: 15px;
}

/* Popup Form */
.popup-form .form-control {
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 16px;
}

.popup-form .btn {
    border-radius: 10px;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
}

/* Popup Actions */
.popup-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.popup-actions .btn {
    border-radius: 10px;
    padding: 12px;
    font-weight: bold;
}

/* Feedback Widget */
.feedback-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9998;
}

.feedback-toggle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.feedback-toggle:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.feedback-toggle i {
    font-size: 20px;
}

.feedback-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.feedback-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    background: white;
    border-radius: 15px;
    width: 320px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    animation: slideUp 0.3s ease;
}

.feedback-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.feedback-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.feedback-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s;
}

.feedback-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.feedback-form {
    padding: 20px;
}

.rating-stars {
    display: flex;
    justify-content: center;
    gap: 10px;
    font-size: 32px;
    cursor: pointer;
}

.rating-stars i {
    color: #ddd;
    transition: all 0.2s;
}

.rating-stars i:hover,
.rating-stars i.active {
    color: #ffc107;
    transform: scale(1.2);
}

/* Mobile Responsive */
@media (max-width: 576px) {
    .popup-container {
        width: 95%;
        border-radius: 15px;
    }
    
    .popup-content {
        padding: 30px 20px;
    }
    
    .popup-title {
        font-size: 20px;
    }
    
    .popup-description {
        font-size: 14px;
    }
    
    .feedback-panel {
        width: calc(100vw - 40px);
        right: -20px;
    }
}
</style>

<script>
// Popup System Configuration
const popupConfig = {
    newsletter: {
        delay: 15000, // Show after 15 seconds
        pagesViewed: 3, // Show after 3 pages viewed
        scrollDepth: 50, // Show after 50% scroll
    },
    exit: {
        enabled: true,
        sensitivity: 20, // pixels from top to trigger
    },
    engagement: {
        pagesViewed: 5, // Show after 5 pages
        timeSpent: 120, // Show after 2 minutes
    }
};

// Tracking Variables
let popupTracking = {
    pagesViewed: parseInt(localStorage.getItem('pagesViewed') || '0'),
    sessionStart: Date.now(),
    exitIntentShown: sessionStorage.getItem('exitIntentShown') === 'true',
    newsletterShown: sessionStorage.getItem('newsletterShown') === 'true',
    engagementShown: sessionStorage.getItem('engagementShown') === 'true',
    feedbackGiven: localStorage.getItem('feedbackGiven') === 'true',
    maxScrollDepth: 0,
};

// Initialize Popup System
document.addEventListener('DOMContentLoaded', function() {
    // Track page view
    popupTracking.pagesViewed++;
    localStorage.setItem('pagesViewed', popupTracking.pagesViewed);
    
    // Track scroll depth
    trackScrollDepth();
    
    // Check conditions for smart popups
    checkSmartConditions();
    
    // Exit intent detection
    if (!popupTracking.exitIntentShown) {
        document.addEventListener('mouseleave', handleExitIntent);
    }
    
    // Newsletter form submission
    document.getElementById('newsletter-form')?.addEventListener('submit', handleNewsletterSubmit);
    
    // Feedback form submission
    document.getElementById('feedback-form')?.addEventListener('submit', handleFeedbackSubmit);
    
    // Rating stars interaction
    initRatingStars();
    
    // Set current page URL for feedback
    document.getElementById('feedback-page-url').value = window.location.href;
});

// Track Scroll Depth
function trackScrollDepth() {
    window.addEventListener('scroll', function() {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = window.scrollY;
        const scrollPercent = (scrolled / scrollHeight) * 100;
        
        if (scrollPercent > popupTracking.maxScrollDepth) {
            popupTracking.maxScrollDepth = scrollPercent;
        }
    });
}

// Check Smart Conditions
function checkSmartConditions() {
    // Newsletter popup - smart timing
    if (!popupTracking.newsletterShown) {
        // Condition 1: Time delay
        setTimeout(function() {
            if (popupTracking.maxScrollDepth > popupConfig.newsletter.scrollDepth) {
                showPopup('newsletter');
            }
        }, popupConfig.newsletter.delay);
        
        // Condition 2: Pages viewed
        if (popupTracking.pagesViewed >= popupConfig.newsletter.pagesViewed) {
            setTimeout(() => showPopup('newsletter'), 5000);
        }
    }
    
    // Engagement popup - after significant interaction
    if (!popupTracking.engagementShown && !{{ auth()->check() ? 'true' : 'false' }}) {
        const timeSpent = (Date.now() - popupTracking.sessionStart) / 1000;
        
        if (popupTracking.pagesViewed >= popupConfig.engagement.pagesViewed || 
            timeSpent >= popupConfig.engagement.timeSpent) {
            setTimeout(() => showPopup('engagement'), 3000);
        }
    }
}

// Handle Exit Intent
function handleExitIntent(e) {
    if (e.clientY <= popupConfig.exit.sensitivity && !popupTracking.exitIntentShown) {
        showPopup('exit');
        popupTracking.exitIntentShown = true;
        sessionStorage.setItem('exitIntentShown', 'true');
    }
}

// Show Popup
function showPopup(type) {
    const popup = document.getElementById(type + '-popup');
    if (!popup) return;
    
    popup.style.display = 'flex';
    
    // Mark as shown
    popupTracking[type + 'Shown'] = true;
    sessionStorage.setItem(type + 'Shown', 'true');
    
    // Track interaction
    trackPopupInteraction(type, 'shown');
}

// Close Popup
function closePopup(type) {
    const popup = document.getElementById(type + '-popup');
    if (popup) {
        popup.style.display = 'none';
        trackPopupInteraction(type, 'closed');
    }
}

// Show Newsletter from Exit Popup
function showNewsletterPopup() {
    closePopup('exit');
    setTimeout(() => showPopup('newsletter'), 300);
}

// Handle Newsletter Submit
async function handleNewsletterSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإرسال...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("popup.newsletter") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            form.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>${data.message}</h4>
                    <p class="text-muted">تحقق من بريدك الإلكتروني لتفعيل الاشتراك</p>
                </div>
            `;
            trackPopupInteraction('newsletter', 'converted');
            setTimeout(() => closePopup('newsletter'), 3000);
        } else {
            alert(data.message || 'حدث خطأ، حاول مرة أخرى');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Newsletter error:', error);
        alert('حدث خطأ في الاتصال، حاول مرة أخرى');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Handle Feedback Submit
async function handleFeedbackSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>إرسال...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("popup.feedback") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('feedback-form').style.display = 'none';
            document.getElementById('feedback-success').style.display = 'block';
            localStorage.setItem('feedbackGiven', 'true');
            setTimeout(() => toggleFeedback(), 3000);
        } else {
            alert(data.message || 'حدث خطأ، حاول مرة أخرى');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>إرسال';
        }
    } catch (error) {
        console.error('Feedback error:', error);
        alert('حدث خطأ في الاتصال، حاول مرة أخرى');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>إرسال';
    }
}

// Toggle Feedback Widget
function toggleFeedback() {
    const panel = document.getElementById('feedback-panel');
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
}

// Initialize Rating Stars
function initRatingStars() {
    const stars = document.querySelectorAll('.rating-stars i');
    const ratingInput = document.getElementById('feedback-rating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        stars.forEach((s, index) => {
            if (index < currentRating) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
}

// Track Popup Interaction
async function trackPopupInteraction(type, action) {
    try {
        await fetch('{{ route("popup.track") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                popup_type: type,
                action: action
            })
        });
    } catch (error) {
        console.error('Tracking error:', error);
    }
}

// Close popup when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('popup-overlay')) {
        const popupType = e.target.id.replace('-popup', '');
        closePopup(popupType);
    }
});
</script>

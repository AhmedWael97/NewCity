<!-- Floating Action Button Component -->
<style>
    .fab-container {
        position: fixed;
        bottom: 80px;
        left: 20px;
        z-index: 999;
        direction: ltr;
    }

    .fab-main {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
        position: relative;
    }

    .fab-main:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 30px rgba(102, 126, 234, 0.6);
    }

    .fab-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4757;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 10px;
        font-weight: bold;
        animation: bounce 1s infinite;
    }

    .fab-menu {
        position: absolute;
        bottom: 70px;
        left: 0;
        display: none;
        flex-direction: column;
        gap: 10px;
    }

    .fab-menu.active {
        display: flex;
        animation: slideUp 0.3s ease;
    }

    .fab-option {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 12px 20px;
        border-radius: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        min-width: 200px;
    }

    .fab-option:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .fab-option i {
        font-size: 20px;
        width: 30px;
        text-align: center;
    }

    .fab-option-text {
        font-size: 14px;
        font-weight: 600;
    }

    .fab-tooltip {
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: #2c3e50;
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .fab-main:hover .fab-tooltip {
        opacity: 1;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        }
        50% {
            box-shadow: 0 4px 30px rgba(102, 126, 234, 0.7);
        }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .fab-container {
            bottom: 70px;
            left: 15px;
        }
        
        .fab-main {
            width: 55px;
            height: 55px;
            font-size: 20px;
        }

        .fab-option {
            min-width: 180px;
            padding: 10px 15px;
            font-size: 13px;
        }
    }
</style>

<div class="fab-container">
    <!-- Main FAB Button -->
    <button class="fab-main" id="fabButton" onclick="toggleFabMenu()">
        <i class="fas fa-comments"></i>
        <span class="fab-badge">جديد</span>
        <span class="fab-tooltip">كيف يمكننا مساعدتك؟</span>
    </button>

    <!-- FAB Menu Options -->
    <div class="fab-menu" id="fabMenu">
        <div class="fab-option" onclick="openFeedback()">
            <i class="fas fa-star text-warning"></i>
            <div>
                <div class="fab-option-text">قيّم تجربتك</div>
                <small style="font-size: 11px; color: #7f8c8d;">احصل على دعم مميز</small>
            </div>
        </div>

        <div class="fab-option" onclick="openNewsletter()">
            <i class="fas fa-envelope text-primary"></i>
            <div>
                <div class="fab-option-text">عروض حصرية 🎁</div>
                <small style="font-size: 11px; color: #7f8c8d;">خصم 10% على اشتراكك</small>
            </div>
        </div>

        <div class="fab-option" onclick="openHelp()">
            <i class="fas fa-question-circle text-info"></i>
            <div>
                <div class="fab-option-text">هل تحتاج مساعدة؟</div>
                <small style="font-size: 11px; color: #7f8c8d;">نحن هنا للمساعدة</small>
            </div>
        </div>

        <div class="fab-option" onclick="openWhatsApp()">
            <i class="fab fa-whatsapp text-success"></i>
            <div>
                <div class="fab-option-text">واتساب مباشر</div>
                <small style="font-size: 11px; color: #7f8c8d;">رد فوري خلال دقائق</small>
            </div>
        </div>
    </div>
</div>

<script>
    let fabMenuOpen = false;

    function toggleFabMenu() {
        const menu = document.getElementById('fabMenu');
        const button = document.getElementById('fabButton');
        fabMenuOpen = !fabMenuOpen;
        
        if (fabMenuOpen) {
            menu.classList.add('active');
            button.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            menu.classList.remove('active');
            button.innerHTML = '<i class="fas fa-comments"></i><span class="fab-badge">جديد</span>';
        }
    }

    function openFeedback() {
        toggleFabMenu();
        // Trigger your existing feedback popup
        if (typeof openFeedbackPopup === 'function') {
            openFeedbackPopup();
        } else {
            // Fallback: scroll to feedback section or open modal
            const feedbackBtn = document.querySelector('[data-bs-target="#feedbackModal"]');
            if (feedbackBtn) feedbackBtn.click();
        }
    }

    function openNewsletter() {
        toggleFabMenu();
        // Trigger your existing newsletter popup
        if (typeof openNewsletterPopup === 'function') {
            openNewsletterPopup();
        } else {
            const newsletterBtn = document.querySelector('[data-bs-target="#newsletterModal"]');
            if (newsletterBtn) newsletterBtn.click();
        }
    }

    function openHelp() {
        toggleFabMenu();
        alert('قسم المساعدة قريباً! يمكنك التواصل عبر واتساب الآن.');
    }

    function openWhatsApp() {
        toggleFabMenu();
        // Replace with your WhatsApp number
        window.open('https://wa.me/966XXXXXXXXX?text=مرحباً، أحتاج مساعدة', '_blank');
    }

    // Close FAB menu when clicking outside
    document.addEventListener('click', function(event) {
        const fabContainer = document.querySelector('.fab-container');
        if (fabMenuOpen && !fabContainer.contains(event.target)) {
            toggleFabMenu();
        }
    });

    // Show engagement prompt after 30 seconds
    setTimeout(() => {
        if (!sessionStorage.getItem('fab_prompted')) {
            const fabButton = document.getElementById('fabButton');
            fabButton.style.animation = 'pulse 0.5s 3';
            sessionStorage.setItem('fab_prompted', 'true');
        }
    }, 30000);
</script>

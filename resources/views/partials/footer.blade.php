<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-content">
            <div>
                <h3>اكتشف المدن</h3>
                <p>منصة لاستكشاف المتاجر والخدمات المحلية بكل سهولة.</p>
            </div>
            <div>
                <h3>روابط سريعة</h3>
                <a href="{{ url('/') }}">الرئيسية</a><br>
                <a href="{{ route('about') }}">عن التطبيق</a><br>
                <a href="{{ route('contact') }}">اتصل بنا</a>
            </div>
            <div>
                <h3>الشروط والسياسات</h3>
                <a href="{{ route('terms-of-use') }}">شروط الاستخدام</a><br>
                <a href="{{ route('terms-and-conditions') }}">الشروط والأحكام</a><br>
                <a href="{{ route('privacy-policy') }}">سياسة الخصوصية</a>
            </div>
            <div>
                <h3>تواصل</h3>
                <a href="mailto:{{ data_get($contactInfo, 'company.email', 'info@senueg.com') }}">{{ data_get($contactInfo, 'company.email', 'info@senueg.com') }}</a><br>
                <a href="tel:{{ str_replace(' ', '', data_get($contactInfo, 'company.phone', '+201060863230')) }}">{{ data_get($contactInfo, 'company.phone_display', data_get($contactInfo, 'company.phone', '0106 086 3230')) }}</a>
            </div>
        </div>
        <div style="text-align:center;margin-top:20px;color:rgba(255,255,255,0.8)">&copy; 2025 {{ data_get($contactInfo, 'company.name', 'SenU') }}. جميع الحقوق محفوظة.</div>
    </div>
</footer>

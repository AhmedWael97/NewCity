<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-content">
            <div>
                <h3>اكتشف المدن</h3>
                <p>منصة لاستكشاف المتاجر والخدمات المحلية بكل سهولة.</p>
            </div>
            <div>
                <h3>روابط</h3>
                <a href="{{ url('/') }}">الرئيسية</a><br>
                <a href="#features">الميزات</a><br>
                <a href="#cities">المدن</a>
            </div>
            <div>
                <h3>للمتاجر</h3>
                <a href="#">تسجيل متجر</a><br>
                <a href="#">لوحة التاجر</a>
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

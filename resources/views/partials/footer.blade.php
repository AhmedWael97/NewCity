<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-content">
            <div>
                <h3>{{ $citySettings['name_ar'] ?? 'SENÚ سنو' }}</h3>
                <p>{{ $citySettings['meta_description_ar'] ?? 'منصة لاستكشاف المتاجر والخدمات المحلية بكل سهولة.' }}</p>
                
                @if(!empty($citySettings['facebook_url']) || !empty($citySettings['twitter_url']) || !empty($citySettings['instagram_url']) || !empty($citySettings['youtube_url']))
                <div class="social-links mt-3">
                    @if(!empty($citySettings['facebook_url']))
                        <a href="{{ $citySettings['facebook_url'] }}" target="_blank" rel="noopener" class="me-2">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                    @endif
                    @if(!empty($citySettings['twitter_url']))
                        <a href="{{ $citySettings['twitter_url'] }}" target="_blank" rel="noopener" class="me-2">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                    @endif
                    @if(!empty($citySettings['instagram_url']))
                        <a href="{{ $citySettings['instagram_url'] }}" target="_blank" rel="noopener" class="me-2">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                    @endif
                    @if(!empty($citySettings['youtube_url']))
                        <a href="{{ $citySettings['youtube_url'] }}" target="_blank" rel="noopener">
                            <i class="fab fa-youtube fa-lg"></i>
                        </a>
                    @endif
                </div>
                @endif
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
                @if(!empty($citySettings['contact_email']))
                    <a href="mailto:{{ $citySettings['contact_email'] }}">
                        <i class="fas fa-envelope me-1"></i>{{ $citySettings['contact_email'] }}
                    </a><br>
                @endif
                @if(!empty($citySettings['contact_phone']))
                    <a href="tel:{{ str_replace(' ', '', $citySettings['contact_phone']) }}">
                        <i class="fas fa-phone me-1"></i>{{ $citySettings['contact_phone'] }}
                    </a><br>
                @endif
                @if(!empty($citySettings['contact_whatsapp']))
                    <a href="https://wa.me/{{ str_replace([' ', '+'], '', $citySettings['contact_whatsapp']) }}" target="_blank">
                        <i class="fab fa-whatsapp me-1"></i>{{ $citySettings['contact_whatsapp'] }}
                    </a><br>
                @endif
                @if(empty($citySettings['contact_email']) && empty($citySettings['contact_phone']))
                    <a href="mailto:{{ data_get($contactInfo, 'company.email', 'info@senueg.com') }}">{{ data_get($contactInfo, 'company.email', 'info@senueg.com') }}</a><br>
                    <a href="tel:{{ str_replace(' ', '', data_get($contactInfo, 'company.phone', '+201060863230')) }}">{{ data_get($contactInfo, 'company.phone_display', data_get($contactInfo, 'company.phone', '0106 086 3230')) }}</a>
                @endif
            </div>
        </div>
        <div style="text-align:center;margin-top:20px;color:rgba(255,255,255,0.8)">&copy; 2025 {{ $citySettings['name_ar'] ?? data_get($contactInfo, 'company.name', 'SenU') }}. جميع الحقوق محفوظة.</div>
    </div>
</footer>

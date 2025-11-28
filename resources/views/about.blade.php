@extends('layouts.app')

@section('title', 'عن التطبيق - رؤيتنا ورسالتنا')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Hero Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 mb-3" style="color: #2d3748; font-weight: bold;">عن التطبيق</h1>
                <p class="lead text-muted">منصتك المفضلة لاكتشاف أفضل المحلات والخدمات في مدينتك</p>
            </div>

            <!-- Mission Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <i class="fas fa-bullseye" style="font-size: 4rem; color: #3182ce;"></i>
                        </div>
                        <div class="col-md-10">
                            <h2 class="mb-3" style="color: #2d3748;">
                                <i class="fas fa-quote-right me-2" style="color: #3182ce;"></i>
                                رسالتنا
                            </h2>
                            <p style="font-size: 1.1rem; line-height: 1.8; color: #4a5568;">
                                نسعى لتوفير منصة شاملة وموثوقة تربط الناس بأفضل المحلات والخدمات في مدنهم، 
                                مما يسهل عليهم العثور على ما يحتاجونه بسرعة وسهولة. نؤمن بأهمية دعم الأعمال المحلية 
                                وتمكين أصحاب المحلات من الوصول إلى جمهور أوسع، مع توفير تجربة مستخدم متميزة تجمع 
                                بين البساطة والفعالية.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vision Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <i class="fas fa-eye" style="font-size: 4rem; color: #38b2ac;"></i>
                        </div>
                        <div class="col-md-10">
                            <h2 class="mb-3" style="color: #2d3748;">
                                <i class="fas fa-quote-right me-2" style="color: #38b2ac;"></i>
                                رؤيتنا
                            </h2>
                            <p style="font-size: 1.1rem; line-height: 1.8; color: #4a5568;">
                                أن نكون المنصة الرائدة والأكثر ثقة في المنطقة لاكتشاف المحلات والخدمات المحلية، 
                                حيث يجد كل مستخدم ما يبحث عنه بثقة وسهولة. نطمح لبناء مجتمع متكامل يجمع المستخدمين 
                                وأصحاب الأعمال في بيئة تفاعلية تعزز النمو الاقتصادي المحلي وتساهم في تطوير تجربة 
                                التسوق والخدمات الرقمية في مصر والعالم العربي.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Values Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: #2d3748;">
                        <i class="fas fa-star me-2" style="color: #ed8936;"></i>
                        قيمنا الأساسية
                    </h2>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="value-item p-3">
                                <h4 class="mb-2" style="color: #3182ce;">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    الثقة والمصداقية
                                </h4>
                                <p style="line-height: 1.7;">
                                    نلتزم بتوفير معلومات دقيقة وموثوقة عن المحلات والخدمات، 
                                    مع نظام تقييم شفاف يساعد المستخدمين على اتخاذ قرارات مستنيرة.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="value-item p-3">
                                <h4 class="mb-2" style="color: #38b2ac;">
                                    <i class="fas fa-users me-2"></i>
                                    دعم المجتمع المحلي
                                </h4>
                                <p style="line-height: 1.7;">
                                    نؤمن بأهمية دعم الأعمال المحلية وتمكينها من النمو والازدهار 
                                    من خلال منصتنا الرقمية.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="value-item p-3">
                                <h4 class="mb-2" style="color: #ed8936;">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    الابتكار المستمر
                                </h4>
                                <p style="line-height: 1.7;">
                                    نسعى دائماً لتطوير وتحسين خدماتنا بناءً على احتياجات المستخدمين 
                                    وأحدث التقنيات المتاحة.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="value-item p-3">
                                <h4 class="mb-2" style="color: #9f7aea;">
                                    <i class="fas fa-heart me-2"></i>
                                    تجربة مستخدم متميزة
                                </h4>
                                <p style="line-height: 1.7;">
                                    نضع المستخدم في المقام الأول، ونعمل على توفير تجربة سلسة 
                                    وممتعة في كل تفاعل مع منصتنا.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: #2d3748;">
                        <i class="fas fa-rocket me-2" style="color: #3182ce;"></i>
                        ما نقدمه لك
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-search fa-3x mb-3" style="color: #3182ce;"></i>
                                <h5>بحث متقدم</h5>
                                <p>ابحث عن المحلات حسب الفئة، المدينة، والتقييمات</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-star fa-3x mb-3" style="color: #ed8936;"></i>
                                <h5>تقييمات موثوقة</h5>
                                <p>اقرأ تجارب المستخدمين الحقيقية وشارك تجربتك</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-heart fa-3x mb-3" style="color: #e53e3e;"></i>
                                <h5>قائمة المفضلة</h5>
                                <p>احفظ المحلات المفضلة لديك للوصول السريع</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-briefcase fa-3x mb-3" style="color: #38b2ac;"></i>
                                <h5>نشر الخدمات</h5>
                                <p>انشر خدماتك الخاصة واصل إلى عملاء جدد</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-mobile-alt fa-3x mb-3" style="color: #9f7aea;"></i>
                                <h5>تطبيق موبايل</h5>
                                <p>استخدم التطبيق على جوالك في أي وقت ومكان</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box text-center p-3">
                                <i class="fas fa-headset fa-3x mb-3" style="color: #4299e1;"></i>
                                <h5>دعم فني</h5>
                                <p>فريق دعم متاح لمساعدتك في أي وقت</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact CTA -->
            <div class="card shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-5 text-center text-white">
                    <h3 class="mb-3">لديك سؤال أو اقتراح؟</h3>
                    <p class="mb-4">نحن هنا للاستماع إليك ومساعدتك</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('contact') }}" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-envelope me-2"></i>
                            اتصل بنا
                        </a>
                        <a href="https://wa.me/201060863230?text=مرحبا اريد مساعدة" class="btn btn-success btn-lg px-4" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>
                            واتساب
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .value-item {
        border-right: 3px solid #e2e8f0;
        transition: border-color 0.3s ease;
    }
    
    .value-item:hover {
        border-color: #3182ce;
    }
    
    .feature-box {
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .feature-box:hover {
        background: #edf2f7;
        transform: translateY(-5px);
    }
    
    .feature-box i {
        transition: transform 0.3s ease;
    }
    
    .feature-box:hover i {
        transform: scale(1.1);
    }
</style>
@endsection

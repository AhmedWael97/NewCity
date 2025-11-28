@extends('layouts.app')

@section('title', 'الشروط والأحكام')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4" style="color: #2d3748; font-weight: bold;">الشروط والأحكام</h1>
                    <p class="text-muted text-center mb-5">آخر تحديث: {{ date('Y/m/d') }}</p>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">1. المقدمة</h3>
                        <p style="line-height: 1.8;">
                            مرحباً بكم في منصتنا. هذه الشروط والأحكام تحكم استخدامك لخدماتنا. بدخولك إلى الموقع 
                            أو استخدامه، فإنك توافق على الالتزام بهذه الشروط والأحكام بالكامل. إذا كنت لا توافق على 
                            أي جزء من هذه الشروط، فلا يجوز لك استخدام خدماتنا.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">2. الخدمات المقدمة</h3>
                        <p style="line-height: 1.8;">نوفر من خلال منصتنا الخدمات التالية:</p>
                        <ul style="line-height: 1.8;">
                            <li><strong>دليل المحلات:</strong> قاعدة بيانات شاملة للمحلات التجارية في مختلف المدن</li>
                            <li><strong>البحث والتصفية:</strong> إمكانية البحث عن المحلات حسب الفئة والمدينة والتقييم</li>
                            <li><strong>التقييمات والمراجعات:</strong> إمكانية إضافة وقراءة تقييمات المستخدمين</li>
                            <li><strong>نشر الخدمات:</strong> إمكانية نشر خدماتك الخاصة على المنصة</li>
                            <li><strong>اقتراح المحلات:</strong> إمكانية اقتراح محلات جديدة للإضافة</li>
                            <li><strong>المفضلة:</strong> حفظ المحلات والخدمات المفضلة لديك</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">3. التسجيل والحساب</h3>
                        <div style="line-height: 1.8;">
                            <p><strong>3.1 إنشاء الحساب:</strong></p>
                            <ul>
                                <li>يجب أن يكون عمرك 18 عاماً على الأقل لإنشاء حساب</li>
                                <li>يجب تقديم معلومات دقيقة وكاملة عند التسجيل</li>
                                <li>يجب تحديث معلوماتك عند حدوث أي تغييرات</li>
                            </ul>
                            
                            <p class="mt-3"><strong>3.2 أمان الحساب:</strong></p>
                            <ul>
                                <li>أنت مسؤول عن الحفاظ على سرية معلومات تسجيل الدخول</li>
                                <li>أنت مسؤول عن جميع الأنشطة التي تحدث تحت حسابك</li>
                                <li>يجب إخطارنا فوراً بأي اختراق أمني أو استخدام غير مصرح به</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">4. قواعد السلوك</h3>
                        <p style="line-height: 1.8;">عند استخدام منصتنا، توافق على عدم:</p>
                        <ul style="line-height: 1.8;">
                            <li>نشر محتوى كاذب أو مضلل أو احتيالي</li>
                            <li>انتهاك حقوق الآخرين أو التشهير بهم</li>
                            <li>نشر محتوى مسيء أو بذيء أو غير لائق</li>
                            <li>إرسال رسائل غير مرغوب فيها (spam)</li>
                            <li>محاولة الوصول غير المصرح به إلى النظام</li>
                            <li>استخدام المنصة لأي أغراض غير قانونية</li>
                            <li>التلاعب بالتقييمات أو المراجعات</li>
                            <li>انتحال شخصية أفراد أو كيانات أخرى</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">5. نشر المحتوى</h3>
                        <div style="line-height: 1.8;">
                            <p><strong>5.1 ملكية المحتوى:</strong></p>
                            <ul>
                                <li>أنت تحتفظ بملكية المحتوى الذي تنشره على المنصة</li>
                                <li>تمنحنا ترخيصاً غير حصري لاستخدام وعرض وتوزيع المحتوى الخاص بك</li>
                            </ul>
                            
                            <p class="mt-3"><strong>5.2 المسؤولية عن المحتوى:</strong></p>
                            <ul>
                                <li>أنت مسؤول بالكامل عن المحتوى الذي تنشره</li>
                                <li>نحتفظ بالحق في إزالة أي محتوى نعتبره غير مناسب</li>
                                <li>نحتفظ بالحق في مراجعة المحتوى قبل نشره</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">6. الرسوم والدفع</h3>
                        <p style="line-height: 1.8;">
                            حالياً، معظم خدمات المنصة مجانية للاستخدام الأساسي. ومع ذلك:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>قد نقدم خدمات مدفوعة مميزة في المستقبل</li>
                            <li>سيتم إخطارك بوضوح بأي رسوم قبل تطبيقها</li>
                            <li>جميع الرسوم نهائية وغير قابلة للاسترداد ما لم ينص على خلاف ذلك</li>
                            <li>نحتفظ بالحق في تغيير الأسعار في أي وقت</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">7. حقوق الملكية الفكرية</h3>
                        <p style="line-height: 1.8;">
                            جميع حقوق الملكية الفكرية في المنصة وتصميمها ومحتواها الأصلي هي ملك لنا أو لمرخصينا. 
                            يشمل ذلك على سبيل المثال لا الحصر:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>الشعارات والعلامات التجارية</li>
                            <li>التصميمات والرسومات</li>
                            <li>الأكواد البرمجية والبرامج</li>
                            <li>المحتوى النصي الأصلي</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">8. إخلاء المسؤولية</h3>
                        <div style="line-height: 1.8;">
                            <p><strong>8.1 دقة المعلومات:</strong></p>
                            <ul>
                                <li>نسعى لتوفير معلومات دقيقة، لكن لا نضمن دقة أو اكتمال جميع المعلومات</li>
                                <li>المعلومات المقدمة من المستخدمين هي مسؤوليتهم الخاصة</li>
                            </ul>
                            
                            <p class="mt-3"><strong>8.2 الخدمات الخارجية:</strong></p>
                            <ul>
                                <li>المنصة قد تحتوي على روابط لمواقع خارجية</li>
                                <li>نحن غير مسؤولين عن محتوى أو سياسات المواقع الخارجية</li>
                            </ul>
                            
                            <p class="mt-3"><strong>8.3 ضمان الخدمة:</strong></p>
                            <ul>
                                <li>الخدمة مقدمة "كما هي" دون أي ضمانات</li>
                                <li>لا نضمن أن الخدمة ستكون خالية من الأخطاء أو متاحة دائماً</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">9. حدود المسؤولية</h3>
                        <p style="line-height: 1.8;">
                            لن نكون مسؤولين عن أي أضرار مباشرة أو غير مباشرة أو عرضية أو خاصة أو تبعية تنشأ عن:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>استخدامك أو عدم قدرتك على استخدام المنصة</li>
                            <li>أي معلومات أو محتوى حصلت عليه من خلال المنصة</li>
                            <li>الوصول غير المصرح به إلى حسابك</li>
                            <li>الأخطاء أو السهو في المحتوى</li>
                            <li>أي تعامل بينك وبين أصحاب المحلات أو مقدمي الخدمات</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">10. التعديلات والإنهاء</h3>
                        <div style="line-height: 1.8;">
                            <p><strong>10.1 تعديل الخدمة:</strong></p>
                            <ul>
                                <li>نحتفظ بالحق في تعديل أو إيقاف الخدمة في أي وقت دون إشعار مسبق</li>
                            </ul>
                            
                            <p class="mt-3"><strong>10.2 إنهاء الحساب:</strong></p>
                            <ul>
                                <li>يمكنك إنهاء حسابك في أي وقت</li>
                                <li>يمكننا تعليق أو إنهاء حسابك إذا انتهكت هذه الشروط</li>
                                <li>عند الإنهاء، ستفقد الوصول إلى جميع ميزات الحساب</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">11. القانون الحاكم</h3>
                        <p style="line-height: 1.8;">
                            تخضع هذه الشروط والأحكام وتفسر وفقاً لقوانين جمهورية مصر العربية. أي نزاع ينشأ عن 
                            هذه الشروط يخضع للاختصاص الحصري للمحاكم المصرية.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">12. التواصل معنا</h3>
                        <p style="line-height: 1.8;">
                            لأي استفسارات أو مخاوف بشأن هذه الشروط والأحكام، يرجى الاتصال بنا:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>واتساب: <a href="https://wa.me/201060863230">+201060863230</a></li>
                            <li>صفحة الدعم: <a href="{{ route('contact') }}">اتصل بنا</a></li>
                        </ul>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ url()->previous() }}" class="btn btn-primary px-4">العودة للخلف</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-section h3 {
        border-right: 4px solid #3182ce;
        padding-right: 15px;
    }
    
    .content-section ul {
        padding-right: 25px;
    }
    
    .content-section ul li {
        margin-bottom: 8px;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
</style>
@endsection

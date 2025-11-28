@extends('layouts.app')

@section('title', 'سياسة الخصوصية')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4" style="color: #2d3748; font-weight: bold;">سياسة الخصوصية</h1>
                    <p class="text-muted text-center mb-5">آخر تحديث: {{ date('Y/m/d') }}</p>

                    <div class="alert alert-info" role="alert">
                        <strong>ملاحظة:</strong> نحن ملتزمون بحماية خصوصيتك وبياناتك الشخصية. هذه السياسة توضح كيفية جمعنا واستخدامنا وحماية معلوماتك.
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">1. المعلومات التي نجمعها</h3>
                        <div style="line-height: 1.8;">
                            <p><strong>1.1 المعلومات التي تقدمها مباشرة:</strong></p>
                            <ul>
                                <li><strong>معلومات التسجيل:</strong> الاسم، البريد الإلكتروني، رقم الهاتف، كلمة المرور</li>
                                <li><strong>معلومات الملف الشخصي:</strong> الصورة الشخصية، المدينة، الفئة المفضلة</li>
                                <li><strong>المحتوى:</strong> التقييمات، التعليقات، الخدمات المنشورة، الصور</li>
                                <li><strong>معلومات الاتصال:</strong> الرسائل التي ترسلها عبر نماذج الاتصال</li>
                            </ul>
                            
                            <p class="mt-3"><strong>1.2 المعلومات التي نجمعها تلقائياً:</strong></p>
                            <ul>
                                <li><strong>معلومات الجهاز:</strong> نوع الجهاز، نظام التشغيل، المتصفح، عنوان IP</li>
                                <li><strong>معلومات الاستخدام:</strong> الصفحات التي تزورها، وقت الزيارة، مدة البقاء</li>
                                <li><strong>معلومات الموقع:</strong> موقعك الجغرافي التقريبي (إذا وافقت)</li>
                                <li><strong>ملفات تعريف الارتباط (Cookies):</strong> لتحسين تجربتك على الموقع</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">2. كيف نستخدم معلوماتك</h3>
                        <p style="line-height: 1.8;">نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
                        <ul style="line-height: 1.8;">
                            <li><strong>تقديم الخدمات:</strong> لتمكينك من استخدام ميزات المنصة</li>
                            <li><strong>تحسين الخدمة:</strong> لفهم كيفية استخدامك للمنصة وتطويرها</li>
                            <li><strong>التواصل:</strong> لإرسال إشعارات وتحديثات مهمة</li>
                            <li><strong>الأمان:</strong> لمنع الاحتيال وحماية المستخدمين</li>
                            <li><strong>التخصيص:</strong> لتقديم محتوى وتوصيات مخصصة</li>
                            <li><strong>التحليلات:</strong> لفهم سلوك المستخدمين وتحسين الأداء</li>
                            <li><strong>الامتثال القانوني:</strong> للالتزام بالقوانين واللوائح</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">3. مشاركة المعلومات</h3>
                        <div style="line-height: 1.8;">
                            <p>نحن لا نبيع بياناتك الشخصية. قد نشارك معلوماتك في الحالات التالية:</p>
                            
                            <p class="mt-3"><strong>3.1 المعلومات العامة:</strong></p>
                            <ul>
                                <li>اسمك وصورتك الشخصية عند نشر تقييم أو تعليق</li>
                                <li>الخدمات التي تنشرها تكون متاحة للجمهور</li>
                            </ul>
                            
                            <p class="mt-3"><strong>3.2 مقدمو الخدمات:</strong></p>
                            <ul>
                                <li>شركات الاستضافة وتخزين البيانات</li>
                                <li>خدمات التحليلات (مثل Google Analytics)</li>
                                <li>خدمات الدفع الإلكتروني</li>
                            </ul>
                            
                            <p class="mt-3"><strong>3.3 المتطلبات القانونية:</strong></p>
                            <ul>
                                <li>عندما يُطلب منا ذلك بموجب القانون</li>
                                <li>لحماية حقوقنا أو حقوق الآخرين</li>
                                <li>لمنع الاحتيال أو الأنشطة غير القانونية</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">4. حماية البيانات</h3>
                        <p style="line-height: 1.8;">نتخذ إجراءات أمنية لحماية معلوماتك:</p>
                        <ul style="line-height: 1.8;">
                            <li><strong>التشفير:</strong> استخدام SSL/TLS لتشفير البيانات أثناء النقل</li>
                            <li><strong>تشفير كلمات المرور:</strong> جميع كلمات المرور مشفرة (hashed)</li>
                            <li><strong>الوصول المحدود:</strong> فقط الموظفون المصرح لهم يمكنهم الوصول للبيانات</li>
                            <li><strong>النسخ الاحتياطي:</strong> نسخ احتياطي منتظم للبيانات</li>
                            <li><strong>المراقبة:</strong> مراقبة مستمرة للأنشطة المشبوهة</li>
                            <li><strong>التحديثات الأمنية:</strong> تحديث منتظم للأنظمة والبرامج</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">5. حقوقك</h3>
                        <p style="line-height: 1.8;">لديك الحقوق التالية فيما يتعلق ببياناتك الشخصية:</p>
                        <ul style="line-height: 1.8;">
                            <li><strong>الوصول:</strong> يمكنك طلب نسخة من بياناتك الشخصية</li>
                            <li><strong>التصحيح:</strong> يمكنك تصحيح أي معلومات غير دقيقة</li>
                            <li><strong>الحذف:</strong> يمكنك طلب حذف حسابك وبياناتك</li>
                            <li><strong>التقييد:</strong> يمكنك طلب تقييد معالجة بياناتك</li>
                            <li><strong>الاعتراض:</strong> يمكنك الاعتراض على استخدام بياناتك لأغراض معينة</li>
                            <li><strong>نقل البيانات:</strong> يمكنك طلب نقل بياناتك إلى خدمة أخرى</li>
                        </ul>
                        <p class="mt-2" style="line-height: 1.8;">
                            لممارسة أي من هذه الحقوق، يرجى الاتصال بنا عبر صفحة الدعم.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">6. ملفات تعريف الارتباط (Cookies)</h3>
                        <div style="line-height: 1.8;">
                            <p>نستخدم ملفات تعريف الارتباط لتحسين تجربتك:</p>
                            
                            <p class="mt-3"><strong>6.1 أنواع الـ Cookies:</strong></p>
                            <ul>
                                <li><strong>Cookies ضرورية:</strong> مطلوبة لعمل الموقع (مثل تسجيل الدخول)</li>
                                <li><strong>Cookies التفضيلات:</strong> لحفظ إعداداتك المفضلة</li>
                                <li><strong>Cookies التحليلية:</strong> لفهم كيفية استخدام الموقع</li>
                            </ul>
                            
                            <p class="mt-3"><strong>6.2 التحكم في الـ Cookies:</strong></p>
                            <ul>
                                <li>يمكنك تعطيل الـ Cookies من خلال إعدادات المتصفح</li>
                                <li>تعطيل الـ Cookies قد يؤثر على وظائف الموقع</li>
                            </ul>
                        </div>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">7. خصوصية الأطفال</h3>
                        <p style="line-height: 1.8;">
                            خدماتنا غير موجهة للأطفال دون سن 18 عاماً. نحن لا نجمع عن قصد معلومات شخصية من 
                            الأطفال. إذا علمنا أننا جمعنا معلومات من طفل، سنقوم بحذفها فوراً.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">8. الروابط الخارجية</h3>
                        <p style="line-height: 1.8;">
                            قد يحتوي موقعنا على روابط لمواقع خارجية. نحن غير مسؤولين عن ممارسات الخصوصية 
                            لهذه المواقع. ننصحك بقراءة سياسات الخصوصية لأي موقع تزوره.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">9. الاحتفاظ بالبيانات</h3>
                        <p style="line-height: 1.8;">
                            نحتفظ ببياناتك الشخصية طالما كان حسابك نشطاً أو حسب الحاجة لتقديم الخدمات. 
                            يمكنك طلب حذف حسابك في أي وقت، وسنقوم بحذف معلوماتك خلال 30 يوماً، 
                            باستثناء المعلومات المطلوبة للاحتفاظ بها قانونياً.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">10. التحديثات على سياسة الخصوصية</h3>
                        <p style="line-height: 1.8;">
                            قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. سنخطرك بأي تغييرات جوهرية عبر:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>إشعار على الموقع</li>
                            <li>رسالة عبر البريد الإلكتروني</li>
                            <li>إشعار في تطبيق الموبايل</li>
                        </ul>
                        <p class="mt-2" style="line-height: 1.8;">
                            استمرارك في استخدام الخدمة بعد التحديثات يعني موافقتك على السياسة الجديدة.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">11. الموافقة</h3>
                        <p style="line-height: 1.8;">
                            باستخدامك لمنصتنا، فإنك توافق على سياسة الخصوصية هذه وعلى معالجة بياناتك الشخصية 
                            كما هو موضح أعلاه.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">12. اتصل بنا</h3>
                        <p style="line-height: 1.8;">
                            إذا كانت لديك أسئلة أو مخاوف بشأن سياسة الخصوصية أو ممارساتنا:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>واتساب: <a href="https://wa.me/201060863230">+201060863230</a></li>
                            <li>صفحة الدعم: <a href="{{ route('contact') }}">تذاكر الدعم</a></li>
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

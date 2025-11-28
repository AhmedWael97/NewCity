@extends('layouts.app')

@section('title', 'شروط الاستخدام')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4" style="color: #2d3748; font-weight: bold;">شروط الاستخدام</h1>
                    <p class="text-muted text-center mb-5">آخر تحديث: {{ date('Y/m/d') }}</p>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">1. قبول الشروط</h3>
                        <p style="line-height: 1.8;">
                            بدخولك واستخدامك لهذا الموقع، فإنك توافق على الالتزام بشروط الاستخدام هذه وجميع القوانين واللوائح المعمول بها. 
                            إذا كنت لا توافق على أي من هذه الشروط، يُرجى عدم استخدام هذا الموقع.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">2. استخدام الموقع</h3>
                        <p style="line-height: 1.8;">يُسمح لك باستخدام هذا الموقع للأغراض التالية:</p>
                        <ul style="line-height: 1.8;">
                            <li>البحث عن المحلات والخدمات في مدينتك</li>
                            <li>الاطلاع على تفاصيل المحلات والخدمات المتاحة</li>
                            <li>إضافة المحلات المفضلة وتقييمها</li>
                            <li>نشر الخدمات الخاصة بك بعد التسجيل</li>
                            <li>التواصل مع أصحاب المحلات والخدمات</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">3. حساب المستخدم</h3>
                        <p style="line-height: 1.8;">
                            عند إنشاء حساب على موقعنا، فإنك توافق على:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>تقديم معلومات دقيقة وحديثة</li>
                            <li>الحفاظ على سرية كلمة المرور الخاصة بك</li>
                            <li>إخطارنا فوراً بأي استخدام غير مصرح به لحسابك</li>
                            <li>تحمل المسؤولية عن جميع الأنشطة التي تتم من خلال حسابك</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">4. المحتوى المنشور</h3>
                        <p style="line-height: 1.8;">
                            عند نشر محتوى على الموقع (تقييمات، خدمات، صور)، فإنك:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>تضمن أن لديك الحق في نشر هذا المحتوى</li>
                            <li>تمنح الموقع ترخيصاً لاستخدام هذا المحتوى</li>
                            <li>توافق على عدم نشر محتوى مسيء أو غير قانوني</li>
                            <li>تتحمل المسؤولية الكاملة عن المحتوى الذي تنشره</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">5. الممنوعات</h3>
                        <p style="line-height: 1.8;">يُمنع استخدام الموقع في:</p>
                        <ul style="line-height: 1.8;">
                            <li>نشر محتوى مضلل أو احتيالي</li>
                            <li>انتحال شخصية الآخرين أو الشركات</li>
                            <li>نشر برمجيات ضارة أو فيروسات</li>
                            <li>محاولة اختراق الموقع أو الوصول غير المصرح به</li>
                            <li>استخدام الموقع لأغراض غير قانونية</li>
                            <li>إرسال رسائل تجارية غير مرغوب فيها (spam)</li>
                        </ul>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">6. الملكية الفكرية</h3>
                        <p style="line-height: 1.8;">
                            جميع المواد الموجودة على هذا الموقع، بما في ذلك النصوص والصور والشعارات والتصاميم، 
                            محمية بحقوق الطبع والنشر وحقوق الملكية الفكرية الأخرى. لا يجوز نسخ أو توزيع أو تعديل 
                            أي محتوى من الموقع دون إذن كتابي مسبق.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">7. إخلاء المسؤولية</h3>
                        <p style="line-height: 1.8;">
                            نحن لا نضمن دقة أو اكتمال المعلومات المقدمة من قبل المستخدمين أو أصحاب المحلات. 
                            استخدامك للمعلومات على مسؤوليتك الخاصة. نحن غير مسؤولين عن أي أضرار أو خسائر 
                            ناتجة عن استخدام الموقع أو الاعتماد على المعلومات الواردة فيه.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">8. تعليق أو إنهاء الحساب</h3>
                        <p style="line-height: 1.8;">
                            نحتفظ بالحق في تعليق أو إنهاء حسابك أو حظر وصولك إلى الموقع في أي وقت، 
                            دون إشعار مسبق، إذا اعتقدنا أنك انتهكت شروط الاستخدام هذه أو إذا كان استخدامك 
                            للموقع يشكل خطراً على الموقع أو مستخدميه.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">9. التعديلات على الشروط</h3>
                        <p style="line-height: 1.8;">
                            نحتفظ بالحق في تعديل شروط الاستخدام هذه في أي وقت. سيتم إخطارك بأي تغييرات جوهرية 
                            من خلال الموقع أو عبر البريد الإلكتروني. استمرارك في استخدام الموقع بعد هذه التعديلات 
                            يعني موافقتك على الشروط المعدلة.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">10. القانون المطبق</h3>
                        <p style="line-height: 1.8;">
                            تخضع شروط الاستخدام هذه وتفسر وفقاً لقوانين جمهورية مصر العربية، وأي نزاع ينشأ عنها 
                            يخضع للاختصاص الحصري للمحاكم المصرية.
                        </p>
                    </div>

                    <div class="content-section mb-4">
                        <h3 class="mb-3" style="color: #4a5568;">11. اتصل بنا</h3>
                        <p style="line-height: 1.8;">
                            إذا كان لديك أي أسئلة حول شروط الاستخدام، يمكنك التواصل معنا عبر:
                        </p>
                        <ul style="line-height: 1.8;">
                            <li>الهاتف/واتساب: <a href="https://wa.me/201060863230">+201060863230</a></li>
                            <li>صفحة <a href="{{ route('contact') }}">اتصل بنا</a></li>
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

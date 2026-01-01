<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* أنماط الصفحة الداخلية */
    .shipping-hero {
        background: linear-gradient(135deg, rgba(90, 15, 241, 0.9) 0%, rgba(18, 243, 70, 0.9) 100%), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        min-height: 300px;
        display: flex;
        align-items: center;
        text-align: center;
        color: white;
        position: relative;
        margin-bottom: 3rem;
    }

    .shipping-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .shipping-hero .container {
        position: relative;
        z-index: 2;
    }

    .shipping-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .shipping-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .shipping-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .shipping-intro {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .shipping-intro::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(to bottom,rgb(112, 18, 243),rgb(15, 241, 56));
    }

    .shipping-intro i {
        font-size: 3rem;
        color: #f39c12;
        margin-bottom: 1rem;
    }

    .shipping-methods-container {
        margin-bottom: 3rem;
    }

    .section-title {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
    }

    .section-title h2 {
        font-size: 2.2rem;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(to right,rgb(149, 18, 243),rgb(15, 241, 34));
    }

    .shipping-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .method-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-top: 3px solid #8a12f3;
    }

    .method-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .method-icon {
        background: linear-gradient(135deg,rgb(119, 18, 243) 0%,rgb(22, 226, 18) 100%);
        color: white;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
    }

    .method-card h3 {
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .method-details {
        margin-top: 1.5rem;
    }

    .method-details p {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .method-details i {
        color: #8a12f3;
    }

    .delivery-areas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .area-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        transition: all 0.3s ease;
        border-left: 3px solid #8a12f3;
    }

    .area-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .area-card h3 {
        color: #8a12f3;
        margin-bottom: 0.5rem;
    }

    .note-box {
        background: #fff9e6;
        border-right: 4px solid #8a12f3;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 2rem;
    }

    .note-box i {
        color: #8a12f3;
        margin-left: 0.5rem;
    }

    .shipping-faq {
        margin-top: 4rem;
    }

    .faq-item {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }

    .faq-question {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
    }

    .faq-question i {
        transition: all 0.3s ease;
        color: #8a12f3;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-answer {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item.active .faq-answer {
        padding: 0 1.5rem 1.5rem;
        max-height: 500px;
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .shipping-hero h1 {
            font-size: 2.2rem;
        }

        .shipping-methods {
            grid-template-columns: 1fr;
        }
    }
</style>

<main>
    <section class="shipping-hero">
        <div class="container">
            <h1>الشحن والتوصيل</h1>
            <p>تعرف على سياسات الشحن وطرق التوصيل المتاحة</p>
        </div>
    </section>

    <div class="shipping-container">
        <div class="shipping-intro">
            <i class="fas fa-shipping-fast"></i>
            <h2>خدمة توصيل سريعة وموثوقة</h2>
            <p>نحن نقدم حلول شحن متنوعة لتلبية جميع احتياجاتك، مع ضمان وصول طلباتك في الوقت المحدد وبحالة ممتازة</p>
        </div>

        <div class="shipping-methods-container">
            <div class="section-title">
                <h2>طرق الشحن المتاحة</h2>
                <p>اختر الطريقة التي تناسبك من بين خياراتنا المميزة</p>
            </div>

            <div class="shipping-methods">
                <div class="method-card">
                    <div class="method-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>الشحن العادي</h3>
                    <div class="method-details">
                        <p><i class="fas fa-clock"></i> 3-5 أيام عمل</p>
                        <p><i class="fas fa-money-bill-wave"></i> 15 ريال</p>
                        <p><i class="fas fa-check-circle"></i> تغطية جميع المناطق</p>
                    </div>
                </div>

                <div class="method-card">
                    <div class="method-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>الشحن السريع</h3>
                    <div class="method-details">
                        <p><i class="fas fa-clock"></i> 1-2 أيام عمل</p>
                        <p><i class="fas fa-money-bill-wave"></i> 30 ريال</p>
                        <p><i class="fas fa-check-circle"></i> توصيل سريع ومضمون</p>
                    </div>
                </div>

                <div class="method-card">
                    <div class="method-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>الاستلام من المتجر</h3>
                    <div class="method-details">
                        <p><i class="fas fa-clock"></i> خلال 24 ساعة</p>
                        <p><i class="fas fa-money-bill-wave"></i> مجانًا</p>
                        <p><i class="fas fa-check-circle"></i> توفير على تكلفة الشحن</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="shipping-areas-container">
            <div class="section-title">
                <h2>مناطق التوصيل</h2>
                <p>نقوم بالتوصيل إلى جميع أنحاء الجمهورية اليمنية</p>
            </div>

            <div class="delivery-areas">
                <div class="area-card">
                    <h3>صنعاء</h3>
                    <p><i class="fas fa-clock"></i> التوصيل خلال 1-3 أيام</p>
                    <p><i class="fas fa-map-marker-alt"></i> جميع الأحياء والمناطق</p>
                </div>

                <div class="area-card">
                    <h3>المحافظات الرئيسية</h3>
                    <p><i class="fas fa-clock"></i> التوصيل خلال 3-5 أيام</p>
                    <p><i class="fas fa-map-marker-alt"></i> عدن، تعز، الحديدة، إب</p>
                </div>

                <div class="area-card">
                    <h3>المناطق النائية</h3>
                    <p><i class="fas fa-clock"></i> التوصيل خلال 5-7 أيام</p>
                    <p><i class="fas fa-map-marker-alt"></i> جميع المحافظات الأخرى</p>
                </div>
            </div>

            <div class="note-box">
                <p><i class="fas fa-info-circle"></i> ملاحظة: قد تختلف أوقات التوصيل حسب الظروف الجوية أو أي ظروف خارجة عن إرادتنا.</p>
            </div>
        </div>

        <div class="tracking-section">
            <div class="section-title">
                <h2>تتبع الشحنة</h2>
                <p>تابع طلبك خطوة بخطوة حتى وصوله إليك</p>
            </div>

            <div class="method-card" style="text-align: right;">
                <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                    <i class="fas fa-truck" style="font-size: 2rem; color:hsl(272, 90.40%, 51.20%); margin-left: 1rem;"></i>
                    <h3 style="margin: 0;">كيفية تتبع شحنتك</h3>
                </div>

                <p>بعد إتمام عملية الشراء، ستصلك رسالة بريد إلكتروني تحتوي على رقم تتبع الشحنة. يمكنك استخدام هذا الرقم لتتبع حالة شحنتك:</p>

                <ol style="padding-right: 1.5rem; margin-top: 1rem;">
                    <li>سجل الدخول إلى حسابك في موقعنا</li>
                    <li>انتقل إلى قسم "طلباتي"</li>
                    <li>اضغط على "تتبع الشحنة" بجوار الطلب</li>
                    <li>ستظهر لك جميع تفاصيل الشحن والتحديثات</li>
                </ol>

                <p style="margin-top: 1rem;">إذا واجهتك أي مشكلة في تتبع شحنتك، يرجى <a href="contact.php" style="color: #f39c12; font-weight: 600;">الاتصال بنا</a> وسيسعد فريق خدمة العملاء بمساعدتك.</p>
            </div>
        </div>

        <div class="shipping-faq">
            <div class="section-title">
                <h2>أسئلة شائعة حول الشحن</h2>
                <p>إجابات على أكثر الاستفسارات شيوعًا</p>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>كم تستغرق عملية التجهيز قبل الشحن؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>تستغرق معظم الطلبات 1-2 أيام عمل للتجهيز قبل الشحن. خلال فترات الذروة أو العروض الخاصة، قد تستغرق العملية وقتًا أطول قليلاً.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>هل يمكن تغيير عنوان الشحن بعد تقديم الطلب؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>نعم، يمكن تغيير عنوان الشحن إذا كان الطلب لم يتم شحنه بعد. يرجى الاتصال بنا في أسرع وقت ممكن لتعديل العنوان.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>ماذا أفعل إذا لم أستلم طلبي؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>إذا لم تستلم طلبك خلال الفترة المتوقعة، يرجى التحقق من حالة التتبع أولاً. إذا كانت هناك أي مشكلة، لا تتردد في الاتصال بنا وسنساعدك في حل المشكلة.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>هل تقدمون شحنًا مجانيًا؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>نعم، نقدم شحنًا مجانيًا للطلبات التي تزيد قيمتها عن 500 ريال للشحن العادي داخل صنعاء.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // تفعيل الأكوورديون
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            item.classList.toggle('active');

            // إغلاق العناصر الأخرى
            document.querySelectorAll('.faq-item').forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

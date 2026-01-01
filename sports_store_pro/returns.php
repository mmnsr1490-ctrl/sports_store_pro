<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* أنماط الصفحة الداخلية */
    .returns-hero {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.9) 0%, rgba(78, 173, 68, 0.9) 100%), url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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

    .returns-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .returns-hero .container {
        position: relative;
        z-index: 2;
    }

    .returns-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .returns-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .returns-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .policy-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .policy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .policy-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f5f7fa;
    }

    .policy-icon {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-left: 1.5rem;
    }

    .policy-content h2 {
        color: #9b59b6;
        margin-bottom: 1rem;
    }

    .policy-list {
        list-style-type: none;
        padding-right: 0;
    }

    .policy-list li {
        margin-bottom: 0.8rem;
        padding-right: 1.5rem;
        position: relative;
    }

    .policy-list li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0.7rem;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #9b59b6;
    }

    .policy-steps {
        list-style-type: none;
        padding-right: 0;
        counter-reset: step-counter;
    }

    .policy-steps li {
        margin-bottom: 1.5rem;
        padding-right: 2.5rem;
        position: relative;
        counter-increment: step-counter;
    }

    .policy-steps li::before {
        content: counter(step-counter);
        position: absolute;
        right: 0;
        top: 0;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #9b59b6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .policy-note {
        background: #f9f0ff;
        border-right: 4px solid #9b59b6;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 2rem;
        display: flex;
        align-items: flex-start;
    }

    .policy-note i {
        color: #9b59b6;
        font-size: 1.5rem;
        margin-left: 1rem;
    }

    .policy-note-content p {
        margin-bottom: 0.5rem;
    }

    .policy-note-content a {
        color: #9b59b6;
        font-weight: 600;
        text-decoration: none;
    }

    .policy-note-content a:hover {
        text-decoration: underline;
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .returns-hero h1 {
            font-size: 2.2rem;
        }

        .policy-header {
            flex-direction: column;
            text-align: center;
        }

        .policy-icon {
            margin-left: 0;
            margin-bottom: 1rem;
        }
    }
</style>

<main>
    <section class="returns-hero">
        <div class="container">
            <h1>سياسة الإرجاع والاستبدال</h1>
            <p>تعرف على شروط وإجراءات إرجاع أو استبدال المنتجات</p>
        </div>
    </section>

    <div class="returns-container">
        <div class="policy-card">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h2>شروط الإرجاع والاستبدال</h2>
                    <p>نحن في متجر الملابس الرياضية نحرص على رضاكم التام عن منتجاتنا</p>
                </div>
            </div>

            <div class="policy-content">
                <p>في حال لم تكن راضيًا عن المنتج، يمكنك إرجاعه أو استبداله وفقًا للشروط التالية:</p>

                <ul class="policy-list">
                    <li>يجب أن يكون المنتج في حالته الأصلية غير مستخدم، مع جميع العلامات والتغليف الأصلي.</li>
                    <li>يجب أن يتم طلب الإرجاع خلال 14 يومًا من تاريخ الاستلام.</li>
                    <li>يجب تقديم فاتورة الشراء الأصلية أو إثبات الشراء.</li>
                    <li>لا ينطبق حق الإرجاع على المنتجات الشخصية مثل الملابس الداخلية أو الجوارب لأسباب صحية.</li>
                    <li>المنتجات المخصومة أو المعروضة في العروض الخاصة لا يمكن إرجاعها إلا في حال وجود عيب مصنعي.</li>
                </ul>

                <div class="policy-note">
                    <i class="fas fa-info-circle"></i>
                    <div class="policy-note-content">
                        <p>للاستفسار عن أي بند من بنود سياسة الإرجاع، يرجى <a href="contact.php">الاتصال بنا</a>.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="policy-card">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="fas fa-list-ol"></i>
                </div>
                <div>
                    <h2>إجراءات الإرجاع</h2>
                    <p>خطوات سهلة ومباشرة لإرجاع أو استبدال منتجك</p>
                </div>
            </div>

            <div class="policy-content">
                <ol class="policy-steps">
                    <li>تواصل مع خدمة العملاء عبر البريد الإلكتروني أو الهاتف لإبلاغهم برغبتك في الإرجاع.</li>
                    <li>سنزودك برقم مرجعي للإرجاع وتفاصيل حول كيفية إعادة المنتج.</li>
                    <li>قم بتعبئة المنتج بشكل آمن مع الاحتفاظ بجميع الملحقات والتغليف الأصلي.</li>
                    <li>أرسل المنتج إلى عنواننا المذكور في رسالة التأكيد.</li>
                    <li>بعد استلام المنتج وفحصه، سنقوم بإجراء المبلغ المسترد أو استبدال المنتج خلال 5 أيام عمل.</li>
                </ol>
            </div>
        </div>

        <div class="policy-card">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h2>المبالغ المستردة</h2>
                    <p>كيف ومتى ستحصل على استرداد أموالك</p>
                </div>
            </div>

            <div class="policy-content">
                <p>سيتم إرجاع المبلغ بنفس طريقة الدفع الأصلية خلال 5-7 أيام عمل بعد استلام المنتج وفحصه. في حالات الدفع نقدًا عند الاستلام، سيتم تحويل المبلغ إلى حسابك البنكي.</p>

                <ul class="policy-list">
                    <li>الدفع ببطاقة الائتمان: استرداد خلال 3-5 أيام عمل</li>
                    <li>التحويل البنكي: استرداد خلال 5-7 أيام عمل</li>
                    <li>الدفع عند الاستلام: تحويل بنكي خلال 7 أيام عمل</li>
                </ul>

                <div class="policy-note">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="policy-note-content">
                        <p>ملاحظة: تكاليف الشحن الأصلية غير قابلة للاسترداد، وتكاليف شحن الإرجاع تكون على عاتق العميل إلا في حال وجود خطأ من المتجر أو عيب في المنتج.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="policy-card">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <h2>المنتجات المعيبة</h2>
                    <p>إجراءات خاصة بالمنتجات التي تحتوي على عيوب صناعية</p>
                </div>
            </div>

            <div class="policy-content">
                <p>في حال وجود عيب مصنعي في المنتج، يرجى التواصل معنا خلال 48 ساعة من استلام المنتج. سنقوم بترتيب استبدال المنتج أو استرداد المبلغ دون أي تكاليف إضافية عليك.</p>

                <p>يرجى إرفاق صور واضحة للعيب عند التواصل مع خدمة العملاء لتسريع عملية المعالجة.</p>

                <div class="policy-note">
                    <i class="fas fa-camera"></i>
                    <div class="policy-note-content">
                        <p>ننصح بتصوير العيب من زوايا متعددة وتوثيق عملية فتح الطرد في حال وجود تلف ظاهر أثناء التسليم.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="policy-card">
            <div class="policy-header">
                <div class="policy-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div>
                    <h2>أسئلة شائعة</h2>
                    <p>إجابات على أكثر الاستفسارات شيوعًا</p>
                </div>
            </div>

            <div class="policy-content">
                <div class="faq-item" style="border: none; box-shadow: none; padding: 0;">
                    <div class="faq-question" style="padding: 1rem 0; border-bottom: 1px solid #eee;">
                        <span>كم من الوقت يستغرق معالجة طلب الإرجاع؟</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" style="padding: 1rem 0;">
                        <p>تستغرق عملية معالجة طلب الإرجاع من 5-7 أيام عمل بعد استلام المنتج في مستودعاتنا.</p>
                    </div>
                </div>

                <div class="faq-item" style="border: none; box-shadow: none; padding: 0;">
                    <div class="faq-question" style="padding: 1rem 0; border-bottom: 1px solid #eee;">
                        <span>هل يمكن استبدال المنتج بآخر مختلف؟</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" style="padding: 1rem 0;">
                        <p>نعم، يمكنك اختيار منتج آخر مع دفع أو استرداد الفرق في السعر إن وجد.</p>
                    </div>
                </div>

                <div class="faq-item" style="border: none; box-shadow: none; padding: 0;">
                    <div class="faq-question" style="padding: 1rem 0; border-bottom: 1px solid #eee;">
                        <span>ماذا لو رفض طلب الإرجاع؟</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer" style="padding: 1rem 0;">
                        <p>في حال رفض طلب الإرجاع لعدم استيفاء الشروط، سيتم إعلامك بالأسباب وإعادة المنتج إليك على نفقتك.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // تفعيل الأكوورديون للأسئلة الشائعة
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

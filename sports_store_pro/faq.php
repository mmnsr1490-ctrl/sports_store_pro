<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* أنماط الصفحة الداخلية */
    .faq-hero {
        background: linear-gradient(135deg, rgba(151, 60, 231, 0.9) 0%, rgba(0, 168, 0, 0.9) 100%), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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

    .faq-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .faq-hero .container {
        position: relative;
        z-index: 2;
    }

    .faq-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .faq-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .faq-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .faq-search {
        position: relative;
        margin-bottom: 2rem;
    }

    .faq-search input {
        width: 100%;
        padding: 1rem 1.5rem;
        padding-left: 50px;
        border: 2px solid #bdc3c7;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .faq-search input:focus {
        outline: none;
        border-color:rgb(128, 60, 231);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .faq-search i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
    }

    .category-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-bottom: 2rem;
    }

    .tab-btn {
        background: #f5f7fa;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #2c3e50;
    }

    .tab-btn:hover, .tab-btn.active {
        background: linear-gradient(135deg,rgb(103, 60, 231) 0%,rgb(43, 192, 65) 100%);
        color: white;
    }

    .faq-accordion {
        margin-bottom: 3rem;
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

    .faq-item:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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
        color:rgb(134, 60, 231);
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

    .faq-answer ol, .faq-answer ul {
        padding-right: 1.5rem;
    }

    .faq-contact-card {
        background: linear-gradient(135deg,rgb(151, 60, 231) 0%,rgb(43, 192, 48) 100%);
        color: white;
        text-align: center;
        padding: 3rem 2rem;
        border-radius: 12px;
        margin-top: 3rem;
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
    }

    .faq-contact-card i {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        color: white;
    }

    .faq-contact-card h2 {
        margin-bottom: 1rem;
    }

    .faq-contact-card p {
        margin-bottom: 1.5rem;
        opacity: 0.9;
    }

    .cta-btn {
        display: inline-block;
        background: white;
        color:rgb(126, 60, 231);
        padding: 1rem 2.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .faq-hero h1 {
            font-size: 2.2rem;
        }

        .category-tabs {
            justify-content: center;
        }

        .faq-question {
            padding: 1rem;
            font-size: 1rem;
        }
    }
</style>

<main>
    <section class="faq-hero">
        <div class="container">
            <h1>الأسئلة الشائعة</h1>
            <p>إجابات على أكثر الأسئلة شيوعًا من عملائنا</p>
        </div>
    </section>

    <div class="faq-container">
        <div class="faq-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="ابحث في الأسئلة الشائعة...">
        </div>

        <div class="category-tabs">
            <button class="tab-btn active" data-category="all">الكل</button>
            <button class="tab-btn" data-category="ordering">الطلبات</button>
            <button class="tab-btn" data-category="shipping">الشحن</button>
            <button class="tab-btn" data-category="payments">الدفع</button>
            <button class="tab-btn" data-category="returns">الإرجاع</button>
            <button class="tab-btn" data-category="account">الحساب</button>
        </div>

        <div class="faq-accordion">
            <div class="faq-item" data-category="ordering">
                <div class="faq-question">
                    <span>كيف يمكنني الطلب من المتجر؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>يمكنك الطلب بسهولة عبر موقعنا الإلكتروني باتباع هذه الخطوات:</p>
                    <ol>
                        <li>تصفح المنتجات وأضف ما تريد إلى سلة التسوق</li>
                        <li>انتقل إلى سلة التسوق وحدد "إتمام الطلب"</li>
                        <li>قم بتسجيل الدخول أو إنشاء حساب جديد (أو تابع كضيف)</li>
                        <li>أدخل معلومات الشحن والدفع</li>
                        <li>راجع طلبك وأكد الشراء</li>
                    </ol>
                    <p>ستصلك رسالة تأكيد بالبريد الإلكتروني مع تفاصيل الطلب.</p>
                </div>
            </div>

            <div class="faq-item" data-category="ordering">
                <div class="faq-question">
                    <span>هل يمكنني تعديل طلبي بعد تقديمه؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>يمكنك تعديل الطلب أو إلغاؤه فقط إذا كان لم يتم تجهيزه للشحن بعد. للقيام بذلك، يرجى الاتصال بنا في أسرع وقت ممكن عبر صفحة <a href="contact.php" style="color: #e74c3c; font-weight: 600;">اتصل بنا</a> أو عبر خدمة العملاء.</p>
                </div>
            </div>

            <div class="faq-item" data-category="shipping">
                <div class="faq-question">
                    <span>كم تستغرق مدة التوصيل؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>تختلف مدة التوصيل حسب طريقة الشحن التي تختارها وموقعك:</p>
                    <ul>
                        <li><strong>الشحن العادي:</strong> 3-5 أيام عمل</li>
                        <li><strong>الشحن السريع:</strong> 1-2 أيام عمل</li>
                        <li><strong>المناطق النائية:</strong> قد تستغرق حتى 7 أيام عمل</li>
                    </ul>
                    <p>يمكنك الاطلاع على المزيد من التفاصيل في صفحة <a href="shipping.php" style="color: #e74c3c; font-weight: 600;">الشحن والتوصيل</a>.</p>
                </div>
            </div>

            <div class="faq-item" data-category="payments">
                <div class="faq-question">
                    <span>ما هي طرق الدفع المتاحة؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>نحن نقبل عدة طرق دفع لتسهيل عملية الشراء:</p>
                    <ul>
                        <li>الدفع نقدًا عند الاستلام</li>
                        <li>التحويل البنكي المباشر</li>
                        <li>بطاقات الائتمان والخصم (Visa, MasterCard)</li>
                        <li>محافظ إلكترونية (حسب التوفر)</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item" data-category="returns">
                <div class="faq-question">
                    <span>ما هي سياسة الإرجاع والاستبدال؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>يمكنك إرجاع أو استبدال المنتجات خلال 14 يومًا من الاستلام بشرط أن تكون في حالتها الأصلية مع جميع العلامات والتغليف. بعض المنتجات مثل الملابس الداخلية لا يمكن إرجاعها لأسباب صحية.</p>
                    <p>لمزيد من التفاصيل، يرجى زيارة صفحة <a href="returns.php" style="color: #e74c3c; font-weight: 600;">سياسة الإرجاع</a>.</p>
                </div>
            </div>

            <div class="faq-item" data-category="account">
                <div class="faq-question">
                    <span>كيف يمكنني تغيير معلومات حسابي؟</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>يمكنك تحديث معلومات حسابك في أي وقت عن طريق تسجيل الدخول إلى حسابك والنقر على "الملف الشخصي". من هناك يمكنك تعديل معلوماتك الشخصية، عنوان الشحن، وتفضيلات الاتصال.</p>
                </div>
            </div>
        </div>

        <div class="faq-contact-card">
            <i class="fas fa-headset"></i>
            <h2>لم تجد إجابتك؟</h2>
            <p>إذا كان لديك أي استفسارات أخرى، لا تتردد في الاتصال بنا. فريق خدمة العملاء لدينا متاح لمساعدتك على مدار الساعة.</p>
            <a href="contact.php" class="cta-btn">اتصل بنا الآن</a>
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

    // تصفية الأسئلة حسب الفئة
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.getAttribute('data-category');

            // تحديث التبويبات النشطة
            document.querySelectorAll('.tab-btn').forEach(tab => {
                tab.classList.remove('active');
            });
            btn.classList.add('active');

            // تصفية الأسئلة
            document.querySelectorAll('.faq-item').forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // بحث الأسئلة
    document.querySelector('.faq-search input').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();

        document.querySelectorAll('.faq-item').forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

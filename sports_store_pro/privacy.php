<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* أنماط الصفحة الداخلية */
    .privacy-hero {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.9) 0%, rgba(41, 128, 185, 0.9) 100%), url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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

    .privacy-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .privacy-hero .container {
        position: relative;
        z-index: 2;
    }

    .privacy-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .privacy-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .privacy-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .privacy-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .privacy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .privacy-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f5f7fa;
    }

    .privacy-icon {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
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

    .privacy-content h2 {
        color: #3498db;
        margin-bottom: 1rem;
    }

    .privacy-list {
        list-style-type: none;
        padding-right: 0;
    }

    .privacy-list li {
        margin-bottom: 0.8rem;
        padding-right: 1.5rem;
        position: relative;
    }

    .privacy-list li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0.7rem;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3498db;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .feature-item {
        background: #f5f7fa;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .feature-icon {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin: 0 auto 1rem;
    }

    .privacy-note {
        background: #ebf5fb;
        border-right: 4px solid #3498db;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 2rem;
        display: flex;
        align-items: flex-start;
    }

    .privacy-note i {
        color: #3498db;
        font-size: 1.5rem;
        margin-left: 1rem;
    }

    .privacy-note-content p {
        margin-bottom: 0.5rem;
    }

    .privacy-note-content a {
        color: #3498db;
        font-weight: 600;
        text-decoration: none;
    }

    .privacy-note-content a:hover {
        text-decoration: underline;
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .privacy-hero h1 {
            font-size: 2.2rem;
        }

        .privacy-header {
            flex-direction: column;
            text-align: center;
        }

        .privacy-icon {
            margin-left: 0;
            margin-bottom: 1rem;
        }
    }
</style>

<main>
    <section class="privacy-hero">
        <div class="container">
            <h1>سياسة الخصوصية</h1>
            <p>كيف نحمي ونستخدم بياناتك الشخصية</p>
        </div>
    </section>

    <div class="privacy-container">
        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h2>مقدمة</h2>
                    <p>التزامنا بحماية خصوصيتك وبياناتك الشخصية</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>نحن في متجر الملابس الرياضية ندرك أهمية خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضح سياسة الخصوصية هذه كيف نجمع ونستخدم ونحمي معلوماتك الشخصية عند استخدامك لموقعنا الإلكتروني أو خدماتنا.</p>

                <p>باستخدامك لموقعنا أو خدماتنا، فإنك توافق على شروط سياسة الخصوصية هذه. إذا كنت لا توافق على هذه السياسة، يرجى عدم استخدام موقعنا أو خدماتنا.</p>

                <div class="privacy-note">
                    <i class="fas fa-info-circle"></i>
                    <div class="privacy-note-content">
                        <p>تاريخ آخر تحديث: 1 يناير 2024</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h2>البيانات التي نجمعها</h2>
                    <p>أنواع المعلومات التي قد نجمعها عنك</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>قد نجمع الأنواع التالية من المعلومات:</p>

                <div class="feature-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>المعلومات الشخصية</h3>
                        <p>الاسم، البريد الإلكتروني، رقم الهاتف، العنوان، ومعلومات الدفع</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3>معلومات التصفح</h3>
                        <p>عنوان IP، نوع المتصفح، صفحات الزيارة، وقت ومدة الزيارة</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-cookie"></i>
                        </div>
                        <h3>الكوكيز</h3>
                        <p>ملفات تعريف الارتباط لتحسين تجربة المستخدم</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div>
                    <h2>كيف نستخدم بياناتك</h2>
                    <p>الأغراض المشروعة لمعالجة بياناتك الشخصية</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>نستخدم المعلومات التي نجمعها للأغراض التالية:</p>

                <ul class="privacy-list">
                    <li>معالجة الطلبات وإتمام عمليات الشراء</li>
                    <li>تحسين تجربة المستخدم وتخصيص المحتوى</li>
                    <li>إرسال رسائل بريدية حول العروض والمنتجات الجديدة (يمكنك إلغاء الاشتراك في أي وقت)</li>
                    <li>تحليل استخدام الموقع لتحسين خدماتنا</li>
                    <li>الرد على استفساراتك وطلباتك</li>
                    <li>الكشف عن ومنع الاحتيال والأنشطة غير القانونية</li>
                </ul>

                <div class="privacy-note">
                    <i class="fas fa-lock"></i>
                    <div class="privacy-note-content">
                        <p>نحن لا نبيع أو نؤجر بياناتك الشخصية لأطراف ثالثة لأغراض التسويق.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h2>حماية البيانات</h2>
                    <p>الإجراءات الأمنية التي نتخذها لحماية معلوماتك</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>نحن نستخدم إجراءات أمنية فنية وإدارية لضمان حماية بياناتك الشخصية من الوصول غير المصرح به أو الاستخدام غير القانوني.</p>

                <div class="feature-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h3>تشفير البيانات</h3>
                        <p>تشفير البيانات أثناء نقلها باستخدام تقنية SSL</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <h3>خوادم آمنة</h3>
                        <p>تخزين البيانات على خوادم آمنة مع وصول محدود</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <h3>تحديثات مستمرة</h3>
                        <p>التحديث المنتظم لأنظمتنا الأمنية</p>
                    </div>
                </div>

                <p>مع ذلك، لا يمكن ضمان أمان كامل لأي نقل بيانات عبر الإنترنت أو تخزين إلكتروني.</p>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div>
                    <h2>مشاركة البيانات</h2>
                    <p>مع من قد نشارك معلوماتك ولماذا</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>نحن لا نبيع أو نؤجر بياناتك الشخصية لأطراف ثالثة. قد نشارك معلومات محدودة مع:</p>

                <ul class="privacy-list">
                    <li>مقدمي خدمات الدفع لمعالجة المدفوعات</li>
                    <li>شركات الشحن لتوصيل الطلبات</li>
                    <li>مقدمي خدمات التحليلات لتحسين موقعنا</li>
                </ul>

                <p>تلتزم هذه الأطراف الثالثة بالحفاظ على سرية المعلومات واستخدامها فقط للأغراض المتفق عليها.</p>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h2>حقوقك</h2>
                    <p>حقوقك فيما يتعلق ببياناتك الشخصية</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>لديك الحق في:</p>

                <div class="feature-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>الوصول</h3>
                        <p>الوصول إلى بياناتك الشخصية التي نحتفظ بها</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h3>التصحيح</h3>
                        <p>طلب تصحيح أي معلومات غير دقيقة</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <h3>الحذف</h3>
                        <p>طلب حذف بياناتك الشخصية في ظروف معينة</p>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <h3>المعارضة</h3>
                        <p>معارضة معالجة بياناتك لأغراض التسويق المباشر</p>
                    </div>
                </div>

                <div class="privacy-note">
                    <i class="fas fa-envelope"></i>
                    <div class="privacy-note-content">
                        <p>لتنفيذ أي من هذه الحقوق، يرجى <a href="contact.php">الاتصال بنا</a> عبر صفحة اتصل بنا.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="privacy-card">
            <div class="privacy-header">
                <div class="privacy-icon">
                    <i class="fas fa-sync"></i>
                </div>
                <div>
                    <h2>التغييرات على السياسة</h2>
                    <p>كيف ومتى قد نقوم بتحديث هذه السياسة</p>
                </div>
            </div>

            <div class="privacy-content">
                <p>قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر. سيتم نشر أي تغييرات على هذه الصفحة مع تحديث تاريخ التعديل. ننصحك بمراجعة هذه السياسة دوريًا للاطلاع على أي تغييرات.</p>

                <p>سيتم اعتبار استمرارك في استخدام الموقع أو الخدمات بعد نشر التغييرات موافقة منها على التحديثات.</p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

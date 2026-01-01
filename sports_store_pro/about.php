<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* أنماط الصفحة الداخلية */
    .about-hero {
        background: linear-gradient(135deg, rgba(114, 46, 204, 0.9) 0%, rgba(39, 174, 96, 0.9) 100%), url('https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .about-hero .container {
        position: relative;
        z-index: 2;
    }

    .about-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .about-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .about-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .about-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-left: 1rem;
    }

    .story-timeline {
        position: relative;
        padding-right: 30px;
        margin-top: 2rem;
    }

    .story-timeline::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
        padding-right: 30px;
    }

    .timeline-year {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 0.5rem;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }

    .timeline-content {
        background: #f5f7fa;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border-right: 3px solid #667eea;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .value-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }

    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: #667eea;
    }

    .value-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1rem;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .team-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .team-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem;
        border: 5px solid #f5f7fa;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .team-position {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-top: -0.5rem;
        margin-bottom: 1rem;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
    }

    .social-links a {
        color: #3498db;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        color: #2980b9;
        transform: translateY(-3px);
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .about-hero h1 {
            font-size: 2.2rem;
        }

        .section-header {
            flex-direction: column;
            text-align: center;
        }

        .section-icon {
            margin-left: 0;
            margin-bottom: 1rem;
        }
    }
</style>

<main>
    <section class="about-hero">
        <div class="container">
            <h1>من نحن</h1>
            <p>قصتنا، رحلتنا، وقيمنا التي نؤمن بها</p>
        </div>
    </section>

    <div class="about-container">
        <div class="about-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h2>قصتنا</h2>
            </div>

            <p>بدأ متجر الملابس الرياضية رحلته في عام 2020 برؤية بسيطة: توفير ملابس رياضية عالية الجودة بأسعار معقولة. ومنذ ذلك الحين، كبرنا لنصبح أحد أبرز المتاجر الإلكترونية المتخصصة في الملابس الرياضية في المنطقة بأكملها.</p>

            <p>نحن نؤمن بأن الرياضة أسلوب حياة، وملابسك الرياضية يجب أن تكون مريحة وعملية وأنيقة في نفس الوقت.</p>

            <div class="story-timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-content">تأسيس المتجر بفرع واحد في قلب صنعاء</div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2021</div>
                    <div class="timeline-content">إطلاق أول متجر إلكتروني متخصص في الملابس الرياضية</div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2022</div>
                    <div class="timeline-content">توسعة خط الإنتاج ليشمل أكثر من 100 منتج رياضي</div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-content">وصول عدد عملائنا إلى أكثر من 10,000 عميل راضي</div>
                </div>
            </div>
        </div>

        <div class="about-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h2>رسالتنا</h2>
            </div>

            <p>رسالتنا هي إلهام الناس لتبني حياة أكثر نشاطًا وصحة من خلال توفير أفضل الملابس والأدوات الرياضية التي تساعدهم على تحقيق أهدافهم.</p>

            <p>نحن نعمل مع أفضل الموردين والمصنعين لضمان جودة منتجاتنا وملاءمتها لجميع أنواع الرياضات والتمارين.</p>

            <div class="mission-stats" style="display: flex; justify-content: space-around; flex-wrap: wrap; margin-top: 2rem;">
                <div style="text-align: center; margin: 1rem;">
                    <div style="font-size: 2.5rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">100+</div>
                    <div style="color: #7f8c8d;">منتج رياضي</div>
                </div>

                <div style="text-align: center; margin: 1rem;">
                    <div style="font-size: 2.5rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">98%</div>
                    <div style="color: #7f8c8d;">رضا العملاء</div>
                </div>

                <div style="text-align: center; margin: 1rem;">
                    <div style="font-size: 2.5rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">24/7</div>
                    <div style="color: #7f8c8d;">دعم فني</div>
                </div>
            </div>
        </div>

        <div class="about-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h2>قيمنا</h2>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>الجودة</h3>
                    <p>نلتزم بأعلى معايير الجودة في جميع منتجاتنا وخدماتنا.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>الشغف</h3>
                    <p>نحن متحمسون للرياضة ونساعد عملائنا على تحقيق أهدافهم.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>الرضا</h3>
                    <p>رضا العميل هو أولويتنا القصوى في كل تفاعل.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>الابتكار</h3>
                    <p>نبحث دائمًا عن أحدث التقنيات والتصاميم لتحسين تجربة العملاء.</p>
                </div>
            </div>
        </div>



        <div class="about-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2>فريقنا</h2>
            </div>
            <p>تم مناقشه المشروع من قبل الاستاذ</p>
            <div class="team-grid">
                <div class="team-card">
                    <img src="images/team/مالك_المصنف.jpg" alt="م / مالك المصنف" class="team-img">
                    <h3>م / مالك المصنف</h3>
                    <p class="team-position">تقنيات ويب</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                </div>



            <p>تم تكليف هذا المشروع كتطبيق لمقرر تقنيات ويب عملي اعداد الطلاب</p>

            <div class="team-grid">

                <div class="team-card">
                    <img src="images/team/abdulmageed-alothmani.jpg" alt="عبدالمجيد العثماني" class="team-img">
                    <h3>عبدالمجيد العثماني</h3>
                    <p class="team-position">مستوى ثالث</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="team-card">
                    <img src="images/team/mohammed_noman_munassar.jpg" alt="محمد نعمان منصر" class="team-img">
                    <h3>محمد نعمان منصر</h3>
                    <p class="team-position">مستوى ثالث</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="team-card">
                    <img src="images/team/faisal_obaid.jpg" alt="فيصل عبيد" class="team-img">
                    <h3>فيصل عبيد</h3>
                    <p class="team-position">مستوى ثالث</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="team-card">
                    <img src="images/team/osama-hashed.jpg" alt="اسامه حاشد" class="team-img">
                    <h3>اسامه حاشد</h3>
                    <p class="team-position">مستوى ثالث</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

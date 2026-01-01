<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // معالجة البيانات وإرسال البريد
    $success = true; // افتراض نجاح الإرسال

    if ($success) {
        $alert = '<div class="alert" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 5px solid #27ae60; animation: slideIn 0.5s ease-out;">
                    <i class="fas fa-check-circle" style="margin-left: 0.5rem; color: #27ae60;"></i>
                    شكرًا لتواصلك معنا! سنرد عليك في أقرب وقت ممكن.
                 </div>';
    } else {
        $alert = '<div class="alert" style="background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 5px solid #e74c3c; animation: slideIn 0.5s ease-out;">
                    <i class="fas fa-exclamation-circle" style="margin-left: 0.5rem; color: #e74c3c;"></i>
                    حدث خطأ أثناء إرسال رسالتك. يرجى المحاولة مرة أخرى.
                 </div>';
    }
}
?>

<style>
    /* أنماط الصفحة الداخلية */
    .contact-hero {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.9) 0%, rgba(41, 128, 185, 0.9) 100%), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
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

    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }

    .contact-hero .container {
        position: relative;
        z-index: 2;
    }

    .contact-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .contact-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .contact-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .contact-info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        transition: all 0.3s ease;
    }

    .contact-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .contact-info-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .contact-info-icon {
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

    .contact-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .contact-info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .contact-item-icon {
        background: #f5f7fa;
        color: #3498db;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .contact-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        transition: all 0.3s ease;
    }

    .contact-form-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .contact-form-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .contact-form-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control {
        width: 100%;
        padding: 1rem;
        border: 2px solid #bdc3c7;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        background: white;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .submit-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .map-container {
        margin-top: 3rem;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* التكيف مع الشاشات الصغيرة */
    @media (max-width: 768px) {
        .contact-hero h1 {
            font-size: 2.2rem;
        }

        .contact-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<main>
    <section class="contact-hero">
        <div class="container">
            <h1>تواصل معنا</h1>
            <p>فريق الدعم لدينا متاح للإجابة على جميع استفساراتك</p>
        </div>
    </section>

    <div class="contact-container">
        <div class="contact-info-card">
            <div class="contact-info-header">
                <div class="contact-info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h2>معلومات التواصل</h2>
            </div>

            <div class="contact-info-item">
                <div class="contact-item-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h3>العنوان</h3>
                    <p>اب، الجمهورية اليمنية</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-item-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div>
                    <h3>الهاتف</h3>
                    <p>+967 123 456 789</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-item-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h3>البريد الإلكتروني</h3>
                    <p>info@sportsstore.com</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-item-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3>ساعات العمل</h3>
                    <p>الأحد - الخميس: 9 صباحًا - 5 مساءً</p>
                    <p>الجمعة - السبت: مغلق</p>
                </div>
            </div>
        </div>

        <div class="contact-form-card">
            <div class="contact-form-header">
                <div class="contact-form-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2>أرسل رسالة</h2>
            </div>

            <?php if (isset($alert)) echo $alert; ?>

            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>

                <div class="form-group">
                    <label for="subject">الموضوع</label>
                    <select id="subject" name="subject" class="form-control" required>
                        <option value="">اختر الموضوع</option>
                        <option value="استفسار">استفسار</option>
                        <option value="اقتراح">اقتراح</option>
                        <option value="شكوى">شكوى</option>
                        <option value="دعم فني">دعم فني</option>
                        <option value="أخرى">أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">الرسالة</label>
                    <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> إرسال الرسالة
                </button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3886.0080732192846!2d44.20653331530388!3d15.354944963882785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTXCsDIxJzE3LjgiTiA0NMKwMTInMzEuOSJF!5e0!3m2!1sen!2sye!4v1620000000000!5m2!1sen!2sye"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

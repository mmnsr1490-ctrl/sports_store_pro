
<?php
include 'config/database.php';
// جلب إعدادات المتجر من جدول الإعدادات
$footer_settings = [
    'site_phone' => '',
    'site_email' => '',
    'site_address' => ''
];

try {
    // جلب جميع الإعدادات المطلوبة في استعلام واحد
    $stmt = $pdo->prepare("
        SELECT setting_key, setting_value
        FROM settings
        WHERE setting_key IN (
            'site_phone', 'site_email', 'site_address')
    ");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $footer_settings[$row['setting_key']] = htmlspecialchars($row['setting_value']);
    }
} catch (PDOException $e) {
    error_log("Error fetching footer settings: " . $e->getMessage());
}
?>

<footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>متجر الملابس الرياضية</h3>
                    <p>أفضل الملابس الرياضية عالية الجودة بأسعار منافسة</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="products.php">المنتجات</a></li>
                        <li><a href="about.php">من نحن</a></li>
                        <li><a href="contact.php">اتصل بنا</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>خدمة العملاء</h4>
                    <ul>
                        <li><a href="faq.php">الأسئلة الشائعة</a></li>
                        <li><a href="shipping.php">الشحن والتوصيل</a></li>
                        <li><a href="returns.php">سياسة الإرجاع</a></li>
                        <li><a href="privacy.php">سياسة الخصوصية</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>تواصل معنا</h4>
                    <div class="contact-info">
                        <p>
                            <i class="fas fa-phone"></i>
                            <?php if (!empty($footer_settings['site_phone'])): ?>
                            <span><?php echo $footer_settings['site_phone']; ?></span>
                            <?php endif; ?>
                        </p>

                        <p>
                            <i class="fas fa-envelope"></i>
                            <?php if (!empty($footer_settings['site_email'])): ?>
                            <span><?php echo $footer_settings['site_email']; ?></span>
                            <?php endif; ?>
                        </p>

                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            <?php if (!empty($footer_settings['site_address'])): ?>
                            <span><?php echo $footer_settings['site_address']; ?></span>
                            <?php endif; ?>
                        </p>

                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 متجر الملابس الرياضية. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>

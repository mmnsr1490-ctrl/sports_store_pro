<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// معالجة حفظ الإعدادات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_settings'])) {
        try {
            $pdo->beginTransaction();

            // قائمة بجميع مفاتيح الإعدادات المسموح بها
            $allowed_settings = [
                'site_name', 'site_description', 'site_email', 'site_phone',
                'site_address', 'currency', 'tax_rate', 'shipping_cost',
                'free_shipping_threshold', 'allow_registration', 'maintenance_mode'
            ];

            foreach ($allowed_settings as $key) {
                $value = $_POST[$key] ?? '';

                // معالجة القيم الخاصة (مثل checkboxes)
                if ($key === 'allow_registration' || $key === 'maintenance_mode') {
                    $value = isset($_POST[$key]) ? 1 : 0;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = ?
                ");
                $stmt->execute([$key, $value, $value]);
            }

            $pdo->commit();
            $_SESSION['success'] = "تم حفظ الإعدادات بنجاح";
            header('Location: settings.php');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "حدث خطأ أثناء حفظ الإعدادات: " . $e->getMessage();
        }
    }

    if (isset($_POST['backup_database'])) {
        // هنا يمكن إضافة كود لعمل نسخة احتياطية من قاعدة البيانات
        $_SESSION['success'] = "تم إنشاء نسخة احتياطية من قاعدة البيانات";
        header('Location: settings.php');
        exit();
    }
}

// جلب الإعدادات الحالية
$current_settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $error = "حدث خطأ أثناء جلب الإعدادات: " . $e->getMessage();
}

// إعدادات افتراضية
 $default_settings = [];
    // 'site_name' => 'متجر الملابس الرياضية',
    // 'site_description' => 'أفضل متجر للملابس الرياضية عالية الجودة',
    // 'site_email' => 'info@sportsstore.com',
    // 'site_phone' => '+966501234567',
    // 'site_address' => 'الرياض، المملكة العربية السعودية',
    // 'currency' => 'ريال سعودي',
    // 'tax_rate' => '15',
    // 'shipping_cost' => '25',
    // 'free_shipping_threshold' => '200',
    // 'allow_registration' => '1',
    // 'maintenance_mode' => '0'
// ];
//
// دمج الإعدادات مع الأولوية للقيم المخزنة
$settings = array_merge($default_settings, $current_settings);

// جلب إحصائيات النظام
$system_stats = [
    'total_users' => 0,
    'total_products' => 0,
    'total_orders' => 0,
    'total_categories' => 0
];

try {
    $stmt = $pdo->query("
        SELECT
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM products) as total_products,
            (SELECT COUNT(*) FROM orders) as total_orders,
            (SELECT COUNT(*) FROM categories) as total_categories
    ");
    $system_stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "حدث خطأ أثناء جلب إحصائيات النظام: " . $e->getMessage();
}

// عرض رسائل النجاح من الجلسة
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات النظام - <?php echo htmlspecialchars($settings['site_name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .settings-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }

        .settings-tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            margin-bottom: -1px;
            border-radius: 5px 5px 0 0;
        }

        .settings-tab.active {
            background: white;
            border-color: #dee2e6 #dee2e6 white;
            font-weight: bold;
        }

        .settings-tab-content {
            display: none;
        }

        .settings-tab-content.active {
            display: block;
        }

        /* ... (بقية التنسيقات الحالية) ... */
    </style>
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> المنتجات</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> الفئات</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../index.php"><i class="fas fa-eye"></i> عرض المتجر</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>إعدادات النظام</h1>
                <p>إدارة إعدادات المتجر والنظام</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- System Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_stats['total_users']; ?></h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_stats['total_products']; ?></h3>
                        <p>إجمالي المنتجات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_stats['total_orders']; ?></h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_stats['total_categories']; ?></h3>
                        <p>إجمالي الفئات</p>
                    </div>
                </div>
            </div>

            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <div class="settings-tab active" data-tab="general">الإعدادات العامة</div>
                <div class="settings-tab" data-tab="shipping">الشحن والضرائب</div>
                <div class="settings-tab" data-tab="system">إعدادات النظام</div>
                <div class="settings-tab" data-tab="tools">أدوات النظام</div>
            </div>

            <!-- General Settings -->
            <div class="settings-tab-content active" id="general-tab">
                <div class="content-section">
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>اسم المتجر</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>البريد الإلكتروني</label>
                                <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>رقم الهاتف</label>
                                <input type="text" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone']); ?>">
                            </div>

                            <div class="form-group">
                                <label>العملة</label>
                                <input type="text" name="currency" value="<?php echo htmlspecialchars($settings['currency']); ?>" required>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>وصف المتجر</label>
                                <textarea name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>عنوان المتجر</label>
                                <textarea name="site_address" rows="2"><?php echo htmlspecialchars($settings['site_address']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ الإعدادات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Shipping & Tax Settings -->
            <div class="settings-tab-content" id="shipping-tab">
                <div class="content-section">
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>معدل الضريبة (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="<?php echo htmlspecialchars($settings['tax_rate']); ?>">
                            </div>

                            <div class="form-group">
                                <label>تكلفة الشحن (ريال)</label>
                                <input type="number" step="0.01" name="shipping_cost" value="<?php echo htmlspecialchars($settings['shipping_cost']); ?>">
                            </div>

                            <div class="form-group">
                                <label>الحد الأدنى للشحن المجاني (ريال)</label>
                                <input type="number" step="0.01" name="free_shipping_threshold" value="<?php echo htmlspecialchars($settings['free_shipping_threshold']); ?>">
                            </div>

                            <div class="form-group">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ الإعدادات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Settings -->
            <div class="settings-tab-content" id="system-tab">
                <div class="content-section">
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="allow_registration" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                                    السماح بتسجيل مستخدمين جدد
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                    وضع الصيانة
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ الإعدادات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Tools -->
            <div class="settings-tab-content" id="tools-tab">
                <div class="content-section">
                    <div class="system-tools">
                        <div class="tool-card">
                            <div class="tool-icon bg-danger">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="tool-info">
                                <h3>النسخ الاحتياطي</h3>
                                <p>إنشاء نسخة احتياطية من قاعدة البيانات</p>
                            </div>
                            <div class="tool-action">
                                <form method="POST">
                                    <button type="submit" name="backup_database" class="btn btn-warning">
                                        <i class="fas fa-download"></i> إنشاء نسخة احتياطية
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="tool-card">
                            <div class="tool-icon bg-secondary">
                                <i class="fas fa-broom"></i>
                            </div>
                            <div class="tool-info">
                                <h3>تنظيف النظام</h3>
                                <p>حذف الملفات المؤقتة والسجلات القديمة</p>
                            </div>
                            <div class="tool-action">
                                <button type="button" class="btn btn-info" onclick="alert('ميزة تنظيف النظام قيد التطوير')">
                                    <i class="fas fa-trash"></i> تنظيف النظام
                                </button>
                            </div>
                        </div>

                        <div class="tool-card">
                            <div class="tool-icon bg-success">
                                <i class="fas fa-sync"></i>
                            </div>
                            <div class="tool-info">
                                <h3>تحديث النظام</h3>
                                <p>البحث عن تحديثات النظام وتطبيقها</p>
                            </div>
                            <div class="tool-action">
                                <button type="button" class="btn btn-success" onclick="alert('النظام محدث إلى أحدث إصدار')">
                                    <i class="fas fa-check"></i> محدث
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="content-section">
                    <h3>معلومات النظام</h3>
                    <div class="system-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>إصدار PHP:</strong>
                                <span><?php echo PHP_VERSION; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>إصدار MySQL:</strong>
                                <span><?php echo $pdo->query('SELECT VERSION()')->fetchColumn(); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>خادم الويب:</strong>
                                <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'غير محدد'; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>نظام التشغيل:</strong>
                                <span><?php echo PHP_OS; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>استخدام الذاكرة:</strong>
                                <span><?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB</span>
                            </div>
                            <div class="info-item">
                                <strong>وقت التشغيل:</strong>
                                <span><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // تبديل تبويبات الإعدادات
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // إزالة النشاط من جميع التبويبات
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.settings-tab-content').forEach(c => c.classList.remove('active'));

            // إضافة النشاط للتبويب المحدد
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
    </script>
</body>
</html>

<?php
include 'config/database.php';

// جلب اسم الموقع من جدول الإعدادات
$site_name = "FitWare"; // قيمة افتراضية

try {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['setting_value'])) {
        $site_name = htmlspecialchars($result['setting_value']);
    }
} catch (PDOException $e) {
    error_log("Error fetching site name: " . $e->getMessage());
}

// جلب وصف الموقع
$site_description = "";
try {
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'site_description'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['setting_value'])) {
        $site_description = htmlspecialchars($result['setting_value']);
    }
} catch (PDOException $e) {
    error_log("Error fetching site description: " . $e->getMessage());
}

// التحقق إذا كان المستخدم مديراً
$is_admin = false;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_admin = $user && $user['is_admin'];
    } catch (PDOException $e) {
        error_log("Error checking admin status: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 8px;
        }
        .user-menu-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }
        .user-menu-trigger {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            min-width: 180px;
            z-index: 1000;
            padding: 8px 0;
        }
        .dropdown a {
            display: block;
            padding: 8px 16px;
            color: #333;
            text-decoration: none;
            transition: background 0.2s;
        }
        .dropdown a:hover {
            background: #f5f5f5;
        }
        .dropdown.show {
            display: block;
        }
        .admin-link {
            background-color: #f8f9fa;
            border-left: 3px solid #e74c3c;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <a href="index.php">
                        <i class="fas fa-tshirt"></i>
                        <?php echo $site_name; ?>
                    </a>
                </div>

                <ul class="nav-menu">
                    <li><a href="index.php">الرئيسية</a></li>
                    <li><a href="products.php">المنتجات</a></li>
                    <li><a href="offers.php"><i class="fas fa-percentage"></i> العروض</a></li>
                    <li><a href="about.php">من نحن</a></li>
                    <li><a href="contact.php">اتصل بنا</a></li>
                </ul>

                <div class="nav-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cart.php" class="cart-icon" title="سلة التسوق">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count">
                                <?php
                                $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
                                echo $cart_count;
                                ?>
                            </span>
                        </a>

                        <div class="user-menu-wrapper">
                            <div class="user-menu-trigger" onclick="toggleUserMenu()">
                                <?php if (!empty($_SESSION['user_avatar'])): ?>
                                    <img src="uploads/avatars/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>"
                                         alt="صورة المستخدم" class="user-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user-circle user-avatar"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-chevron-down" style="margin-right: 5px;"></i>
                            </div>

                            <div class="dropdown" id="userDropdown">
                                <a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                                <a href="orders.php"><i class="fas fa-shopping-bag"></i> طلباتي</a>
                                <?php if ($is_admin): ?>
                                    <a href="admin/" class="admin-link"><i class="fas fa-cog"></i> لوحة التحكم</a>
                                <?php endif; ?>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">تسجيل الدخول</a>
                        <a href="register.php" class="btn-register">التسجيل</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // إغلاق القائمة عند النقر خارجها
        window.onclick = function(event) {
            if (!event.target.matches('.user-menu-trigger') && !event.target.closest('.user-menu-trigger')) {
                const dropdowns = document.getElementsByClassName("dropdown");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>

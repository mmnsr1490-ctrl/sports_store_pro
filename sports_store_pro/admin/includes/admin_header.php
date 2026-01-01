
<?php
// التأكد من أن المتغيرات مُعرَّفة
$page_title = isset($page_title) ? $page_title : 'لوحة التحكم';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'المدير';
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $username;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - إدارة المتجر الرياضي</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/productsstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* تنسيق متطابق مع الموقع الرئيسي */
        .admin-header-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .admin-nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-brand h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .sidebar-toggle {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
        }

        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .admin-user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .user-dropdown {
            position: relative;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            min-width: 200px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
        }

        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .dropdown-menu a:hover {
            background: #f8f9fa;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
            color: #dc3545;
        }

        /* تنسيق الشريط الجانبي */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .admin-sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            flex-shrink: 0;
            transition: all 0.3s;
            position: relative;
        }

        .admin-sidebar.collapsed {
            width: 70px;
        }

        .admin-sidebar.collapsed .sidebar-text {
            display: none;
        }

        .admin-sidebar.collapsed .sidebar-header h3 {
            font-size: 0;
        }

        .admin-main {
            flex: 1;
            background: #f5f5f5;
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                z-index: 1000;
            }

            .admin-sidebar.active {
                left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }

            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body class="admin-body">
    <!-- Header Navigation -->
    <header class="admin-header-nav">
        <div class="admin-nav-container">
            <div class="admin-brand">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>
                    <i class="fas fa-store"></i>
                    المتجر الرياضي - لوحة التحكم
                </h1>
            </div>

            <div class="admin-user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>مرحباً، <?php echo htmlspecialchars($full_name); ?></span>
                </div>

                <div class="user-dropdown">
                    <button class="dropdown-toggle" onclick="toggleUserDropdown()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a href="../index.php">
                            <i class="fas fa-eye"></i>
                            عرض المتجر
                        </a>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            الإعدادات
                        </a>
                        <a href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            تسجيل الخروج
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-layout">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <nav class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-text">لوحة التحكم</span>
                </h3>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-text">الرئيسية</span>
                    </a>
                </li>
                <li>
                    <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i>
                        <span class="sidebar-text">المنتجات</span>
                    </a>
                </li>
                <li>
                    <a href="offers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'offers.php' ? 'active' : ''; ?> ">
                        <i class="fas fa-percentage"></i>
                        <span class="sidebar-text">العروض</span>
                    </a>
                </li>
                <li>
                    <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i>
                        <span class="sidebar-text">الفئات</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="sidebar-text">الطلبات</span>
                    </a>
                </li>
                <li>
                    <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-text">المستخدمين</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span class="sidebar-text">التقارير</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span class="sidebar-text">الإعدادات</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">

    <script>
        // وظائف JavaScript لإدارة الشريط الجانبي والقوائم المنسدلة
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth <= 768) {
                // الهواتف المحمولة
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            } else {
                // أجهزة سطح المكتب
                sidebar.classList.toggle('collapsed');
            }
        }

        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.classList.toggle('active');
        }

        // إغلاق القائمة المنسدلة عند النقر خارجها
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdownMenu');
            const toggle = event.target.closest('.dropdown-toggle');

            if (!toggle && dropdown) {
                dropdown.classList.remove('active');
            }
        });

        // إدارة حجم الشاشة
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    </script>

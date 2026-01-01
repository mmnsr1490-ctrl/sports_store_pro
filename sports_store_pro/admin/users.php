
<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// معالجة العمليات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
                $stmt->execute([$_POST['user_id'], $_SESSION['user_id']]);
                $success = "تم حذف المستخدم بنجاح";
                break;

            case 'toggle_status':
                $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
                $stmt->execute([$_POST['user_id']]);
                $success = "تم تحديث حالة المستخدم بنجاح";
                break;

            case 'make_admin':
                $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
                $stmt->execute([$_POST['user_id']]);
                $success = "تم منح صلاحيات الإدارة للمستخدم";
                break;
        }
    }
}

// جلب المستخدمين
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$query = "SELECT *, (SELECT COUNT(*) FROM orders WHERE user_id = users.id) as orders_count FROM users WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    if ($role_filter == 'admin') {
        $query .= " AND is_admin = 1";
    } else {
        $query .= " AND is_admin = 0";
    }
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إحصائيات المستخدمين
$stmt = $pdo->query("SELECT
    COUNT(*) as total_users,
    COUNT(CASE WHEN is_admin = 1 THEN 1 END) as admin_count,
    COUNT(*) as active_count,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users
    FROM users");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                <li><a href="offers.php"><i class="fas fa-percentage"></i> العروض</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> الفئات</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> المستخدمين</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../index.php"><i class="fas fa-eye"></i> عرض المتجر</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>إدارة المستخدمين</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- User Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['admin_count']; ?></h3>
                        <p>المديرين</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['active_count']; ?></h3>
                        <p>المستخدمين النشطين</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['new_users']; ?></h3>
                        <p>مستخدمين جدد (30 يوم)</p>
                    </div>
                </div>
            </div>

            <!-- Users List -->
            <div class="content-section">
                <div class="actions-bar">
                    <h2>قائمة المستخدمين</h2>
                    <div class="search-box">
                        <form method="GET" style="display: flex; gap: 0.5rem;">
                            <input type="text" name="search" placeholder="البحث في المستخدمين..." value="<?php echo $search; ?>">
                            <select name="role">
                                <option value="">جميع الأدوار</option>
                                <option value="user" <?php echo $role_filter == 'user' ? 'selected' : ''; ?>>مستخدم</option>
                                <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>مدير</option>
                            </select>
                            <button type="submit" class="btn-sm btn-primary">بحث</button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>اسم المستخدم</th>
                                <th>الاسم الكامل</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>الدور</th>
                                <th>عدد الطلبات</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['full_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone'] ?: 'غير محدد'; ?></td>
                                <td>
                                    <span class="status status-<?php echo $user['is_admin'] ? 'admin' : 'user'; ?>">
                                        <?php echo $user['is_admin'] ? 'مدير' : 'مستخدم'; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['orders_count']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <?php if (!$user['is_admin']): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="make_admin">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn-sm btn-warning" onclick="return confirm('هل تريد منح صلاحيات الإدارة؟')">
                                                    <i class="fas fa-user-shield"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">أنت</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <style>
    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 5px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .text-muted {
        color: #6c757d;
        font-style: italic;
    }

    .status-admin {
        background: #007bff;
        color: white;
    }

    .status-user {
        background: #28a745;
        color: white;
    }
    </style>
</body>
</html>

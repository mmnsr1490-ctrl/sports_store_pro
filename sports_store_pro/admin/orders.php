
<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// معالجة تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    $success = "تم تحديث حالة الطلب بنجاح";
}

// جلب الطلبات
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];

if ($status_filter) {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $query .= " AND (u.username LIKE ? OR o.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// عرض تفاصيل طلب محدد
$order_details = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email, u.phone, u.address
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$_GET['view']]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order_details) {
        $stmt = $pdo->prepare("
            SELECT oi.*, p.name as product_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$_GET['view']]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
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
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../index.php"><i class="fas fa-eye"></i> عرض المتجر</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>إدارة الطلبات</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($order_details): ?>
                <!-- Order Details -->
                <div class="content-section">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2>تفاصيل الطلب #<?php echo $order_details['id']; ?></h2>
                        <a href="orders.php" class="btn-sm btn-primary">العودة للقائمة</a>
                    </div>

                    <div class="form-grid">
                        <div>
                            <h3>معلومات العميل</h3>
                            <p><strong>الاسم:</strong> <?php echo $order_details['username']; ?></p>
                            <p><strong>البريد الإلكتروني:</strong> <?php echo $order_details['email']; ?></p>
                            <p><strong>الهاتف:</strong> <?php echo $order_details['phone'] ?: 'غير محدد'; ?></p>
                            <p><strong>العنوان:</strong> <?php echo $order_details['address'] ?: 'غير محدد'; ?></p>
                        </div>

                        <div>
                            <h3>معلومات الطلب</h3>
                            <p><strong>تاريخ الطلب:</strong> <?php echo date('Y-m-d H:i', strtotime($order_details['order_date'])); ?></p>
                            <p><strong>إجمالي المبلغ:</strong> <?php echo number_format($order_details['total_amount'], 2); ?> ريال</p>
                            <p><strong>عنوان الشحن:</strong> <?php echo $order_details['shipping_address'] ?: 'غير محدد'; ?></p>

                            <form method="POST" style="margin-top: 1rem;">
                                <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                                <label><strong>حالة الطلب:</strong></label>
                                <select name="status" style="margin: 0.5rem 0;">
                                    <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>في الانتظار</option>
                                    <option value="processing" <?php echo $order_details['status'] == 'processing' ? 'selected' : ''; ?>>قيد المعالجة</option>
                                    <option value="shipped" <?php echo $order_details['status'] == 'shipped' ? 'selected' : ''; ?>>تم الشحن</option>
                                    <option value="delivered" <?php echo $order_details['status'] == 'delivered' ? 'selected' : ''; ?>>تم التسليم</option>
                                    <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-sm btn-success">تحديث الحالة</button>
                            </form>
                        </div>
                    </div>

                    <h3>منتجات الطلب</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo $item['product_name']; ?></td>
                                <td><?php echo number_format($item['price'], 2); ?> ريال</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ريال</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="content-section">
                    <div class="actions-bar">
                        <h2>قائمة الطلبات</h2>
                        <div class="search-box">
                            <form method="GET" style="display: flex; gap: 0.5rem;">
                                <input type="text" name="search" placeholder="البحث برقم الطلب أو اسم العميل..." value="<?php echo $search; ?>">
                                <select name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>في الانتظار</option>
                                    <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>قيد المعالجة</option>
                                    <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>تم الشحن</option>
                                    <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>تم التسليم</option>
                                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
                                </select>
                                <button type="submit" class="btn-sm btn-primary">بحث</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo $order['username']; ?></td>
                                    <td><?php echo $order['email']; ?></td>
                                    <td><?php echo number_format($order['total_amount'], 2); ?> ريال</td>
                                    <td>
                                        <span class="status status-<?php echo $order['status']; ?>">
                                            <?php
                                            $status_ar = [
                                                'pending' => 'في الانتظار',
                                                'processing' => 'قيد المعالجة',
                                                'shipped' => 'تم الشحن',
                                                'delivered' => 'تم التسليم',
                                                'cancelled' => 'ملغي'
                                            ];
                                            echo $status_ar[$order['status']];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn-sm btn-primary">عرض</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
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
    </style>
</body>
</html>

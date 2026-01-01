
<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// جلب معلومات المستخدم الكاملة إذا لم تكن موجودة في الجلسة
if (!isset($_SESSION['full_name'])) {
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['full_name'] = $user['full_name'] ?? $_SESSION['username'];
}

$page_title = 'لوحة التحكم الرئيسية';

// جلب الإحصائيات
$stats = [];

// إجمالي المنتجات
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// إجمالي الطلبات
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// إجمالي المستخدمين
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// إجمالي المبيعات
$stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'");
$stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;

// الطلبات الحديثة
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/admin_header.php'; ?>

            <div class="admin-content-header">
                <h1>مرحباً بك في لوحة التحكم</h1>
                <p>مرحباً، <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['products']; ?></h3>
                        <p>إجمالي المنتجات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['orders']; ?></h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['users']; ?></h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($stats['revenue'], 2); ?> ريال</h3>
                        <p>إجمالي المبيعات</p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="content-section">
                <h2>الطلبات الحديثة</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>اسم العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['username']; ?></td>
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
        </main>
    </div>
</body>
</html>


<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// إحصائيات عامة
$stmt = $pdo->query("
    SELECT
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_order_value,
        COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_orders
    FROM orders
");
$general_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// إحصائيات شهرية
$stmt = $pdo->query("
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') as month,
        COUNT(*) as orders_count,
        SUM(total_amount) as revenue
    FROM orders
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthly_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// أفضل المنتجات مبيعاً
$stmt = $pdo->query("
    SELECT
        p.name,
        p.price,
        SUM(oi.quantity) as total_sold,
        SUM(oi.quantity * oi.price) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'delivered'
    GROUP BY p.id, p.name, p.price
    ORDER BY total_sold DESC
    LIMIT 10
");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// أفضل العملاء
$stmt = $pdo->query("
    SELECT
        u.username,
        u.email,
        COUNT(o.id) as orders_count,
        SUM(o.total_amount) as total_spent
    FROM users u
    JOIN orders o ON u.id = o.user_id
    WHERE o.status = 'delivered'
    GROUP BY u.id, u.username, u.email
    ORDER BY total_spent DESC
    LIMIT 10
");
$top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إحصائيات الفئات
$stmt = $pdo->query("
    SELECT
        c.name as category_name,
        COUNT(p.id) as products_count,
        COALESCE(SUM(oi.quantity), 0) as total_sold,
        COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    LEFT JOIN order_items oi ON p.id = oi.product_id
    LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'delivered'
    GROUP BY c.id, c.name
    ORDER BY total_revenue DESC
");
$category_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إحصائيات حالة الطلبات
$stmt = $pdo->query("
    SELECT
        status,
        COUNT(*) as count,
        ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM orders)), 2) as percentage
    FROM orders
    GROUP BY status
    ORDER BY count DESC
");
$order_status_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير والإحصائيات</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../index.php"><i class="fas fa-eye"></i> عرض المتجر</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>التقارير والإحصائيات</h1>
                <p>نظرة شاملة على أداء المتجر</p>
            </div>

            <!-- General Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($general_stats['total_orders']); ?></h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($general_stats['total_revenue'], 2); ?> ريال</h3>
                        <p>إجمالي الإيرادات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($general_stats['avg_order_value'], 2); ?> ريال</h3>
                        <p>متوسط قيمة الطلب</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($general_stats['completed_orders']); ?></h3>
                        <p>الطلبات المكتملة</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Monthly Revenue Chart -->
                <div class="content-section">
                    <h2>الإيرادات الشهرية</h2>
                    <canvas id="monthlyRevenueChart" style="max-height: 300px;"></canvas>
                </div>

                <!-- Order Status Chart -->
                <div class="content-section">
                    <h2>حالة الطلبات</h2>
                    <canvas id="orderStatusChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            <!-- Tables Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Top Products -->
                <div class="content-section">
                    <h2>أفضل المنتجات مبيعاً</h2>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية المباعة</th>
                                    <th>الإيرادات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_products as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo $product['total_sold']; ?></td>
                                    <td><?php echo number_format($product['total_revenue'], 2); ?> ريال</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Customers -->
                <div class="content-section">
                    <h2>أفضل العملاء</h2>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>العميل</th>
                                    <th>عدد الطلبات</th>
                                    <th>إجمالي الإنفاق</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers as $customer): ?>
                                <tr>
                                    <td><?php echo $customer['username']; ?></td>
                                    <td><?php echo $customer['orders_count']; ?></td>
                                    <td><?php echo number_format($customer['total_spent'], 2); ?> ريال</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category Stats -->
            <div class="content-section">
                <h2>إحصائيات الفئات</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>الفئة</th>
                                <th>عدد المنتجات</th>
                                <th>إجمالي المبيعات</th>
                                <th>الإيرادات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($category_stats as $category): ?>
                            <tr>
                                <td><?php echo $category['category_name']; ?></td>
                                <td><?php echo $category['products_count']; ?></td>
                                <td><?php echo $category['total_sold']; ?></td>
                                <td><?php echo number_format($category['total_revenue'], 2); ?> ريال</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyData = <?php echo json_encode(array_reverse($monthly_stats)); ?>;

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'الإيرادات (ريال)',
                data: monthlyData.map(item => item.revenue),
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ريال';
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const statusData = <?php echo json_encode($order_status_stats); ?>;

    const statusLabels = {
        'pending': 'في الانتظار',
        'processing': 'قيد المعالجة',
        'shipped': 'تم الشحن',
        'delivered': 'تم التسليم',
        'cancelled': 'ملغي'
    };

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => statusLabels[item.status] || item.status),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    '#f39c12',
                    '#3498db',
                    '#2ecc71',
                    '#27ae60',
                    '#e74c3c'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
</body>
</html>

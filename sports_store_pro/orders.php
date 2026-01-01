
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب طلبات المستخدم
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main>
    <section class="orders-page">
        <div class="container">
            <h1>طلباتي</h1>

            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h2>لا توجد طلبات</h2>
                    <p>لم تقم بإجراء أي طلبات بعد</p>
                    <a href="products.php" class="btn-shop">تسوق الآن</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>طلب رقم #<?php echo $order['id']; ?></h3>
                                    <p class="order-date"><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php
                                        $status_text = [
                                            'pending' => 'في الانتظار',
                                            'processing' => 'قيد المعالجة',
                                            'shipped' => 'تم الشحن',
                                            'delivered' => 'تم التوصيل',
                                            'cancelled' => 'ملغي'
                                        ];
                                        echo $status_text[$order['status']];
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-summary">
                                    <p><strong>عدد المنتجات:</strong> <?php echo $order['item_count']; ?></p>
                                    <p><strong>المبلغ الإجمالي:</strong> <?php echo $order['total_amount']; ?> ريال</p>
                                    <p><strong>طريقة الدفع:</strong>
                                        <?php
                                        $payment_methods = [
                                            'cash' => 'الدفع عند الاستلام',
                                            'credit_card' => 'بطاقة ائتمانية',
                                            'stc_pay' => 'STC Pay',
                                            'apple_pay' => 'Apple Pay'
                                        ];
                                        echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                        ?>
                                    </p>
                                </div>

                                <div class="order-actions">
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-view">
                                        عرض التفاصيل
                                    </a>

                                    <?php if ($order['status'] === 'pending'): ?>
                                        <form method="POST" action="cancel_order.php" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn btn-cancel"
                                                    onclick="return confirm('هل تريد إلغاء هذا الطلب؟')">
                                                إلغاء الطلب
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($order['status'] === 'delivered'): ?>
                                        <a href="reorder.php?id=<?php echo $order['id']; ?>" class="btn btn-reorder">
                                            إعادة الطلب
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.orders-page {
    padding: 2rem 0;
    min-height: 60vh;
}

.orders-page h1 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    color: #333;
}

.no-orders {
    text-align: center;
    padding: 4rem 2rem;
}

.no-orders i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.no-orders h2 {
    color: #666;
    margin-bottom: 1rem;
}

.btn-shop {
    display: inline-block;
    background: #e74c3c;
    color: white;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 1rem;
    transition: background 0.3s;
}

.btn-shop:hover {
    background: #c0392b;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.order-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.order-info h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

.order-date {
    color: #666;
    font-size: 0.9rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #d1ecf1;
    color: #0c5460;
}

.status-shipped {
    background: #d4edda;
    color: #155724;
}

.status-delivered {
    background: #d1ecf1;
    color: #0c5460;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.order-details {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.order-summary p {
    margin-bottom: 0.5rem;
    color: #555;
}

.order-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: bold;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-view {
    background: #3498db;
    color: white;
}

.btn-view:hover {
    background: #2980b9;
}

.btn-cancel {
    background: #e74c3c;
    color: white;
}

.btn-cancel:hover {
    background: #c0392b;
}

.btn-reorder {
    background: #27ae60;
    color: white;
}

.btn-reorder:hover {
    background: #229954;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .order-details {
        flex-direction: column;
        gap: 1rem;
    }

    .order-actions {
        width: 100%;
        justify-content: center;
    }

    .btn {
        flex: 1;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

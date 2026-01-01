
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب طلبات المستخدم
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main>
    <section class="my-orders">
        <div class="container">
            <h1>طلباتي</h1>

            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h2>لا توجد طلبات</h2>
                    <p>لم تقم بأي طلبات حتى الآن</p>
                    <a href="products.php" class="btn-shop">ابدأ التسوق</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>طلب #<?php echo $order['id']; ?></h3>
                                    <p class="order-date"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status <?php echo $order['status']; ?>">
                                        <?php
                                        $status_ar = [
                                            'pending' => 'قيد المعالجة',
                                            'processing' => 'قيد التجهيز',
                                            'shipped' => 'تم الشحن',
                                            'delivered' => 'تم التوصيل',
                                            'cancelled' => 'ملغي'
                                        ];
                                        echo $status_ar[$order['status']];
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-amount">
                                    <strong><?php echo $order['total_amount']; ?> ريال</strong>
                                </div>
                                <div class="order-payment">
                                    <?php
                                    $payment_ar = [
                                        'cash' => 'الدفع عند التوصيل',
                                        'card' => 'بطاقة ائتمانية',
                                        'bank' => 'تحويل بنكي'
                                    ];
                                    echo $payment_ar[$order['payment_method']];
                                    ?>
                                </div>
                            </div>

                            <div class="order-actions">
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-details">
                                    عرض التفاصيل
                                </a>
                                <?php if ($order['status'] == 'pending'): ?>
                                    <button onclick="cancelOrder(<?php echo $order['id']; ?>)" class="btn-cancel">
                                        إلغاء الطلب
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.my-orders {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.my-orders h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

.no-orders {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    border-radius: 10px;
    font-weight: bold;
    margin-top: 1rem;
    transition: background 0.3s;
}

.btn-shop:hover {
    background: #c0392b;
}

.orders-list {
    display: grid;
    gap: 1.5rem;
}

.order-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.order-card:hover {
    transform: translateY(-2px);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.order-info h3 {
    color: #333;
    margin-bottom: 0.5rem;
}

.order-date {
    color: #666;
    font-size: 0.9rem;
}

.status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
}

.status.pending {
    background: #ffeaa7;
    color: #d63031;
}

.status.processing {
    background: #74b9ff;
    color: #0984e3;
}

.status.shipped {
    background: #fd79a8;
    color: #e84393;
}

.status.delivered {
    background: #00b894;
    color: white;
}

.status.cancelled {
    background: #636e72;
    color: white;
}

.order-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.order-amount {
    font-size: 1.3rem;
    color: #e74c3c;
}

.order-payment {
    color: #666;
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.order-actions {
    display: flex;
    gap: 1rem;
}

.btn-details, .btn-cancel {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-details {
    background: #3498db;
    color: white;
}

.btn-details:hover {
    background: #2980b9;
}

.btn-cancel {
    background: #e74c3c;
    color: white;
}

.btn-cancel:hover {
    background: #c0392b;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .order-details {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .order-actions {
        flex-direction: column;
    }
}
</style>

<script>
function cancelOrder(orderId) {
    if (confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'order_id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء إلغاء الطلب');
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>

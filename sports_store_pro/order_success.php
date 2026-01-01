
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// جلب بيانات الطلب
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit();
}

include 'includes/header.php';
?>

<main>
    <section class="order-success">
        <div class="container">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <h1>تم تأكيد طلبك بنجاح!</h1>
                <p class="order-number">رقم الطلب: #<?php echo $order_id; ?></p>

                <div class="order-details">
                    <h3>تفاصيل الطلب</h3>
                    <div class="detail-row">
                        <span>الإجمالي:</span>
                        <span><?php echo $order['total_amount']; ?> ريال</span>
                    </div>
                    <div class="detail-row">
                        <span>طريقة الدفع:</span>
                        <span>
                            <?php
                            echo $order['payment_method'] == 'cash' ? 'الدفع عند التوصيل' :
                                ($order['payment_method'] == 'card' ? 'بطاقة ائتمانية' : 'تحويل بنكي');
                            ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span>حالة الطلب:</span>
                        <span class="status pending">قيد المعالجة</span>
                    </div>
                    <div class="detail-row">
                        <span>عنوان التوصيل:</span>
                        <span><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                    </div>
                </div>

                <div class="success-actions">
                    <a href="my_orders.php" class="btn btn-primary">عرض طلباتي</a>
                    <a href="products.php" class="btn btn-secondary">متابعة التسوق</a>
                </div>

                <div class="delivery-info">
                    <h4>معلومات التوصيل</h4>
                    <p>سيتم التواصل معك خلال 24 ساعة لتأكيد الطلب وتحديد موعد التوصيل.</p>
                    <p>مدة التوصيل المتوقعة: 2-3 أيام عمل</p>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.order-success {
    padding: 4rem 0;
    background: #f8f9fa;
    min-height: 60vh;
}

.success-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
}

.success-icon {
    font-size: 4rem;
    color: #27ae60;
    margin-bottom: 2rem;
}

.success-content h1 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 2rem;
}

.order-number {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #ddd;
}

.order-details {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    margin: 2rem 0;
    text-align: right;
}

.order-details h3 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #333;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.status.pending {
    background: #ffeaa7;
    color: #d63031;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.9rem;
}

.success-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
}

.btn {
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 10px;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-primary {
    background: #e74c3c;
    color: white;
}

.btn-primary:hover {
    background: #c0392b;
}

.btn-secondary {
    background: #ecf0f1;
    color: #333;
}

.btn-secondary:hover {
    background: #d5dbdb;
}

.delivery-info {
    background: #e8f5e8;
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #27ae60;
}

.delivery-info h4 {
    color: #27ae60;
    margin-bottom: 1rem;
}

.delivery-info p {
    margin: 0.5rem 0;
    color: #666;
}

@media (max-width: 768px) {
    .success-content {
        margin: 1rem;
        padding: 2rem 1rem;
    }

    .success-actions {
        flex-direction: column;
    }

    .detail-row {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

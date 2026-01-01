<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: my_orders.php');
    exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// جلب بيانات الطلب الرئيسية
$stmt = $pdo->prepare("
    SELECT o.*
    FROM orders o
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: my_orders.php');
    exit();
}

// جلب عناصر الطلب
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main>
    <section class="order-details">
        <div class="container">
            <a href="my_orders.php" class="back-link">
                <i class="fas fa-arrow-left"></i> العودة إلى الطلبات
            </a>

            <div class="order-summary">
                <h1>تفاصيل الطلب #<?php echo $order['id']; ?></h1>

                <div class="order-meta">
                    <div class="meta-item">
                        <span class="meta-label">تاريخ الطلب:</span>
                        <span class="meta-value"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">حالة الطلب:</span>
                        <span class="meta-value status <?php echo $order['status']; ?>">
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
                    <div class="meta-item">
                        <span class="meta-label">طريقة الدفع:</span>
                        <span class="meta-value">
                            <?php echo htmlspecialchars($order['payment_method']); ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">المجموع:</span>
                        <span class="meta-value price"><?php echo htmlspecialchars($order['total_amount']); ?> ريال</span>
                    </div>
                </div>
            </div>

            <div class="order-sections">
                <div class="order-section">
                    <h2>عناصر الطلب</h2>
                    <div class="order-items">
                        <?php foreach ($order_items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="images/products/<?php echo $item['image'] ?: 'default.jpg'; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <div class="item-meta">
                                        <span class="item-price"><?php echo $item['price']; ?> ريال</span>
                                        <span class="item-quantity">الكمية: <?php echo $item['quantity']; ?></span>
                                        <span class="item-subtotal">المجموع: <?php echo $item['price'] * $item['quantity']; ?> ريال</span>
                                        <?php if (!empty($item['size'])): ?>
                                            <span class="item-size">المقاس: <?php echo htmlspecialchars($item['size']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($item['color'])): ?>
                                            <span class="item-color">اللون: <?php echo htmlspecialchars($item['color']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="order-section">
                    <h2>معلومات التوصيل</h2>
                    <div class="delivery-info">
                        <?php if (!empty($order['shipping_address'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        <?php else: ?>
                            <p>لم يتم تحديد عنوان التوصيل</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($order['status'] == 'pending'): ?>
            <div class="order-actions">
                <button onclick="cancelOrder(<?php echo $order['id']; ?>)" class="btn-cancel">
                    إلغاء الطلب
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.order-details {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #3498db;
    text-decoration: none;
    margin-bottom: 1.5rem;
}

.back-link:hover {
    text-decoration: underline;
}

.order-summary {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.order-summary h1 {
    margin-bottom: 1.5rem;
    color: #333;
}

.order-meta {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.meta-label {
    color: #666;
    font-size: 0.9rem;
}

.meta-value {
    font-weight: bold;
}

.price {
    color: #e74c3c;
    font-size: 1.2rem;
}

.status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: bold;
    display: inline-block;
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

.order-sections {
    display: grid;
    gap: 2rem;
}

.order-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.order-section h2 {
    margin-bottom: 1.5rem;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.75rem;
}

.order-items {
    display: grid;
    gap: 1.5rem;
}

.order-item {
    display: flex;
    gap: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.item-image {
    width: 100px;
    height: 100px;
    flex-shrink: 0;
    border-radius: 10px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex-grow: 1;
}

.item-details h3 {
    margin-bottom: 0.5rem;
    color: #333;
}

.item-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    color: #666;
}

.item-price {
    color: #e74c3c;
    font-weight: bold;
}

.delivery-info {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.delivery-info p {
    margin: 0;
    padding: 0.5rem 0;
}

.order-actions {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-cancel:hover {
    background: #c0392b;
}

.alert-message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 15px 25px;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease;
    max-width: 80%;
    text-align: center;
}

.alert-message.show {
    opacity: 1;
}

.alert-message.success {
    background-color: #27ae60;
}

.alert-message.error {
    background-color: #e74c3c;
}

@media (max-width: 768px) {
    .order-item {
        flex-direction: column;
    }

    .item-image {
        width: 100%;
        height: 200px;
    }

    .alert-message {
        top: 10px;
        width: 90%;
        font-size: 14px;
    }
}
</style>

<script>
function showMessage(message, isSuccess) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert-message ${isSuccess ? 'success' : 'error'}`;
    messageDiv.textContent = message;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.classList.add('show');
    }, 100);

    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(messageDiv);
        }, 300);
    }, 3000);
}

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
            showMessage(data.message, data.success);
            if (data.success) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, data.delay || 10000);
            }
        })
        .catch(error => {
            showMessage('حدث خطأ في الاتصال بالخادم', false);
            console.error('Error:', error);
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>

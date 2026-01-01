
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب عناصر السلة
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image, (p.price * c.quantity) as total_price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

// حساب الإجمالي
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['total_price'];
}

// معالجة الطلب
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $card_number = isset($_POST['card_number']) ? $_POST['card_number'] : '';
    $card_holder = isset($_POST['card_holder']) ? $_POST['card_holder'] : '';
    $card_expiry = isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '';
    $card_cvv = isset($_POST['card_cvv']) ? $_POST['card_cvv'] : '';

    if (empty($shipping_address)) {
        $error = 'يرجى إدخال عنوان التوصيل';
    } elseif ($payment_method == 'card' && (empty($card_number) || empty($card_holder) || empty($card_expiry) || empty($card_cvv))) {
        $error = 'يرجى إدخال بيانات البطاقة الائتمانية كاملة';
    } else {
        try {
            $pdo->beginTransaction();

            // إنشاء الطلب
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $total_amount, $shipping_address, $payment_method]);
            $order_id = $pdo->lastInsertId();

            // إضافة عناصر الطلب
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

                // تحديث المخزون
                $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }

            // مسح السلة
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $pdo->commit();

            header('Location: order_success.php?order_id=' . $order_id);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'حدث خطأ أثناء معالجة الطلب';
        }
    }
}

include 'includes/header.php';
?>

<main>
    <section class="checkout-page">
        <div class="container">
            <h1>إتمام الطلب</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="checkout-content">
                <div class="checkout-form">
                    <form method="POST" id="checkoutForm">
                        <!-- معلومات التوصيل -->
                        <div class="form-section">
                            <h3>معلومات التوصيل</h3>
                            <div class="form-group">
                                <label for="shipping_address">عنوان التوصيل *:</label>
                                <textarea id="shipping_address" name="shipping_address" required
                                         placeholder="أدخل العنوان الكامل للتوصيل"><?php echo htmlspecialchars($_POST['shipping_address'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- طريقة الدفع -->
                        <div class="form-section">
                            <h3>طريقة الدفع</h3>
                            <div class="payment-methods">
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="cash" checked>
                                    <div class="payment-option">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>الدفع عند التوصيل</span>
                                    </div>
                                </label>

                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="card">
                                    <div class="payment-option">
                                        <i class="fas fa-credit-card"></i>
                                        <span>بطاقة ائتمانية</span>
                                    </div>
                                </label>

                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="bank">
                                    <div class="payment-option">
                                        <i class="fas fa-university"></i>
                                        <span>تحويل بنكي</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- بيانات البطاقة الائتمانية -->
                        <div class="form-section card-details" style="display: none;">
                            <h3>بيانات البطاقة الائتمانية</h3>
                            <div class="card-form">
                                <div class="form-group">
                                    <label for="card_number">رقم البطاقة:</label>
                                    <input type="text" id="card_number" name="card_number"
                                           placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>

                                <div class="form-group">
                                    <label for="card_holder">اسم حامل البطاقة:</label>
                                    <input type="text" id="card_holder" name="card_holder"
                                           placeholder="الاسم كما يظهر على البطاقة">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="card_expiry">تاريخ الانتهاء:</label>
                                        <input type="text" id="card_expiry" name="card_expiry"
                                               placeholder="MM/YY" maxlength="5">
                                    </div>

                                    <div class="form-group">
                                        <label for="card_cvv">CVV:</label>
                                        <input type="text" id="card_cvv" name="card_cvv"
                                               placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-place-order">تأكيد الطلب</button>
                    </form>
                </div>

                <!-- ملخص الطلب -->
                <div class="order-summary">
                    <h3>ملخص الطلب</h3>
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <img src="images/products/<?php echo $item['image'] ?: 'default.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>الكمية: <?php echo $item['quantity']; ?></p>
                                    <p class="item-price"><?php echo $item['total_price']; ?> ريال</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>المجموع الفرعي:</span>
                            <span><?php echo $total_amount; ?> ريال</span>
                        </div>
                        <div class="summary-row">
                            <span>الشحن:</span>
                            <span>مجاني</span>
                        </div>
                        <div class="summary-row total">
                            <strong>
                                <span>الإجمالي:</span>
                                <span><?php echo $total_amount; ?> ريال</span>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.checkout-page {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: 70vh;
}

.checkout-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.checkout-form, .order-summary {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    margin-bottom: 1.5rem;
    color: #333;
    font-size: 1.3rem;
}

.payment-methods {
    display: grid;
    gap: 1rem;
}

.payment-method {
    cursor: pointer;
    display: block;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid #eee;
    border-radius: 10px;
    transition: all 0.3s;
}

.payment-method input:checked + .payment-option {
    border-color: #e74c3c;
    background: #fff5f5;
}

.payment-option i {
    font-size: 1.5rem;
    color: #666;
}

.card-form {
    display: grid;
    gap: 1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.btn-place-order {
    width: 100%;
    padding: 1rem;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-place-order:hover {
    background: #229954;
}

.order-items {
    margin-bottom: 2rem;
}

.order-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details h4 {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.item-details p {
    margin: 0.25rem 0;
    color: #666;
    font-size: 0.9rem;
}

.item-price {
    color: #e74c3c !important;
    font-weight: bold !important;
}

.summary-totals {
    border-top: 2px solid #eee;
    padding-top: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.summary-row.total {
    font-size: 1.2rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

@media (max-width: 768px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.querySelector('.card-details');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'card') {
                cardDetails.style.display = 'block';
                cardDetails.querySelectorAll('input').forEach(input => {
                    input.required = true;
                });
            } else {
                cardDetails.style.display = 'none';
                cardDetails.querySelectorAll('input').forEach(input => {
                    input.required = false;
                });
            }
        });
    });

    // تنسيق رقم البطاقة
    const cardNumber = document.getElementById('card_number');
    if (cardNumber) {
        cardNumber.addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = formattedValue;
        });
    }

    // تنسيق تاريخ انتهاء البطاقة
    const cardExpiry = document.getElementById('card_expiry');
    if (cardExpiry) {
        cardExpiry.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            this.value = value;
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>


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
    ORDER BY c.added_at DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب الإجمالي
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['total_price'];
}

include 'includes/header.php';
?>

<main>
    <section class="cart-page">
        <div class="container">
            <h1>سلة التسوق</h1>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'added'): ?>
                <div class="alert alert-success">تم إضافة المنتج إلى السلة بنجاح!</div>
            <?php endif; ?>
            
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>سلتك فارغة</h2>
                    <p>لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
                    <a href="products.php" class="btn-shop">تسوق الآن</a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="images/products/<?php echo $item['image'] ?: 'default.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="item-price"><?php echo $item['price']; ?> ريال</p>
                                </div>
                                <div class="quantity-controls">
                                    <form method="POST" action="update_cart.php" class="quantity-form">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="action" value="decrease" class="qty-btn">-</button>
                                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                                        <button type="submit" name="action" value="increase" class="qty-btn">+</button>
                                    </form>
                                </div>
                                <div class="item-total">
                                    <strong><?php echo $item['total_price']; ?> ريال</strong>
                                </div>
                                <form method="POST" action="remove_from_cart.php" class="remove-form">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="remove-btn" onclick="return confirm('هل تريد حذف هذا المنتج من السلة؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h3>ملخص الطلب</h3>
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
                        <a href="checkout.php" class="btn-checkout">إتمام الطلب</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.cart-page {
    padding: 2rem 0;
    min-height: 60vh;
}

.cart-page h1 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    color: #333;
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-cart i {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 1rem;
}

.empty-cart h2 {
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

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.cart-items {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details h3 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.item-price {
    color: #666;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-form {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.qty-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 3px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.qty-btn:hover {
    background: #f0f0f0;
}

.quantity {
    padding: 0 0.5rem;
    font-weight: bold;
}

.item-total {
    font-weight: bold;
    color: #e74c3c;
}

.remove-btn {
    background: none;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0.5rem;
    transition: color 0.3s;
}

.remove-btn:hover {
    color: #c0392b;
}

.cart-summary {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: fit-content;
}

.cart-summary h3 {
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
    color: #333;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
}

.summary-row.total {
    border-top: 2px solid #eee;
    padding-top: 1rem;
    font-size: 1.2rem;
}

.btn-checkout {
    display: block;
    width: 100%;
    background: #27ae60;
    color: white;
    padding: 1rem;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 1.5rem;
    transition: background 0.3s;
}

.btn-checkout:hover {
    background: #229954;
}

@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        grid-template-columns: 60px 1fr;
        gap: 0.5rem;
    }
    
    .quantity-controls, .item-total, .remove-btn {
        grid-column: 1 / -1;
        justify-self: center;
        margin-top: 0.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

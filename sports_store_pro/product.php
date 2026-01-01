<?php
session_start();
include 'config/database.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: products.php');
    exit();
}

// جلب تفاصيل المنتج
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit();
}

// جلب التقييمات
$stmt = $pdo->prepare("
    SELECT r.*, u.username
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب متوسط التقييم
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE product_id = ?");
$stmt->execute([$product_id]);
$rating_data = $stmt->fetch(PDO::FETCH_ASSOC);
$avg_rating = round($rating_data['avg_rating'], 1);
$review_count = $rating_data['review_count'];

// التحقق من وجود عرض للمنتج
$stmt = $pdo->prepare("
    SELECT * FROM offers
    WHERE product_id = ?
    AND is_active = TRUE
    AND start_date <= NOW()
    AND end_date >= NOW()
    LIMIT 1
");
$stmt->execute([$product['id']]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($offer) {
    $discounted_price = $offer['discount_type'] == 'percentage'
        ? $product['price'] * (1 - ($offer['discount_value'] / 100))
        : $product['price'] - $offer['discount_value'];
}

include 'includes/header.php';
?>

<main>
    <section class="product-details">
        <div class="container">
            <div class="product-content">
                <div class="product-image">
                    <img src="images/products/<?php echo $product['image'] ?: 'default.jpg'; ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>

                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>

                    <div class="rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">(<?php echo $review_count; ?> تقييم)</span>
                    </div>

                    <div class="price">
                        <?php if ($offer): ?>
                            <span class="original-price"><?php echo $product['price']; ?> ريال</span>
                            <span class="current-price"><?php echo number_format($discounted_price, 2); ?> ريال</span>
                        <?php else: ?>
                            <span class="current-price"><?php echo $product['price']; ?> ريال</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($offer): ?>
                    <div class="product-offer">
                        <span class="offer-badge">عرض خاص</span>
                        <div class="offer-details">
                            <h3><?php echo $offer['title']; ?></h3>
                            <p><?php echo $offer['description']; ?></p>
                            <div class="offer-price">
                                <span class="discount-value">
                                    وفر <?php echo $offer['discount_type'] == 'percentage'
                                        ? $offer['discount_value'] . '%'
                                        : $offer['discount_value'] . ' ريال'; ?>
                                </span>
                            </div>
                            <div class="time-left">
                                <i class="fas fa-clock"></i> ينتهي في <?php
                                $end = new DateTime($offer['end_date']);
                                $now = new DateTime();
                                echo $end->diff($now)->format('%a أيام %h ساعات');
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="description">
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <?php if ($product['sizes'] && $product['sizes'] !== 'واحد'): ?>
                                <div class="size-selector">
                                    <label>المقاس:</label>
                                    <select name="size" required>
                                        <option value="">اختر المقاس</option>
                                        <?php foreach (explode(',', $product['sizes']) as $size): ?>
                                            <option value="<?php echo trim($size); ?>"><?php echo trim($size); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if ($product['colors']): ?>
                                <div class="color-selector">
                                    <label>اللون:</label>
                                    <select name="color" required>
                                        <option value="">اختر اللون</option>
                                        <?php foreach (explode(',', $product['colors']) as $color): ?>
                                            <option value="<?php echo trim($color); ?>"><?php echo trim($color); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="quantity-selector">
                                <label>الكمية:</label>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            </div>

                            <div class="stock-info">
                                <span class="stock-count">متوفر: <?php echo $product['stock_quantity']; ?> قطعة</span>
                            </div>

                            <button type="submit" class="btn-add-cart" <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $product['stock_quantity'] <= 0 ? 'غير متوفر' : 'أضف إلى السلة'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="login-required">
                            <a href="login.php">سجل الدخول</a> لإضافة المنتج إلى السلة
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="reviews-section">
                <h3>التقييمات والمراجعات</h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="add-review">
                        <h4>أضف تقييمك</h4>
                        <form method="POST" action="add_review.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <div class="rating-input">
                                <label>التقييم:</label>
                                <div class="stars-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>">
                                        <label for="star<?php echo $i; ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="comment-input">
                                <label for="comment">التعليق:</label>
                                <textarea name="comment" id="comment" rows="4" placeholder="اكتب تعليقك هنا..."></textarea>
                            </div>

                            <button type="submit" class="btn-submit">إرسال التقييم</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php if (empty($reviews)): ?>
                        <p class="no-reviews">لا توجد تقييمات لهذا المنتج بعد</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="review-date"><?php echo date('Y-m-d', strtotime($review['created_at'])); ?></span>
                                </div>
                                <div class="review-comment">
                                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.product-details {
    padding: 2rem 0;
}

.product-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.product-image img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 10px;
}

.product-info h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.category {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars .star {
    color: #ddd;
    font-size: 1.5rem;
}

.stars .star.filled {
    color: #ffd700;
}

.price {
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.current-price {
    font-size: 2rem;
    font-weight: bold;
    color: #e74c3c;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 1.5rem;
}

.description {
    margin-bottom: 2rem;
    line-height: 1.6;
}

.product-offer {
    background: #fff8e1;
    border: 1px solid #ffd54f;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    position: relative;
}

.offer-badge {
    position: absolute;
    top: -10px;
    left: 15px;
    background: #e74c3c;
    color: white;
    padding: 3px 15px;
    border-radius: 15px;
    font-weight: bold;
    font-size: 0.9rem;
}

.offer-details h3 {
    font-size: 1.3rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.offer-details p {
    margin-bottom: 0.5rem;
    color: #666;
}

.offer-price {
    margin: 10px 0;
}

.discount-value {
    color: #e74c3c;
    font-weight: bold;
}

.time-left {
    color: #e67e22;
    font-size: 0.9rem;
}

.add-to-cart-form {
    border: 2px solid #f0f0f0;
    padding: 2rem;
    border-radius: 10px;
    background: #fafafa;
}

.size-selector, .color-selector, .quantity-selector {
    margin-bottom: 1rem;
}

.size-selector label, .color-selector label, .quantity-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.size-selector select, .color-selector select, .quantity-selector input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.stock-info {
    margin-bottom: 1rem;
    color: #27ae60;
    font-weight: bold;
}

.btn-add-cart {
    width: 100%;
    padding: 1rem;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-add-cart:hover:not(:disabled) {
    background: #229954;
}

.btn-add-cart:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.login-required {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 5px;
}

.login-required a {
    color: #e74c3c;
    text-decoration: none;
    font-weight: bold;
}

.reviews-section {
    border-top: 2px solid #f0f0f0;
    padding-top: 2rem;
}

.reviews-section h3 {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: #333;
}

.add-review {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.rating-input {
    margin-bottom: 1rem;
}

.stars-input {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.25rem;
}

.stars-input input {
    display: none;
}

.stars-input label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.3s;
}

.stars-input input:checked ~ label,
.stars-input label:hover,
.stars-input label:hover ~ label {
    color: #ffd700;
}

.comment-input {
    margin-bottom: 1rem;
}

.comment-input textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-family: inherit;
    resize: vertical;
}

.btn-submit {
    background: #3498db;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #2980b9;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.review-item {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.review-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.review-rating .star {
    color: #ddd;
    font-size: 1.2rem;
}

.review-rating .star.filled {
    color: #ffd700;
}

.review-date {
    color: #666;
    font-size: 0.9rem;
    margin-left: auto;
}

.no-reviews {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 2rem;
}

@media (max-width: 768px) {
    .product-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .product-info h1 {
        font-size: 2rem;
    }

    .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .review-date {
        margin-left: 0;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

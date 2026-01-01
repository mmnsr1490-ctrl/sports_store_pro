<?php
session_start();
include 'config/database.php';

// جلب العروض النشطة
$current_date = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("
    SELECT o.*, p.name as product_name, p.price, p.image as product_image,
           p.description as product_description, p.id as product_id
    FROM offers o
    JOIN products p ON o.product_id = p.id
    WHERE o.is_active = TRUE
    AND o.start_date <= ?
    AND o.end_date >= ?
    ORDER BY o.end_date ASC
");
$stmt->execute([$current_date, $current_date]);
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main>
    <section class="offers-section">
        <div class="container">
            <h1 class="section-title">العروض الخاصة</h1>

            <?php if (empty($offers)): ?>
                <div class="no-offers">
                    <i class="fas fa-percentage"></i>
                    <p>لا توجد عروض متاحة حالياً</p>
                </div>
            <?php else: ?>
                <div class="offers-grid">
                    <?php foreach ($offers as $offer):
                        $discounted_price = $offer['discount_type'] == 'percentage'
                            ? $offer['price'] * (1 - ($offer['discount_value'] / 100))
                            : $offer['price'] - $offer['discount_value'];
                    ?>
                        <div class="offer-item">
                            <div class="offer-image">
                                <?php if ($offer['image']): ?>
                                    <img src="images/offers/<?php echo $offer['image']; ?>" alt="<?php echo $offer['title']; ?>">
                                <?php else: ?>
                                    <img src="images/products/<?php echo $offer['product_image']; ?>" alt="<?php echo $offer['product_name']; ?>">
                                <?php endif; ?>
                                <div class="offer-badge">
                                    <?php echo $offer['discount_type'] == 'percentage'
                                        ? $offer['discount_value'] . '%'
                                        : $offer['discount_value'] . ' ريال'; ?>
                                </div>
                                <div class="time-left">
                                    <i class="fas fa-clock"></i>
                                    <?php
                                    $end = new DateTime($offer['end_date']);
                                    $now = new DateTime();
                                    $diff = $end->diff($now);
                                    echo $diff->format('%a أيام %h ساعات');
                                    ?>
                                </div>
                            </div>
                            <div class="offer-content">
                                <h3><?php echo $offer['title']; ?></h3>
                                <p class="product-name"><?php echo $offer['product_name']; ?></p>
                                <p class="offer-description"><?php echo $offer['description']; ?></p>

                                <div class="price-section">
                                    <span class="original-price"><?php echo number_format($offer['price'], 2); ?> ريال</span>
                                    <span class="discounted-price"><?php echo number_format($discounted_price, 2); ?> ريال</span>
                                </div>

                                <a href="product.php?id=<?php echo $offer['product_id']; ?>" class="btn btn-primary">
                                    عرض المنتج
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
.offers-section {
    padding: 40px 0;
}

.section-title {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.no-offers {
    text-align: center;
    padding: 50px;
    background: #f8f9fa;
    border-radius: 10px;
}

.no-offers i {
    font-size: 50px;
    color: #ddd;
    margin-bottom: 15px;
}

.no-offers p {
    color: #666;
    font-size: 1.2rem;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.offer-item {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.offer-item:hover {
    transform: translateY(-5px);
}

.offer-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.offer-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.offer-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #e74c3c;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}

.time-left {
    position: absolute;
    bottom: 10px;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px;
    text-align: center;
    font-size: 0.9rem;
}

.offer-content {
    padding: 20px;
}

.product-name {
    color: #666;
    margin: 5px 0;
}

.offer-description {
    color: #333;
    margin: 10px 0;
}

.price-section {
    margin: 15px 0;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    margin-left: 10px;
}

.discounted-price {
    font-size: 1.3rem;
    color: #e74c3c;
    font-weight: bold;
}

.btn-primary {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #2980b9;
}

@media (max-width: 768px) {
    .offers-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

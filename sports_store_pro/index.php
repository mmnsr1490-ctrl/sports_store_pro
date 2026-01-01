<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="fade-in">أفضل متجر للملابس الرياضية</h1>
            <p class="fade-in">اكتشف أحدث تشكيلة من الملابس الرياضية عالية الجودة مع تقنيات متطورة وتصاميم عصرية</p>
            <a href="products.php" class="cta-button interactive-element">
                <i class="fas fa-shopping-bag"></i>
                تسوق الآن
            </a>
            <div class="hero-stats">
                <div class="stat-item fade-in">
                    <h3>1000+</h3>
                    <p>منتج متوفر</p>
                </div>
                <div class="stat-item fade-in">
                    <h3>5000+</h3>
                    <p>عميل راضي</p>
                </div>
                <div class="stat-item fade-in">
                    <h3>24/7</h3>
                    <p>دعم فني</p>
                </div>
            </div>
        </div>
        <div class="hero-decoration">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <h2>المنتجات المميزة</h2>
            <div class="products-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 8");
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="product-card">
                    <img src="images/products/<?php echo $product['image'] ?: 'default.jpg';?> ">
                    <h3> <?php echo $product['name']; ?> </h3>
                    <p class="price"> <?php echo $product['price']; ?> ريال</p>
                    <a href="product.php?id='<?php echo $product['name']; ?>" class="btn">عرض التفاصيل</a>
                    </div>
                <?php
                }
                ?>

            </div>
        </div>
    </section>

    <!-- Categories -->
     <!-- Categories -->
<section class="categories">
    <div class="container">
        <h2>الفئات</h2>
        <div class="categories-grid">
            <?php

            try {
                // استعلام لجلب جميع الفئات
                $stmt = $pdo->query("SELECT * FROM categories");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // عرض كل فئة
                foreach ($categories as $category) {
                    $categoryName = htmlspecialchars($category['name']);
                    $categoryImage = !empty($category['image']) ? 'images/categories/' . htmlspecialchars($category['image']) : 'images/categories/default-category.jpg';
                    $categoryLink = 'products.php?category=' . urlencode($category['name'] ?? strtolower(str_replace(' ', '-', $category['name'])));

                    echo <<<HTML
                    <div class="category-card">
                        <img src="{$categoryImage}" alt="{$categoryName}">
                        <h3>{$categoryName}</h3>
                        <a href="{$categoryLink}">تسوق الآن</a>
                    </div>
                    HTML;

                }
            } catch (PDOException $e) {
                echo "<p>خطأ في جلب الفئات: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</section>
</main>

<?php include 'includes/footer.php'; ?>

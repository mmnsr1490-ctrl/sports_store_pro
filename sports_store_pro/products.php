<?php
session_start();
include 'config/database.php';
include 'includes/header.php';

$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category_filter) {
    $sql .= " AND c.name = ?";
    $params[] = $category_filter;
}

if ($search_query) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <section class="products-page">
        <div class="container">
            <div class="page-header">
                <h1>منتجاتنا</h1>
                <p class="page-subtitle">اكتشف أحدث مجموعاتنا المميزة</p>
            </div>

            <!-- Search and Filter -->
            <div class="search-filter">
                <form method="GET" class="search-form">
                    <div class="form-group search-box">
                        <input type="text" name="search" placeholder="ابحث عن منتج..."
                               value="<?php echo htmlspecialchars($search_query); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>


                    <div class="form-group">
                        <select name="category" class="custom-select">
                            <option value="">جميع الفئات</option>
                            <?php

                            try {
                                // استعلام لجلب جميع الفئات
                                $stmt = $pdo->query("SELECT id, name FROM categories");
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // عرض كل فئة كخيار في القائمة المنسدلة
                                foreach ($categories as $cat) {
                                    $selected = (isset($category_filter) && $category_filter == $cat['name']) ? 'selected' : '';
                                    echo '<option value="'.htmlspecialchars($cat['name']).'" '.$selected.'>'.htmlspecialchars($cat['name']).'</option>';
                                }
                            } catch (PDOException $e) {
                                echo "<option value=''>خطأ في جلب الفئات</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <button type="submit" class="btn btn-search">
                        <span>بحث</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                        </svg>
                        <h3>لا توجد منتجات متاحة</h3>
                        <p>حاول تغيير معايير البحث الخاصة بك</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-badge">جديد</div>
                            <div class="product-image">
                                <img src="images/products/<?php echo $product['image'] ?: 'default.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="image-overlay"></div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="quick-view">
                                    عرض سريع
                                </a>
                            </div>
                            <div class="product-content">
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-meta">
                                    <span class="product-price"><?php echo number_format($product['price'], 2); ?> ر.س</span>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn-cart">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="9" cy="21" r="1"></circle>
                                                    <circle cx="20" cy="21" r="1"></circle>
                                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<style>
:root {
    --primary-color:linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --primary-dark:linear-gradient(135deg,rgb(79, 97, 180) 0%,rgb(88, 55, 121) 100%);
    --secondary-color: #3498db;
    --secondary-dark: #2980b9;
    --success-color: #27ae60;
    --success-dark: #229954;
    --text-color: #333;
    --text-light: #666;
    --border-color: #eee;
    --bg-light: #f9f9f9;
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}

.products-page {
    padding: 3rem 0;
    background-color: var(--bg-light);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-header h1 {
    font-size: 2.5rem;
    color: var(--text-color);
    margin-bottom: 0.5rem;
    position: relative;
    display: inline-block;
}

.page-header h1:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: var(--primary-color);
}

.page-subtitle {
    font-size: 1.1rem;
    color: var(--text-light);
    margin-top: 0.5rem;
}

.search-filter {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 2.5rem;
}

.search-form {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.search-box {
    position: relative;
}

.search-box svg {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
}

.search-form input {
    width: 100%;
    padding: 0.85rem 1rem 0.85rem 2.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
    background-color: #f5f5f5;
}

.search-form input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    background-color: white;
}

.custom-select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    appearance: none;
    background-color: #f5f5f5;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
    transition: var(--transition);
}

.custom-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    background-color: white;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.85rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
}

.btn-search {
    background: var(--primary-color);
    color: white;
}

.btn-search:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem 0;
}

.no-products svg {
    margin-bottom: 1rem;
}

.no-products h3 {
    font-size: 1.5rem;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.no-products p {
    color: var(--text-light);
    font-size: 1rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

.product-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.1);
    opacity: 0;
    transition: var(--transition);
}

.quick-view {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    color: var(--text-color);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: var(--shadow-sm);
    opacity: 0;
    transition: var(--transition);
    z-index: 2;
    white-space: nowrap;
}

.product-card:hover .image-overlay {
    opacity: 1;
}

.product-card:hover .quick-view {
    bottom: 20px;
    opacity: 1;
}

.product-card:hover img {
    transform: scale(1.05);
}

.product-content {
    padding: 1.25rem;
}

.product-category {
    display: block;
    font-size: 0.8rem;
    color: var(--text-light);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-title {
    font-size: 1.1rem;
    color: var(--text-color);
    margin-bottom: 0.75rem;
    font-weight: 600;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1rem;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.add-to-cart-form {
    display: flex;
}

.btn-cart {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--success-color);
    color: white;
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-cart:hover {
    background: var(--success-dark);
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
    }

    .form-group, .search-form input, .search-form select, .btn-search {
        width: 100%;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
}

@media (max-width: 480px) {
    .page-header h1 {
        font-size: 2rem;
    }

    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

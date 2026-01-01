
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // التحقق من وجود المنتج
    $stmt = $pdo->prepare("SELECT id, stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: products.php?error=product_not_found');
        exit();
    }
    
    if ($product['stock_quantity'] < $quantity) {
        header('Location: products.php?error=insufficient_stock');
        exit();
    }
    
    // التحقق من وجود المنتج في السلة
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_item) {
        // تحديث الكمية
        $new_quantity = $existing_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $existing_item['id']]);
    } else {
        // إضافة منتج جديد
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    
    header('Location: cart.php?success=added');
    exit();
} else {
    header('Location: products.php');
    exit();
}
?>

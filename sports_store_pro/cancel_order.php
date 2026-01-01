<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'يجب تسجيل الدخول أولاً',
        'redirect' => 'login.php'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'طلب غير صالح',
        'redirect' => 'my_orders.php'
    ]);
    exit();
}

$order_id = (int)$_POST['order_id'];
$user_id = $_SESSION['user_id'];

// التحقق من أن الطلب موجود ويخص المستخدم الحالي
$stmt = $pdo->prepare("SELECT id, status FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode([
        'success' => false,
        'message' => 'الطلب غير موجود',
        'redirect' => 'my_orders.php'
    ]);
    exit();
}

if ($order['status'] !== 'pending') {
    echo json_encode([
        'success' => false,
        'message' => 'لا يمكن إلغاء الطلب في حالته الحالية',
        'redirect' => 'my_orders.php'
    ]);
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$order_id]);

    $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'تم إلغاء الطلب بنجاح',
        'redirect' => 'my_orders.php',
        'delay' => 3000
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء إلغاء الطلب: ' . $e->getMessage(),
        'redirect' => 'my_orders.php'
    ]);
}
?>

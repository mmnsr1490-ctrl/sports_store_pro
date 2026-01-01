
<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id']) && isset($_POST['action'])) {
    $cart_id = (int)$_POST['cart_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];

    // التحقق من ملكية العنصر
    $stmt = $pdo->prepare("SELECT c.*, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        if ($action == 'increase') {
            if ($cart_item['quantity'] < $cart_item['stock_quantity']) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
                $stmt->execute([$cart_id]);
            }
        } elseif ($action == 'decrease') {
            if ($cart_item['quantity'] > 1) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ?");
                $stmt->execute([$cart_id]);
            }
        }
    }
}

header('Location: cart.php');
exit();
?>

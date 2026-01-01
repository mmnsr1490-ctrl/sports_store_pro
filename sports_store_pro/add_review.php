<?php
session_start();
include 'config/database.php';

// التحقق من أن المستخدم مسجل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// التحقق من أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit();
}

// التحقق من وجود البيانات المطلوبة
if (!isset($_POST['product_id'], $_POST['rating'], $_POST['comment'])) {
    $_SESSION['review_error'] = 'جميع الحقول مطلوبة';
    header("Location: product.php?id=" . $_POST['product_id']);
    exit();
}

$product_id = (int)$_POST['product_id'];
$user_id = (int)$_SESSION['user_id'];
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

// التحقق من صحة البيانات
if ($rating < 1 || $rating > 5) {
    $_SESSION['review_error'] = 'التقييم يجب أن يكون بين 1 و 5 نجوم';
    header("Location: product.php?id=$product_id");
    exit();
}

if (empty($comment)) {
    $_SESSION['review_error'] = 'يجب كتابة تعليق';
    header("Location: product.php?id=$product_id");
    exit();
}

// التحقق من أن المنتج موجود
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    $_SESSION['review_error'] = 'المنتج غير موجود';
    header("Location: products.php");
    exit();
}

// التحقق مما إذا كان المستخدم قد قام بتقييم هذا المنتج مسبقًا
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
if ($stmt->fetch()) {
    $_SESSION['review_error'] = 'لقد قمت بتقييم هذا المنتج مسبقًا';
    header("Location: product.php?id=$product_id");
    exit();
}

// إضافة التقييم إلى قاعدة البيانات
try {
    $stmt = $pdo->prepare("
        INSERT INTO reviews (user_id, product_id, rating, comment, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $product_id, $rating, $comment]);

    $_SESSION['review_success'] = 'تم إضافة التقييم بنجاح';
    header("Location: product.php?id=$product_id");
    exit();
} catch (PDOException $e) {
    $_SESSION['review_error'] = 'حدث خطأ أثناء إضافة التقييم';
    header("Location: product.php?id=$product_id");
    exit();
}
?>

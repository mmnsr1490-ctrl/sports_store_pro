
<?php
session_start();

// حذف جميع متغيرات الجلسة
$_SESSION = array();

// حذف ملف cookie الخاص بالجلسة إذا كان موجوداً
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى الصفحة الرئيسية
header('Location: index.php');
exit();
?>

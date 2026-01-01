
<?php
session_start();
include 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // توجيه المدير للوحة الإدارة
            if ($user['is_admin']) {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}

include 'includes/header.php';
?>

<main>
    <div class="form-container">
        <h2>تسجيل الدخول</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">اسم المستخدم أو البريد الإلكتروني:</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">تسجيل الدخول</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            ليس لديك حساب؟ <a href="register.php">سجل الآن</a>
        </p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

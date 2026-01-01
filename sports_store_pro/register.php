
<?php
session_start();
include 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif ($password !== $confirm_password) {
        $error = 'كلمة المرور وتأكيد كلمة المرور غير متطابقتين';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        // التحقق من وجود اسم المستخدم أو البريد الإلكتروني
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $error = 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");

            if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول';
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب';
            }
        }
    }
}

include 'includes/header.php';
?>

<main>
    <div class="form-container">
        <h2>إنشاء حساب جديد</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">اسم المستخدم *:</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني *:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="full_name">الاسم الكامل *:</label>
                <input type="text" id="full_name" name="full_name" required
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف:</label>
                <input type="tel" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور *:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">تأكيد كلمة المرور *:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-submit">إنشاء الحساب</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            لديك حساب بالفعل؟ <a href="login.php">سجل الدخول</a>
        </p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>


<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب بيانات المستخدم
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        if (empty($full_name) || empty($email)) {
            $error = 'الاسم والبريد الإلكتروني مطلوبان';
        } else {
            // التحقق من عدم وجود البريد الإلكتروني لمستخدم آخر
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);

            if ($stmt->fetch()) {
                $error = 'البريد الإلكتروني مستخدم من قبل مستخدم آخر';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
                if ($stmt->execute([$full_name, $email, $phone, $address, $user_id])) {
                    $success = 'تم تحديث الملف الشخصي بنجاح';
                    // إعادة جلب البيانات المحدثة
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $error = 'حدث خطأ أثناء تحديث الملف الشخصي';
                }
            }
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'يرجى ملء جميع حقول كلمة المرور';
        } elseif ($new_password !== $confirm_password) {
            $error = 'كلمة المرور الجديدة وتأكيدها غير متطابقتين';
        } elseif (strlen($new_password) < 6) {
            $error = 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل';
        } elseif (!password_verify($current_password, $user['password'])) {
            $error = 'كلمة المرور الحالية غير صحيحة';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $user_id])) {
                $success = 'تم تغيير كلمة المرور بنجاح';
            } else {
                $error = 'حدث خطأ أثناء تغيير كلمة المرور';
            }
        }
    }
}

// جلب إحصائيات المستخدم
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND status != 'cancelled'");
$stmt->execute([$user_id]);
$total_spent = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchColumn();

include 'includes/header.php';
?>

<main>
    <section class="profile-page">
        <div class="container">
            <h1>الملف الشخصي</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="profile-content">
                <!-- Profile Stats -->
                <div class="profile-stats">
                    <div class="stat-card">
                        <i class="fas fa-shopping-cart"></i>
                        <div class="stat-info">
                            <h3><?php echo $total_orders; ?></h3>
                            <p>إجمالي الطلبات</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="stat-info">
                            <h3><?php echo number_format($total_spent, 2); ?> ريال</h3>
                            <p>إجمالي المبلغ المنفق</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-heart"></i>
                        <div class="stat-info">
                            <h3><?php echo $cart_items; ?></h3>
                            <p>منتجات في السلة</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Forms -->
                <div class="profile-forms">
                    <!-- Update Profile Form -->
                    <div class="form-section">
                        <h2>تحديث الملف الشخصي</h2>

                        <form method="POST" class="profile-form">
                            <div class="form-group">
                                <label for="full_name">الاسم الكامل:</label>
                                <input type="text" id="full_name" name="full_name" required
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">البريد الإلكتروني:</label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone">رقم الهاتف:</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="address">العنوان:</label>
                                <textarea id="address" name="address" rows="3"
                                          placeholder="أدخل عنوانك الكامل"><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>

                            <button type="submit" name="update_profile" class="btn-submit">
                                تحديث الملف الشخصي
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Form -->
                    <div class="form-section">
                        <h2>تغيير كلمة المرور</h2>

                        <form method="POST" class="profile-form">
                            <div class="form-group">
                                <label for="current_password">كلمة المرور الحالية:</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label for="new_password">كلمة المرور الجديدة:</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">تأكيد كلمة المرور الجديدة:</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_password" class="btn-submit">
                                تغيير كلمة المرور
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="account-actions">
                    <h2>إجراءات الحساب</h2>

                    <div class="action-buttons">
                        <a href="orders.php" class="btn btn-view">
                            <i class="fas fa-list"></i>
                            عرض الطلبات
                        </a>

                        <a href="cart.php" class="btn btn-cart">
                            <i class="fas fa-shopping-cart"></i>
                            عرض السلة
                        </a>

                        <a href="logout.php" class="btn btn-logout" onclick="return confirm('هل تريد تسجيل الخروج؟')">
                            <i class="fas fa-sign-out-alt"></i>
                            تسجيل الخروج
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.profile-page {
    padding: 2rem 0;
    min-height: 60vh;
}

.profile-page h1 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    color: #333;
}

.profile-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card i {
    font-size: 2.5rem;
    color: #e74c3c;
    width: 60px;
    text-align: center;
}

.stat-info h3 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    color: #333;
}

.stat-info p {
    color: #666;
    font-size: 0.9rem;
}

.profile-forms {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.form-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-section h2 {
    margin-bottom: 1.5rem;
    color: #333;
    font-size: 1.3rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-submit {
    background: #3498db;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #2980b9;
}

.account-actions {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.account-actions h2 {
    margin-bottom: 1.5rem;
    color: #333;
    font-size: 1.3rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-view {
    background: #3498db;
    color: white;
}

.btn-view:hover {
    background: #2980b9;
}

.btn-cart {
    background: #27ae60;
    color: white;
}

.btn-cart:hover {
    background: #229954;
}

.btn-logout {
    background: #e74c3c;
    color: white;
}

.btn-logout:hover {
    background: #c0392b;
}

@media (max-width: 768px) {
    .profile-stats {
        grid-template-columns: 1fr;
    }

    .profile-forms {
        grid-template-columns: 1fr;
    }

    .stat-card {
        flex-direction: column;
        text-align: center;
    }

    .stat-card i {
        width: auto;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

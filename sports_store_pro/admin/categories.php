<?php
session_start();
include '../config/database.php';

// التحقق من صلاحية المدير
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// معالجة العمليات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        // دالة لمعالجة رفع الصورة
        function handleImageUpload($existing_image = null) {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../images/categories/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['image']['type'];

                if (!in_array($file_type, $allowed_types)) {
                    return ['error' => 'نوع الملف غير مسموح به. يرجى رفع صورة (JPEG, PNG, GIF)'];
                }

                $file_name = $_FILES['image']['name']; // الحصول على اسم الملف الأصلي مع الامتداد
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    // حذف الصورة القديمة إذا كانت موجودة
                    if ($existing_image && file_exists($upload_dir . $existing_image)) {
                        unlink($upload_dir . $existing_image);
                    }
                    return ['success' => $file_name]; // إرجاع اسم الملف فقط
                } else {
                    return ['error' => 'حدث خطأ أثناء رفع الملف'];
                }
            }
            return ['success' => $existing_image]; // إذا لم يتم رفع صورة جديدة
        }

        switch ($_POST['action']) {
            case 'add':
                $image_result = handleImageUpload();
                if (isset($image_result['error'])) {
                    $error = $image_result['error'];
                    break;
                }

                $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $image_result['success'] // حفظ اسم الملف فقط
                ]);
                $success = "تم إضافة الفئة بنجاح";
                break;

            case 'edit':
                // جلب الصورة الحالية
                $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                $stmt->execute([$_POST['category_id']]);
                $current_image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];

                $image_result = handleImageUpload($current_image);
                if (isset($image_result['error'])) {
                    $error = $image_result['error'];
                    break;
                }

                $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $image_result['success'], // حفظ اسم الملف فقط
                    $_POST['category_id']
                ]);
                $success = "تم تحديث الفئة بنجاح";
                break;

            case 'delete':
                // جلب الصورة لحذفها
                $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                $stmt->execute([$_POST['category_id']]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);

                // التحقق من وجود منتجات في هذه الفئة
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
                $stmt->execute([$_POST['category_id']]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                if ($count > 0) {
                    $error = "لا يمكن حذف الفئة لأنها تحتوي على منتجات";
                } else {
                    // حذف الصورة المرتبطة
                    if ($category['image'] && file_exists('../images/categories/' . $category['image'])) {
                        unlink('../images/categories/' . $category['image']);
                    }

                    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                    $stmt->execute([$_POST['category_id']]);
                    $success = "تم حذف الفئة بنجاح";
                }
                break;
        }
    }
}

// جلب الفئات
$stmt = $pdo->query("
    SELECT c.*,
           COUNT(p.id) as products_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// الفئة للتعديل
$edit_category = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>




<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الفئات</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> المنتجات</a></li>
                <li><a href="offers.php"><i class="fas fa-percentage"></i> العروض</a></li>
                <li><a href="categories.php" class="active"><i class="fas fa-tags"></i> الفئات</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> المستخدمين</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../index.php"><i class="fas fa-eye"></i> عرض المتجر</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>إدارة الفئات</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add/Edit Category Form -->
            <div class="content-section">
                <h2><?php echo $edit_category ? 'تعديل الفئة' : 'إضافة فئة جديدة'; ?></h2>
                <form method="POST" class="form-grid" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>اسم الفئة</label>
                        <input type="text" name="name" value="<?php echo $edit_category['name'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>صورة الفئة</label>
                        <input type="file" name="image" accept="image/*" class="file-input">
                        <?php if ($edit_category && $edit_category['image']): ?>
                            <div class="current-image">
                                <img src="../<?php echo $edit_category['image']; ?>" alt="الصورة الحالية" style="max-width: 100px; margin-top: 10px;">
                                <p><?php echo basename($edit_category['image']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>الوصف</label>
                        <textarea name="description" rows="4"><?php echo $edit_category['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-sm btn-success">
                            <?php echo $edit_category ? 'تحديث الفئة' : 'إضافة الفئة'; ?>
                        </button>
                        <?php if ($edit_category): ?>
                            <a href="categories.php" class="btn-sm btn-warning">إلغاء</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Categories List -->
            <div class="content-section">
                <h2>قائمة الفئات</h2>

                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                    <div class="category-admin-card">
                        <div class="category-image">
                            <?php if ($category['image']): ?>
                                <img src="../images/categories/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <p>لا توجد صورة</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="category-info">
                            <h3><?php echo $category['name']; ?></h3>
                            <p><?php echo $category['description'] ?: 'لا يوجد وصف'; ?></p>
                            <div class="category-stats">
                                <span><i class="fas fa-box"></i> <?php echo $category['products_count']; ?> منتج</span>
                            </div>
                        </div>

                        <div class="category-actions">
                            <a href="categories.php?edit=<?php echo $category['id']; ?>" class="btn-sm btn-warning">
                                <i class="fas fa-edit"></i> تعديل
                            </a>

                            <?php if ($category['products_count'] == 0): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn-sm btn-danger" disabled title="لا يمكن حذف الفئة لوجود منتجات بها">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <style>
    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 5px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .category-admin-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }

    .category-image {
        height: 150px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        text-align: center;
        color: #6c757d;
    }

    .no-image i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .category-info {
        padding: 1rem;
    }

    .category-info h3 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
    }

    .category-info p {
        color: #6c757d;
        margin: 0 0 1rem 0;
        font-size: 0.9rem;
    }

    .category-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: #007bff;
    }

    .category-actions {
        padding: 1rem;
        background: #f8f9fa;
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-sm:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .file-input {
        display: block;
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .current-image {
        margin-top: 10px;
        text-align: center;
    }

    .current-image img {
        border: 1px solid #eee;
        border-radius: 4px;
    }

    .current-image p {
        margin: 5px 0 0;
        font-size: 0.8rem;
        color: #666;
    }
    </style>
</body>
</html>

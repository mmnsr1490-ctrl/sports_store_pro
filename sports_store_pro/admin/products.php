
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
        switch ($_POST['action']) {
            case 'add':
                $image_name = null;

                // معالجة رفع الصورة
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = $_FILES['image']['type'];

                    if (in_array($file_type, $allowed_types)) {
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $image_name = uniqid() . '.' . $file_extension;
                        $upload_path = '../images/products/' . $image_name;

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $error = "فشل في رفع الصورة";
                            break;
                        }
                    } else {
                        $error = "نوع الملف غير مدعوم. يرجى استخدام صور من نوع JPG, PNG, GIF, أو WebP";
                        break;
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock_quantity, featured, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['category_id'],
                    $_POST['stock_quantity'],
                    isset($_POST['featured']) ? 1 : 0,
                    $image_name
                ]);
                $success = "تم إضافة المنتج بنجاح";
                break;

            case 'edit':
                $image_name = $_POST['current_image'];

                // معالجة رفع صورة جديدة
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = $_FILES['image']['type'];

                    if (in_array($file_type, $allowed_types)) {
                        // حذف الصورة القديمة
                        if ($image_name && file_exists('../images/products/' . $image_name)) {
                            unlink('../images/products/' . $image_name);
                        }

                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $image_name = uniqid() . '.' . $file_extension;
                        $upload_path = '../images/products/' . $image_name;

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $error = "فشل في رفع الصورة";
                            break;
                        }
                    } else {
                        $error = "نوع الملف غير مدعوم. يرجى استخدام صور من نوع JPG, PNG, GIF, أو WebP";
                        break;
                    }
                }

                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock_quantity = ?, featured = ?, image = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['category_id'],
                    $_POST['stock_quantity'],
                    isset($_POST['featured']) ? 1 : 0,
                    $image_name,
                    $_POST['product_id']
                ]);
                $success = "تم تحديث المنتج بنجاح";
                break;

            case 'delete':
                // جلب اسم الصورة قبل الحذف
                $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->execute([$_POST['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // حذف الصورة من الخادم
                if ($product && $product['image'] && file_exists('../images/products/' . $product['image'])) {
                    unlink('../images/products/' . $product['image']);
                }

                $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
                $stmt->execute([$_POST['product_id']]);

                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$_POST['product_id']]);
                $success = "تم حذف المنتج بنجاح";
                break;
        }
    }
}

// جلب المنتجات
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

if ($category_filter) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$query .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب الفئات
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// المنتج للتعديل
$edit_product = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/productsstyle.css">
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
                <li><a href="products.php" class="active"><i class="fas fa-box"></i> المنتجات</a></li>
                <li><a href="offers.php"><i class="fas fa-percentage"></i> العروض</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> الفئات</a></li>
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
                <h1>إدارة المنتجات</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add/Edit Product Form -->
            <div class="content-section">
                <h2><?php echo $edit_product ? 'تعديل المنتج' : 'إضافة منتج جديد'; ?></h2>
                <form method="POST" enctype="multipart/form-data" class="form-grid">
                    <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'add'; ?>">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $edit_product['image']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>اسم المنتج</label>
                        <input type="text" name="name" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>الفئة</label>
                        <select name="category_id" required>
                            <option value="">اختر الفئة</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>السعر (ريال)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>الكمية المتوفرة</label>
                        <input type="number" name="stock_quantity" value="<?php echo $edit_product['stock_quantity'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>الوصف</label>
                        <textarea name="description" rows="4"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>صورة المنتج</label>
                        <?php if ($edit_product && $edit_product['image']): ?>
                            <div class="current-image">
                                <img src="../images/products/<?php echo $edit_product['image']; ?>" alt="الصورة الحالية" style="max-width: 200px; max-height: 200px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;">
                                <p>الصورة الحالية</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*" class="file-input">
                        <small>أنواع الملفات المدعومة: JPG, PNG, GIF, WebP</small>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" <?php echo ($edit_product && $edit_product['featured']) ? 'checked' : ''; ?>>
                            منتج مميز
                        </label>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-sm btn-success">
                            <?php echo $edit_product ? 'تحديث المنتج' : 'إضافة المنتج'; ?>
                        </button>
                        <?php if ($edit_product): ?>
                            <a href="products.php" class="btn-sm btn-warning">إلغاء</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Products List -->
            <div class="content-section">
                <div class="actions-bar">
                    <h2>قائمة المنتجات</h2>
                    <div class="search-box">
                        <form method="GET" style="display: flex; gap: 0.5rem;">
                            <input type="text" name="search" placeholder="البحث في المنتجات..." value="<?php echo $search; ?>">
                            <select name="category">
                                <option value="">جميع الفئات</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                        <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-sm btn-primary">بحث</button>
                        </form>
                    </div>
                </div>

                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>الرقم</th>
                                <th>الصورة</th>
                                <th>الاسم</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                                <th>مميز</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="../images/products/<?php echo $product['image']; ?>"
                                             alt="<?php echo $product['name']; ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <span style="color: #999;">لا توجد صورة</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo number_format($product['price'], 2); ?> ريال</td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td><?php echo $product['featured'] ? 'نعم' : 'لا'; ?></td>
                                <td>
                                    <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn-sm btn-warning">تعديل</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>

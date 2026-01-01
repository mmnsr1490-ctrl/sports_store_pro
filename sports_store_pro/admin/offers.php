<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// معالجة العمليات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function handleImageUpload($existing_image = null) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/offers/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];

            if (!in_array($file_type, $allowed_types)) {
                return ['error' => 'نوع الملف غير مسموح به'];
            }

            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('offer_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                if ($existing_image && file_exists($upload_dir . $existing_image)) {
                    unlink($upload_dir . $existing_image);
                }
                return ['success' => $file_name];
            }
        }
        return ['success' => $existing_image];
    }

    $action = $_POST['action'];
    $image_result = ['success' => null];

    if ($action != 'delete' && isset($_FILES['image'])) {
        $image_result = handleImageUpload($_POST['existing_image'] ?? null);
    }

    if (isset($image_result['error'])) {
        $error = $image_result['error'];
    } else {
        try {
            switch ($action) {
                case 'add':
                    $stmt = $pdo->prepare("INSERT INTO offers (product_id, title, description, discount_type, discount_value, start_date, end_date, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['product_id'],
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['discount_type'],
                        $_POST['discount_value'],
                        $_POST['start_date'],
                        $_POST['end_date'],
                        $image_result['success']
                    ]);
                    $success = "تم إضافة العرض بنجاح";
                    break;

                case 'edit':
                    $stmt = $pdo->prepare("UPDATE offers SET product_id = ?, title = ?, description = ?, discount_type = ?, discount_value = ?, start_date = ?, end_date = ?, image = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['product_id'],
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['discount_type'],
                        $_POST['discount_value'],
                        $_POST['start_date'],
                        $_POST['end_date'],
                        $image_result['success'],
                        $_POST['offer_id']
                    ]);
                    $success = "تم تحديث العرض بنجاح";
                    break;

                case 'delete':
                    $stmt = $pdo->prepare("SELECT image FROM offers WHERE id = ?");
                    $stmt->execute([$_POST['offer_id']]);
                    $offer = $stmt->fetch();

                    if ($offer['image'] && file_exists('../images/offers/' . $offer['image'])) {
                        unlink('../images/offers/' . $offer['image']);
                    }

                    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
                    $stmt->execute([$_POST['offer_id']]);
                    $success = "تم حذف العرض بنجاح";
                    break;
            }
        } catch (PDOException $e) {
            $error = "حدث خطأ في قاعدة البيانات: " . $e->getMessage();
        }
    }
}

// جلب العروض
$stmt = $pdo->query("
    SELECT o.*, p.name as product_name
    FROM offers o
    LEFT JOIN products p ON o.product_id = p.id
    ORDER BY o.start_date DESC
");
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// جلب المنتجات للقائمة المنسدلة
$products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// العرض للتعديل
$edit_offer = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_offer = $stmt->fetch(PDO::FETCH_ASSOC);
}

include 'includes/admin_header.php';
?>

<div class="admin-container">
    <h1><i class="fas fa-percentage"></i> إدارة العروض</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="content-section">
        <h2><?php echo $edit_offer ? 'تعديل العرض' : 'إضافة عرض جديد'; ?></h2>
        <form method="POST" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $edit_offer ? 'edit' : 'add'; ?>">
            <?php if ($edit_offer): ?>
                <input type="hidden" name="offer_id" value="<?php echo $edit_offer['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $edit_offer['image']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>المنتج</label>
                <select name="product_id" required>
                    <option value="">اختر منتج</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo ($edit_offer && $edit_offer['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                            <?php echo $product['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>عنوان العرض</label>
                <input type="text" name="title" value="<?php echo $edit_offer['title'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>صورة العرض</label>
                <input type="file" name="image" accept="image/*">
                <?php if ($edit_offer && $edit_offer['image']): ?>
                    <div class="current-image">
                        <img src="../images/offers/<?php echo $edit_offer['image']; ?>" alt="صورة العرض الحالية" style="max-width: 100px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>نوع الخصم</label>
                <select name="discount_type" required>
                    <option value="percentage" <?php echo ($edit_offer && $edit_offer['discount_type'] == 'percentage') ? 'selected' : ''; ?>>نسبة مئوية %</option>
                    <option value="fixed" <?php echo ($edit_offer && $edit_offer['discount_type'] == 'fixed') ? 'selected' : ''; ?>>مبلغ ثابت</option>
                </select>
            </div>

            <div class="form-group">
                <label>قيمة الخصم</label>
                <input type="number" name="discount_value" step="0.01" min="0"
                       value="<?php echo $edit_offer['discount_value'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>تاريخ البدء</label>
                <input type="datetime-local" name="start_date"
                       value="<?php echo $edit_offer ? str_replace(' ', 'T', substr($edit_offer['start_date'], 0, 16)) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>تاريخ الانتهاء</label>
                <input type="datetime-local" name="end_date"
                       value="<?php echo $edit_offer ? str_replace(' ', 'T', substr($edit_offer['end_date'], 0, 16)) : ''; ?>" required>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>وصف العرض</label>
                <textarea name="description" rows="4"><?php echo $edit_offer['description'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_offer ? 'تحديث العرض' : 'إضافة العرض'; ?>
                </button>
                <?php if ($edit_offer): ?>
                    <a href="offers.php" class="btn btn-secondary">إلغاء</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="content-section">
        <h2>العروض الحالية</h2>
        <div class="offers-grid">
            <?php foreach ($offers as $offer): ?>
                <div class="offer-card">
                    <div class="offer-image">
                        <?php if ($offer['image']): ?>
                            <img src="../images/offers/<?php echo $offer['image']; ?>" alt="<?php echo $offer['title']; ?>">
                        <?php else: ?>
                            <div class="no-image"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="offer-details">
                        <h3><?php echo $offer['title']; ?></h3>
                        <p><?php echo $offer['product_name'] ?? 'لا يوجد منتج'; ?></p>
                        <div class="offer-meta">
                            <span class="discount-badge">
                                <?php echo $offer['discount_type'] == 'percentage'
                                    ? $offer['discount_value'] . '%'
                                    : $offer['discount_value'] . ' ريال'; ?>
                            </span>
                            <span class="dates">
                                <?php echo date('Y-m-d', strtotime($offer['start_date'])); ?> إلى
                                <?php echo date('Y-m-d', strtotime($offer['end_date'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="offer-actions">
                        <a href="offers.php?edit=<?php echo $offer['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا العرض؟')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.offer-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.offer-card:hover {
    transform: translateY(-5px);
}

.offer-image {
    height: 150px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.offer-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    color: #ccc;
    font-size: 3rem;
}

.offer-details {
    padding: 15px;
}

.offer-details h3 {
    margin: 0 0 5px 0;
    color: #333;
}

.offer-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 0.9rem;
}

.discount-badge {
    background: #e74c3c;
    color: white;
    padding: 3px 10px;
    border-radius: 15px;
    font-weight: bold;
}

.dates {
    color: #666;
}

.offer-actions {
    padding: 10px;
    background: #f8f9fa;
    display: flex;
    gap: 5px;
}

.current-image {
    margin-top: 10px;
}

.current-image img {
    border: 1px solid #ddd;
    border-radius: 4px;
}
</style>

<?php include 'includes/admin_footer.php'; ?>

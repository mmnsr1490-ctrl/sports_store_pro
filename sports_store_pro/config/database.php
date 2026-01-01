
<?php
$host = '';
$dbname = 'sports_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// إنشاء الجداول إذا لم تكن موجودة
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category_id INT,
        stock_quantity INT DEFAULT 0,
        featured BOOLEAN DEFAULT FALSE,
        sizes VARCHAR(255),
        colors VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )",

    "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        quantity INT DEFAULT 1,
        size VARCHAR(20),
        color VARCHAR(20),
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )",

    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50),
        payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
        shipping_address TEXT,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )",

    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        size VARCHAR(20),
        color VARCHAR(20),
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )",

    "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50),
        transaction_id VARCHAR(100),
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )",

    "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )"
];

foreach ($tables as $table) {
    try {
        $pdo->exec($table);
    } catch(PDOException $e) {
        echo "Error creating table: " . $e->getMessage() . "\n";
    }
}










// إنشاء جدول إعدادات الموقع
$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// إنشاء جدول الكوبونات
$pdo->exec("CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    valid_from DATE,
    valid_until DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// إنشاء جدول تقييمات المنتجات
$pdo->exec("CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// إنشاء جدول الأنشطة (Activity Log)
$pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)");


// إعدادات الموقع الافتراضية
$pdo->exec("INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type) VALUES
    ('site_name', 'متجر الملابس الرياضية', 'text'),
    ('site_description', 'أفضل متجر للملابس الرياضية عالية الجودة', 'text'),
    ('contact_email', 'info@store.com', 'text'),
    ('contact_phone', '+966123456789', 'text'),
    ('free_shipping_threshold', '200', 'number'),
    ('tax_rate', '15', 'number'),
    ('maintenance_mode', 'false', 'boolean')");



// إدراج بيانات تجريبية للفئات
$stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count == 0) {
    $categories = [
        ['ملابس رجالية', 'ملابس رياضية للرجال عالية الجودة', 'men-clothing.jpg'],
        ['ملابس نسائية', 'ملابس رياضية للنساء مريحة وأنيقة', 'women-clothing.jpg'],
        ['أحذية رياضية', 'أحذية رياضية للجري والتمارين', 'sports-shoes.jpg'],
        ['إكسسوارات', 'إكسسوارات رياضية متنوعة', 'accessories.jpg']
    ];

    $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }
}

// إدراج بيانات تجريبية للمنتجات
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products");
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count == 0) {
    $products = [
        ['تيشيرت رياضي للرجال', 'تيشيرت مريح للتمارين الرياضية', 89.99, 'men-tshirt.jpg', 1, 50, 1, 'S,M,L,XL', 'أبيض,أسود,أزرق'],
        ['شورت رياضي للرجال', 'شورت مريح للجري والتمارين', 79.99, 'men-shorts.jpg', 1, 30, 0, 'S,M,L,XL', 'أسود,رمادي,أزرق'],
        ['ليقينز رياضية للنساء', 'ليقينز مرنة ومريحة للتمارين', 129.99, 'women-leggings.jpg', 2, 40, 1, 'XS,S,M,L', 'أسود,رمادي,وردي'],
        ['تيشيرت رياضي للنساء', 'تيشيرت مريح وأنيق للتمارين', 99.99, 'women-tshirt.jpg', 2, 35, 0, 'XS,S,M,L', 'أبيض,وردي,بنفسجي'],
        ['حذاء جري للرجال', 'حذاء جري مريح وخفيف', 299.99, 'men-running-shoes.jpg', 3, 25, 1, '40,41,42,43,44', 'أسود,أبيض,أزرق'],
        ['حذاء جري للنساء', 'حذاء جري مريح وأنيق', 279.99, 'women-running-shoes.jpg', 3, 20, 0, '36,37,38,39,40', 'أبيض,وردي,رمادي'],
        ['حقيبة رياضية', 'حقيبة واسعة للأدوات الرياضية', 149.99, 'sports-bag.jpg', 4, 15, 0, 'واحد', 'أسود,أزرق,أحمر'],
        ['ساعة رياضية ذكية', 'ساعة ذكية لتتبع التمارين', 599.99, 'smart-watch.jpg', 4, 10, 1, 'واحد', 'أسود,أبيض,ذهبي']
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category_id, stock_quantity, featured, sizes, colors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute($product);
    }
}

// إنشاء مستخدم أدمن افتراضي
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
$stmt->execute();
$admin_count = $stmt->fetchColumn();

if ($admin_count == 0) {
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, is_admin) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@sportsstore.com', $admin_password, 'مدير النظام', 1]);
}
?>

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 24 يونيو 2025 الساعة 22:12
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sports_store`
--

-- --------------------------------------------------------

--
-- بنية الجدول `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `size` varchar(20) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'ملابس رجالية', 'ملابس رياضية للرجال عالية الجودة', 'تنزيل (19).jpg', '2025-06-20 21:09:32'),
(2, 'ملابس نسائية', 'ملابس رياضية للنساء مريحة وأنيقة', 'LNDR, from C38.jpg', '2025-06-20 21:09:32'),
(3, 'أحذية رياضية', 'أحذية رياضية للجري والتمارين', 'cat_6859c435e5f9e.jpg', '2025-06-20 21:09:32'),
(4, 'إكسسوارات', 'إكسسوارات رياضية متنوعة', 'Barça.jpg', '2025-06-20 21:09:32'),
(6, 'مستلزمات الملاعب', NULL, 'Rule the small football courts ?.jpg', '2025-06-23 09:34:22'),
(7, 'الكرات', NULL, 'تنزيل (16).jpg', '2025-06-23 15:02:18'),
(8, 'ادوات التمارين', 'كل احتياجات التمارين تجدونها هنا', 'cat_6859c40cc39dd.jpg', '2025-06-23 21:15:56');

-- --------------------------------------------------------

--
-- بنية الجدول `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `offers`
--

INSERT INTO `offers` (`id`, `product_id`, `title`, `description`, `discount_type`, `discount_value`, `start_date`, `end_date`, `is_active`, `image`) VALUES
(1, 5, 'خصم يصل ل 50%', 'لا تفوت العرض', 'percentage', 10.00, '2025-06-24 01:01:00', '2025-06-30 01:01:00', 1, 'offer_6859dac130640.jpg'),
(2, 6, 'سارع بالشراء', 'الكميه محدوده', 'percentage', 10.00, '2025-06-24 01:01:00', '2025-06-30 01:01:00', 1, 'offer_6859db1bbfb11.jpg'),
(3, 14, 'سارع بالشراء', 'لا تفوت العرض الكميه محدوده', 'percentage', 15.00, '2025-06-23 01:01:00', '2025-06-30 01:01:00', 1, 'offer_6859dc2f8fc9d.jpg');

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `payment_status`, `shipping_address`, `order_date`) VALUES
(1, 1, 2399.96, 'processing', 'cash', 'pending', 'ءئؤ', '2025-06-20 21:10:32'),
(2, 1, 50000.00, 'delivered', 'cash', 'pending', 'iieiei', '2025-06-21 12:16:58'),
(3, 2, 15099.99, 'shipped', 'cash', 'pending', 'اب', '2025-06-23 09:37:58'),
(4, 1, 50000.00, 'delivered', 'cash', 'pending', 'اب', '2025-06-23 14:58:51'),
(5, 1, 900000.00, 'cancelled', 'cash', 'pending', 'اب', '2025-06-23 14:59:55'),
(6, 1, 5000.00, 'shipped', 'cash', 'pending', 'اب', '2025-06-23 19:58:21'),
(7, 1, 16000.00, 'cancelled', 'cash', 'pending', 'اق', '2025-06-23 20:26:38'),
(8, 1, 100000.00, 'cancelled', 'cash', 'pending', 'ي', '2025-06-23 20:39:54'),
(9, 1, 100000.00, 'cancelled', 'cash', 'pending', 'ثصث', '2025-06-23 20:56:04'),
(10, 1, 100000.00, 'cancelled', 'cash', 'pending', 'ث', '2025-06-23 20:57:05'),
(11, 1, 8000.00, 'cancelled', 'cash', 'pending', 'ي', '2025-06-23 20:57:15'),
(12, 1, 8000.00, 'cancelled', 'cash', 'pending', 'يسيي', '2025-06-23 20:58:32'),
(13, 1, 1119.96, 'pending', 'cash', 'pending', 'ibb', '2025-06-24 13:47:21');

-- --------------------------------------------------------

--
-- بنية الجدول `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`, `color`) VALUES
(1, 1, 8, 4, 599.99, NULL, NULL),
(2, 2, 9, 10, 5000.00, NULL, NULL),
(3, 3, 9, 3, 5000.00, NULL, NULL),
(4, 3, 4, 1, 99.99, NULL, NULL),
(5, 4, 9, 3, 5000.00, NULL, NULL),
(6, 4, 11, 7, 5000.00, NULL, NULL),
(7, 5, 13, 9, 100000.00, NULL, NULL),
(9, 7, 14, 2, 8000.00, NULL, NULL),
(10, 8, 13, 1, 100000.00, NULL, NULL),
(11, 9, 13, 1, 100000.00, NULL, NULL),
(12, 10, 13, 1, 100000.00, NULL, NULL),
(13, 11, 14, 1, 8000.00, NULL, NULL),
(14, 12, 14, 1, 8000.00, NULL, NULL),
(15, 13, 6, 4, 279.99, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `sizes` varchar(255) DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `stock_quantity`, `featured`, `sizes`, `colors`, `created_at`) VALUES
(1, 'تيشيرت رياضي للرجال', 'تيشيرت مريح للتمارين الرياضية', 89.99, '6859cd8350780.jpg', 1, 50, 1, 'S,M,L,XL', 'أبيض,أسود,أزرق', '2025-06-20 21:09:32'),
(2, 'شورت رياضي للرجال', 'شورت مريح للجري والتمارين', 79.99, '6859ce01ea5f2.jpg', 1, 30, 0, 'S,M,L,XL', 'أسود,رمادي,أزرق', '2025-06-20 21:09:32'),
(3, 'ليقينز رياضية للنساء', 'ليقينز مرنة ومريحة للتمارين', 129.99, '6859ceeeb7aa2.jpg', 2, 40, 0, 'XS,S,M,L', 'أسود,رمادي,وردي', '2025-06-20 21:09:32'),
(4, 'تيشيرت رياضي للنساء', 'تيشيرت مريح وأنيق للتمارين', 99.99, '6859cdee3ad24.jpg', 2, 34, 0, 'XS,S,M,L', 'أبيض,وردي,بنفسجي', '2025-06-20 21:09:32'),
(5, 'حذاء جري للرجال', 'حذاء جري مريح وخفيف', 299.99, '6859cdc059f8a.jpg', 3, 25, 1, '40,41,42,43,44', 'أسود,أبيض,أزرق', '2025-06-20 21:09:32'),
(6, 'حذاء جري للنساء', 'حذاء جري مريح وأنيق', 279.99, '6859cde06e383.jpg', 3, 16, 0, '36,37,38,39,40', 'أبيض,وردي,رمادي', '2025-06-20 21:09:32'),
(7, 'حقيبة رياضية', 'حقيبة واسعة للأدوات الرياضية', 149.99, '6859c9381ed6b.jpg', 4, 15, 0, 'واحد', 'أسود,أزرق,أحمر', '2025-06-20 21:09:32'),
(8, 'ساعة رياضية ذكية', 'ساعة ذكية لتتبع التمارين', 599.99, '6859cdae7a873.jpg', 4, 6, 1, 'واحد', 'أسود,أبيض,ذهبي', '2025-06-20 21:09:32'),
(9, 'قميص برشلونه الاسود 2025', 'قميص برشلنه اخر موسم 2025 القميص الاسود', 5000.00, '6855e56a4f472.jpg', 1, 84, 1, NULL, NULL, '2025-06-20 22:49:14'),
(11, 'طقم برشلونه الاسود', 'من اجمل الاطقم لدينا', 5000.00, '6859687e4ddd5.jpg', 1, 25, 1, NULL, NULL, '2025-06-23 14:45:18'),
(12, 'طقم ريال مدريد', 'طقم ريال مدريد الموسم الاخير', 5000.00, '685968cbc27cf.jpg', 1, 20, 1, NULL, NULL, '2025-06-23 14:46:35'),
(13, 'طقم كامل جميل وانيق', 'طقم كامل بالوان متناسقه وجميله', 100000.00, '6859692feb611.jpg', 1, 14, 1, NULL, NULL, '2025-06-23 14:48:15'),
(14, 'حذاء اسود', 'حذا انيق وجميل مع الوان عصرية', 8000.00, '68596ccceb35c.jpg', 3, 30, 1, NULL, NULL, '2025-06-23 15:03:40');

-- --------------------------------------------------------

--
-- بنية الجدول `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 14, 5, 'جميل جداً', '2025-06-23 20:25:33'),
(2, 1, 13, 5, 'هذا جميل جدا', '2025-06-23 20:39:38');

-- --------------------------------------------------------

--
-- بنية الجدول `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(4, 'store_phone', '+966501234567', '2025-06-20 21:45:26', '2025-06-20 21:45:26'),
(5, 'store_address', 'الرياض، المملكة العربية السعودية', '2025-06-20 21:45:26', '2025-06-20 21:45:26'),
(6, 'currency', 'ريال يمني', '2025-06-20 21:45:26', '2025-06-23 15:07:07'),
(7, 'tax_rate', '', '2025-06-20 21:45:26', '2025-06-20 22:19:56'),
(8, 'shipping_cost', '', '2025-06-20 21:45:26', '2025-06-20 22:19:56'),
(9, 'free_shipping_threshold', '', '2025-06-20 21:45:26', '2025-06-20 22:19:56'),
(10, 'allow_registration', '0', '2025-06-20 21:45:26', '2025-06-23 15:07:07'),
(11, 'maintenance_mode', '0', '2025-06-20 21:45:26', '2025-06-20 22:11:13'),
(34, 'site_name', 'FitWare', '2025-06-20 22:11:13', '2025-06-23 15:07:07'),
(35, 'site_description', 'متجر مختص لجميع المستلزمات الرياضيه', '2025-06-20 22:11:13', '2025-06-23 15:07:07'),
(36, 'site_email', 'fitware@gmail.com', '2025-06-20 22:11:13', '2025-06-23 15:07:07'),
(37, 'site_phone', '781332606', '2025-06-20 22:11:13', '2025-06-23 15:07:07'),
(38, 'site_address', 'الجمهوريه اليمنيه, اب', '2025-06-20 22:11:13', '2025-06-23 15:07:07');

-- --------------------------------------------------------

--
-- بنية الجدول `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'متجر الملابس الرياضية', 'text', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(2, 'site_description', 'أفضل متجر للملابس الرياضية عالية الجودة', 'text', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(3, 'contact_email', 'info@store.com', 'text', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(4, 'contact_phone', '+966123456789', 'text', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(5, 'free_shipping_threshold', '200', 'number', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(6, 'tax_rate', '15', 'number', '2025-06-20 21:45:01', '2025-06-20 21:45:01'),
(7, 'maintenance_mode', 'false', 'boolean', '2025-06-20 21:45:01', '2025-06-20 21:45:01');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@sportsstore.com', '$2y$10$d6.G/0S2/iTP10/rNqTe1u7yo87fit8LxZKULbGPLZJfDKLkWC8Ty', 'مدير النظام', NULL, NULL, 1, '2025-06-20 21:09:32'),
(2, 'aboud', 'aboud@gmail.com', '$2y$10$6LiddcwoDVs1eiYnyCWOveJdVbkykGODqklSYm5nxJzPCjTjXhwfq', 'عبدالمجيد محمد ناجي العثماني', '781332606', NULL, 0, '2025-06-21 14:24:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15556;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- قيود الجداول `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- قيود الجداول `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- قيود الجداول `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- قيود الجداول `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

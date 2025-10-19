-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2025 at 07:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jewelkart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(166, 32, 2, 1, '2025-09-03 10:54:37'),
(167, 32, 3, 1, '2025-09-03 10:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'AYAN RAJU SAYYED', 'sayadesmail8@gmail.com', 'hii', '2025-08-30 13:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `razorpay_payment_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `created_at`, `status`, `payment_status`, `payment_method`, `razorpay_payment_id`) VALUES
(38, 1, 38599.00, '2025-08-31 00:15:22', 'Delivered', 'Paid', 'UPI', 'pay_RBjxVzyW5eovNe'),
(39, 1, 38599.00, '2025-08-31 00:19:38', 'Pending', 'Pending', 'UPI', NULL),
(40, 1, 38599.00, '2025-08-31 00:41:44', 'Cancelled', 'Paid', 'UPI', 'pay_RBkQhZBd8j2eFD'),
(41, 1, 38599.00, '2025-08-31 00:44:04', 'Delivered', 'Pending', 'Online payment', NULL),
(42, 1, 38599.00, '2025-08-31 00:50:45', 'Shipped', 'Pending', 'Online Payment', NULL),
(43, 1, 38599.00, '2025-08-31 00:52:41', 'Processing', 'Paid', 'UPI', 'pay_RBkcG1dK9mugBL'),
(44, 1, 532542.00, '2025-08-31 10:34:15', 'Delivered', 'Pending', 'Cash on Delivery', NULL),
(45, 1, 38599.00, '2025-08-31 18:21:45', 'Delivered', 'Pending', 'Cash on Delivery', NULL),
(46, 1, 38599.00, '2025-08-31 18:57:01', 'Pending', 'Paid', 'Online Payment', NULL),
(47, 1, 50.00, '2025-08-31 19:02:12', 'Pending', 'Paid', 'Online Payment', NULL),
(48, 1, 50.00, '2025-08-31 19:04:07', 'Pending', 'Paid', 'Online Payment', NULL),
(49, 1, 50.00, '2025-08-31 19:15:53', 'Pending', 'Paid', 'Online Payment', NULL),
(50, 1, 30599.00, '2025-08-31 21:29:06', 'Pending', 'Paid', 'Online Payment', NULL),
(51, 1, 50.00, '2025-09-01 20:04:03', 'Pending', 'Paid', 'Online Payment', NULL),
(52, 1, 30599.00, '2025-09-01 20:39:32', 'Pending', 'Paid', 'Online Payment', NULL),
(53, 1, 30599.00, '2025-09-02 08:02:45', 'Pending', 'Paid', 'Online Payment', NULL),
(54, 1, 30599.00, '2025-09-02 08:09:23', 'Pending', 'Paid', 'Online Payment', NULL),
(55, 1, 30599.00, '2025-09-05 08:06:17', 'Pending', 'Paid', 'Online Payment', NULL),
(56, 1, 30599.00, '2025-09-07 08:33:53', 'Pending', 'Paid', 'Online Payment', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(46, 38, 16, 1, 38599.00),
(47, 39, 16, 1, 38599.00),
(48, 40, 16, 1, 38599.00),
(49, 41, 16, 1, 38599.00),
(50, 42, 16, 1, 38599.00),
(51, 43, 16, 1, 38599.00),
(52, 44, 2, 3, 40499.00),
(53, 44, 5, 2, 77599.00),
(54, 44, 8, 1, 45899.00),
(55, 44, 4, 2, 80699.00),
(56, 44, 15, 1, 48500.00),
(57, 44, 18, 1, 50.00),
(58, 45, 16, 1, 38599.00),
(59, 46, 16, 1, 38599.00),
(60, 47, 18, 1, 50.00),
(61, 48, 18, 1, 50.00),
(62, 49, 18, 1, 50.00),
(63, 50, 9, 1, 30599.00),
(64, 51, 18, 1, 50.00),
(65, 52, 9, 1, 30599.00),
(66, 53, 9, 1, 30599.00),
(67, 54, 9, 1, 30599.00),
(68, 55, 9, 1, 30599.00),
(69, 56, 9, 1, 30599.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `category` varchar(50) DEFAULT 'Uncategorized',
  `description` text DEFAULT NULL,
  `gallery_images` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `status`, `category`, `description`, `gallery_images`) VALUES
(1, 'Eternal Starlight Solitaire Ring', 46573.00, 'prod_1756463343.png', 'Available', 'Rings', 'A dazzling solitaire ring that embodies the radiance of every special moment. Perfect for engagements, anniversaries, or unforgettable celebrations, this ring exudes sophistication and timeless charm. Crafted with precision, the Eternal Starlight Solitaire Ring features a brilliant-cut diamond centerpiece set in a polished band. Available in multiple ring sizes from 5 to 10 (US), it ensures a perfect fit for your loved one. Each angle captures the sparkle and elegance that makes this piece truly unforgettable.', NULL),
(2, 'Vintage Charm Diamond Finger Ring', 40499.00, 'prod_1756463361.png', 'Available', 'Rings', 'A timeless diamond finger ring that evokes classic elegance with a modern touch. Perfect for engagements, celebrations, or as a cherished heirloom, this ring radiates sophistication and refined beauty. The Vintage Charm Diamond Finger Ring showcases a stunning central diamond surrounded by intricate vintage-inspired detailing on the band. Available in multiple ring sizes from 5 to 10 (US), it offers a comfortable and precise fit. Every facet sparkles with brilliance, making this piece a captivating addition to any jewelry collection', NULL),
(3, 'Radiance Royale Solitaire Ring', 55699.00, 'prod_1756463375.png', 'Available', 'Rings', 'An exquisite solitaire ring that embodies luxury and refined elegance. Perfect for proposals, anniversaries, or special occasions, the Radiance Royale Solitaire Ring dazzles with a brilliant-cut central diamond that captures every ray of light. The sleek, polished band enhances the gemstone’s radiance while ensuring a comfortable fit for sizes 5 to 10 (US). This ring combines timeless charm with modern sophistication, making it a treasured piece to celebrate life’s most precious moments.\r\n\r\n', NULL),
(4, 'Twinkle Berry Diamond Necklace', 80699.00, 'prod_1756462985.jpg', 'Available', 'Pendants', 'A stunning diamond necklace that exudes sophistication and grace. The Twinkle Berry Diamond Necklace features a delicately crafted design with sparkling diamonds that catch the light from every angle. Perfect for weddings, formal events, or special celebrations, this necklace rests comfortably on the neckline, highlighting elegance and charm. Available in adjustable lengths, it complements any attire while radiating timeless beauty. A piece designed to make every moment unforgettable.', NULL),
(5, 'Orbit Bloom Silver Necklace', 77599.00, 'prod_1756463136.jpg', 'Available', 'Pendants', 'A dazzling diamond necklace that captures the essence of elegance and sophistication. The Twinkle Berry Diamond Necklace showcases intricately set diamonds that sparkle with every movement, making it perfect for formal events or special occasions. Designed to sit gracefully along the neckline, it enhances any outfit with timeless charm. Adjustable in length for a perfect fit, this necklace combines comfort with luxury, making every moment shine brilliantly. A statement piece that reflects style and refinement.', NULL),
(6, 'Haloed Radiance Diamond Necklace', 198599.00, 'prod_1756672142.jpg', 'Available', 'Pendants', 'A dazzling diamond necklace that captures the essence of elegance and sophistication. The Twinkle Berry Diamond Necklace showcases intricately set diamonds that sparkle with every movement, making it perfect for formal events or special occasions. Designed to sit gracefully along the neckline, it enhances any outfit with timeless charm. Adjustable in length for a perfect fit, this necklace combines comfort with luxury, making every moment shine brilliantly. A statement piece that reflects style and refinement.', NULL),
(7, 'Whimsical Diamond Cluster Earring', 65599.00, 'prod_1756463240.webp', 'Available', 'Earrings', 'Delightful and sparkling, the Whimsical Diamond Cluster Earrings are designed to captivate and charm. Each earring features a cluster of brilliant diamonds arranged in an elegant pattern, perfect for parties, celebrations, or gifting to someone special. Lightweight and comfortable, they sit beautifully on the earlobe, adding a touch of glamour to any outfit. With secure fittings and a timeless design, these earrings combine sophistication and everyday elegance, making them a must-have accessory for every jewelry collection.', NULL),
(8, 'Cupid\'s Delight Diamond Earring', 45899.00, 'prod_1756463273.jpg', 'Available', 'Earrings', 'Charming and elegant, the Cupid\'s Delight Diamond Earrings are designed to add a touch of romance to your look. Each earring features delicately set diamonds that shimmer with every movement, making them perfect for special occasions or gifting to a loved one. Lightweight and comfortable, they sit gracefully on the ears, offering both style and sophistication. With secure clasps and a timeless design, these earrings effortlessly combine beauty and elegance, making them an essential addition to any jewelry collection.\r\n\r\n', NULL),
(9, 'Radiant Single Line Diamond Earring', 30599.00, 'prod_1756462498.jpg', 'Available', 'Earrings', 'A dazzling solitaire ring that embodies the radiance of every special moment. Perfect for engagements, anniversaries, or unforgettable celebrations, this ring exudes sophistication and timeless charm. Crafted with precision, the Eternal Starlight Solitaire Ring features a brilliant-cut diamond centerpiece set in a polished band. Available in multiple ring sizes from 5 to 10 (US), it ensures a perfect fit for your loved one. Each angle captures the sparkle and elegance that makes this piece truly unforgettable.', NULL),
(14, 'Delicate Charms Bracelet', 45599.00, 'prod_1756465830.png', 'Available', 'Bracelets', 'The Delicate Charms Bracelet is a graceful piece designed to celebrate elegance in its purest form. Adorned with finely crafted charms, it beautifully balances simplicity with sophistication, making it perfect for both daily wear and special occasions. Each charm adds a touch of personality, while the polished finish ensures a timeless appeal.\r\n\r\nCrafted with care, this bracelet sits comfortably on the wrist and complements every outfit — from casual chic to evening glamour. Whether as a thoughtful gift or a personal keepsake, the Delicate Charms Bracelet is more than jewelry — it’s a symbol of grace, charm, and everlasting style.', NULL),
(15, 'Gleaming Grid Diamond Bracelet', 48500.00, 'prod_1756465957.png', 'Available', 'Bracelets', 'The Gleaming Grid Diamond Bracelet is a masterpiece of modern design, combining geometric precision with timeless brilliance. Featuring a striking grid pattern, each segment is meticulously set with sparkling stones that reflect light from every angle. The result is a radiant bracelet that shimmers with sophistication and grace.\r\n\r\nCrafted with high-quality materials, this bracelet is designed for both durability and elegance, making it an ideal choice for evening soirées, festive celebrations, or as a statement accessory for daily wear. The secure clasp ensures comfort and confidence, while the dazzling design makes it a centerpiece in any jewelry collection.', NULL),
(16, 'Understated Elegance Bracelet', 38599.00, 'prod_1756466060.png', 'Available', 'Bracelets', 'The Understated Elegance Bracelet is the epitome of refined beauty, crafted for those who believe in the power of subtle sophistication. Designed with a sleek and minimal aesthetic, this bracelet adds just the right touch of shimmer without overpowering your style.\r\n\r\nIts polished finish and delicate design make it perfect for everyday wear, yet versatile enough to complement formal attire. Lightweight and comfortable, it rests gracefully on the wrist, offering a timeless look that never goes out of fashion. A true essential for anyone who values elegance in its purest form.', NULL),
(18, 'sample', 50.00, 'prod_1756577923.png', 'Available', 'Bracelets', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `email_verified`, `verification_token`, `otp`) VALUES
(1, 'Ayan', 'shaayarayan@gmail.com', '$2y$10$M3WueB1CPXrNlKewZLABGO0JuXiolS6Hkui.4EB6dFH5hLuN.aiFm', '2025-08-29 00:09:14', 1, NULL, NULL),
(32, 'jewelkart', 'jewelkart41@gmail.com', '', '2025-09-03 10:54:34', 0, NULL, NULL),
(33, 'sdfds@gmail.com', 'asdas@gmail.com', '$2y$10$qo.Iv8k57MVTJ2ttll7o7.EYVcdA9ozEAwFiL94NR4alw457.zqaO', '2025-09-07 08:35:45', 0, NULL, '494743'),
(34, 'parth', 'sayadesmail8@gmail.com', '$2y$10$Xs23zU6LfOgkOzCQRqLGpOt/c2mLl1Dx1V40PLw8pIwaPG1hjIL5O', '2025-09-07 08:37:55', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `full_name`, `address`, `city`, `state`, `pincode`, `payment_method`, `updated_at`) VALUES
(1, 1, 'AYAN RAJU SAYYED', 'yerwada\r\nlaxmi nagar yerwada pune', 'PUNE', 'Maharashtra', '411006', 'Online Payment', '2025-09-01 20:38:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

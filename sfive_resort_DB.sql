-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2026 at 04:19 PM
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
-- Database: `sfive_resort`
--

-- --------------------------------------------------------

--
-- Table structure for table `cottages`
--

CREATE TABLE `cottages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Bahay Kubo','Open Cottage','Kubo Premium') DEFAULT 'Bahay Kubo',
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `images` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cottages`
--

INSERT INTO `cottages` (`id`, `name`, `category`, `description`, `price_per_night`, `capacity`, `images`, `amenities`, `is_available`, `created_at`) VALUES
(1, 'Bahay Kubo 1', 'Bahay Kubo', 'A cozy authentic bamboo kubo with natural ventilation and a relaxing veranda. Perfect for small families or couples who love a simple Filipino countryside vibe.', 800.00, 8, NULL, 'Outdoor seating, Garden view', 1, '2026-06-24 02:54:12'),
(2, 'Bahay Kubo 2', 'Bahay Kubo', 'Surrounded by lush tropical plants, Bahay Kubo 2 offers a peaceful retreat with natural breezes. Ideal for guests who enjoy waking up to the sounds of nature.', 800.00, 8, NULL, 'Videoke, Outdoor seating, Garden view', 1, '2026-06-24 02:54:12'),
(3, 'Bahay Kubo 3', 'Bahay Kubo', 'Nestled near the garden path, this kubo features traditional bamboo construction with hammock space outside. A true Filipino countryside experience.', 800.00, 8, NULL, 'Hammock, Garden path access', 1, '2026-06-24 02:54:12'),
(4, 'Bahay Kubo 4', 'Bahay Kubo', 'Overlooking the resort grounds, Bahay Kubo 4 gives guests a wide open view of the greenery while enjoying the cool natural breeze from the highlands.', 800.00, 8, NULL, 'Hammock, Resort view, Outdoor bench', 1, '2026-06-24 02:54:12'),
(5, 'Bahay Kubo 5', 'Bahay Kubo', 'The most private of our kubos, tucked away for guests who want quiet and solitude. Great for couples or solo travelers.', 800.00, 8, NULL, 'Private garden, BBQ grill access', 1, '2026-06-24 02:54:12'),
(6, 'Open Cottage 1 — Fiesta Hall', 'Open Cottage', 'Our largest open-air event venue perfect for birthdays, weddings, reunions, and fiestas. Wide covered area with open garden surroundings for up to 60 guests.', 7500.00, 60, NULL, 'Open-air covered hall, Long tables &amp;amp; chairs, BBQ grill stations, Sound system ready, Outdoor lighting, Garden surroundings, Parking access', 1, '2026-06-24 02:54:12'),
(8, 'Kubo Premium 1', 'Kubo Premium', 'Experience Filipino heritage in luxury. This premium kubo features a split-type air conditioner, a king-size bed with premium linens, and a private veranda with garden view.', 2000.00, 2, NULL, 'Split-type Aircon, Mosquito Net, King bed, Premium bedding, Private bathroom, Hot &amp;amp;amp; cold shower, Garden view', 1, '2026-06-24 02:54:12'),
(9, 'Kubo Premium 2', 'Kubo Premium', 'One of our most popular premium cottages with king bed, sofa area, and private bathroom. Split aircon keeps it cool even on the hottest days.', 2000.00, 2, NULL, 'Split-type Aircon, Premium bedding, Private bathroom, Hot &amp;amp;amp;amp;amp;amp; cold shower, Veranda, Mini ref, Smart TV', 1, '2026-06-24 02:54:12'),
(10, 'Kubo Premium 3', 'Kubo Premium', 'Ideal for Families. Features two queen beds, full aircon comfort, private shower room, and cozy bamboo-styled interior with modern touches.', 2600.00, 4, NULL, 'Split-type Aircon, King bed, Sofa area, Private bathroom, Hot &amp;amp;amp;amp; cold shower, Mini ref, Veranda, Nature sound', 1, '2026-06-24 02:54:12'),
(11, 'Kubo Premium 4', 'Kubo Premium', 'Perfect fo rcouple. With a Big King bed with aircon.', 2200.00, 2, NULL, 'Central Aircon,King Bed, Hot &amp;amp;amp;amp; cold shower, Bathroom, Mini ref, Smart TV, Veranda', 1, '2026-06-24 02:54:12'),
(12, 'Kubo Premium 5 — The Suite', 'Kubo Premium', 'Our flagship premium kubo — The Suite. King bed, lounging area, jacuzzi shower, premium linens, mini bar, and panoramic resort views. The ultimate S-Five experience.', 2500.00, 2, NULL, 'Split-type Aircon, King bed, Premium bedding, Jacuzzi shower, Mini bar, Smart TV, Panoramic resort view, Veranda, Breakfast option', 1, '2026-06-24 02:54:12');

-- --------------------------------------------------------

--
-- Table structure for table `cottage_images`
--

CREATE TABLE `cottage_images` (
  `id` int(11) NOT NULL,
  `cottage_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `label` varchar(50) DEFAULT 'Photo',
  `sort_order` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cottage_images`
--

INSERT INTO `cottage_images` (`id`, `cottage_id`, `filename`, `label`, `sort_order`, `uploaded_at`) VALUES
(1, 1, 'cottage_1_thumb_1784605537.jpg', 'Thumbnail', 0, '2026-07-21 03:45:37'),
(2, 2, 'cottage_2_thumb_1784605551.jpg', 'Thumbnail', 0, '2026-07-21 03:45:51'),
(3, 3, 'cottage_3_thumb_1784605566.jpg', 'Thumbnail', 0, '2026-07-21 03:46:06'),
(4, 4, 'cottage_4_thumb_1784605582.jpg', 'Thumbnail', 0, '2026-07-21 03:46:22'),
(5, 5, 'cottage_5_thumb_1784605602.jpg', 'Thumbnail', 0, '2026-07-21 03:46:42'),
(6, 6, 'cottage_6_thumb_1784605616.jpg', 'Thumbnail', 0, '2026-07-21 03:46:56'),
(8, 8, 'cottage_8_thumb_1784605647.jpg', 'Thumbnail', 0, '2026-07-21 03:47:27'),
(9, 9, 'cottage_9_thumb_1784605660.jpg', 'Thumbnail', 0, '2026-07-21 03:47:40'),
(10, 10, 'cottage_10_thumb_1784605682.jpg', 'Thumbnail', 0, '2026-07-21 03:48:02'),
(11, 11, 'cottage_11_thumb_1784605716.jpg', 'Thumbnail', 0, '2026-07-21 03:48:36'),
(12, 12, 'cottage_12_thumb_1784605751.webp', 'Thumbnail', 0, '2026-07-21 03:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `gcash_payments`
--

CREATE TABLE `gcash_payments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_number` varchar(20) NOT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `gcash_settings`
--

CREATE TABLE `gcash_settings` (
  `id` int(11) NOT NULL,
  `account_name` varchar(100) NOT NULL DEFAULT 'S-Five Inland Resort',
  `qr_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gcash_settings`
--

INSERT INTO `gcash_settings` (`id`, `account_name`, `qr_image`) VALUES
(1, 'S-Five Inland Resort', NULL);

-- --------------------------------------------------------

--
=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `cottage_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `num_guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  `payment_method` enum('GCash','GCash Online') DEFAULT 'GCash Online',
  `payment_status` enum('Unpaid','Pending Verification','Paid') DEFAULT 'Unpaid',
  `paymongo_link_id` varchar(100) DEFAULT NULL,
  `paymongo_checkout_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@sfive.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'admin', '2026-06-24 02:54:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cottages`
--
ALTER TABLE `cottages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cottage_images`
--
ALTER TABLE `cottage_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cottage_id` (`cottage_id`);

--
-- Indexes for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
<<<<<<< HEAD
-- Indexes for table `gcash_settings`
--
ALTER TABLE `gcash_settings`
  ADD PRIMARY KEY (`id`);

--
=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `cottage_id` (`cottage_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cottages`
--
ALTER TABLE `cottages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cottage_images`
--
ALTER TABLE `cottage_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `gcash_settings`
--
ALTER TABLE `gcash_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cottage_images`
--
ALTER TABLE `cottage_images`
  ADD CONSTRAINT `cottage_images_ibfk_1` FOREIGN KEY (`cottage_id`) REFERENCES `cottages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  ADD CONSTRAINT `gcash_payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`cottage_id`) REFERENCES `cottages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

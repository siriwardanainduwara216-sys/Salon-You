-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2026 at 10:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_you`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_mins` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`, `duration_mins`) VALUES
(1, 'Hair Cut & Styling', 1500.00, 30),
(2, 'Hair Coloring', 6500.00, 90),
(3, 'Facial Treatment', 4500.00, 45),
(4, 'Manicure & Pedicure', 3500.00, 60);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','employee','admin') DEFAULT 'customer',
  `otp_code` varchar(6) DEFAULT NULL,
  `status` enum('pending','active','suspended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `otp_code`, `status`, `created_at`, `is_verified`) VALUES
(3, 'test customer', 'customer@salon.com', '', '$2y$10$0vHeQP7em5xrRx0Riltz2.Z4ulh2sSTAj/h8VOeQIyk.i1vTTtvDq', 'customer', NULL, 'pending', '2026-06-07 07:06:28', 0),
(6, 'sachintha', 'sachintha125@gmail.com', '0718445798', '$2y$10$B4vEZgENuvWE/d9JUlkiv.rVcZMT1x6VzXmuErrlAEkX8C.NHztxO', 'customer', '343724', 'pending', '2026-07-29 11:30:02', 0),
(7, 'ranjana', 'ranjana1234@gmail.com', '0372266525', '$2y$10$ZUk7K2Brqf8Lyv1443EvHOfECTUtbJ3/p/N2KqCIg80BVyd7j/Gvi', 'customer', '709652', 'pending', '2026-07-29 11:38:06', 0),
(8, 'ishan', 'ishan456@gmail.com', '0775441330', '$2y$10$wevYm12doIhrDnLtFywI9.wNFUVTrUwI312NkFpSC43lpLxhK8wq.', 'customer', '940034', 'pending', '2026-07-29 11:50:57', 0),
(10, 'isanka', 'isanka@gmail.com', '0725661330', '$2y$10$LJoQVIOAQFFYk9vOYHZLT.DROwzDKfTRvlu4ybtpKEdtiZ7SH33T6', 'customer', '816242', 'pending', '2026-07-29 12:13:06', 0),
(19, 'ranjana', 'ranjanakaushal100@gmail.com', '0791251330', '$2y$10$ynw9TwwDe1I1qPaJ3jBXsO7avcmDdssqeuecUNrUwDugGN704h5oS', 'customer', '196074', 'pending', '2026-07-29 16:49:57', 0),
(20, 'pasindu dilmina', 'dilminaishan084@gmail.com', '0777522133', '$2y$10$VIOXg7i1Bb63XCDMRfTmqekmn7CbH0uz2EvveriyRkMF2KHPmhwyy', 'customer', NULL, 'active', '2026-07-30 04:13:57', 0),
(22, 'dasun', 'dasuntheekshana45@gmail.com', '0895551330', '$2y$10$eHS/tKaLKF21YrxIuIM/ierYzHytRgkpBHMbTbYSu07Uy4LoAM/hy', 'customer', NULL, 'active', '2026-07-30 05:01:46', 0),
(27, 'Sakunya Siriwardena', 'sepaliwijesinghe9770@gmail.com', '0775221330', '$2y$10$tlbXrlhKHpCwkKWiTUgwR.UMBW6A9StN.McY0DzVRJSMxZg8c3q2i', 'customer', '011456', 'pending', '2026-07-31 07:49:21', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

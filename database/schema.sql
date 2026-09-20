-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 07:08 AM
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
-- Database: `fitnesstracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achievement_name` varchar(255) DEFAULT NULL,
  `unlocked` tinyint(1) DEFAULT 0,
  `unlocked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `workout_id` int(11) DEFAULT NULL,
  `exercise_name` varchar(100) DEFAULT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `workout_id`, `exercise_name`, `sets`, `reps`) VALUES
(1, 1, 'Jumping Jacks', 3, '30'),
(2, 1, 'Burpees', 3, '15'),
(3, 1, 'Mountain Climbers', 3, '20'),
(4, 2, 'Plank', 3, '60'),
(5, 2, 'Russian Twists', 3, '20'),
(6, 2, 'Leg Raises', 3, '15'),
(7, 3, 'Running', 1, '20'),
(8, 3, 'Jump Rope', 3, '15'),
(9, 4, 'Bench Press', 4, '10'),
(10, 4, 'Pull Ups', 4, '8'),
(11, 5, 'Squats', 4, '12'),
(12, 5, 'Deadlifts', 4, '10'),
(13, 6, 'Push Ups', 3, '15'),
(14, 6, 'Lunges', 3, '12'),
(16, 1, 'Burpees', 3, '12'),
(17, 1, 'Jumping Jacks', 3, '20'),
(18, 1, 'Mountain Climbers', 3, '15');

-- --------------------------------------------------------

--
-- Table structure for table `food_diary`
--

CREATE TABLE `food_diary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal` varchar(100) NOT NULL,
  `calories` int(11) DEFAULT 0,
  `carbs` int(11) DEFAULT 0,
  `protein` int(11) DEFAULT 0,
  `fat` int(11) DEFAULT 0,
  `date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_value` int(11) NOT NULL,
  `current_value` int(11) DEFAULT 0,
  `unit` varchar(50) DEFAULT '',
  `status` enum('ongoing','completed') DEFAULT 'ongoing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `title`, `description`, `target_value`, `current_value`, `unit`, `status`, `created_at`) VALUES
(1, 6, 'hhfg', 'sdgdfgdrgr', 20, 20, 'km', 'completed', '2025-06-28 11:35:16'),
(2, 6, 'aew', 'efedfe', 123, 123, 'gh', 'completed', '2025-06-28 11:45:00'),
(3, 6, 'fcvfcvf', 'bfdbfb', 12, 12, 'km', 'completed', '2025-06-28 11:54:19'),
(4, 6, 'gfdgdf', 'gfgfdgd', 45, 0, 'ff', 'ongoing', '2025-06-28 12:16:27'),
(5, 6, 'asasa', 'sdsadsaf', 12, 0, 'min', 'ongoing', '2025-06-28 17:20:10'),
(6, 6, 'abc', 'xzcvxvxvxvv', 123, 34, 'min', 'ongoing', '2025-06-28 21:30:17'),
(7, 6, '10 km run', 'it is very hard but you can do it', 10, 10, 'km', 'completed', '2025-06-29 04:26:43');

-- --------------------------------------------------------

--
-- Table structure for table `macro_goals`
--

CREATE TABLE `macro_goals` (
  `user_id` int(11) NOT NULL,
  `calories` int(11) NOT NULL,
  `carbs` int(11) NOT NULL,
  `protein` int(11) NOT NULL,
  `fat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `macro_goals`
--

INSERT INTO `macro_goals` (`user_id`, `calories`, `carbs`, `protein`, `fat`) VALUES
(6, 1008, 405, 350, 250);

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `meal` varchar(100) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `protein` int(11) DEFAULT NULL,
  `fat` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`id`, `user_id`, `meal`, `calories`, `carbs`, `protein`, `fat`, `date`) VALUES
(2, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-25'),
(3, 6, 'Banana (1 medium)', 105, 27, 1, 0, '2025-06-25'),
(4, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-25'),
(5, 6, 'cxcx', 123, 23, 54, 11, '2025-06-25'),
(6, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-26'),
(7, 6, 'cxcx', 50, 15, 10, 25, '2025-06-26'),
(8, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-26'),
(9, 6, 'omlet', 344, 133, 132, 32, '2025-06-26'),
(10, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-26'),
(12, 6, 'omlet', 123, 100, 20, 3, '2025-06-27'),
(13, 6, 'cxcx', 67, 43, 20, 4, '2025-06-27'),
(14, 6, 'omlet', 100, 10, 80, 10, '2025-06-27'),
(15, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-27'),
(16, 6, 'omlet', 213, 11, 33, 11, '2025-06-28'),
(17, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-28'),
(18, 6, 'Oats (100g)', 389, 66, 17, 7, '2025-06-28'),
(19, 6, 'Mango', 400, 200, 150, 50, '2025-06-29');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_logs`
--

CREATE TABLE `nutrition_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_name` varchar(100) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` float DEFAULT NULL,
  `carbs` float DEFAULT NULL,
  `fat` float DEFAULT NULL,
  `meal_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT NULL CHECK (`duration_weeks` between 4 and 12),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `duration_weeks`, `description`) VALUES
(1, 'Fat Loss Blast', 4, 'HIIT and cardio focused'),
(2, 'Strength Builder', 8, 'Progressive weight training');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `workout_id` int(11) DEFAULT NULL,
  `week_number` int(11) DEFAULT NULL CHECK (`week_number` between 1 and 12),
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `user_id`, `program_id`, `workout_id`, `week_number`, `day_of_week`) VALUES
(18, 6, 1, 1, 1, 'Monday'),
(19, 6, 1, 1, 1, 'Tuesday'),
(20, 6, 1, 1, 1, 'Wednesday'),
(21, 6, 1, 1, 1, 'Saturday'),
(22, 6, 1, 1, 1, 'Thursday'),
(23, 6, 1, 1, 1, 'Friday'),
(24, 6, 1, 1, 1, 'Sunday'),
(25, 6, 1, 1, 2, 'Monday'),
(26, 6, 1, 1, 2, 'Tuesday'),
(27, 6, 1, 1, 2, 'Wednesday'),
(28, 6, 1, 1, 2, 'Thursday'),
(29, 6, 1, 1, 2, 'Friday'),
(30, 6, 1, 1, 2, 'Saturday'),
(31, 6, 1, 1, 2, 'Sunday'),
(32, 6, 1, 8, 3, 'Monday'),
(33, 6, 1, 8, 3, 'Tuesday'),
(34, 6, 1, 1, 3, 'Wednesday'),
(35, 6, 1, 8, 3, 'Thursday'),
(36, 6, 1, 8, 3, 'Friday'),
(37, 6, 1, 2, 3, 'Saturday'),
(38, 6, 1, 1, 3, 'Sunday'),
(39, 6, 1, 8, 4, 'Monday'),
(40, 6, 1, 8, 4, 'Tuesday'),
(41, 6, 1, 1, 4, 'Wednesday'),
(42, 6, 1, 8, 4, 'Thursday'),
(43, 6, 1, 8, 4, 'Friday'),
(44, 6, 1, 8, 4, 'Saturday'),
(45, 6, 1, 1, 4, 'Sunday');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `birth_date`, `username`, `age`, `gender`, `phone`, `email`, `password`) VALUES
(1, 'Test', 'User', NULL, NULL, NULL, NULL, NULL, 'testuser@example.com', 'hashed_password_here'),
(2, 'MD RAFIUR RAHMAN ', NULL, NULL, 'rafi', 23, 'Male', '1234567890', 'r.rahman.dip@gmail.com', '$2y$10$vFRHSCcvLzKqd.KQkpCEKenLwR9Ul2oMEqOrzHwg2zywOqe812mpG'),
(6, 'rafi', 'dipto', '2000-06-13', 'raf', 23, 'Male', '01834712414', 'abc@abc.abc', '$2y$10$0VXhVS/jRigs9nTF95NfT.5DV1HHtxnpTu0fY/cts9ZfLPGfOB7Le');

-- --------------------------------------------------------

--
-- Table structure for table `user_programs`
--

CREATE TABLE `user_programs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_programs`
--

INSERT INTO `user_programs` (`id`, `user_id`, `program_id`, `start_date`) VALUES
(2, 6, 1, '2025-06-29');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `workout_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `program_id`, `workout_name`, `description`) VALUES
(1, 1, 'Day 1 - HIIT', NULL),
(2, 1, 'Day 2 - Core', NULL),
(3, 1, 'Day 3 - Cardio', NULL),
(4, 2, 'Day 1 - Upper Body Strength', NULL),
(5, 2, 'Day 2 - Lower Body Strength', NULL),
(6, 2, 'Day 3 - Full Body', NULL),
(8, 1, 'Saturday Full Body', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- Indexes for table `food_diary`
--
ALTER TABLE `food_diary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `macro_goals`
--
ALTER TABLE `macro_goals`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`week_number`,`day_of_week`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_programs`
--
ALTER TABLE `user_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `food_diary`
--
ALTER TABLE `food_diary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_programs`
--
ALTER TABLE `user_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `food_diary`
--
ALTER TABLE `food_diary`
  ADD CONSTRAINT `food_diary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `macro_goals`
--
ALTER TABLE `macro_goals`
  ADD CONSTRAINT `macro_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_programs`
--
ALTER TABLE `user_programs`
  ADD CONSTRAINT `user_programs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_programs_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

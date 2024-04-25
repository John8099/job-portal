-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2024 at 12:13 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicant_skills`
--

CREATE TABLE `applicant_skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_skills`
--

INSERT INTO `applicant_skills` (`id`, `user_id`, `skill_id`) VALUES
(36, 9, 9);

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `status` enum('Applied','Interviewing','Not selected by employer','Hired','Withdrawn') NOT NULL,
  `date_applied` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_date` text DEFAULT NULL,
  `interview_time` text DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `user_id`, `job_id`, `status`, `date_applied`, `interview_date`, `interview_time`, `date_modified`) VALUES
(1, 9, 9, 'Withdrawn', '2024-04-25 08:38:33', NULL, NULL, '2024-04-25 19:17:28'),
(2, 9, 7, 'Not selected by employer', '2024-04-25 08:41:01', '2024-04-26', '16:40 - 17:30', '2024-04-25 19:06:33'),
(3, 9, 9, 'Applied', '2024-04-25 19:21:48', NULL, NULL, '2024-04-25 19:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `verification_id` int(11) DEFAULT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `company_logo` text DEFAULT NULL,
  `address` text NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `verification_id`, `industry_id`, `name`, `company_logo`, `address`, `description`, `date_created`) VALUES
(1, 3, 1, 'wallmart', '', 'Jaro Iloilo City', 'test', '2024-03-28 02:56:29'),
(2, 4, 2, 'target', '', 'Jaro Iloilo City', 'test', '2024-03-28 02:56:29'),
(3, 5, 18, 'Stacktrek1', '66093268aa9ac_screencapture-localhost-primeenergyportal-dashboard-php-2024-03-19-23_29_55.png', 'Jaro Iloilo City', 'test', '2024-03-28 07:34:35'),
(4, 6, 2, 'test', '6610e4e001528_wallpapers-hd-7974-8304-hd-wallpapers.jpg', 'Molo Iloilo City', 'test', '2024-04-06 06:00:06'),
(5, NULL, NULL, 'sample', NULL, '', '', '2024-04-18 09:46:26'),
(6, NULL, NULL, 'holiday', NULL, '', '', '2024-04-18 11:29:33'),
(7, NULL, NULL, 'reklamador', NULL, '', '', '2024-04-18 11:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `attainment_id` int(11) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `school_name` text NOT NULL,
  `sy` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `user_id`, `attainment_id`, `course`, `school_name`, `sy`) VALUES
(3, 9, 4, 'Sample', 'Test', '2324');

-- --------------------------------------------------------

--
-- Table structure for table `educational_attainment`
--

CREATE TABLE `educational_attainment` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_attainment`
--

INSERT INTO `educational_attainment` (`id`, `name`, `date_created`) VALUES
(1, 'Less than high school', '2024-04-17 06:53:11'),
(2, 'High school', '2024-04-17 06:53:11'),
(3, 'Graduated from high school', '2024-04-17 06:53:11'),
(4, 'Vocational Course', '2024-04-17 06:53:11'),
(5, 'Completed vocational course', '2024-04-17 06:53:11'),
(6, 'Associate\'s studies', '2024-04-17 06:53:11'),
(7, 'Completed associate\'s degree', '2024-04-17 06:53:11'),
(8, 'Bachelor\'s studies', '2024-04-17 06:53:11'),
(9, 'Bachelor\'s degree graduate', '2024-04-17 06:53:11'),
(10, 'Graduate studies (Masters)', '2024-04-17 06:53:11'),
(11, 'Master\'s degree graduate', '2024-04-17 06:53:11'),
(12, 'Post-graduate studies (Doctorate)', '2024-04-17 06:53:11'),
(13, 'Doctoral degree', '2024-04-17 06:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `experience_list`
--

CREATE TABLE `experience_list` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience_list`
--

INSERT INTO `experience_list` (`id`, `name`, `date_created`) VALUES
(1, 'Testing', '2024-04-25 03:33:09'),
(2, 'Sales', '2024-04-25 03:39:27'),
(3, 'Content Creation', '2024-04-25 03:39:34'),
(4, 'No Experience Needed', '2024-04-25 03:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE `industries` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `industries`
--

INSERT INTO `industries` (`id`, `name`, `date_created`) VALUES
(1, 'Aerospace and Defense', '2024-03-27 08:35:24'),
(2, 'Agriculture', '2024-03-27 08:35:24'),
(3, 'Arts, Entertainment and Recreation', '2024-03-27 08:35:24'),
(4, 'Construction, Repair and Maintenance Services', '2024-03-27 08:35:24'),
(5, 'Education', '2024-03-27 08:35:24'),
(6, 'Energy, Mining and Utilities', '2024-03-27 08:35:24'),
(7, 'Financial Services', '2024-03-27 08:35:24'),
(8, 'Government and Public Administration', '2024-03-27 08:35:24'),
(9, 'Healthcare', '2024-03-27 08:35:24'),
(10, 'Hotels and Travel Accommodation', '2024-03-27 08:35:24'),
(11, 'Human Resources and Staffing', '2024-03-27 08:35:24'),
(12, 'Information Technology', '2024-03-27 08:35:24'),
(13, 'Insurance', '2024-03-27 08:35:24'),
(14, 'Legal', '2024-03-27 08:35:24'),
(15, 'Management and Consulting', '2024-03-27 08:35:24'),
(16, 'Manufacturing', '2024-03-27 08:35:24'),
(17, 'Media and Communication', '2024-03-27 08:35:24'),
(18, 'Nonprofit and NGO', '2024-03-27 08:35:24'),
(19, 'Personal Consumer Services', '2024-03-27 08:35:24'),
(20, 'Pharmaceutical and Biotechnology', '2024-03-27 08:35:24'),
(21, 'Real Estate', '2024-03-27 08:35:24'),
(22, 'Restaurants and Food Service', '2024-03-27 08:35:24'),
(23, 'Retail and Wholesale', '2024-03-27 08:35:24'),
(24, 'Telecommunications', '2024-03-27 08:35:24'),
(25, 'Transportation and Logistics', '2024-03-27 08:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `experience_level` varchar(32) NOT NULL,
  `location_type` varchar(32) NOT NULL,
  `schedule` text NOT NULL,
  `pay` varchar(32) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`id`, `company_id`, `title`, `type`, `experience_level`, `location_type`, `schedule`, `pay`, `benefits`, `qualifications`, `experience`, `description`, `status`, `date_created`) VALUES
(7, 1, 'Test1', 'Full time', '2 years', 'Remote (WFH)', '[\"4 hour shift\",\"8 hour shift\",\"Monday to Friday\"]', '5,001.50 - 1,000.20 per month', '[\"Vision insurance\",\"Flexible schedule\"]', '[2,3]', NULL, 'test1', 'active', '2024-04-24 20:12:22'),
(8, 1, 'Test', 'Contract', 'Under 1 year', 'Remote (WFH)', '[\"Monday to Friday\",\"On Call\"]', '5,001.00 - 10,001.00 per day', '[\"Health insurance\",\"Retirement plan\"]', '[1,2]', NULL, 'Test', 'active', '2024-04-25 03:39:38'),
(9, 1, 'Test 3', 'Part time', 'Under 1 year', 'Remote (WFH)', '[\"8 hour shift\",\"10 hour shift\"]', '500.00 - 1,000.00 per day', '[\"Paid time off\",\"Dental insurance\",\"Vision insurance\"]', '[6,7]', '[4]', 'test', 'active', '2024-04-25 03:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `search_keywords`
--

CREATE TABLE `search_keywords` (
  `id` int(11) NOT NULL,
  `keywords` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `search_keywords`
--

INSERT INTO `search_keywords` (`id`, `keywords`) VALUES
(8, 'UI Designer'),
(9, 'test'),
(10, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `skills_list`
--

CREATE TABLE `skills_list` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills_list`
--

INSERT INTO `skills_list` (`id`, `name`, `date_created`) VALUES
(1, 'Customer Service', '2024-04-18 18:50:39'),
(2, 'Organizational Skills', '2024-04-18 18:50:39'),
(3, 'Microsoft Skills', '2024-04-18 18:50:39'),
(4, 'Cashiering', '2024-04-18 18:50:39'),
(5, 'Maintenance', '2024-04-18 18:50:39'),
(6, 'Communication Skills', '2024-04-18 18:50:39'),
(7, 'Leadership', '2024-04-18 18:50:39'),
(8, 'Cash Handling', '2024-04-18 18:50:39'),
(9, 'Time Management', '2024-04-18 18:50:39'),
(10, 'Problem-solving', '2024-04-18 18:50:39'),
(11, 'Creativity', '2024-04-18 18:50:39'),
(12, 'Work Ethic', '2024-04-18 18:50:39'),
(13, 'Attention to Detail', '2024-04-18 18:50:39'),
(14, 'Frontend', '2024-04-18 19:50:29'),
(19, 'Testing', '2024-04-24 20:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(32) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `role` enum('applicant','employer','admin') NOT NULL,
  `avatar` text DEFAULT NULL,
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `fname`, `mname`, `lname`, `address`, `contact`, `email`, `password`, `role`, `avatar`, `is_password_changed`, `date_created`) VALUES
(1, NULL, 'super', 'test', 'admin', '09876543', '0987654', 'admin@admin.com', '$argon2i$v=19$m=65536,t=4,p=1$blAudjU3emJGaHR4VE16dA$q4SYAg+hmpvJrSUF/OyjeFJc7zXXBI9UIXOxo23XfqM', 'admin', NULL, 1, '2024-03-02'),
(5, 1, 'employer', 'test', 'test', 'La Paz Iloilo City', '098765432', 'test@test.com', '$argon2i$v=19$m=65536,t=4,p=1$blAudjU3emJGaHR4VE16dA$q4SYAg+hmpvJrSUF/OyjeFJc7zXXBI9UIXOxo23XfqM', 'employer', NULL, 0, '2024-03-27'),
(6, 3, 'employer', 'paderan', 'tests', 'Jaro Iloilo City', '09876543', 'employer@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$blAudjU3emJGaHR4VE16dA$q4SYAg+hmpvJrSUF/OyjeFJc7zXXBI9UIXOxo23XfqM', 'employer', '6606eda03174c_scary.png', 0, '2024-03-28'),
(7, NULL, 'employer1', 'paderan', 'montemar', 'awdawdawd', '09876543', 'montemar@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$YjVHc0NGTktBSlFYSk5RRA$BZ8v4OTG545pzzUQsNrmYPSWYjGuHrTY53P8Yt7b6qA', 'employer', NULL, 0, '2024-03-29'),
(9, NULL, 'test', 'test', 'test', 'Iloilo City Proper', '0987654', 'test@test.test', '$argon2i$v=19$m=65536,t=4,p=1$NTBHVENMWm5MMDV1Lm9YUw$rCEF/qORNRwWnp1xIP0Ie+2cP/5e1X2H8lL1ug2RvGc', 'applicant', NULL, 1, '2024-04-17');

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int(11) NOT NULL,
  `business_permit` varchar(250) NOT NULL,
  `status` enum('pending','denied','approved') DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`id`, `business_permit`, `status`, `message`, `date_created`, `date_updated`) VALUES
(3, 'https://img.freepik.com/free-photo/painting-mountain-lake-with-mountain-background_188544-9126.jpg', 'approved', 'Company\'s fully verified.<br>You can start posting your job.', '2024-03-28 07:30:35', '2024-04-25 17:13:19'),
(4, 'https://rjmtravel.files.wordpress.com/2013/10/dti-busines-name-permit.jpg', 'pending', 'Waiting for admin to validate the business permit.', '2024-03-28 07:32:11', '2024-03-31 11:14:21'),
(5, 'https://rjmtravel.files.wordpress.com/2013/10/dti-busines-name-permit.jpg', 'approved', 'Company\'s fully verified.<br>You can start posting your job.', '2024-03-28 07:34:26', '2024-03-31 11:23:09'),
(6, 'http://localhost/job-portal/uploads/company/6610e4e001b69_wp2760866.png', 'denied', 'Denied reason: Test', '2024-04-06 06:00:04', '2024-04-25 20:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `work_experience`
--

CREATE TABLE `work_experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_title` varchar(100) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `work_from` varchar(55) NOT NULL,
  `work_to` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experience`
--

INSERT INTO `work_experience` (`id`, `user_id`, `job_title`, `company_id`, `industry_id`, `work_from`, `work_to`) VALUES
(6, 9, 'Testing', 4, 12, 'September 2008', 'Present');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicant_skills`
--
ALTER TABLE `applicant_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `skills_id` (`skill_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verification_id` (`verification_id`),
  ADD KEY `industry_id` (`industry_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attainement_id` (`attainment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `educational_attainment`
--
ALTER TABLE `educational_attainment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experience_list`
--
ALTER TABLE `experience_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `industries`
--
ALTER TABLE `industries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `search_keywords`
--
ALTER TABLE `search_keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills_list`
--
ALTER TABLE `skills_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `industry_id` (`industry_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicant_skills`
--
ALTER TABLE `applicant_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `educational_attainment`
--
ALTER TABLE `educational_attainment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `experience_list`
--
ALTER TABLE `experience_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `industries`
--
ALTER TABLE `industries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `search_keywords`
--
ALTER TABLE `search_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `skills_list`
--
ALTER TABLE `skills_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicant_skills`
--
ALTER TABLE `applicant_skills`
  ADD CONSTRAINT `applicant_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `applicant_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills_list` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `candidates_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`verification_id`) REFERENCES `verification` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `company_ibfk_2` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`attainment_id`) REFERENCES `educational_attainment` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `education_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `job`
--
ALTER TABLE `job`
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `work_experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `work_experience_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `work_experience_ibfk_3` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

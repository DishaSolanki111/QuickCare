-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2026 at 05:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quick_care`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment_reminder_tbl`
--

CREATE TABLE `appointment_reminder_tbl` (
  `APPOINTMENT_REMINDER_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `REMINDER_TIME` time NOT NULL,
  `REMARKS` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_reminder_tbl`
-- NOTE: Rows 61-75 removed as they referenced APPOINTMENT_IDs (24,25,26,29,30,31)
--       that were old/deleted records no longer present in appointment_tbl.
--

INSERT INTO `appointment_reminder_tbl` (`APPOINTMENT_REMINDER_ID`, `RECEPTIONIST_ID`, `APPOINTMENT_ID`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 1, '10:00:00', 'Appointment booked'),
(2, 1, 1, '10:00:00', '24 hours before appointment'),
(3, 1, 1, '07:00:00', '3 hours before appointment'),
(4, 1, 2, '10:00:00', 'Appointment booked'),
(5, 1, 2, '10:00:00', '24 hours before appointment'),
(6, 1, 2, '07:00:00', '3 hours before appointment'),
(7, 1, 3, '11:00:00', 'Appointment booked'),
(8, 1, 3, '11:00:00', '24 hours before appointment'),
(9, 1, 3, '08:00:00', '3 hours before appointment'),
(10, 1, 4, '11:00:00', 'Appointment booked'),
(11, 1, 4, '11:00:00', '24 hours before appointment'),
(12, 1, 4, '08:00:00', '3 hours before appointment'),
(13, 2, 5, '12:00:00', 'Appointment booked'),
(14, 2, 5, '09:00:00', '24 hours before appointment'),
(15, 2, 5, '06:00:00', '3 hours before appointment'),
(16, 2, 6, '12:00:00', 'Appointment booked'),
(17, 2, 6, '09:00:00', '24 hours before appointment'),
(18, 2, 6, '06:00:00', '3 hours before appointment'),
(19, 2, 7, '13:00:00', 'Appointment booked'),
(20, 2, 7, '10:00:00', '24 hours before appointment'),
(21, 2, 7, '07:00:00', '3 hours before appointment'),
(22, 2, 8, '13:00:00', 'Appointment booked'),
(23, 2, 8, '10:00:00', '24 hours before appointment'),
(24, 2, 8, '07:00:00', '3 hours before appointment'),
(25, 3, 9, '14:00:00', 'Appointment booked'),
(26, 3, 9, '11:00:00', '24 hours before appointment'),
(27, 3, 9, '08:00:00', '3 hours before appointment'),
(28, 3, 10, '14:00:00', 'Appointment booked'),
(29, 3, 10, '11:00:00', '24 hours before appointment'),
(30, 3, 10, '08:00:00', '3 hours before appointment'),
(31, 3, 11, '15:00:00', 'Appointment booked'),
(32, 3, 11, '09:00:00', '24 hours before appointment'),
(33, 3, 11, '06:00:00', '3 hours before appointment'),
(34, 3, 12, '15:00:00', 'Appointment booked'),
(35, 3, 12, '09:00:00', '24 hours before appointment'),
(36, 3, 12, '06:00:00', '3 hours before appointment'),
(37, 4, 13, '16:00:00', 'Appointment booked'),
(38, 4, 13, '10:00:00', '24 hours before appointment'),
(39, 4, 13, '07:00:00', '3 hours before appointment'),
(40, 4, 14, '16:00:00', 'Appointment booked'),
(41, 4, 14, '10:00:00', '24 hours before appointment'),
(42, 4, 14, '07:00:00', '3 hours before appointment'),
(43, 4, 15, '17:00:00', 'Appointment booked'),
(44, 4, 15, '11:00:00', '24 hours before appointment'),
(45, 4, 15, '08:00:00', '3 hours before appointment'),
(46, 4, 16, '17:00:00', 'Appointment booked'),
(47, 4, 16, '11:00:00', '24 hours before appointment'),
(48, 4, 16, '08:00:00', '3 hours before appointment'),
(49, 1, 17, '18:00:00', 'Appointment booked'),
(50, 1, 17, '09:00:00', '24 hours before appointment'),
(51, 1, 17, '06:00:00', '3 hours before appointment'),
(52, 1, 18, '18:00:00', 'Appointment booked'),
(53, 1, 18, '09:00:00', '24 hours before appointment'),
(54, 1, 18, '06:00:00', '3 hours before appointment'),
(55, 1, 19, '19:00:00', 'Appointment booked'),
(56, 1, 19, '10:00:00', '24 hours before appointment'),
(57, 1, 19, '07:00:00', '3 hours before appointment'),
(58, 1, 20, '19:00:00', 'Appointment booked'),
(59, 1, 20, '10:00:00', '24 hours before appointment'),
(60, 1, 20, '07:00:00', '3 hours before appointment');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_tbl`
--

CREATE TABLE `appointment_tbl` (
  `APPOINTMENT_ID` int(11) NOT NULL,
  `PATIENT_ID` int(11) NOT NULL,
  `DOCTOR_ID` int(11) NOT NULL,
  `SCHEDULE_ID` int(11) NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp(),
  `APPOINTMENT_DATE` date NOT NULL,
  `APPOINTMENT_TIME` time NOT NULL,
  `STATUS` enum('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_tbl`
--

INSERT INTO appointment_tbl (APPOINTMENT_ID, PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(1, 1, 1, 1, '2026-03-20 00:00:00', '2026-03-22', '09:00:00', 'COMPLETED'),
(2, 2, 1, 2, '2026-03-21 00:00:00', '2026-03-23', '10:00:00', 'COMPLETED'),
(3, 3, 1, 3, '2026-03-22 00:00:00', '2026-03-24', '11:00:00', 'COMPLETED'),
(4, 4, 2, 4, '2026-03-23 00:00:00', '2026-03-25', '09:00:00', 'COMPLETED'),
(5, 5, 2, 5, '2026-03-24 00:00:00', '2026-03-26', '10:00:00', 'COMPLETED'),
(6, 6, 2, 6, '2026-03-25 00:00:00', '2026-03-27', '11:00:00', 'COMPLETED'),
(7, 7, 3, 7, '2026-03-26 00:00:00', '2026-03-28', '09:00:00', 'COMPLETED'),
(8, 8, 3, 8, '2026-03-27 00:00:00', '2026-03-29', '10:00:00', 'COMPLETED'),
(9, 9, 3, 9, '2026-03-28 00:00:00', '2026-03-30', '11:00:00', 'COMPLETED'),
(10, 10, 4, 10, '2026-03-29 00:00:00', '2026-03-31', '09:00:00', 'COMPLETED'),
(11, 1, 4, 11, '2026-03-30 00:00:00', '2026-04-01', '10:00:00', 'COMPLETED'),
(12, 2, 4, 12, '2026-03-31 00:00:00', '2026-04-02', '11:00:00', 'COMPLETED'),
(13, 3, 5, 13, '2026-04-01 00:00:00', '2026-04-03', '09:00:00', 'COMPLETED'),
(14, 4, 5, 14, '2026-04-02 00:00:00', '2026-04-04', '10:00:00', 'COMPLETED'),
(15, 5, 5, 15, '2026-04-03 00:00:00', '2026-04-05', '11:00:00', 'COMPLETED'),
(16, 6, 6, 16, '2026-04-04 00:00:00', '2026-04-06', '09:00:00', 'COMPLETED'),
(17, 7, 6, 17, '2026-04-05 00:00:00', '2026-04-07', '10:00:00', 'COMPLETED'),
(18, 8, 6, 18, '2026-04-06 00:00:00', '2026-04-08', '11:00:00', 'COMPLETED'),
(19, 9, 7, 19, '2026-04-07 00:00:00', '2026-04-09', '09:00:00', 'COMPLETED'),
(20, 10, 7, 20, '2026-04-08 00:00:00', '2026-04-10', '10:00:00', 'COMPLETED'),
(21, 1, 7, 21, '2026-04-09 00:00:00', '2026-04-11', '11:00:00', 'SCHEDULED'),
(22, 2, 8, 22, '2026-04-10 00:00:00', '2026-04-12', '09:00:00', 'SCHEDULED'),
(23, 3, 8, 23, '2026-04-11 00:00:00', '2026-04-13', '10:00:00', 'SCHEDULED'),
(24, 4, 8, 24, '2026-04-12 00:00:00', '2026-04-14', '11:00:00', 'SCHEDULED'),
(25, 5, 9, 25, '2026-04-13 00:00:00', '2026-04-15', '09:00:00', 'SCHEDULED'),
(26, 6, 9, 26, '2026-04-14 00:00:00', '2026-04-16', '10:00:00', 'SCHEDULED'),
(27, 7, 9, 27, '2026-04-15 00:00:00', '2026-04-17', '11:00:00', 'SCHEDULED'),
(28, 8, 10, 28, '2026-04-16 00:00:00', '2026-04-18', '09:00:00', 'SCHEDULED'),
(29, 9, 10, 29, '2026-04-17 00:00:00', '2026-04-19', '10:00:00', 'SCHEDULED'),
(30, 10, 10, 30, '2026-04-18 00:00:00', '2026-04-20', '11:00:00', 'SCHEDULED');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_tbl`
--

CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int(11) NOT NULL,
  `DOCTOR_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `AVAILABLE_DAY` enum('MON','TUE','WED','THUR','FRI','SAT','SUN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule_tbl`
--

INSERT INTO `doctor_schedule_tbl` (`SCHEDULE_ID`, `DOCTOR_ID`, `RECEPTIONIST_ID`, `START_TIME`, `END_TIME`, `AVAILABLE_DAY`) VALUES
(1, 1, 1, '09:00:00', '17:00:00', 'MON'),
(2, 1, 1, '09:00:00', '17:00:00', 'WED'),
(3, 1, 1, '09:00:00', '17:00:00', 'FRI'),
(4, 2, 1, '10:00:00', '18:00:00', 'TUE'),
(5, 2, 1, '10:00:00', '18:00:00', 'THUR'),
(6, 2, 1, '10:00:00', '18:00:00', 'SAT'),
(7, 3, 2, '08:00:00', '16:00:00', 'MON'),
(8, 3, 2, '08:00:00', '16:00:00', 'WED'),
(9, 3, 2, '08:00:00', '16:00:00', 'FRI'),
(10, 4, 2, '09:00:00', '17:00:00', 'TUE'),
(11, 4, 2, '09:00:00', '17:00:00', 'THUR'),
(12, 4, 2, '09:00:00', '17:00:00', 'SAT'),
(13, 5, 3, '10:00:00', '18:00:00', 'MON'),
(14, 5, 3, '10:00:00', '18:00:00', 'WED'),
(15, 5, 3, '10:00:00', '18:00:00', 'FRI'),
(16, 6, 3, '08:00:00', '16:00:00', 'TUE'),
(17, 6, 3, '08:00:00', '16:00:00', 'THUR'),
(18, 6, 3, '08:00:00', '16:00:00', 'SAT'),
(19, 7, 4, '09:00:00', '17:00:00', 'MON'),
(20, 7, 4, '09:00:00', '17:00:00', 'WED'),
(21, 7, 4, '09:00:00', '17:00:00', 'FRI'),
(22, 8, 4, '10:00:00', '18:00:00', 'TUE'),
(23, 8, 4, '10:00:00', '18:00:00', 'THUR'),
(24, 8, 4, '10:00:00', '18:00:00', 'SAT'),
(25, 9, 1, '08:00:00', '16:00:00', 'MON'),
(26, 9, 1, '08:00:00', '16:00:00', 'WED'),
(27, 9, 1, '08:00:00', '16:00:00', 'FRI'),
(28, 10, 1, '09:00:00', '17:00:00', 'TUE'),
(29, 10, 1, '09:00:00', '17:00:00', 'THUR'),
(30, 10, 1, '09:00:00', '17:00:00', 'SAT'),
(31, 11, 1, '09:00:00', '17:00:00', 'MON'),
(32, 11, 1, '09:00:00', '17:00:00', 'WED'),
(33, 11, 1, '09:00:00', '17:00:00', 'FRI'),
(34, 12, 1, '10:00:00', '18:00:00', 'TUE'),
(35, 12, 1, '10:00:00', '18:00:00', 'THUR'),
(36, 12, 1, '10:00:00', '18:00:00', 'SAT'),
(37, 13, 2, '08:00:00', '16:00:00', 'MON'),
(38, 13, 2, '08:00:00', '16:00:00', 'WED'),
(39, 13, 2, '08:00:00', '16:00:00', 'FRI'),
(40, 14, 2, '09:00:00', '17:00:00', 'TUE'),
(41, 14, 2, '09:00:00', '17:00:00', 'THUR'),
(42, 14, 2, '09:00:00', '17:00:00', 'SAT'),
(43, 15, 3, '10:00:00', '18:00:00', 'MON'),
(44, 15, 3, '10:00:00', '18:00:00', 'WED'),
(45, 15, 3, '10:00:00', '18:00:00', 'FRI');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_tbl`
--

CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int(11) NOT NULL,
  `SPECIALISATION_ID` int(11) NOT NULL,
  `PROFILE_IMAGE` varchar(255) NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date NOT NULL,
  `DOJ` date NOT NULL,
  `USERNAME` varchar(20) NOT NULL,
  `PSWD` varchar(60) NOT NULL,
  `PHONE` bigint(20) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') NOT NULL,
  `EDUCATION` varchar(50) NOT NULL,
  `STATUS` enum('pending','approved','rejected') DEFAULT 'pending',
  `SECURITY_QUESTION` varchar(255) NOT NULL,
  `SECURITY_ANSWER` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_tbl`
--

INSERT INTO `doctor_tbl` (`DOCTOR_ID`, `SPECIALISATION_ID`, `PROFILE_IMAGE`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `USERNAME`, `PSWD`, `PHONE`, `EMAIL`, `GENDER`, `EDUCATION`, `STATUS`, `SECURITY_QUESTION`, `SECURITY_ANSWER`) VALUES
(1, 1, 'uploads/rajesh.jpeg', 'Rajesh', 'Kumar', '1975-03-15', '2010-06-20', 'Dr_rajesh01', '$2y$10$J2Pa/v7n9cwMwGL/URSOjOhTImcZqkphMjKjdnPWsFWWY0uQYFiGG', 9876543210, 'rajesh.kumar@gmail.com', 'MALE', 'MBBS, MD (Pediatrics)', 'approved', 'What was the name of your first school?', 'St. Xavier School'),
(2, 1, 'uploads/priya.jpeg', 'Priya', 'Sharma', '1980-07-22', '2012-09-15', 'Dr_priya02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543211, 'priya.sharma@gmail.com', 'FEMALE', 'MBBS, DCH', 'approved', 'What is your favorite food from childhood?', 'Khichdi'),
(3, 1, 'uploads/amit.jpeg', 'Amit', 'Patel', '1978-11-10', '2015-03-25', 'Dr_amit03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543212, 'amit.patel@gmail.com', 'MALE', 'MBBS, DNB (Pediatrics)', 'approved', 'Where did you go for your first school trip?', 'Ahmedabad'),
(4, 2, 'uploads/sunita1.jpeg', 'Sunita', 'Reddy', '1976-05-18', '2011-07-10', 'Dr_sunita04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543213, 'sunita.reddy@gmail.com', 'FEMALE', 'MBBS, MD (Cardiology)', 'approved', 'What was the nickname your family calls you?', 'Suni'),
(5, 2, 'uploads/vikram1.jpeg', 'Vikram', 'Singh', '1973-09-25', '2009-12-05', 'Dr_vikram05', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543214, 'vikram.singh@gmail.com', 'MALE', 'MBBS, DM (Cardiology)', 'approved', 'What was the name of your first school?', 'Army Public School'),
(6, 2, 'uploads/anjali.jpeg', 'Anjali', 'Gupta', '1982-02-14', '2014-08-20', 'Dr_anjali06', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543215, 'anjali.gupta@gmail.com', 'FEMALE', 'MBBS, MD (Cardiology)', 'approved', 'What is your favorite food from childhood?', 'Rajma chawal'),
(7, 3, 'uploads/rahul.jpeg', 'Rahul', 'Verma', '1977-08-30', '2013-04-15', 'Dr_rahul07', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543216, 'rahul.verma@gmail.com', 'MALE', 'MBBS, MS (Orthopedics)', 'approved', 'Where did you go for your first school trip?', 'Agra'),
(8, 3, 'uploads/meera.jpeg', 'Meera', 'Joshi', '1981-12-05', '2016-01-10', 'Dr_meera08', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543217, 'meera.joshi@gmail.com', 'FEMALE', 'MBBS, DNB (Orthopedics)', 'approved', 'What was the nickname your family calls you?', 'Meeru'),
(9, 4, 'uploads/sanjay.jpeg', 'Sanjay', 'Malhotra', '1974-06-20', '2010-11-30', 'Dr_sanjay09', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543218, 'sanjay.malhotra@gmail.com', 'MALE', 'MBBS, MD, DM (Neurology)', 'approved', 'What was the name of your first school?', 'Kendriya Vidyalaya'),
(10, 4, 'uploads/kavita1.jpeg', 'Kavita', 'Nair', '1979-04-12', '2015-07-25', 'Dr_kavita10', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543219, 'kavita.nair@gmail.com', 'FEMALE', 'MBBS, MD (Neurology)', 'approved', 'What is your favorite food from childhood?', 'Appam'),
(11, 1, 'uploads/suresh.jpeg', 'Suresh', 'Reddy', '1976-01-18', '2011-05-10', 'Dr_suresh11', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543220, 'suresh.reddy@gmail.com', 'MALE', 'MBBS, MD (Pediatrics)', 'approved', 'Where did you go for your first school trip?', 'Tirupati'),
(12, 1, 'uploads/lakshmi.jpeg', 'Lakshmi', 'Iyer', '1983-09-05', '2014-02-20', 'Dr_lakshmi12', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543221, 'lakshmi.iyer@gmail.com', 'FEMALE', 'MBBS, DCH', 'approved', 'What was the nickname your family calls you?', 'Lakku'),
(13, 1, 'uploads/arun.jpeg', 'Arun', 'Shah', '1979-12-22', '2013-08-15', 'Dr_arun13', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543222, 'arun.shah@gmail.com', 'MALE', 'MBBS, DNB (Pediatrics)', 'approved', 'What was the name of your first school?', 'Shree Ram School'),
(14, 1, 'uploads/Deepa.jpeg', 'Deepa', 'Nair', '1981-04-08', '2016-11-01', 'Dr_deepa14', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543223, 'deepa.nair@gmail.com', 'FEMALE', 'MBBS, MD (Pediatrics)', 'approved', 'What is your favorite food from childhood?', 'Pav bhaji'),
(15, 1, 'uploads/karthik.jpeg', 'Karthik', 'Pillai', '1977-07-30', '2010-03-12', 'Dr_karthik15', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543224, 'karthik.pillai@gmail.com', 'MALE', 'MBBS, DCH', 'approved', 'Where did you go for your first school trip?', 'Munnar');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `RATING` int(11) NOT NULL,
  `COMMENTS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_tbl`
--

INSERT INTO `feedback_tbl` (`FEEDBACK_ID`, `APPOINTMENT_ID`, `RATING`, `COMMENTS`) VALUES
(1, 1, 5, 'Doctor was very patient with my child. Explained everything clearly.'),
(2, 2, 2, 'Follow-up was thorough. Treatment is working well.'),
(3, 3, 4, 'Good consultation, but waiting time was a bit long.'),
(4, 4, 5, 'Doctor took time to explain the test results. Very satisfied.'),
(5, 5, 5, 'Doctor handled my child`s asthma attack very professionally.'),
(6, 6, 4, 'Good follow-up. Wish the appointment time was a bit longer.'),
(7, 7, 5, 'Excellent doctor. Very knowledgeable about diabetes management.'),
(8, 8, 5, 'Very happy with the treatment plan. Feeling much better now.'),
(9, 9, 4, 'Doctor is good, but the clinic was crowded today.'),
(10, 10, 5, 'Great follow-up. Blood pressure is under control now.'),
(11, 11, 5, 'Doctor explained the heart condition very well. Very reassuring.'),
(12, 12, 5, 'Excellent care. Medication is working perfectly.'),
(13, 13, 4, 'Good diagnosis. Physical therapy recommendations are helpful.'),
(14, 14, 5, 'Pain has reduced significantly. Thank you, doctor.'),
(15, 15, 5, 'Doctor explained the surgery options clearly. Very professional.'),
(16, 16, 4, 'Improving well. Physiotherapy is helping a lot.'),
(17, 17, 5, 'Doctor identified my migraine triggers accurately. Very grateful.'),
(18, 18, 5, 'Medication is working well. Migraine frequency has reduced.'),
(19, 19, 5, 'Doctor handled my seizure episode very calmly. Excellent care.'),
(20, 20, 5, 'Medication is effective. No seizures since starting treatment.');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_reminder_tbl`
--

CREATE TABLE `medicine_reminder_tbl` (
  `MEDICINE_REMINDER_ID` int(11) NOT NULL,
  `MEDICINE_ID` int(11) NOT NULL,
  `CREATOR_ROLE` enum('PATIENT','RECEPTIONIST') NOT NULL,
  `CREATOR_ID` int(11) NOT NULL,
  `PATIENT_ID` int(11) NOT NULL,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL,
  `REMINDER_TIME` time NOT NULL,
  `REMARKS` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_reminder_tbl`
--

INSERT INTO medicine_reminder_tbl (MEDICINE_REMINDER_ID, MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) VALUES
(7, 5, 'RECEPTIONIST', 1, 2, '2026-03-11', '2026-04-11', '08:00:00', 'Morning dose'),
(8, 6, 'RECEPTIONIST', 1, 2, '2026-03-11', '2026-04-11', '09:00:00', 'Morning dose'),
(9, 3, 'RECEPTIONIST', 2, 3, '2026-03-13', '2026-04-13', '08:00:00', 'Morning dose'),
(10, 3, 'RECEPTIONIST', 2, 3, '2026-03-13', '2026-04-13', '20:00:00', 'Evening dose'),
(11, 7, 'RECEPTIONIST', 2, 4, '2026-03-16', '2026-04-16', '08:00:00', 'Morning dose'),
(12, 8, 'RECEPTIONIST', 2, 4, '2026-03-16', '2026-04-16', '21:00:00', 'Night dose'),
(13, 9, 'RECEPTIONIST', 3, 5, '2026-03-21', '2026-04-21', '08:00:00', 'Morning dose'),
(14, 9, 'RECEPTIONIST', 3, 5, '2026-03-21', '2026-04-21', '14:00:00', 'Afternoon dose'),
(15, 9, 'RECEPTIONIST', 3, 5, '2026-03-21', '2026-04-21', '20:00:00', 'Evening dose'),
(16, 10, 'RECEPTIONIST', 3, 5, '2026-03-22', '2026-04-22', '09:00:00', 'Morning dose'),
(17, 10, 'RECEPTIONIST', 3, 5, '2026-03-22', '2026-04-22', '21:00:00', 'Night dose'),
(18, 11, 'RECEPTIONIST', 3, 6, '2026-04-26', '2026-05-26', '08:00:00', 'Morning dose'),
(19, 11, 'RECEPTIONIST', 3, 6, '2026-04-26', '2026-05-26', '14:00:00', 'Afternoon dose'),
(20, 11, 'RECEPTIONIST', 3, 6, '2026-04-26', '2026-05-26', '20:00:00', 'Evening dose'),
(21, 13, 'RECEPTIONIST', 1, 9, '2026-04-25', '2026-05-25', '08:00:00', 'Morning dose'),
(22, 13, 'RECEPTIONIST', 1, 9, '2026-04-25', '2026-05-25', '20:00:00', 'Evening dose'),
(23, 14, 'RECEPTIONIST', 1, 9, '2026-04-25', '2026-05-25', '21:00:00', 'Night dose'),
(24, 15, 'RECEPTIONIST', 1, 10, '2026-05-01', '2026-05-31', '08:00:00', 'Morning dose'),
(25, 15, 'RECEPTIONIST', 1, 10, '2026-05-01', '2026-05-31', '14:00:00', 'Afternoon dose'),
(26, 15, 'RECEPTIONIST', 1, 10, '2026-05-01', '2026-05-31', '20:00:00', 'Evening dose'),
(27, 16, 'RECEPTIONIST', 1, 10, '2026-05-01', '2026-05-31', '21:00:00', 'Night dose');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_tbl`
--

CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `MED_NAME` varchar(25) NOT NULL,
  `DESCRIPTION` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_tbl`
--

INSERT INTO `medicine_tbl` (`MEDICINE_ID`, `RECEPTIONIST_ID`, `MED_NAME`, `DESCRIPTION`) VALUES
(1, 1, 'Paracetamol Syrup', 'Fever and pain relief for children'),
(2, 1, 'Amoxicillin', 'Antibiotic for bacterial infections'),
(3, 1, 'Salbutamol Inhaler', 'Asthma relief medication'),
(4, 1, 'Vitamin D Drops', 'Vitamin D supplement for infants'),
(5, 2, 'Atenolol', 'Beta-blocker for hypertension'),
(6, 2, 'Aspirin', 'Blood thinner for heart conditions'),
(7, 2, 'Lisinopril', 'ACE inhibitor for blood pressure'),
(8, 2, 'Simvastatin', 'Statin for cholesterol management'),
(9, 3, 'Ibuprofen', 'Anti-inflammatory for joint pain'),
(10, 3, 'Calcium Carbonate', 'Calcium supplement for bone health'),
(11, 3, 'Gabapentin', 'Nerve pain medication'),
(12, 3, 'Tramadol', 'Pain relief for severe pain'),
(13, 4, 'Levetiracetam', 'Anti-epileptic medication'),
(14, 4, 'Amitriptyline', 'Tricyclic antidepressant for nerve pain'),
(15, 4, 'Ropinirole', 'Medication for Parkinson\'s disease'),
(16, 4, 'Topiramate', 'Anti-epileptic and migraine prevention');

-- --------------------------------------------------------

--
-- Table structure for table `notification_seen_tbl`
--

CREATE TABLE `notification_seen_tbl` (
  `SEEN_ID` int(11) NOT NULL,
  `USER_TYPE` varchar(30) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NOTIF_KEY` varchar(100) NOT NULL,
  `SEEN_AT` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_seen_tbl`
--

INSERT INTO `notification_seen_tbl` (`SEEN_ID`, `USER_TYPE`, `USER_ID`, `NOTIF_KEY`, `SEEN_AT`) VALUES
(1, 'receptionist', 1, '7a9179318e086fe25dc79ff557cfab3e875ebb6c', '2026-03-21 21:36:29'),
(2, 'receptionist', 1, '3d28211dafd8e0fc5edf406098f33662ef94ea4a', '2026-03-21 21:36:29'),
(3, 'receptionist', 1, '3a14bfa5d3a7f6736f3bbdcba9654e91fa7f9822', '2026-03-21 21:36:29'),
(4, 'receptionist', 1, 'ae2ea64493b68d5d5e844f09381a7c99de4aef98', '2026-03-21 21:36:29'),
(5, 'receptionist', 1, '0c3660fbfc92e106ddd5b4dcf3f094fabcd942c4', '2026-03-21 21:36:29'),
(6, 'receptionist', 1, '7b314175825024447f75593b0c0803d0013ae963', '2026-03-21 21:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_type` enum('patient','doctor','receptionist') NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_type`, `user_id`, `phone`, `otp_hash`, `expires_at`, `attempts`, `created_at`) VALUES
(1, 'doctor', 1, '9876543210', '$2y$10$60fLZchkBICvTDdVbaR.8uFqDi2ND0uSa.Aiur2bh0QGYAuGEEG4i', '2026-03-13 15:09:30', 0, '2026-03-13 13:59:30');

-- --------------------------------------------------------

--
-- Table structure for table `patient_tbl`
--

CREATE TABLE `patient_tbl` (
  `PATIENT_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `USERNAME` varchar(20) NOT NULL,
  `PSWD` varchar(60) NOT NULL,
  `DOB` date NOT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') NOT NULL,
  `BLOOD_GROUP` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
  `PHONE` bigint(20) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `ADDRESS` text NOT NULL,
  `MEDICAL_HISTORY_FILE` varchar(255) NOT NULL,
  `SECURITY_QUESTION` varchar(255) NOT NULL,
  `SECURITY_ANSWER` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tbl`
--

INSERT INTO `patient_tbl` (`PATIENT_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PSWD`, `DOB`, `GENDER`, `BLOOD_GROUP`, `PHONE`, `EMAIL`, `ADDRESS`, `MEDICAL_HISTORY_FILE`, `SECURITY_QUESTION`, `SECURITY_ANSWER`) VALUES
(1, 'Arjun', 'Mishra', 'Arjun_m01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1990-05-15', 'MALE', 'B+', 9876543201, 'arjun.mishra@gmail.com', '123, Park Street, Mumbai', '', 'What was the name of your first school?', 'Sunrise Public School'),
(2, 'Pooja', 'Sharma', 'Pooja_s02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1985-08-22', 'FEMALE', 'A+', 9876543202, 'pooja.sharma@gmail.com', '456, MG Road, Delhi', '', 'What is your favorite food from childhood?', 'Mango ice cream'),
(3, 'Rohan', 'Patel', 'Rohan_p03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1992-12-10', 'MALE', 'O+', 9876543203, 'rohan.patel@gmail.com', '789, Brigade Road, Bangalore', '', 'Where did you go for your first school trip?', 'Mysore'),
(4, 'Neha', 'Gupta', 'Neha_g04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1988-03-18', 'FEMALE', 'AB+', 9876543204, 'neha.gupta@gmail.com', '321, FC Road, Pune', '', 'What was the nickname your family calls you?', 'Nehu'),
(5, 'Amit', 'Kumar', 'Amit_k05', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1987-07-25', 'MALE', 'A-', 9876543205, 'amit.kumar@gmail.com', '654, Jubilee Hills, Hyderabad', '', 'What was the name of your first school?', 'St. Mary\'s High School'),
(6, 'Sneha', 'Reddy', 'Sneha_r06', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1991-11-30', 'FEMALE', 'B-', 9876543206, 'sneha.reddy@gmail.com', '987, Banjara Hills, Hyderabad', '', 'What is your favorite food from childhood?', 'Dosa'),
(7, 'Vikas', 'Singh', 'Vikas_s07', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1986-09-14', 'MALE', 'O-', 9876543207, 'vikas.singh@gmail.com', '147, Connaught Place, Delhi', '', 'Where did you go for your first school trip?', 'Shimla'),
(8, 'Anjali', 'Desai', 'Anjali_d08', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1993-02-05', 'FEMALE', 'AB-', 9876543208, 'anjali.desai@gmail.com', '258, Marine Drive, Mumbai', '', 'What was the nickname your family calls you?', 'Anju'),
(9, 'Rahul', 'Verma', 'Rahul_v09', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1989-06-20', 'MALE', 'A+', 9876543209, 'rahul.verma@gmail.com', '369, Park Street, Kolkata', '', 'What was the name of your first school?', 'Green Valley School'),
(10, 'Kavita', 'Sharma', 'Kavita_s10', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1994-04-12', 'FEMALE', 'B+', 9876543210, 'kavita.sharma@gmail.com', '741, MG Road, Bangalore', '', 'What is your favorite food from childhood?', 'Pani puri');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `PAYMENT_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `AMOUNT` decimal(10,2) NOT NULL,
  `PAYMENT_DATE` date NOT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') NOT NULL,
  `STATUS` enum('COMPLETED','FAILED') NOT NULL,
  `TRANSACTION_ID` varchar(36) NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_tbl`
--

INSERT INTO payment_tbl (PAYMENT_ID, APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID, CREATED_AT) VALUES
(1, 1, 300.00, '2026-03-20', 'CREDIT CARD', 'COMPLETED', 'TXN100000001', '2026-03-20 00:00:00'),
(2, 2, 300.00, '2026-03-21', 'GOOGLE PAY', 'COMPLETED', 'TXN100000002', '2026-03-21 00:00:00'),
(3, 3, 300.00, '2026-03-22', 'UPI', 'COMPLETED', 'TXN100000003', '2026-03-22 00:00:00'),
(4, 4, 300.00, '2026-03-23', 'NET BANKING', 'COMPLETED', 'TXN100000004', '2026-03-23 00:00:00'),
(5, 5, 300.00, '2026-03-24', 'CREDIT CARD', 'COMPLETED', 'TXN100000005', '2026-03-24 00:00:00'),
(6, 6, 300.00, '2026-03-25', 'GOOGLE PAY', 'COMPLETED', 'TXN100000006', '2026-03-25 00:00:00'),
(7, 7, 300.00, '2026-03-26', 'UPI', 'COMPLETED', 'TXN100000007', '2026-03-26 00:00:00'),
(8, 8, 300.00, '2026-03-27', 'NET BANKING', 'COMPLETED', 'TXN100000008', '2026-03-27 00:00:00'),
(9, 9, 300.00, '2026-03-28', 'CREDIT CARD', 'COMPLETED', 'TXN100000009', '2026-03-28 00:00:00'),
(10, 10, 300.00, '2026-03-29', 'GOOGLE PAY', 'COMPLETED', 'TXN100000010', '2026-03-29 00:00:00'),
(11, 11, 300.00, '2026-03-30', 'UPI', 'COMPLETED', 'TXN100000011', '2026-03-30 00:00:00'),
(12, 12, 300.00, '2026-03-31', 'NET BANKING', 'COMPLETED', 'TXN100000012', '2026-03-31 00:00:00'),
(13, 13, 300.00, '2026-04-01', 'CREDIT CARD', 'COMPLETED', 'TXN100000013', '2026-04-01 00:00:00'),
(14, 14, 300.00, '2026-04-02', 'GOOGLE PAY', 'COMPLETED', 'TXN100000014', '2026-04-02 00:00:00'),
(15, 15, 300.00, '2026-04-03', 'UPI', 'COMPLETED', 'TXN100000015', '2026-04-03 00:00:00'),
(16, 16, 300.00, '2026-04-04', 'NET BANKING', 'COMPLETED', 'TXN100000016', '2026-04-04 00:00:00'),
(17, 17, 300.00, '2026-04-05', 'CREDIT CARD', 'COMPLETED', 'TXN100000017', '2026-04-05 00:00:00'),
(18, 18, 300.00, '2026-04-06', 'GOOGLE PAY', 'COMPLETED', 'TXN100000018', '2026-04-06 00:00:00'),
(19, 19, 300.00, '2026-04-07', 'UPI', 'COMPLETED', 'TXN100000019', '2026-04-07 00:00:00'),
(20, 20, 300.00, '2026-04-08', 'NET BANKING', 'COMPLETED', 'TXN100000020', '2026-04-08 00:00:00'),
(21, 21, 300.00, '2026-04-09', 'CREDIT CARD', 'COMPLETED', 'TXN100000021', '2026-04-09 00:00:00'),
(22, 22, 300.00, '2026-04-10', 'GOOGLE PAY', 'COMPLETED', 'TXN100000022', '2026-04-10 00:00:00'),
(23, 23, 300.00, '2026-04-11', 'UPI', 'COMPLETED', 'TXN100000023', '2026-04-11 00:00:00'),
(24, 24, 300.00, '2026-04-12', 'NET BANKING', 'COMPLETED', 'TXN100000024', '2026-04-12 00:00:00'),
(25, 25, 300.00, '2026-04-13', 'CREDIT CARD', 'COMPLETED', 'TXN100000025', '2026-04-13 00:00:00'),
(26, 26, 300.00, '2026-04-14', 'GOOGLE PAY', 'COMPLETED', 'TXN100000026', '2026-04-14 00:00:00'),
(27, 27, 300.00, '2026-04-15', 'UPI', 'COMPLETED', 'TXN100000027', '2026-04-15 00:00:00'),
(28, 28, 300.00, '2026-04-16', 'NET BANKING', 'COMPLETED', 'TXN100000028', '2026-04-16 00:00:00'),
(29, 29, 300.00, '2026-04-17', 'CREDIT CARD', 'COMPLETED', 'TXN100000029', '2026-04-17 00:00:00'),
(30, 30, 300.00, '2026-04-18', 'GOOGLE PAY', 'COMPLETED', 'TXN100000030', '2026-04-18 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicine_tbl`
--

CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int(11) NOT NULL,
  `MEDICINE_ID` int(11) NOT NULL,
  `DOSAGE` varchar(30) NOT NULL,
  `DURATION` varchar(30) NOT NULL,
  `FREQUENCY` varchar(30) NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_medicine_tbl`
--

INSERT INTO prescription_medicine_tbl (PRESCRIPTION_ID, MEDICINE_ID, DOSAGE, DURATION, FREQUENCY, CREATED_AT) VALUES
(1, 1, '5ml', '5 days', 'Twice daily', '2026-03-03 10:00:00'),
(1, 2, '125mg', '7 days', 'Three times daily', '2026-03-03 10:00:00'),
(2, 1, '5ml', '3 days', 'As needed', '2026-03-07 10:00:00'),
(2, 4, '1ml', '30 days', 'Once daily', '2026-03-07 10:00:00'),
(3, 5, '50mg', '30 days', 'Once daily', '2026-03-04 11:00:00'),
(3, 6, '75mg', '30 days', 'Once daily', '2026-03-04 11:00:00'),
(4, 5, '50mg', '30 days', 'Once daily', '2026-03-10 11:00:00'),
(4, 7, '10mg', '30 days', 'Once daily', '2026-03-10 11:00:00'),
(5, 1, '5ml', '3 days', 'As needed for fever', '2026-03-09 09:00:00'),
(5, 3, '2 puffs', '30 days', 'As needed', '2026-03-09 09:00:00'),
(6, 3, '2 puffs', '30 days', 'As needed', '2026-03-12 09:00:00'),
(6, 4, '1ml', '30 days', 'Once daily', '2026-03-12 09:00:00'),
(7, 7, '10mg', '30 days', 'Once daily', '2026-03-14 10:00:00'),
(7, 8, '20mg', '30 days', 'Once daily at night', '2026-03-14 10:00:00'),
(8, 7, '10mg', '30 days', 'Once daily', '2026-03-15 10:00:00'),
(8, 8, '20mg', '30 days', 'Once daily at night', '2026-03-15 10:00:00'),
(9, 5, '50mg', '30 days', 'Once daily', '2026-03-17 11:00:00'),
(9, 7, '10mg', '30 days', 'Once daily', '2026-03-17 11:00:00'),
(10, 5, '50mg', '30 days', 'Once daily', '2026-03-20 11:00:00'),
(10, 7, '10mg', '30 days', 'Once daily', '2026-03-20 11:00:00'),
(11, 5, '25mg', '30 days', 'Twice daily', '2026-04-22 09:00:00'),
(11, 6, '75mg', '30 days', 'Once daily', '2026-04-22 09:00:00'),
(12, 5, '25mg', '30 days', 'Twice daily', '2026-04-25 09:00:00'),
(12, 6, '75mg', '30 days', 'Once daily', '2026-04-25 09:00:00'),
(13, 9, '400mg', '30 days', 'Three times daily', '2026-04-06 10:00:00'),
(13, 10, '500mg', '30 days', 'Twice daily', '2026-04-06 10:00:00'),
(14, 9, '400mg', '30 days', 'As needed', '2026-04-11 10:00:00'),
(14, 10, '500mg', '30 days', 'Twice daily', '2026-04-11 10:00:00'),
(15, 11, '300mg', '30 days', 'Three times daily', '2026-04-16 11:00:00'),
(15, 12, '50mg', '15 days', 'As needed for pain', '2026-04-16 11:00:00'),
(16, 11, '300mg', '30 days', 'Three times daily', '2026-04-18 11:00:00'),
(16, 12, '50mg', '15 days', 'As needed for pain', '2026-04-18 11:00:00'),
(17, 13, '500mg', '30 days', 'Twice daily', '2026-04-21 09:00:00'),
(17, 14, '25mg', '30 days', 'Once daily at night', '2026-04-21 09:00:00'),
(18, 13, '500mg', '30 days', 'Twice daily', '2026-04-24 09:00:00'),
(18, 14, '25mg', '30 days', 'Once daily at night', '2026-04-24 09:00:00'),
(19, 15, '2mg', '30 days', 'Three times daily', '2026-04-28 10:00:00'),
(19, 16, '100mg', '30 days', 'Once daily at night', '2026-04-28 10:00:00'),
(20, 15, '2mg', '30 days', 'Three times daily', '2026-04-30 10:00:00'),
(20, 16, '100mg', '30 days', 'Once daily at night', '2026-04-30 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_tbl`
--

CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `ISSUE_DATE` date NOT NULL,
  `HEIGHT_CM` int(11) NOT NULL,
  `WEIGHT_KG` decimal(5,2) NOT NULL,
  `BLOOD_PRESSURE` smallint(6) NOT NULL,
  `DIABETES` enum('NO','TYPE-1','TYPE-2','PRE-DIABTIC') NOT NULL,
  `SYMPTOMS` text NOT NULL,
  `DIAGNOSIS` text NOT NULL,
  `ADDITIONAL_NOTES` text NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_tbl`
--

INSERT INTO prescription_tbl (PRESCRIPTION_ID, APPOINTMENT_ID, ISSUE_DATE, HEIGHT_CM, WEIGHT_KG, BLOOD_PRESSURE, DIABETES, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES, CREATED_AT) VALUES
(1, 1, '2026-03-03', 120, 25.00, 110, 'NO', 'Fever, cough, and cold', 'Upper respiratory tract infection', 'Advise plenty of rest and fluids', '2026-03-03 10:00:00'),
(2, 2, '2026-03-07', 120, 25.00, 110, 'NO', 'Follow-up check', 'Recovering well', 'Continue prescribed medication', '2026-03-07 10:00:00'),
(3, 3, '2026-03-04', 160, 55.00, 120, 'NO', 'Chest pain, shortness of breath', 'Angina', 'Stress management recommended', '2026-03-04 11:00:00'),
(4, 4, '2026-03-10', 160, 55.00, 120, 'NO', 'Follow-up check', 'Stable condition', 'Continue medication as prescribed', '2026-03-10 11:00:00'),
(5, 5, '2026-03-09', 110, 20.00, 100, 'NO', 'Wheezing, difficulty breathing', 'Asthma exacerbation', 'Avoid triggers like dust and pollen', '2026-03-09 09:00:00'),
(6, 6, '2026-03-12', 110, 20.00, 100, 'NO', 'Follow-up check', 'Asthma under control', 'Continue inhaler as needed', '2026-03-12 09:00:00'),
(7, 7, '2026-03-14', 165, 60.00, 130, 'TYPE-2', 'Chest discomfort, palpitations', 'Hypertension with diabetes', 'Strict diet control required', '2026-03-14 10:00:00'),
(8, 8, '2026-03-15', 165, 60.00, 130, 'TYPE-2', 'Follow-up check', 'Blood sugar levels improving', 'Continue medication and exercise', '2026-03-15 10:00:00'),
(9, 9, '2026-03-17', 170, 75.00, 140, 'PRE-DIABTIC', 'High blood pressure, dizziness', 'Hypertension', 'Reduce salt intake', '2026-03-17 11:00:00'),
(10, 10, '2026-03-20', 170, 75.00, 140, 'PRE-DIABTIC', 'Follow-up check', 'Blood pressure controlled', 'Continue lifestyle changes', '2026-03-20 11:00:00'),
(11, 11, '2026-04-22', 155, 50.00, 0, 'NO', 'Irregular heartbeat', 'Arrhythmia', 'Avoid caffeine and stress', '2026-04-22 09:00:00'),
(12, 12, '2026-04-25', 155, 50.00, 120, 'NO', 'Follow-up check', 'Heart rhythm normal', 'Continue medication as prescribed', '2026-04-25 09:00:00'),
(13, 13, '2026-04-06', 175, 80.00, 125, 'NO', 'Knee pain, difficulty walking', 'Osteoarthritis', 'Physical therapy recommended', '2026-04-06 10:00:00'),
(14, 14, '2026-04-11', 175, 80.00, 125, 'NO', 'Follow-up check', 'Pain management effective', 'Continue exercises', '2026-04-11 10:00:00'),
(15, 15, '2026-04-16', 160, 65.00, 115, 'NO', 'Back pain, numbness in legs', 'Herniated disc', 'Surgery may be considered if no improvement', '2026-04-16 11:00:00'),
(16, 16, '2026-04-18', 160, 65.00, 115, 'NO', 'Follow-up check', 'Symptoms improving', 'Continue physiotherapy', '2026-04-18 11:00:00'),
(17, 17, '2026-04-21', 172, 70.00, 130, 'NO', 'Frequent headaches, dizziness', 'Migraine', 'Identify and avoid triggers', '2026-04-21 09:00:00'),
(18, 18, '2026-04-24', 172, 70.00, 130, 'NO', 'Follow-up check', 'Migraine frequency reduced', 'Continue preventive medication', '2026-04-24 09:00:00'),
(19, 19, '2026-04-28', 165, 60.00, 120, 'NO', 'Seizures, loss of consciousness', 'Epilepsy', 'Regular medication essential', '2026-04-28 10:00:00'),
(20, 20, '2026-04-30', 165, 60.00, 120, 'NO', 'Follow-up check', 'Seizure controlled', 'Never skip medication', '2026-04-30 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_notifications`
--

CREATE TABLE `receptionist_notifications` (
  `RECEPTIONIST_NOTIFICATION_ID` int(11) NOT NULL,
  `MESSAGE` text NOT NULL,
  `TYPE` varchar(50) DEFAULT 'schedule_deleted',
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_tbl`
--

CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date NOT NULL,
  `DOJ` date NOT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') NOT NULL,
  `PHONE` bigint(20) NOT NULL,
  `EMAIL` varchar(50) NOT NULL,
  `ADDRESS` text NOT NULL,
  `USERNAME` varchar(20) NOT NULL,
  `PSWD` varchar(60) NOT NULL,
  `SECURITY_QUESTION` varchar(255) NOT NULL,
  `SECURITY_ANSWER` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionist_tbl`
--

INSERT INTO `receptionist_tbl` (`RECEPTIONIST_ID`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `GENDER`, `PHONE`, `EMAIL`, `ADDRESS`, `USERNAME`, `PSWD`, `SECURITY_QUESTION`, `SECURITY_ANSWER`) VALUES
(1, 'Meena', 'Kumari', '1985-03-15', '2018-06-20', 'FEMALE', 9876543301, 'meena.k@gmail.com', '123, Staff Quarters, Mumbai', 'Meena_k01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 'What was the name of your first school?', 'Delhi Public School'),
(2, 'Ramesh', 'Kumar', '1987-07-22', '2019-09-15', 'MALE', 9876543302, 'ramesh.k@gmail.com', '456, Staff Quarters, Delhi', 'Ramesh_k02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 'What is your favorite food from childhood?', 'Idli sambar'),
(3, 'Sunita', 'Devi', '1990-11-10', '2020-03-25', 'FEMALE', 9876543303, 'sunita.d@gmail.com', '789, Staff Quarters, Bangalore', 'Sunita_d03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 'Where did you go for your first school trip?', 'Ooty'),
(4, 'Anil', 'Sharma', '1988-05-18', '2021-07-10', 'MALE', 9876543304, 'anil.s@gmail.com', '321, Staff Quarters, Pune', 'Anil_s04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 'What was the nickname your family calls you?', 'Anu');

-- --------------------------------------------------------

--
-- Table structure for table `specialisation_tbl`
--

CREATE TABLE `specialisation_tbl` (
  `SPECIALISATION_ID` int(11) NOT NULL,
  `SPECIALISATION_NAME` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specialisation_tbl`
--

INSERT INTO `specialisation_tbl` (`SPECIALISATION_ID`, `SPECIALISATION_NAME`) VALUES
(1, 'Pediatrician'),
(2, 'Cardiologist'),
(3, 'Orthopedics'),
(4, 'Neurologist');

-- --------------------------------------------------------

--
-- Structure for view `view_appointment_report`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_appointment_report` AS
SELECT
  `a`.`APPOINTMENT_ID` AS `APPOINTMENT_ID`,
  CONCAT(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`,
  CONCAT(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`,
  `s`.`SPECIALISATION_NAME` AS `Specialisation`,
  `a`.`APPOINTMENT_DATE` AS `APPOINTMENT_DATE`,
  DAYNAME(`a`.`APPOINTMENT_DATE`) AS `Day_Name`,
  WEEK(`a`.`APPOINTMENT_DATE`) AS `Week_Number`,
  MONTH(`a`.`APPOINTMENT_DATE`) AS `Month_Number`,
  MONTHNAME(`a`.`APPOINTMENT_DATE`) AS `Month_Name`,
  YEAR(`a`.`APPOINTMENT_DATE`) AS `Year`,
  `a`.`APPOINTMENT_TIME` AS `APPOINTMENT_TIME`,
  `a`.`STATUS` AS `STATUS`
FROM (((`appointment_tbl` `a`
  JOIN `patient_tbl` `p` ON(`a`.`PATIENT_ID` = `p`.`PATIENT_ID`))
  JOIN `doctor_tbl` `d` ON(`a`.`DOCTOR_ID` = `d`.`DOCTOR_ID`))
  JOIN `specialisation_tbl` `s` ON(`d`.`SPECIALISATION_ID` = `s`.`SPECIALISATION_ID`));

-- --------------------------------------------------------

--
-- Structure for view `view_doctor_report`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_doctor_report` AS
SELECT
  `d`.`DOCTOR_ID` AS `DOCTOR_ID`,
  CONCAT(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`,
  `s`.`SPECIALISATION_NAME` AS `Specialisation`,
  `d`.`EDUCATION` AS `EDUCATION`,
  `d`.`STATUS` AS `Doctor_Status`,
  `a`.`APPOINTMENT_ID` AS `APPOINTMENT_ID`,
  `a`.`APPOINTMENT_DATE` AS `APPOINTMENT_DATE`,
  COALESCE(MONTHNAME(`a`.`APPOINTMENT_DATE`),'N/A') AS `Month_Name`,
  COALESCE(MONTH(`a`.`APPOINTMENT_DATE`),0) AS `Month_Number`,
  COALESCE(YEAR(`a`.`APPOINTMENT_DATE`),0) AS `Year`,
  COALESCE(`a`.`STATUS`,'NONE') AS `Appointment_Status`,
  COALESCE(`doc_stats`.`Total_Patients`,0) AS `Total_Patients`,
  COALESCE(`doc_stats`.`Avg_Rating`,0) AS `Avg_Rating`
FROM (((`doctor_tbl` `d`
  JOIN `specialisation_tbl` `s` ON(`d`.`SPECIALISATION_ID` = `s`.`SPECIALISATION_ID`))
  LEFT JOIN `appointment_tbl` `a` ON(`d`.`DOCTOR_ID` = `a`.`DOCTOR_ID`))
  LEFT JOIN (
    SELECT `a2`.`DOCTOR_ID` AS `DOCTOR_ID`,
      COUNT(DISTINCT `a2`.`PATIENT_ID`) AS `Total_Patients`,
      ROUND(AVG(`f2`.`RATING`),1) AS `Avg_Rating`
    FROM (`appointment_tbl` `a2`
      LEFT JOIN `feedback_tbl` `f2` ON(`a2`.`APPOINTMENT_ID` = `f2`.`APPOINTMENT_ID`))
    GROUP BY `a2`.`DOCTOR_ID`
  ) `doc_stats` ON(`d`.`DOCTOR_ID` = `doc_stats`.`DOCTOR_ID`));

-- --------------------------------------------------------

--
-- Structure for view `view_patient_report`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_patient_report` AS
SELECT
  `p`.`PATIENT_ID` AS `PATIENT_ID`,
  CONCAT(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`,
  `p`.`GENDER` AS `GENDER`,
  `p`.`BLOOD_GROUP` AS `BLOOD_GROUP`,
  `p`.`PHONE` AS `PHONE`,
  `p`.`EMAIL` AS `EMAIL`,
  `p`.`ADDRESS` AS `ADDRESS`,
  COUNT(DISTINCT `a`.`APPOINTMENT_ID`) AS `Total_Appointments`,
  SUM(CASE WHEN `a`.`STATUS` = 'COMPLETED' THEN 1 ELSE 0 END) AS `Completed_Visits`,
  SUM(CASE WHEN `a`.`STATUS` = 'SCHEDULED' THEN 1 ELSE 0 END) AS `Upcoming_Visits`,
  SUM(CASE WHEN `a`.`STATUS` = 'CANCELLED' THEN 1 ELSE 0 END) AS `Cancelled_Visits`,
  MAX(`a`.`APPOINTMENT_DATE`) AS `Last_Visit_Date`,
  MIN(`a`.`APPOINTMENT_DATE`) AS `First_Visit_Date`,
  COUNT(DISTINCT `a`.`DOCTOR_ID`) AS `Doctors_Visited`,
  COUNT(DISTINCT `pr`.`PRESCRIPTION_ID`) AS `Total_Prescriptions`,
  COALESCE(SUM(CASE WHEN `py`.`STATUS` = 'COMPLETED' THEN `py`.`AMOUNT` ELSE 0 END),0) AS `Total_Amount_Paid`,
  COUNT(DISTINCT CASE WHEN `py`.`STATUS` = 'COMPLETED' THEN `py`.`PAYMENT_ID` END) AS `Successful_Payments`,
  COUNT(DISTINCT `f`.`FEEDBACK_ID`) AS `Total_Feedback_Given`,
  ROUND(AVG(`f`.`RATING`),1) AS `Avg_Rating_Given`,
  COUNT(DISTINCT `mr`.`MEDICINE_REMINDER_ID`) AS `Medicine_Reminders`
FROM (((((`patient_tbl` `p`
  LEFT JOIN `appointment_tbl` `a` ON(`p`.`PATIENT_ID` = `a`.`PATIENT_ID`))
  LEFT JOIN `prescription_tbl` `pr` ON(`a`.`APPOINTMENT_ID` = `pr`.`APPOINTMENT_ID`))
  LEFT JOIN `payment_tbl` `py` ON(`a`.`APPOINTMENT_ID` = `py`.`APPOINTMENT_ID`))
  LEFT JOIN `feedback_tbl` `f` ON(`a`.`APPOINTMENT_ID` = `f`.`APPOINTMENT_ID`))
  LEFT JOIN `medicine_reminder_tbl` `mr` ON(`p`.`PATIENT_ID` = `mr`.`PATIENT_ID`))
GROUP BY `p`.`PATIENT_ID`
ORDER BY COUNT(DISTINCT `a`.`APPOINTMENT_ID`) DESC;

-- --------------------------------------------------------

--
-- Structure for view `view_payment_report`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_payment_report` AS
SELECT
  `py`.`PAYMENT_ID` AS `PAYMENT_ID`,
  `py`.`TRANSACTION_ID` AS `TRANSACTION_ID`,
  CONCAT(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`,
  CONCAT(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`,
  `py`.`AMOUNT` AS `AMOUNT`,
  `py`.`PAYMENT_MODE` AS `PAYMENT_MODE`,
  `py`.`STATUS` AS `Payment_Status`,
  `py`.`PAYMENT_DATE` AS `PAYMENT_DATE`,
  DAYNAME(`py`.`PAYMENT_DATE`) AS `Day_Name`,
  WEEK(`py`.`PAYMENT_DATE`) AS `Week_Number`,
  MONTH(`py`.`PAYMENT_DATE`) AS `Month_Number`,
  MONTHNAME(`py`.`PAYMENT_DATE`) AS `Month_Name`,
  YEAR(`py`.`PAYMENT_DATE`) AS `Year`
FROM (((`payment_tbl` `py`
  JOIN `appointment_tbl` `a` ON(`py`.`APPOINTMENT_ID` = `a`.`APPOINTMENT_ID`))
  JOIN `patient_tbl` `p` ON(`a`.`PATIENT_ID` = `p`.`PATIENT_ID`))
  JOIN `doctor_tbl` `d` ON(`a`.`DOCTOR_ID` = `d`.`DOCTOR_ID`));

--
-- Indexes for dumped tables
--

ALTER TABLE `appointment_reminder_tbl`
  ADD PRIMARY KEY (`APPOINTMENT_REMINDER_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

ALTER TABLE `appointment_tbl`
  ADD PRIMARY KEY (`APPOINTMENT_ID`),
  ADD KEY `PATIENT_ID` (`PATIENT_ID`),
  ADD KEY `DOCTOR_ID` (`DOCTOR_ID`),
  ADD KEY `SCHEDULE_ID` (`SCHEDULE_ID`);

ALTER TABLE `doctor_schedule_tbl`
  ADD PRIMARY KEY (`SCHEDULE_ID`),
  ADD KEY `DOCTOR_ID` (`DOCTOR_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`);

ALTER TABLE `doctor_tbl`
  ADD PRIMARY KEY (`DOCTOR_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`),
  ADD KEY `SPECIALISATION_ID` (`SPECIALISATION_ID`);

ALTER TABLE `feedback_tbl`
  ADD PRIMARY KEY (`FEEDBACK_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

ALTER TABLE `medicine_reminder_tbl`
  ADD PRIMARY KEY (`MEDICINE_REMINDER_ID`),
  ADD KEY `MEDICINE_ID` (`MEDICINE_ID`),
  ADD KEY `PATIENT_ID` (`PATIENT_ID`);

ALTER TABLE `medicine_tbl`
  ADD PRIMARY KEY (`MEDICINE_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`);

ALTER TABLE `notification_seen_tbl`
  ADD PRIMARY KEY (`SEEN_ID`),
  ADD UNIQUE KEY `uniq_user_notif` (`USER_TYPE`,`USER_ID`,`NOTIF_KEY`);

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `patient_tbl`
  ADD PRIMARY KEY (`PATIENT_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

ALTER TABLE `payment_tbl`
  ADD PRIMARY KEY (`PAYMENT_ID`),
  ADD UNIQUE KEY `TRANSACTION_ID` (`TRANSACTION_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

ALTER TABLE `prescription_medicine_tbl`
  ADD PRIMARY KEY (`PRESCRIPTION_ID`,`MEDICINE_ID`),
  ADD KEY `MEDICINE_ID` (`MEDICINE_ID`);

ALTER TABLE `prescription_tbl`
  ADD PRIMARY KEY (`PRESCRIPTION_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

ALTER TABLE `receptionist_notifications`
  ADD PRIMARY KEY (`RECEPTIONIST_NOTIFICATION_ID`);

ALTER TABLE `receptionist_tbl`
  ADD PRIMARY KEY (`RECEPTIONIST_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

ALTER TABLE `specialisation_tbl`
  ADD PRIMARY KEY (`SPECIALISATION_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `appointment_reminder_tbl`
  MODIFY `APPOINTMENT_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

ALTER TABLE `appointment_tbl`
  MODIFY `APPOINTMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

ALTER TABLE `doctor_schedule_tbl`
  MODIFY `SCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

ALTER TABLE `doctor_tbl`
  MODIFY `DOCTOR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `feedback_tbl`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `medicine_reminder_tbl`
  MODIFY `MEDICINE_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `medicine_tbl`
  MODIFY `MEDICINE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `notification_seen_tbl`
  MODIFY `SEEN_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `patient_tbl`
  MODIFY `PATIENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `payment_tbl`
  MODIFY `PAYMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `prescription_tbl`
  MODIFY `PRESCRIPTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `receptionist_notifications`
  MODIFY `RECEPTIONIST_NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `receptionist_tbl`
  MODIFY `RECEPTIONIST_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `specialisation_tbl`
  MODIFY `SPECIALISATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

ALTER TABLE `appointment_reminder_tbl`
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_2` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `appointment_tbl`
  ADD CONSTRAINT `appointment_tbl_ibfk_1` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_tbl_ibfk_2` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_tbl_ibfk_3` FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `doctor_schedule_tbl` (`SCHEDULE_ID`) ON UPDATE CASCADE;

ALTER TABLE `doctor_schedule_tbl`
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_1` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_2` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE;

ALTER TABLE `doctor_tbl`
  ADD CONSTRAINT `doctor_tbl_ibfk_1` FOREIGN KEY (`SPECIALISATION_ID`) REFERENCES `specialisation_tbl` (`SPECIALISATION_ID`) ON UPDATE CASCADE;

ALTER TABLE `feedback_tbl`
  ADD CONSTRAINT `feedback_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `medicine_reminder_tbl`
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_1` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_2` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `medicine_tbl`
  ADD CONSTRAINT `medicine_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE;

ALTER TABLE `payment_tbl`
  ADD CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON UPDATE CASCADE;

ALTER TABLE `prescription_medicine_tbl`
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_1` FOREIGN KEY (`PRESCRIPTION_ID`) REFERENCES `prescription_tbl` (`PRESCRIPTION_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_2` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON UPDATE CASCADE;

ALTER TABLE `prescription_tbl`
  ADD CONSTRAINT `prescription_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
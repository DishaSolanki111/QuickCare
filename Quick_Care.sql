-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 09:47 AM
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
-- Database:quick_care
--

-- --------------------------------------------------------

--
-- Table structure for table appointment_reminder_tbl
--

CREATE TABLE `appointment_reminder_tbl` (
  `APPOINTMENT_REMINDER_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_reminder_tbl`
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
(60, 1, 20, '07:00:00', '3 hours before appointment'),
(61, 1, 24, '12:11:14', 'Your appointment has been booked successfully with Dr. Dr. Rajesh Kumar on January 14, 2026 at 10:00 AM'),
(62, 1, 24, '10:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar tomorrow at 10:00 AM on January 14, 2026'),
(63, 1, 24, '07:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar in 3 hours at 10:00 AM today'),
(64, 1, 25, '15:44:14', 'Your appointment has been booked successfully with Dr. Dr. Rajesh Kumar on January 21, 2026 at 01:00 PM'),
(65, 1, 25, '13:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar tomorrow at 01:00 PM on January 21, 2026'),
(66, 1, 25, '10:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar in 3 hours at 01:00 PM today'),
(67, 1, 26, '09:00:25', 'Your appointment has been booked successfully with Dr. Dr. Rajesh Kumar on January 12, 2026 at 12:00 PM'),
(68, 1, 26, '12:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar tomorrow at 12:00 PM on January 12, 2026'),
(69, 1, 26, '09:00:00', 'Reminder: You have an appointment with Dr. Rajesh Kumar in 3 hours at 12:00 PM today');

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
  `APPOINTMENT_DATE` date DEFAULT NULL,
  `APPOINTMENT_TIME` time DEFAULT NULL,
  `STATUS` enum('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_tbl`
--

INSERT INTO `appointment_tbl` (`APPOINTMENT_ID`, `PATIENT_ID`, `DOCTOR_ID`, `SCHEDULE_ID`, `CREATED_AT`, `APPOINTMENT_DATE`, `APPOINTMENT_TIME`, `STATUS`) VALUES
(1, 1, 1, 1, '2026-11-01 09:00:00', '2026-11-03', '10:00:00', 'COMPLETED'),
(2, 1, 1, 2, '2026-11-03 10:15:00', '2026-11-07', '10:00:00', 'COMPLETED'),
(3, 2, 2, 4, '2026-11-01 11:30:00', '2026-11-04', '11:00:00', 'COMPLETED'),
(4, 2, 2, 5, '2026-11-05 12:00:00', '2026-11-10', '11:00:00', 'COMPLETED'),
(5, 3, 3, 7, '2026-11-04 13:20:00', '2026-11-09', '09:00:00', 'COMPLETED'),
(6, 3, 3, 8, '2026-11-06 14:45:00', '2026-11-12', '09:00:00', 'COMPLETED'),
(7, 4, 4, 10, '2026-11-07 08:30:00', '2026-11-14', '10:00:00', 'COMPLETED'),
(8, 4, 4, 11, '2026-11-08 09:10:00', '2026-11-15', '10:00:00', 'COMPLETED'),
(9, 5, 5, 13, '2026-11-09 10:00:00', '2026-11-17', '11:00:00', 'COMPLETED'),
(10, 5, 5, 14, '2026-11-10 11:30:00', '2026-11-20', '11:00:00', 'COMPLETED'),
(11, 6, 6, 16, '2026-11-11 12:15:00', '2026-11-22', '09:00:00', 'COMPLETED'),
(12, 6, 6, 17, '2026-11-12 13:00:00', '2026-11-25', '09:00:00', 'COMPLETED'),
(13, 7, 7, 19, '2026-11-02 14:00:00', '2026-11-06', '10:00:00', 'COMPLETED'),
(14, 7, 7, 20, '2026-11-05 15:30:00', '2026-11-11', '10:00:00', 'COMPLETED'),
(15, 8, 8, 22, '2026-11-08 16:00:00', '2026-11-16', '11:00:00', 'COMPLETED'),
(16, 8, 8, 23, '2026-11-10 17:20:00', '2026-11-18', '11:00:00', 'COMPLETED'),
(17, 9, 9, 25, '2026-11-12 09:45:00', '2026-11-21', '09:00:00', 'COMPLETED'),
(18, 9, 9, 26, '2026-11-15 10:30:00', '2026-11-24', '09:00:00', 'COMPLETED'),
(19, 10, 10, 28, '2026-11-18 11:00:00', '2026-11-28', '10:00:00', 'COMPLETED'),
(20, 10, 10, 29, '2026-11-20 12:00:00', '2026-11-30', '10:00:00', 'COMPLETED'),
(21, 7, 2, 18, '2026-01-09 10:38:09', '2026-02-10', '10:30:00', 'SCHEDULED'),
(22, 8, 3, 19, '2026-01-09 10:38:09', '2026-02-15', '11:00:00', 'SCHEDULED'),
(23, 9, 1, 20, '2026-01-09 10:38:09', '2026-02-20', '09:30:00', 'SCHEDULED'),
(24, 1, 1, 1, '2026-01-09 11:11:14', '2026-02-12', '10:00:00', 'SCHEDULED'),
(25, 1, 1, 1, '2026-01-09 14:44:14', '2026-02-25', '13:00:00', 'SCHEDULED'),
(26, 1, 1, 1, '2026-01-10 08:00:25', '2026-02-08', '12:00:00', 'SCHEDULED');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_tbl`
--

CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int(11) NOT NULL,
  `DOCTOR_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `START_TIME` time DEFAULT NULL,
  `END_TIME` time DEFAULT NULL,
  `AVAILABLE_DAY` enum('MON','TUE','WED','THUR','FRI','SAT','SUN') DEFAULT NULL
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
(45, 15, 3, '10:00:00', '18:00:00', 'FRI'),
(46, 16, 3, '08:00:00', '16:00:00', 'TUE'),
(47, 16, 3, '08:00:00', '16:00:00', 'THUR'),
(48, 16, 3, '08:00:00', '16:00:00', 'SAT'),
(49, 17, 4, '09:00:00', '17:00:00', 'MON'),
(50, 17, 4, '09:00:00', '17:00:00', 'WED'),
(51, 17, 4, '09:00:00', '17:00:00', 'FRI'),
(52, 18, 1, '09:00:00', '17:00:00', 'TUE'),
(53, 18, 1, '09:00:00', '17:00:00', 'THUR'),
(54, 18, 1, '09:00:00', '17:00:00', 'SAT'),
(55, 19, 1, '10:00:00', '18:00:00', 'MON'),
(56, 19, 1, '10:00:00', '18:00:00', 'WED'),
(57, 19, 1, '10:00:00', '18:00:00', 'FRI'),
(58, 20, 2, '08:00:00', '16:00:00', 'TUE'),
(59, 20, 2, '08:00:00', '16:00:00', 'THUR'),
(60, 20, 2, '08:00:00', '16:00:00', 'SAT'),
(61, 21, 2, '09:00:00', '17:00:00', 'MON'),
(62, 21, 2, '09:00:00', '17:00:00', 'WED'),
(63, 21, 2, '09:00:00', '17:00:00', 'FRI'),
(64, 22, 3, '10:00:00', '18:00:00', 'TUE'),
(65, 22, 3, '10:00:00', '18:00:00', 'THUR'),
(66, 22, 3, '10:00:00', '18:00:00', 'SAT'),
(67, 23, 3, '08:00:00', '16:00:00', 'MON'),
(68, 23, 3, '08:00:00', '16:00:00', 'WED'),
(69, 23, 3, '08:00:00', '16:00:00', 'FRI'),
(70, 24, 4, '09:00:00', '17:00:00', 'TUE'),
(71, 24, 4, '09:00:00', '17:00:00', 'THUR'),
(72, 24, 4, '09:00:00', '17:00:00', 'SAT'),
(73, 25, 1, '10:00:00', '18:00:00', 'MON'),
(74, 25, 1, '10:00:00', '18:00:00', 'WED'),
(75, 25, 1, '10:00:00', '18:00:00', 'FRI'),
(76, 26, 1, '09:00:00', '17:00:00', 'TUE'),
(77, 26, 1, '09:00:00', '17:00:00', 'THUR'),
(78, 26, 1, '09:00:00', '17:00:00', 'SAT'),
(79, 27, 2, '08:00:00', '16:00:00', 'MON'),
(80, 27, 2, '08:00:00', '16:00:00', 'WED'),
(81, 27, 2, '08:00:00', '16:00:00', 'FRI'),
(82, 28, 2, '10:00:00', '18:00:00', 'TUE'),
(83, 28, 2, '10:00:00', '18:00:00', 'THUR'),
(84, 28, 2, '10:00:00', '18:00:00', 'SAT'),
(85, 29, 3, '09:00:00', '17:00:00', 'MON'),
(86, 29, 3, '09:00:00', '17:00:00', 'WED'),
(87, 29, 3, '09:00:00', '17:00:00', 'FRI'),
(88, 30, 3, '08:00:00', '16:00:00', 'TUE'),
(89, 30, 3, '08:00:00', '16:00:00', 'THUR'),
(90, 30, 3, '08:00:00', '16:00:00', 'SAT'),
(91, 31, 4, '10:00:00', '18:00:00', 'MON'),
(92, 31, 4, '10:00:00', '18:00:00', 'WED'),
(93, 31, 4, '10:00:00', '18:00:00', 'FRI'),
(94, 32, 4, '09:00:00', '17:00:00', 'TUE'),
(95, 32, 4, '09:00:00', '17:00:00', 'THUR'),
(96, 32, 4, '09:00:00', '17:00:00', 'SAT'),
(97, 33, 1, '08:00:00', '16:00:00', 'MON'),
(98, 33, 1, '08:00:00', '16:00:00', 'WED'),
(99, 33, 1, '08:00:00', '16:00:00', 'FRI'),
(100, 34, 1, '09:00:00', '17:00:00', 'TUE'),
(101, 34, 1, '09:00:00', '17:00:00', 'THUR'),
(102, 34, 1, '09:00:00', '17:00:00', 'SAT'),
(103, 35, 2, '10:00:00', '18:00:00', 'MON'),
(104, 35, 2, '10:00:00', '18:00:00', 'WED'),
(105, 35, 2, '10:00:00', '18:00:00', 'FRI'),
(106, 36, 2, '08:00:00', '16:00:00', 'TUE'),
(107, 36, 2, '08:00:00', '16:00:00', 'THUR'),
(108, 36, 2, '08:00:00', '16:00:00', 'SAT'),
(109, 37, 3, '09:00:00', '17:00:00', 'MON'),
(110, 37, 3, '09:00:00', '17:00:00', 'WED'),
(111, 37, 3, '09:00:00', '17:00:00', 'FRI'),
(112, 38, 3, '10:00:00', '18:00:00', 'TUE'),
(113, 38, 3, '10:00:00', '18:00:00', 'THUR'),
(114, 38, 3, '10:00:00', '18:00:00', 'SAT'),
(115, 39, 4, '08:00:00', '16:00:00', 'MON'),
(116, 39, 4, '08:00:00', '16:00:00', 'WED'),
(117, 39, 4, '08:00:00', '16:00:00', 'FRI'),
(118, 40, 4, '09:00:00', '17:00:00', 'TUE'),
(119, 40, 4, '09:00:00', '17:00:00', 'THUR'),
(120, 40, 4, '09:00:00', '17:00:00', 'SAT');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_tbl`
--

CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int(11) NOT NULL,
  `SPECIALISATION_ID` int(11) NOT NULL,
  `PROFILE_IMAGE` varchar(255) DEFAULT NULL,
  `FIRST_NAME` varchar(20) DEFAULT NULL,
  `LAST_NAME` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `USERNAME` varchar(20) DEFAULT NULL,
  `PSWD` varchar(60) DEFAULT NULL,
  `PHONE` bigint(20) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  
  `EDUCATION` varchar(50) DEFAULT NULL,
  `STATUS` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- otp table
--
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('patient','doctor','receptionist') NOT NULL,
    user_id INT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Dumping data for table `doctor_tbl`
--

INSERT INTO doctor_tbl (DOCTOR_ID, SPECIALISATION_ID, PROFILE_IMAGE, FIRST_NAME, LAST_NAME, DOB, DOJ, USERNAME, PSWD, PHONE, EMAIL, GENDER, EDUCATION,STATUS) VALUES
(1, 1, 'uploads/rajesh.jpeg', 'Rajesh', 'Kumar', '1975-03-15', '2010-06-20', 'Dr_rajesh01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543210, 'rajesh.kumar@gmail.com', 'MALE', 'MBBS, MD (Pediatrics)','approved'),
(2, 1, 'uploads/priya.jpeg', 'Priya', 'Sharma', '1980-07-22', '2012-09-15', 'Dr_priya02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543211, 'priya.sharma@gmail.com', 'FEMALE', 'MBBS, DCH','approved'),
(3, 1, 'uploads/amit.jpeg', 'Amit', 'Patel', '1978-11-10', '2015-03-25', 'Dr_amit03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543212, 'amit.patel@gmail.com', 'MALE',  'MBBS, DNB (Pediatrics)','approved'),
(4, 2, 'uploads/sunita1.jpeg', 'Sunita', 'Reddy', '1976-05-18', '2011-07-10', 'Dr_sunita04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543213, 'sunita.reddy@gmail.com', 'FEMALE',  'MBBS, MD (Cardiology)','approved'),
(5, 2, 'uploads/vikram1.jpeg', 'Vikram', 'Singh', '1973-09-25', '2009-12-05', 'Dr_vikram05', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543214, 'vikram.singh@gmail.com', 'MALE',    'MBBS, DM (Cardiology)','approved'),
(6, 2, 'uploads/anjali.jpeg', 'Anjali', 'Gupta', '1982-02-14', '2014-08-20', 'Dr_anjali06', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543215, 'anjali.gupta@gmail.com', 'FEMALE',     'MBBS, MD (Cardiology)','approved'),
(7, 3, 'uploads/rahul.jpeg', 'Rahul', 'Verma', '1977-08-30', '2013-04-15', 'Dr_rahul07', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543216, 'rahul.verma@gmail.com', 'MALE',   'MBBS, MS (Orthopedics)','approved'),
(8, 3, 'uploads/meera.jpeg', 'Meera', 'Joshi', '1981-12-05', '2016-01-10', 'Dr_meera08', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543217, 'meera.joshi@gmail.com', 'FEMALE',     'MBBS, DNB (Orthopedics)' ,'approved'),
(9, 4, 'uploads/sanjay.jpeg', 'Sanjay', 'Malhotra', '1974-06-20', '2010-11-30', 'Dr_sanjay09', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543218, 'sanjay.malhotra@gmail.com', 'MALE',    'MBBS, MD, DM (Neurology)','approved' ),
(10, 4, 'uploads/kavita1.jpeg', 'Kavita', 'Nair', '1979-04-12', '2015-07-25', 'Dr_kavita10', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543219, 'kavita.nair@gmail.com', 'FEMALE',     'MBBS, MD (Neurology)','approved' ),
(11, 1, 'uploads/suresh.jpeg', 'Suresh', 'Reddy', '1976-01-18', '2011-05-10', 'Dr_suresh11', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543220, 'suresh.reddy@gmail.com', 'MALE',     'MBBS, MD (Pediatrics)','approved' ),
(12, 1, 'uploads/lakshmi.jpeg', 'Lakshmi', 'Iyer', '1983-09-05', '2014-02-20', 'Dr_lakshmi12', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543221, 'lakshmi.iyer@gmail.com', 'FEMALE',   'MBBS, DCH' ,'approved'),
(13, 1,  'uploads/arun.jpeg', 'Arun', 'Shah', '1979-12-22', '2013-08-15', 'Dr_arun13', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543222, 'arun.shah@gmail.com', 'MALE',    'MBBS, DNB (Pediatrics)','approved' ),
(14, 1,  'uploads/Deepa.jpeg', 'Deepa', 'Nair', '1981-04-08', '2016-11-01', 'Dr_deepa14', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543223, 'deepa.nair@gmail.com', 'FEMALE',     'MBBS, MD (Pediatrics)' ,'approved'),
(15, 1, 'uploads/karthik.jpeg', 'Karthik', 'Pillai', '1977-07-30', '2010-03-12', 'Dr_karthik15', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543224, 'karthik.pillai@gmail.com', 'MALE',     'MBBS, DCH' ,'approved'),
(16, 1, 'uploads/divya.jpeg', 'Divya', 'Krishnan', '1984-02-14', '2015-06-25', 'Dr_divya16', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543225, 'divya.krishnan@gmail.com', 'FEMALE',     'MBBS, DNB (Pediatrics)','approved' ),
(17, 1,  'uploads/ramesh.jpeg', 'Ramesh', 'Bose', '1974-10-28', '2009-09-08', 'Dr_ramesh17', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543226, 'ramesh.bose@gmail.com', 'MALE',    'MBBS, MD (Pediatrics)' ,'approved'),
(18, 2, 'uploads/manoj.jpeg', 'Manoj', 'Desai', '1978-06-12', '2012-04-18', 'Dr_manoj18', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543230, 'manoj.desai@gmail.com', 'MALE',    'MBBS, DM (Cardiology)' ,'approved'),
(19, 2, 'uploads/preeti.jpeg', 'Preeti', 'Shah', '1980-11-03', '2015-01-22', 'Dr_preeti19', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543231, 'preeti.shah@gmail.com', 'FEMALE',    'MBBS, MD (Cardiology)' ,'approved'),
(20, 2, 'uploads/rohit.jpeg', 'Rohit', 'Kapoor', '1975-03-25', '2010-10-05', 'Dr_rohit20', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543232, 'rohit.kapoor@gmail.com', 'MALE',    'MBBS, DM (Cardiology)' ,'approved'),
(21, 2, 'uploads/swati.jpeg', 'Swati', 'Bhatia', '1982-08-17', '2013-07-14', 'Dr_swati21', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543233, 'swati.bhatia@gmail.com', 'FEMALE',     'MBBS, MD (Cardiology)' ,'approved'),
(22, 2, 'uploads/nikhil.jpeg', 'Nikhil', 'Agarwal', '1979-01-30', '2016-05-28', 'Dr_nikhil22', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543234, 'nikhil.agarwal@gmail.com', 'MALE',    'MBBS, DNB (Cardiology)' ,'approved'),
(23, 2, 'uploads/pooja.jpeg', 'Pooja', 'Mehta', '1983-05-08', '2014-12-10', 'Dr_pooja23', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543235, 'pooja.mehta@gmail.com', 'FEMALE',   'MBBS, MD (Cardiology)' ,'approved'),
(24, 2, 'uploads/ajay.jpeg', 'Ajay', 'Sethi', '1976-09-20', '2011-02-15', 'Dr_ajay24', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543236, 'ajay.sethi@gmail.com', 'MALE',     'MBBS, DM (Cardiology)' ,'approved'),
(25, 2, 'uploads/tanuja.jpeg', 'Tanuja', 'Verma', '1981-04-05', '2012-08-20', 'Dr_tanuja25', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543237, 'tanuja.verma@gmail.com', 'FEMALE',     'MBBS, MD (Cardiology)' ,'approved'),
(26, 3, 'uploads/vishal.jpeg', 'Vishal', 'Jain', '1974-04-18', '2010-08-22', 'Dr_vishal26', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543240, 'vishal.jain@gmail.com', 'MALE','MBBS, MS (Orthopedics)' ,'approved'),
(27, 3, 'uploads/neha.jpeg', 'Neha', 'Aggarwal', '1982-07-09', '2014-03-05', 'Dr_neha27', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543241, 'neha.aggarwal@gmail.com', 'FEMALE',    'MBBS, DNB (Orthopedics)' ,'approved'),
(28, 3, 'uploads/rajvi.jpeg', 'Rajvi', 'Chopra', '1978-02-28', '2012-11-18', 'Dr_rajvi28', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543242, 'rajvi.chopra@gmail.com', 'FEMALE',   'MBBS, MS (Orthopedics)' ,'approved'),
(29, 3, 'uploads/kavita.jpeg', 'Kavita', 'Srinivasan', '1980-10-14', '2015-06-30', 'Dr_kavita29', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543243, 'kavita.srinivasan@gmail.com', 'FEMALE',    'MBBS, DNB (Orthopedics)' ,'approved'),
(30, 3, 'uploads/sandeep.jpeg', 'Sandeep', 'Rao', '1976-12-05', '2011-09-12', 'Dr_sandeep30', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543244, 'sandeep.rao@gmail.com', 'MALE',    'MBBS, MS (Orthopedics)' ,'approved'),
(31, 3, 'uploads/anita.jpeg', 'Anita', 'Malhotra', '1983-05-22', '2016-04-08', 'Dr_anita31', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543245, 'anita.malhotra@gmail.com', 'FEMALE',     'MBBS, DNB (Orthopedics)' ,'approved'),
(32, 3, 'uploads/prakash.jpeg', 'Prakash', 'Tiwari', '1975-08-03', '2009-07-20', 'Dr_prakash32', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543246, 'prakash.tiwari@gmail.com', 'MALE',     'MBBS, MS (Orthopedics)' ,'approved'),
(33, 3, 'uploads/shalini.jpeg', 'Shalini', 'Venkatesh', '1981-01-16', '2013-10-25', 'Dr_shalini33', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543247, 'shalini.venkatesh@gmail.com', 'FEMALE',     'MBBS, DNB (Orthopedics)' ,'approved'),
(34, 4, 'uploads/ashok.jpeg', 'Ashok', 'Bhatt', '1977-11-08', '2012-02-14', 'Dr_ashok34', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543250, 'ashok.bhatt@gmail.com', 'MALE',     'MBBS, DM (Neurology)','approved' ),
(35, 4, 'uploads/rekha.jpeg', 'Rekha', 'Subramanian', '1982-03-25', '2014-09-18', 'Dr_rekha35', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543251, 'rekha.subramanian@gmail.com', 'FEMALE',    'MBBS, MD (Neurology)','approved' ),
(36, 4, 'uploads/ganesh.jpeg', 'Ganesh', 'Murthy', '1976-07-12', '2011-05-30', 'Dr_ganesh36', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543252, 'ganesh.murthy@gmail.com', 'MALE',     'MBBS, DM (Neurology)' ,'approved'),
(37, 4, 'uploads/smita.jpeg', 'Smita', 'Dixit', '1980-12-03', '2015-11-22', 'Dr_smita37', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543253, 'smita.dixit@gmail.com', 'FEMALE',     'MBBS, DNB (Neurology)','approved' ),
(38, 4, 'uploads/venkat.jpeg', 'Venkat', 'Rao', '1978-05-18', '2013-08-05', 'Dr_venkat38', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543254, 'venkat.rao@gmail.com', 'MALE',    'MBBS, MD, DM (Neurology)' ,'approved'),
(39, 4, 'uploads/mamta.jpeg', 'Mamta', 'Singh', '1983-09-28', '2016-01-15', 'Dr_mamta39', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543255, 'mamta.singh@gmail.com', 'FEMALE',     'MBBS, MD (Neurology)' ,'approved'),
(40, 4, 'uploads/harish.jpeg', 'Harish', 'Sidha', '1975-02-14', '2010-04-28', 'Dr_harish40', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', 9876543256, 'harish.sidha@gmail.com', 'MALE',  'MBBS, DM (Neurology)' ,'approved');
-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `RATING` int(11) DEFAULT NULL,
  `COMMENTS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_tbl`
--

INSERT INTO `feedback_tbl` (`FEEDBACK_ID`, `APPOINTMENT_ID`, `RATING`, `COMMENTS`) VALUES
(1, 1, 5, 'Doctor was very patient with my child. Explained everything clearly.'),
(2, 2, 5, 'Follow-up was thorough. Treatment is working well.'),
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
  `CREATOR_ROLE` enum('PATIENT','RECEPTIONIST') DEFAULT NULL,
  `CREATOR_ID` int(11) NOT NULL,
  `PATIENT_ID` int(11) NOT NULL,
  `START_DATE` date DEFAULT NULL,
  `END_DATE` date DEFAULT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_reminder_tbl`
--

INSERT INTO `medicine_reminder_tbl` (`MEDICINE_REMINDER_ID`, `MEDICINE_ID`, `CREATOR_ROLE`, `CREATOR_ID`, `PATIENT_ID`, `START_DATE`, `END_DATE`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 'RECEPTIONIST', 1, 1, '2026-11-03', '2026-12-03', '08:00:00', 'Morning dose'),
(2, 1, 'RECEPTIONIST', 1, 1, '2026-11-03', '2026-12-03', '20:00:00', 'Evening dose'),
(3, 2, 'RECEPTIONIST', 1, 1, '2026-11-03', '2026-12-03', '08:00:00', 'Morning dose'),
(4, 2, 'RECEPTIONIST', 1, 1, '2026-11-03', '2026-12-03', '14:00:00', 'Afternoon dose'),
(5, 2, 'RECEPTIONIST', 1, 1, '2026-11-03', '2026-12-03', '20:00:00', 'Evening dose'),
(6, 4, 'RECEPTIONIST', 1, 1, '2026-11-04', '2026-12-04', '09:00:00', 'Morning dose'),
(7, 5, 'RECEPTIONIST', 1, 2, '2026-11-05', '2026-12-05', '08:00:00', 'Morning dose'),
(8, 6, 'RECEPTIONIST', 1, 2, '2026-11-05', '2026-12-05', '09:00:00', 'Morning dose'),
(9, 3, 'RECEPTIONIST', 2, 3, '2026-11-04', '2026-12-04', '08:00:00', 'Morning dose'),
(10, 3, 'RECEPTIONIST', 2, 3, '2026-11-04', '2026-12-04', '20:00:00', 'Evening dose'),
(11, 7, 'RECEPTIONIST', 2, 4, '2026-11-10', '2026-12-10', '08:00:00', 'Morning dose'),
(12, 8, 'RECEPTIONIST', 2, 4, '2026-11-10', '2026-12-10', '21:00:00', 'Night dose'),
(13, 9, 'RECEPTIONIST', 3, 5, '2026-11-09', '2026-12-09', '08:00:00', 'Morning dose'),
(14, 9, 'RECEPTIONIST', 3, 5, '2026-11-09', '2026-12-09', '14:00:00', 'Afternoon dose'),
(15, 9, 'RECEPTIONIST', 3, 5, '2026-11-09', '2026-12-09', '20:00:00', 'Evening dose'),
(16, 10, 'RECEPTIONIST', 3, 5, '2026-11-10', '2026-12-10', '09:00:00', 'Morning dose'),
(17, 10, 'RECEPTIONIST', 3, 5, '2026-11-10', '2026-12-10', '21:00:00', 'Night dose'),
(18, 11, 'RECEPTIONIST', 3, 6, '2026-11-11', '2026-12-11', '08:00:00', 'Morning dose'),
(19, 11, 'RECEPTIONIST', 3, 6, '2026-11-11', '2026-12-11', '14:00:00', 'Afternoon dose'),
(20, 11, 'RECEPTIONIST', 3, 6, '2026-11-11', '2026-12-11', '20:00:00', 'Evening dose'),
(21, 13, 'RECEPTIONIST', 1, 9, '2026-11-21', '2026-12-21', '08:00:00', 'Morning dose'),
(22, 13, 'RECEPTIONIST', 1, 9, '2026-11-21', '2026-12-21', '20:00:00', 'Evening dose'),
(23, 14, 'RECEPTIONIST', 1, 9, '2026-11-21', '2026-12-21', '21:00:00', 'Night dose'),
(24, 15, 'RECEPTIONIST', 1, 10, '2026-11-28', '2026-12-28', '08:00:00', 'Morning dose'),
(25, 15, 'RECEPTIONIST', 1, 10, '2026-11-28', '2026-12-28', '14:00:00', 'Afternoon dose'),
(26, 15, 'RECEPTIONIST', 1, 10, '2026-11-28', '2026-12-28', '20:00:00', 'Evening dose'),
(27, 16, 'RECEPTIONIST', 1, 10, '2026-11-28', '2026-12-28', '21:00:00', 'Night dose');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_tbl`
--

CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int(11) NOT NULL,
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `MED_NAME` varchar(25) DEFAULT NULL,
  `DESCRIPTION` text DEFAULT NULL
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
-- Table structure for table `patient_tbl`
--

CREATE TABLE `patient_tbl` (
  `PATIENT_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(20) DEFAULT NULL,
  `LAST_NAME` varchar(20) DEFAULT NULL,
  `USERNAME` varchar(20) DEFAULT NULL,
  `PSWD` varchar(60) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  `BLOOD_GROUP` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') DEFAULT NULL,
  `PHONE` bigint(20) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `ADDRESS` text DEFAULT NULL,
  `MEDICAL_HISTORY_FILE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tbl`
--

INSERT INTO `patient_tbl` (`PATIENT_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PSWD`, `DOB`, `GENDER`, `BLOOD_GROUP`, `PHONE`, `EMAIL`, `ADDRESS`) VALUES
(1, 'Arjun', 'Mishra', 'Arjun_m01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1990-05-15', 'MALE', 'B+', 9876543201, 'arjun.mishra@gmail.com', '123, Park Street, Mumbai'),
(2, 'Pooja', 'Sharma', 'Pooja_s02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1985-08-22', 'FEMALE', 'A+', 9876543202, 'pooja.sharma@gmail.com', '456, MG Road, Delhi'),
(3, 'Rohan', 'Patel', 'Rohan_p03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1992-12-10', 'MALE', 'O+', 9876543203, 'rohan.patel@gmail.com', '789, Brigade Road, Bangalore'),
(4, 'Neha', 'Gupta', 'Neha_g04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1988-03-18', 'FEMALE', 'AB+', 9876543204, 'neha.gupta@gmail.com', '321, FC Road, Pune'),
(5, 'Amit', 'Kumar', 'Amit_k05', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1987-07-25', 'MALE', 'A-', 9876543205, 'amit.kumar@gmail.com', '654, Jubilee Hills, Hyderabad'),
(6, 'Sneha', 'Reddy', 'Sneha_r06', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1991-11-30', 'FEMALE', 'B-', 9876543206, 'sneha.reddy@gmail.com', '987, Banjara Hills, Hyderabad'),
(7, 'Vikas', 'Singh', 'Vikas_s07', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1986-09-14', 'MALE', 'O-', 9876543207, 'vikas.singh@gmail.com', '147, Connaught Place, Delhi'),
(8, 'Anjali', 'Desai', 'Anjali_d08', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1993-02-05', 'FEMALE', 'AB-', 9876543208, 'anjali.desai@gmail.com', '258, Marine Drive, Mumbai'),
(9, 'Rahul', 'Verma', 'Rahul_v09', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1989-06-20', 'MALE', 'A+', 9876543209, 'rahul.verma@gmail.com', '369, Park Street, Kolkata'),
(10, 'Kavita', 'Sharma', 'Kavita_s10', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '1994-04-12', 'FEMALE', 'B+', 9876543210, 'kavita.sharma@gmail.com', '741, MG Road, Bangalore');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `PAYMENT_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `AMOUNT` decimal(10,2) DEFAULT NULL,
  `PAYMENT_DATE` date DEFAULT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') DEFAULT NULL,
  `STATUS` enum('COMPLETED','FAILED') DEFAULT NULL,
  `TRANSACTION_ID` varchar(36) DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_tbl`
--

INSERT INTO `payment_tbl` (`PAYMENT_ID`, `APPOINTMENT_ID`, `AMOUNT`, `PAYMENT_DATE`, `PAYMENT_MODE`, `STATUS`, `TRANSACTION_ID`, `CREATED_AT`) VALUES
(1, 1, 300.00, '2026-11-01', 'CREDIT CARD', 'COMPLETED', 'TXN123456789', '2026-11-01 09:00:00'),
(2, 2, 300.00, '2026-11-03', 'GOOGLE PAY', 'COMPLETED', 'TXN123456790', '2026-11-03 10:15:00'),
(3, 3, 300.00, '2026-11-01', 'UPI', 'COMPLETED', 'TXN123456791', '2026-11-01 11:30:00'),
(4, 4, 300.00, '2026-11-05', 'NET BANKING', 'COMPLETED', 'TXN123456792', '2026-11-05 12:00:00'),
(5, 5, 300.00, '2026-11-04', 'CREDIT CARD', 'COMPLETED', 'TXN123456793', '2026-11-04 13:20:00'),
(6, 6, 300.00, '2026-11-06', 'GOOGLE PAY', 'COMPLETED', 'TXN123456794', '2026-11-06 14:45:00'),
(7, 7, 300.00, '2026-11-07', 'UPI', 'COMPLETED', 'TXN123456795', '2026-11-07 08:30:00'),
(8, 8, 300.00, '2026-11-08', 'NET BANKING', 'COMPLETED', 'TXN123456796', '2026-11-08 09:10:00'),
(9, 9, 300.00, '2026-11-09', 'CREDIT CARD', 'COMPLETED', 'TXN123456797', '2026-11-09 10:00:00'),
(10, 10, 300.00, '2026-11-10', 'GOOGLE PAY', 'COMPLETED', 'TXN123456798', '2026-11-10 11:30:00'),
(11, 11, 300.00, '2026-11-11', 'UPI', 'COMPLETED', 'TXN123456799', '2026-11-11 12:15:00'),
(12, 12, 300.00, '2026-11-12', 'NET BANKING', 'COMPLETED', 'TXN123456800', '2026-11-12 13:00:00'),
(13, 13, 300.00, '2026-11-02', 'CREDIT CARD', 'COMPLETED', 'TXN123456801', '2026-11-02 14:00:00'),
(14, 14, 300.00, '2026-11-05', 'GOOGLE PAY', 'COMPLETED', 'TXN123456802', '2026-11-05 15:30:00'),
(15, 15, 300.00, '2026-11-08', 'UPI', 'COMPLETED', 'TXN123456803', '2026-11-08 16:00:00'),
(16, 16, 300.00, '2026-11-10', 'NET BANKING', 'COMPLETED', 'TXN123456804', '2026-11-10 17:20:00'),
(17, 17, 300.00, '2026-11-12', 'CREDIT CARD', 'COMPLETED', 'TXN123456805', '2026-11-12 09:45:00'),
(18, 18, 300.00, '2026-11-15', 'GOOGLE PAY', 'COMPLETED', 'TXN123456806', '2026-11-15 10:30:00'),
(19, 19, 300.00, '2026-11-18', 'UPI', 'COMPLETED', 'TXN123456807', '2026-11-18 11:00:00'),
(20, 20, 300.00, '2026-11-20', 'NET BANKING', 'COMPLETED', 'TXN123456808', '2026-11-20 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicine_tbl`
--

CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int(11) NOT NULL,
  `MEDICINE_ID` int(11) NOT NULL,
  `DOSAGE` varchar(30) DEFAULT NULL,
  `DURATION` varchar(30) DEFAULT NULL,
  `FREQUENCY` varchar(30) DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_medicine_tbl`
--

INSERT INTO `prescription_medicine_tbl` (`PRESCRIPTION_ID`, `MEDICINE_ID`, `DOSAGE`, `DURATION`, `FREQUENCY`, `CREATED_AT`) VALUES
(1, 1, '5ml', '5 days', 'Twice daily', '2026-11-03 10:00:00'),
(1, 2, '125mg', '7 days', 'Three times daily', '2026-11-03 10:00:00'),
(2, 1, '5ml', '3 days', 'As needed', '2026-11-07 10:00:00'),
(2, 4, '1ml', '30 days', 'Once daily', '2026-11-07 10:00:00'),
(3, 5, '50mg', '30 days', 'Once daily', '2026-11-04 11:00:00'),
(3, 6, '75mg', '30 days', 'Once daily', '2026-11-04 11:00:00'),
(4, 5, '50mg', '30 days', 'Once daily', '2026-11-10 11:00:00'),
(4, 7, '10mg', '30 days', 'Once daily', '2026-11-10 11:00:00'),
(5, 1, '5ml', '3 days', 'As needed for fever', '2026-11-09 09:00:00'),
(5, 3, '2 puffs', '30 days', 'As needed', '2026-11-09 09:00:00'),
(6, 3, '2 puffs', '30 days', 'As needed', '2026-11-12 09:00:00'),
(6, 4, '1ml', '30 days', 'Once daily', '2026-11-12 09:00:00'),
(7, 7, '10mg', '30 days', 'Once daily', '2026-11-14 10:00:00'),
(7, 8, '20mg', '30 days', 'Once daily at night', '2026-11-14 10:00:00'),
(8, 7, '10mg', '30 days', 'Once daily', '2026-11-15 10:00:00'),
(8, 8, '20mg', '30 days', 'Once daily at night', '2026-11-15 10:00:00'),
(9, 5, '50mg', '30 days', 'Once daily', '2026-11-17 11:00:00'),
(9, 7, '10mg', '30 days', 'Once daily', '2026-11-17 11:00:00'),
(10, 5, '50mg', '30 days', 'Once daily', '2026-11-20 11:00:00'),
(10, 7, '10mg', '30 days', 'Once daily', '2026-11-20 11:00:00'),
(11, 5, '25mg', '30 days', 'Twice daily', '2026-11-22 09:00:00'),
(11, 6, '75mg', '30 days', 'Once daily', '2026-11-22 09:00:00'),
(12, 5, '25mg', '30 days', 'Twice daily', '2026-11-25 09:00:00'),
(12, 6, '75mg', '30 days', 'Once daily', '2026-11-25 09:00:00'),
(13, 9, '400mg', '30 days', 'Three times daily', '2026-11-06 10:00:00'),
(13, 10, '500mg', '30 days', 'Twice daily', '2026-11-06 10:00:00'),
(14, 9, '400mg', '30 days', 'As needed', '2026-11-11 10:00:00'),
(14, 10, '500mg', '30 days', 'Twice daily', '2026-11-11 10:00:00'),
(15, 11, '300mg', '30 days', 'Three times daily', '2026-11-16 11:00:00'),
(15, 12, '50mg', '15 days', 'As needed for pain', '2026-11-16 11:00:00'),
(16, 11, '300mg', '30 days', 'Three times daily', '2026-11-18 11:00:00'),
(16, 12, '50mg', '15 days', 'As needed for pain', '2026-11-18 11:00:00'),
(17, 13, '500mg', '30 days', 'Twice daily', '2026-11-21 09:00:00'),
(17, 14, '25mg', '30 days', 'Once daily at night', '2026-11-21 09:00:00'),
(18, 13, '500mg', '30 days', 'Twice daily', '2026-11-24 09:00:00'),
(18, 14, '25mg', '30 days', 'Once daily at night', '2026-11-24 09:00:00'),
(19, 15, '2mg', '30 days', 'Three times daily', '2026-11-28 10:00:00'),
(19, 16, '100mg', '30 days', 'Once daily at night', '2026-11-28 10:00:00'),
(20, 15, '2mg', '30 days', 'Three times daily', '2026-11-30 10:00:00'),
(20, 16, '100mg', '30 days', 'Once daily at night', '2026-11-30 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_tbl`
--

CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int(11) NOT NULL,
  `APPOINTMENT_ID` int(11) NOT NULL,
  `ISSUE_DATE` date DEFAULT NULL,
  `HEIGHT_CM` int(11) DEFAULT NULL,
  `WEIGHT_KG` decimal(5,2) DEFAULT NULL,
  `BLOOD_PRESSURE` smallint(6) DEFAULT NULL,
  `DIABETES` enum('NO','TYPE-1','TYPE-2','PRE-DIABTIC') DEFAULT NULL,
  `SYMPTOMS` text DEFAULT NULL,
  `DIAGNOSIS` text DEFAULT NULL,
  `ADDITIONAL_NOTES` text DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_tbl`
--

INSERT INTO `prescription_tbl` (`PRESCRIPTION_ID`, `APPOINTMENT_ID`, `ISSUE_DATE`, `HEIGHT_CM`, `WEIGHT_KG`, `BLOOD_PRESSURE`, `DIABETES`, `SYMPTOMS`, `DIAGNOSIS`, `ADDITIONAL_NOTES`, `CREATED_AT`) VALUES
(1, 1, '2026-11-03', 120, 25.00, 110, 'NO', 'Fever, cough, and cold', 'Upper respiratory tract infection', 'Advise plenty of rest and fluids', '2026-11-03 10:00:00'),
(2, 2, '2026-11-07', 120, 25.00, 110, 'NO', 'Follow-up check', 'Recovering well', 'Continue prescribed medication', '2026-11-07 10:00:00'),
(3, 3, '2026-11-04', 160, 55.00, 120, 'NO', 'Chest pain, shortness of breath', 'Angina', 'Stress management recommended', '2026-11-04 11:00:00'),
(4, 4, '2026-11-10', 160, 55.00, 120, 'NO', 'Follow-up check', 'Stable condition', 'Continue medication as prescribed', '2026-11-10 11:00:00'),
(5, 5, '2026-11-09', 110, 20.00, 100, 'NO', 'Wheezing, difficulty breathing', 'Asthma exacerbation', 'Avoid triggers like dust and pollen', '2026-11-09 09:00:00'),
(6, 6, '2026-11-12', 110, 20.00, 100, 'NO', 'Follow-up check', 'Asthma under control', 'Continue inhaler as needed', '2026-11-12 09:00:00'),
(7, 7, '2026-11-14', 165, 60.00, 130, 'TYPE-2', 'Chest discomfort, palpitations', 'Hypertension with diabetes', 'Strict diet control required', '2026-11-14 10:00:00'),
(8, 8, '2026-11-15', 165, 60.00, 130, 'TYPE-2', 'Follow-up check', 'Blood sugar levels improving', 'Continue medication and exercise', '2026-11-15 10:00:00'),
(9, 9, '2026-11-17', 170, 75.00, 140, 'PRE-DIABTIC', 'High blood pressure, dizziness', 'Hypertension', 'Reduce salt intake', '2026-11-17 11:00:00'),
(10, 10, '2026-11-20', 170, 75.00, 140, 'PRE-DIABTIC', 'Follow-up check', 'Blood pressure controlled', 'Continue lifestyle changes', '2026-11-20 11:00:00'),
(11, 11, '2026-11-22', 155, 50.00, 120, 'NO', 'Irregular heartbeat', 'Arrhythmia', 'Avoid caffeine and stress', '2026-11-22 09:00:00'),
(12, 12, '2026-11-25', 155, 50.00, 120, 'NO', 'Follow-up check', 'Heart rhythm normal', 'Continue medication as prescribed', '2026-11-25 09:00:00'),
(13, 13, '2026-11-06', 175, 80.00, 125, 'NO', 'Knee pain, difficulty walking', 'Osteoarthritis', 'Physical therapy recommended', '2026-11-06 10:00:00'),
(14, 14, '2026-11-11', 175, 80.00, 125, 'NO', 'Follow-up check', 'Pain management effective', 'Continue exercises', '2026-11-11 10:00:00'),
(15, 15, '2026-11-16', 160, 65.00, 115, 'NO', 'Back pain, numbness in legs', 'Herniated disc', 'Surgery may be considered if no improvement', '2026-11-16 11:00:00'),
(16, 16, '2026-11-18', 160, 65.00, 115, 'NO', 'Follow-up check', 'Symptoms improving', 'Continue physiotherapy', '2026-11-18 11:00:00'),
(17, 17, '2026-11-21', 172, 70.00, 130, 'NO', 'Frequent headaches, dizziness', 'Migraine', 'Identify and avoid triggers', '2026-11-21 09:00:00'),
(18, 18, '2026-11-24', 172, 70.00, 130, 'NO', 'Follow-up check', 'Migraine frequency reduced', 'Continue preventive medication', '2026-11-24 09:00:00'),
(19, 19, '2026-11-28', 165, 60.00, 120, 'NO', 'Seizures, loss of consciousness', 'Epilepsy', 'Regular medication essential', '2026-11-28 10:00:00'),
(20, 20, '2026-11-30', 165, 60.00, 120, 'NO', 'Follow-up check', 'Seizure controlled', 'Never skip medication', '2026-11-30 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_tbl`
--

CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(20) DEFAULT NULL,
  `LAST_NAME` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  `PHONE` bigint(20) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `ADDRESS` text DEFAULT NULL,
  `USERNAME` varchar(20) DEFAULT NULL,
  `PSWD` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionist_tbl`
--

INSERT INTO `receptionist_tbl` (`RECEPTIONIST_ID`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `GENDER`, `PHONE`, `EMAIL`, `ADDRESS`, `USERNAME`, `PSWD`) VALUES
(1, 'Meena', 'Kumari', '1985-03-15', '2018-06-20', 'FEMALE', 9876543301, 'meena.k@gmail.com', '123, Staff Quarters, Mumbai', 'Meena_k01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K'),
(2, 'Ramesh', 'Kumar', '1987-07-22', '2019-09-15', 'MALE', 9876543302, 'ramesh.k@gmail.com', '456, Staff Quarters, Delhi', 'Ramesh_k02', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K'),
(3, 'Sunita', 'Devi', '1990-11-10', '2020-03-25', 'FEMALE', 9876543303, 'sunita.d@gmail.com', '789, Staff Quarters, Bangalore', 'Sunita_d03', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K'),
(4, 'Anil', 'Sharma', '1988-05-18', '2021-07-10', 'MALE', 9876543304, 'anil.s@gmail.com', '321, Staff Quarters, Pune', 'Anil_s04', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K');

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment_reminder_tbl`
--
ALTER TABLE `appointment_reminder_tbl`
  ADD PRIMARY KEY (`APPOINTMENT_REMINDER_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

--
-- Indexes for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  ADD PRIMARY KEY (`APPOINTMENT_ID`),
  ADD KEY `PATIENT_ID` (`PATIENT_ID`),
  ADD KEY `DOCTOR_ID` (`DOCTOR_ID`),
  ADD KEY `SCHEDULE_ID` (`SCHEDULE_ID`);

--
-- Indexes for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  ADD PRIMARY KEY (`SCHEDULE_ID`),
  ADD KEY `DOCTOR_ID` (`DOCTOR_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`);

--
-- Indexes for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  ADD PRIMARY KEY (`DOCTOR_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`),
  ADD KEY `SPECIALISATION_ID` (`SPECIALISATION_ID`);

--
-- Indexes for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  ADD PRIMARY KEY (`FEEDBACK_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

--
-- Indexes for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  ADD PRIMARY KEY (`MEDICINE_REMINDER_ID`),
  ADD KEY `MEDICINE_ID` (`MEDICINE_ID`),
  ADD KEY `PATIENT_ID` (`PATIENT_ID`);

--
-- Indexes for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  ADD PRIMARY KEY (`MEDICINE_ID`),
  ADD KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`);

--
-- Indexes for table `patient_tbl`
--
ALTER TABLE `patient_tbl`
  ADD PRIMARY KEY (`PATIENT_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- Indexes for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD PRIMARY KEY (`PAYMENT_ID`),
  ADD UNIQUE KEY `TRANSACTION_ID` (`TRANSACTION_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

--
-- Indexes for table `prescription_medicine_tbl`
--
ALTER TABLE `prescription_medicine_tbl`
  ADD PRIMARY KEY (`PRESCRIPTION_ID`,`MEDICINE_ID`),
  ADD KEY `MEDICINE_ID` (`MEDICINE_ID`);

--
-- Indexes for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  ADD PRIMARY KEY (`PRESCRIPTION_ID`),
  ADD KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`);

--
-- Indexes for table `receptionist_tbl`
--
ALTER TABLE `receptionist_tbl`
  ADD PRIMARY KEY (`RECEPTIONIST_ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`);

--
-- Indexes for table `specialisation_tbl`
--
ALTER TABLE `specialisation_tbl`
  ADD PRIMARY KEY (`SPECIALISATION_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment_reminder_tbl`
--
ALTER TABLE `appointment_reminder_tbl`
  MODIFY `APPOINTMENT_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  MODIFY `APPOINTMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  MODIFY `SCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  MODIFY `DOCTOR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  MODIFY `MEDICINE_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  MODIFY `MEDICINE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `patient_tbl`
--
ALTER TABLE `patient_tbl`
  MODIFY `PATIENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `PAYMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  MODIFY `PRESCRIPTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `receptionist_tbl`
--
ALTER TABLE `receptionist_tbl`
  MODIFY `RECEPTIONIST_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `specialisation_tbl`
--
ALTER TABLE `specialisation_tbl`
  MODIFY `SPECIALISATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_reminder_tbl`
--
ALTER TABLE `appointment_reminder_tbl`
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_2` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  ADD CONSTRAINT `appointment_tbl_ibfk_1` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_tbl_ibfk_2` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_tbl_ibfk_3` FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `doctor_schedule_tbl` (`SCHEDULE_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_1` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_2` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  ADD CONSTRAINT `doctor_tbl_ibfk_1` FOREIGN KEY (`SPECIALISATION_ID`) REFERENCES `specialisation_tbl` (`SPECIALISATION_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  ADD CONSTRAINT `feedback_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_1` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_2` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  ADD CONSTRAINT `medicine_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `prescription_medicine_tbl`
--
ALTER TABLE `prescription_medicine_tbl`
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_1` FOREIGN KEY (`PRESCRIPTION_ID`) REFERENCES `prescription_tbl` (`PRESCRIPTION_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_2` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  ADD CONSTRAINT `prescription_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
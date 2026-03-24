-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 09:45 AM
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
--

INSERT INTO `appointment_reminder_tbl` (`APPOINTMENT_REMINDER_ID`, `RECEPTIONIST_ID`, `APPOINTMENT_ID`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 1, '10:00:00', 'Appointment booked'),
(2, 1, 1, '10:00:00', '24 hours before appointment'),
(3, 1, 1, '07:00:00', '3 hours before appointment'),
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
(61, 3, 38, '10:00:00', 'Appointment booked'),
(62, 3, 38, '10:00:00', '24 hours before appointment'),
(63, 3, 38, '07:00:00', '3 hours before appointment'),
(64, 4, 39, '09:00:00', 'Appointment booked'),
(65, 4, 39, '09:00:00', '24 hours before appointment'),
(66, 4, 39, '06:00:00', '3 hours before appointment'),
(67, 1, 40, '08:00:00', 'Appointment booked'),
(68, 1, 40, '08:00:00', '24 hours before appointment'),
(69, 1, 40, '05:00:00', '3 hours before appointment'),
(70, 3, 41, '10:00:00', 'Appointment booked'),
(71, 3, 41, '10:00:00', '24 hours before appointment'),
(72, 3, 41, '07:00:00', '3 hours before appointment'),
(73, 3, 42, '11:00:00', 'Appointment booked'),
(74, 3, 42, '11:00:00', '24 hours before appointment'),
(75, 3, 42, '08:00:00', '3 hours before appointment'),
(76, 4, 43, '10:00:00', 'Appointment booked'),
(77, 4, 43, '10:00:00', '24 hours before appointment'),
(78, 4, 43, '07:00:00', '3 hours before appointment'),
(79, 1, 44, '09:00:00', 'Appointment booked'),
(80, 1, 44, '09:00:00', '24 hours before appointment'),
(81, 1, 44, '06:00:00', '3 hours before appointment'),
(82, 3, 45, '11:00:00', 'Appointment booked'),
(83, 3, 45, '11:00:00', '24 hours before appointment'),
(84, 3, 45, '08:00:00', '3 hours before appointment');

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

INSERT INTO `appointment_tbl` (`APPOINTMENT_ID`, `PATIENT_ID`, `DOCTOR_ID`, `SCHEDULE_ID`, `CREATED_AT`, `APPOINTMENT_DATE`, `APPOINTMENT_TIME`, `STATUS`) VALUES
(1, 1, 1, 3, '2026-03-19 10:00:00', '2026-03-20', '11:00:00', 'COMPLETED'),
(3, 4, 2, 4, '2026-03-22 00:00:00', '2026-03-25', '09:00:00', 'SCHEDULED'),
(4, 5, 2, 5, '2026-03-23 00:00:00', '2026-03-26', '10:00:00', 'SCHEDULED'),
(5, 6, 2, 6, '2026-03-24 00:00:00', '2026-03-27', '11:00:00', 'SCHEDULED'),
(6, 7, 3, 7, '2026-03-23 00:00:00', '2026-03-28', '09:00:00', 'SCHEDULED'),
(7, 8, 3, 8, '2026-03-24 00:00:00', '2026-03-29', '10:00:00', 'SCHEDULED'),
(8, 9, 3, 9, '2026-03-23 00:00:00', '2026-03-30', '11:00:00', 'SCHEDULED'),
(9, 10, 4, 10, '2026-03-20 00:00:00', '2026-03-31', '09:00:00', 'SCHEDULED'),
(10, 1, 4, 11, '2026-03-21 00:00:00', '2026-04-01', '10:00:00', 'SCHEDULED'),
(11, 2, 4, 12, '2026-03-22 00:00:00', '2026-04-02', '11:00:00', 'SCHEDULED'),
(12, 3, 5, 13, '2026-03-23 00:00:00', '2026-04-03', '09:00:00', 'SCHEDULED'),
(13, 4, 5, 14, '2026-03-20 00:00:00', '2026-04-04', '10:00:00', 'SCHEDULED'),
(14, 5, 5, 15, '2026-03-19 00:00:00', '2026-04-05', '11:00:00', 'SCHEDULED'),
(15, 6, 6, 16, '2026-03-18 00:00:00', '2026-04-06', '09:00:00', 'SCHEDULED'),
(16, 7, 6, 17, '2026-03-20 00:00:00', '2026-04-07', '10:00:00', 'CANCELLED'),
(17, 8, 6, 18, '2026-03-19 00:00:00', '2026-04-08', '11:00:00', 'SCHEDULED'),
(18, 9, 7, 19, '2026-03-23 00:00:00', '2026-04-09', '09:00:00', 'CANCELLED'),
(19, 10, 7, 20, '2026-03-24 00:00:00', '2026-04-10', '10:00:00', 'CANCELLED'),
(20, 1, 7, 21, '2026-03-24 00:00:00', '2026-04-11', '11:00:00', 'SCHEDULED'),
(21, 2, 8, 22, '2026-03-23 00:00:00', '2026-04-12', '09:00:00', 'SCHEDULED'),
(22, 3, 8, 23, '2026-03-19 00:00:00', '2026-04-13', '10:00:00', 'SCHEDULED'),
(23, 4, 8, 24, '2026-03-11 00:00:00', '2026-04-14', '11:00:00', 'SCHEDULED'),
(24, 5, 9, 25, '2026-03-12 00:00:00', '2026-04-15', '09:00:00', 'SCHEDULED'),
(25, 6, 9, 26, '2026-03-13 00:00:00', '2026-04-16', '10:00:00', 'SCHEDULED'),
(26, 7, 9, 27, '2026-03-14 00:00:00', '2026-04-17', '11:00:00', 'SCHEDULED'),
(27, 8, 10, 28, '2026-03-15 00:00:00', '2026-04-18', '09:00:00', 'SCHEDULED'),
(28, 9, 10, 29, '2026-03-16 00:00:00', '2026-04-19', '10:00:00', 'SCHEDULED'),
(29, 10, 10, 30, '2026-03-17 00:00:00', '2026-04-20', '11:00:00', 'SCHEDULED'),
(35, 1, 1, 2, '2026-03-23 14:51:23', '2026-03-25', '16:00:00', 'SCHEDULED'),
(36, 3, 4, 10, '2026-03-24 08:10:21', '2026-03-24', '14:00:00', 'COMPLETED'),
(38, 2, 5, 13, '2026-03-15 06:30:00', '2026-03-16', '10:00:00', 'COMPLETED'),
(39, 3, 7, 19, '2026-03-15 06:30:00', '2026-03-16', '09:00:00', 'COMPLETED'),
(40, 4, 9, 25, '2026-03-15 06:30:00', '2026-03-09', '08:00:00', 'COMPLETED'),
(41, 5, 15, 44, '2026-03-15 06:30:00', '2026-03-18', '10:00:00', 'COMPLETED'),
(42, 6, 5, 13, '2026-03-15 06:30:00', '2026-03-16', '11:00:00', 'COMPLETED'),
(43, 7, 7, 19, '2026-03-15 06:30:00', '2026-03-16', '10:00:00', 'COMPLETED'),
(44, 8, 9, 25, '2026-03-15 06:30:00', '2026-03-09', '09:00:00', 'COMPLETED'),
(45, 9, 15, 44, '2026-03-15 06:30:00', '2026-03-18', '11:00:00', 'COMPLETED');

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
(2, 38, 5, 'Dr. was very thorough in explaining my heart condition. Felt very reassured about the treatment plan. Excellent care.'),
(3, 39, 4, 'Good consultation. Doctor explained the knee problem clearly. Waiting time was a bit long but overall satisfied.'),
(4, 40, 5, 'Very professional and caring. Took time to understand my migraine triggers. Prescribed effective medication.'),
(5, 41, 5, 'Excellent pediatrician! Very gentle with my child. Explained everything clearly and put our minds at ease.'),
(6, 42, 4, 'Competent doctor but appointment felt rushed. Medication seems to be working well though.'),
(7, 43, 5, 'Great experience. Doctor listened carefully to my concerns and provided practical advice for managing back pain.'),
(8, 44, 5, 'Very knowledgeable and empathetic. Helped us understand the diagnosis and next steps. Highly recommend.'),
(9, 45, 5, 'Outstanding care for my child. Took time to teach us how to use the inhaler properly. Very patient and kind.');

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

INSERT INTO `medicine_reminder_tbl` (`MEDICINE_REMINDER_ID`, `MEDICINE_ID`, `CREATOR_ROLE`, `CREATOR_ID`, `PATIENT_ID`, `START_DATE`, `END_DATE`, `REMINDER_TIME`, `REMARKS`) VALUES
(7, 5, 'RECEPTIONIST', 1, 1, '2026-03-20', '2026-03-22', '10:00:00', 'take your medicine after breakfast'),
(8, 5, 'RECEPTIONIST', 1, 1, '2026-03-20', '2026-03-22', '14:00:00', 'Have your medicine if you have taken lunch'),
(9, 17, 'RECEPTIONIST', 1, 1, '2026-03-20', '2026-03-25', '09:00:00', 'take your syrup after breakfast'),
(10, 17, 'RECEPTIONIST', 1, 1, '2026-03-20', '2026-03-25', '14:00:00', 'take your syrup after lunch'),
(11, 17, 'RECEPTIONIST', 1, 1, '2026-03-20', '2026-03-25', '21:00:00', 'take your syrup after dinner');

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
(16, 4, 'Topiramate', 'Anti-epileptic and migraine prevention'),
(17, 1, 'Ambroxol Syrup', 'Loosen mucus and relieve cough');

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
(6, 'receptionist', 1, '7b314175825024447f75593b0c0803d0013ae963', '2026-03-21 21:36:29'),
(7, 'receptionist', 1, '19cfccfbfdcb507d369dc44bad91ea6cf4867cb6', '2026-03-24 14:01:54'),
(8, 'receptionist', 1, '6f455d53e1729ef5c8cd9610de5664303271e469', '2026-03-24 14:01:54');

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
(1, 'Arjun', 'Mishra', 'Arjun_m01', '$2y$10$3mncvv9RcxtYax9yfevdOenGwQo5mOwt4tFobJp.iveNEyfSsVA9K', '2022-02-01', 'MALE', 'B+', 9876543201, 'suchi.mishra@gmail.com', '123, Navrangpura, Ahmedabad', '', 'What was the name of your first school?', 'Sunrise Public School'),
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

INSERT INTO `payment_tbl` (`PAYMENT_ID`, `APPOINTMENT_ID`, `AMOUNT`, `PAYMENT_DATE`, `PAYMENT_MODE`, `STATUS`, `TRANSACTION_ID`, `CREATED_AT`) VALUES
(1, 1, 300.00, '2026-03-19', 'CREDIT CARD', 'COMPLETED', 'TXN100000001', '2026-03-19 10:00:00'),
(3, 3, 300.00, '2026-03-22', 'UPI', 'COMPLETED', 'TXN100000003', '2026-03-22 00:00:00'),
(4, 4, 300.00, '2026-03-23', 'NET BANKING', 'COMPLETED', 'TXN100000004', '2026-03-23 00:00:00'),
(5, 5, 300.00, '2026-03-24', 'CREDIT CARD', 'COMPLETED', 'TXN100000005', '2026-03-24 00:00:00'),
(6, 6, 300.00, '2026-03-23', 'GOOGLE PAY', 'COMPLETED', 'TXN100000006', '2026-03-23 00:00:00'),
(7, 7, 300.00, '2026-03-24', 'UPI', 'COMPLETED', 'TXN100000007', '2026-03-24 00:00:00'),
(8, 8, 300.00, '2026-03-23', 'NET BANKING', 'COMPLETED', 'TXN100000008', '2026-03-23 00:00:00'),
(9, 9, 300.00, '2026-03-20', 'CREDIT CARD', 'COMPLETED', 'TXN100000009', '2026-03-20 00:00:00'),
(10, 10, 300.00, '2026-03-21', 'GOOGLE PAY', 'COMPLETED', 'TXN100000010', '2026-03-21 00:00:00'),
(11, 11, 300.00, '2026-03-22', 'UPI', 'COMPLETED', 'TXN100000011', '2026-03-22 00:00:00'),
(12, 12, 300.00, '2026-03-23', 'NET BANKING', 'COMPLETED', 'TXN100000012', '2026-03-23 00:00:00'),
(13, 13, 300.00, '2026-03-20', 'CREDIT CARD', 'COMPLETED', 'TXN100000013', '2026-03-20 00:00:00'),
(14, 14, 300.00, '2026-03-19', 'GOOGLE PAY', 'COMPLETED', 'TXN100000014', '2026-03-19 00:00:00'),
(15, 15, 300.00, '2026-03-18', 'UPI', 'COMPLETED', 'TXN100000015', '2026-03-18 00:00:00'),
(16, 16, 300.00, '2026-03-20', 'NET BANKING', 'COMPLETED', 'TXN100000016', '2026-03-20 00:00:00'),
(17, 17, 300.00, '2026-03-19', 'CREDIT CARD', 'COMPLETED', 'TXN100000017', '2026-03-19 00:00:00'),
(18, 18, 300.00, '2026-03-23', 'GOOGLE PAY', 'COMPLETED', 'TXN100000018', '2026-03-23 00:00:00'),
(19, 19, 300.00, '2026-03-24', 'UPI', 'COMPLETED', 'TXN100000019', '2026-03-24 00:00:00'),
(20, 20, 300.00, '2026-03-24', 'NET BANKING', 'COMPLETED', 'TXN100000020', '2026-03-24 00:00:00'),
(21, 21, 300.00, '2026-03-23', 'CREDIT CARD', 'COMPLETED', 'TXN100000021', '2026-03-23 00:00:00'),
(22, 22, 300.00, '2026-03-19', 'GOOGLE PAY', 'COMPLETED', 'TXN100000022', '2026-03-19 00:00:00'),
(23, 23, 300.00, '2026-03-11', 'UPI', 'COMPLETED', 'TXN100000023', '2026-03-11 00:00:00'),
(24, 24, 300.00, '2026-03-12', 'NET BANKING', 'COMPLETED', 'TXN100000024', '2026-03-12 00:00:00'),
(25, 25, 300.00, '2026-03-13', 'CREDIT CARD', 'COMPLETED', 'TXN100000025', '2026-03-13 00:00:00'),
(26, 26, 300.00, '2026-03-14', 'GOOGLE PAY', 'COMPLETED', 'TXN100000026', '2026-03-14 00:00:00'),
(27, 27, 300.00, '2026-03-15', 'UPI', 'COMPLETED', 'TXN100000027', '2026-03-15 00:00:00'),
(28, 28, 300.00, '2026-03-16', 'NET BANKING', 'COMPLETED', 'TXN100000028', '2026-03-16 00:00:00'),
(29, 29, 300.00, '2026-03-17', 'CREDIT CARD', 'COMPLETED', 'TXN100000029', '2026-03-17 00:00:00'),
(31, 35, 300.00, '2026-03-23', 'UPI', 'COMPLETED', 'pay_SUhqxej69FCZLv', '2026-03-23 14:51:23'),
(32, 36, 300.00, '2026-03-24', 'UPI', 'COMPLETED', 'pay_SUzYSzukmeAVAx', '2026-03-24 08:10:21'),
(34, 38, 300.00, '2026-03-15', 'UPI', 'COMPLETED', 'TXN100000031', '2026-03-15 06:30:00'),
(35, 39, 300.00, '2026-03-15', 'CREDIT CARD', 'COMPLETED', 'TXN100000032', '2026-03-15 06:30:00'),
(36, 40, 300.00, '2026-03-15', 'NET BANKING', 'COMPLETED', 'TXN100000033', '2026-03-15 06:30:00'),
(37, 41, 300.00, '2026-03-15', 'GOOGLE PAY', 'COMPLETED', 'TXN100000034', '2026-03-15 06:30:00'),
(38, 42, 300.00, '2026-03-15', 'UPI', 'COMPLETED', 'TXN100000035', '2026-03-15 06:30:00'),
(39, 43, 300.00, '2026-03-15', 'CREDIT CARD', 'COMPLETED', 'TXN100000036', '2026-03-15 06:30:00'),
(40, 44, 300.00, '2026-03-15', 'NET BANKING', 'COMPLETED', 'TXN100000037', '2026-03-15 06:30:00'),
(41, 45, 300.00, '2026-03-15', 'GOOGLE PAY', 'COMPLETED', 'TXN100000038', '2026-03-15 06:30:00');

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

INSERT INTO `prescription_medicine_tbl` (`PRESCRIPTION_ID`, `MEDICINE_ID`, `DOSAGE`, `DURATION`, `FREQUENCY`, `CREATED_AT`) VALUES
(1, 1, '5ml', '3 days', 'Twice daily after meals', '2026-03-20 11:40:00'),
(1, 17, '5ml', '7 days', 'Three times daily', '2026-03-20 11:40:00'),
(21, 5, '20mg', '30 days', 'Once daily', '2026-03-24 08:39:51'),
(21, 6, '75mg', '30 days', 'Once daily', '2026-03-24 08:39:51'),
(21, 7, '10mg', '30 days', 'Once daily', '2026-03-24 08:39:51'),
(23, 5, '50mg', '30 days', 'Once daily morning', '2026-03-16 10:45:00'),
(23, 6, '75mg', '30 days', 'Once daily evening', '2026-03-16 10:45:00'),
(23, 7, '10mg', '30 days', 'Once daily morning', '2026-03-16 10:45:00'),
(24, 9, '400mg', '14 days', 'Thrice daily after meals', '2026-03-16 09:50:00'),
(24, 10, '500mg', '30 days', 'Twice daily', '2026-03-16 09:50:00'),
(25, 14, '25mg', '30 days', 'Once daily at night', '2026-03-09 08:45:00'),
(25, 16, '50mg', '30 days', 'Twice daily', '2026-03-09 08:45:00'),
(26, 1, '5ml', '5 days', 'Thrice daily', '2026-03-18 10:40:00'),
(26, 17, '5ml', '7 days', 'Twice daily', '2026-03-18 10:40:00'),
(27, 5, '100mg', '30 days', 'Once daily morning', '2026-03-16 11:50:00'),
(27, 6, '150mg', '30 days', 'Once daily morning', '2026-03-16 11:50:00'),
(27, 7, '20mg', '30 days', 'Once daily morning', '2026-03-16 11:50:00'),
(27, 8, '40mg', '30 days', 'Once daily at night', '2026-03-16 11:50:00'),
(28, 9, '400mg', '10 days', 'Thrice daily after meals', '2026-03-16 10:45:00'),
(28, 12, '50mg', '7 days', 'Twice daily', '2026-03-16 10:45:00'),
(29, 13, '500mg', '30 days', 'Twice daily', '2026-03-09 09:55:00'),
(29, 15, '2mg', '30 days', 'Thrice daily with food', '2026-03-09 09:55:00'),
(30, 3, '2 puffs', '30 days', 'As needed', '2026-03-18 11:50:00'),
(30, 17, '5ml', '14 days', 'Twice daily', '2026-03-18 11:50:00');

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
  `BLOOD_PRESSURE` varchar(20) NOT NULL,
  `DIABETES` enum('NO','TYPE-1','TYPE-2','PRE-DIABTIC') NOT NULL,
  `SYMPTOMS` text NOT NULL,
  `DIAGNOSIS` text NOT NULL,
  `ADDITIONAL_NOTES` text NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_tbl`
--

INSERT INTO `prescription_tbl` (`PRESCRIPTION_ID`, `APPOINTMENT_ID`, `ISSUE_DATE`, `HEIGHT_CM`, `WEIGHT_KG`, `BLOOD_PRESSURE`, `DIABETES`, `SYMPTOMS`, `DIAGNOSIS`, `ADDITIONAL_NOTES`, `CREATED_AT`) VALUES
(1, 1, '2026-03-20', 120, 25.00, '110/70', 'NO', 'Fever, cough, and cold', 'Upper respiratory tract infection', 'Advise plenty of rest and fluids', '2026-03-20 11:40:00'),
(21, 36, '2026-03-24', 164, 78.00, '140/95', 'NO', 'Chest tightness, pain radiating to left arm, sweating, breathlessness, irregular pulse', 'Stage 1 Hypertension, Stable Angina, Atrial Fibrillation', 'Reduce salt intake, avoid stress, regular monitoring', '2026-03-24 08:33:26'),
(23, 38, '2026-03-16', 172, 78.00, '145/92', 'NO', 'Chest pain, shortness of breath, fatigue', 'Hypertension, mild coronary artery disease', 'Patient advised to reduce salt intake and exercise regularly. Follow-up in 2 weeks.', '2026-03-16 10:45:00'),
(24, 39, '2026-03-16', 165, 82.00, '128/84', 'NO', 'Knee pain, difficulty walking, swelling', 'Osteoarthritis of right knee', 'Recommend physiotherapy sessions. Avoid strenuous activities. Apply ice packs.', '2026-03-16 09:50:00'),
(25, 40, '2026-03-09', 168, 71.00, '118/76', 'NO', 'Severe headache, dizziness, nausea', 'Migraine with aura', 'Patient advised to avoid triggers like bright lights and stress. Keep headache diary.', '2026-03-09 08:45:00'),
(26, 41, '2026-03-18', 95, 15.00, '95/60', 'NO', 'High fever, cough, runny nose', 'Viral upper respiratory tract infection', 'Keep child hydrated. Monitor temperature. Rest advised. Return if fever persists beyond 3 days.', '2026-03-18 10:40:00'),
(27, 42, '2026-03-16', 180, 95.00, '152/96', 'TYPE-2', 'Palpitations, chest discomfort, anxiety', 'Atrial fibrillation, poorly controlled hypertension', 'Strict medication compliance required. Regular BP monitoring at home. Low sodium diet essential.', '2026-03-16 11:50:00'),
(28, 43, '2026-03-16', 158, 68.00, '122/78', 'NO', 'Lower back pain, stiffness, limited mobility', 'Lumbar strain with muscle spasm', 'Avoid heavy lifting. Apply heat therapy. Maintain good posture. Gentle stretching exercises recommended.', '2026-03-16 10:45:00'),
(29, 44, '2026-03-09', 175, 85.00, '135/88', 'NO', 'Memory problems, confusion, tremors', 'Early stage Parkinson\'s disease', 'Patient enrolled in support group. Family counseling recommended. Regular neurological follow-ups scheduled.', '2026-03-09 09:55:00'),
(30, 45, '2026-03-18', 110, 22.00, '100/65', 'NO', 'Persistent cough, wheezing, breathing difficulty', 'Bronchial asthma', 'Teach proper inhaler technique. Avoid allergens and cold air. Emergency action plan provided to parents.', '2026-03-18 11:50:00');

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
-- Stand-in structure for view `view_appointment_report`
--
CREATE TABLE `view_appointment_report` (
`APPOINTMENT_ID` int(11)
,`Patient_Name` varchar(41)
,`Doctor_Name` varchar(41)
,`Specialisation` varchar(50)
,`APPOINTMENT_DATE` date
,`Day_Name` varchar(9)
,`Week_Number` int(2)
,`Month_Number` int(2)
,`Month_Name` varchar(9)
,`Year` int(4)
,`APPOINTMENT_TIME` time
,`STATUS` enum('SCHEDULED','COMPLETED','CANCELLED')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_doctor_report`
--
CREATE TABLE `view_doctor_report` (
`DOCTOR_ID` int(11)
,`Doctor_Name` varchar(41)
,`Specialisation` varchar(50)
,`EDUCATION` varchar(50)
,`Doctor_Status` enum('pending','approved','rejected')
,`APPOINTMENT_ID` int(11)
,`APPOINTMENT_DATE` date
,`Month_Name` varchar(9)
,`Month_Number` int(2)
,`Year` int(4)
,`Appointment_Status` varchar(9)
,`Total_Patients` bigint(21)
,`Avg_Rating` decimal(12,1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_patient_report`
--
CREATE TABLE `view_patient_report` (
`PATIENT_ID` int(11)
,`Patient_Name` varchar(41)
,`GENDER` enum('MALE','FEMALE','OTHER')
,`BLOOD_GROUP` enum('A+','A-','B+','B-','O+','O-','AB+','AB-')
,`PHONE` bigint(20)
,`EMAIL` varchar(50)
,`ADDRESS` text
,`Total_Appointments` bigint(21)
,`Completed_Visits` decimal(22,0)
,`Upcoming_Visits` decimal(22,0)
,`Cancelled_Visits` decimal(22,0)
,`Last_Visit_Date` date
,`First_Visit_Date` date
,`Doctors_Visited` bigint(21)
,`Total_Prescriptions` bigint(21)
,`Total_Amount_Paid` decimal(32,2)
,`Successful_Payments` bigint(21)
,`Total_Feedback_Given` bigint(21)
,`Avg_Rating_Given` decimal(12,1)
,`Medicine_Reminders` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_payment_report`
--
CREATE TABLE `view_payment_report` (
`PAYMENT_ID` int(11)
,`TRANSACTION_ID` varchar(36)
,`Patient_Name` varchar(41)
,`Doctor_Name` varchar(41)
,`AMOUNT` decimal(10,2)
,`PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING')
,`Payment_Status` enum('COMPLETED','FAILED')
,`PAYMENT_DATE` date
,`Day_Name` varchar(9)
,`Week_Number` int(2)
,`Month_Number` int(2)
,`Month_Name` varchar(9)
,`Year` int(4)
);

-- --------------------------------------------------------

--
-- Structure for view `view_appointment_report`
--
DROP TABLE IF EXISTS `view_appointment_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_appointment_report`  AS SELECT `a`.`APPOINTMENT_ID` AS `APPOINTMENT_ID`, concat(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`, concat(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`, `s`.`SPECIALISATION_NAME` AS `Specialisation`, `a`.`APPOINTMENT_DATE` AS `APPOINTMENT_DATE`, dayname(`a`.`APPOINTMENT_DATE`) AS `Day_Name`, week(`a`.`APPOINTMENT_DATE`) AS `Week_Number`, month(`a`.`APPOINTMENT_DATE`) AS `Month_Number`, monthname(`a`.`APPOINTMENT_DATE`) AS `Month_Name`, year(`a`.`APPOINTMENT_DATE`) AS `Year`, `a`.`APPOINTMENT_TIME` AS `APPOINTMENT_TIME`, `a`.`STATUS` AS `STATUS` FROM (((`appointment_tbl` `a` join `patient_tbl` `p` on(`a`.`PATIENT_ID` = `p`.`PATIENT_ID`)) join `doctor_tbl` `d` on(`a`.`DOCTOR_ID` = `d`.`DOCTOR_ID`)) join `specialisation_tbl` `s` on(`d`.`SPECIALISATION_ID` = `s`.`SPECIALISATION_ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_doctor_report`
--
DROP TABLE IF EXISTS `view_doctor_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_doctor_report`  AS SELECT `d`.`DOCTOR_ID` AS `DOCTOR_ID`, concat(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`, `s`.`SPECIALISATION_NAME` AS `Specialisation`, `d`.`EDUCATION` AS `EDUCATION`, `d`.`STATUS` AS `Doctor_Status`, `a`.`APPOINTMENT_ID` AS `APPOINTMENT_ID`, `a`.`APPOINTMENT_DATE` AS `APPOINTMENT_DATE`, coalesce(monthname(`a`.`APPOINTMENT_DATE`),'N/A') AS `Month_Name`, coalesce(month(`a`.`APPOINTMENT_DATE`),0) AS `Month_Number`, coalesce(year(`a`.`APPOINTMENT_DATE`),0) AS `Year`, coalesce(`a`.`STATUS`,'NONE') AS `Appointment_Status`, coalesce(`doc_stats`.`Total_Patients`,0) AS `Total_Patients`, coalesce(`doc_stats`.`Avg_Rating`,0) AS `Avg_Rating` FROM (((`doctor_tbl` `d` join `specialisation_tbl` `s` on(`d`.`SPECIALISATION_ID` = `s`.`SPECIALISATION_ID`)) left join `appointment_tbl` `a` on(`d`.`DOCTOR_ID` = `a`.`DOCTOR_ID`)) left join (select `a2`.`DOCTOR_ID` AS `DOCTOR_ID`,count(distinct `a2`.`PATIENT_ID`) AS `Total_Patients`,round(avg(`f2`.`RATING`),1) AS `Avg_Rating` from (`appointment_tbl` `a2` left join `feedback_tbl` `f2` on(`a2`.`APPOINTMENT_ID` = `f2`.`APPOINTMENT_ID`)) group by `a2`.`DOCTOR_ID`) `doc_stats` on(`d`.`DOCTOR_ID` = `doc_stats`.`DOCTOR_ID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_patient_report`
--
DROP TABLE IF EXISTS `view_patient_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_patient_report`  AS SELECT `p`.`PATIENT_ID` AS `PATIENT_ID`, concat(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`, `p`.`GENDER` AS `GENDER`, `p`.`BLOOD_GROUP` AS `BLOOD_GROUP`, `p`.`PHONE` AS `PHONE`, `p`.`EMAIL` AS `EMAIL`, `p`.`ADDRESS` AS `ADDRESS`, count(distinct `a`.`APPOINTMENT_ID`) AS `Total_Appointments`, sum(case when `a`.`STATUS` = 'COMPLETED' then 1 else 0 end) AS `Completed_Visits`, sum(case when `a`.`STATUS` = 'SCHEDULED' then 1 else 0 end) AS `Upcoming_Visits`, sum(case when `a`.`STATUS` = 'CANCELLED' then 1 else 0 end) AS `Cancelled_Visits`, max(`a`.`APPOINTMENT_DATE`) AS `Last_Visit_Date`, min(`a`.`APPOINTMENT_DATE`) AS `First_Visit_Date`, count(distinct `a`.`DOCTOR_ID`) AS `Doctors_Visited`, count(distinct `pr`.`PRESCRIPTION_ID`) AS `Total_Prescriptions`, coalesce(sum(case when `py`.`STATUS` = 'COMPLETED' then `py`.`AMOUNT` else 0 end),0) AS `Total_Amount_Paid`, count(distinct case when `py`.`STATUS` = 'COMPLETED' then `py`.`PAYMENT_ID` end) AS `Successful_Payments`, count(distinct `f`.`FEEDBACK_ID`) AS `Total_Feedback_Given`, round(avg(`f`.`RATING`),1) AS `Avg_Rating_Given`, count(distinct `mr`.`MEDICINE_REMINDER_ID`) AS `Medicine_Reminders` FROM (((((`patient_tbl` `p` left join `appointment_tbl` `a` on(`p`.`PATIENT_ID` = `a`.`PATIENT_ID`)) left join `prescription_tbl` `pr` on(`a`.`APPOINTMENT_ID` = `pr`.`APPOINTMENT_ID`)) left join `payment_tbl` `py` on(`a`.`APPOINTMENT_ID` = `py`.`APPOINTMENT_ID`)) left join `feedback_tbl` `f` on(`a`.`APPOINTMENT_ID` = `f`.`APPOINTMENT_ID`)) left join `medicine_reminder_tbl` `mr` on(`p`.`PATIENT_ID` = `mr`.`PATIENT_ID`)) GROUP BY `p`.`PATIENT_ID` ORDER BY count(distinct `a`.`APPOINTMENT_ID`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_payment_report`
--
DROP TABLE IF EXISTS `view_payment_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_payment_report`  AS SELECT `py`.`PAYMENT_ID` AS `PAYMENT_ID`, `py`.`TRANSACTION_ID` AS `TRANSACTION_ID`, concat(`p`.`FIRST_NAME`,' ',`p`.`LAST_NAME`) AS `Patient_Name`, concat(`d`.`FIRST_NAME`,' ',`d`.`LAST_NAME`) AS `Doctor_Name`, `py`.`AMOUNT` AS `AMOUNT`, `py`.`PAYMENT_MODE` AS `PAYMENT_MODE`, `py`.`STATUS` AS `Payment_Status`, `py`.`PAYMENT_DATE` AS `PAYMENT_DATE`, dayname(`py`.`PAYMENT_DATE`) AS `Day_Name`, week(`py`.`PAYMENT_DATE`) AS `Week_Number`, month(`py`.`PAYMENT_DATE`) AS `Month_Number`, monthname(`py`.`PAYMENT_DATE`) AS `Month_Name`, year(`py`.`PAYMENT_DATE`) AS `Year` FROM (((`payment_tbl` `py` join `appointment_tbl` `a` on(`py`.`APPOINTMENT_ID` = `a`.`APPOINTMENT_ID`)) join `patient_tbl` `p` on(`a`.`PATIENT_ID` = `p`.`PATIENT_ID`)) join `doctor_tbl` `d` on(`a`.`DOCTOR_ID` = `d`.`DOCTOR_ID`)) ;

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
-- Indexes for table `notification_seen_tbl`
--
ALTER TABLE `notification_seen_tbl`
  ADD PRIMARY KEY (`SEEN_ID`),
  ADD UNIQUE KEY `uniq_user_notif` (`USER_TYPE`,`USER_ID`,`NOTIF_KEY`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `receptionist_notifications`
--
ALTER TABLE `receptionist_notifications`
  ADD PRIMARY KEY (`RECEPTIONIST_NOTIFICATION_ID`);

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
  MODIFY `APPOINTMENT_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  MODIFY `APPOINTMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  MODIFY `SCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  MODIFY `DOCTOR_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  MODIFY `FEEDBACK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  MODIFY `MEDICINE_REMINDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  MODIFY `MEDICINE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notification_seen_tbl`
--
ALTER TABLE `notification_seen_tbl`
  MODIFY `SEEN_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_tbl`
--
ALTER TABLE `patient_tbl`
  MODIFY `PATIENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `PAYMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  MODIFY `PRESCRIPTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `receptionist_notifications`
--
ALTER TABLE `receptionist_notifications`
  MODIFY `RECEPTIONIST_NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT;

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
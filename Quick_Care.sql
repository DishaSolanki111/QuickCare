-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2025 at 04:57 AM
-- Server version: 8.0.40
-- PHP Version: 8.2.12

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
  `APPOINTMENT_REMINDER_ID` int NOT NULL,
  `RECEPTIONIST_ID` int NOT NULL,
  `APPOINTMENT_ID` int NOT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_tbl`
--

CREATE TABLE `appointment_tbl` (
  `APPOINTMENT_ID` int NOT NULL,
  `PATIENT_ID` int NOT NULL,
  `DOCTOR_ID` int NOT NULL,
  `SCHEDULE_ID` int NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `APPOINTMENT_DATE` date DEFAULT NULL,
  `APPOINTMENT_TIME` time DEFAULT NULL,
  `STATUS` enum('SCHEDULED','COMPLETED','CANCELLED') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_tbl`
--

INSERT INTO `appointment_tbl` (`APPOINTMENT_ID`, `PATIENT_ID`, `DOCTOR_ID`, `SCHEDULE_ID`, `CREATED_AT`, `APPOINTMENT_DATE`, `APPOINTMENT_TIME`, `STATUS`) VALUES
(52, 12, 4, 7, '2025-12-25 06:51:39', '2025-12-29', '12:00:00', 'SCHEDULED'),
(53, 28, 4, 7, '2025-12-25 06:56:11', '2025-12-28', '11:00:00', 'SCHEDULED'),
(54, 12, 5, 10, '2025-12-25 07:02:18', '2025-12-31', '16:00:00', 'SCHEDULED'),
(55, 12, 5, 10, '2025-12-25 07:22:34', '2025-12-26', '09:00:00', 'SCHEDULED');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_tbl`
--

CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int NOT NULL,
  `DOCTOR_ID` int NOT NULL,
  `RECEPTIONIST_ID` int NOT NULL,
  `START_TIME` time DEFAULT NULL,
  `END_TIME` time DEFAULT NULL,
  `AVAILABLE_DAY` enum('MON','TUE','WED','THUR','FRI','SAT','SUN') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule_tbl`
--

INSERT INTO `doctor_schedule_tbl` (`SCHEDULE_ID`, `DOCTOR_ID`, `RECEPTIONIST_ID`, `START_TIME`, `END_TIME`, `AVAILABLE_DAY`) VALUES
(1, 1, 1, '09:00:00', '17:00:00', 'MON'),
(2, 1, 1, '09:00:00', '17:00:00', 'WED'),
(3, 1, 1, '09:00:00', '14:00:00', 'SAT'),
(4, 2, 2, '10:00:00', '16:00:00', 'TUE'),
(5, 2, 2, '10:00:00', '16:00:00', 'THUR'),
(6, 2, 2, '10:00:00', '16:00:00', 'FRI'),
(7, 4, 3, '08:00:00', '15:00:00', 'MON'),
(8, 4, 3, '08:00:00', '15:00:00', 'THUR'),
(9, 4, 3, '09:00:00', '13:00:00', 'SUN'),
(10, 5, 4, '09:00:00', '17:00:00', 'TUE'),
(11, 5, 4, '09:00:00', '17:00:00', 'WED'),
(12, 5, 4, '09:00:00', '17:00:00', 'FRI'),
(13, 7, 5, '11:00:00', '18:00:00', 'MON'),
(14, 7, 5, '11:00:00', '18:00:00', 'WED'),
(15, 7, 5, '11:00:00', '15:00:00', 'SAT'),
(16, 8, 6, '08:00:00', '14:00:00', 'TUE'),
(17, 8, 6, '08:00:00', '14:00:00', 'THUR'),
(18, 8, 6, '08:00:00', '14:00:00', 'FRI'),
(19, 9, 7, '10:00:00', '16:00:00', 'WED'),
(20, 9, 7, '10:00:00', '16:00:00', 'FRI'),
(21, 9, 7, '10:00:00', '16:00:00', 'SUN'),
(22, 10, 8, '09:00:00', '15:00:00', 'MON'),
(23, 10, 8, '09:00:00', '15:00:00', 'TUE'),
(24, 10, 8, '09:00:00', '15:00:00', 'SAT');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_tbl`
--

CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int NOT NULL,
  `SPECIALISATION_ID` int NOT NULL,
  `PROFILE_IMAGE` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FIRST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `USERNAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PSWD` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_tbl`
--

INSERT INTO `doctor_tbl` (`DOCTOR_ID`, `SPECIALISATION_ID`, `PROFILE_IMAGE`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `USERNAME`, `PSWD`, `PHONE`, `EMAIL`, `GENDER`) VALUES
(1, 1, 'doc_ellen_page.jpg', 'Ellen', 'Page', '1985-04-16', '2018-05-20', 'e.page', 'password123', 9876543210, 'e.page.pedia@hospital.com', 'FEMALE'),
(2, 1, 'doc_tom_holland.jpg', 'Tom', 'Holland', '1990-06-01', '2020-01-15', 't.holland', 'docpass1', 9876543211, 't.holland.pedia@hospital.com', 'MALE'),
(3, 1, 'doc_saoirse_ronan.jpg', 'Saoirse', 'Ronan', '1988-04-12', '2019-07-22', 's.ronan', 'pediatrician_pass', 9876543212, 's.ronan.pedia@hospital.com', 'FEMALE'),
(4, 2, 'doc_amar_kumar.jpg', 'Amar', 'Kumar', '1978-11-25', '2015-03-10', 'a.kumar', 'heartpass123', 9876543213, 'a.kumar.cardio@hospital.com', 'MALE'),
(5, 2, 'doc_priya_sharma.jpg', 'Priya', 'Sharma', '1982-02-18', '2017-09-01', 'p.sharma', 'cardio_pass', 9876543214, 'p.sharma.cardio@hospital.com', 'FEMALE'),
(6, 2, 'doc_rajiv_menon.jpg', 'Rajiv', 'Menon', '1975-09-30', '2016-11-11', 'r.menon', 'pass1234', 9876543215, 'r.menon.cardio@hospital.com', 'MALE'),
(7, 3, 'doc_david_miller.jpg', 'David', 'Miller', '1980-05-22', '2019-02-12', 'd.miller', 'bone_pass', 9876543216, 'd.miller.ortho@hospital.com', 'MALE'),
(8, 3, 'doc_sarah_jones.jpg', 'Sarah', 'Jones', '1988-10-05', '2021-06-30', 's.jones', 'ortho_secure', 9876543217, 's.jones.ortho@hospital.com', 'FEMALE'),
(9, 4, 'doc_anjali_reddy.jpg', 'Anjali', 'Reddy', '1984-07-19', '2018-08-05', 'a.reddy', 'neuro_pass', 9876543218, 'a.reddy.neuro@hospital.com', 'FEMALE'),
(10, 4, 'doc_michael_lee.jpg', 'Michael', 'Lee', '1979-12-01', '2017-04-25', 'm.lee', 'brainpass', 9876543219, 'm.lee.neuro@hospital.com', 'MALE'),
(13, 1, 'uploads/1766122658_Screenshot 2023-11-05 183043.png', 'Disha', 'Solanki', '1990-11-11', '2006-11-11', 'disha', '$2y$10$MobJ1w2tanQQTk.jeCplzO5T494qj8Hwv0tq8yqiOEX8dGepHWkBW', 1452369875, 'solankidisha009@gmail.com', 'FEMALE');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int NOT NULL,
  `APPOINTMENT_ID` int NOT NULL,
  `RATING` int DEFAULT NULL,
  `COMMENTS` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_reminder_tbl`
--

CREATE TABLE `medicine_reminder_tbl` (
  `MEDICINE_REMINDER_ID` int NOT NULL,
  `MEDICINE_ID` int NOT NULL,
  `CREATOR_ROLE` enum('PATIENT','RECEPTIONIST') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATOR_ID` int NOT NULL,
  `PATIENT_ID` int NOT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_reminder_tbl`
--

INSERT INTO `medicine_reminder_tbl` (`MEDICINE_REMINDER_ID`, `MEDICINE_ID`, `CREATOR_ROLE`, `CREATOR_ID`, `PATIENT_ID`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 'RECEPTIONIST', 1, 1, '08:00:00', 'Morning dose of Paracetamol'),
(2, 1, 'RECEPTIONIST', 1, 1, '20:00:00', 'Evening dose of Paracetamol'),
(3, 3, 'RECEPTIONIST', 2, 2, '00:00:00', 'Use inhaler as prescribed'),
(4, 1, 'RECEPTIONIST', 2, 2, '08:00:00', 'Morning dose of Paracetamol'),
(5, 1, 'RECEPTIONIST', 2, 2, '13:00:00', 'Afternoon dose of Paracetamol'),
(6, 1, 'RECEPTIONIST', 2, 2, '18:00:00', 'Evening dose of Paracetamol'),
(7, 4, 'RECEPTIONIST', 3, 3, '09:00:00', 'Daily Aspirin'),
(8, 5, 'RECEPTIONIST', 3, 3, '21:00:00', 'Night dose of Atorvastatin'),
(9, 6, 'RECEPTIONIST', 4, 4, '08:00:00', 'Morning dose of Metoprolol'),
(10, 6, 'RECEPTIONIST', 4, 4, '20:00:00', 'Evening dose of Metoprolol'),
(11, 4, 'RECEPTIONIST', 4, 4, '09:00:00', 'Daily Aspirin'),
(12, 7, 'RECEPTIONIST', 5, 5, '09:00:00', 'Morning dose of Ibuprofen'),
(13, 7, 'RECEPTIONIST', 5, 5, '14:00:00', 'Afternoon dose of Ibuprofen'),
(14, 7, 'RECEPTIONIST', 5, 5, '19:00:00', 'Evening dose of Ibuprofen'),
(15, 8, 'RECEPTIONIST', 5, 5, '10:00:00', 'Daily Calcium Supplement'),
(16, 7, 'RECEPTIONIST', 6, 6, '09:00:00', 'Morning dose of Ibuprofen'),
(17, 7, 'RECEPTIONIST', 6, 6, '14:00:00', 'Afternoon dose of Ibuprofen'),
(18, 7, 'RECEPTIONIST', 6, 6, '19:00:00', 'Evening dose of Ibuprofen'),
(19, 9, 'RECEPTIONIST', 6, 6, '10:00:00', 'Weekly Vitamin D'),
(20, 10, 'RECEPTIONIST', 7, 7, '09:00:00', 'Morning dose of Pregabalin'),
(21, 10, 'RECEPTIONIST', 7, 7, '21:00:00', 'Evening dose of Pregabalin'),
(22, 7, 'RECEPTIONIST', 7, 7, '15:00:00', 'Ibuprofen for pain as needed'),
(23, 10, 'RECEPTIONIST', 8, 8, '21:00:00', 'Night dose of Pregabalin'),
(24, 7, 'RECEPTIONIST', 8, 8, '00:00:00', 'Ibuprofen for acute migraine attack'),
(25, 6, 'RECEPTIONIST', 9, 9, '09:00:00', 'Daily dose of Metoprolol'),
(26, 4, 'RECEPTIONIST', 9, 9, '10:00:00', 'Daily Aspirin'),
(27, 2, 'RECEPTIONIST', 10, 10, '08:00:00', 'Morning dose of Antibiotic'),
(28, 2, 'RECEPTIONIST', 10, 10, '14:00:00', 'Afternoon dose of Antibiotic'),
(29, 2, 'RECEPTIONIST', 10, 10, '20:00:00', 'Evening dose of Antibiotic'),
(30, 1, 'RECEPTIONIST', 10, 10, '22:00:00', 'Paracetamol for fever as needed');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_tbl`
--

CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int NOT NULL,
  `RECEPTIONIST_ID` int NOT NULL,
  `MED_NAME` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DESCRIPTION` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_tbl`
--

INSERT INTO `medicine_tbl` (`MEDICINE_ID`, `RECEPTIONIST_ID`, `MED_NAME`, `DESCRIPTION`) VALUES
(1, 1, 'Paracetamol Syrup', 'For fever and pain relief in children.'),
(2, 1, 'Amoxicillin Suspension', 'Antibiotic for bacterial infections.'),
(3, 1, 'Salbutamol Inhaler', 'For asthma relief.'),
(4, 1, 'Aspirin', 'Blood thinner, prevents clots.'),
(5, 1, 'Atorvastatin', 'Lowers cholesterol levels.'),
(6, 1, 'Metoprolol', 'Beta-blocker for high blood pressure.'),
(7, 1, 'Clopidogrel', 'Antiplatelet agent.'),
(8, 1, 'Ibuprofen', 'NSAID for pain and inflammation.'),
(9, 1, 'Calcium Carbonate', 'Calcium supplement for bone health.'),
(10, 1, 'Vitamin D3', 'Supports calcium absorption.'),
(11, 1, 'Pregabalin', 'For neuropathic pain and seizures.'),
(12, 1, 'Levetiracetam', 'Anti-epileptic medication.'),
(13, 1, 'Ropinirole', 'For Parkinson\'s disease and Restless Legs Syndrome.');

-- --------------------------------------------------------

--
-- Table structure for table `patient_tbl`
--

CREATE TABLE `patient_tbl` (
  `PATIENT_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `USERNAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PSWD` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `BLOOD_GROUP` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ADDRESS` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tbl`
--

INSERT INTO `patient_tbl` (`PATIENT_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PSWD`, `DOB`, `GENDER`, `BLOOD_GROUP`, `PHONE`, `EMAIL`, `ADDRESS`) VALUES
(1, 'Rohan', 'Verma', 'r.verma', 'patientpass1', '2015-03-12', 'MALE', 'O+', 9811111111, 'rohan.v@email.com', '123, ABC Street, Delhi'),
(2, 'Ananya', 'Iyer', 'a.iyer', 'patientpass2', '2018-07-21', 'FEMALE', 'A+', 9811111112, 'ananya.i@email.com', '456, XYZ Road, Mumbai'),
(3, 'Sunil', 'Kapoor', 's.kapoor', 'patientpass3', '1965-01-30', 'MALE', 'B+', 9811111113, 's.kapoor@email.com', '789, DEF Lane, Bangalore'),
(4, 'Meera', 'Nair', 'm.nair', 'patientpass4', '1972-11-08', 'FEMALE', 'AB-', 9811111114, 'meera.n@email.com', '101, GHI Block, Chennai'),
(5, 'Karan', 'Joshi', 'k.joshi', 'patientpass5', '2001-05-25', 'MALE', 'O-', 9811111115, 'karan.j@email.com', '202, JKL Avenue, Pune'),
(6, 'Fatima', 'Sheikh', 'f.sheikh', 'patientpass6', '1995-09-15', 'FEMALE', 'A-', 9811111116, 'fatima.s@email.com', '303, MNO Street, Kolkata'),
(7, 'Amit', 'Patel', 'a.patel', 'patientpass7', '1988-04-02', 'MALE', 'B-', 9811111117, 'amit.p@email.com', '404, PQR Circle, Ahmedabad'),
(8, 'Priyanka', 'Das', 'p.das', 'patientpass8', '2003-12-20', 'FEMALE', 'AB+', 9811111118, 'priyanka.d@email.com', '505, STU Cross, Hyderabad'),
(9, 'Vikram', 'Singh', 'v.singh', 'patientpass9', '1978-06-18', 'MALE', 'A+', 9811111119, 'vikram.s@email.com', '606, VWX Park, Jaipur'),
(10, 'Leela', 'Gopalakrishnan', 'l.gopal', 'patientpass10', '1968-10-28', 'FEMALE', 'O+', 9811111120, 'leela.g@email.com', '707, YZA Road, Kochi'),
(11, 'happy', 'patel', 'happy', '$2y$10$ms9bh3y6IxGMQXYFoWe/9ep1OL59YnisEEK8G1vcXcm5CHwtZqlxK', '2006-05-05', 'FEMALE', 'O-', 1478963256, 'happy22@gmail.com', 'ahmedabad'),
(12, 'neha', 'chaudhary', 'neha12', '$2y$10$vJlL8.rRA1tOWGp2pqn/LOPi7K8J72RPW.bYKSQThv7dE8I.HC4nK', NULL, NULL, NULL, 1245653287, 'neha12@gmail.com', NULL),
(13, 'hiya', 'rao', 'hiyaa', '$2y$10$lR1hWjneZjTRaGz3dmjej.MyojlVnu.C/M1oS4d1q3Mu3icKokuCq', '2005-12-12', 'FEMALE', 'B+', 1478523698, 'hiyarao@gmail.com', 'ahmedabad'),
(14, 'celia', 'anthony', 'celia', '$2y$10$VJs6Ug9rCxU3OlONF2dkC.0lBOmI7vBQi7nBwzvwvOqKlOPuB0/nm', '1990-11-11', 'FEMALE', 'A-', 1478523654, 'celia12@gmail.com', 'ahmedabad'),
(15, 'jaydev', 'solanki', 'jaydev111', '$2y$10$GRTGMNiqKlU5S/W7eA9P3.qo0nIWtcUP6XpIlpss0dvGZpmE8MM5G', '2005-11-11', 'MALE', 'B+', 1236547896, 'jaydev123@gmail.com', 'ahmedabad'),
(17, 'jay', 'patel', 'jay123', '$2y$10$r1ULFcmDbw1wPvjJtN/o4OBZeLJJV193edI0vztYBqpOcI8/QE7Ia', '2006-12-11', 'MALE', 'B+', 2589631475, 'jay12@gmail.com', 'bapunagar'),
(20, 'marvin', 'rupera ', 'marvin123', '', '2006-12-11', 'MALE', 'A+', 2589631472, 'marvin12@gmail.com', 'ahmedabad'),
(22, 'hina', 'patel', 'hina12', '', '2005-12-11', 'FEMALE', 'A+', 1258743692, 'hina12@gmail.com', 'ahmedabad'),
(25, 'sneha', 'akbari', 'sneha12', '$2y$10$LBgFRxVGabYJuDHaNIKvU.vfXXMZuxH6PZzD6GZ0sl9vQf/G3CD02', '2005-05-05', 'FEMALE', 'AB+', 1254896354, 'sneha12@gmail.com', 'ahmedabad'),
(26, 'Henil', 'Parmar', 'henil12', '$2y$10$97UFBye6oK/eF/qyFatNSuvWz3/4YgTu5h0nsj7.K.m1lbgKX6MJW', '2005-12-06', 'MALE', 'AB+', 2546321897, 'henil303@gmail.com', 'ahmedabad'),
(28, 'heer', 'joshi', 'heer001', '$2y$10$ESa1H2y8xZ7cjtWffNhhe.tmwCtdW2dQ7RnZQbjF34DV52/JKgMcW', '2000-06-05', 'FEMALE', 'A-', 2546319111, 'heer12@gmail.com', 'ahmedabad');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `PAYMENT_ID` int NOT NULL,
  `APPOINTMENT_ID` int NOT NULL,
  `AMOUNT` decimal(10,2) DEFAULT NULL,
  `PAYMENT_DATE` date DEFAULT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `STATUS` enum('COMPLETED','FAILED') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TRANSACTION_ID` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicine_tbl`
--

CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `MEDICINE_ID` int NOT NULL,
  `DOSAGE` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DURATION` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FREQUENCY` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_tbl`
--

CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `APPOINTMENT_ID` int NOT NULL,
  `ISSUE_DATE` date DEFAULT NULL,
  `HEIGHT_CM` int DEFAULT NULL,
  `WEIGHT_KG` decimal(5,2) DEFAULT NULL,
  `BLOOD_PRESSURE` smallint DEFAULT NULL,
  `DIABETES` enum('NO','TYPE-1','TYPE-2','PRE-DIABTIC') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `SYMPTOMS` text COLLATE utf8mb4_general_ci,
  `DIAGNOSIS` text COLLATE utf8mb4_general_ci,
  `ADDITIONAL_NOTES` text COLLATE utf8mb4_general_ci,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_tbl`
--

CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ADDRESS` text COLLATE utf8mb4_general_ci,
  `USERNAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PSWD` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionist_tbl`
--

INSERT INTO `receptionist_tbl` (`RECEPTIONIST_ID`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `GENDER`, `PHONE`, `EMAIL`, `ADDRESS`, `USERNAME`, `PSWD`) VALUES
(1, 'John', 'Doe', '1992-03-15', '2022-01-10', 'MALE', 9911111111, 'john.doe@hospital.com', 'Near Main Gate', 'j.doe', 'recept_pass1'),
(2, 'Jane', 'Smith', '1994-07-22', '2022-02-18', 'FEMALE', 9911111112, 'jane.smith@hospital.com', 'Reception Block A', 'j.smith', 'recept_pass2'),
(3, 'Peter', 'Jones', '1990-11-30', '2021-05-20', 'MALE', 9911111113, 'peter.jones@hospital.com', 'Admin Office', 'p.jones', 'recept_pass3'),
(4, 'Mary', 'Williams', '1991-02-08', '2021-09-01', 'FEMALE', 9911111114, 'mary.w@hospital.com', 'Front Desk', 'm.williams', 'recept_pass4'),
(5, 'David', 'Brown', '1988-05-25', '2020-03-12', 'MALE', 9911111115, 'david.b@hospital.com', 'Patient Services', 'd.brown', 'recept_pass5'),
(6, 'Susan', 'Davis', '1993-09-14', '2023-01-05', 'FEMALE', 9911111116, 'susan.d@hospital.com', 'Billing Counter', 's.davis', 'recept_pass6'),
(7, 'Chris', 'Miller', '1989-12-01', '2022-06-30', 'MALE', 9911111117, 'chris.m@hospital.com', 'Wing B Reception', 'c.miller', 'recept_pass7'),
(8, 'Lisa', 'Wilson', '1992-04-18', '2023-02-15', 'FEMALE', 9911111118, 'lisa.w@hospital.com', 'Emergency Desk', 'l.wilson', 'recept_pass8'),
(9, 'Kevin', 'Moore', '1991-08-21', '2022-11-11', 'MALE', 9911111119, 'kevin.m@hospital.com', 'OPD Reception', 'k.moore', 'recept_pass9'),
(10, 'Nancy', 'Taylor', '1990-06-10', '2021-12-20', 'FEMALE', 9911111120, 'nancy.t@hospital.com', 'Main Lobby', 'n.taylor', 'recept_pass10');

-- --------------------------------------------------------

--
-- Table structure for table `specialisation_tbl`
--

CREATE TABLE `specialisation_tbl` (
  `SPECIALISATION_ID` int NOT NULL,
  `SPECIALISATION_NAME` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
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
  ADD UNIQUE KEY `PHONE` (`PHONE`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
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
  ADD UNIQUE KEY `USERNAME` (`USERNAME`),
  ADD UNIQUE KEY `PHONE` (`PHONE`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

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
  ADD UNIQUE KEY `PHONE` (`PHONE`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
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
  MODIFY `APPOINTMENT_REMINDER_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  MODIFY `APPOINTMENT_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  MODIFY `SCHEDULE_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  MODIFY `DOCTOR_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  MODIFY `FEEDBACK_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  MODIFY `MEDICINE_REMINDER_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  MODIFY `MEDICINE_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `patient_tbl`
--
ALTER TABLE `patient_tbl`
  MODIFY `PATIENT_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `PAYMENT_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  MODIFY `PRESCRIPTION_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `receptionist_tbl`
--
ALTER TABLE `receptionist_tbl`
  MODIFY `RECEPTIONIST_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `specialisation_tbl`
--
ALTER TABLE `specialisation_tbl`
  MODIFY `SPECIALISATION_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_reminder_tbl`
--
ALTER TABLE `appointment_reminder_tbl`
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`),
  ADD CONSTRAINT `appointment_reminder_tbl_ibfk_2` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`);

--
-- Constraints for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  ADD CONSTRAINT `appointment_tbl_ibfk_1` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`),
  ADD CONSTRAINT `appointment_tbl_ibfk_2` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`),
  ADD CONSTRAINT `appointment_tbl_ibfk_3` FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `doctor_schedule_tbl` (`SCHEDULE_ID`);

--
-- Constraints for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_1` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`),
  ADD CONSTRAINT `doctor_schedule_tbl_ibfk_2` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`);

--
-- Constraints for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  ADD CONSTRAINT `doctor_tbl_ibfk_1` FOREIGN KEY (`SPECIALISATION_ID`) REFERENCES `specialisation_tbl` (`SPECIALISATION_ID`);

--
-- Constraints for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  ADD CONSTRAINT `feedback_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`);

--
-- Constraints for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_1` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`),
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_2` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`);

--
-- Constraints for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  ADD CONSTRAINT `medicine_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`);

--
-- Constraints for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`);

--
-- Constraints for table `prescription_medicine_tbl`
--
ALTER TABLE `prescription_medicine_tbl`
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_1` FOREIGN KEY (`PRESCRIPTION_ID`) REFERENCES `prescription_tbl` (`PRESCRIPTION_ID`),
  ADD CONSTRAINT `prescription_medicine_tbl_ibfk_2` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`);

--
-- Constraints for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  ADD CONSTRAINT `prescription_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



-- Add the education field to the doctor_tbl table
ALTER TABLE `doctor_tbl` ADD COLUMN `EDUCATION` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `GENDER`;

-- Update the education data for each doctor based on their specialization
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Pediatrics)' WHERE `DOCTOR_ID` = 1;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, DCH (Diploma in Child Health)' WHERE `DOCTOR_ID` = 2;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, DNB (Pediatrics)' WHERE `DOCTOR_ID` = 3;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Cardiology), DM (Cardiology)' WHERE `DOCTOR_ID` = 4;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Medicine), DM (Cardiology)' WHERE `DOCTOR_ID` = 5;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Cardiology)' WHERE `DOCTOR_ID` = 6;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MS (Orthopedics), MCh (Orthopedics)' WHERE `DOCTOR_ID` = 7;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, DNB (Orthopedics)' WHERE `DOCTOR_ID` = 8;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Medicine), DM (Neurology)' WHERE `DOCTOR_ID` = 9;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Neurology)' WHERE `DOCTOR_ID` = 10;
UPDATE `doctor_tbl` SET `EDUCATION` = 'MBBS, MD (Pediatrics), Fellowship in Neonatology' WHERE `DOCTOR_ID` = 13;
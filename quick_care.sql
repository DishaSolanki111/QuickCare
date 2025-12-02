-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 03:18 PM
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
  `RECEPTIONIST_ID` int DEFAULT NULL,
  `APPOINTMENT_ID` int DEFAULT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_tbl`
--

CREATE TABLE `appointment_tbl` (
  `APPOINTMENT_ID` int NOT NULL,
  `PATIENT_ID` int DEFAULT NULL,
  `DOCTOR_ID` int DEFAULT NULL,
  `SCHEDULE_ID` int DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `APPOINTMENT_DATE` date NOT NULL,
  `APPOINTMENT_TIME` time NOT NULL,
  `status` enum('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_tbl`
--

CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int NOT NULL,
  `DOCTOR_ID` int DEFAULT NULL,
  `RECEPTIONIST_ID` int DEFAULT NULL,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `AVAILABLE` enum('MON','TUE','WED','THUR','FRI','SAT','SUN') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_tbl`
--

CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int NOT NULL,
  `SPECIALISATION_ID` int DEFAULT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `USERNAME` varchar(20) DEFAULT NULL,
  `PASSWORD` varchar(60) NOT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(30) DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int NOT NULL,
  `APPOINTMENT_ID` int DEFAULT NULL,
  `RATING` int DEFAULT NULL,
  `COMMENTS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_reminder_tbl`
--

CREATE TABLE `medicine_reminder_tbl` (
  `MEDICINE_REMINDER_ID` int NOT NULL,
  `MEDICINE_ID` int DEFAULT NULL,
  `CREATED_BY` enum('PATIENT','RECEPTIONIST') DEFAULT NULL,
  `PATIENT_ID` int DEFAULT NULL,
  `REMINDER_TIME` time NOT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_tbl`
--

CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int NOT NULL,
  `RECEPTIONIST_ID` int DEFAULT NULL,
  `MED_NAME` varchar(25) NOT NULL,
  `DESCRIPTION` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_tbl`
--

CREATE TABLE `patient_tbl` (
  `P_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `USERNAME` varchar(20) DEFAULT NULL,
  `PSW` varchar(60) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(30) DEFAULT NULL,
  `ADDRESS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patient_tbl`
--

INSERT INTO `patient_tbl` (`P_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PSW`, `DOB`, `GENDER`, `PHONE`, `EMAIL`, `ADDRESS`) VALUES
(1, 'disha', 'solanki', 'disha11', '$2y$10$9n7mbUHswUnSiz2QhPqM3eDR0/JcXAcEXJR0XVGStTXG9x7GyGT9K', '2006-07-17', 'FEMALE', 9725180685, 'solankidisha009@gmail.com', 'bapunagar,ahmedabad');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `PAYMENT_ID` int NOT NULL,
  `APPOINTMENT_ID` int DEFAULT NULL,
  `AMOUNT` decimal(10,2) NOT NULL,
  `PAYMENT_DATE` date NOT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') DEFAULT NULL,
  `STATUS` enum('PENDING','COMPLETED','FAILED') DEFAULT 'PENDING',
  `TRANSACTION_ID` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicine_tbl`
--

CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `MEDICINE_ID` int NOT NULL,
  `DOSAGE` int DEFAULT NULL,
  `FREQUENCY` int DEFAULT NULL,
  `DURATION` varchar(50) DEFAULT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_tbl`
--

CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `APPOINTMENT_ID` int DEFAULT NULL,
  `ISSUE_DATE` date NOT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_tbl`
--

CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `ADDRESS` text,
  `USERNAME` varchar(50) DEFAULT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `specialisation_tbl`
--

CREATE TABLE `specialisation_tbl` (
  `SPECIALISATION_ID` int NOT NULL,
  `SPECIALISATION_NAME` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  ADD PRIMARY KEY (`P_ID`),
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
  MODIFY `APPOINTMENT_REMINDER_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_tbl`
--
ALTER TABLE `appointment_tbl`
  MODIFY `APPOINTMENT_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_schedule_tbl`
--
ALTER TABLE `doctor_schedule_tbl`
  MODIFY `SCHEDULE_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_tbl`
--
ALTER TABLE `doctor_tbl`
  MODIFY `DOCTOR_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  MODIFY `FEEDBACK_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_reminder_tbl`
--
ALTER TABLE `medicine_reminder_tbl`
  MODIFY `MEDICINE_REMINDER_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_tbl`
--
ALTER TABLE `medicine_tbl`
  MODIFY `MEDICINE_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_tbl`
--
ALTER TABLE `patient_tbl`
  MODIFY `P_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `PAYMENT_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_tbl`
--
ALTER TABLE `prescription_tbl`
  MODIFY `PRESCRIPTION_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receptionist_tbl`
--
ALTER TABLE `receptionist_tbl`
  MODIFY `RECEPTIONIST_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialisation_tbl`
--
ALTER TABLE `specialisation_tbl`
  MODIFY `SPECIALISATION_ID` int NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `appointment_tbl_ibfk_1` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`P_ID`),
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
  ADD CONSTRAINT `medicine_reminder_tbl_ibfk_2` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`P_ID`);
  

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

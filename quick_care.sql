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
  `RECEPTIONIST_ID` int  ,
  `APPOINTMENT_ID` int  ,
  `REMINDER_TIME` time  ,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_tbl`
--

CREATE TABLE `appointment_tbl` (
  `APPOINTMENT_ID` int NOT NULL,
  `PATIENT_ID` int  ,
  `DOCTOR_ID` int  ,
  `SCHEDULE_ID` int  ,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `APPOINTMENT_DATE` date NOT NULL,
  `APPOINTMENT_TIME` time NOT NULL,
  `status` enum('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_tbl`
--

CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int NOT NULL,
  `DOCTOR_ID` int  ,
  `RECEPTIONIST_ID` int  ,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `AVAILABLE` enum('MON','TUE','WED','THUR','FRI','SAT','SUN')  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_tbl`
--

CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int NOT NULL,
  `SPECIALISATION_ID` int  ,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date  ,
  `DOJ` date  ,
  `USERNAME` varchar(20)  ,
  `PASSWORD` varchar(60) NOT NULL,
  `PHONE` bigint  ,
  `EMAIL` varchar(30)  ,
  `GENDER` enum('MALE','FEMALE','OTHER')  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int NOT NULL,
  `APPOINTMENT_ID` int  ,
  `RATING` int  ,
  `COMMENTS` varchar(255)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_reminder_tbl`
--

CREATE TABLE `medicine_reminder_tbl` (
  `MEDICINE_REMINDER_ID` int NOT NULL,
  `MEDICINE_ID` int  ,
  `CREATOR_ROLE` ENUM('PATIENT','RECEPTIONIST') NOT NULL,
  `CREATOR_ID` INT NOT NULL,
  `PATIENT_ID` int  ,
  `REMINDER_TIME` time NOT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_tbl`
--

CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int NOT NULL,
  `RECEPTIONIST_ID` int  ,
  `MED_NAME` varchar(25) NOT NULL,
  `DESCRIPTION` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_tbl`
--

CREATE TABLE `patient_tbl` (
  `P_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `USERNAME` varchar(20)  ,
  `PSW` varchar(60)  ,
  `DOB` date  ,
  `GENDER` enum('MALE','FEMALE','OTHER')  ,
  `PHONE` bigint  ,
  `EMAIL` varchar(30)  ,
  `ADDRESS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `APPOINTMENT_ID` int  ,
  `AMOUNT` decimal(10,2) NOT NULL,
  `PAYMENT_DATE` date NOT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING')  ,
  `STATUS` enum('PENDING','COMPLETED','FAILED') DEFAULT 'PENDING',
  `TRANSACTION_ID` varchar(100)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicine_tbl`
--

CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `MEDICINE_ID` int NOT NULL,
  `DOSAGE` int  ,
  `FREQUENCY` int  ,
  `DURATION` varchar(50)  ,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_tbl`
--

CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `APPOINTMENT_ID` int  ,
  `ISSUE_DATE` date NOT NULL,
  `REMARKS` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionist_tbl`
--

CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int NOT NULL,
  `FIRST_NAME` varchar(20) NOT NULL,
  `LAST_NAME` varchar(20) NOT NULL,
  `DOB` date  ,
  `DOJ` date  ,
  `GENDER` enum('MALE','FEMALE','OTHER')  ,
  `PHONE` bigint  ,
  `EMAIL` varchar(100)  ,
  `ADDRESS` text,
  `USERNAME` varchar(50)  ,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `specialisation_tbl`
--

CREATE TABLE `specialisation_tbl` (
  `SPECIALISATION_ID` int NOT NULL,
  `SPECIALISATION_NAME` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO specialisation_tbl (SPECIALISATION_ID, SPECIALISATION_NAME) VALUES
(1, 'Pediatrician'),
(2, 'Cardiologist'),
(3, 'Neurologist'),
(4, 'Orthopedic');

INSERT INTO patient_tbl 
(P_ID, FIRST_NAME, LAST_NAME, USERNAME, PSW, DOB, GENDER, BLOOD_GROUP, DIABETES, PHONE, EMAIL, ADDRESS) 
VALUES
(1, 'Rohan', 'Shah', 'rohan123', 'pass123', '2000-05-12', 'MALE', 'A+', 'TYPE-1', 9876543210, 'rohan@gmail.com', 'Ahmedabad'),
(2, 'Priya', 'Mehta', 'priya001', 'priya@123', '1999-11-22', 'FEMALE', 'B+', 'NONE', 9898989898, 'priya@gmail.com', 'Surat'),
(3, 'Amit', 'Patel', 'amitp', 'amit321', '1988-04-10', 'MALE', 'O+', 'TYPE-2', 9123456780, 'amit@gmail.com', 'Vadodara');

INSERT INTO doctor_tbl 
(DOCTOR_ID, SPECIALISATION_ID, FIRST_NAME, LAST_NAME, DOB, USERNAME, PASSWORD, PHONE, EMAIL, GENDER) 
VALUES
(1, 1, 'Bhavesh', 'Trivedi', '1980-02-14', 'drbhavesh', 'dr123', 9887766554, 'bhavesh@clinic.com', 'MALE'),
(2, 2, 'Kiran', 'Sharma', '1975-08-20', 'drkiran', 'kiran@123', 9876501234, 'kiran@clinic.com', 'FEMALE'),
(3, 3, 'Sneha', 'Gupta', '1982-10-10', 'drsneha', 'sneha987', 9012345678, 'sneha@clinic.com', 'FEMALE'),
(4, 4, 'Harish', 'Rathod', '1978-03-05', 'drharish', 'harish12', 9090909090, 'harish@clinic.com', 'MALE');

INSERT INTO receptionist_tbl 
(RECEPTIONIST_ID, FIRST_NAME, LAST_NAME, DOB, PHONE, EMAIL, USERNAME, PASSWORD, GENDER, ADDRESS)
VALUES
(1, 'Neha', 'Chauhan', '1995-01-12', 9876512345, 'neha@clinic.com', 'neha_rcp', 'neha123', 'FEMALE', 'Ahmedabad'),
(2, 'Raj', 'Vyas', '1993-07-15', 9988776655, 'raj@clinic.com', 'raj_rcp', 'raj@123', 'MALE', 'Surat');

INSERT INTO doctor_schedule_tbl 
(SCHEDULE_ID, DOCTOR_ID, RECEPTIONIST_ID, START_TIME, END_TIME, AVAILABLE_DAY)
VALUES
(1, 1, 1, '10:00:00', '13:00:00', 'MON'),
(2, 1, 1, '17:00:00', '20:00:00', 'WED'),
(3, 2, 2, '09:00:00', '12:00:00', 'TUE'),
(4, 3, 1, '14:00:00', '18:00:00', 'THU'),
(5, 4, 2, '11:00:00', '15:00:00', 'FRI');

INSERT INTO appointment_tbl 
(APPOINTMENT_ID, PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
(1, 1, 1, 1, NOW(), '2025-01-05', '10:30:00', 'SCHEDULED'),
(2, 2, 2, 3, NOW(), '2025-01-06', '09:30:00', 'COMPLETED'),
(3, 3, 3, 4, NOW(), '2025-01-07', '14:15:00', 'CANCELLED');

INSERT INTO appointment_reminder_tbl 
(APPOINTMENT_REMINDER_ID, RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS)
VALUES
(1, 1, 1, '09:30:00', 'Call patient for confirmation'),
(2, 2, 2, '08:30:00', 'Send SMS reminder');

INSERT INTO prescription_tbl 
(PRESCRIPTION_ID, APPOINTMENT_ID, ISSUE_DATE, HEIGHT(CM), WEIGHT(KG), BLOOD_PRESSURE, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES, CREATED_AT)
VALUES
(1, 2, '2025-01-06', 160, 55.5, '120/80', 'Chest pain', 'Minor Cardiac Issue', 'Continue exercise', NOW());

INSERT INTO medicine_tbl 
(MEDICINE_ID, RECEPTIONIST_ID, MED_NAME, MED_DOSE, DESCRIPTION)
VALUES
(1, 1, 'Paracetamol', '500mg', 'Pain Relief'),
(2, 1, 'Atorvastatin', '10mg', 'Cholesterol Control'),
(3, 2, 'Neurolox', '5mg', 'Neural Pain Relief');

INSERT INTO prescription_medicine_tbl 
(PRESCRIPTION_MEDICINE_ID, PRESCRIPTION_ID, MEDICINE_ID, DOSAGE, DURATION, CREATED_AT)
VALUES
(1, 1, 1, '1 tablet', '5 Days', NOW()),
(2, 1, 2, '1 tablet', '10 Days', NOW());

INSERT INTO medicine_reminder_tbl 
(MEDICINE_REMINDER_ID, PRESCRIPTION_ID, PATIENT_ID, REMINDER_TIME, REMARKS)
VALUES
(1, 1, 1, '09:00:00', 'Morning dose'),
(2, 1, 1, '21:00:00', 'Night dose');

INSERT INTO feedback_tbl 
(FEEDBACK_ID, APPOINTMENT_ID, RATING, COMMENTS)
VALUES
(1, 2, 5, 'Very good service'),
(2, 1, 4, 'Doctor was helpful');

INSERT INTO payment_tbl 
(PAYMENT_ID, APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID)
VALUES
(1, 2, 500.00, '2025-01-06', 'UPI', 'COMPLETED', 'TXN12345'),
(2, 1, 300.00, '2025-01-05', 'CREDIT CARD', 'COMPLETED', 'TXN98765');



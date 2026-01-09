-- #####################################################################
-- # Hospital Management System Database Schema and Data Insertion
-- #####################################################################

-- ---------------------------------------------------------------------
-- -- Section 1: Drop Existing Tables (in reverse order of dependency)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `medicine_reminder_tbl`;
DROP TABLE IF EXISTS `prescription_medicine_tbl`;
DROP TABLE IF EXISTS `feedback_tbl`;
DROP TABLE IF EXISTS `payment_tbl`;
DROP TABLE IF EXISTS `appointment_reminder_tbl`;
DROP TABLE IF EXISTS `prescription_tbl`;
DROP TABLE IF EXISTS `appointment_tbl`;
DROP TABLE IF EXISTS `doctor_schedule_tbl`;
DROP TABLE IF EXISTS `specialization_tbl`;
DROP TABLE IF EXISTS `doctor_tbl`;
DROP TABLE IF EXISTS `receptionist_tbl`;
DROP TABLE IF EXISTS `medicine_tbl`;
DROP TABLE IF EXISTS `patient_tbl`;


-- ---------------------------------------------------------------------
-- -- Section 2: Create Tables with Foreign Keys
-- ---------------------------------------------------------------------

-- Table for storing doctor specializations
CREATE TABLE `specialization_tbl` (
  `SPECIALISATION_ID` int NOT NULL AUTO_INCREMENT,
  `SPECIALISATION_NAME` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`SPECIALISATION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing patient information
CREATE TABLE `patient_tbl` (
  `PATIENT_ID` int NOT NULL AUTO_INCREMENT,
  `FIRST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `USERNAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PSWD` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `BLOOD_GROUP` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ADDRESS` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`PATIENT_ID`),
  UNIQUE KEY `USERNAME` (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing receptionist information
CREATE TABLE `receptionist_tbl` (
  `RECEPTIONIST_ID` int NOT NULL AUTO_INCREMENT,
  `FIRST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `DOJ` date DEFAULT NULL,
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PHONE` bigint DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ADDRESS` text COLLATE utf8mb4_general_ci,
  `USERNAME` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PSWD` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`RECEPTIONIST_ID`),
  UNIQUE KEY `USERNAME` (`USERNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing medicines
CREATE TABLE `medicine_tbl` (
  `MEDICINE_ID` int NOT NULL AUTO_INCREMENT,
  `RECEPTIONIST_ID` int NOT NULL,
  `MED_NAME` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DESCRIPTION` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`MEDICINE_ID`),
  KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`),
  CONSTRAINT `medicine_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing doctor information
CREATE TABLE `doctor_tbl` (
  `DOCTOR_ID` int NOT NULL AUTO_INCREMENT,
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
  `GENDER` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `EDUCATION` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`DOCTOR_ID`),
  UNIQUE KEY `USERNAME` (`USERNAME`),
  KEY `SPECIALISATION_ID` (`SPECIALISATION_ID`),
  CONSTRAINT `doctor_tbl_ibfk_1` FOREIGN KEY (`SPECIALISATION_ID`) REFERENCES `specialization_tbl` (`SPECIALISATION_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing doctor schedules
CREATE TABLE `doctor_schedule_tbl` (
  `SCHEDULE_ID` int NOT NULL AUTO_INCREMENT,
  `DOCTOR_ID` int NOT NULL,
  `RECEPTIONIST_ID` int NOT NULL,
  `START_TIME` time DEFAULT NULL,
  `END_TIME` time DEFAULT NULL,
  `AVAILABLE_DAY` enum('MON','TUE','WED','THUR','FRI','SAT','SUN') COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`SCHEDULE_ID`),
  KEY `DOCTOR_ID` (`DOCTOR_ID`),
  KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`),
  CONSTRAINT `doctor_schedule_tbl_ibfk_1` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doctor_schedule_tbl_ibfk_2` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing appointments
CREATE TABLE `appointment_tbl` (
  `APPOINTMENT_ID` int NOT NULL AUTO_INCREMENT,
  `PATIENT_ID` int NOT NULL,
  `DOCTOR_ID` int NOT NULL,
  `SCHEDULE_ID` int NOT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `APPOINTMENT_DATE` date DEFAULT NULL,
  `APPOINTMENT_TIME` time DEFAULT NULL,
  `STATUS` enum('SCHEDULED','COMPLETED','CANCELLED') COLLATE utf8mb4_general_ci DEFAULT 'SCHEDULED',
  PRIMARY KEY (`APPOINTMENT_ID`),
  KEY `PATIENT_ID` (`PATIENT_ID`),
  KEY `DOCTOR_ID` (`DOCTOR_ID`),
  KEY `SCHEDULE_ID` (`SCHEDULE_ID`),
  CONSTRAINT `appointment_tbl_ibfk_1` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `appointment_tbl_ibfk_2` FOREIGN KEY (`DOCTOR_ID`) REFERENCES `doctor_tbl` (`DOCTOR_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `appointment_tbl_ibfk_3` FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `doctor_schedule_tbl` (`SCHEDULE_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing prescriptions
CREATE TABLE `prescription_tbl` (
  `PRESCRIPTION_ID` int NOT NULL AUTO_INCREMENT,
  `APPOINTMENT_ID` int NOT NULL,
  `ISSUE_DATE` date DEFAULT NULL,
  `HEIGHT_CM` int DEFAULT NULL,
  `WEIGHT_KG` decimal(5,2) DEFAULT NULL,
  `BLOOD_PRESSURE` smallint DEFAULT NULL,
  `DIABETES` enum('NO','TYPE-1','TYPE-2','PRE-DIABTIC') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `SYMPTOMS` text COLLATE utf8mb4_general_ci,
  `DIAGNOSIS` text COLLATE utf8mb4_general_ci,
  `ADDITIONAL_NOTES` text COLLATE utf8mb4_general_ci,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PRESCRIPTION_ID`),
  KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`),
  CONSTRAINT `prescription_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing payment information
CREATE TABLE `payment_tbl` (
  `PAYMENT_ID` int NOT NULL AUTO_INCREMENT,
  `APPOINTMENT_ID` int NOT NULL,
  `AMOUNT` decimal(10,2) DEFAULT NULL,
  `PAYMENT_DATE` date DEFAULT NULL,
  `PAYMENT_MODE` enum('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `STATUS` enum('COMPLETED','FAILED') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TRANSACTION_ID` varchar(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`PAYMENT_ID`),
  UNIQUE KEY `TRANSACTION_ID` (`TRANSACTION_ID`),
  KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`),
  CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing patient feedback
CREATE TABLE `feedback_tbl` (
  `FEEDBACK_ID` int NOT NULL AUTO_INCREMENT,
  `APPOINTMENT_ID` int NOT NULL,
  `RATING` int DEFAULT NULL,
  `COMMENTS` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`FEEDBACK_ID`),
  KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`),
  CONSTRAINT `feedback_tbl_ibfk_1` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing appointment reminders
CREATE TABLE `appointment_reminder_tbl` (
  `APPOINTMENT_REMINDER_ID` int NOT NULL AUTO_INCREMENT,
  `RECEPTIONIST_ID` int NOT NULL,
  `APPOINTMENT_ID` int NOT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`APPOINTMENT_REMINDER_ID`),
  KEY `RECEPTIONIST_ID` (`RECEPTIONIST_ID`),
  KEY `APPOINTMENT_ID` (`APPOINTMENT_ID`),
  CONSTRAINT `appointment_reminder_tbl_ibfk_1` FOREIGN KEY (`RECEPTIONIST_ID`) REFERENCES `receptionist_tbl` (`RECEPTIONIST_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `appointment_reminder_tbl_ibfk_2` FOREIGN KEY (`APPOINTMENT_ID`) REFERENCES `appointment_tbl` (`APPOINTMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Junction table for prescriptions and medicines
CREATE TABLE `prescription_medicine_tbl` (
  `PRESCRIPTION_ID` int NOT NULL,
  `MEDICINE_ID` int NOT NULL,
  `DOSAGE` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DURATION` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FREQUENCY` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PRESCRIPTION_ID`,`MEDICINE_ID`),
  KEY `MEDICINE_ID` (`MEDICINE_ID`),
  CONSTRAINT `prescription_medicine_tbl_ibfk_1` FOREIGN KEY (`PRESCRIPTION_ID`) REFERENCES `prescription_tbl` (`PRESCRIPTION_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prescription_medicine_tbl_ibfk_2` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for storing medicine reminders
CREATE TABLE `medicine_reminder_tbl` (
  `MEDICINE_REMINDER_ID` int NOT NULL AUTO_INCREMENT,
  `MEDICINE_ID` int NOT NULL,
  `CREATOR_ROLE` enum('PATIENT','RECEPTIONIST') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATOR_ID` int NOT NULL,
  `PATIENT_ID` int NOT NULL,
  `REMINDER_TIME` time DEFAULT NULL,
  `REMARKS` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`MEDICINE_REMINDER_ID`),
  KEY `MEDICINE_ID` (`MEDICINE_ID`),
  KEY `PATIENT_ID` (`PATIENT_ID`),
  CONSTRAINT `medicine_reminder_tbl_ibfk_1` FOREIGN KEY (`MEDICINE_ID`) REFERENCES `medicine_tbl` (`MEDICINE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `medicine_reminder_tbl_ibfk_2` FOREIGN KEY (`PATIENT_ID`) REFERENCES `patient_tbl` (`PATIENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE
  -- NOTE: CREATOR_ID does not have a direct foreign key as it can refer to either patient_tbl or receptionist_tbl
  -- based on the CREATOR_ROLE. This logic should be handled at the application level.
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ---------------------------------------------------------------------
-- -- Section 3: Insert Data into Tables (in order of dependency)
-- ---------------------------------------------------------------------

-- Insert specializations
  INSERT INTO `specialization_tbl` (`SPECIALISATION_ID`, `SPECIALISATION_NAME`) VALUES
  (1, 'Pediatrician'),
  (2, 'Cardiologist'),
  (3, 'Orthopedics'),
  (4, 'Neurologist');

-- Insert receptionists
INSERT INTO `receptionist_tbl` (`RECEPTIONIST_ID`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `GENDER`, `PHONE`, `EMAIL`, `ADDRESS`, `USERNAME`, `PSWD`) VALUES
(1, 'Meena', 'Kumari', '1985-03-15', '2018-06-20', 'FEMALE', 9876543301, 'meena.k@gmail.com', '123, Staff Quarters, Mumbai', 'meena.k', '$2y$10$X3EekceK1ma1ZkXCBoZASOn3xgx7iaHC3Js8ac1hilRUv.RSWD1yq'),
(2, 'Ramesh', 'Kumar', '1987-07-22', '2019-09-15', 'MALE', 9876543302, 'ramesh.k@gmail.com', '456, Staff Quarters, Delhi', 'ramesh.k', '$2y$10$X3EekceK1ma1ZkXCBoZASOn3xgx7iaHC3Js8ac1hilRUv.RSWD1yq'),
(3, 'Sunita', 'Devi', '1990-11-10', '2020-03-25', 'FEMALE', 9876543303, 'sunita.d@gmail.com', '789, Staff Quarters, Bangalore', 'sunita.d', '$2y$10$X3EekceK1ma1ZkXCBoZASOn3xgx7iaHC3Js8ac1hilRUv.RSWD1yq'),
(4, 'Anil', 'Sharma', '1988-05-18', '2021-07-10', 'MALE', 9876543304, 'anil.s@gmail.com', '321, Staff Quarters, Pune', 'anil.s', '$2y$10$X3EekceK1ma1ZkXCBoZASOn3xgx7iaHC3Js8ac1hilRUv.RSWD1yq');

-- Insert patients
INSERT INTO `patient_tbl` (`PATIENT_ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PSWD`, `DOB`, `GENDER`, `BLOOD_GROUP`, `PHONE`, `EMAIL`, `ADDRESS`) VALUES
(1, 'Arjun', 'Mishra', 'arjun.m', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1990-05-15', 'MALE', 'B+', 9876543201, 'arjun.mishra@gmail.com', '123, Park Street, Mumbai'),
(2, 'Pooja', 'Sharma', 'pooja.s', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1985-08-22', 'FEMALE', 'A+', 9876543202, 'pooja.sharma@gmail.com', '456, MG Road, Delhi'),
(3, 'Rohan', 'Patel', 'rohan.p', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1992-12-10', 'MALE', 'O+', 9876543203, 'rohan.patel@gmail.com', '789, Brigade Road, Bangalore'),
(4, 'Neha', 'Gupta', 'neha.g', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1988-03-18', 'FEMALE', 'AB+', 9876543204, 'neha.gupta@gmail.com', '321, FC Road, Pune'),
(5, 'Amit', 'Kumar', 'amit.k', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1987-07-25', 'MALE', 'A-', 9876543205, 'amit.kumar@gmail.com', '654, Jubilee Hills, Hyderabad'),
(6, 'Sneha', 'Reddy', 'sneha.r', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1991-11-30', 'FEMALE', 'B-', 9876543206, 'sneha.reddy@gmail.com', '987, Banjara Hills, Hyderabad'),
(7, 'Vikas', 'Singh', 'vikas.s', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1986-09-14', 'MALE', 'O-', 9876543207, 'vikas.singh@gmail.com', '147, Connaught Place, Delhi'),
(8, 'Anjali', 'Desai', 'anjali.d', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1993-02-05', 'FEMALE', 'AB-', 9876543208, 'anjali.desai@gmail.com', '258, Marine Drive, Mumbai'),
(9, 'Rahul', 'Verma', 'rahul.v', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1989-06-20', 'MALE', 'A+', 9876543209, 'rahul.verma@gmail.com', '369, Park Street, Kolkata'),
(10, 'Kavita', 'Sharma', 'kavita.s', '$2y$10$MSqRXoWU0J7QbPphr8wm0.8qL6adBgW7UumQkj7QOXpeDMKJcqlea', '1994-04-12', 'FEMALE', 'B+', 9876543210, 'kavita.sharma@gmail.com', '741, MG Road, Bangalore');

-- Insert medicines
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

-- Insert doctors
INSERT INTO `doctor_tbl` (`DOCTOR_ID`, `SPECIALISATION_ID`, `PROFILE_IMAGE`, `FIRST_NAME`, `LAST_NAME`, `DOB`, `DOJ`, `USERNAME`, `PSWD`, `PHONE`, `EMAIL`, `GENDER`, `EDUCATION`) VALUES
(1, 1, 'doctor1.jpg', 'Rajesh', 'Kumar', '1975-03-15', '2010-06-20', 'dr.rajesh', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543210, 'rajesh.kumar@gmail.com', 'MALE', 'MBBS, MD (Pediatrics)'),
(2, 1, 'doctor2.jpg', 'Priya', 'Sharma', '1980-07-22', '2012-09-15', 'dr.priya', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543211, 'priya.sharma@gmail.com', 'FEMALE', 'MBBS, DCH (Diploma in Child Health)'),
(3, 1, 'doctor3.jpg', 'Amit', 'Patel', '1978-11-10', '2015-03-25', 'dr.amit', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543212, 'amit.patel@gmail.com', 'MALE', 'MBBS, DNB (Pediatrics)'),
(4, 2, 'doctor4.jpg', 'Sunita', 'Reddy', '1976-05-18', '2011-07-10', 'dr.sunita', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543213, 'sunita.reddy@gmail.com', 'FEMALE', 'MBBS, MD (Cardiology), DM (Cardiology)'),
(5, 2, 'doctor5.jpg', 'Vikram', 'Singh', '1973-09-25', '2009-12-05', 'dr.vikram', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543214, 'vikram.singh@gmail.com', 'MALE', 'MBBS, MD (Medicine), DM (Cardiology)'),
(6, 2, 'doctor6.jpg', 'Anjali', 'Gupta', '1982-02-14', '2014-08-20', 'dr.anjali', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543215, 'anjali.gupta@gmail.com', 'FEMALE', 'MBBS, MD (Cardiology)'),
(7, 3, 'doctor7.jpg', 'Rahul', 'Verma', '1977-08-30', '2013-04-15', 'dr.rahul', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543216, 'rahul.verma@gmail.com', 'MALE', 'MBBS, MS (Orthopedics), MCh (Orthopedics)'),
(8, 3, 'doctor8.jpg', 'Meera', 'Joshi', '1981-12-05', '2016-01-10', 'dr.meera', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543217, 'meera.joshi@gmail.com', 'FEMALE', 'MBBS, DNB (Orthopedics)'),
(9, 4, 'doctor9.jpg', 'Sanjay', 'Malhotra', '1974-06-20', '2010-11-30', 'dr.sanjay', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543218, 'sanjay.malhotra@gmail.com', 'MALE', 'MBBS, MD (Medicine), DM (Neurology)'),
(10, 4, 'doctor10.jpg', 'Kavita', 'Nair', '1979-04-12', '2015-07-25', 'dr.kavita', '$2y$10$r5IXN1grDNiEXiNNtcn0CuGfbXkiwXkNJv5BX1n.wMFhmNG3E6vbC', 9876543219, 'kavita.nair@gmail.com', 'FEMALE', 'MBBS, MD (Neurology)');

-- Insert doctor schedules
INSERT INTO `doctor_schedule_tbl` (`SCHEDULE_ID`, `DOCTOR_ID`, `RECEPTIONIST_ID`, `START_TIME`, `END_TIME`, `AVAILABLE_DAY`) VALUES
(1, 1, 1, '09:00:00', '17:00:00', 'MON'), (2, 1, 1, '09:00:00', '17:00:00', 'WED'), (3, 1, 1, '09:00:00', '17:00:00', 'FRI'),
(4, 2, 1, '10:00:00', '18:00:00', 'TUE'), (5, 2, 1, '10:00:00', '18:00:00', 'THUR'), (6, 2, 1, '10:00:00', '18:00:00', 'SAT'),
(7, 3, 2, '08:00:00', '16:00:00', 'MON'), (8, 3, 2, '08:00:00', '16:00:00', 'WED'), (9, 3, 2, '08:00:00', '16:00:00', 'FRI'),
(10, 4, 2, '09:00:00', '17:00:00', 'TUE'), (11, 4, 2, '09:00:00', '17:00:00', 'THUR'), (12, 4, 2, '09:00:00', '17:00:00', 'SAT'),
(13, 5, 3, '10:00:00', '18:00:00', 'MON'), (14, 5, 3, '10:00:00', '18:00:00', 'WED'), (15, 5, 3, '10:00:00', '18:00:00', 'FRI'),
(16, 6, 3, '08:00:00', '16:00:00', 'TUE'), (17, 6, 3, '08:00:00', '16:00:00', 'THUR'), (18, 6, 3, '08:00:00', '16:00:00', 'SAT'),
(19, 7, 4, '09:00:00', '17:00:00', 'MON'), (20, 7, 4, '09:00:00', '17:00:00', 'WED'), (21, 7, 4, '09:00:00', '17:00:00', 'FRI'),
(22, 8, 4, '10:00:00', '18:00:00', 'TUE'), (23, 8, 4, '10:00:00', '18:00:00', 'THUR'), (24, 8, 4, '10:00:00', '18:00:00', 'SAT'),
(25, 9, 1, '08:00:00', '16:00:00', 'MON'), (26, 9, 1, '08:00:00', '16:00:00', 'WED'), (27, 9, 1, '08:00:00', '16:00:00', 'FRI'),
(28, 10, 1, '09:00:00', '17:00:00', 'TUE'), (29, 10, 1, '09:00:00', '17:00:00', 'THUR'), (30, 10, 1, '09:00:00', '17:00:00', 'SAT');

-- Insert appointments
INSERT INTO `appointment_tbl` (`APPOINTMENT_ID`, `PATIENT_ID`, `DOCTOR_ID`, `SCHEDULE_ID`, `CREATED_AT`, `APPOINTMENT_DATE`, `APPOINTMENT_TIME`, `STATUS`) VALUES
(1, 1, 1, 1, '2023-11-13 10:00:00', '2023-11-20', '10:00:00', 'SCHEDULED'), (2, 1, 1, 2, '2023-11-13 10:00:00', '2023-12-04', '10:00:00', 'SCHEDULED'),
(3, 2, 2, 4, '2023-11-13 11:00:00', '2023-11-21', '11:00:00', 'SCHEDULED'), (4, 2, 2, 5, '2023-11-13 11:00:00', '2023-12-05', '11:00:00', 'SCHEDULED'),
(5, 3, 3, 7, '2023-11-13 12:00:00', '2023-11-22', '09:00:00', 'SCHEDULED'), (6, 3, 3, 8, '2023-11-13 12:00:00', '2023-12-06', '09:00:00', 'SCHEDULED'),
(7, 4, 4, 10, '2023-11-13 13:00:00', '2023-11-23', '10:00:00', 'SCHEDULED'), (8, 4, 4, 11, '2023-11-13 13:00:00', '2023-12-07', '10:00:00', 'SCHEDULED'),
(9, 5, 5, 13, '2023-11-13 14:00:00', '2023-11-24', '11:00:00', 'SCHEDULED'), (10, 5, 5, 14, '2023-11-13 14:00:00', '2023-12-08', '11:00:00', 'SCHEDULED'),
(11, 6, 6, 16, '2023-11-13 15:00:00', '2023-11-25', '09:00:00', 'SCHEDULED'), (12, 6, 6, 17, '2023-11-13 15:00:00', '2023-12-09', '09:00:00', 'SCHEDULED'),
(13, 7, 7, 19, '2023-11-13 16:00:00', '2023-11-27', '10:00:00', 'SCHEDULED'), (14, 7, 7, 20, '2023-11-13 16:00:00', '2023-12-11', '10:00:00', 'SCHEDULED'),
(15, 8, 8, 22, '2023-11-13 17:00:00', '2023-11-28', '11:00:00', 'SCHEDULED'), (16, 8, 8, 23, '2023-11-13 17:00:00', '2023-12-12', '11:00:00', 'SCHEDULED'),
(17, 9, 9, 25, '2023-11-13 18:00:00', '2023-11-29', '09:00:00', 'SCHEDULED'), (18, 9, 9, 26, '2023-11-13 18:00:00', '2023-12-13', '09:00:00', 'SCHEDULED'),
(19, 10, 10, 28, '2023-11-13 19:00:00', '2023-11-30', '10:00:00', 'SCHEDULED'), (20, 10, 10, 29, '2023-11-13 19:00:00', '2023-12-14', '10:00:00', 'SCHEDULED');

-- Insert prescriptions
INSERT INTO `prescription_tbl` (`PRESCRIPTION_ID`, `APPOINTMENT_ID`, `ISSUE_DATE`, `HEIGHT_CM`, `WEIGHT_KG`, `BLOOD_PRESSURE`, `DIABETES`, `SYMPTOMS`, `DIAGNOSIS`, `ADDITIONAL_NOTES`, `CREATED_AT`) VALUES
(1, 1, '2023-11-20', 120, 25, 110, 'NO', 'Fever, cough, and cold', 'Upper respiratory tract infection', 'Advise plenty of rest and fluids', '2023-11-13 10:00:00'), (2, 2, '2023-12-04', 120, 25, 110, 'NO', 'Follow-up check', 'Recovering well', 'Continue prescribed medication', '2023-11-13 10:00:00'),
(3, 3, '2023-11-21', 160, 55, 120, 'NO', 'Chest pain, shortness of breath', 'Angina', 'Stress management recommended', '2023-11-13 11:00:00'), (4, 4, '2023-12-05', 160, 55, 120, 'NO', 'Follow-up check', 'Stable condition', 'Continue medication as prescribed', '2023-11-13 11:00:00'),
(5, 5, '2023-11-22', 110, 20, 100, 'NO', 'Wheezing, difficulty breathing', 'Asthma exacerbation', 'Avoid triggers like dust and pollen', '2023-11-13 12:00:00'), (6, 6, '2023-12-06', 110, 20, 100, 'NO', 'Follow-up check', 'Asthma under control', 'Continue inhaler as needed', '2023-11-13 12:00:00'),
(7, 7, '2023-11-23', 165, 60, 130, 'TYPE-2', 'Chest discomfort, palpitations', 'Hypertension with diabetes', 'Strict diet control required', '2023-11-13 13:00:00'), (8, 8, '2023-12-07', 165, 60, 130, 'TYPE-2', 'Follow-up check', 'Blood sugar levels improving', 'Continue medication and exercise', '2023-11-13 13:00:00'),
(9, 9, '2023-11-24', 170, 75, 140, 'PRE-DIABTIC', 'High blood pressure, dizziness', 'Hypertension', 'Reduce salt intake', '2023-11-13 14:00:00'), (10, 10, '2023-12-08', 170, 75, 140, 'PRE-DIABTIC', 'Follow-up check', 'Blood pressure controlled', 'Continue lifestyle changes', '2023-11-13 14:00:00'),
(11, 11, '2023-11-25', 155, 50, 120, 'NO', 'Irregular heartbeat', 'Arrhythmia', 'Avoid caffeine and stress', '2023-11-13 15:00:00'), (12, 12, '2023-12-09', 155, 50, 120, 'NO', 'Follow-up check', 'Heart rhythm normal', 'Continue medication as prescribed', '2023-11-13 15:00:00'),
(13, 13, '2023-11-27', 175, 80, 125, 'NO', 'Knee pain, difficulty walking', 'Osteoarthritis', 'Physical therapy recommended', '2023-11-13 16:00:00'), (14, 14, '2023-12-11', 175, 80, 125, 'NO', 'Follow-up check', 'Pain management effective', 'Continue exercises', '2023-11-13 16:00:00'),
(15, 15, '2023-11-28', 160, 65, 115, 'NO', 'Back pain, numbness in legs', 'Herniated disc', 'Surgery may be considered if no improvement', '2023-11-13 17:00:00'), (16, 16, '2023-12-12', 160, 65, 115, 'NO', 'Follow-up check', 'Symptoms improving', 'Continue physiotherapy', '2023-11-13 17:00:00'),
(17, 17, '2023-11-29', 172, 70, 130, 'NO', 'Frequent headaches, dizziness', 'Migraine', 'Identify and avoid triggers', '2023-11-13 18:00:00'), (18, 18, '2023-12-13', 172, 70, 130, 'NO', 'Follow-up check', 'Migraine frequency reduced', 'Continue preventive medication', '2023-11-13 18:00:00'),
(19, 19, '2023-11-30', 165, 60, 120, 'NO', 'Seizures, loss of consciousness', 'Epilepsy', 'Regular medication essential', '2023-11-13 19:00:00'), (20, 20, '2023-12-14', 165, 60, 120, 'NO', 'Follow-up check', 'Seizure controlled', 'Never skip medication', '2023-11-13 19:00:00');

-- Insert payments
INSERT INTO `payment_tbl` (`PAYMENT_ID`, `APPOINTMENT_ID`, `AMOUNT`, `PAYMENT_DATE`, `PAYMENT_MODE`, `STATUS`, `TRANSACTION_ID`) VALUES
(1, 1, 500.00, '2023-11-13', 'CREDIT CARD', 'COMPLETED', 'TXN123456789'), (2, 2, 500.00, '2023-11-13', 'GOOGLE PAY', 'COMPLETED', 'TXN123456790'),
(3, 3, 800.00, '2023-11-13', 'UPI', 'COMPLETED', 'TXN123456791'), (4, 4, 800.00, '2023-11-13', 'NET BANKING', 'COMPLETED', 'TXN123456792'),
(5, 5, 600.00, '2023-11-13', 'CREDIT CARD', 'COMPLETED', 'TXN123456793'), (6, 6, 600.00, '2023-11-13', 'GOOGLE PAY', 'COMPLETED', 'TXN123456794'),
(7, 7, 1000.00, '2023-11-13', 'UPI', 'COMPLETED', 'TXN123456795'), (8, 8, 1000.00, '2023-11-13', 'NET BANKING', 'COMPLETED', 'TXN123456796'),
(9, 9, 900.00, '2023-11-13', 'CREDIT CARD', 'COMPLETED', 'TXN123456797'), (10, 10, 900.00, '2023-11-13', 'GOOGLE PAY', 'COMPLETED', 'TXN123456798'),
(11, 11, 850.00, '2023-11-13', 'UPI', 'COMPLETED', 'TXN123456799'), (12, 12, 850.00, '2023-11-13', 'NET BANKING', 'COMPLETED', 'TXN123456800'),
(13, 13, 700.00, '2023-11-13', 'CREDIT CARD', 'COMPLETED', 'TXN123456801'), (14, 14, 700.00, '2023-11-13', 'GOOGLE PAY', 'COMPLETED', 'TXN123456802'),
(15, 15, 750.00, '2023-11-13', 'UPI', 'COMPLETED', 'TXN123456803'), (16, 16, 750.00, '2023-11-13', 'NET BANKING', 'COMPLETED', 'TXN123456804'),
(17, 17, 1200.00, '2023-11-13', 'CREDIT CARD', 'COMPLETED', 'TXN123456805'), (18, 18, 1200.00, '2023-11-13', 'GOOGLE PAY', 'COMPLETED', 'TXN123456806'),
(19, 19, 1100.00, '2023-11-13', 'UPI', 'COMPLETED', 'TXN123456807'), (20, 20, 1100.00, '2023-11-13', 'NET BANKING', 'COMPLETED', 'TXN123456808');

-- Insert feedback
INSERT INTO `feedback_tbl` (`FEEDBACK_ID`, `APPOINTMENT_ID`, `RATING`, `COMMENTS`) VALUES
(1, 1, 5, 'Doctor was very patient with my child. Explained everything clearly.'), (2, 2, 5, 'Follow-up was thorough. Treatment is working well.'),
(3, 3, 4, 'Good consultation, but waiting time was a bit long.'), (4, 4, 5, 'Doctor took time to explain the test results. Very satisfied.'),
(5, 5, 5, 'Doctor handled my child\'s asthma attack very professionally.'), (6, 6, 4, 'Good follow-up. Wish the appointment time was a bit longer.'),
(7, 7, 5, 'Excellent doctor. Very knowledgeable about diabetes management.'), (8, 8, 5, 'Very happy with the treatment plan. Feeling much better now.'),
(9, 9, 4, 'Doctor is good, but the clinic was crowded today.'), (10, 10, 5, 'Great follow-up. Blood pressure is under control now.'),
(11, 11, 5, 'Doctor explained the heart condition very well. Very reassuring.'), (12, 12, 5, 'Excellent care. Medication is working perfectly.'),
(13, 13, 4, 'Good diagnosis. Physical therapy recommendations are helpful.'), (14, 14, 5, 'Pain has reduced significantly. Thank you, doctor.'),
(15, 15, 5, 'Doctor explained the surgery options clearly. Very professional.'), (16, 16, 4, 'Improving well. Physiotherapy is helping a lot.'),
(17, 17, 5, 'Doctor identified my migraine triggers accurately. Very grateful.'), (18, 18, 5, 'Medication is working well. Migraine frequency has reduced.'),
(19, 19, 5, 'Doctor handled my seizure episode very calmly. Excellent care.'), (20, 20, 5, 'Medication is effective. No seizures since starting treatment.');

-- Insert appointment reminders
INSERT INTO `appointment_reminder_tbl` (`APPOINTMENT_REMINDER_ID`, `RECEPTIONIST_ID`, `APPOINTMENT_ID`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 1, '10:00:00', 'Appointment booked'), (2, 1, 1, '10:00:00', '24 hours before appointment'), (3, 1, 1, '07:00:00', '3 hours before appointment'),
(4, 1, 2, '10:00:00', 'Appointment booked'), (5, 1, 2, '10:00:00', '24 hours before appointment'), (6, 1, 2, '07:00:00', '3 hours before appointment'),
(7, 1, 3, '11:00:00', 'Appointment booked'), (8, 1, 3, '11:00:00', '24 hours before appointment'), (9, 1, 3, '08:00:00', '3 hours before appointment'),
(10, 1, 4, '11:00:00', 'Appointment booked'), (11, 1, 4, '11:00:00', '24 hours before appointment'), (12, 1, 4, '08:00:00', '3 hours before appointment'),
(13, 2, 5, '12:00:00', 'Appointment booked'), (14, 2, 5, '09:00:00', '24 hours before appointment'), (15, 2, 5, '06:00:00', '3 hours before appointment'),
(16, 2, 6, '12:00:00', 'Appointment booked'), (17, 2, 6, '09:00:00', '24 hours before appointment'), (18, 2, 6, '06:00:00', '3 hours before appointment'),
(19, 2, 7, '13:00:00', 'Appointment booked'), (20, 2, 7, '10:00:00', '24 hours before appointment'), (21, 2, 7, '07:00:00', '3 hours before appointment'),
(22, 2, 8, '13:00:00', 'Appointment booked'), (23, 2, 8, '10:00:00', '24 hours before appointment'), (24, 2, 8, '07:00:00', '3 hours before appointment'),
(25, 3, 9, '14:00:00', 'Appointment booked'), (26, 3, 9, '11:00:00', '24 hours before appointment'), (27, 3, 9, '08:00:00', '3 hours before appointment'),
(28, 3, 10, '14:00:00', 'Appointment booked'), (29, 3, 10, '11:00:00', '24 hours before appointment'), (30, 3, 10, '08:00:00', '3 hours before appointment'),
(31, 3, 11, '15:00:00', 'Appointment booked'), (32, 3, 11, '09:00:00', '24 hours before appointment'), (33, 3, 11, '06:00:00', '3 hours before appointment'),
(34, 3, 12, '15:00:00', 'Appointment booked'), (35, 3, 12, '09:00:00', '24 hours before appointment'), (36, 3, 12, '06:00:00', '3 hours before appointment'),
(37, 4, 13, '16:00:00', 'Appointment booked'), (38, 4, 13, '10:00:00', '24 hours before appointment'), (39, 4, 13, '07:00:00', '3 hours before appointment'),
(40, 4, 14, '16:00:00', 'Appointment booked'), (41, 4, 14, '10:00:00', '24 hours before appointment'), (42, 4, 14, '07:00:00', '3 hours before appointment'),
(43, 4, 15, '17:00:00', 'Appointment booked'), (44, 4, 15, '11:00:00', '24 hours before appointment'), (45, 4, 15, '08:00:00', '3 hours before appointment'),
(46, 4, 16, '17:00:00', 'Appointment booked'), (47, 4, 16, '11:00:00', '24 hours before appointment'), (48, 4, 16, '08:00:00', '3 hours before appointment'),
(49, 1, 17, '18:00:00', 'Appointment booked'), (50, 1, 17, '09:00:00', '24 hours before appointment'), (51, 1, 17, '06:00:00', '3 hours before appointment'),
(52, 1, 18, '18:00:00', 'Appointment booked'), (53, 1, 18, '09:00:00', '24 hours before appointment'), (54, 1, 18, '06:00:00', '3 hours before appointment'),
(55, 1, 19, '19:00:00', 'Appointment booked'), (56, 1, 19, '10:00:00', '24 hours before appointment'), (57, 1, 19, '07:00:00', '3 hours before appointment'),
(58, 1, 20, '19:00:00', 'Appointment booked'), (59, 1, 20, '10:00:00', '24 hours before appointment'), (60, 1, 20, '07:00:00', '3 hours before appointment');

-- Insert prescription medicines
INSERT INTO `prescription_medicine_tbl` (`PRESCRIPTION_ID`, `MEDICINE_ID`, `DOSAGE`, `DURATION`, `FREQUENCY`, `CREATED_AT`) VALUES
(1, 1, '5ml', '5 days', 'Twice daily', '2023-11-13 10:00:00'), (1, 2, '125mg', '7 days', 'Three times daily', '2023-11-13 10:00:00'),
(2, 1, '5ml', '3 days', 'As needed', '2023-11-13 10:00:00'), (2, 4, '1ml', '30 days', 'Once daily', '2023-11-13 10:00:00'),
(3, 5, '50mg', '30 days', 'Once daily', '2023-11-13 11:00:00'), (3, 6, '75mg', '30 days', 'Once daily', '2023-11-13 11:00:00'),
(4, 5, '50mg', '30 days', 'Once daily', '2023-11-13 11:00:00'), (4, 7, '10mg', '30 days', 'Once daily', '2023-11-13 11:00:00'),
(5, 3, '2 puffs', '30 days', 'As needed', '2023-11-13 12:00:00'), (5, 1, '5ml', '3 days', 'As needed for fever', '2023-11-13 12:00:00'),
(6, 3, '2 puffs', '30 days', 'As needed', '2023-11-13 12:00:00'), (6, 4, '1ml', '30 days', 'Once daily', '2023-11-13 12:00:00'),
(7, 7, '10mg', '30 days', 'Once daily', '2023-11-13 13:00:00'), (7, 8, '20mg', '30 days', 'Once daily at night', '2023-11-13 13:00:00'),
(8, 7, '10mg', '30 days', 'Once daily', '2023-11-13 13:00:00'), (8, 8, '20mg', '30 days', 'Once daily at night', '2023-11-13 13:00:00'),
(9, 5, '50mg', '30 days', 'Once daily', '2023-11-13 14:00:00'), (9, 7, '10mg', '30 days', 'Once daily', '2023-11-13 14:00:00'),
(10, 5, '50mg', '30 days', 'Once daily', '2023-11-13 14:00:00'), (10, 7, '10mg', '30 days', 'Once daily', '2023-11-13 14:00:00'),
(11, 6, '75mg', '30 days', 'Once daily', '2023-11-13 15:00:00'), (11, 5, '25mg', '30 days', 'Twice daily', '2023-11-13 15:00:00'),
(12, 6, '75mg', '30 days', 'Once daily', '2023-11-13 15:00:00'), (12, 5, '25mg', '30 days', 'Twice daily', '2023-11-13 15:00:00'),
(13, 9, '400mg', '30 days', 'Three times daily', '2023-11-13 16:00:00'), (13, 10, '500mg', '30 days', 'Twice daily', '2023-11-13 16:00:00'),
(14, 9, '400mg', '30 days', 'As needed', '2023-11-13 16:00:00'), (14, 10, '500mg', '30 days', 'Twice daily', '2023-11-13 16:00:00'),
(15, 11, '300mg', '30 days', 'Three times daily', '2023-11-13 17:00:00'), (15, 12, '50mg', '15 days', 'As needed for pain', '2023-11-13 17:00:00'),
(16, 11, '300mg', '30 days', 'Three times daily', '2023-11-13 17:00:00'), (16, 12, '50mg', '15 days', 'As needed for pain', '2023-11-13 17:00:00'),
(17, 13, '500mg', '30 days', 'Twice daily', '2023-11-13 18:00:00'), (17, 14, '25mg', '30 days', 'Once daily at night', '2023-11-13 18:00:00'),
(18, 13, '500mg', '30 days', 'Twice daily', '2023-11-13 18:00:00'), (18, 14, '25mg', '30 days', 'Once daily at night', '2023-11-13 18:00:00'),
(19, 15, '2mg', '30 days', 'Three times daily', '2023-11-13 19:00:00'), (19, 16, '100mg', '30 days', 'Once daily at night', '2023-11-13 19:00:00'),
(20, 15, '2mg', '30 days', 'Three times daily', '2023-11-13 19:00:00'), (20, 16, '100mg', '30 days', 'Once daily at night', '2023-11-13 19:00:00');

-- Insert medicine reminders
INSERT INTO `medicine_reminder_tbl` (`MEDICINE_REMINDER_ID`, `MEDICINE_ID`, `CREATOR_ROLE`, `CREATOR_ID`, `PATIENT_ID`, `REMINDER_TIME`, `REMARKS`) VALUES
(1, 1, 'RECEPTIONIST', 1, 1, '08:00:00', 'Morning dose'), (2, 1, 'RECEPTIONIST', 1, 1, '20:00:00', 'Evening dose'),
(3, 2, 'RECEPTIONIST', 1, 1, '08:00:00', 'Morning dose'), (4, 2, 'RECEPTIONIST', 1, 1, '14:00:00', 'Afternoon dose'), (5, 2, 'RECEPTIONIST', 1, 1, '20:00:00', 'Evening dose'),
(6, 4, 'RECEPTIONIST', 1, 1, '09:00:00', 'Morning dose'), (7, 5, 'RECEPTIONIST', 1, 2, '08:00:00', 'Morning dose'), (8, 6, 'RECEPTIONIST', 1, 2, '09:00:00', 'Morning dose'),
(9, 3, 'RECEPTIONIST', 2, 3, '08:00:00', 'Morning dose'), (10, 3, 'RECEPTIONIST', 2, 3, '20:00:00', 'Evening dose'),
(11, 7, 'RECEPTIONIST', 2, 4, '08:00:00', 'Morning dose'), (12, 8, 'RECEPTIONIST', 2, 4, '21:00:00', 'Night dose'),
(13, 9, 'RECEPTIONIST', 3, 5, '08:00:00', 'Morning dose'), (14, 9, 'RECEPTIONIST', 3, 5, '14:00:00', 'Afternoon dose'), (15, 9, 'RECEPTIONIST', 3, 5, '20:00:00', 'Evening dose'),
(16, 10, 'RECEPTIONIST', 3, 5, '09:00:00', 'Morning dose'), (17, 10, 'RECEPTIONIST', 3, 5, '21:00:00', 'Night dose'),
(18, 11, 'RECEPTIONIST', 3, 6, '08:00:00', 'Morning dose'), (19, 11, 'RECEPTIONIST', 3, 6, '14:00:00', 'Afternoon dose'), (20, 11, 'RECEPTIONIST', 3, 6, '20:00:00', 'Evening dose'),
(21, 13, 'RECEPTIONIST', 1, 9, '08:00:00', 'Morning dose'), (22, 13, 'RECEPTIONIST', 1, 9, '20:00:00', 'Evening dose'), (23, 14, 'RECEPTIONIST', 1, 9, '21:00:00', 'Night dose'),
(24, 15, 'RECEPTIONIST', 1, 10, '08:00:00', 'Morning dose'), (25, 15, 'RECEPTIONIST', 1, 10, '14:00:00', 'Afternoon dose'), (26, 15, 'RECEPTIONIST', 1, 10, '20:00:00', 'Evening dose'),
(27, 16, 'RECEPTIONIST', 1, 10, '21:00:00', 'Night dose');
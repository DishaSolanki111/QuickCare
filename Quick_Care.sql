CREATE TABLE specialisation_tbl (
  SPECIALISATION_ID INT NOT NULL AUTO_INCREMENT,
  SPECIALISATION_NAME VARCHAR(50),
  PRIMARY KEY (SPECIALISATION_ID)
);


CREATE TABLE doctor_tbl (
  DOCTOR_ID INT NOT NULL AUTO_INCREMENT,
  SPECIALISATION_ID INT NOT NULL,
  PROFILE_IMAGE VARCHAR(255),
  FIRST_NAME VARCHAR(20) ,
  LAST_NAME VARCHAR(20) ,
  DOB DATE  ,
  DOJ DATE ,
  USERNAME VARCHAR(20) ,
  PSWD VARCHAR(60) ,
  PHONE BIGINT,
  EMAIL VARCHAR(50) ,
  GENDER ENUM('MALE','FEMALE','OTHER') ,
  PRIMARY KEY (DOCTOR_ID),
  UNIQUE (USERNAME),
  UNIQUE (PHONE),
  UNIQUE (EMAIL),
  FOREIGN KEY (SPECIALISATION_ID) REFERENCES specialisation_tbl(SPECIALISATION_ID)
);

CREATE TABLE patient_tbl (
  PATIENT_ID INT NOT NULL AUTO_INCREMENT,
  FIRST_NAME VARCHAR(20) ,
  LAST_NAME VARCHAR(20) ,
  USERNAME VARCHAR(20) ,
  PSWD VARCHAR(60) ,
  DOB DATE ,
  GENDER ENUM('MALE','FEMALE','OTHER') ,
  BLOOD_GROUP ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-'),
  PHONE BIGINT ,
  EMAIL VARCHAR(50) ,
  ADDRESS TEXT ,
  PRIMARY KEY (PATIENT_ID),
  UNIQUE (USERNAME),
  UNIQUE (PHONE),
  UNIQUE (EMAIL)
);

CREATE TABLE receptionist_tbl (
  RECEPTIONIST_ID INT NOT NULL AUTO_INCREMENT,
  FIRST_NAME VARCHAR(20) ,
  LAST_NAME VARCHAR(20) ,
  DOB DATE ,
  DOJ DATE,
  GENDER ENUM('MALE','FEMALE','OTHER') ,
  PHONE BIGINT ,
  EMAIL VARCHAR(50) ,
  ADDRESS TEXT ,
  USERNAME VARCHAR(20) ,
  PSWD VARCHAR(60) ,
  PRIMARY KEY (RECEPTIONIST_ID),
  UNIQUE (PHONE),
  UNIQUE (EMAIL),
  UNIQUE (USERNAME)
);


CREATE TABLE doctor_schedule_tbl (
  SCHEDULE_ID INT NOT NULL AUTO_INCREMENT,
  DOCTOR_ID INT NOT NULL,
  RECEPTIONIST_ID INT NOT NULL,
  START_TIME TIME ,
  END_TIME TIME ,
  AVAILABLE_DAY ENUM('MON','TUE','WED','THUR','FRI','SAT','SUN') ,
  PRIMARY KEY (SCHEDULE_ID),
  FOREIGN KEY (DOCTOR_ID) REFERENCES doctor_tbl(DOCTOR_ID),
  FOREIGN KEY (RECEPTIONIST_ID) REFERENCES receptionist_tbl(RECEPTIONIST_ID)
);


CREATE TABLE appointment_tbl (
  APPOINTMENT_ID INT NOT NULL AUTO_INCREMENT,
  PATIENT_ID INT NOT NULL,
  DOCTOR_ID INT NOT NULL,
  SCHEDULE_ID INT NOT NULL,
  CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  APPOINTMENT_DATE DATE ,
  APPOINTMENT_TIME TIME ,
  STATUS ENUM('SCHEDULED','COMPLETED','CANCELLED'),
  PRIMARY KEY (APPOINTMENT_ID),
  FOREIGN KEY (PATIENT_ID) REFERENCES patient_tbl(PATIENT_ID),
  FOREIGN KEY (DOCTOR_ID) REFERENCES doctor_tbl(DOCTOR_ID),
  FOREIGN KEY (SCHEDULE_ID) REFERENCES doctor_schedule_tbl(SCHEDULE_ID)
);


CREATE TABLE prescription_tbl (
  PRESCRIPTION_ID INT NOT NULL AUTO_INCREMENT,
  APPOINTMENT_ID INT NOT NULL,
  ISSUE_DATE DATE ,
  HEIGHT_CM INT ,
  WEIGHT_KG DECIMAL(5,2) ,
  BLOOD_PRESSURE SMALLINT,
  DIABETES ENUM('NO','TYPE-1','TYPE-2','PRE-DIABTIC'),
  SYMPTOMS TEXT ,
  DIAGNOSIS TEXT ,
  ADDITIONAL_NOTES TEXT,
  CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (PRESCRIPTION_ID),
  FOREIGN KEY (APPOINTMENT_ID) REFERENCES appointment_tbl(APPOINTMENT_ID)
);


CREATE TABLE medicine_tbl (
  MEDICINE_ID INT NOT NULL AUTO_INCREMENT,
  RECEPTIONIST_ID INT NOT NULL,
  MED_NAME VARCHAR(25),
  DESCRIPTION TEXT,
  PRIMARY KEY (MEDICINE_ID),
  FOREIGN KEY (RECEPTIONIST_ID) REFERENCES receptionist_tbl(RECEPTIONIST_ID)
);


CREATE TABLE prescription_medicine_tbl (
  PRESCRIPTION_ID INT NOT NULL,
  MEDICINE_ID INT NOT NULL,
  DOSAGE VARCHAR(30) ,
  DURATION VARCHAR(30),
  FREQUENCY VARCHAR(30),
  CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (PRESCRIPTION_ID, MEDICINE_ID),
  FOREIGN KEY (PRESCRIPTION_ID) REFERENCES prescription_tbl(PRESCRIPTION_ID),
  FOREIGN KEY (MEDICINE_ID) REFERENCES medicine_tbl(MEDICINE_ID)
);


CREATE TABLE payment_tbl (
  PAYMENT_ID INT NOT NULL AUTO_INCREMENT,
  APPOINTMENT_ID INT NOT NULL,
  AMOUNT DECIMAL(10,2) ,
  PAYMENT_DATE DATE ,
  PAYMENT_MODE ENUM('CREDIT CARD','GOOGLE PAY','UPI','NET BANKING') ,
  STATUS ENUM('COMPLETED','FAILED') ,
  TRANSACTION_ID VARCHAR(36) ,
  PRIMARY KEY (PAYMENT_ID),
  UNIQUE (TRANSACTION_ID),
  FOREIGN KEY (APPOINTMENT_ID) REFERENCES appointment_tbl(APPOINTMENT_ID)
);


CREATE TABLE feedback_tbl (
  FEEDBACK_ID INT NOT NULL AUTO_INCREMENT,
  APPOINTMENT_ID INT NOT NULL,
  RATING INT ,
  COMMENTS VARCHAR(255),
  PRIMARY KEY (FEEDBACK_ID),
  FOREIGN KEY (APPOINTMENT_ID) REFERENCES appointment_tbl(APPOINTMENT_ID)
);


CREATE TABLE medicine_reminder_tbl (
  MEDICINE_REMINDER_ID INT NOT NULL AUTO_INCREMENT,
  MEDICINE_ID INT NOT NULL,
  CREATOR_ROLE ENUM('PATIENT','RECEPTIONIST') ,
  CREATOR_ID INT NOT NULL,
  PATIENT_ID INT NOT NULL,
  REMINDER_TIME TIME ,
  REMARKS TEXT,
  PRIMARY KEY (MEDICINE_REMINDER_ID),
  FOREIGN KEY (MEDICINE_ID) REFERENCES medicine_tbl(MEDICINE_ID),
  FOREIGN KEY (PATIENT_ID) REFERENCES patient_tbl(PATIENT_ID)
);


CREATE TABLE appointment_reminder_tbl (
  APPOINTMENT_REMINDER_ID INT NOT NULL AUTO_INCREMENT,
  RECEPTIONIST_ID INT NOT NULL,
  APPOINTMENT_ID INT NOT NULL,
  REMINDER_TIME TIME ,
  REMARKS TEXT,
  PRIMARY KEY (APPOINTMENT_REMINDER_ID),
  FOREIGN KEY (RECEPTIONIST_ID) REFERENCES receptionist_tbl(RECEPTIONIST_ID),
  FOREIGN KEY (APPOINTMENT_ID) REFERENCES appointment_tbl(APPOINTMENT_ID)
);

-- #####################################################################
-- #                PATIENT APPOINTMENT SYSTEM DUMMY DATA              #
-- #####################################################################

-- NOTE: It's recommended to run this script on an empty database.
-- This script is designed to be run in order to satisfy foreign key constraints.

-- #####################################################################
-- # 1. SPECIALIZATION DATA (4 entries as requested)
-- #####################################################################
INSERT INTO specialisation_tbl (SPECIALISATION_NAME) VALUES
('Pediatrician'),
('Cardiologist'),
('Orthopedics'),
('Neurologist');

-- #####################################################################
-- # 2. DOCTOR DATA (10 doctors)
-- #####################################################################
-- NOTE: Passwords are in plain text as requested. In a real application, ALWAYS hash passwords.
INSERT INTO doctor_tbl (SPECIALISATION_ID, PROFILE_IMAGE, FIRST_NAME, LAST_NAME, DOB, DOJ, USERNAME, PSWD, PHONE, EMAIL, GENDER) VALUES
-- Pediatricians
(1, 'doc_ellen_page.jpg', 'Ellen', 'Page', '1985-04-16', '2018-05-20', 'e.page', 'password123', 9876543210, 'e.page.pedia@hospital.com', 'FEMALE'),
(1, 'doc_tom_holland.jpg', 'Tom', 'Holland', '1990-06-01', '2020-01-15', 't.holland', 'docpass1', 9876543211, 't.holland.pedia@hospital.com', 'MALE'),
(1, 'doc_saoirse_ronan.jpg', 'Saoirse', 'Ronan', '1988-04-12', '2019-07-22', 's.ronan', 'pediatrician_pass', 9876543212, 's.ronan.pedia@hospital.com', 'FEMALE'),

-- Cardiologists
(2, 'doc_amar_kumar.jpg', 'Amar', 'Kumar', '1978-11-25', '2015-03-10', 'a.kumar', 'heartpass123', 9876543213, 'a.kumar.cardio@hospital.com', 'MALE'),
(2, 'doc_priya_sharma.jpg', 'Priya', 'Sharma', '1982-02-18', '2017-09-01', 'p.sharma', 'cardio_pass', 9876543214, 'p.sharma.cardio@hospital.com', 'FEMALE'),
(2, 'doc_rajiv_menon.jpg', 'Rajiv', 'Menon', '1975-09-30', '2016-11-11', 'r.menon', 'pass1234', 9876543215, 'r.menon.cardio@hospital.com', 'MALE'),

-- Orthopedics
(3, 'doc_david_miller.jpg', 'David', 'Miller', '1980-05-22', '2019-02-12', 'd.miller', 'bone_pass', 9876543216, 'd.miller.ortho@hospital.com', 'MALE'),
(3, 'doc_sarah_jones.jpg', 'Sarah', 'Jones', '1988-10-05', '2021-06-30', 's.jones', 'ortho_secure', 9876543217, 's.jones.ortho@hospital.com', 'FEMALE'),

-- Neurologists
(4, 'doc_anjali_reddy.jpg', 'Anjali', 'Reddy', '1984-07-19', '2018-08-05', 'a.reddy', 'neuro_pass', 9876543218, 'a.reddy.neuro@hospital.com', 'FEMALE'),
(4, 'doc_michael_lee.jpg', 'Michael', 'Lee', '1979-12-01', '2017-04-25', 'm.lee', 'brainpass', 9876543219, 'm.lee.neuro@hospital.com', 'MALE');


-- #####################################################################
-- # 3. PATIENT DATA (10 patients)
-- #####################################################################
INSERT INTO patient_tbl (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS) VALUES
('Rohan', 'Verma', 'r.verma', 'patientpass1', '2015-03-12', 'MALE', 'O+', 9811111111, 'rohan.v@email.com', '123, ABC Street, Delhi'),
('Ananya', 'Iyer', 'a.iyer', 'patientpass2', '2018-07-21', 'FEMALE', 'A+', 9811111112, 'ananya.i@email.com', '456, XYZ Road, Mumbai'),
('Sunil', 'Kapoor', 's.kapoor', 'patientpass3', '1965-01-30', 'MALE', 'B+', 9811111113, 's.kapoor@email.com', '789, DEF Lane, Bangalore'),
('Meera', 'Nair', 'm.nair', 'patientpass4', '1972-11-08', 'FEMALE', 'AB-', 9811111114, 'meera.n@email.com', '101, GHI Block, Chennai'),
('Karan', 'Joshi', 'k.joshi', 'patientpass5', '2001-05-25', 'MALE', 'O-', 9811111115, 'karan.j@email.com', '202, JKL Avenue, Pune'),
('Fatima', 'Sheikh', 'f.sheikh', 'patientpass6', '1995-09-15', 'FEMALE', 'A-', 9811111116, 'fatima.s@email.com', '303, MNO Street, Kolkata'),
('Amit', 'Patel', 'a.patel', 'patientpass7', '1988-04-02', 'MALE', 'B-', 9811111117, 'amit.p@email.com', '404, PQR Circle, Ahmedabad'),
('Priyanka', 'Das', 'p.das', 'patientpass8', '2003-12-20', 'FEMALE', 'AB+', 9811111118, 'priyanka.d@email.com', '505, STU Cross, Hyderabad'),
('Vikram', 'Singh', 'v.singh', 'patientpass9', '1978-06-18', 'MALE', 'A+', 9811111119, 'vikram.s@email.com', '606, VWX Park, Jaipur'),
('Leela', 'Gopalakrishnan', 'l.gopal', 'patientpass10', '1968-10-28', 'FEMALE', 'O+', 9811111120, 'leela.g@email.com', '707, YZA Road, Kochi');


-- #####################################################################
-- # 4. RECEPTIONIST DATA (10 receptionists)
-- #####################################################################
INSERT INTO receptionist_tbl (FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, ADDRESS, USERNAME, PSWD) VALUES
('John', 'Doe', '1992-03-15', '2022-01-10', 'MALE', 9911111111, 'john.doe@hospital.com', 'Near Main Gate', 'j.doe', 'recept_pass1'),
('Jane', 'Smith', '1994-07-22', '2022-02-18', 'FEMALE', 9911111112, 'jane.smith@hospital.com', 'Reception Block A', 'j.smith', 'recept_pass2'),
('Peter', 'Jones', '1990-11-30', '2021-05-20', 'MALE', 9911111113, 'peter.jones@hospital.com', 'Admin Office', 'p.jones', 'recept_pass3'),
('Mary', 'Williams', '1991-02-08', '2021-09-01', 'FEMALE', 9911111114, 'mary.w@hospital.com', 'Front Desk', 'm.williams', 'recept_pass4'),
('David', 'Brown', '1988-05-25', '2020-03-12', 'MALE', 9911111115, 'david.b@hospital.com', 'Patient Services', 'd.brown', 'recept_pass5'),
('Susan', 'Davis', '1993-09-14', '2023-01-05', 'FEMALE', 9911111116, 'susan.d@hospital.com', 'Billing Counter', 's.davis', 'recept_pass6'),
('Chris', 'Miller', '1989-12-01', '2022-06-30', 'MALE', 9911111117, 'chris.m@hospital.com', 'Wing B Reception', 'c.miller', 'recept_pass7'),
('Lisa', 'Wilson', '1992-04-18', '2023-02-15', 'FEMALE', 9911111118, 'lisa.w@hospital.com', 'Emergency Desk', 'l.wilson', 'recept_pass8'),
('Kevin', 'Moore', '1991-08-21', '2022-11-11', 'MALE', 9911111119, 'kevin.m@hospital.com', 'OPD Reception', 'k.moore', 'recept_pass9'),
('Nancy', 'Taylor', '1990-06-10', '2021-12-20', 'FEMALE', 9911111120, 'nancy.t@hospital.com', 'Main Lobby', 'n.taylor', 'recept_pass10');


-- #####################################################################
-- # 5. DOCTOR SCHEDULE DATA
-- #####################################################################
INSERT INTO doctor_schedule_tbl (DOCTOR_ID, RECEPTIONIST_ID, START_TIME, END_TIME, AVAILABLE_DAY) VALUES
-- Dr. Ellen Page (Pediatrician)
(1, 1, '09:00:00', '17:00:00', 'MON'), (1, 1, '09:00:00', '17:00:00', 'WED'), (1, 1, '09:00:00', '14:00:00', 'SAT'),
-- Dr. Tom Holland (Pediatrician)
(2, 2, '10:00:00', '16:00:00', 'TUE'), (2, 2, '10:00:00', '16:00:00', 'THUR'), (2, 2, '10:00:00', '16:00:00', 'FRI'),
-- Dr. Amar Kumar (Cardiologist)
(4, 3, '08:00:00', '15:00:00', 'MON'), (4, 3, '08:00:00', '15:00:00', 'THUR'), (4, 3, '09:00:00', '13:00:00', 'SUN'),
-- Dr. Priya Sharma (Cardiologist)
(5, 4, '09:00:00', '17:00:00', 'TUE'), (5, 4, '09:00:00', '17:00:00', 'WED'), (5, 4, '09:00:00', '17:00:00', 'FRI'),
-- Dr. David Miller (Orthopedics)
(7, 5, '11:00:00', '18:00:00', 'MON'), (7, 5, '11:00:00', '18:00:00', 'WED'), (7, 5, '11:00:00', '15:00:00', 'SAT'),
-- Dr. Sarah Jones (Orthopedics)
(8, 6, '08:00:00', '14:00:00', 'TUE'), (8, 6, '08:00:00', '14:00:00', 'THUR'), (8, 6, '08:00:00', '14:00:00', 'FRI'),
-- Dr. Anjali Reddy (Neurologist)
(9, 7, '10:00:00', '16:00:00', 'WED'), (9, 7, '10:00:00', '16:00:00', 'FRI'), (9, 7, '10:00:00', '16:00:00', 'SUN'),
-- Dr. Michael Lee (Neurologist)
(10, 8, '09:00:00', '15:00:00', 'MON'), (10, 8, '09:00:00', '15:00:00', 'TUE'), (10, 8, '09:00:00', '15:00:00', 'SAT');


-- #####################################################################
-- # 6. MEDICINE DATA (Added by Receptionist 1 for simplicity)
-- #####################################################################
INSERT INTO medicine_tbl (RECEPTIONIST_ID, MED_NAME, DESCRIPTION) VALUES
-- Pediatric Medicines
(1, 'Paracetamol Syrup', 'For fever and pain relief in children.'),
(1, 'Amoxicillin Suspension', 'Antibiotic for bacterial infections.'),
(1, 'Salbutamol Inhaler', 'For asthma relief.'),
-- Cardiology Medicines
(1, 'Aspirin', 'Blood thinner, prevents clots.'),
(1, 'Atorvastatin', 'Lowers cholesterol levels.'),
(1, 'Metoprolol', 'Beta-blocker for high blood pressure.'),
(1, 'Clopidogrel', 'Antiplatelet agent.'),
-- Orthopedic Medicines
(1, 'Ibuprofen', 'NSAID for pain and inflammation.'),
(1, 'Calcium Carbonate', 'Calcium supplement for bone health.'),
(1, 'Vitamin D3', 'Supports calcium absorption.'),
-- Neurology Medicines
(1, 'Pregabalin', 'For neuropathic pain and seizures.'),
(1, 'Levetiracetam', 'Anti-epileptic medication.'),
(1, 'Ropinirole', 'For Parkinson''s disease and Restless Legs Syndrome.');


-- #####################################################################
-- # 7. APPOINTMENT DATA (20 appointments - 2 per patient)
-- #####################################################################
-- Appointment 1: Past and COMPLETED
-- Appointment 2: Future and SCHEDULED

-- Patient 1 (Rohan) with Dr. Ellen Page (Pediatrician)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(1, 1, 1, '2024-04-15', '10:00:00', 'COMPLETED'), -- Past
(1, 1, 1, '2024-07-22', '11:00:00', 'SCHEDULED'); -- Future

-- Patient 2 (Ananya) with Dr. Tom Holland (Pediatrician)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(2, 2, 4, '2024-05-21', '11:00:00', 'COMPLETED'), -- Past
(2, 2, 5, '2024-07-25', '14:00:00', 'SCHEDULED'); -- Future

-- Patient 3 (Sunil) with Dr. Amar Kumar (Cardiologist)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(3, 4, 7, '2024-03-18', '09:00:00', 'COMPLETED'), -- Past
(3, 4, 8, '2024-08-01', '10:00:00', 'SCHEDULED'); -- Future

-- Patient 4 (Meera) with Dr. Priya Sharma (Cardiologist)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(4, 5, 10, '2024-04-09', '10:30:00', 'COMPLETED'), -- Past
(4, 5, 12, '2024-08-05', '11:30:00', 'SCHEDULED'); -- Future

-- Patient 5 (Karan) with Dr. David Miller (Orthopedics)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(5, 7, 13, '2024-05-06', '13:00:00', 'COMPLETED'), -- Past
(5, 7, 15, '2024-07-27', '12:00:00', 'SCHEDULED'); -- Future

-- Patient 6 (Fatima) with Dr. Sarah Jones (Orthopedics)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(6, 8, 16, '2024-04-23', '09:00:00', 'COMPLETED'), -- Past
(6, 8, 18, '2024-08-08', '10:00:00', 'SCHEDULED'); -- Future

-- Patient 7 (Amit) with Dr. Anjali Reddy (Neurologist)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(7, 9, 19, '2024-05-29', '11:00:00', 'COMPLETED'), -- Past
(7, 9, 20, '2024-08-12', '14:00:00', 'SCHEDULED'); -- Future

-- Patient 8 (Priyanka) with Dr. Michael Lee (Neurologist)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(8, 10, 22, '2024-06-03', '10:00:00', 'COMPLETED'), -- Past
(8, 10, 24, '2024-08-15', '11:00:00', 'SCHEDULED'); -- Future

-- Patient 9 (Vikram) with Dr. Rajiv Menon (Cardiologist)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(9, 6, 7, '2024-04-01', '11:00:00', 'COMPLETED'), -- Past
(9, 6, 9, '2024-07-30', '09:30:00', 'SCHEDULED'); -- Future

-- Patient 10 (Leela) with Dr. Saoirse Ronan (Pediatrician)
INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) VALUES
(10, 3, 3, '2024-05-13', '10:00:00', 'COMPLETED'), -- Past
(10, 3, 3, '2024-07-20', '11:30:00', 'SCHEDULED'); -- Future


-- #####################################################################
-- # 8. PRESCRIPTION DATA (For COMPLETED appointments only)
-- #####################################################################
INSERT INTO prescription_tbl (APPOINTMENT_ID, ISSUE_DATE, HEIGHT_CM, WEIGHT_KG, BLOOD_PRESSURE, DIABETES, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES) VALUES
-- Appointment 1: Pediatrician
(1, '2024-04-15', 110, 20, 110, 'NO', 'Fever, cough, and runny nose for 2 days.', 'Common Cold', 'Advise rest and plenty of fluids.'),
-- Appointment 3: Pediatrician
(3, '2024-05-21', 95, 16, 100, 'NO', 'Wheezing and shortness of breath.', 'Asthma Attack', 'Prescribed inhaler. Avoid dust and pollen.'),
-- Appointment 5: Cardiologist
(5, '2024-03-18', 175, 80, 150, 'NO', 'Chest pain on exertion, relieved by rest.', 'Stable Angina', 'Lifestyle changes and medication prescribed.'),
-- Appointment 7: Cardiologist
(7, '2024-04-09', 162, 65, 140, 'TYPE-2', 'Occasional palpitations and fatigue.', 'Hypertension with Diabetes', 'Strict diet control required. Monitor BP daily.'),
-- Appointment 9: Orthopedics
(9, '2024-05-06', 180, 75, 120, 'NO', 'Persistent knee pain for 3 months.', 'Early-stage Osteoarthritis', 'Physiotherapy recommended. Avoid high-impact activities.'),
-- Appointment 11: Orthopedics
(11, '2024-04-23', 165, 58, 115, 'NO', 'Lower back pain, especially after sitting.', 'Muscle Strain', 'Hot water bag and pain relievers prescribed.'),
-- Appointment 13: Neurologist
(13, '2024-05-29', 170, 72, 130, 'NO', 'Numbness and tingling sensation in hands.', 'Carpal Tunnel Syndrome', 'Advise wrist splint and ergonomic changes at work.'),
-- Appointment 15: Neurologist
(15, '2024-06-03', 158, 55, 125, 'NO', 'Frequent, severe headaches, often on one side.', 'Migraine', 'Identify triggers. Prescribed medication for acute attacks.'),
-- Appointment 17: Cardiologist
(17, '2024-04-01', 172, 85, 160, 'PRE-DIABTIC', 'Shortness of breath and swollen ankles.', 'Congestive Heart Failure', 'Diuretics and ACE inhibitors prescribed. Low sodium diet.'),
-- Appointment 19: Pediatrician
(19, '2024-05-13', 105, 18, 108, 'NO', 'Ear pain and irritability.', 'Otitis Media (Ear Infection)', 'Prescribed antibiotics. Keep ear dry.');

-- #####################################################################
-- # 9. PAYMENT DATA (For all appointments)
-- #####################################################################
INSERT INTO payment_tbl (APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID) VALUES
(1, 500.00, '2024-04-15', 'CREDIT CARD', 'COMPLETED', 'txn_3k2j4h5g6f7d8s9a'),
(2, 500.00, '2024-07-15', 'UPI', 'COMPLETED', 'txn_l2k3j4h5g6f7d8s9'),
(3, 500.00, '2024-05-21', 'GOOGLE PAY', 'COMPLETED', 'txn_m3n4b5v6c7x8z9a1'),
(4, 500.00, '2024-07-20', 'NET BANKING', 'COMPLETED', 'txn_a1s2d3f4g5h6j7k8'),
(5, 800.00, '2024-03-18', 'UPI', 'COMPLETED', 'txn_q2w3e4r5t6y7u8i9'),
(6, 800.00, '2024-07-30', 'CREDIT CARD', 'COMPLETED', 'txn_z9x8c7v6b5n4m3a2'),
(7, 800.00, '2024-04-09', 'GOOGLE PAY', 'COMPLETED', 'txn_p1o2i3u4y5t6r7e8'),
(8, 800.00, '2024-08-01', 'UPI', 'COMPLETED', 'txn_h6g5f4d3s2a1q2w3'),
(9, 700.00, '2024-05-06', 'NET BANKING', 'COMPLETED', 'txn_j7h8g9f0d1s2a3q4'),
(10, 700.00, '2024-07-25', 'CREDIT CARD', 'COMPLETED', 'txn_k8l9m0n1b2v3c4x5'),
(11, 700.00, '2024-04-23', 'UPI', 'COMPLETED', 'txn_w2e3r4t5y6u7i8o9'),
(12, 700.00, '2024-08-05', 'GOOGLE PAY', 'COMPLETED', 'txn_p0o9i8u7y6t5r4e3'),
(13, 900.00, '2024-05-29', 'CREDIT CARD', 'COMPLETED', 'txn_l1k2j3h4g5f6d7s8'),
(14, 900.00, '2024-08-10', 'UPI', 'COMPLETED', 'txn_m3n4b5v6c7x8z9a2'),
(15, 900.00, '2024-06-03', 'NET BANKING', 'COMPLETED', 'txn_a4s5d6f7g8h9j0k1'),
(16, 900.00, '2024-08-13', 'GOOGLE PAY', 'COMPLETED', 'txn_q1w2e3r4t5y6u7i0'),
(17, 800.00, '2024-04-01', 'UPI', 'COMPLETED', 'txn_z2x3c4v5b6n7m8a1'),
(18, 800.00, '2024-07-28', 'CREDIT CARD', 'COMPLETED', 'txn_p3o4i5u6y7t8r9e2'),
(19, 500.00, '2024-05-13', 'GOOGLE PAY', 'COMPLETED', 'txn_l4k5j6h7g8f9d0s3'),
(20, 500.00, '2024-07-18', 'UPI', 'COMPLETED', 'txn_m6n7b8v9c0x1z2a4');

-- #####################################################################
-- # 10. FEEDBACK DATA (For COMPLETED appointments only)
-- #####################################################################
INSERT INTO feedback_tbl (APPOINTMENT_ID, RATING, COMMENTS) VALUES
(1, 5, 'Dr. Page was very gentle with my child. Excellent experience.'),
(3, 5, 'Dr. Holland explained the inhaler usage very clearly. Very satisfied.'),
(5, 4, 'Dr. Kumar is thorough. The consultation was good but the waiting time was a bit long.'),
(7, 5, 'Dr. Sharma is very empathetic and provided a clear plan of action.'),
(9, 5, 'Dr. Miller gave great advice and the physiotherapy referral was very helpful.'),
(11, 4, 'Good consultation. Dr. Jones diagnosed the problem quickly.'),
(13, 5, 'Finally, a doctor who understood my problem. Dr. Reddy is the best.'),
(15, 5, 'Dr. Lee is a true expert. His treatment plan has worked wonders.'),
(17, 5, 'Dr. Menon''s detailed explanation put my mind at ease. Highly recommend.'),
(19, 5, 'Very caring and professional. My daughter is feeling much better now.');


-- #####################################################################
-- # 11. PRESCRIPTION-MEDICINE LINK DATA
-- #####################################################################
INSERT INTO prescription_medicine_tbl (PRESCRIPTION_ID, MEDICINE_ID, DOSAGE, DURATION, FREQUENCY) VALUES
-- Prescription 1 (Common Cold)
(1, 1, '5ml', '3 days', 'Twice a day'),
-- Prescription 2 (Asthma)
(2, 3, '1-2 puffs', 'As needed', 'During attack'),
(2, 1, '5ml', '2 days', 'Three times a day'),
-- Prescription 3 (Angina)
(3, 4, '75mg', '1 month', 'Once a day'),
(3, 5, '20mg', '3 months', 'Once a day at night'),
-- Prescription 4 (Hypertension)
(4, 6, '50mg', '3 months', 'Twice a day'),
(4, 4, '75mg', '3 months', 'Once a day'),
-- Prescription 5 (Osteoarthritis)
(5, 7, '400mg', '10 days', 'Three times a day after food'),
(5, 8, '500mg', '2 months', 'Once a day'),
-- Prescription 6 (Muscle Strain)
(6, 7, '400mg', '5 days', 'Three times a day after food'),
(6, 9, '60,000 IU', '10 weeks', 'Once a week'),
-- Prescription 7 (Carpal Tunnel)
(7, 10, '75mg', '1 month', 'Twice a day'),
(7, 7, '400mg', '15 days', 'As needed for pain'),
-- Prescription 8 (Migraine)
(8, 10, '150mg', '1 month', 'Once a day at night'),
(8, 7, '400mg', 'As needed', 'For acute pain'),
-- Prescription 9 (Heart Failure)
(9, 6, '25mg', '1 month', 'Once a day'),
(9, 4, '75mg', '1 month', 'Once a day'),
-- Prescription 10 (Ear Infection)
(10, 2, '125mg/5ml', '7 days', 'Three times a day'),
(10, 1, '5ml', '3 days', 'As needed for fever');

-- #####################################################################
-- # 12. APPOINTMENT REMINDER DATA (3 reminders per appointment)
-- #####################################################################
INSERT INTO appointment_reminder_tbl (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) VALUES
-- Reminders for Appointment 1
(1, 1, '10:05:00', 'Appointment booked successfully.'),
(1, 1, '10:00:00', '24-hour reminder for your appointment.'),
(1, 1, '07:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 2
(1, 2, '11:05:00', 'Appointment booked successfully.'),
(1, 2, '11:00:00', '24-hour reminder for your appointment.'),
(1, 2, '08:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 3
(2, 3, '11:05:00', 'Appointment booked successfully.'),
(2, 3, '11:00:00', '24-hour reminder for your appointment.'),
(2, 3, '08:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 4
(2, 4, '14:05:00', 'Appointment booked successfully.'),
(2, 4, '14:00:00', '24-hour reminder for your appointment.'),
(2, 4, '11:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 5
(3, 5, '09:05:00', 'Appointment booked successfully.'),
(3, 5, '09:00:00', '24-hour reminder for your appointment.'),
(3, 5, '06:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 6
(3, 6, '10:05:00', 'Appointment booked successfully.'),
(3, 6, '10:00:00', '24-hour reminder for your appointment.'),
(3, 6, '07:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 7
(4, 7, '10:35:00', 'Appointment booked successfully.'),
(4, 7, '10:30:00', '24-hour reminder for your appointment.'),
(4, 7, '07:30:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 8
(4, 8, '11:35:00', 'Appointment booked successfully.'),
(4, 8, '11:30:00', '24-hour reminder for your appointment.'),
(4, 8, '08:30:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 9
(5, 9, '13:05:00', 'Appointment booked successfully.'),
(5, 9, '13:00:00', '24-hour reminder for your appointment.'),
(5, 9, '10:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 10
(5, 10, '12:05:00', 'Appointment booked successfully.'),
(5, 10, '12:00:00', '24-hour reminder for your appointment.'),
(5, 10, '09:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 11
(6, 11, '09:05:00', 'Appointment booked successfully.'),
(6, 11, '09:00:00', '24-hour reminder for your appointment.'),
(6, 11, '06:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 12
(6, 12, '10:05:00', 'Appointment booked successfully.'),
(6, 12, '10:00:00', '24-hour reminder for your appointment.'),
(6, 12, '07:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 13
(7, 13, '11:05:00', 'Appointment booked successfully.'),
(7, 13, '11:00:00', '24-hour reminder for your appointment.'),
(7, 13, '08:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 14
(7, 14, '14:05:00', 'Appointment booked successfully.'),
(7, 14, '14:00:00', '24-hour reminder for your appointment.'),
(7, 14, '11:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 15
(8, 15, '10:05:00', 'Appointment booked successfully.'),
(8, 15, '10:00:00', '24-hour reminder for your appointment.'),
(8, 15, '07:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 16
(8, 16, '11:05:00', 'Appointment booked successfully.'),
(8, 16, '11:00:00', '24-hour reminder for your appointment.'),
(8, 16, '08:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 17
(9, 17, '11:05:00', 'Appointment booked successfully.'),
(9, 17, '11:00:00', '24-hour reminder for your appointment.'),
(9, 17, '08:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 18
(9, 18, '09:35:00', 'Appointment booked successfully.'),
(9, 18, '09:30:00', '24-hour reminder for your appointment.'),
(9, 18, '06:30:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 19
(10, 19, '10:05:00', 'Appointment booked successfully.'),
(10, 19, '10:00:00', '24-hour reminder for your appointment.'),
(10, 19, '07:00:00', '3-hour reminder for your appointment.'),
-- Reminders for Appointment 20
(10, 20, '11:35:00', 'Appointment booked successfully.'),
(10, 20, '11:30:00', '24-hour reminder for your appointment.'),
(10, 20, '08:30:00', '3-hour reminder for your appointment.');


-- #####################################################################
-- # 13. MEDICINE REMINDER DATA
-- #####################################################################
INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, REMINDER_TIME, REMARKS) VALUES
-- For Patient 1 (Rohan)
(1, 'RECEPTIONIST', 1, 1, '08:00:00', 'Morning dose of Paracetamol'),
(1, 'RECEPTIONIST', 1, 1, '20:00:00', 'Evening dose of Paracetamol'),
-- For Patient 2 (Ananya)
(3, 'RECEPTIONIST', 2, 2, '00:00:00', 'Use inhaler as prescribed'),
(1, 'RECEPTIONIST', 2, 2, '08:00:00', 'Morning dose of Paracetamol'),
(1, 'RECEPTIONIST', 2, 2, '13:00:00', 'Afternoon dose of Paracetamol'),
(1, 'RECEPTIONIST', 2, 2, '18:00:00', 'Evening dose of Paracetamol'),
-- For Patient 3 (Sunil)
(4, 'RECEPTIONIST', 3, 3, '09:00:00', 'Daily Aspirin'),
(5, 'RECEPTIONIST', 3, 3, '21:00:00', 'Night dose of Atorvastatin'),
-- For Patient 4 (Meera)
(6, 'RECEPTIONIST', 4, 4, '08:00:00', 'Morning dose of Metoprolol'),
(6, 'RECEPTIONIST', 4, 4, '20:00:00', 'Evening dose of Metoprolol'),
(4, 'RECEPTIONIST', 4, 4, '09:00:00', 'Daily Aspirin'),
-- For Patient 5 (Karan)
(7, 'RECEPTIONIST', 5, 5, '09:00:00', 'Morning dose of Ibuprofen'),
(7, 'RECEPTIONIST', 5, 5, '14:00:00', 'Afternoon dose of Ibuprofen'),
(7, 'RECEPTIONIST', 5, 5, '19:00:00', 'Evening dose of Ibuprofen'),
(8, 'RECEPTIONIST', 5, 5, '10:00:00', 'Daily Calcium Supplement'),
-- For Patient 6 (Fatima)
(7, 'RECEPTIONIST', 6, 6, '09:00:00', 'Morning dose of Ibuprofen'),
(7, 'RECEPTIONIST', 6, 6, '14:00:00', 'Afternoon dose of Ibuprofen'),
(7, 'RECEPTIONIST', 6, 6, '19:00:00', 'Evening dose of Ibuprofen'),
(9, 'RECEPTIONIST', 6, 6, '10:00:00', 'Weekly Vitamin D'),
-- For Patient 7 (Amit)
(10, 'RECEPTIONIST', 7, 7, '09:00:00', 'Morning dose of Pregabalin'),
(10, 'RECEPTIONIST', 7, 7, '21:00:00', 'Evening dose of Pregabalin'),
(7, 'RECEPTIONIST', 7, 7, '15:00:00', 'Ibuprofen for pain as needed'),
-- For Patient 8 (Priyanka)
(10, 'RECEPTIONIST', 8, 8, '21:00:00', 'Night dose of Pregabalin'),
(7, 'RECEPTIONIST', 8, 8, '00:00:00', 'Ibuprofen for acute migraine attack'),
-- For Patient 9 (Vikram)
(6, 'RECEPTIONIST', 9, 9, '09:00:00', 'Daily dose of Metoprolol'),
(4, 'RECEPTIONIST', 9, 9, '10:00:00', 'Daily Aspirin'),
-- For Patient 10 (Leela)
(2, 'RECEPTIONIST', 10, 10, '08:00:00', 'Morning dose of Antibiotic'),
(2, 'RECEPTIONIST', 10, 10, '14:00:00', 'Afternoon dose of Antibiotic'),
(2, 'RECEPTIONIST', 10, 10, '20:00:00', 'Evening dose of Antibiotic'),
(1, 'RECEPTIONIST', 10, 10, '22:00:00', 'Paracetamol for fever as needed');










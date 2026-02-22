-- Fix: Allow deleting doctor schedules even when appointments reference them
-- Run this script once in phpMyAdmin (select quick_care database, then run SQL)

-- Step 1: Drop the existing foreign key
ALTER TABLE `appointment_tbl`
  DROP FOREIGN KEY `appointment_tbl_ibfk_3`;

-- Step 2: Modify SCHEDULE_ID to allow NULL (appointments will keep doctor/date/time when schedule is deleted)
ALTER TABLE `appointment_tbl`
  MODIFY `SCHEDULE_ID` int(11) DEFAULT NULL;

-- Step 3: Re-add the foreign key with ON DELETE SET NULL
ALTER TABLE `appointment_tbl`
  ADD CONSTRAINT `appointment_tbl_ibfk_3` 
  FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `doctor_schedule_tbl` (`SCHEDULE_ID`) 
  ON DELETE SET NULL ON UPDATE CASCADE;

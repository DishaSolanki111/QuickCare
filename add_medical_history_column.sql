-- Add MEDICAL_HISTORY_FILE column to patient_tbl
-- This column stores the file path for uploaded medical history documents

ALTER TABLE `patient_tbl` 
ADD COLUMN `MEDICAL_HISTORY_FILE` VARCHAR(255) DEFAULT NULL AFTER `ADDRESS`;

-- The column will store the relative path to the uploaded file
-- Example: uploads/medical_history/medical_history_1234567890_abc123.pdf

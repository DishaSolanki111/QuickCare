-- Create receptionist_notifications table for schedule/slot delete notifications
-- Run once in phpMyAdmin (select quick_care database, then run SQL)

CREATE TABLE IF NOT EXISTS `receptionist_notifications` (
  `RECEPTIONIST_NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MESSAGE` text NOT NULL,
  `TYPE` varchar(50) DEFAULT 'schedule_deleted',
  `CREATED_AT` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`RECEPTIONIST_NOTIFICATION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

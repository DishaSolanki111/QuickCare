<?php

namespace PHPMaker2026\Project2;

return [
    /**
     * User levels
     *
     * @var array<int, string, string>
     * [0] int User level ID
     * [1] string User level name
     * [2] string User level hierarchy
     */
    'user.levels' => [
    ['-2', 'Anonymous', '']
],

    /**
     * User roles
     *
     * @var array<int, string>
     * [0] int User level ID
     * [1] string User role name
     */
    'user.roles' => [
    ['-1', 'ROLE_ADMIN'],
    ['', 'ROLE_UNDEFINED']
],

    /**
     * User level permissions
     *
     * @var array<string, int, int>
     * [0] string Project ID + Table name
     * [1] int User level ID
     * [2] int Permissions
     */
    'user.level.privs' => [
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}appointment_reminder_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}appointment_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}doctor_schedule_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}doctor_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}feedback_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}medicine_reminder_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}medicine_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}password_resets', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}patient_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}payment_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}prescription_medicine_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}prescription_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}receptionist_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}specialisation_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}Appointment_report', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}view_appointment_report', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}view_patient_report', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}view_payment_report', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}view_doctor_report', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}notification_seen_tbl', '-2', '0'],
    ['{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}receptionist_notifications', '-2', '0']
],

    /**
     * Tables
     *
     * @var array<string, string, string, bool, string>
     * [0] string Table name
     * [1] string Table variable name
     * [2] string Table caption
     * [3] bool Allowed for update (for userpriv.php)
     * [4] string Project ID
     * [5] string URL (for AppController::index)
     */
    'user.level.tables' => [
    ['appointment_reminder_tbl', 'appointment_reminder_tbl', 'appointment reminder tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['appointment_tbl', 'appointment_tbl', 'appointment tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['doctor_schedule_tbl', 'doctor_schedule_tbl', 'doctor schedule tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['doctor_tbl', 'doctor_tbl', 'doctor tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['feedback_tbl', 'feedback_tbl', 'feedback tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['medicine_reminder_tbl', 'medicine_reminder_tbl', 'medicine reminder tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['medicine_tbl', 'medicine_tbl', 'medicine tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['password_resets', 'password_resets', 'password resets', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['patient_tbl', 'patient_tbl', 'patient tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['payment_tbl', 'payment_tbl', 'payment tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['prescription_medicine_tbl', 'prescription_medicine_tbl', 'prescription medicine tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['prescription_tbl', 'prescription_tbl', 'prescription tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['receptionist_tbl', 'receptionist_tbl', 'receptionist tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['specialisation_tbl', 'specialisation_tbl', 'specialisation tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['Appointment_report', 'Appointment_report', 'Appointment report', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['view_appointment_report', 'view_appointment_report', 'Appointment Report', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', 'ViewAppointmentReportList'],
    ['view_patient_report', 'view_patient_report', 'Patient Report', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['view_payment_report', 'view_payment_report', 'Payment report', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', 'ViewPaymentReportList'],
    ['view_doctor_report', 'view_doctor_report', 'Doctor report', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', 'ViewDoctorReportList'],
    ['notification_seen_tbl', 'notification_seen_tbl', 'notification seen tbl', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', ''],
    ['receptionist_notifications', 'receptionist_notifications', 'receptionist notifications', true, '{A46002E4-B4F6-47F2-B55F-A3278D4B1DC4}', '']
],
];

<?php

namespace PHPMaker2026\Project2;

// Language
$language = Language();

// Navbar menu
$topMenu = new Menu("navbar", true, true);
echo $topMenu->toScript();

// Sidebar menu
$sideMenu = new Menu("menu", true, false);
$sideMenu->addMenuItem(17, "mi_view_appointment_report", $language->menuPhrase("17", "MenuText"), "ViewAppointmentReportList", -1, "", true, false, false, "", "", false, true);
$sideMenu->addMenuItem(21, "mi_view_payment_report", $language->menuPhrase("21", "MenuText"), "ViewPaymentReportList", -1, "", true, false, false, "", "", false, true);
$sideMenu->addMenuItem(22, "mi_view_doctor_report", $language->menuPhrase("22", "MenuText"), "ViewDoctorReportList", -1, "", true, false, false, "", "", false, true);
echo $sideMenu->toScript();

<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/register' => [[['_route' => 'api.register', '_controller' => 'PHPMaker2026\\Project2\\ApiController::register'], null, ['POST' => 0, 'OPTIONS' => 1], null, false, false, null]],
        '/api/upload' => [[['_route' => 'api.upload', '_controller' => 'PHPMaker2026\\Project2\\ApiController::upload'], null, ['POST' => 0, 'OPTIONS' => 1], null, false, false, null]],
        '/api/jupload' => [[['_route' => 'api.jupload', '_controller' => 'PHPMaker2026\\Project2\\ApiController::jupload'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/api/lookup' => [[['_route' => 'api.lookup', '_controller' => 'PHPMaker2026\\Project2\\ApiController::lookup'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/session' => [[['_route' => 'session', '_controller' => 'PHPMaker2026\\Project2\\AppController::session'], null, ['GET' => 0, 'OPTIONS' => 1], null, false, false, null]],
        '/' => [[['_route' => 'index', '_controller' => 'PHPMaker2026\\Project2\\AppController::index'], null, ['GET' => 0], null, false, false, null]],
        '/RefundTblList' => [[['_route' => 'list.refund_tbl', '_controller' => 'PHPMaker2026\\Project2\\RefundTblController::list'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewAppointmentReportSearch' => [[['_route' => 'search.view_appointment_report', '_controller' => 'PHPMaker2026\\Project2\\ViewAppointmentReportController::search'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewDoctorReportSearch' => [[['_route' => 'search.view_doctor_report', '_controller' => 'PHPMaker2026\\Project2\\ViewDoctorReportController::search'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewFeedbackReportList' => [[['_route' => 'list.view_feedback_report', '_controller' => 'PHPMaker2026\\Project2\\ViewFeedbackReportController::list'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewPatientReportList' => [[['_route' => 'list.view_patient_report', '_controller' => 'PHPMaker2026\\Project2\\ViewPatientReportController::list'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewPaymentReportSearch' => [[['_route' => 'search.view_payment_report', '_controller' => 'PHPMaker2026\\Project2\\ViewPaymentReportController::search'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewPrescriptionReportList' => [[['_route' => 'list.view_prescription_report', '_controller' => 'PHPMaker2026\\Project2\\ViewPrescriptionReportController::list'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/ViewPrescriptionReportSearch' => [[['_route' => 'search.view_prescription_report', '_controller' => 'PHPMaker2026\\Project2\\ViewPrescriptionReportController::search'], null, ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, false, null]],
        '/logout' => [[['_route' => '_logout_main'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|list/([^/]++)(*:28)'
                    .'|view/([^/]++)/(.+)(*:53)'
                    .'|add/([^/]++)(?:/([^/]++))?(*:86)'
                    .'|e(?'
                        .'|dit/([^/]++)/(.+)(*:114)'
                        .'|xport/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:164)'
                    .')'
                    .'|delete/([^/]++)(?:/([^/]++))?(*:202)'
                    .'|file/([^/]++)/([^/]++)(?:/([^/]++))?(*:246)'
                    .'|cha(?'
                        .'|rt/(.+)(*:267)'
                        .'|t/([0-9]+)(*:285)'
                    .')'
                    .'|permissions/([0-9]+)(*:314)'
                    .'|twofa/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:364)'
                .')'
                .'|/AppointmentReport(?:/([^/]++))?(*:405)'
                .'|/RefundTbl(?'
                    .'|Add(?:/([^/]++))?(*:443)'
                    .'|View(?:/([^/]++))?(*:469)'
                    .'|Edit(?:/([^/]++))?(*:495)'
                    .'|Delete(?:/([^/]++))?(*:523)'
                .')'
                .'|/View(?'
                    .'|AppointmentReportList(?:/([^/]++))?(*:575)'
                    .'|DoctorReportList(?:/([^/]++))?(*:613)'
                    .'|PaymentReportList(?:/([^/]++))?(*:652)'
                .')'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:689)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        28 => [[['_route' => 'api.list', '_controller' => 'PHPMaker2026\\Project2\\ApiController::list'], ['table'], ['GET' => 0, 'OPTIONS' => 1], null, false, true, null]],
        53 => [[['_route' => 'api.view', '_controller' => 'PHPMaker2026\\Project2\\ApiController::view'], ['table', 'key'], ['GET' => 0, 'OPTIONS' => 1], null, false, true, null]],
        86 => [[['_route' => 'api.add', 'key' => null, '_controller' => 'PHPMaker2026\\Project2\\ApiController::add'], ['table', 'key'], ['POST' => 0, 'OPTIONS' => 1], null, false, true, null]],
        114 => [[['_route' => 'api.edit', '_controller' => 'PHPMaker2026\\Project2\\ApiController::edit'], ['table', 'key'], ['POST' => 0, 'OPTIONS' => 1], null, false, true, null]],
        164 => [[['_route' => 'api.export', 'table' => null, 'key' => null, '_controller' => 'PHPMaker2026\\Project2\\ApiController::export'], ['param', 'table', 'key'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        202 => [[['_route' => 'api.delete', 'key' => null, '_controller' => 'PHPMaker2026\\Project2\\ApiController::delete'], ['table', 'key'], ['GET' => 0, 'POST' => 1, 'DELETE' => 2, 'OPTIONS' => 3], null, false, true, null]],
        246 => [[['_route' => 'api.file', 'key' => null, '_controller' => 'PHPMaker2026\\Project2\\ApiController::getFile'], ['table', 'param', 'key'], ['GET' => 0, 'OPTIONS' => 1], null, false, true, null]],
        267 => [[['_route' => 'api.chart', '_controller' => 'PHPMaker2026\\Project2\\ApiController::chart'], ['params'], ['GET' => 0, 'OPTIONS' => 1], null, false, true, null]],
        285 => [[['_route' => 'api.chat', '_controller' => 'PHPMaker2026\\Project2\\ApiController::chat'], ['value'], ['GET' => 0], null, false, true, null]],
        314 => [[['_route' => 'api.permissions', '_controller' => 'PHPMaker2026\\Project2\\ApiController::permissions'], ['userLevel'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        364 => [[['_route' => 'api.twofa', 'authType' => null, 'parm' => null, '_controller' => 'PHPMaker2026\\Project2\\ApiController::twofa'], ['action', 'authType', 'parm'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        405 => [[['_route' => 'summary.appointment_report', 'chartName' => null, '_controller' => 'PHPMaker2026\\Project2\\AppointmentReportController'], ['chartName'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        443 => [[['_route' => 'add.refund_tbl', 'refundId' => null, '_route_mapping' => ['refundId' => 'refundTbl'], '_controller' => 'PHPMaker2026\\Project2\\RefundTblController::add'], ['refundId'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        469 => [[['_route' => 'view.refund_tbl', 'refundId' => null, '_route_mapping' => ['refundId' => 'refundTbl'], '_controller' => 'PHPMaker2026\\Project2\\RefundTblController::view'], ['refundId'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        495 => [[['_route' => 'edit.refund_tbl', 'refundId' => null, '_route_mapping' => ['refundId' => 'refundTbl'], '_controller' => 'PHPMaker2026\\Project2\\RefundTblController::edit'], ['refundId'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        523 => [[['_route' => 'delete.refund_tbl', 'refundId' => null, '_route_mapping' => ['refundId' => 'refundTbl'], '_controller' => 'PHPMaker2026\\Project2\\RefundTblController::delete'], ['refundId'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        575 => [[['_route' => 'list.view_appointment_report', 'chartName' => null, '_controller' => 'PHPMaker2026\\Project2\\ViewAppointmentReportController::list'], ['chartName'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        613 => [[['_route' => 'list.view_doctor_report', 'chartName' => null, '_controller' => 'PHPMaker2026\\Project2\\ViewDoctorReportController::list'], ['chartName'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        652 => [[['_route' => 'list.view_payment_report', 'chartName' => null, '_controller' => 'PHPMaker2026\\Project2\\ViewPaymentReportController::list'], ['chartName'], ['GET' => 0, 'POST' => 1, 'OPTIONS' => 2], null, false, true, null]],
        689 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];

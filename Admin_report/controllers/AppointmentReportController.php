<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\EventStreamResponse;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use PHPMaker2026\Project2\Db\Entity;

/**
 * AppointmentReport controller
 */
class AppointmentReportController extends BaseController
{
    // Summary Report
    #[Route('/AppointmentReport/{chartName?}', methods: ['GET', 'POST', 'OPTIONS'], name: 'summary.appointment_report')]
    public function __invoke(Request $request, AppointmentReportService $service, AppointmentReportSummary $page, ?string $chartName): Response
    {
        // Init page
        $page->init();

        // Get report and chart data
        $page->ReportData = $service->getReportData($page->Filter, $page->Sort, $page->PageNumber, $page->DisplayGroups);
        foreach ($page->Charts as $id => $chart) {
            $page->ChartData[$id] = $service->getChartData($chart, $page->Filter, $page->Sort);
        }

        // Run page
        if ($chartName) {
            return $this->runChart($page, $chartName);
        } else {
            return $this->runPage($page);
        }
    }
}

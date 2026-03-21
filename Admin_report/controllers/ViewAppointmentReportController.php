<?php

namespace PHPMaker2026\Project2;

use Symfony\Component\DependencyInjection\ContainerInterface;
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
use PHPMaker2026\Project2\Db\Entity;

/**
 * ViewAppointmentReport controller
 */
class ViewAppointmentReportController extends BaseController
{
    // list
    #[Route('/ViewAppointmentReportList/{chartName}', methods: ['GET', 'POST', 'OPTIONS'], name: 'list.view_appointment_report')]
    public function list(Request $request, ViewAppointmentReportList $page, ?string $chartName = null): Response
    {
        // Init page
        $page->init();

        // Check resolved arguments
        $hasResolved = false;

        // Perform inline/grid actions
        if ($response = $page->action()) {
            return $response;
        }
        $page->TotalRecords = $page->listRecordCount();
        if (!$page->Records) {
            $page->Records = $page->loadRecords($page->StartRecord - 1, $page->DisplayRecords);
        }

        // Get chart data
        foreach ($page->Charts as $id => $chart) {
            $chartService = new ChartService($chart, $this);
            $page->ChartData[$id] = $chartService->getChartData($page->Filter, $page->getOrderBy());
        }

        // Run page
        return $chartName ? $this->runChart($page, $chartName) : $this->runPage($page);
    }

    // search
    #[Route('/ViewAppointmentReportSearch', methods: ['GET', 'POST', 'OPTIONS'], name: 'search.view_appointment_report')]
    public function search(Request $request, ViewAppointmentReportSearch $page): Response
    {
        // Check resolved arguments
        $hasResolved = false;

        // Run page
        return $this->runPage($page);
    }
}

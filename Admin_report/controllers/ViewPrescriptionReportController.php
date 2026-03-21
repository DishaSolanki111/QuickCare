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
 * ViewPrescriptionReport controller
 */
class ViewPrescriptionReportController extends BaseController
{
    // list
    #[Route('/ViewPrescriptionReportList', methods: ['GET', 'POST', 'OPTIONS'], name: 'list.view_prescription_report')]
    public function list(Request $request, ViewPrescriptionReportList $page): Response
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

        // Run page
        return $this->runPage($page);
    }

    // search
    #[Route('/ViewPrescriptionReportSearch', methods: ['GET', 'POST', 'OPTIONS'], name: 'search.view_prescription_report')]
    public function search(Request $request, ViewPrescriptionReportSearch $page): Response
    {
        // Check resolved arguments
        $hasResolved = false;

        // Run page
        return $this->runPage($page);
    }
}

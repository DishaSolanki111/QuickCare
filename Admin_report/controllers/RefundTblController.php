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
 * RefundTbl controller
 */
class RefundTblController extends BaseController
{
    // list
    #[Route('/RefundTblList', methods: ['GET', 'POST', 'OPTIONS'], name: 'list.refund_tbl')]
    public function list(Request $request, RefundTblList $page): Response
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

    // add
    #[Route('/RefundTblAdd/{refundId:refundTbl?}', methods: ['GET', 'POST', 'OPTIONS'], name: 'add.refund_tbl')]
    public function add(Request $request, RefundTblAdd $page, ?Entity\RefundTbl $refundTbl = null): Response
    {
        // Init page
        $page->init();

        // Check resolved arguments
        $hasResolved = false;

        // Set current record
        if ($refundTbl) {
            $page->CurrentRecord = $refundTbl;
            $hasResolved = true;
        }

        // Run page
        return $this->runPage($page);
    }

    // view
    #[Route('/RefundTblView/{refundId:refundTbl?}', methods: ['GET', 'POST', 'OPTIONS'], name: 'view.refund_tbl')]
    public function view(Request $request, RefundTblView $page, ?Entity\RefundTbl $refundTbl = null): Response
    {
        // Init page
        $page->init();

        // Check resolved arguments
        $hasResolved = false;

        // Set current record
        if ($refundTbl) {
            $page->CurrentRecord = $refundTbl;
            $hasResolved = true;
        }

        // Run page
        return $this->runPage($page);
    }

    // edit
    #[Route('/RefundTblEdit/{refundId:refundTbl?}', methods: ['GET', 'POST', 'OPTIONS'], name: 'edit.refund_tbl')]
    public function edit(Request $request, RefundTblEdit $page, ?Entity\RefundTbl $refundTbl = null): Response
    {
        // Init page
        $page->init();

        // Check resolved arguments
        $hasResolved = false;

        // Set current record
        if ($refundTbl) {
            $page->CurrentRecord = $refundTbl;
            $hasResolved = true;
        }

        // Run page
        return $this->runPage($page);
    }

    // delete
    #[Route('/RefundTblDelete/{refundId:refundTbl?}', methods: ['GET', 'POST', 'OPTIONS'], name: 'delete.refund_tbl')]
    public function delete(Request $request, RefundTblDelete $page, ?Entity\RefundTbl $refundTbl = null): Response
    {
        // Check resolved arguments
        $hasResolved = false;

        // Set current record
        if ($refundTbl) {
            $page->CurrentRecord = $refundTbl;
            $hasResolved = true;
        }

        // Run page
        return $this->runPage($page);
    }
}

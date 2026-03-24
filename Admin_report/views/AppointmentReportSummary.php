<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport() && !$Page->DrillDown && !$DashboardReport) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { Appointment_report: currentTable } });
var currentPageID = ew.PAGE_ID = "summary";
var currentForm;
</script>
<script<?= Nonce() ?>>
ew.on("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php } ?>
<a id="top"></a>
<!-- Content Container -->
<div id="ew-report" class="ew-report container-fluid">
<div class="btn-toolbar ew-toolbar">
<?php
if (!$Page->DrillDownInPanel) {
    $Page->ExportOptions->render("body");
    $Page->SearchOptions->render("body");
    $Page->FilterOptions->render("body");
}
?>
</div>
<?php if (!$Page->isExport() && !$Page->DrillDown && !$DashboardReport) { ?>
<?php } ?>
<?= $Page->getPageHeader() ?>
<?= $Page->getHtmlMessage() ?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
<!-- Middle Container -->
<div id="ew-middle" class="<?= $Page->MiddleContentClass ?>">
<?php } ?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
<!-- Content Container -->
<div id="ew-content" class="<?= $Page->ContainerClass ?>">
<?php } ?>
<?php if ($Page->ShowReport) { ?>
<!-- Summary report (begin) -->
<main class="report-summary<?= ($Page->TotalGroups == 0) ? " ew-no-record" : "" ?>">
<?php
foreach ($Page->ReportData->appointmentDateGroups as $appointmentDateGroup) {
?>
<?php
    // Show header
    if ($Page->ShowHeader) {
?>
<?php if ($Page->GroupCount > 1) { ?>
</tbody>
</table>
</div>
<!-- /.ew-grid-middle-panel -->
<!-- Report grid (end) -->
</div>
<!-- /.ew-grid -->
<?= $Page->PageBreakHtml ?>
<?php } ?>
<div class="<?= $Page->ReportContainerClass ?>">
<?php if (!$Page->isExport() && !($Page->DrillDown && $Page->TotalGroups > 0) && $Page->Pager->Visible) { ?>
<!-- Top pager -->
<div class="card-header ew-grid-upper-panel">
<?= $Page->Pager?->render() ?>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<div id="gmp_Appointment_report" class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<table class="<?= $Page->TableClass ?>">
<thead>
	<!-- Table header -->
    <tr class="ew-table-header">
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
    <?php if ($Page->APPOINTMENT_DATE->ShowGroupHeaderAsRow) { ?>
    <th data-name="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes("ew-rpt-grp-caret") ?>><?= $Page->APPOINTMENT_DATE->groupToggleIcon() ?></th>
    <?php } else { ?>
    <th data-name="APPOINTMENT_DATE" class="<?= $Page->APPOINTMENT_DATE->headerCellClass() ?>"><div class="Appointment_report_APPOINTMENT_DATE"><?= $Page->renderFieldHeader($Page->APPOINTMENT_DATE) ?></div></th>
    <?php } ?>
<?php } ?>
<?php if ($Page->STATUS->Visible) { ?>
    <?php if ($Page->STATUS->ShowGroupHeaderAsRow) { ?>
    <th data-name="STATUS">&nbsp;</th>
    <?php } else { ?>
    <th data-name="STATUS" class="<?= $Page->STATUS->headerCellClass() ?>"><div class="Appointment_report_STATUS"><?= $Page->renderFieldHeader($Page->STATUS) ?></div></th>
    <?php } ?>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
    <th data-name="APPOINTMENT_ID" class="<?= $Page->APPOINTMENT_ID->headerCellClass() ?>"><div class="Appointment_report_APPOINTMENT_ID"><?= $Page->renderFieldHeader($Page->APPOINTMENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
    <th data-name="PATIENT_ID" class="<?= $Page->PATIENT_ID->headerCellClass() ?>"><div class="Appointment_report_PATIENT_ID"><?= $Page->renderFieldHeader($Page->PATIENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
    <th data-name="DOCTOR_ID" class="<?= $Page->DOCTOR_ID->headerCellClass() ?>"><div class="Appointment_report_DOCTOR_ID"><?= $Page->renderFieldHeader($Page->DOCTOR_ID) ?></div></th>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
    <th data-name="APPOINTMENT_TIME" class="<?= $Page->APPOINTMENT_TIME->headerCellClass() ?>"><div class="Appointment_report_APPOINTMENT_TIME"><?= $Page->renderFieldHeader($Page->APPOINTMENT_TIME) ?></div></th>
<?php } ?>
    </tr>
</thead>
<tbody>
<?php
        if ($Page->TotalGroups == 0) {
            break; // Show header only
        }
        $Page->ShowHeader = false;
    } // End show header
?>
<?php
?>
<?php $Page->renderGroupHeaderSummary(0, $appointmentDateGroup); ?>
<?php if ($Page->APPOINTMENT_DATE->Visible && $Page->APPOINTMENT_DATE->ShowGroupHeaderAsRow) { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes("ew-rpt-grp-caret") ?>><?= $Page->APPOINTMENT_DATE->groupToggleIcon() ?></td>
<?php } ?>
        <td data-field="APPOINTMENT_DATE" colspan="<?= ($Page->GroupColumnCount + $Page->DetailColumnCount - 1) ?>"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
            <span class="ew-summary-caption Appointment_report_APPOINTMENT_DATE"><?= $Page->renderFieldHeader($Page->APPOINTMENT_DATE) ?></span><?= Language()->phrase("SummaryColon") ?><span<?= $Page->APPOINTMENT_DATE->viewAttributes() ?>><?= $Page->APPOINTMENT_DATE->GroupViewValue ?></span>
            <span class="ew-summary-count">(<span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->APPOINTMENT_DATE->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span>)</span>
        </td>
    </tr>
<?php } ?>
<?php
    foreach ($appointmentDateGroup->statusGroups as $statusGroup) {
?>
<?php $Page->renderGroupHeaderSummary(1, $statusGroup); ?>
<?php if ($Page->STATUS->Visible && $Page->STATUS->ShowGroupHeaderAsRow) { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->STATUS->Visible) { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes("ew-rpt-grp-caret") ?>><?= $Page->STATUS->groupToggleIcon() ?></td>
<?php } ?>
        <td data-field="STATUS" colspan="<?= ($Page->GroupColumnCount + $Page->DetailColumnCount - 2) ?>"<?= $Page->STATUS->cellAttributes() ?>>
            <span class="ew-summary-caption Appointment_report_STATUS"><?= $Page->renderFieldHeader($Page->STATUS) ?></span><?= Language()->phrase("SummaryColon") ?><span<?= $Page->STATUS->viewAttributes() ?>><?= $Page->STATUS->GroupViewValue ?></span>
            <span class="ew-summary-count">(<span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->STATUS->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span>)</span>
        </td>
    </tr>
<?php } ?>
<?php
        foreach ($statusGroup->details as $detail) {
?>
<?php $Page->renderDetail($detail); ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
    <?php if ($Page->APPOINTMENT_DATE->ShowGroupHeaderAsRow) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
    <?php } else { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>><span<?= $Page->APPOINTMENT_DATE->viewAttributes() ?>><?= $Page->APPOINTMENT_DATE->GroupViewValue ?></span></td>
    <?php } ?>
<?php } ?>
<?php if ($Page->STATUS->Visible) { ?>
    <?php if ($Page->STATUS->ShowGroupHeaderAsRow) { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes() ?>></td>
    <?php } else { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes() ?>><span<?= $Page->STATUS->viewAttributes() ?>><?= $Page->STATUS->GroupViewValue ?></span></td>
    <?php } ?>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->getViewValue() ?></span>
</td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span<?= $Page->PATIENT_ID->viewAttributes() ?>>
<?= $Page->PATIENT_ID->getViewValue() ?></span>
</td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->DOCTOR_ID->cellAttributes() ?>>
<span<?= $Page->DOCTOR_ID->viewAttributes() ?>>
<?= $Page->DOCTOR_ID->getViewValue() ?></span>
</td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_TIME->cellAttributes() ?>>
<span<?= $Page->APPOINTMENT_TIME->viewAttributes() ?>>
<?= $Page->APPOINTMENT_TIME->getViewValue() ?></span>
</td>
<?php } ?>
    </tr>
<?php
    }
?>
<?php if ($Page->TotalGroups > 0) { ?>
<?php $Page->renderGroupFooterSummary(1, $statusGroup); ?>
<?php if ($Page->STATUS->ShowCompactSummaryFooter) { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
    <?php if ($Page->APPOINTMENT_DATE->ShowGroupHeaderAsRow) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
    <?php } elseif ($Page->RowGroupLevel != 1) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
        </td>
    <?php } else { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
            <span class="ew-summary-count"><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->APPOINTMENT_DATE->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span></span>
        </td>
    <?php } ?>
<?php } ?>
<?php if ($Page->STATUS->Visible) { ?>
    <?php if ($Page->STATUS->ShowGroupHeaderAsRow) { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes() ?>></td>
    <?php } elseif ($Page->RowGroupLevel != 2) { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes() ?>>
        </td>
    <?php } else { ?>
        <td data-field="STATUS"<?= $Page->STATUS->cellAttributes() ?>>
            <span class="ew-summary-count"><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->STATUS->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span></span>
        </td>
    <?php } ?>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->STATUS->cellAttributes() ?>><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>><?= $Page->APPOINTMENT_ID->CountViewValue ?></span></span></td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } else { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->SubGroupColumnCount + $Page->DetailColumnCount > 0) { ?>
        <td colspan="<?= ($Page->SubGroupColumnCount + $Page->DetailColumnCount) ?>"<?= $Page->STATUS->cellAttributes() ?>><?= sprintf(Language()->phrase("RptSumHead"), $Page->STATUS->caption(), $Page->STATUS->GroupViewValue) ?> <span class="ew-summary-count ew-dir-ltr">(<?= FormatNumber($Page->STATUS->Count, Config("DEFAULT_NUMBER_FORMAT")) ?><?= Language()->phrase("RptDtlRec") ?>)</span></td>
<?php } ?>
    </tr>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->GroupColumnCount > 0) { ?>
        <td colspan="<?= ($Page->GroupColumnCount - 1) ?>"<?= $Page->STATUS->cellAttributes() ?>><?= Language()->phrase("RptCnt") ?></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->CountViewValue ?></span>
</td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->STATUS->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } ?>
<?php } ?>
<?php
    } // End group level 1
?>
<?php if ($Page->TotalGroups > 0) { ?>
<?php $Page->renderGroupFooterSummary(0, $appointmentDateGroup); ?>
<?php if ($Page->APPOINTMENT_DATE->ShowCompactSummaryFooter) { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->APPOINTMENT_DATE->Visible) { ?>
    <?php if ($Page->APPOINTMENT_DATE->ShowGroupHeaderAsRow) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
    <?php } elseif ($Page->RowGroupLevel != 1) { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
        </td>
    <?php } else { ?>
        <td data-field="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
            <span class="ew-summary-count"><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->APPOINTMENT_DATE->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span></span>
        </td>
    <?php } ?>
<?php } ?>
<?php if ($Page->STATUS->Visible) { ?>
    <?php if ($Page->STATUS->ShowGroupHeaderAsRow) { ?>
        <td data-field="STATUS"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
    <?php } elseif ($Page->RowGroupLevel != 2) { ?>
        <td data-field="STATUS"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
        </td>
    <?php } else { ?>
        <td data-field="STATUS"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
            <span class="ew-summary-count"><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->STATUS->Count, Config("DEFAULT_NUMBER_FORMAT")) ?></span></span>
        </td>
    <?php } ?>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>><?= $Page->APPOINTMENT_ID->CountViewValue ?></span></span></td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } else { ?>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->GroupColumnCount + $Page->DetailColumnCount > 0) { ?>
        <td colspan="<?= ($Page->GroupColumnCount + $Page->DetailColumnCount) ?>"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>><?= sprintf(Language()->phrase("RptSumHead"), $Page->APPOINTMENT_DATE->caption(), $Page->APPOINTMENT_DATE->GroupViewValue) ?> <span class="ew-summary-count ew-dir-ltr">(<?= FormatNumber($Page->APPOINTMENT_DATE->Count, Config("DEFAULT_NUMBER_FORMAT")) ?><?= Language()->phrase("RptDtlRec") ?>)</span></td>
<?php } ?>
    </tr>
    <tr<?= $Page->rowAttributes(); ?>>
<?php if ($Page->GroupColumnCount > 0) { ?>
        <td colspan="<?= ($Page->GroupColumnCount - 0) ?>"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>><?= Language()->phrase("RptCnt") ?></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->CountViewValue ?></span>
</td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } ?>
<?php } ?>
<?php
?>
<?php
    $Page->GroupCount++;
} // End while
?>
<?php if ($Page->TotalGroups > 0) { ?>
</tbody>
<tfoot>
<?php $Page->renderGrandSummary(); ?>
<?php if ($Page->APPOINTMENT_DATE->ShowCompactSummaryFooter) { ?>
    <tr<?= $Page->rowAttributes() ?>><td colspan="<?= ($Page->GroupColumnCount + $Page->DetailColumnCount) ?>"><?= Language()->phrase("RptGrandSummary") ?> <span class="ew-summary-count">(<span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><?= FormatNumber($Page->TotalCount, Config("DEFAULT_NUMBER_FORMAT")) ?></span>)</span></td></tr>
    <tr<?= $Page->rowAttributes() ?>>
<?php if ($Page->GroupColumnCount > 0) { ?>
        <td colspan="<?= $Page->GroupColumnCount ?>" class="ew-rpt-grp-aggregate"></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>><span class="ew-aggregate-caption"><?= Language()->phrase("RptCnt") ?></span><span class="ew-aggregate-equal"><?= Language()->phrase("AggregateEqual") ?></span><span class="ew-aggregate-value"><span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>><?= $Page->APPOINTMENT_ID->CountViewValue ?></span></span></td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->DOCTOR_ID->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_TIME->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } else { ?>
    <tr<?= $Page->rowAttributes() ?>><td colspan="<?= ($Page->GroupColumnCount + $Page->DetailColumnCount) ?>"><?= Language()->phrase("RptGrandSummary") ?> <span class="ew-summary-count">(<?= FormatNumber($Page->TotalCount, Config("DEFAULT_NUMBER_FORMAT")) ?><?= Language()->phrase("RptDtlRec") ?>)</span></td></tr>
    <tr<?= $Page->rowAttributes() ?>>
<?php if ($Page->GroupColumnCount > 0) { ?>
        <td colspan="<?= $Page->GroupColumnCount ?>" class="ew-rpt-grp-aggregate"><?= Language()->phrase("RptCnt") ?></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { ?>
        <td data-field="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
    <span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
    <?= $Page->APPOINTMENT_ID->CountViewValue ?></span>
    </td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { ?>
        <td data-field="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->DOCTOR_ID->Visible) { ?>
        <td data-field="DOCTOR_ID"<?= $Page->DOCTOR_ID->cellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { ?>
        <td data-field="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_TIME->cellAttributes() ?>></td>
<?php } ?>
    </tr>
<?php } ?>
</tfoot>
</table>
</div>
<!-- /.ew-grid-middle-panel -->
<!-- Report grid (end) -->
</div>
<!-- /.ew-grid -->
<?php } ?>
</main>
<!-- /.report-summary -->
<!-- Summary report (end) -->
<?php } ?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
</div>
<!-- /#ew-content -->
<?php } ?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
</div>
<!-- /#ew-middle -->
<?php } ?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
<!-- Bottom Container -->
<div id="ew-bottom" class="<?= $Page->BottomContentClass ?>">
<?php } ?>
<?php
if (!$DashboardReport) {
    // Set up chart drilldown
    $Page->Chart1->DrillDownInPanel = $Page->DrillDownInPanel;
    echo $Page->Chart1->render($Page->ChartData, "ew-chart-bottom");
}
?>
<?php if ((!$Page->isExport() || $Page->isExport("print")) && !$DashboardReport) { ?>
</div>
<!-- /#ew-bottom -->
<?php } ?>
<?php if (!$DashboardReport && !$Page->isExport() && !$Page->DrillDown) { ?>
<div class="mb-3"><a class="ew-top-link" data-ew-action="scroll-top"><?= Language()->phrase("Top") ?></a></div>
<?php } ?>
</div>
<!-- /.ew-report -->
<?= $Page->getPageFooter() ?>
<?php if (!$Page->isExport() && !$Page->DrillDown && !$DashboardReport) { ?>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

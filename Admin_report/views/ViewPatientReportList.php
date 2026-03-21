<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_patient_report: currentTable } });
var currentPageID = ew.PAGE_ID = "list";
var currentForm;
var <?= $Page->FormName ?>;
ew.on("wrapper", function () {
    let $ = jQuery;
    let fields = currentTable.fields;

    // Form object
    let form = new ew.FormBuilder()
        .setId("<?= $Page->FormName ?>")
        .setPageId("list")
        .setSubmitWithFetch(<?= $Page->UseAjaxActions ? "true" : "false" ?>)
        .setFormKeyCountName("<?= $Page->getFormKeyCountName() ?>")
        .build();
    window[form.id] = form;
    currentForm = form;
    ew.emit(form.id);
});
</script>
<script<?= Nonce() ?>>
ew.on("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php } ?>
<?php if (!$Page->isExport()) { ?>
<div class="btn-toolbar ew-toolbar">
<?php if ($Page->TotalRecords > 0) { ?>
<?= $Page->ExportOptions->render("body") ?>
<?php } ?>
<?= $Page->ImportOptions->render("body") ?>
<?= $Page->SearchOptions->render("body") ?>
<?= $Page->FilterOptions->render("body") ?>
</div>
<?php } ?>
<?php if (!$Page->IsModal) { ?>
<form name="fview_patient_reportsrch" id="fview_patient_reportsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>" novalidate autocomplete="off">
<div id="fview_patient_reportsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_patient_report: currentTable } });
var currentForm;
var fview_patient_reportsrch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_patient_reportsrch")
        .setPageId("list")
<?php if ($Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Dynamic selection lists
        .setLists({
        })

        // Filters
        .setFilterList(<?= $Page->getFilterList() ?>)
        .build();
    window[form.id] = form;
    currentSearchForm = form;
    ew.emit(form.id);
});
</script>
<input type="hidden" name="cmd" value="search">
<?php if (!$Page->isExport() && !($Page->CurrentAction && $Page->CurrentAction != "search") && $Page->hasSearchFields()) { ?>
<div class="ew-extended-search container-fluid ps-2">
<div class="row mb-0">
    <div class="col-sm-auto px-0 pe-sm-2">
        <div class="ew-basic-search input-group">
            <input type="search" name="<?= Config("TABLE_BASIC_SEARCH") ?>" id="<?= Config("TABLE_BASIC_SEARCH") ?>" class="form-control ew-basic-search-keyword" value="<?= HtmlEncode($Page->BasicSearch->getKeyword()) ?>" placeholder="<?= HtmlEncode(Language()->phrase("Search")) ?>" aria-label="<?= HtmlEncode(Language()->phrase("Search")) ?>">
            <input type="hidden" name="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" id="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" class="ew-basic-search-type" value="<?= HtmlEncode($Page->BasicSearch->getType()) ?>">
            <button type="button" data-bs-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" aria-haspopup="true" aria-expanded="false">
                <span id="searchtype"><?= $Page->BasicSearch->getTypeNameShort() ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fview_patient_reportsrch" data-ew-action="search-type"><?= Language()->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fview_patient_reportsrch" data-ew-action="search-type" data-search-type="="><?= Language()->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fview_patient_reportsrch" data-ew-action="search-type" data-search-type="AND"><?= Language()->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fview_patient_reportsrch" data-ew-action="search-type" data-search-type="OR"><?= Language()->phrase("QuickSearchAny") ?></button>
            </div>
        </div>
    </div>
    <div class="col-sm-auto mb-3">
        <button class="btn btn-primary ew-submit" name="btn-submit" id="btn-submit" type="submit"><?= Language()->phrase("SearchBtn") ?></button>
    </div>
</div>
</div><!-- /.ew-extended-search -->
<?php } ?>
</div><!-- /.ew-search-panel -->
</form>
<?php } ?>
<?= $Page->getPageHeader() ?>
<?= $Page->getHtmlMessage() ?>
<main class="list<?= ($Page->TotalRecords == 0 && !$Page->isAdd()) ? " ew-no-record" : "" ?>">
<div id="ew-header-options">
<?php $Page->HeaderOptions?->render("body") ?>
</div>
<div id="ew-list">
<?php if ($Page->TotalRecords > 0 || $Page->CurrentAction) { ?>
<div class="card ew-card ew-grid<?= $Page->isAddOrEdit() ? " ew-grid-add-edit" : "" ?> <?= $Page->TableGridClass ?>">
<?php $formAction = GetUrl(UrlFor("list.view_patient_report", $Page->getUrlKey(true))) ?>
<form name="<?= $Page->FormName ?>" id="<?= $Page->FormName ?>" class="ew-form ew-list-form" action="<?= $formAction ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_patient_report">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div id="gmp_view_patient_report" class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit() || $Page->isMultiEdit()) { ?>
<table id="tbl_view_patient_reportlist" class="<?= $Page->TableClass ?>"><!-- .ew-table -->
<thead>
    <tr class="ew-table-header">
<?php
// Header row
$Page->RowType = RowType::HEADER;

// Render list options
$Page->renderListOptions();

// Render list options (header, left)
$Page->ListOptions->render("header", "left");
?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <th data-name="PATIENT_ID" class="<?= $Page->PATIENT_ID->headerCellClass() ?>"><div id="elh_view_patient_report_PATIENT_ID" class="view_patient_report_PATIENT_ID"><?= $Page->renderFieldHeader($Page->PATIENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <th data-name="Patient_Name" class="<?= $Page->Patient_Name->headerCellClass() ?>"><div id="elh_view_patient_report_Patient_Name" class="view_patient_report_Patient_Name"><?= $Page->renderFieldHeader($Page->Patient_Name) ?></div></th>
<?php } ?>
<?php if ($Page->GENDER->Visible) { // GENDER ?>
        <th data-name="GENDER" class="<?= $Page->GENDER->headerCellClass() ?>"><div id="elh_view_patient_report_GENDER" class="view_patient_report_GENDER"><?= $Page->renderFieldHeader($Page->GENDER) ?></div></th>
<?php } ?>
<?php if ($Page->BLOOD_GROUP->Visible) { // BLOOD_GROUP ?>
        <th data-name="BLOOD_GROUP" class="<?= $Page->BLOOD_GROUP->headerCellClass() ?>"><div id="elh_view_patient_report_BLOOD_GROUP" class="view_patient_report_BLOOD_GROUP"><?= $Page->renderFieldHeader($Page->BLOOD_GROUP) ?></div></th>
<?php } ?>
<?php if ($Page->PHONE->Visible) { // PHONE ?>
        <th data-name="PHONE" class="<?= $Page->PHONE->headerCellClass() ?>"><div id="elh_view_patient_report_PHONE" class="view_patient_report_PHONE"><?= $Page->renderFieldHeader($Page->PHONE) ?></div></th>
<?php } ?>
<?php if ($Page->EMAIL->Visible) { // EMAIL ?>
        <th data-name="EMAIL" class="<?= $Page->EMAIL->headerCellClass() ?>"><div id="elh_view_patient_report_EMAIL" class="view_patient_report_EMAIL"><?= $Page->renderFieldHeader($Page->EMAIL) ?></div></th>
<?php } ?>
<?php if ($Page->Total_Appointments->Visible) { // Total_Appointments ?>
        <th data-name="Total_Appointments" class="<?= $Page->Total_Appointments->headerCellClass() ?>"><div id="elh_view_patient_report_Total_Appointments" class="view_patient_report_Total_Appointments"><?= $Page->renderFieldHeader($Page->Total_Appointments) ?></div></th>
<?php } ?>
<?php if ($Page->Completed_Appointments->Visible) { // Completed_Appointments ?>
        <th data-name="Completed_Appointments" class="<?= $Page->Completed_Appointments->headerCellClass() ?>"><div id="elh_view_patient_report_Completed_Appointments" class="view_patient_report_Completed_Appointments"><?= $Page->renderFieldHeader($Page->Completed_Appointments) ?></div></th>
<?php } ?>
<?php if ($Page->Upcoming_Appointments->Visible) { // Upcoming_Appointments ?>
        <th data-name="Upcoming_Appointments" class="<?= $Page->Upcoming_Appointments->headerCellClass() ?>"><div id="elh_view_patient_report_Upcoming_Appointments" class="view_patient_report_Upcoming_Appointments"><?= $Page->renderFieldHeader($Page->Upcoming_Appointments) ?></div></th>
<?php } ?>
<?php if ($Page->Cancelled_Appointments->Visible) { // Cancelled_Appointments ?>
        <th data-name="Cancelled_Appointments" class="<?= $Page->Cancelled_Appointments->headerCellClass() ?>"><div id="elh_view_patient_report_Cancelled_Appointments" class="view_patient_report_Cancelled_Appointments"><?= $Page->renderFieldHeader($Page->Cancelled_Appointments) ?></div></th>
<?php } ?>
<?php if ($Page->Last_Visit->Visible) { // Last_Visit ?>
        <th data-name="Last_Visit" class="<?= $Page->Last_Visit->headerCellClass() ?>"><div id="elh_view_patient_report_Last_Visit" class="view_patient_report_Last_Visit"><?= $Page->renderFieldHeader($Page->Last_Visit) ?></div></th>
<?php } ?>
<?php if ($Page->First_Visit->Visible) { // First_Visit ?>
        <th data-name="First_Visit" class="<?= $Page->First_Visit->headerCellClass() ?>"><div id="elh_view_patient_report_First_Visit" class="view_patient_report_First_Visit"><?= $Page->renderFieldHeader($Page->First_Visit) ?></div></th>
<?php } ?>
<?php if ($Page->Total_Prescriptions->Visible) { // Total_Prescriptions ?>
        <th data-name="Total_Prescriptions" class="<?= $Page->Total_Prescriptions->headerCellClass() ?>"><div id="elh_view_patient_report_Total_Prescriptions" class="view_patient_report_Total_Prescriptions"><?= $Page->renderFieldHeader($Page->Total_Prescriptions) ?></div></th>
<?php } ?>
<?php if ($Page->Total_Amount_Paid->Visible) { // Total_Amount_Paid ?>
        <th data-name="Total_Amount_Paid" class="<?= $Page->Total_Amount_Paid->headerCellClass() ?>"><div id="elh_view_patient_report_Total_Amount_Paid" class="view_patient_report_Total_Amount_Paid"><?= $Page->renderFieldHeader($Page->Total_Amount_Paid) ?></div></th>
<?php } ?>
<?php if ($Page->Avg_Rating_Given->Visible) { // Avg_Rating_Given ?>
        <th data-name="Avg_Rating_Given" class="<?= $Page->Avg_Rating_Given->headerCellClass() ?>"><div id="elh_view_patient_report_Avg_Rating_Given" class="view_patient_report_Avg_Rating_Given"><?= $Page->renderFieldHeader($Page->Avg_Rating_Given) ?></div></th>
<?php } ?>
<?php
// Render list options (header, right)
$Page->ListOptions->render("header", "right");
?>
    </tr>
</thead>
<tbody data-page="<?= $Page->getPageNumber() ?>">
<?php
while ($Page->getRowData()) {
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Page->ListOptions->render("body", "left", $Page->RowCount);
?>
    <?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <td data-name="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_PATIENT_ID" class="el_view_patient_report_PATIENT_ID">
<span<?= $Page->PATIENT_ID->viewAttributes() ?>>
<?= $Page->PATIENT_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <td data-name="Patient_Name"<?= $Page->Patient_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Patient_Name" class="el_view_patient_report_Patient_Name">
<span<?= $Page->Patient_Name->viewAttributes() ?>>
<?= $Page->Patient_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->GENDER->Visible) { // GENDER ?>
        <td data-name="GENDER"<?= $Page->GENDER->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_GENDER" class="el_view_patient_report_GENDER">
<span<?= $Page->GENDER->viewAttributes() ?>>
<?= $Page->GENDER->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->BLOOD_GROUP->Visible) { // BLOOD_GROUP ?>
        <td data-name="BLOOD_GROUP"<?= $Page->BLOOD_GROUP->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_BLOOD_GROUP" class="el_view_patient_report_BLOOD_GROUP">
<span<?= $Page->BLOOD_GROUP->viewAttributes() ?>>
<?= $Page->BLOOD_GROUP->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->PHONE->Visible) { // PHONE ?>
        <td data-name="PHONE"<?= $Page->PHONE->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_PHONE" class="el_view_patient_report_PHONE">
<span<?= $Page->PHONE->viewAttributes() ?>>
<?= $Page->PHONE->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->EMAIL->Visible) { // EMAIL ?>
        <td data-name="EMAIL"<?= $Page->EMAIL->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_EMAIL" class="el_view_patient_report_EMAIL">
<span<?= $Page->EMAIL->viewAttributes() ?>>
<?= $Page->EMAIL->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Total_Appointments->Visible) { // Total_Appointments ?>
        <td data-name="Total_Appointments"<?= $Page->Total_Appointments->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Total_Appointments" class="el_view_patient_report_Total_Appointments">
<span<?= $Page->Total_Appointments->viewAttributes() ?>>
<?= $Page->Total_Appointments->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Completed_Appointments->Visible) { // Completed_Appointments ?>
        <td data-name="Completed_Appointments"<?= $Page->Completed_Appointments->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Completed_Appointments" class="el_view_patient_report_Completed_Appointments">
<span<?= $Page->Completed_Appointments->viewAttributes() ?>>
<?= $Page->Completed_Appointments->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Upcoming_Appointments->Visible) { // Upcoming_Appointments ?>
        <td data-name="Upcoming_Appointments"<?= $Page->Upcoming_Appointments->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Upcoming_Appointments" class="el_view_patient_report_Upcoming_Appointments">
<span<?= $Page->Upcoming_Appointments->viewAttributes() ?>>
<?= $Page->Upcoming_Appointments->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Cancelled_Appointments->Visible) { // Cancelled_Appointments ?>
        <td data-name="Cancelled_Appointments"<?= $Page->Cancelled_Appointments->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Cancelled_Appointments" class="el_view_patient_report_Cancelled_Appointments">
<span<?= $Page->Cancelled_Appointments->viewAttributes() ?>>
<?= $Page->Cancelled_Appointments->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Last_Visit->Visible) { // Last_Visit ?>
        <td data-name="Last_Visit"<?= $Page->Last_Visit->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Last_Visit" class="el_view_patient_report_Last_Visit">
<span<?= $Page->Last_Visit->viewAttributes() ?>>
<?= $Page->Last_Visit->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->First_Visit->Visible) { // First_Visit ?>
        <td data-name="First_Visit"<?= $Page->First_Visit->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_First_Visit" class="el_view_patient_report_First_Visit">
<span<?= $Page->First_Visit->viewAttributes() ?>>
<?= $Page->First_Visit->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Total_Prescriptions->Visible) { // Total_Prescriptions ?>
        <td data-name="Total_Prescriptions"<?= $Page->Total_Prescriptions->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Total_Prescriptions" class="el_view_patient_report_Total_Prescriptions">
<span<?= $Page->Total_Prescriptions->viewAttributes() ?>>
<?= $Page->Total_Prescriptions->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Total_Amount_Paid->Visible) { // Total_Amount_Paid ?>
        <td data-name="Total_Amount_Paid"<?= $Page->Total_Amount_Paid->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Total_Amount_Paid" class="el_view_patient_report_Total_Amount_Paid">
<span<?= $Page->Total_Amount_Paid->viewAttributes() ?>>
<?= $Page->Total_Amount_Paid->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Avg_Rating_Given->Visible) { // Avg_Rating_Given ?>
        <td data-name="Avg_Rating_Given"<?= $Page->Avg_Rating_Given->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_patient_report_Avg_Rating_Given" class="el_view_patient_report_Avg_Rating_Given">
<span<?= $Page->Avg_Rating_Given->viewAttributes() ?>>
<?= $Page->Avg_Rating_Given->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
<?php
// Render list options (body, right)
$Page->ListOptions->render("body", "right", $Page->RowCount);
?>
    </tr>
<?php
}
?>
</tbody>
</table><!-- /.ew-table -->
<?php } ?>
</div><!-- /.ew-grid-middle-panel -->
<?php if (!$Page->CurrentAction && !$Page->UseAjaxActions) { ?>
<input type="hidden" name="action" id="action" value="">
<?php } ?>
</form><!-- /.ew-list-form -->
<?php if (!$Page->isExport()) { ?>
<div class="card-footer ew-grid-lower-panel">
<?= $Page->Pager?->render() ?>
<div class="ew-list-other-options">
<?= $Page->OtherOptions->render("body", "bottom") ?>
</div>
</div>
<?php } ?>
</div><!-- /.ew-grid -->
<?php } else { ?>
<div class="ew-list-other-options">
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
</div>
<div id="ew-footer-options">
<?php $Page->FooterOptions?->render("body") ?>
</div>
</main>
<?= $Page->getPageFooter() ?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
// Field event handlers
ew.on("head", function() {
    ew.addEventHandlers("view_patient_report");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

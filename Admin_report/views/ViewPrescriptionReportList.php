<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_prescription_report: currentTable } });
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
<form name="fview_prescription_reportsrch" id="fview_prescription_reportsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>" novalidate autocomplete="off">
<div id="fview_prescription_reportsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_prescription_report: currentTable } });
var currentForm;
var fview_prescription_reportsrch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_prescription_reportsrch")
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fview_prescription_reportsrch" data-ew-action="search-type"><?= Language()->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fview_prescription_reportsrch" data-ew-action="search-type" data-search-type="="><?= Language()->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fview_prescription_reportsrch" data-ew-action="search-type" data-search-type="AND"><?= Language()->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fview_prescription_reportsrch" data-ew-action="search-type" data-search-type="OR"><?= Language()->phrase("QuickSearchAny") ?></button>
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
<?php $formAction = GetUrl(UrlFor("list.view_prescription_report", $Page->getUrlKey(true))) ?>
<form name="<?= $Page->FormName ?>" id="<?= $Page->FormName ?>" class="ew-form ew-list-form" action="<?= $formAction ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_prescription_report">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div id="gmp_view_prescription_report" class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit() || $Page->isMultiEdit()) { ?>
<table id="tbl_view_prescription_reportlist" class="<?= $Page->TableClass ?>"><!-- .ew-table -->
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
<?php if ($Page->PRESCRIPTION_ID->Visible) { // PRESCRIPTION_ID ?>
        <th data-name="PRESCRIPTION_ID" class="<?= $Page->PRESCRIPTION_ID->headerCellClass() ?>"><div id="elh_view_prescription_report_PRESCRIPTION_ID" class="view_prescription_report_PRESCRIPTION_ID"><?= $Page->renderFieldHeader($Page->PRESCRIPTION_ID) ?></div></th>
<?php } ?>
<?php if ($Page->ISSUE_DATE->Visible) { // ISSUE_DATE ?>
        <th data-name="ISSUE_DATE" class="<?= $Page->ISSUE_DATE->headerCellClass() ?>"><div id="elh_view_prescription_report_ISSUE_DATE" class="view_prescription_report_ISSUE_DATE"><?= $Page->renderFieldHeader($Page->ISSUE_DATE) ?></div></th>
<?php } ?>
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <th data-name="Patient_Name" class="<?= $Page->Patient_Name->headerCellClass() ?>"><div id="elh_view_prescription_report_Patient_Name" class="view_prescription_report_Patient_Name"><?= $Page->renderFieldHeader($Page->Patient_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
        <th data-name="Doctor_Name" class="<?= $Page->Doctor_Name->headerCellClass() ?>"><div id="elh_view_prescription_report_Doctor_Name" class="view_prescription_report_Doctor_Name"><?= $Page->renderFieldHeader($Page->Doctor_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Specialisation->Visible) { // Specialisation ?>
        <th data-name="Specialisation" class="<?= $Page->Specialisation->headerCellClass() ?>"><div id="elh_view_prescription_report_Specialisation" class="view_prescription_report_Specialisation"><?= $Page->renderFieldHeader($Page->Specialisation) ?></div></th>
<?php } ?>
<?php if ($Page->SYMPTOMS->Visible) { // SYMPTOMS ?>
        <th data-name="SYMPTOMS" class="<?= $Page->SYMPTOMS->headerCellClass() ?>"><div id="elh_view_prescription_report_SYMPTOMS" class="view_prescription_report_SYMPTOMS"><?= $Page->renderFieldHeader($Page->SYMPTOMS) ?></div></th>
<?php } ?>
<?php if ($Page->DIAGNOSIS->Visible) { // DIAGNOSIS ?>
        <th data-name="DIAGNOSIS" class="<?= $Page->DIAGNOSIS->headerCellClass() ?>"><div id="elh_view_prescription_report_DIAGNOSIS" class="view_prescription_report_DIAGNOSIS"><?= $Page->renderFieldHeader($Page->DIAGNOSIS) ?></div></th>
<?php } ?>
<?php if ($Page->DIABETES->Visible) { // DIABETES ?>
        <th data-name="DIABETES" class="<?= $Page->DIABETES->headerCellClass() ?>"><div id="elh_view_prescription_report_DIABETES" class="view_prescription_report_DIABETES"><?= $Page->renderFieldHeader($Page->DIABETES) ?></div></th>
<?php } ?>
<?php if ($Page->BLOOD_PRESSURE->Visible) { // BLOOD_PRESSURE ?>
        <th data-name="BLOOD_PRESSURE" class="<?= $Page->BLOOD_PRESSURE->headerCellClass() ?>"><div id="elh_view_prescription_report_BLOOD_PRESSURE" class="view_prescription_report_BLOOD_PRESSURE"><?= $Page->renderFieldHeader($Page->BLOOD_PRESSURE) ?></div></th>
<?php } ?>
<?php if ($Page->ADDITIONAL_NOTES->Visible) { // ADDITIONAL_NOTES ?>
        <th data-name="ADDITIONAL_NOTES" class="<?= $Page->ADDITIONAL_NOTES->headerCellClass() ?>"><div id="elh_view_prescription_report_ADDITIONAL_NOTES" class="view_prescription_report_ADDITIONAL_NOTES"><?= $Page->renderFieldHeader($Page->ADDITIONAL_NOTES) ?></div></th>
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
    <?php if ($Page->PRESCRIPTION_ID->Visible) { // PRESCRIPTION_ID ?>
        <td data-name="PRESCRIPTION_ID"<?= $Page->PRESCRIPTION_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_PRESCRIPTION_ID" class="el_view_prescription_report_PRESCRIPTION_ID">
<span<?= $Page->PRESCRIPTION_ID->viewAttributes() ?>>
<?= $Page->PRESCRIPTION_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->ISSUE_DATE->Visible) { // ISSUE_DATE ?>
        <td data-name="ISSUE_DATE"<?= $Page->ISSUE_DATE->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_ISSUE_DATE" class="el_view_prescription_report_ISSUE_DATE">
<span<?= $Page->ISSUE_DATE->viewAttributes() ?>>
<?= $Page->ISSUE_DATE->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <td data-name="Patient_Name"<?= $Page->Patient_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_Patient_Name" class="el_view_prescription_report_Patient_Name">
<span<?= $Page->Patient_Name->viewAttributes() ?>>
<?= $Page->Patient_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
        <td data-name="Doctor_Name"<?= $Page->Doctor_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_Doctor_Name" class="el_view_prescription_report_Doctor_Name">
<span<?= $Page->Doctor_Name->viewAttributes() ?>>
<?= $Page->Doctor_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Specialisation->Visible) { // Specialisation ?>
        <td data-name="Specialisation"<?= $Page->Specialisation->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_Specialisation" class="el_view_prescription_report_Specialisation">
<span<?= $Page->Specialisation->viewAttributes() ?>>
<?= $Page->Specialisation->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->SYMPTOMS->Visible) { // SYMPTOMS ?>
        <td data-name="SYMPTOMS"<?= $Page->SYMPTOMS->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_SYMPTOMS" class="el_view_prescription_report_SYMPTOMS">
<span<?= $Page->SYMPTOMS->viewAttributes() ?>>
<?= $Page->SYMPTOMS->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->DIAGNOSIS->Visible) { // DIAGNOSIS ?>
        <td data-name="DIAGNOSIS"<?= $Page->DIAGNOSIS->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_DIAGNOSIS" class="el_view_prescription_report_DIAGNOSIS">
<span<?= $Page->DIAGNOSIS->viewAttributes() ?>>
<?= $Page->DIAGNOSIS->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->DIABETES->Visible) { // DIABETES ?>
        <td data-name="DIABETES"<?= $Page->DIABETES->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_DIABETES" class="el_view_prescription_report_DIABETES">
<span<?= $Page->DIABETES->viewAttributes() ?>>
<?= $Page->DIABETES->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->BLOOD_PRESSURE->Visible) { // BLOOD_PRESSURE ?>
        <td data-name="BLOOD_PRESSURE"<?= $Page->BLOOD_PRESSURE->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_BLOOD_PRESSURE" class="el_view_prescription_report_BLOOD_PRESSURE">
<span<?= $Page->BLOOD_PRESSURE->viewAttributes() ?>>
<?= $Page->BLOOD_PRESSURE->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->ADDITIONAL_NOTES->Visible) { // ADDITIONAL_NOTES ?>
        <td data-name="ADDITIONAL_NOTES"<?= $Page->ADDITIONAL_NOTES->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_prescription_report_ADDITIONAL_NOTES" class="el_view_prescription_report_ADDITIONAL_NOTES">
<span<?= $Page->ADDITIONAL_NOTES->viewAttributes() ?>>
<?= $Page->ADDITIONAL_NOTES->getViewValue() ?></span>
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
    ew.addEventHandlers("view_prescription_report");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

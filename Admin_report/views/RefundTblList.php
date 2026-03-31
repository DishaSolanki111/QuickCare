<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { refund_tbl: currentTable } });
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
<form name="frefund_tblsrch" id="frefund_tblsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>" novalidate autocomplete="off">
<div id="frefund_tblsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { refund_tbl: currentTable } });
var currentForm;
var frefund_tblsrch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("frefund_tblsrch")
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
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="frefund_tblsrch" data-ew-action="search-type"><?= Language()->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="frefund_tblsrch" data-ew-action="search-type" data-search-type="="><?= Language()->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="frefund_tblsrch" data-ew-action="search-type" data-search-type="AND"><?= Language()->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="frefund_tblsrch" data-ew-action="search-type" data-search-type="OR"><?= Language()->phrase("QuickSearchAny") ?></button>
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
<?php if (!$Page->isExport()) { ?>
<div class="card-header ew-grid-upper-panel">
<?= $Page->Pager?->render() ?>
<div class="ew-list-other-options">
<?= $Page->OtherOptions->render("body") ?>
</div>
</div>
<?php } ?>
<?php $formAction = GetUrl(UrlFor("list.refund_tbl", $Page->getUrlKey(true))) ?>
<form name="<?= $Page->FormName ?>" id="<?= $Page->FormName ?>" class="ew-form ew-list-form" action="<?= $formAction ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="refund_tbl">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div id="gmp_refund_tbl" class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit() || $Page->isMultiEdit()) { ?>
<table id="tbl_refund_tbllist" class="<?= $Page->TableClass ?>"><!-- .ew-table -->
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
<?php if ($Page->REFUND_ID->Visible) { // REFUND_ID ?>
        <th data-name="REFUND_ID" class="<?= $Page->REFUND_ID->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_ID" class="refund_tbl_REFUND_ID"><?= $Page->renderFieldHeader($Page->REFUND_ID) ?></div></th>
<?php } ?>
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
        <th data-name="PAYMENT_ID" class="<?= $Page->PAYMENT_ID->headerCellClass() ?>"><div id="elh_refund_tbl_PAYMENT_ID" class="refund_tbl_PAYMENT_ID"><?= $Page->renderFieldHeader($Page->PAYMENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
        <th data-name="APPOINTMENT_ID" class="<?= $Page->APPOINTMENT_ID->headerCellClass() ?>"><div id="elh_refund_tbl_APPOINTMENT_ID" class="refund_tbl_APPOINTMENT_ID"><?= $Page->renderFieldHeader($Page->APPOINTMENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <th data-name="PATIENT_ID" class="<?= $Page->PATIENT_ID->headerCellClass() ?>"><div id="elh_refund_tbl_PATIENT_ID" class="refund_tbl_PATIENT_ID"><?= $Page->renderFieldHeader($Page->PATIENT_ID) ?></div></th>
<?php } ?>
<?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
        <th data-name="REFUND_AMOUNT" class="<?= $Page->REFUND_AMOUNT->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_AMOUNT" class="refund_tbl_REFUND_AMOUNT"><?= $Page->renderFieldHeader($Page->REFUND_AMOUNT) ?></div></th>
<?php } ?>
<?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
        <th data-name="REFUND_DATE" class="<?= $Page->REFUND_DATE->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_DATE" class="refund_tbl_REFUND_DATE"><?= $Page->renderFieldHeader($Page->REFUND_DATE) ?></div></th>
<?php } ?>
<?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
        <th data-name="REFUND_STATUS" class="<?= $Page->REFUND_STATUS->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_STATUS" class="refund_tbl_REFUND_STATUS"><?= $Page->renderFieldHeader($Page->REFUND_STATUS) ?></div></th>
<?php } ?>
<?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
        <th data-name="REFUND_REASON" class="<?= $Page->REFUND_REASON->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_REASON" class="refund_tbl_REFUND_REASON"><?= $Page->renderFieldHeader($Page->REFUND_REASON) ?></div></th>
<?php } ?>
<?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
        <th data-name="REFUND_TXN_ID" class="<?= $Page->REFUND_TXN_ID->headerCellClass() ?>"><div id="elh_refund_tbl_REFUND_TXN_ID" class="refund_tbl_REFUND_TXN_ID"><?= $Page->renderFieldHeader($Page->REFUND_TXN_ID) ?></div></th>
<?php } ?>
<?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
        <th data-name="CREATED_AT" class="<?= $Page->CREATED_AT->headerCellClass() ?>"><div id="elh_refund_tbl_CREATED_AT" class="refund_tbl_CREATED_AT"><?= $Page->renderFieldHeader($Page->CREATED_AT) ?></div></th>
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
    <?php if ($Page->REFUND_ID->Visible) { // REFUND_ID ?>
        <td data-name="REFUND_ID"<?= $Page->REFUND_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_ID" class="el_refund_tbl_REFUND_ID">
<span<?= $Page->REFUND_ID->viewAttributes() ?>>
<?= $Page->REFUND_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
        <td data-name="PAYMENT_ID"<?= $Page->PAYMENT_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_PAYMENT_ID" class="el_refund_tbl_PAYMENT_ID">
<span<?= $Page->PAYMENT_ID->viewAttributes() ?>>
<?= $Page->PAYMENT_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
        <td data-name="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_APPOINTMENT_ID" class="el_refund_tbl_APPOINTMENT_ID">
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <td data-name="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_PATIENT_ID" class="el_refund_tbl_PATIENT_ID">
<span<?= $Page->PATIENT_ID->viewAttributes() ?>>
<?= $Page->PATIENT_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
        <td data-name="REFUND_AMOUNT"<?= $Page->REFUND_AMOUNT->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_AMOUNT" class="el_refund_tbl_REFUND_AMOUNT">
<span<?= $Page->REFUND_AMOUNT->viewAttributes() ?>>
<?= $Page->REFUND_AMOUNT->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
        <td data-name="REFUND_DATE"<?= $Page->REFUND_DATE->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_DATE" class="el_refund_tbl_REFUND_DATE">
<span<?= $Page->REFUND_DATE->viewAttributes() ?>>
<?= $Page->REFUND_DATE->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
        <td data-name="REFUND_STATUS"<?= $Page->REFUND_STATUS->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_STATUS" class="el_refund_tbl_REFUND_STATUS">
<span<?= $Page->REFUND_STATUS->viewAttributes() ?>>
<?= $Page->REFUND_STATUS->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
        <td data-name="REFUND_REASON"<?= $Page->REFUND_REASON->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_REASON" class="el_refund_tbl_REFUND_REASON">
<span<?= $Page->REFUND_REASON->viewAttributes() ?>>
<?= $Page->REFUND_REASON->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
        <td data-name="REFUND_TXN_ID"<?= $Page->REFUND_TXN_ID->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_REFUND_TXN_ID" class="el_refund_tbl_REFUND_TXN_ID">
<span<?= $Page->REFUND_TXN_ID->viewAttributes() ?>>
<?= $Page->REFUND_TXN_ID->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
        <td data-name="CREATED_AT"<?= $Page->CREATED_AT->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_refund_tbl_CREATED_AT" class="el_refund_tbl_CREATED_AT">
<span<?= $Page->CREATED_AT->viewAttributes() ?>>
<?= $Page->CREATED_AT->getViewValue() ?></span>
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
    ew.addEventHandlers("refund_tbl");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_appointment_report: currentTable } });
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

        // Dynamic selection lists
        .setLists({
            "Doctor_Name": <?= $Page->Doctor_Name->toClientList($Page) ?>,
            "APPOINTMENT_DATE": <?= $Page->APPOINTMENT_DATE->toClientList($Page) ?>,
            "Day_Name": <?= $Page->Day_Name->toClientList($Page) ?>,
            "Month_Name": <?= $Page->Month_Name->toClientList($Page) ?>,
            "STATUS": <?= $Page->STATUS->toClientList($Page) ?>,
        })
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
<?php } else { ?>
<!-- Debug: No records to export -->
<span class="alert alert-info">No records available for export</span>
<?php } ?>
<?= $Page->ImportOptions->render("body") ?>
<?= $Page->SearchOptions->render("body") ?>
<?= $Page->FilterOptions->render("body") ?>
</div>
<?php } ?>
<?= $Page->showFilterList() ?>
<?php if (!$Page->IsModal) { ?>
<form name="fview_appointment_reportsrch" id="fview_appointment_reportsrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>" novalidate autocomplete="off">
<div id="fview_appointment_reportsrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_appointment_report: currentTable } });
var currentForm;
var fview_appointment_reportsrch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_appointment_reportsrch")
        .setPageId("list")
<?php if ($Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Add fields
        .addFields([
        ])
        // Validate form
        .setValidate(
            async function () {
                if (!this.validateRequired)
                    return true; // Ignore validation
                let fobj = this.getForm();

                // Validate fields
                if (!this.validateFields())
                    return false;
                return true;
            }
        )

        // Use JavaScript validation or not
        .setValidateRequired(ew.CLIENT_VALIDATE)

        // Dynamic selection lists
        .setLists({
            "Doctor_Name": <?= $Page->Doctor_Name->toClientList($Page) ?>,
            "APPOINTMENT_DATE": <?= $Page->APPOINTMENT_DATE->toClientList($Page) ?>,
            "Day_Name": <?= $Page->Day_Name->toClientList($Page) ?>,
            "Month_Name": <?= $Page->Month_Name->toClientList($Page) ?>,
            "STATUS": <?= $Page->STATUS->toClientList($Page) ?>,
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
<?php $Page->renderRow(RowType::SEARCH); ?>
<div class="row mb-0<?= ($Page->SearchFieldsPerRow > 0) ? " row-cols-sm-" . $Page->SearchFieldsPerRow : "" ?>">
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
<?php
if (!$Page->Doctor_Name->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_Doctor_Name" class="col-sm-auto d-sm-flex align-items-start mb-3 px-0 pe-sm-2<?= $Page->Doctor_Name->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_Doctor_Name"
            name="x_Doctor_Name[]"
            class="form-control ew-select<?= $Page->Doctor_Name->isInvalidClass() ?>"
            data-select2-id="fview_appointment_reportsrch_x_Doctor_Name"
            data-table="view_appointment_report"
            data-field="x_Doctor_Name"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->Doctor_Name->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->Doctor_Name->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->Doctor_Name->getPlaceHolder()) ?>"
            data-ew-action="update-options"
            <?= $Page->Doctor_Name->editAttributes() ?>>
            <?= $Page->Doctor_Name->selectOptionListHtml("x_Doctor_Name", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->Doctor_Name->getErrorMessage(false) ?></div>
        <script<?= Nonce() ?>>
        ew.on("fview_appointment_reportsrch", function() {
            let options = {
                name: "x_Doctor_Name",
                selectId: "fview_appointment_reportsrch_x_Doctor_Name",
                ajax: { id: "x_Doctor_Name", form: "fview_appointment_reportsrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.view_appointment_report.fields.Doctor_Name.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
<?php if ($Page->APPOINTMENT_DATE->Visible) { // APPOINTMENT_DATE ?>
<?php
if (!$Page->APPOINTMENT_DATE->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_APPOINTMENT_DATE" class="col-sm-auto d-sm-flex align-items-start mb-3 px-0 pe-sm-2<?= $Page->APPOINTMENT_DATE->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_APPOINTMENT_DATE"
            name="x_APPOINTMENT_DATE[]"
            class="form-control ew-select<?= $Page->APPOINTMENT_DATE->isInvalidClass() ?>"
            data-select2-id="fview_appointment_reportsrch_x_APPOINTMENT_DATE"
            data-table="view_appointment_report"
            data-field="x_APPOINTMENT_DATE"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->APPOINTMENT_DATE->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->APPOINTMENT_DATE->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->APPOINTMENT_DATE->getPlaceHolder()) ?>"
            data-ew-action="update-options"
            <?= $Page->APPOINTMENT_DATE->editAttributes() ?>>
            <?= $Page->APPOINTMENT_DATE->selectOptionListHtml("x_APPOINTMENT_DATE", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->APPOINTMENT_DATE->getErrorMessage(false) ?></div>
        <script<?= Nonce() ?>>
        ew.on("fview_appointment_reportsrch", function() {
            let options = {
                name: "x_APPOINTMENT_DATE",
                selectId: "fview_appointment_reportsrch_x_APPOINTMENT_DATE",
                ajax: { id: "x_APPOINTMENT_DATE", form: "fview_appointment_reportsrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.view_appointment_report.fields.APPOINTMENT_DATE.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
<?php if ($Page->Day_Name->Visible) { // Day_Name ?>
<?php
if (!$Page->Day_Name->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_Day_Name" class="col-sm-auto d-sm-flex align-items-start mb-3 px-0 pe-sm-2<?= $Page->Day_Name->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_Day_Name"
            name="x_Day_Name[]"
            class="form-control ew-select<?= $Page->Day_Name->isInvalidClass() ?>"
            data-select2-id="fview_appointment_reportsrch_x_Day_Name"
            data-table="view_appointment_report"
            data-field="x_Day_Name"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->Day_Name->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->Day_Name->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->Day_Name->getPlaceHolder()) ?>"
            data-ew-action="update-options"
            <?= $Page->Day_Name->editAttributes() ?>>
            <?= $Page->Day_Name->selectOptionListHtml("x_Day_Name", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->Day_Name->getErrorMessage(false) ?></div>
        <script<?= Nonce() ?>>
        ew.on("fview_appointment_reportsrch", function() {
            let options = {
                name: "x_Day_Name",
                selectId: "fview_appointment_reportsrch_x_Day_Name",
                ajax: { id: "x_Day_Name", form: "fview_appointment_reportsrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.view_appointment_report.fields.Day_Name.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
<?php if ($Page->Month_Name->Visible) { // Month_Name ?>
<?php
if (!$Page->Month_Name->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_Month_Name" class="col-sm-auto d-sm-flex align-items-start mb-3 px-0 pe-sm-2<?= $Page->Month_Name->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_Month_Name"
            name="x_Month_Name[]"
            class="form-control ew-select<?= $Page->Month_Name->isInvalidClass() ?>"
            data-select2-id="fview_appointment_reportsrch_x_Month_Name"
            data-table="view_appointment_report"
            data-field="x_Month_Name"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->Month_Name->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->Month_Name->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->Month_Name->getPlaceHolder()) ?>"
            data-ew-action="update-options"
            <?= $Page->Month_Name->editAttributes() ?>>
            <?= $Page->Month_Name->selectOptionListHtml("x_Month_Name", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->Month_Name->getErrorMessage(false) ?></div>
        <script<?= Nonce() ?>>
        ew.on("fview_appointment_reportsrch", function() {
            let options = {
                name: "x_Month_Name",
                selectId: "fview_appointment_reportsrch_x_Month_Name",
                ajax: { id: "x_Month_Name", form: "fview_appointment_reportsrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.view_appointment_report.fields.Month_Name.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
<?php if ($Page->STATUS->Visible) { // STATUS ?>
<?php
if (!$Page->STATUS->UseFilter) {
    $Page->SearchColumnCount++;
}
?>
    <div id="xs_STATUS" class="col-sm-auto d-sm-flex align-items-start mb-3 px-0 pe-sm-2<?= $Page->STATUS->UseFilter ? " ew-filter-field" : "" ?>">
        <select
            id="x_STATUS"
            name="x_STATUS[]"
            class="form-control ew-select<?= $Page->STATUS->isInvalidClass() ?>"
            data-select2-id="fview_appointment_reportsrch_x_STATUS"
            data-table="view_appointment_report"
            data-field="x_STATUS"
            data-caption="<?= HtmlEncode(RemoveHtml($Page->STATUS->caption())) ?>"
            data-filter="true"
            multiple
            size="1"
            data-value-separator="<?= $Page->STATUS->displayValueSeparatorAttribute() ?>"
            data-placeholder="<?= HtmlEncode($Page->STATUS->getPlaceHolder()) ?>"
            data-ew-action="update-options"
            <?= $Page->STATUS->editAttributes() ?>>
            <?= $Page->STATUS->selectOptionListHtml("x_STATUS", true) ?>
        </select>
        <div class="invalid-feedback"><?= $Page->STATUS->getErrorMessage(false) ?></div>
        <script<?= Nonce() ?>>
        ew.on("fview_appointment_reportsrch", function() {
            let options = {
                name: "x_STATUS",
                selectId: "fview_appointment_reportsrch_x_STATUS",
                ajax: { id: "x_STATUS", form: "fview_appointment_reportsrch", limit: ew.FILTER_PAGE_SIZE, data: { ajax: "filter" } }
            };
            options = Object.assign({}, ew.filterOptions, options, ew.vars.tables.view_appointment_report.fields.STATUS.filterOptions);
            ew.createFilter(options);
        });
        </script>
    </div><!-- /.col-sm-auto -->
<?php } ?>
</div><!-- /.row -->
<div class="row mb-0">
    <div class="col-sm-auto px-0 pe-sm-2">
        <div class="ew-basic-search input-group">
            <input type="search" name="<?= Config("TABLE_BASIC_SEARCH") ?>" id="<?= Config("TABLE_BASIC_SEARCH") ?>" class="form-control ew-basic-search-keyword" value="<?= HtmlEncode($Page->BasicSearch->getKeyword()) ?>" placeholder="<?= HtmlEncode(Language()->phrase("Search")) ?>" aria-label="<?= HtmlEncode(Language()->phrase("Search")) ?>">
            <input type="hidden" name="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" id="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" class="ew-basic-search-type" value="<?= HtmlEncode($Page->BasicSearch->getType()) ?>">
            <button type="button" data-bs-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" aria-haspopup="true" aria-expanded="false">
                <span id="searchtype"><?= $Page->BasicSearch->getTypeNameShort() ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fview_appointment_reportsrch" data-ew-action="search-type"><?= Language()->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fview_appointment_reportsrch" data-ew-action="search-type" data-search-type="="><?= Language()->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fview_appointment_reportsrch" data-ew-action="search-type" data-search-type="AND"><?= Language()->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fview_appointment_reportsrch" data-ew-action="search-type" data-search-type="OR"><?= Language()->phrase("QuickSearchAny") ?></button>
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
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
<!-- Middle Container -->
<div id="ew-middle" class="<?= $Page->MiddleContentClass ?>">
<?php } ?>
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
<!-- Content Container -->
<div id="ew-content" class="<?= $Page->ContainerClass ?>">
<?php } ?>
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
<?php $formAction = GetUrl(UrlFor("list.view_appointment_report", $Page->getUrlKey(true))) ?>
<form name="<?= $Page->FormName ?>" id="<?= $Page->FormName ?>" class="ew-form ew-list-form" action="<?= $formAction ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_appointment_report">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div id="gmp_view_appointment_report" class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit() || $Page->isMultiEdit()) { ?>
<table id="tbl_view_appointment_reportlist" class="<?= $Page->TableClass ?>"><!-- .ew-table -->
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
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <th data-name="Patient_Name" class="<?= $Page->Patient_Name->headerCellClass() ?>"><div id="elh_view_appointment_report_Patient_Name" class="view_appointment_report_Patient_Name"><?= $Page->renderFieldHeader($Page->Patient_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
        <th data-name="Doctor_Name" class="<?= $Page->Doctor_Name->headerCellClass() ?>"><div id="elh_view_appointment_report_Doctor_Name" class="view_appointment_report_Doctor_Name"><?= $Page->renderFieldHeader($Page->Doctor_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Specialisation->Visible) { // Specialisation ?>
        <th data-name="Specialisation" class="<?= $Page->Specialisation->headerCellClass() ?>"><div id="elh_view_appointment_report_Specialisation" class="view_appointment_report_Specialisation"><?= $Page->renderFieldHeader($Page->Specialisation) ?></div></th>
<?php } ?>
<?php if ($Page->APPOINTMENT_DATE->Visible) { // APPOINTMENT_DATE ?>
        <th data-name="APPOINTMENT_DATE" class="<?= $Page->APPOINTMENT_DATE->headerCellClass() ?>"><div id="elh_view_appointment_report_APPOINTMENT_DATE" class="view_appointment_report_APPOINTMENT_DATE"><?= $Page->renderFieldHeader($Page->APPOINTMENT_DATE) ?></div></th>
<?php } ?>
<?php if ($Page->Day_Name->Visible) { // Day_Name ?>
        <th data-name="Day_Name" class="<?= $Page->Day_Name->headerCellClass() ?>"><div id="elh_view_appointment_report_Day_Name" class="view_appointment_report_Day_Name"><?= $Page->renderFieldHeader($Page->Day_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Month_Name->Visible) { // Month_Name ?>
        <th data-name="Month_Name" class="<?= $Page->Month_Name->headerCellClass() ?>"><div id="elh_view_appointment_report_Month_Name" class="view_appointment_report_Month_Name"><?= $Page->renderFieldHeader($Page->Month_Name) ?></div></th>
<?php } ?>
<?php if ($Page->Year->Visible) { // Year ?>
        <th data-name="Year" class="<?= $Page->Year->headerCellClass() ?>"><div id="elh_view_appointment_report_Year" class="view_appointment_report_Year"><?= $Page->renderFieldHeader($Page->Year) ?></div></th>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { // APPOINTMENT_TIME ?>
        <th data-name="APPOINTMENT_TIME" class="<?= $Page->APPOINTMENT_TIME->headerCellClass() ?>"><div id="elh_view_appointment_report_APPOINTMENT_TIME" class="view_appointment_report_APPOINTMENT_TIME"><?= $Page->renderFieldHeader($Page->APPOINTMENT_TIME) ?></div></th>
<?php } ?>
<?php if ($Page->STATUS->Visible) { // STATUS ?>
        <th data-name="STATUS" class="<?= $Page->STATUS->headerCellClass() ?>"><div id="elh_view_appointment_report_STATUS" class="view_appointment_report_STATUS"><?= $Page->renderFieldHeader($Page->STATUS) ?></div></th>
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
    <?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
        <td data-name="Patient_Name"<?= $Page->Patient_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Patient_Name" class="el_view_appointment_report_Patient_Name">
<span<?= $Page->Patient_Name->viewAttributes() ?>>
<?= $Page->Patient_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
        <td data-name="Doctor_Name"<?= $Page->Doctor_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Doctor_Name" class="el_view_appointment_report_Doctor_Name">
<span<?= $Page->Doctor_Name->viewAttributes() ?>>
<?= $Page->Doctor_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Specialisation->Visible) { // Specialisation ?>
        <td data-name="Specialisation"<?= $Page->Specialisation->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Specialisation" class="el_view_appointment_report_Specialisation">
<span<?= $Page->Specialisation->viewAttributes() ?>>
<?= $Page->Specialisation->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->APPOINTMENT_DATE->Visible) { // APPOINTMENT_DATE ?>
        <td data-name="APPOINTMENT_DATE"<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_APPOINTMENT_DATE" class="el_view_appointment_report_APPOINTMENT_DATE">
<span<?= $Page->APPOINTMENT_DATE->viewAttributes() ?>>
<?= $Page->APPOINTMENT_DATE->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Day_Name->Visible) { // Day_Name ?>
        <td data-name="Day_Name"<?= $Page->Day_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Day_Name" class="el_view_appointment_report_Day_Name">
<span<?= $Page->Day_Name->viewAttributes() ?>>
<?= $Page->Day_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Month_Name->Visible) { // Month_Name ?>
        <td data-name="Month_Name"<?= $Page->Month_Name->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Month_Name" class="el_view_appointment_report_Month_Name">
<span<?= $Page->Month_Name->viewAttributes() ?>>
<?= $Page->Month_Name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->Year->Visible) { // Year ?>
        <td data-name="Year"<?= $Page->Year->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_Year" class="el_view_appointment_report_Year">
<span<?= $Page->Year->viewAttributes() ?>>
<?= $Page->Year->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->APPOINTMENT_TIME->Visible) { // APPOINTMENT_TIME ?>
        <td data-name="APPOINTMENT_TIME"<?= $Page->APPOINTMENT_TIME->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_APPOINTMENT_TIME" class="el_view_appointment_report_APPOINTMENT_TIME">
<span<?= $Page->APPOINTMENT_TIME->viewAttributes() ?>>
<?= $Page->APPOINTMENT_TIME->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->STATUS->Visible) { // STATUS ?>
        <td data-name="STATUS"<?= $Page->STATUS->cellAttributes() ?>>
<span id="el<?= $Page->RowIndex == '$rowindex$' ? '$rowindex$' : $Page->RowCount ?>_view_appointment_report_STATUS" class="el_view_appointment_report_STATUS">
<span<?= $Page->STATUS->viewAttributes() ?>>
<?= $Page->STATUS->getViewValue() ?></span>
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
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
</div>
<!-- /#ew-content -->
<?php } ?>
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
</div>
<!-- /#ew-middle -->
<?php } ?>
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
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
<?php
if (!$DashboardReport) {
    // Set up chart drilldown
    $Page->Chart2->DrillDownInPanel = $Page->DrillDownInPanel;
    echo $Page->Chart2->render($Page->ChartData, "ew-chart-bottom");
}
?>
<?php
if (!$DashboardReport) {
    // Set up chart drilldown
    $Page->Chart3->DrillDownInPanel = $Page->DrillDownInPanel;
    echo $Page->Chart3->render($Page->ChartData, "ew-chart-bottom");
}
?>
<?php if (!$Page->isExport() || $Page->isExport("print")) { ?>
</div>
<!-- /#ew-bottom -->
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
    ew.addEventHandlers("view_appointment_report");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

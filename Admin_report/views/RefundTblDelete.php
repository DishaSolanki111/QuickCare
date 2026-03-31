<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { refund_tbl: currentTable } });
var currentPageID = ew.PAGE_ID = "delete";
var currentForm;
var frefund_tbldelete;
ew.on("wrapper", function () {
    let $ = jQuery;
    let fields = currentTable.fields;

    // Form object
    let form = new ew.FormBuilder()
        .setId("frefund_tbldelete")
        .setPageId("delete")
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
<?= $Page->getPageHeader() ?>
<?= $Page->getHtmlMessage() ?>
<form name="frefund_tbldelete" id="frefund_tbldelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="refund_tbl">
<input type="hidden" name="action" id="action" value="delete">
<?php foreach ($Page->Records as $record) { ?>
<input type="hidden" name="key_m[]" value="<?= HtmlEncode($record->identifierValuesAsString()) ?>">
<?php } ?>
<div class="card ew-card ew-grid <?= $Page->TableGridClass ?>">
<div class="card-body ew-grid-middle-panel <?= $Page->TableContainerClass ?>">
<table class="<?= $Page->TableClass ?>">
    <thead>
    <tr class="ew-table-header">
<?php if ($Page->REFUND_ID->Visible) { // REFUND_ID ?>
        <th class="<?= $Page->REFUND_ID->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_ID" class="refund_tbl_REFUND_ID"><?= $Page->REFUND_ID->caption() ?></span></th>
<?php } ?>
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
        <th class="<?= $Page->PAYMENT_ID->headerCellClass() ?>"><span id="elh_refund_tbl_PAYMENT_ID" class="refund_tbl_PAYMENT_ID"><?= $Page->PAYMENT_ID->caption() ?></span></th>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
        <th class="<?= $Page->APPOINTMENT_ID->headerCellClass() ?>"><span id="elh_refund_tbl_APPOINTMENT_ID" class="refund_tbl_APPOINTMENT_ID"><?= $Page->APPOINTMENT_ID->caption() ?></span></th>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <th class="<?= $Page->PATIENT_ID->headerCellClass() ?>"><span id="elh_refund_tbl_PATIENT_ID" class="refund_tbl_PATIENT_ID"><?= $Page->PATIENT_ID->caption() ?></span></th>
<?php } ?>
<?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
        <th class="<?= $Page->REFUND_AMOUNT->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_AMOUNT" class="refund_tbl_REFUND_AMOUNT"><?= $Page->REFUND_AMOUNT->caption() ?></span></th>
<?php } ?>
<?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
        <th class="<?= $Page->REFUND_DATE->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_DATE" class="refund_tbl_REFUND_DATE"><?= $Page->REFUND_DATE->caption() ?></span></th>
<?php } ?>
<?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
        <th class="<?= $Page->REFUND_STATUS->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_STATUS" class="refund_tbl_REFUND_STATUS"><?= $Page->REFUND_STATUS->caption() ?></span></th>
<?php } ?>
<?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
        <th class="<?= $Page->REFUND_REASON->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_REASON" class="refund_tbl_REFUND_REASON"><?= $Page->REFUND_REASON->caption() ?></span></th>
<?php } ?>
<?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
        <th class="<?= $Page->REFUND_TXN_ID->headerCellClass() ?>"><span id="elh_refund_tbl_REFUND_TXN_ID" class="refund_tbl_REFUND_TXN_ID"><?= $Page->REFUND_TXN_ID->caption() ?></span></th>
<?php } ?>
<?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
        <th class="<?= $Page->CREATED_AT->headerCellClass() ?>"><span id="elh_refund_tbl_CREATED_AT" class="refund_tbl_CREATED_AT"><?= $Page->CREATED_AT->caption() ?></span></th>
<?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
while ($Page->getRowData()) {
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php if ($Page->REFUND_ID->Visible) { // REFUND_ID ?>
        <td<?= $Page->REFUND_ID->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_ID->viewAttributes() ?>>
<?= $Page->REFUND_ID->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
        <td<?= $Page->PAYMENT_ID->cellAttributes() ?>>
<span id="">
<span<?= $Page->PAYMENT_ID->viewAttributes() ?>>
<?= $Page->PAYMENT_ID->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
        <td<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span id="">
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
        <td<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span id="">
<span<?= $Page->PATIENT_ID->viewAttributes() ?>>
<?= $Page->PATIENT_ID->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
        <td<?= $Page->REFUND_AMOUNT->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_AMOUNT->viewAttributes() ?>>
<?= $Page->REFUND_AMOUNT->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
        <td<?= $Page->REFUND_DATE->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_DATE->viewAttributes() ?>>
<?= $Page->REFUND_DATE->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
        <td<?= $Page->REFUND_STATUS->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_STATUS->viewAttributes() ?>>
<?= $Page->REFUND_STATUS->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
        <td<?= $Page->REFUND_REASON->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_REASON->viewAttributes() ?>>
<?= $Page->REFUND_REASON->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
        <td<?= $Page->REFUND_TXN_ID->cellAttributes() ?>>
<span id="">
<span<?= $Page->REFUND_TXN_ID->viewAttributes() ?>>
<?= $Page->REFUND_TXN_ID->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
        <td<?= $Page->CREATED_AT->cellAttributes() ?>>
<span id="">
<span<?= $Page->CREATED_AT->viewAttributes() ?>>
<?= $Page->CREATED_AT->getViewValue() ?></span>
</span>
</td>
<?php } ?>
    </tr>
<?php
}
?>
</tbody>
</table>
</div>
</div>
<div class="ew-buttons ew-desktop-buttons">
<button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit"><?= Language()->phrase("DeleteBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= Language()->phrase("CancelBtn") ?></button>
</div>
</form>
<?= $Page->getPageFooter() ?>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>

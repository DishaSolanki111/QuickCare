<?php

namespace PHPMaker2026\Project2;
?>
<?php if (!$Page->isExport()) { ?>
<div class="btn-toolbar ew-toolbar">
<?php $Page->ExportOptions->render("body") ?>
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
<?= $Page->getPageHeader() ?>
<?= $Page->getHtmlMessage() ?>
<main class="view">
<form name="frefund_tblview" id="frefund_tblview" class="ew-form ew-view-form overlay-wrapper" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { refund_tbl: currentTable } });
var currentPageID = ew.PAGE_ID = "view";
var currentForm;
var frefund_tblview;
ew.on("wrapper", function () {
    let $ = jQuery;
    let fields = currentTable.fields;

    // Form object
    let form = new ew.FormBuilder()
        .setId("frefund_tblview")
        .setPageId("view")
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
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="refund_tbl">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="<?= $Page->TableClass ?>">
<?php if ($Page->REFUND_ID->Visible) { // REFUND_ID ?>
    <tr id="r_REFUND_ID"<?= $Page->REFUND_ID->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_ID"><?= $Page->REFUND_ID->caption() ?></span></td>
        <td data-name="REFUND_ID"<?= $Page->REFUND_ID->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_ID">
<span<?= $Page->REFUND_ID->viewAttributes() ?>>
<?= $Page->REFUND_ID->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
    <tr id="r_PAYMENT_ID"<?= $Page->PAYMENT_ID->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_PAYMENT_ID"><?= $Page->PAYMENT_ID->caption() ?></span></td>
        <td data-name="PAYMENT_ID"<?= $Page->PAYMENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_PAYMENT_ID">
<span<?= $Page->PAYMENT_ID->viewAttributes() ?>>
<?= $Page->PAYMENT_ID->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
    <tr id="r_APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_APPOINTMENT_ID"><?= $Page->APPOINTMENT_ID->caption() ?></span></td>
        <td data-name="APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_APPOINTMENT_ID">
<span<?= $Page->APPOINTMENT_ID->viewAttributes() ?>>
<?= $Page->APPOINTMENT_ID->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
    <tr id="r_PATIENT_ID"<?= $Page->PATIENT_ID->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_PATIENT_ID"><?= $Page->PATIENT_ID->caption() ?></span></td>
        <td data-name="PATIENT_ID"<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_PATIENT_ID">
<span<?= $Page->PATIENT_ID->viewAttributes() ?>>
<?= $Page->PATIENT_ID->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
    <tr id="r_REFUND_AMOUNT"<?= $Page->REFUND_AMOUNT->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_AMOUNT"><?= $Page->REFUND_AMOUNT->caption() ?></span></td>
        <td data-name="REFUND_AMOUNT"<?= $Page->REFUND_AMOUNT->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_AMOUNT">
<span<?= $Page->REFUND_AMOUNT->viewAttributes() ?>>
<?= $Page->REFUND_AMOUNT->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
    <tr id="r_REFUND_DATE"<?= $Page->REFUND_DATE->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_DATE"><?= $Page->REFUND_DATE->caption() ?></span></td>
        <td data-name="REFUND_DATE"<?= $Page->REFUND_DATE->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_DATE">
<span<?= $Page->REFUND_DATE->viewAttributes() ?>>
<?= $Page->REFUND_DATE->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
    <tr id="r_REFUND_STATUS"<?= $Page->REFUND_STATUS->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_STATUS"><?= $Page->REFUND_STATUS->caption() ?></span></td>
        <td data-name="REFUND_STATUS"<?= $Page->REFUND_STATUS->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_STATUS">
<span<?= $Page->REFUND_STATUS->viewAttributes() ?>>
<?= $Page->REFUND_STATUS->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
    <tr id="r_REFUND_REASON"<?= $Page->REFUND_REASON->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_REASON"><?= $Page->REFUND_REASON->caption() ?></span></td>
        <td data-name="REFUND_REASON"<?= $Page->REFUND_REASON->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_REASON">
<span<?= $Page->REFUND_REASON->viewAttributes() ?>>
<?= $Page->REFUND_REASON->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
    <tr id="r_REFUND_TXN_ID"<?= $Page->REFUND_TXN_ID->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_REFUND_TXN_ID"><?= $Page->REFUND_TXN_ID->caption() ?></span></td>
        <td data-name="REFUND_TXN_ID"<?= $Page->REFUND_TXN_ID->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_TXN_ID">
<span<?= $Page->REFUND_TXN_ID->viewAttributes() ?>>
<?= $Page->REFUND_TXN_ID->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
    <tr id="r_CREATED_AT"<?= $Page->CREATED_AT->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_refund_tbl_CREATED_AT"><?= $Page->CREATED_AT->caption() ?></span></td>
        <td data-name="CREATED_AT"<?= $Page->CREATED_AT->cellAttributes() ?>>
<span id="el_refund_tbl_CREATED_AT">
<span<?= $Page->CREATED_AT->viewAttributes() ?>>
<?= $Page->CREATED_AT->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
</table>
</form>
</main>
<?= $Page->getPageFooter() ?>
<?php if (!$Page->isExport()) { ?>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>

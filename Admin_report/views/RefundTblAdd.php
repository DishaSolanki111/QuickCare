<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { refund_tbl: currentTable } });
var currentPageID = ew.PAGE_ID = "add";
var currentForm;
var frefund_tbladd;
ew.on("wrapper", function () {
    let $ = jQuery;
    let fields = currentTable.fields;

    // Form object
    let form = new ew.FormBuilder()
        .setId("frefund_tbladd")
        .setPageId("add")

        // Add fields
        .setFields([
            ["PAYMENT_ID", [fields.PAYMENT_ID.visible && fields.PAYMENT_ID.required ? ew.Validators.required(fields.PAYMENT_ID.caption) : null, ew.Validators.integer], fields.PAYMENT_ID.isInvalid],
            ["APPOINTMENT_ID", [fields.APPOINTMENT_ID.visible && fields.APPOINTMENT_ID.required ? ew.Validators.required(fields.APPOINTMENT_ID.caption) : null, ew.Validators.integer], fields.APPOINTMENT_ID.isInvalid],
            ["PATIENT_ID", [fields.PATIENT_ID.visible && fields.PATIENT_ID.required ? ew.Validators.required(fields.PATIENT_ID.caption) : null, ew.Validators.integer], fields.PATIENT_ID.isInvalid],
            ["REFUND_AMOUNT", [fields.REFUND_AMOUNT.visible && fields.REFUND_AMOUNT.required ? ew.Validators.required(fields.REFUND_AMOUNT.caption) : null, ew.Validators.float], fields.REFUND_AMOUNT.isInvalid],
            ["REFUND_DATE", [fields.REFUND_DATE.visible && fields.REFUND_DATE.required ? ew.Validators.required(fields.REFUND_DATE.caption) : null, ew.Validators.datetime(fields.REFUND_DATE.clientFormatPattern)], fields.REFUND_DATE.isInvalid],
            ["REFUND_STATUS", [fields.REFUND_STATUS.visible && fields.REFUND_STATUS.required ? ew.Validators.required(fields.REFUND_STATUS.caption) : null], fields.REFUND_STATUS.isInvalid],
            ["REFUND_REASON", [fields.REFUND_REASON.visible && fields.REFUND_REASON.required ? ew.Validators.required(fields.REFUND_REASON.caption) : null], fields.REFUND_REASON.isInvalid],
            ["REFUND_TXN_ID", [fields.REFUND_TXN_ID.visible && fields.REFUND_TXN_ID.required ? ew.Validators.required(fields.REFUND_TXN_ID.caption) : null], fields.REFUND_TXN_ID.isInvalid],
            ["CREATED_AT", [fields.CREATED_AT.visible && fields.CREATED_AT.required ? ew.Validators.required(fields.CREATED_AT.caption) : null, ew.Validators.datetime(fields.CREATED_AT.clientFormatPattern)], fields.CREATED_AT.isInvalid]
        ])

        // Use JavaScript validation or not
        .setValidateRequired(ew.CLIENT_VALIDATE)

        // Dynamic selection lists
        .setLists({
            "REFUND_STATUS": <?= $Page->REFUND_STATUS->toClientList($Page) ?>,
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
<?= $Page->getPageHeader() ?>
<?= $Page->getHtmlMessage() ?>
<form name="frefund_tbladd" id="frefund_tbladd" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="refund_tbl">
<input type="hidden" name="action" id="action" value="insert">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<?php if (IsJsonResponse()) { ?>
<input type="hidden" name="json" value="1">
<?php } ?>
<input type="hidden" name="<?= $Page->getFormOldKeyName() ?>" value="<?= $Page->getOldKeyAsString() ?>">
<div class="ew-add-div"><!-- page* -->
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
    <div id="r_PAYMENT_ID"<?= $Page->PAYMENT_ID->rowAttributes() ?>>
        <label id="elh_refund_tbl_PAYMENT_ID" for="x_PAYMENT_ID" class="<?= $Page->LeftColumnClass ?>"><?= $Page->PAYMENT_ID->caption() ?><?= $Page->PAYMENT_ID->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->PAYMENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_PAYMENT_ID">
<input type="<?= $Page->PAYMENT_ID->getInputTextType() ?>" name="x_PAYMENT_ID" id="x_PAYMENT_ID" data-table="refund_tbl" data-field="x_PAYMENT_ID" value="<?= $Page->PAYMENT_ID->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->PAYMENT_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->PAYMENT_ID->formatPattern()) ?>"<?= $Page->PAYMENT_ID->editAttributes() ?> aria-describedby="x_PAYMENT_ID_help">
<?= $Page->PAYMENT_ID->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->PAYMENT_ID->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
    <div id="r_APPOINTMENT_ID"<?= $Page->APPOINTMENT_ID->rowAttributes() ?>>
        <label id="elh_refund_tbl_APPOINTMENT_ID" for="x_APPOINTMENT_ID" class="<?= $Page->LeftColumnClass ?>"><?= $Page->APPOINTMENT_ID->caption() ?><?= $Page->APPOINTMENT_ID->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_APPOINTMENT_ID">
<input type="<?= $Page->APPOINTMENT_ID->getInputTextType() ?>" name="x_APPOINTMENT_ID" id="x_APPOINTMENT_ID" data-table="refund_tbl" data-field="x_APPOINTMENT_ID" value="<?= $Page->APPOINTMENT_ID->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->APPOINTMENT_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->APPOINTMENT_ID->formatPattern()) ?>"<?= $Page->APPOINTMENT_ID->editAttributes() ?> aria-describedby="x_APPOINTMENT_ID_help">
<?= $Page->APPOINTMENT_ID->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->APPOINTMENT_ID->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->PATIENT_ID->Visible) { // PATIENT_ID ?>
    <div id="r_PATIENT_ID"<?= $Page->PATIENT_ID->rowAttributes() ?>>
        <label id="elh_refund_tbl_PATIENT_ID" for="x_PATIENT_ID" class="<?= $Page->LeftColumnClass ?>"><?= $Page->PATIENT_ID->caption() ?><?= $Page->PATIENT_ID->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->PATIENT_ID->cellAttributes() ?>>
<span id="el_refund_tbl_PATIENT_ID">
<input type="<?= $Page->PATIENT_ID->getInputTextType() ?>" name="x_PATIENT_ID" id="x_PATIENT_ID" data-table="refund_tbl" data-field="x_PATIENT_ID" value="<?= $Page->PATIENT_ID->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->PATIENT_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->PATIENT_ID->formatPattern()) ?>"<?= $Page->PATIENT_ID->editAttributes() ?> aria-describedby="x_PATIENT_ID_help">
<?= $Page->PATIENT_ID->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->PATIENT_ID->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->REFUND_AMOUNT->Visible) { // REFUND_AMOUNT ?>
    <div id="r_REFUND_AMOUNT"<?= $Page->REFUND_AMOUNT->rowAttributes() ?>>
        <label id="elh_refund_tbl_REFUND_AMOUNT" for="x_REFUND_AMOUNT" class="<?= $Page->LeftColumnClass ?>"><?= $Page->REFUND_AMOUNT->caption() ?><?= $Page->REFUND_AMOUNT->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->REFUND_AMOUNT->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_AMOUNT">
<input type="<?= $Page->REFUND_AMOUNT->getInputTextType() ?>" name="x_REFUND_AMOUNT" id="x_REFUND_AMOUNT" data-table="refund_tbl" data-field="x_REFUND_AMOUNT" value="<?= $Page->REFUND_AMOUNT->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->REFUND_AMOUNT->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->REFUND_AMOUNT->formatPattern()) ?>"<?= $Page->REFUND_AMOUNT->editAttributes() ?> aria-describedby="x_REFUND_AMOUNT_help">
<?= $Page->REFUND_AMOUNT->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->REFUND_AMOUNT->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->REFUND_DATE->Visible) { // REFUND_DATE ?>
    <div id="r_REFUND_DATE"<?= $Page->REFUND_DATE->rowAttributes() ?>>
        <label id="elh_refund_tbl_REFUND_DATE" for="x_REFUND_DATE" class="<?= $Page->LeftColumnClass ?>"><?= $Page->REFUND_DATE->caption() ?><?= $Page->REFUND_DATE->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->REFUND_DATE->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_DATE">
<input type="<?= $Page->REFUND_DATE->getInputTextType() ?>" name="x_REFUND_DATE" id="x_REFUND_DATE" data-table="refund_tbl" data-field="x_REFUND_DATE" value="<?= $Page->REFUND_DATE->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->REFUND_DATE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->REFUND_DATE->formatPattern()) ?>"<?= $Page->REFUND_DATE->editAttributes() ?> aria-describedby="x_REFUND_DATE_help">
<?= $Page->REFUND_DATE->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->REFUND_DATE->getErrorMessage() ?></div>
<?php if (!$Page->REFUND_DATE->ReadOnly && !$Page->REFUND_DATE->Disabled && !isset($Page->REFUND_DATE->EditAttrs["readonly"]) && !isset($Page->REFUND_DATE->EditAttrs["disabled"])) { ?>
<script<?= Nonce() ?>>
(function () {
    let format = "<?= DateFormat(0) ?>",
        options = {
            localization: {
                locale: ew.LANGUAGE_ID + "-u-nu-" + ew.getNumberingSystem(),
                hourCycle: format.match(/H/) ? "h24" : "h12",
                format,
                ...ew.language.phrase("datetimepicker")
            },
            display: {
                icons: {
                    previous: ew.IS_RTL ? "fa-solid fa-chevron-right" : "fa-solid fa-chevron-left",
                    next: ew.IS_RTL ? "fa-solid fa-chevron-left" : "fa-solid fa-chevron-right"
                },
                components: {
                    clock: !!format.match(/h/i) || !!format.match(/m/) || !!format.match(/s/i),
                    hours: !!format.match(/h/i),
                    minutes: !!format.match(/m/),
                    seconds: !!format.match(/s/i)
                },
                theme: ew.getPreferredTheme()
            }
        };
    ew.createDateTimePicker(
        "frefund_tbladd",
        "x_REFUND_DATE",
        ew.deepAssign({"useCurrent":false,"display":{"sideBySide":false}}, options),
        {"inputGroup":true}
    );
})();
</script>
<?php } ?>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->REFUND_STATUS->Visible) { // REFUND_STATUS ?>
    <div id="r_REFUND_STATUS"<?= $Page->REFUND_STATUS->rowAttributes() ?>>
        <label id="elh_refund_tbl_REFUND_STATUS" class="<?= $Page->LeftColumnClass ?>"><?= $Page->REFUND_STATUS->caption() ?><?= $Page->REFUND_STATUS->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->REFUND_STATUS->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_STATUS">
<template id="tp_x_REFUND_STATUS">
    <div class="form-check">
        <input type="radio" class="form-check-input" data-table="refund_tbl" data-field="x_REFUND_STATUS" name="x_REFUND_STATUS" id="x_REFUND_STATUS"<?= $Page->REFUND_STATUS->editAttributes() ?>>
        <label class="form-check-label"></label>
    </div>
</template>
<div id="dsl_x_REFUND_STATUS" class="ew-item-list"></div>
<selection-list hidden
    id="x_REFUND_STATUS"
    name="x_REFUND_STATUS"
    value="<?= HtmlEncode($Page->REFUND_STATUS->CurrentValue) ?>"
    data-type="select-one"
    data-template="tp_x_REFUND_STATUS"
    data-target="dsl_x_REFUND_STATUS"
    data-repeatcolumn="5"
    class="form-control<?= $Page->REFUND_STATUS->isInvalidClass() ?>"
    data-table="refund_tbl"
    data-field="x_REFUND_STATUS"
    data-value-separator="<?= $Page->REFUND_STATUS->displayValueSeparatorAttribute() ?>"
    <?= $Page->REFUND_STATUS->editAttributes() ?>></selection-list>
<?= $Page->REFUND_STATUS->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->REFUND_STATUS->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->REFUND_REASON->Visible) { // REFUND_REASON ?>
    <div id="r_REFUND_REASON"<?= $Page->REFUND_REASON->rowAttributes() ?>>
        <label id="elh_refund_tbl_REFUND_REASON" for="x_REFUND_REASON" class="<?= $Page->LeftColumnClass ?>"><?= $Page->REFUND_REASON->caption() ?><?= $Page->REFUND_REASON->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->REFUND_REASON->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_REASON">
<input type="<?= $Page->REFUND_REASON->getInputTextType() ?>" name="x_REFUND_REASON" id="x_REFUND_REASON" data-table="refund_tbl" data-field="x_REFUND_REASON" value="<?= $Page->REFUND_REASON->getEditValue() ?>" size="30" maxlength="255" placeholder="<?= HtmlEncode($Page->REFUND_REASON->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->REFUND_REASON->formatPattern()) ?>"<?= $Page->REFUND_REASON->editAttributes() ?> aria-describedby="x_REFUND_REASON_help">
<?= $Page->REFUND_REASON->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->REFUND_REASON->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->REFUND_TXN_ID->Visible) { // REFUND_TXN_ID ?>
    <div id="r_REFUND_TXN_ID"<?= $Page->REFUND_TXN_ID->rowAttributes() ?>>
        <label id="elh_refund_tbl_REFUND_TXN_ID" for="x_REFUND_TXN_ID" class="<?= $Page->LeftColumnClass ?>"><?= $Page->REFUND_TXN_ID->caption() ?><?= $Page->REFUND_TXN_ID->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->REFUND_TXN_ID->cellAttributes() ?>>
<span id="el_refund_tbl_REFUND_TXN_ID">
<input type="<?= $Page->REFUND_TXN_ID->getInputTextType() ?>" name="x_REFUND_TXN_ID" id="x_REFUND_TXN_ID" data-table="refund_tbl" data-field="x_REFUND_TXN_ID" value="<?= $Page->REFUND_TXN_ID->getEditValue() ?>" size="30" maxlength="50" placeholder="<?= HtmlEncode($Page->REFUND_TXN_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->REFUND_TXN_ID->formatPattern()) ?>"<?= $Page->REFUND_TXN_ID->editAttributes() ?> aria-describedby="x_REFUND_TXN_ID_help">
<?= $Page->REFUND_TXN_ID->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->REFUND_TXN_ID->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->CREATED_AT->Visible) { // CREATED_AT ?>
    <div id="r_CREATED_AT"<?= $Page->CREATED_AT->rowAttributes() ?>>
        <label id="elh_refund_tbl_CREATED_AT" for="x_CREATED_AT" class="<?= $Page->LeftColumnClass ?>"><?= $Page->CREATED_AT->caption() ?><?= $Page->CREATED_AT->Required ? Language()->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->CREATED_AT->cellAttributes() ?>>
<span id="el_refund_tbl_CREATED_AT">
<input type="<?= $Page->CREATED_AT->getInputTextType() ?>" name="x_CREATED_AT" id="x_CREATED_AT" data-table="refund_tbl" data-field="x_CREATED_AT" value="<?= $Page->CREATED_AT->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->CREATED_AT->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->CREATED_AT->formatPattern()) ?>"<?= $Page->CREATED_AT->editAttributes() ?> aria-describedby="x_CREATED_AT_help">
<?= $Page->CREATED_AT->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->CREATED_AT->getErrorMessage() ?></div>
<?php if (!$Page->CREATED_AT->ReadOnly && !$Page->CREATED_AT->Disabled && !isset($Page->CREATED_AT->EditAttrs["readonly"]) && !isset($Page->CREATED_AT->EditAttrs["disabled"])) { ?>
<script<?= Nonce() ?>>
(function () {
    let format = "<?= DateFormat(0) ?>",
        options = {
            localization: {
                locale: ew.LANGUAGE_ID + "-u-nu-" + ew.getNumberingSystem(),
                hourCycle: format.match(/H/) ? "h24" : "h12",
                format,
                ...ew.language.phrase("datetimepicker")
            },
            display: {
                icons: {
                    previous: ew.IS_RTL ? "fa-solid fa-chevron-right" : "fa-solid fa-chevron-left",
                    next: ew.IS_RTL ? "fa-solid fa-chevron-left" : "fa-solid fa-chevron-right"
                },
                components: {
                    clock: !!format.match(/h/i) || !!format.match(/m/) || !!format.match(/s/i),
                    hours: !!format.match(/h/i),
                    minutes: !!format.match(/m/),
                    seconds: !!format.match(/s/i)
                },
                theme: ew.getPreferredTheme()
            }
        };
    ew.createDateTimePicker(
        "frefund_tbladd",
        "x_CREATED_AT",
        ew.deepAssign({"useCurrent":false,"display":{"sideBySide":false}}, options),
        {"inputGroup":true}
    );
})();
</script>
<?php } ?>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?= $Page->IsModal ? '<template class="ew-modal-buttons">' : '<div class="row ew-buttons">' ?><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit" form="frefund_tbladd"><?= Language()->phrase("AddBtn") ?></button>
<?php if (IsJsonResponse()) { ?>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-bs-dismiss="modal"><?= Language()->phrase("CancelBtn") ?></button>
<?php } else { ?>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" form="frefund_tbladd" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= Language()->phrase("CancelBtn") ?></button>
<?php } ?>
    </div><!-- /buttons offset -->
<?= $Page->IsModal ? "</template>" : "</div>" ?><!-- /buttons .row -->
</form>
<?= $Page->getPageFooter() ?>
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

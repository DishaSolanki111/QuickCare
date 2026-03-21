<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_payment_report: currentTable } });
var currentPageID = ew.PAGE_ID = "search";
var currentForm;
var fview_payment_reportsearch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_payment_reportsearch")
        .setPageId("search")
<?php if ($Page->IsModal && $Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Add fields
        .addFields([
            ["PAYMENT_ID", [ew.Validators.integer], fields.PAYMENT_ID.isInvalid],
            ["TRANSACTION_ID", [], fields.TRANSACTION_ID.isInvalid],
            ["Patient_Name", [], fields.Patient_Name.isInvalid],
            ["Doctor_Name", [], fields.Doctor_Name.isInvalid],
            ["AMOUNT", [ew.Validators.float], fields.AMOUNT.isInvalid],
            ["PAYMENT_MODE", [], fields.PAYMENT_MODE.isInvalid],
            ["Payment_Status", [], fields.Payment_Status.isInvalid],
            ["PAYMENT_DATE", [ew.Validators.datetime(fields.PAYMENT_DATE.clientFormatPattern)], fields.PAYMENT_DATE.isInvalid],
            ["Day_Name", [], fields.Day_Name.isInvalid],
            ["Week_Number", [ew.Validators.integer], fields.Week_Number.isInvalid],
            ["Month_Number", [ew.Validators.integer], fields.Month_Number.isInvalid],
            ["Month_Name", [], fields.Month_Name.isInvalid],
            ["Year", [ew.Validators.integer], fields.Year.isInvalid]
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
            "PAYMENT_MODE": <?= $Page->PAYMENT_MODE->toClientList($Page) ?>,
            "Payment_Status": <?= $Page->Payment_Status->toClientList($Page) ?>,
        })
        .build();
    window[form.id] = form;
<?php if ($Page->IsModal) { ?>
    currentAdvancedSearchForm = form;
<?php } else { ?>
    currentForm = form;
<?php } ?>
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
<form name="fview_payment_reportsearch" id="fview_payment_reportsearch" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_payment_report">
<input type="hidden" name="action" id="action" value="search">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div class="ew-search-div"><!-- page* -->
<?php if ($Page->PAYMENT_ID->Visible) { // PAYMENT_ID ?>
    <div id="r_PAYMENT_ID" class="row"<?= $Page->PAYMENT_ID->rowAttributes() ?>>
        <label for="x_PAYMENT_ID" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_PAYMENT_ID"><?= $Page->PAYMENT_ID->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_PAYMENT_ID" id="z_PAYMENT_ID" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->PAYMENT_ID->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_PAYMENT_ID" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->PAYMENT_ID->getInputTextType() ?>" name="x_PAYMENT_ID" id="x_PAYMENT_ID" data-table="view_payment_report" data-field="x_PAYMENT_ID" value="<?= $Page->PAYMENT_ID->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->PAYMENT_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->PAYMENT_ID->formatPattern()) ?>"<?= $Page->PAYMENT_ID->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->PAYMENT_ID->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->TRANSACTION_ID->Visible) { // TRANSACTION_ID ?>
    <div id="r_TRANSACTION_ID" class="row"<?= $Page->TRANSACTION_ID->rowAttributes() ?>>
        <label for="x_TRANSACTION_ID" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_TRANSACTION_ID"><?= $Page->TRANSACTION_ID->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_TRANSACTION_ID" id="z_TRANSACTION_ID" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->TRANSACTION_ID->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_TRANSACTION_ID" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->TRANSACTION_ID->getInputTextType() ?>" name="x_TRANSACTION_ID" id="x_TRANSACTION_ID" data-table="view_payment_report" data-field="x_TRANSACTION_ID" value="<?= $Page->TRANSACTION_ID->getEditValue() ?>" size="30" maxlength="36" placeholder="<?= HtmlEncode($Page->TRANSACTION_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->TRANSACTION_ID->formatPattern()) ?>"<?= $Page->TRANSACTION_ID->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->TRANSACTION_ID->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
    <div id="r_Patient_Name" class="row"<?= $Page->Patient_Name->rowAttributes() ?>>
        <label for="x_Patient_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Patient_Name"><?= $Page->Patient_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Patient_Name" id="z_Patient_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Patient_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Patient_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Patient_Name->getInputTextType() ?>" name="x_Patient_Name" id="x_Patient_Name" data-table="view_payment_report" data-field="x_Patient_Name" value="<?= $Page->Patient_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Patient_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Patient_Name->formatPattern()) ?>"<?= $Page->Patient_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Patient_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
    <div id="r_Doctor_Name" class="row"<?= $Page->Doctor_Name->rowAttributes() ?>>
        <label for="x_Doctor_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Doctor_Name"><?= $Page->Doctor_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Doctor_Name" id="z_Doctor_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Doctor_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Doctor_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Doctor_Name->getInputTextType() ?>" name="x_Doctor_Name" id="x_Doctor_Name" data-table="view_payment_report" data-field="x_Doctor_Name" value="<?= $Page->Doctor_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Doctor_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Doctor_Name->formatPattern()) ?>"<?= $Page->Doctor_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Doctor_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->AMOUNT->Visible) { // AMOUNT ?>
    <div id="r_AMOUNT" class="row"<?= $Page->AMOUNT->rowAttributes() ?>>
        <label for="x_AMOUNT" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_AMOUNT"><?= $Page->AMOUNT->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_AMOUNT" id="z_AMOUNT" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->AMOUNT->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_AMOUNT" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->AMOUNT->getInputTextType() ?>" name="x_AMOUNT" id="x_AMOUNT" data-table="view_payment_report" data-field="x_AMOUNT" value="<?= $Page->AMOUNT->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->AMOUNT->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->AMOUNT->formatPattern()) ?>"<?= $Page->AMOUNT->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->AMOUNT->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->PAYMENT_MODE->Visible) { // PAYMENT_MODE ?>
    <div id="r_PAYMENT_MODE" class="row"<?= $Page->PAYMENT_MODE->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_PAYMENT_MODE"><?= $Page->PAYMENT_MODE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_PAYMENT_MODE" id="z_PAYMENT_MODE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->PAYMENT_MODE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_PAYMENT_MODE" class="ew-search-field ew-search-field-single">
                <template id="tp_x_PAYMENT_MODE">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_payment_report" data-field="x_PAYMENT_MODE" name="x_PAYMENT_MODE" id="x_PAYMENT_MODE"<?= $Page->PAYMENT_MODE->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_PAYMENT_MODE" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_PAYMENT_MODE"
                    name="x_PAYMENT_MODE"
                    value="<?= HtmlEncode($Page->PAYMENT_MODE->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_PAYMENT_MODE"
                    data-target="dsl_x_PAYMENT_MODE"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->PAYMENT_MODE->isInvalidClass() ?>"
                    data-table="view_payment_report"
                    data-field="x_PAYMENT_MODE"
                    data-value-separator="<?= $Page->PAYMENT_MODE->displayValueSeparatorAttribute() ?>"
                    <?= $Page->PAYMENT_MODE->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->PAYMENT_MODE->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Payment_Status->Visible) { // Payment_Status ?>
    <div id="r_Payment_Status" class="row"<?= $Page->Payment_Status->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Payment_Status"><?= $Page->Payment_Status->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Payment_Status" id="z_Payment_Status" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Payment_Status->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Payment_Status" class="ew-search-field ew-search-field-single">
                <template id="tp_x_Payment_Status">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_payment_report" data-field="x_Payment_Status" name="x_Payment_Status" id="x_Payment_Status"<?= $Page->Payment_Status->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_Payment_Status" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_Payment_Status"
                    name="x_Payment_Status"
                    value="<?= HtmlEncode($Page->Payment_Status->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_Payment_Status"
                    data-target="dsl_x_Payment_Status"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->Payment_Status->isInvalidClass() ?>"
                    data-table="view_payment_report"
                    data-field="x_Payment_Status"
                    data-value-separator="<?= $Page->Payment_Status->displayValueSeparatorAttribute() ?>"
                    <?= $Page->Payment_Status->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->Payment_Status->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->PAYMENT_DATE->Visible) { // PAYMENT_DATE ?>
    <div id="r_PAYMENT_DATE" class="row"<?= $Page->PAYMENT_DATE->rowAttributes() ?>>
        <label for="x_PAYMENT_DATE" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_PAYMENT_DATE"><?= $Page->PAYMENT_DATE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_PAYMENT_DATE" id="z_PAYMENT_DATE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->PAYMENT_DATE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_PAYMENT_DATE" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->PAYMENT_DATE->getInputTextType() ?>" name="x_PAYMENT_DATE" id="x_PAYMENT_DATE" data-table="view_payment_report" data-field="x_PAYMENT_DATE" value="<?= $Page->PAYMENT_DATE->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->PAYMENT_DATE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->PAYMENT_DATE->formatPattern()) ?>"<?= $Page->PAYMENT_DATE->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->PAYMENT_DATE->getErrorMessage(false) ?></div>
                <?php if (!$Page->PAYMENT_DATE->ReadOnly && !$Page->PAYMENT_DATE->Disabled && !isset($Page->PAYMENT_DATE->EditAttrs["readonly"]) && !isset($Page->PAYMENT_DATE->EditAttrs["disabled"])) { ?>
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
                        "fview_payment_reportsearch",
                        "x_PAYMENT_DATE",
                        ew.deepAssign({"useCurrent":false,"display":{"sideBySide":false}}, options),
                        {"inputGroup":true}
                    );
                })();
                </script>
                <?php } ?>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Day_Name->Visible) { // Day_Name ?>
    <div id="r_Day_Name" class="row"<?= $Page->Day_Name->rowAttributes() ?>>
        <label for="x_Day_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Day_Name"><?= $Page->Day_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Day_Name" id="z_Day_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Day_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Day_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Day_Name->getInputTextType() ?>" name="x_Day_Name" id="x_Day_Name" data-table="view_payment_report" data-field="x_Day_Name" value="<?= $Page->Day_Name->getEditValue() ?>" size="30" maxlength="9" placeholder="<?= HtmlEncode($Page->Day_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Day_Name->formatPattern()) ?>"<?= $Page->Day_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Day_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Week_Number->Visible) { // Week_Number ?>
    <div id="r_Week_Number" class="row"<?= $Page->Week_Number->rowAttributes() ?>>
        <label for="x_Week_Number" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Week_Number"><?= $Page->Week_Number->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Week_Number" id="z_Week_Number" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Week_Number->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Week_Number" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Week_Number->getInputTextType() ?>" name="x_Week_Number" id="x_Week_Number" data-table="view_payment_report" data-field="x_Week_Number" value="<?= $Page->Week_Number->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Week_Number->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Week_Number->formatPattern()) ?>"<?= $Page->Week_Number->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Week_Number->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Month_Number->Visible) { // Month_Number ?>
    <div id="r_Month_Number" class="row"<?= $Page->Month_Number->rowAttributes() ?>>
        <label for="x_Month_Number" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Month_Number"><?= $Page->Month_Number->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Month_Number" id="z_Month_Number" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Month_Number->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Month_Number" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Month_Number->getInputTextType() ?>" name="x_Month_Number" id="x_Month_Number" data-table="view_payment_report" data-field="x_Month_Number" value="<?= $Page->Month_Number->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Month_Number->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Month_Number->formatPattern()) ?>"<?= $Page->Month_Number->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Month_Number->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Month_Name->Visible) { // Month_Name ?>
    <div id="r_Month_Name" class="row"<?= $Page->Month_Name->rowAttributes() ?>>
        <label for="x_Month_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Month_Name"><?= $Page->Month_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Month_Name" id="z_Month_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Month_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Month_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Month_Name->getInputTextType() ?>" name="x_Month_Name" id="x_Month_Name" data-table="view_payment_report" data-field="x_Month_Name" value="<?= $Page->Month_Name->getEditValue() ?>" size="30" maxlength="9" placeholder="<?= HtmlEncode($Page->Month_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Month_Name->formatPattern()) ?>"<?= $Page->Month_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Month_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Year->Visible) { // Year ?>
    <div id="r_Year" class="row"<?= $Page->Year->rowAttributes() ?>>
        <label for="x_Year" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_payment_report_Year"><?= $Page->Year->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Year" id="z_Year" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Year->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_payment_report_Year" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Year->getInputTextType() ?>" name="x_Year" id="x_Year" data-table="view_payment_report" data-field="x_Year" value="<?= $Page->Year->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Year->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Year->formatPattern()) ?>"<?= $Page->Year->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Year->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?= $Page->IsModal ? '<template class="ew-modal-buttons">' : '<div class="row ew-buttons">' ?><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
        <button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit" form="fview_payment_reportsearch"><?= Language()->phrase("Search") ?></button>
        <?php if ($Page->IsModal) { ?>
        <button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" form="fview_payment_reportsearch"><?= Language()->phrase("CancelBtn") ?></button>
        <?php } else { ?>
        <button class="btn btn-secondary ew-btn" name="btn-reset" id="btn-reset" type="button" form="fview_payment_reportsearch" data-ew-action="reload"><?= Language()->phrase("Reset") ?></button>
        <?php } ?>
    </div><!-- /buttons offset -->
<?= $Page->IsModal ? "</template>" : "</div>" ?><!-- /buttons .row -->
</form>
<?= $Page->getPageFooter() ?>
<script<?= Nonce() ?>>
// Field event handlers
ew.on("head", function() {
    ew.addEventHandlers("view_payment_report");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>

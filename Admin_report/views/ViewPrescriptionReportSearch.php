<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_prescription_report: currentTable } });
var currentPageID = ew.PAGE_ID = "search";
var currentForm;
var fview_prescription_reportsearch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_prescription_reportsearch")
        .setPageId("search")
<?php if ($Page->IsModal && $Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Add fields
        .addFields([
            ["PRESCRIPTION_ID", [ew.Validators.integer], fields.PRESCRIPTION_ID.isInvalid],
            ["ISSUE_DATE", [ew.Validators.datetime(fields.ISSUE_DATE.clientFormatPattern)], fields.ISSUE_DATE.isInvalid],
            ["Patient_Name", [], fields.Patient_Name.isInvalid],
            ["Doctor_Name", [], fields.Doctor_Name.isInvalid],
            ["Specialisation", [], fields.Specialisation.isInvalid],
            ["SYMPTOMS", [], fields.SYMPTOMS.isInvalid],
            ["DIAGNOSIS", [], fields.DIAGNOSIS.isInvalid],
            ["DIABETES", [], fields.DIABETES.isInvalid],
            ["BLOOD_PRESSURE", [ew.Validators.integer], fields.BLOOD_PRESSURE.isInvalid],
            ["ADDITIONAL_NOTES", [], fields.ADDITIONAL_NOTES.isInvalid]
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
            "DIABETES": <?= $Page->DIABETES->toClientList($Page) ?>,
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
<form name="fview_prescription_reportsearch" id="fview_prescription_reportsearch" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_prescription_report">
<input type="hidden" name="action" id="action" value="search">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div class="ew-search-div"><!-- page* -->
<?php if ($Page->PRESCRIPTION_ID->Visible) { // PRESCRIPTION_ID ?>
    <div id="r_PRESCRIPTION_ID" class="row"<?= $Page->PRESCRIPTION_ID->rowAttributes() ?>>
        <label for="x_PRESCRIPTION_ID" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_PRESCRIPTION_ID"><?= $Page->PRESCRIPTION_ID->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_PRESCRIPTION_ID" id="z_PRESCRIPTION_ID" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->PRESCRIPTION_ID->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_PRESCRIPTION_ID" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->PRESCRIPTION_ID->getInputTextType() ?>" name="x_PRESCRIPTION_ID" id="x_PRESCRIPTION_ID" data-table="view_prescription_report" data-field="x_PRESCRIPTION_ID" value="<?= $Page->PRESCRIPTION_ID->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->PRESCRIPTION_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->PRESCRIPTION_ID->formatPattern()) ?>"<?= $Page->PRESCRIPTION_ID->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->PRESCRIPTION_ID->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->ISSUE_DATE->Visible) { // ISSUE_DATE ?>
    <div id="r_ISSUE_DATE" class="row"<?= $Page->ISSUE_DATE->rowAttributes() ?>>
        <label for="x_ISSUE_DATE" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_ISSUE_DATE"><?= $Page->ISSUE_DATE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_ISSUE_DATE" id="z_ISSUE_DATE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->ISSUE_DATE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_ISSUE_DATE" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->ISSUE_DATE->getInputTextType() ?>" name="x_ISSUE_DATE" id="x_ISSUE_DATE" data-table="view_prescription_report" data-field="x_ISSUE_DATE" value="<?= $Page->ISSUE_DATE->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->ISSUE_DATE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->ISSUE_DATE->formatPattern()) ?>"<?= $Page->ISSUE_DATE->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->ISSUE_DATE->getErrorMessage(false) ?></div>
                <?php if (!$Page->ISSUE_DATE->ReadOnly && !$Page->ISSUE_DATE->Disabled && !isset($Page->ISSUE_DATE->EditAttrs["readonly"]) && !isset($Page->ISSUE_DATE->EditAttrs["disabled"])) { ?>
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
                        "fview_prescription_reportsearch",
                        "x_ISSUE_DATE",
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
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
    <div id="r_Patient_Name" class="row"<?= $Page->Patient_Name->rowAttributes() ?>>
        <label for="x_Patient_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_Patient_Name"><?= $Page->Patient_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Patient_Name" id="z_Patient_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Patient_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_Patient_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Patient_Name->getInputTextType() ?>" name="x_Patient_Name" id="x_Patient_Name" data-table="view_prescription_report" data-field="x_Patient_Name" value="<?= $Page->Patient_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Patient_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Patient_Name->formatPattern()) ?>"<?= $Page->Patient_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Patient_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
    <div id="r_Doctor_Name" class="row"<?= $Page->Doctor_Name->rowAttributes() ?>>
        <label for="x_Doctor_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_Doctor_Name"><?= $Page->Doctor_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Doctor_Name" id="z_Doctor_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Doctor_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_Doctor_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Doctor_Name->getInputTextType() ?>" name="x_Doctor_Name" id="x_Doctor_Name" data-table="view_prescription_report" data-field="x_Doctor_Name" value="<?= $Page->Doctor_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Doctor_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Doctor_Name->formatPattern()) ?>"<?= $Page->Doctor_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Doctor_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Specialisation->Visible) { // Specialisation ?>
    <div id="r_Specialisation" class="row"<?= $Page->Specialisation->rowAttributes() ?>>
        <label for="x_Specialisation" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_Specialisation"><?= $Page->Specialisation->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Specialisation" id="z_Specialisation" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Specialisation->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_Specialisation" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Specialisation->getInputTextType() ?>" name="x_Specialisation" id="x_Specialisation" data-table="view_prescription_report" data-field="x_Specialisation" value="<?= $Page->Specialisation->getEditValue() ?>" size="30" maxlength="50" placeholder="<?= HtmlEncode($Page->Specialisation->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Specialisation->formatPattern()) ?>"<?= $Page->Specialisation->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Specialisation->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->SYMPTOMS->Visible) { // SYMPTOMS ?>
    <div id="r_SYMPTOMS" class="row"<?= $Page->SYMPTOMS->rowAttributes() ?>>
        <label for="x_SYMPTOMS" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_SYMPTOMS"><?= $Page->SYMPTOMS->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_SYMPTOMS" id="z_SYMPTOMS" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->SYMPTOMS->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_SYMPTOMS" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->SYMPTOMS->getInputTextType() ?>" name="x_SYMPTOMS" id="x_SYMPTOMS" data-table="view_prescription_report" data-field="x_SYMPTOMS" value="<?= $Page->SYMPTOMS->getEditValue() ?>" size="30" maxlength="65535" placeholder="<?= HtmlEncode($Page->SYMPTOMS->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->SYMPTOMS->formatPattern()) ?>"<?= $Page->SYMPTOMS->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->SYMPTOMS->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->DIAGNOSIS->Visible) { // DIAGNOSIS ?>
    <div id="r_DIAGNOSIS" class="row"<?= $Page->DIAGNOSIS->rowAttributes() ?>>
        <label for="x_DIAGNOSIS" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_DIAGNOSIS"><?= $Page->DIAGNOSIS->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_DIAGNOSIS" id="z_DIAGNOSIS" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->DIAGNOSIS->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_DIAGNOSIS" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->DIAGNOSIS->getInputTextType() ?>" name="x_DIAGNOSIS" id="x_DIAGNOSIS" data-table="view_prescription_report" data-field="x_DIAGNOSIS" value="<?= $Page->DIAGNOSIS->getEditValue() ?>" size="30" maxlength="65535" placeholder="<?= HtmlEncode($Page->DIAGNOSIS->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->DIAGNOSIS->formatPattern()) ?>"<?= $Page->DIAGNOSIS->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->DIAGNOSIS->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->DIABETES->Visible) { // DIABETES ?>
    <div id="r_DIABETES" class="row"<?= $Page->DIABETES->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_DIABETES"><?= $Page->DIABETES->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_DIABETES" id="z_DIABETES" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->DIABETES->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_DIABETES" class="ew-search-field ew-search-field-single">
                <template id="tp_x_DIABETES">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_prescription_report" data-field="x_DIABETES" name="x_DIABETES" id="x_DIABETES"<?= $Page->DIABETES->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_DIABETES" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_DIABETES"
                    name="x_DIABETES"
                    value="<?= HtmlEncode($Page->DIABETES->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_DIABETES"
                    data-target="dsl_x_DIABETES"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->DIABETES->isInvalidClass() ?>"
                    data-table="view_prescription_report"
                    data-field="x_DIABETES"
                    data-value-separator="<?= $Page->DIABETES->displayValueSeparatorAttribute() ?>"
                    <?= $Page->DIABETES->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->DIABETES->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->BLOOD_PRESSURE->Visible) { // BLOOD_PRESSURE ?>
    <div id="r_BLOOD_PRESSURE" class="row"<?= $Page->BLOOD_PRESSURE->rowAttributes() ?>>
        <label for="x_BLOOD_PRESSURE" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_BLOOD_PRESSURE"><?= $Page->BLOOD_PRESSURE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_BLOOD_PRESSURE" id="z_BLOOD_PRESSURE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->BLOOD_PRESSURE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_BLOOD_PRESSURE" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->BLOOD_PRESSURE->getInputTextType() ?>" name="x_BLOOD_PRESSURE" id="x_BLOOD_PRESSURE" data-table="view_prescription_report" data-field="x_BLOOD_PRESSURE" value="<?= $Page->BLOOD_PRESSURE->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->BLOOD_PRESSURE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->BLOOD_PRESSURE->formatPattern()) ?>"<?= $Page->BLOOD_PRESSURE->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->BLOOD_PRESSURE->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->ADDITIONAL_NOTES->Visible) { // ADDITIONAL_NOTES ?>
    <div id="r_ADDITIONAL_NOTES" class="row"<?= $Page->ADDITIONAL_NOTES->rowAttributes() ?>>
        <label for="x_ADDITIONAL_NOTES" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_prescription_report_ADDITIONAL_NOTES"><?= $Page->ADDITIONAL_NOTES->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_ADDITIONAL_NOTES" id="z_ADDITIONAL_NOTES" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->ADDITIONAL_NOTES->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_prescription_report_ADDITIONAL_NOTES" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->ADDITIONAL_NOTES->getInputTextType() ?>" name="x_ADDITIONAL_NOTES" id="x_ADDITIONAL_NOTES" data-table="view_prescription_report" data-field="x_ADDITIONAL_NOTES" value="<?= $Page->ADDITIONAL_NOTES->getEditValue() ?>" size="30" maxlength="65535" placeholder="<?= HtmlEncode($Page->ADDITIONAL_NOTES->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->ADDITIONAL_NOTES->formatPattern()) ?>"<?= $Page->ADDITIONAL_NOTES->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->ADDITIONAL_NOTES->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?= $Page->IsModal ? '<template class="ew-modal-buttons">' : '<div class="row ew-buttons">' ?><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
        <button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit" form="fview_prescription_reportsearch"><?= Language()->phrase("Search") ?></button>
        <?php if ($Page->IsModal) { ?>
        <button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" form="fview_prescription_reportsearch"><?= Language()->phrase("CancelBtn") ?></button>
        <?php } else { ?>
        <button class="btn btn-secondary ew-btn" name="btn-reset" id="btn-reset" type="button" form="fview_prescription_reportsearch" data-ew-action="reload"><?= Language()->phrase("Reset") ?></button>
        <?php } ?>
    </div><!-- /buttons offset -->
<?= $Page->IsModal ? "</template>" : "</div>" ?><!-- /buttons .row -->
</form>
<?= $Page->getPageFooter() ?>
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

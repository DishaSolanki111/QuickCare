<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_doctor_report: currentTable } });
var currentPageID = ew.PAGE_ID = "search";
var currentForm;
var fview_doctor_reportsearch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_doctor_reportsearch")
        .setPageId("search")
<?php if ($Page->IsModal && $Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Add fields
        .addFields([
            ["DOCTOR_ID", [ew.Validators.integer], fields.DOCTOR_ID.isInvalid],
            ["Doctor_Name", [], fields.Doctor_Name.isInvalid],
            ["Specialisation", [], fields.Specialisation.isInvalid],
            ["EDUCATION", [], fields.EDUCATION.isInvalid],
            ["Doctor_Status", [], fields.Doctor_Status.isInvalid],
            ["APPOINTMENT_ID", [ew.Validators.integer], fields.APPOINTMENT_ID.isInvalid],
            ["APPOINTMENT_DATE", [ew.Validators.datetime(fields.APPOINTMENT_DATE.clientFormatPattern)], fields.APPOINTMENT_DATE.isInvalid],
            ["Month_Name", [], fields.Month_Name.isInvalid],
            ["Month_Number", [ew.Validators.integer], fields.Month_Number.isInvalid],
            ["Year", [ew.Validators.integer], fields.Year.isInvalid],
            ["Appointment_Status", [], fields.Appointment_Status.isInvalid],
            ["Total_Patients", [ew.Validators.integer], fields.Total_Patients.isInvalid],
            ["Avg_Rating", [ew.Validators.float], fields.Avg_Rating.isInvalid]
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
            "Doctor_Status": <?= $Page->Doctor_Status->toClientList($Page) ?>,
            "Appointment_Status": <?= $Page->Appointment_Status->toClientList($Page) ?>,
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
<form name="fview_doctor_reportsearch" id="fview_doctor_reportsearch" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_doctor_report">
<input type="hidden" name="action" id="action" value="search">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div class="ew-search-div"><!-- page* -->
<?php if ($Page->DOCTOR_ID->Visible) { // DOCTOR_ID ?>
    <div id="r_DOCTOR_ID" class="row"<?= $Page->DOCTOR_ID->rowAttributes() ?>>
        <label for="x_DOCTOR_ID" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_DOCTOR_ID"><?= $Page->DOCTOR_ID->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_DOCTOR_ID" id="z_DOCTOR_ID" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->DOCTOR_ID->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_DOCTOR_ID" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->DOCTOR_ID->getInputTextType() ?>" name="x_DOCTOR_ID" id="x_DOCTOR_ID" data-table="view_doctor_report" data-field="x_DOCTOR_ID" value="<?= $Page->DOCTOR_ID->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->DOCTOR_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->DOCTOR_ID->formatPattern()) ?>"<?= $Page->DOCTOR_ID->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->DOCTOR_ID->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
    <div id="r_Doctor_Name" class="row"<?= $Page->Doctor_Name->rowAttributes() ?>>
        <label for="x_Doctor_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Doctor_Name"><?= $Page->Doctor_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Doctor_Name" id="z_Doctor_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Doctor_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Doctor_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Doctor_Name->getInputTextType() ?>" name="x_Doctor_Name" id="x_Doctor_Name" data-table="view_doctor_report" data-field="x_Doctor_Name" value="<?= $Page->Doctor_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Doctor_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Doctor_Name->formatPattern()) ?>"<?= $Page->Doctor_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Doctor_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Specialisation->Visible) { // Specialisation ?>
    <div id="r_Specialisation" class="row"<?= $Page->Specialisation->rowAttributes() ?>>
        <label for="x_Specialisation" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Specialisation"><?= $Page->Specialisation->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Specialisation" id="z_Specialisation" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Specialisation->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Specialisation" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Specialisation->getInputTextType() ?>" name="x_Specialisation" id="x_Specialisation" data-table="view_doctor_report" data-field="x_Specialisation" value="<?= $Page->Specialisation->getEditValue() ?>" size="30" maxlength="50" placeholder="<?= HtmlEncode($Page->Specialisation->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Specialisation->formatPattern()) ?>"<?= $Page->Specialisation->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Specialisation->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->EDUCATION->Visible) { // EDUCATION ?>
    <div id="r_EDUCATION" class="row"<?= $Page->EDUCATION->rowAttributes() ?>>
        <label for="x_EDUCATION" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_EDUCATION"><?= $Page->EDUCATION->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_EDUCATION" id="z_EDUCATION" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->EDUCATION->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_EDUCATION" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->EDUCATION->getInputTextType() ?>" name="x_EDUCATION" id="x_EDUCATION" data-table="view_doctor_report" data-field="x_EDUCATION" value="<?= $Page->EDUCATION->getEditValue() ?>" size="30" maxlength="50" placeholder="<?= HtmlEncode($Page->EDUCATION->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->EDUCATION->formatPattern()) ?>"<?= $Page->EDUCATION->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->EDUCATION->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Doctor_Status->Visible) { // Doctor_Status ?>
    <div id="r_Doctor_Status" class="row"<?= $Page->Doctor_Status->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Doctor_Status"><?= $Page->Doctor_Status->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Doctor_Status" id="z_Doctor_Status" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Doctor_Status->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Doctor_Status" class="ew-search-field ew-search-field-single">
                <template id="tp_x_Doctor_Status">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_doctor_report" data-field="x_Doctor_Status" name="x_Doctor_Status" id="x_Doctor_Status"<?= $Page->Doctor_Status->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_Doctor_Status" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_Doctor_Status"
                    name="x_Doctor_Status"
                    value="<?= HtmlEncode($Page->Doctor_Status->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_Doctor_Status"
                    data-target="dsl_x_Doctor_Status"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->Doctor_Status->isInvalidClass() ?>"
                    data-table="view_doctor_report"
                    data-field="x_Doctor_Status"
                    data-value-separator="<?= $Page->Doctor_Status->displayValueSeparatorAttribute() ?>"
                    <?= $Page->Doctor_Status->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->Doctor_Status->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->APPOINTMENT_ID->Visible) { // APPOINTMENT_ID ?>
    <div id="r_APPOINTMENT_ID" class="row"<?= $Page->APPOINTMENT_ID->rowAttributes() ?>>
        <label for="x_APPOINTMENT_ID" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_APPOINTMENT_ID"><?= $Page->APPOINTMENT_ID->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_APPOINTMENT_ID" id="z_APPOINTMENT_ID" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->APPOINTMENT_ID->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_APPOINTMENT_ID" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->APPOINTMENT_ID->getInputTextType() ?>" name="x_APPOINTMENT_ID" id="x_APPOINTMENT_ID" data-table="view_doctor_report" data-field="x_APPOINTMENT_ID" value="<?= $Page->APPOINTMENT_ID->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->APPOINTMENT_ID->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->APPOINTMENT_ID->formatPattern()) ?>"<?= $Page->APPOINTMENT_ID->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->APPOINTMENT_ID->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->APPOINTMENT_DATE->Visible) { // APPOINTMENT_DATE ?>
    <div id="r_APPOINTMENT_DATE" class="row"<?= $Page->APPOINTMENT_DATE->rowAttributes() ?>>
        <label for="x_APPOINTMENT_DATE" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_APPOINTMENT_DATE"><?= $Page->APPOINTMENT_DATE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_APPOINTMENT_DATE" id="z_APPOINTMENT_DATE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_APPOINTMENT_DATE" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->APPOINTMENT_DATE->getInputTextType() ?>" name="x_APPOINTMENT_DATE" id="x_APPOINTMENT_DATE" data-table="view_doctor_report" data-field="x_APPOINTMENT_DATE" value="<?= $Page->APPOINTMENT_DATE->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->APPOINTMENT_DATE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->APPOINTMENT_DATE->formatPattern()) ?>"<?= $Page->APPOINTMENT_DATE->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->APPOINTMENT_DATE->getErrorMessage(false) ?></div>
                <?php if (!$Page->APPOINTMENT_DATE->ReadOnly && !$Page->APPOINTMENT_DATE->Disabled && !isset($Page->APPOINTMENT_DATE->EditAttrs["readonly"]) && !isset($Page->APPOINTMENT_DATE->EditAttrs["disabled"])) { ?>
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
                        "fview_doctor_reportsearch",
                        "x_APPOINTMENT_DATE",
                        ew.deepAssign({"useCurrent":false,"display":{"sideBySide":false}}, options),
                        {"inputGroup":true,"minDateField":null,"maxDateField":null}
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
<?php if ($Page->Month_Name->Visible) { // Month_Name ?>
    <div id="r_Month_Name" class="row"<?= $Page->Month_Name->rowAttributes() ?>>
        <label for="x_Month_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Month_Name"><?= $Page->Month_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Month_Name" id="z_Month_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Month_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Month_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Month_Name->getInputTextType() ?>" name="x_Month_Name" id="x_Month_Name" data-table="view_doctor_report" data-field="x_Month_Name" value="<?= $Page->Month_Name->getEditValue() ?>" size="30" maxlength="9" placeholder="<?= HtmlEncode($Page->Month_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Month_Name->formatPattern()) ?>"<?= $Page->Month_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Month_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Month_Number->Visible) { // Month_Number ?>
    <div id="r_Month_Number" class="row"<?= $Page->Month_Number->rowAttributes() ?>>
        <label for="x_Month_Number" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Month_Number"><?= $Page->Month_Number->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Month_Number" id="z_Month_Number" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Month_Number->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Month_Number" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Month_Number->getInputTextType() ?>" name="x_Month_Number" id="x_Month_Number" data-table="view_doctor_report" data-field="x_Month_Number" value="<?= $Page->Month_Number->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Month_Number->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Month_Number->formatPattern()) ?>"<?= $Page->Month_Number->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Month_Number->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Year->Visible) { // Year ?>
    <div id="r_Year" class="row"<?= $Page->Year->rowAttributes() ?>>
        <label for="x_Year" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Year"><?= $Page->Year->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Year" id="z_Year" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Year->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Year" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Year->getInputTextType() ?>" name="x_Year" id="x_Year" data-table="view_doctor_report" data-field="x_Year" value="<?= $Page->Year->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Year->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Year->formatPattern()) ?>"<?= $Page->Year->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Year->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Appointment_Status->Visible) { // Appointment_Status ?>
    <div id="r_Appointment_Status" class="row"<?= $Page->Appointment_Status->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Appointment_Status"><?= $Page->Appointment_Status->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Appointment_Status" id="z_Appointment_Status" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Appointment_Status->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Appointment_Status" class="ew-search-field ew-search-field-single">
                <template id="tp_x_Appointment_Status">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_doctor_report" data-field="x_Appointment_Status" name="x_Appointment_Status" id="x_Appointment_Status"<?= $Page->Appointment_Status->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_Appointment_Status" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_Appointment_Status"
                    name="x_Appointment_Status"
                    value="<?= HtmlEncode($Page->Appointment_Status->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_Appointment_Status"
                    data-target="dsl_x_Appointment_Status"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->Appointment_Status->isInvalidClass() ?>"
                    data-table="view_doctor_report"
                    data-field="x_Appointment_Status"
                    data-value-separator="<?= $Page->Appointment_Status->displayValueSeparatorAttribute() ?>"
                    <?= $Page->Appointment_Status->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->Appointment_Status->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Total_Patients->Visible) { // Total_Patients ?>
    <div id="r_Total_Patients" class="row"<?= $Page->Total_Patients->rowAttributes() ?>>
        <label for="x_Total_Patients" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Total_Patients"><?= $Page->Total_Patients->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Total_Patients" id="z_Total_Patients" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Total_Patients->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Total_Patients" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Total_Patients->getInputTextType() ?>" name="x_Total_Patients" id="x_Total_Patients" data-table="view_doctor_report" data-field="x_Total_Patients" value="<?= $Page->Total_Patients->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Total_Patients->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Total_Patients->formatPattern()) ?>"<?= $Page->Total_Patients->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Total_Patients->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Avg_Rating->Visible) { // Avg_Rating ?>
    <div id="r_Avg_Rating" class="row"<?= $Page->Avg_Rating->rowAttributes() ?>>
        <label for="x_Avg_Rating" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_doctor_report_Avg_Rating"><?= $Page->Avg_Rating->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Avg_Rating" id="z_Avg_Rating" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Avg_Rating->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_doctor_report_Avg_Rating" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Avg_Rating->getInputTextType() ?>" name="x_Avg_Rating" id="x_Avg_Rating" data-table="view_doctor_report" data-field="x_Avg_Rating" value="<?= $Page->Avg_Rating->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Avg_Rating->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Avg_Rating->formatPattern()) ?>"<?= $Page->Avg_Rating->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Avg_Rating->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?= $Page->IsModal ? '<template class="ew-modal-buttons">' : '<div class="row ew-buttons">' ?><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
        <button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit" form="fview_doctor_reportsearch"><?= Language()->phrase("Search") ?></button>
        <?php if ($Page->IsModal) { ?>
        <button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" form="fview_doctor_reportsearch"><?= Language()->phrase("CancelBtn") ?></button>
        <?php } else { ?>
        <button class="btn btn-secondary ew-btn" name="btn-reset" id="btn-reset" type="button" form="fview_doctor_reportsearch" data-ew-action="reload"><?= Language()->phrase("Reset") ?></button>
        <?php } ?>
    </div><!-- /buttons offset -->
<?= $Page->IsModal ? "</template>" : "</div>" ?><!-- /buttons .row -->
</form>
<?= $Page->getPageFooter() ?>
<script<?= Nonce() ?>>
// Field event handlers
ew.on("head", function() {
    ew.addEventHandlers("view_doctor_report");
});
</script>
<script<?= Nonce() ?>>
ew.on("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>

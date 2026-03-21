<?php

namespace PHPMaker2026\Project2;
?>
<script<?= Nonce() ?>>
var currentTable = <?= json_encode($Page->getClientVars()) ?>;
ew.deepAssign(ew.vars, { tables: { view_appointment_report: currentTable } });
var currentPageID = ew.PAGE_ID = "search";
var currentForm;
var fview_appointment_reportsearch, currentSearchForm, currentAdvancedSearchForm;
ew.on("wrapper", () => {
    let $ = jQuery,
        fields = currentTable.fields;

    // Form object for search
    let form = new ew.FormBuilder()
        .setId("fview_appointment_reportsearch")
        .setPageId("search")
<?php if ($Page->IsModal && $Page->UseAjaxActions) { ?>
        .setSubmitWithFetch(true)
<?php } ?>

        // Add fields
        .addFields([
            ["Patient_Name", [], fields.Patient_Name.isInvalid],
            ["Doctor_Name", [], fields.Doctor_Name.isInvalid],
            ["Specialisation", [], fields.Specialisation.isInvalid],
            ["APPOINTMENT_DATE", [ew.Validators.datetime(fields.APPOINTMENT_DATE.clientFormatPattern)], fields.APPOINTMENT_DATE.isInvalid],
            ["Day_Name", [], fields.Day_Name.isInvalid],
            ["Month_Name", [], fields.Month_Name.isInvalid],
            ["Year", [ew.Validators.integer], fields.Year.isInvalid],
            ["APPOINTMENT_TIME", [ew.Validators.time(fields.APPOINTMENT_TIME.clientFormatPattern)], fields.APPOINTMENT_TIME.isInvalid],
            ["STATUS", [], fields.STATUS.isInvalid]
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
            "STATUS": <?= $Page->STATUS->toClientList($Page) ?>,
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
<form name="fview_appointment_reportsearch" id="fview_appointment_reportsearch" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post" novalidate autocomplete="off">
<?php if (Config("CSRF_PROTECTION")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token ID -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="view_appointment_report">
<input type="hidden" name="action" id="action" value="search">
<?php if ($Page->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div class="ew-search-div"><!-- page* -->
<?php if ($Page->Patient_Name->Visible) { // Patient_Name ?>
    <div id="r_Patient_Name" class="row"<?= $Page->Patient_Name->rowAttributes() ?>>
        <label for="x_Patient_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Patient_Name"><?= $Page->Patient_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Patient_Name" id="z_Patient_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Patient_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Patient_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Patient_Name->getInputTextType() ?>" name="x_Patient_Name" id="x_Patient_Name" data-table="view_appointment_report" data-field="x_Patient_Name" value="<?= $Page->Patient_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Patient_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Patient_Name->formatPattern()) ?>"<?= $Page->Patient_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Patient_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Doctor_Name->Visible) { // Doctor_Name ?>
    <div id="r_Doctor_Name" class="row"<?= $Page->Doctor_Name->rowAttributes() ?>>
        <label for="x_Doctor_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Doctor_Name"><?= $Page->Doctor_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Doctor_Name" id="z_Doctor_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Doctor_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Doctor_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Doctor_Name->getInputTextType() ?>" name="x_Doctor_Name" id="x_Doctor_Name" data-table="view_appointment_report" data-field="x_Doctor_Name" value="<?= $Page->Doctor_Name->getEditValue() ?>" size="30" maxlength="41" placeholder="<?= HtmlEncode($Page->Doctor_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Doctor_Name->formatPattern()) ?>"<?= $Page->Doctor_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Doctor_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Specialisation->Visible) { // Specialisation ?>
    <div id="r_Specialisation" class="row"<?= $Page->Specialisation->rowAttributes() ?>>
        <label for="x_Specialisation" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Specialisation"><?= $Page->Specialisation->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Specialisation" id="z_Specialisation" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Specialisation->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Specialisation" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Specialisation->getInputTextType() ?>" name="x_Specialisation" id="x_Specialisation" data-table="view_appointment_report" data-field="x_Specialisation" value="<?= $Page->Specialisation->getEditValue() ?>" size="30" maxlength="50" placeholder="<?= HtmlEncode($Page->Specialisation->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Specialisation->formatPattern()) ?>"<?= $Page->Specialisation->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Specialisation->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->APPOINTMENT_DATE->Visible) { // APPOINTMENT_DATE ?>
    <div id="r_APPOINTMENT_DATE" class="row"<?= $Page->APPOINTMENT_DATE->rowAttributes() ?>>
        <label for="x_APPOINTMENT_DATE" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_APPOINTMENT_DATE"><?= $Page->APPOINTMENT_DATE->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_APPOINTMENT_DATE" id="z_APPOINTMENT_DATE" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->APPOINTMENT_DATE->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_APPOINTMENT_DATE" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->APPOINTMENT_DATE->getInputTextType() ?>" name="x_APPOINTMENT_DATE" id="x_APPOINTMENT_DATE" data-table="view_appointment_report" data-field="x_APPOINTMENT_DATE" value="<?= $Page->APPOINTMENT_DATE->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->APPOINTMENT_DATE->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->APPOINTMENT_DATE->formatPattern()) ?>"<?= $Page->APPOINTMENT_DATE->editAttributes() ?>>
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
                        "fview_appointment_reportsearch",
                        "x_APPOINTMENT_DATE",
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
        <label for="x_Day_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Day_Name"><?= $Page->Day_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Day_Name" id="z_Day_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Day_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Day_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Day_Name->getInputTextType() ?>" name="x_Day_Name" id="x_Day_Name" data-table="view_appointment_report" data-field="x_Day_Name" value="<?= $Page->Day_Name->getEditValue() ?>" size="30" maxlength="9" placeholder="<?= HtmlEncode($Page->Day_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Day_Name->formatPattern()) ?>"<?= $Page->Day_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Day_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Month_Name->Visible) { // Month_Name ?>
    <div id="r_Month_Name" class="row"<?= $Page->Month_Name->rowAttributes() ?>>
        <label for="x_Month_Name" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Month_Name"><?= $Page->Month_Name->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("LIKE") ?>
        <input type="hidden" name="z_Month_Name" id="z_Month_Name" value="LIKE">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Month_Name->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Month_Name" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Month_Name->getInputTextType() ?>" name="x_Month_Name" id="x_Month_Name" data-table="view_appointment_report" data-field="x_Month_Name" value="<?= $Page->Month_Name->getEditValue() ?>" size="30" maxlength="9" placeholder="<?= HtmlEncode($Page->Month_Name->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Month_Name->formatPattern()) ?>"<?= $Page->Month_Name->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Month_Name->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->Year->Visible) { // Year ?>
    <div id="r_Year" class="row"<?= $Page->Year->rowAttributes() ?>>
        <label for="x_Year" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_Year"><?= $Page->Year->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_Year" id="z_Year" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->Year->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_Year" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->Year->getInputTextType() ?>" name="x_Year" id="x_Year" data-table="view_appointment_report" data-field="x_Year" value="<?= $Page->Year->getEditValue() ?>" size="30" placeholder="<?= HtmlEncode($Page->Year->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->Year->formatPattern()) ?>"<?= $Page->Year->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->Year->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->APPOINTMENT_TIME->Visible) { // APPOINTMENT_TIME ?>
    <div id="r_APPOINTMENT_TIME" class="row"<?= $Page->APPOINTMENT_TIME->rowAttributes() ?>>
        <label for="x_APPOINTMENT_TIME" class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_APPOINTMENT_TIME"><?= $Page->APPOINTMENT_TIME->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_APPOINTMENT_TIME" id="z_APPOINTMENT_TIME" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->APPOINTMENT_TIME->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_APPOINTMENT_TIME" class="ew-search-field ew-search-field-single">
                <input type="<?= $Page->APPOINTMENT_TIME->getInputTextType() ?>" name="x_APPOINTMENT_TIME" id="x_APPOINTMENT_TIME" data-table="view_appointment_report" data-field="x_APPOINTMENT_TIME" value="<?= $Page->APPOINTMENT_TIME->getEditValue() ?>" placeholder="<?= HtmlEncode($Page->APPOINTMENT_TIME->getPlaceHolder()) ?>" data-format-pattern="<?= HtmlEncode($Page->APPOINTMENT_TIME->formatPattern()) ?>"<?= $Page->APPOINTMENT_TIME->editAttributes() ?>>
                <div class="invalid-feedback"><?= $Page->APPOINTMENT_TIME->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Page->STATUS->Visible) { // STATUS ?>
    <div id="r_STATUS" class="row"<?= $Page->STATUS->rowAttributes() ?>>
        <label class="<?= $Page->LeftColumnClass ?>"><span id="elh_view_appointment_report_STATUS"><?= $Page->STATUS->caption() ?></span>
        <span class="ew-search-operator">
        <?= Language()->phrase("=") ?>
        <input type="hidden" name="z_STATUS" id="z_STATUS" value="=">
        </span>
        </label>
        <div class="<?= $Page->RightColumnClass ?>">
            <div<?= $Page->STATUS->cellAttributes() ?>>
                <div class="d-flex align-items-start">
                <span id="el_view_appointment_report_STATUS" class="ew-search-field ew-search-field-single">
                <template id="tp_x_STATUS">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" data-table="view_appointment_report" data-field="x_STATUS" name="x_STATUS" id="x_STATUS"<?= $Page->STATUS->editAttributes() ?>>
                        <label class="form-check-label"></label>
                    </div>
                </template>
                <div id="dsl_x_STATUS" class="ew-item-list"></div>
                <selection-list hidden
                    id="x_STATUS"
                    name="x_STATUS"
                    value="<?= HtmlEncode($Page->STATUS->AdvancedSearch->SearchValue) ?>"
                    data-type="select-one"
                    data-template="tp_x_STATUS"
                    data-target="dsl_x_STATUS"
                    data-repeatcolumn="5"
                    class="form-control<?= $Page->STATUS->isInvalidClass() ?>"
                    data-table="view_appointment_report"
                    data-field="x_STATUS"
                    data-value-separator="<?= $Page->STATUS->displayValueSeparatorAttribute() ?>"
                    <?= $Page->STATUS->editAttributes() ?>></selection-list>
                <div class="invalid-feedback"><?= $Page->STATUS->getErrorMessage(false) ?></div>
                </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?= $Page->IsModal ? '<template class="ew-modal-buttons">' : '<div class="row ew-buttons">' ?><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
        <button class="btn btn-primary ew-btn ew-submit" name="btn-action" id="btn-action" type="submit" form="fview_appointment_reportsearch"><?= Language()->phrase("Search") ?></button>
        <?php if ($Page->IsModal) { ?>
        <button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" form="fview_appointment_reportsearch"><?= Language()->phrase("CancelBtn") ?></button>
        <?php } else { ?>
        <button class="btn btn-secondary ew-btn" name="btn-reset" id="btn-reset" type="button" form="fview_appointment_reportsearch" data-ew-action="reload"><?= Language()->phrase("Reset") ?></button>
        <?php } ?>
    </div><!-- /buttons offset -->
<?= $Page->IsModal ? "</template>" : "</div>" ?><!-- /buttons .row -->
</form>
<?= $Page->getPageFooter() ?>
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

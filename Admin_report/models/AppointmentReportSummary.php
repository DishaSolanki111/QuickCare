<?php

namespace PHPMaker2026\Project2;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\EventStreamResponse;
use Symfony\Component\HttpFoundation\ServerEvent;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use ParagonIE\CSPBuilder\CSPBuilder;
use InvalidArgumentException;
use Exception;
use Throwable;
use DateTimeInterface;
use DateTimeImmutable;
use DateInterval;
use DateTime;
use Closure;
use Traversable;
use PHPMaker2026\Project2\Entity as BaseEntity;
use PHPMaker2026\Project2\Db;
use PHPMaker2026\Project2\Db\Entity;
use PHPMaker2026\Project2\ReportHelper;

/**
 * Page class
 */
#[AsAlias("AppointmentReportSummary", true)]
class AppointmentReportSummary extends AppointmentReport implements PageInterface
{
    use MessagesTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "summary";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // CSS class/style
    public string $ReportContainerClass = "ew-grid";
    public string $CurrentPageName = "AppointmentReport"; // Route action

    // Page headings
    public string $Heading = "";
    public string $Subheading = "";
    public string $PageHeader = "";
    public string $PageFooter = "";

    // Page layout
    public bool $UseLayout = true;

    // Page terminated
    private bool $terminated = false;
    public AppointmentReportReportView $ReportData;
    public array $ChartData = [];
    protected array $groupCounts = [];

    // Options
    public bool $HideOptions = false;
    public ?ListOptions $ExportOptions = null; // Export options
    public ?ListOptions $SearchOptions = null; // Search options
    public ?ListOptions $FilterOptions = null; // Filter options

    // Paging variables
    public int $RecordIndex = 0; // Record index
    public int $RecordCount = 0; // Record count (start from 1 for each group)
    public int $StartGroup = 0; // Start group
    public int $StopGroup = 0; // Stop group
    public int $TotalGroups = 0; // Total groups
    public int $GroupCount = 0; // Group count
    public array $GroupCounter = []; // Group counter
    public int $DisplayGroups = 3; // Groups per page
    public int $PageNumber = 1;
    public array $PagerOptions = ["proximity" => 2, "show_dots" => true];
    public string $PageSizes = "1,2,3,5,-1"; // Page sizes (comma separated)
    public string $PageFirstGroupFilter = "";
    public string $UserIDFilter = "";
    public string $DefaultSearchWhere = ""; // Default search WHERE clause
    public string $SearchWhere = "";
    public string $SearchPanelClass = "ew-search-panel collapse show"; // Search Panel class
    public int $SearchColumnCount = 0; // For extended search
    public int $SearchFieldsPerRow = 1; // For extended search
    public string $DrillDownList = "";
    public bool $SearchCommand = false;
    public bool $ShowHeader = true;
    public int $GroupColumnCount = 0;
    public int $SubGroupColumnCount = 0;
    public int $DetailColumnCount = 0;
    public int $TotalCount = 0;
    public int $PageTotalCount = 0;
    public string $TopContentClass = "ew-top";
    public string $MiddleContentClass = "ew-middle";
    public string $BottomContentClass = "ew-bottom";

    // Constructor
    public function __construct(
        Language $language,
        AdvancedSecurity $security,
        CSPBuilder $cspBuilder,
        CacheInterface $cache,
        FieldFactory $fieldFactory,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($language, $security, $cspBuilder, $cache, $fieldFactory, $dispatcher);
        global $httpContext;
        $this->Headers = new HeaderBag();
        $this->TableVar = 'Appointment_report';
        $this->TableName = 'Appointment_report';

        // CSS class name as context
        $this->ContextClass = ConvertToCssClass($this->TableVar);
        AppendClass($this->ReportContainerClass, $this->ContextClass);

        // Fixed header table
        if (!$this->UseCustomTemplate) {
            $this->setFixedHeaderTable(Config("USE_FIXED_HEADER_TABLE"), Config("FIXED_HEADER_TABLE_HEIGHT"));
        }

        // Initialize
        $httpContext["Page"] = $this;

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs

        // Open connection
        $httpContext["Conn"] ??= $this->getConnection();

        // Export options
        $this->ExportOptions = new ListOptions(TagClassName: "ew-export-option");

        // Filter options
        $this->FilterOptions = new ListOptions(TagClassName: "ew-filter-option");

        // Pager options
        if (IsEmpty($this->PagerOptions)) {
            $this->PagerOptions = Config("PAGER_OPTIONS");
        }
    }

    // Page heading
    public function pageHeading(): string
    {
        if ($this->Heading != "") {
            return $this->Heading;
        }
        if (method_exists($this, "tableCaption")) {
            return $this->tableCaption();
        }
        return "";
    }

    // Page subheading
    public function pageSubheading(): string
    {
        if ($this->Subheading != "") {
            return $this->Subheading;
        }
        return "";
    }

    // Page name
    public function pageName(): string
    {
        return CurrentPageName();
    }

    // Page URL
    public function pageUrl(bool $withArgs = true): string
    {
        if ($withArgs) {
            return CurrentPageUrl();
        } else {
            $route = GetRoute();
            $path = $route?->getPath() ?? "";
            // Remove all placeholders like `{id}`
            $stripped = preg_replace('/\{[^}]+\}/', '', $path);
            // Remove trailing slash unless it's root '/', then replace leading slash with BasePath(true)
            return preg_replace('/^\//', BasePath(true), $stripped !== '/' ? rtrim($stripped, '/') : '/');
        }
    }

    // Get Page Header
    public function getPageHeader(): string
    {
        $header = $this->PageHeader;
        $this->pageDataRendering($header);
        if ($header != "") { // Header exists, display
            $header = '<div id="ew-page-header">' . $header . '</div>';
        }
        return $header;
    }

    // Get Page Footer
    public function getPageFooter(): string
    {
        $footer = $this->PageFooter;
        $this->pageDataRendered($footer);
        if ($footer != "") { // Footer exists, display
            $footer = '<div id="ew-page-footer">' . $footer . '</div>';
        }
        return $footer;
    }

    // Is lookup
    public function isLookup(): bool
    {
        return SameText(RouteAction(), Config("API_LOOKUP_ACTION"));
    }

    // Is AutoFill
    public function isAutoFill(): bool
    {
        return $this->isLookup() && SameText(Post("ajax"), "autofill");
    }

    // Is AutoSuggest
    public function isAutoSuggest(): bool
    {
        return $this->isLookup() && SameText(Post("ajax"), "autosuggest");
    }

    // Is modal lookup
    public function isModalLookup(): bool
    {
        return $this->isLookup() && SameText(Post("ajax"), "modal");
    }

    // Is terminated
    public function isTerminated(): bool
    {
        return $this->terminated;
    }

    /**
     * Terminate page
     *
     * @param ?string $url URL for redirection
     * @return void
     */
    public function terminate(?string $url = null): void
    {
        if ($this->terminated) {
            return;
        }
        global $httpContext;

        // Page is terminated
        $this->terminated = true;

        // Page Unload event
        if (method_exists($this, "pageUnload")) {
            $this->pageUnload();
        }
        DispatchEvent(new PageUnloadedEvent($this), PageUnloadedEvent::class);
        if (!IsApi() && method_exists($this, "pageRedirecting")) {
            $this->pageRedirecting($url);
        }

        // Return for API
        if (IsApi()) {
            if (!$this->Response) { // Show response for API
                $ar = array_merge($this->getMessages(), $url ? ["url" => GetUrl($url)] : []);
                $this->Response = new JsonResponse($ar);
            }
            $this->clearMessages(); // Clear messages for API request
            return;
        } else { // Check if response is JSON
            if (IsJsonResponse($this->Response)) { // Has JSON response
                $this->clearMessages();
                return;
            }
        }

        // Go to URL if specified
        if ($url !== null) {
            $this->Response = new RedirectResponse(GetUrl($url), Config("REDIRECT_STATUS_CODE"));
        }
        return; // Return to controller
    }

    // Lookup data
    public function lookup(array $req = []): array
    {
        // Get lookup object
        $fieldName = $req["field"] ?? null;
        if (!$fieldName) {
            return [];
        }
        $fld = $this->Fields[$fieldName];
        $lookup = $fld->Lookup;
        $name = $req["name"] ?? "";
        if (ContainsString($name, "query_builder_rule")) {
            $lookup->FilterFields = []; // Skip parent fields if any
        }

        // Get lookup parameters
        $lookupType = $req["ajax"] ?? "unknown";
        $pageSize = -1;
        $offset = -1;
        $searchValue = "";
        if (SameText($lookupType, "modal") || SameText($lookupType, "filter")) {
            $searchValue = $req["q"] ?? $req["sv"] ?? "";
            $pageSize = $req["n"] ?? $req["recperpage"] ?? 10;
        } elseif (SameText($lookupType, "autosuggest")) {
            $searchValue = $req["q"] ?? "";
            $pageSize = $req["n"] ?? -1;
            $pageSize = is_numeric($pageSize) ? (int)$pageSize : -1;
            if ($pageSize <= 0) {
                $pageSize = Config("AUTO_SUGGEST_MAX_ENTRIES");
            }
        }
        $start = $req["start"] ?? -1;
        $start = is_numeric($start) ? (int)$start : -1;
        $page = $req["page"] ?? -1;
        $page = is_numeric($page) ? (int)$page : -1;
        $offset = $start >= 0 ? $start : ($page > 0 && $pageSize > 0 ? ($page - 1) * $pageSize : 0);
        $userSelect = Decrypt($req["s"] ?? "");
        $userFilter = Decrypt($req["f"] ?? "");
        $userOrderBy = Decrypt($req["o"] ?? "");
        $keys = $req["keys"] ?? null;
        $lookup->LookupType = $lookupType; // Lookup type
        $lookup->FilterValues = []; // Clear filter values first
        if ($keys !== null) { // Selected records from modal
            if (is_array($keys)) {
                $keys = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $keys);
            }
            $lookup->FilterFields = []; // Skip parent fields if any
            $lookup->FilterValues[] = $keys; // Lookup values
            $pageSize = -1; // Show all records
        } else { // Lookup values
            $lookup->FilterValues[] = $req["v0"] ?? $req["lookupValue"] ?? "";
        }
        $cnt = is_array($lookup->FilterFields) ? count($lookup->FilterFields) : 0;
        for ($i = 1; $i <= $cnt; $i++) {
            $lookup->FilterValues[] = $req["v" . $i] ?? "";
        }
        $lookup->SearchValue = $searchValue;
        $lookup->PageSize = $pageSize;
        $lookup->Offset = $offset;
        if ($userSelect != "") {
            $lookup->UserSelect = $userSelect;
        }
        if ($userFilter != "") {
            $lookup->UserFilter = $userFilter;
        }
        if ($userOrderBy != "") {
            $lookup->UserOrderBy = $userOrderBy;
        }
        return $lookup->toJson($this); // Use settings from current page
    }

    /**
     * Page init
     *
     * @return void
     */
    public function init(): void
    {
        // Set up export
        if (($export = Param("export")) !== null) {
            $this->Export = $export;
        }

        // Set up groups per page dynamically
        $this->setupDisplayGroups();

        // Set up Filter
        $this->Filter = $this->getFilter();

        // Set up Sort
        $this->Sort = $this->getSort();

        // Set up PageNumber / DisplayGroups
        if ($this->DisplayGroups <= 0 || $this->DrillDown) { // Display all groups
            $this->DisplayGroups = -1; // Display all
        }
        if ($this->ExportAll && $this->isExport()) {
            $this->DisplayGroups = -1; // Display all
            $this->PageNumber = 1;
        } else {
            $this->PageNumber = $this->getPageNumber();
        }
    }

    /**
     * Page run
     *
     * @return void
     */
    public function run(): void
    {
        global $httpContext;

        // Set up dashboard report
        $httpContext["DashboardReport"] ??= Param(Config("PAGE_DASHBOARD"));
        if ($httpContext["DashboardReport"]) {
            $this->UseAjaxActions = true;
        }

        // Use layout
        $this->UseLayout = $this->UseLayout && ParamBool(Config("PAGE_LAYOUT"), true);

        // View
        $this->View = Get(Config("VIEW"));
        $httpContext["ExportType"] = $this->Export; // Get export parameter, used in header
        if ($httpContext["ExportType"] != "") {
            global $httpContext;
            $httpContext["SkipHeaderFooter"] = true;
        }
        $this->CurrentAction = Param("action"); // Set up current action

        // Setup export options
        $this->setupExportOptions();

        // Global Page Loading event (in userfn*.php)
        DispatchEvent(new PageLoadingEvent($this), PageLoadingEvent::class);

        // Page Load event
        if (method_exists($this, "pageLoad")) {
            $this->pageLoad();
        }

        // Setup other options
        $this->setupOtherOptions();

        // Set up table class
        if ($this->isExport("word") || $this->isExport("excel") || $this->isExport("pdf")) {
            $this->TableClass = "ew-table table-bordered table-sm";
        } else {
            PrependClass($this->TableClass, "table ew-table table-bordered table-sm");
        }

        // Set up report container class
        if (!$this->isExport("word") && !$this->isExport("excel")) {
            $this->ReportContainerClass .= " card ew-card";
        }

        // Set field visibility for detail fields
        $this->APPOINTMENT_ID->setVisibility();
        $this->PATIENT_ID->setVisibility();
        $this->DOCTOR_ID->setVisibility();
        $this->APPOINTMENT_TIME->setVisibility();

        // Set up Breadcrumb
        if (!$this->isExport() && !$httpContext["DashboardReport"]) {
            $this->setupBreadcrumb();
        }

        // No filter
        $this->FilterOptions["savecurrentfilter"]->Visible = false;
        $this->FilterOptions["deletefilter"]->Visible = false;

        // Set up search panel class
        if ($this->SearchWhere != "") {
            AppendClass($this->SearchPanelClass, "show");
        }

        // Search options
        $this->setupSearchOptions();
        foreach ($this->ReportData->appointmentDateGroups as $firstGroup) {
            foreach ($firstGroup->rows as $row) {
                $this->Rows[] = $this->ConvertRowForCustomTemplate($row); // For Custom Template
            }
        }

        // Set up TotalGroups / StartGroup
        $this->TotalGroups = $this->ReportData->groupCount;
        if ($this->DisplayGroups == -1) { // Display all
            $this->DisplayGroups = $this->TotalGroups;
        }
        $this->setupStartGroup($this->PageNumber);

        // Set no record found message
        if ($this->TotalGroups == 0) {
            $this->ShowHeader = false;
                if ($this->SearchWhere == "0=101") {
                    $this->setWarningMessage($this->language->phrase("EnterSearchCriteria"));
                } else {
                    $this->setWarningMessage($this->language->phrase("NoRecord"));
                }
        } else {
            $this->GroupCount = 1;
        }

        // Hide export options if export/dashboard report/hide options
        if ($this->isExport() || $httpContext["DashboardReport"] || $this->HideOptions) {
            $this->ExportOptions->hideAllOptions();
        }

        // Hide search/filter options if export/drilldown/dashboard report/hide options
        if ($this->isExport() || $this->DrillDown || $httpContext["DashboardReport"] || $this->HideOptions) {
            $this->SearchOptions->hideAllOptions();
            $this->FilterOptions->hideAllOptions();
        }
        $this->setupFieldCount();

        // Set the last group to display if not export all
        if ($this->ExportAll && $this->isExport()) {
            $this->StopGroup = $this->TotalGroups;
        } else {
            $this->StopGroup = $this->StartGroup + $this->DisplayGroups - 1;
        }

        // Stop group <= total number of groups
        if (intval($this->StopGroup) > intval($this->TotalGroups)) {
            $this->StopGroup = $this->TotalGroups;
        }
        $this->RecordCount = 0;
        $this->RecordIndex = 0;

        // Set up pager
        $this->Pager = new Pager(
            $httpContext["DashboardReport"] ? BuildUrl($this->CurrentPageName, "dashboard=" . $httpContext["DashboardReport"]) : $this->CurrentPageName,
            $this->StartGroup,
            $this->PageNumber,
            $this->DisplayGroups,
            $this->TotalGroups,
            $this->PageSizes,
            $this->ContextClass,
            $this->UseAjaxActions,
            $this->PagerOptions,
            $this->AutoHidePager,
            $this->AutoHidePageSizeSelector
        );

        // Check if no records
        if ($this->TotalGroups == 0) {
            $this->ReportContainerClass .= " ew-no-record";
        }

        // Set LoginStatus / Page_Rendering / Page_Render
        if (!IsApi() && !$this->isTerminated()) {
            // Pass login status to client side
            SetClientVar("login", LoginStatus());

            // Global Page Rendering event (in userfn*.php)
            DispatchEvent(new PageRenderingEvent($this), PageRenderingEvent::class);

            // Page Render event
            if (method_exists($this, "pageRender")) {
                $this->pageRender();
            }

            // Render search option
            if (method_exists($this, "renderSearchOptions")) {
                $this->renderSearchOptions();
            }
        }
    }

    /**
     * Render group header summary
     *
     * @param int $index Group index
     * @param object $group Group
     */
    public function renderGroupHeaderSummary(int $index, object $group): void
    {
        $groupField = $this->Fields[$this->GroupingFields[$index]];
        $groupField->setGroupValue($group->groupValue());
        foreach ($group->summary as $fieldName => $smry) {
            $detailField = $this->Fields[$fieldName];
            $detailField->SumValue = $smry->sum;
            $detailField->AverageValue = $smry->average;
            $detailField->MinimumValue = $smry->minimum;
            $detailField->MaximumValue = $smry->maximum;
            $detailField->CountValue = $smry->count;
        }
        $groupField->LevelBreak = true;
        $groupField->Count = $group->recordCount;

        // Set up group counters
        if (IsEmpty($this->GroupCounter)) { // Init GroupCounter
            for ($i = 0, $count = count($this->GroupingFields); $i <= $count; $i++) {
                $this->GroupCounter[$i] = 0;
            }
        }
        $this->GroupCounter[$index + 1] += 1;
        if ($index == 0) {
            if (count($this->GroupingFields) == 1) { // Only one group
                $this->setGroupCount($group->recordCount, $this->GroupCount);
            } else {
                $this->setGroupCount(count($group->groups()), $this->GroupCount);
            }
        } else {
            $keys = [];
            for ($i = 0; $i <= $index; $i++) {
                $keys[] = strval($this->GroupCounter[$i + 1]);
            }
            $this->setGroupCount($group->recordCount, implode('_', $keys));
        }
        $this->RecordCount = 0; // Reset record count
        for ($i = $index + 2, $count = count($this->GroupingFields); $i <= $count; $i++) { // Reset lower groups
            $this->GroupCounter[$i] = 0;
        }

        // Reset Level break for previous levels
        if ($index > 0 && $this->GroupCounter[$index + 1] > 1) {
            for ($i = 0; $i < $index; $i++) {
                $this->Fields[$this->GroupingFields[$i]]->LevelBreak = false;
            }
        }

        // Render row
        $this->renderRow(RowType::TOTAL, RowSummary::GROUP, RowTotal::HEADER, $index + 1);
    }

    /**
     * Render group footer summary
     *
     * @param int $index Group index
     * @param object $group Group
     */
    public function renderGroupFooterSummary(int $index, object $group): void
    {
        $groupField = $this->Fields[$this->GroupingFields[$index]];
        $groupField->setGroupValue($group->groupValue());
        foreach ($group->summary as $fieldName => $smry) {
            $detailField = $this->Fields[$fieldName];
            $detailField->SumValue = $smry->sum;
            $detailField->AverageValue = $smry->average;
            $detailField->MinimumValue = $smry->minimum;
            $detailField->MaximumValue = $smry->maximum;
            $detailField->CountValue = $smry->count;
        }
        $groupField->Count = $group->recordCount;

        // Render row
        $this->renderRow(RowType::TOTAL, RowSummary::GROUP, RowTotal::FOOTER, $index + 1);
    }

    /**
     * Render page summary
     *
     */
    public function renderPageSummary(): void
    {
        foreach ($this->ReportData->pageSummary as $fieldName => $smry) {
            $detailField = $this->Fields[$fieldName];
            $detailField->SumValue = $smry->sum;
            $detailField->AverageValue = $smry->average;
            $detailField->MinimumValue = $smry->minimum;
            $detailField->MaximumValue = $smry->maximum;
            $detailField->CountValue = $smry->count;
        }
        $this->PageTotalCount = $this->ReportData->pageCount;

        // Render row
        $this->renderRow(RowType::TOTAL, RowSummary::PAGE, RowTotal::FOOTER, rowClass: "ew-rpt-page-summary");
    }

    /**
     * Render grand summary
     *
     */
    public function renderGrandSummary(): void
    {
        foreach ($this->ReportData->grandSummary as $fieldName => $smry) {
            $detailField = $this->Fields[$fieldName];
            $detailField->SumValue = $smry->sum;
            $detailField->AverageValue = $smry->average;
            $detailField->MinimumValue = $smry->minimum;
            $detailField->MaximumValue = $smry->maximum;
            $detailField->CountValue = $smry->count;
        }
        $this->TotalCount = $this->ReportData->grandCount;

        // Render row
        $this->renderRow(RowType::TOTAL, RowSummary::GRAND, RowTotal::FOOTER, rowClass: "ew-rpt-grand-summary");
    }

    /**
     * Render detail
     *
     * @param AppointmentReportDetailView $detail Detail object
     */
    // Render Group
    public function renderDetail(AppointmentReportDetailView $detail): void
    {
        $this->RecordCount++;
        if ($this->RecordCount > 1) { // Reset LevelBreak for all groups
            foreach ($this->GroupingFields as $groupingField) {
                $this->Fields[$groupingField]->LevelBreak = false;
            }
        }
        $this->APPOINTMENT_ID->setCurrentValue($detail->appointmentId); // APPOINTMENT_ID
        $this->PATIENT_ID->setCurrentValue($detail->patientId); // PATIENT_ID
        $this->DOCTOR_ID->setCurrentValue($detail->doctorId); // DOCTOR_ID
        $this->APPOINTMENT_TIME->setCurrentValue($detail->appointmentTime); // APPOINTMENT_TIME

        // Render row
        $this->renderRow(RowType::DETAIL);
    }

    /**
     * Render row
     *
     * @param RowType $rowType Row type
     * @param ?RowSummary $rowSummary Row summary type
     * @param ?RowTotal $rowTotal Row total type
     * @param int $groupLevel Group level
     * @param string $rowClass Row class
     * @param bool $resetAttributes Reset attributes
     * @return void
     */
    public function renderRow(RowType $rowType = RowType::DETAIL, ?RowSummary $rowSummary = null, ?RowTotal $rowTotal = null, int $groupLevel = 0, string $rowClass = "", bool $resetAttributes = true): void
    {
        // Set up row type
        $this->RowType = $rowType;

        // Set up summary type
        if ($rowSummary !== null) {
            $this->RowTotalType = $rowSummary;
        }

        // Set up total type
        if ($rowTotal !== null) {
            $this->RowTotalSubType = $rowTotal;
        }

        // Set up group level
        if ($groupLevel > 0) {
            $this->RowGroupLevel = $groupLevel;
        }

        // Reset attributes
        if ($resetAttributes) {
            $this->resetAttributes();
        }

        // Set up row class
        if ($rowClass) {
            $this->RowAttrs["class"] = $rowClass;
        }
        $conn = $this->getConnection();

        // Call Row_Rendering event
        $this->rowRendering();

        // APPOINTMENT_DATE

        // STATUS

        // APPOINTMENT_ID

        // PATIENT_ID

        // DOCTOR_ID

        // APPOINTMENT_TIME
        if ($this->RowType == RowType::SEARCH) { // Search row
        } elseif ($this->RowType == RowType::TOTAL && !($this->RowTotalType == RowSummary::GROUP && $this->RowTotalSubType == RowTotal::HEADER)) { // Summary row
            $this->RowAttrs->prependClass(($this->RowTotalType == RowSummary::PAGE || $this->RowTotalType == RowSummary::GRAND) ? "ew-rpt-grp-aggregate" : ""); // Set up row class
            if ($this->RowTotalType == RowSummary::GROUP) {
                $this->RowAttrs["data-group"] = $this->APPOINTMENT_DATE->groupValue(); // Set up group attribute
            }
            if ($this->RowTotalType == RowSummary::GROUP && $this->RowGroupLevel >= 2) {
                $this->RowAttrs["data-group-2"] = $this->STATUS->groupValue(); // Set up group attribute 2
            }

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->GroupViewValue = $this->APPOINTMENT_DATE->groupValue();
            $this->APPOINTMENT_DATE->GroupViewValue = FormatDateTime($this->APPOINTMENT_DATE->GroupViewValue, $this->APPOINTMENT_DATE->formatPattern());
            $this->APPOINTMENT_DATE->CellCssClass = ($this->RowGroupLevel == 1 ? "ew-rpt-grp-summary-1" : "ew-rpt-grp-field-1");
            $this->APPOINTMENT_DATE->GroupViewValue = DisplayGroupValue($this->APPOINTMENT_DATE, $this->APPOINTMENT_DATE->GroupViewValue);

            // STATUS
            if (strval($this->STATUS->groupValue()) != "") {
                $this->STATUS->GroupViewValue = $this->STATUS->optionCaption($this->STATUS->groupValue());
            } else {
                $this->STATUS->GroupViewValue = null;
            }
            $this->STATUS->CellCssClass = ($this->RowGroupLevel == 2 ? "ew-rpt-grp-summary-2" : "ew-rpt-grp-field-2");
            $this->STATUS->GroupViewValue = DisplayGroupValue($this->STATUS, $this->STATUS->GroupViewValue);

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->CountViewValue = $this->APPOINTMENT_ID->CountValue;
            $this->APPOINTMENT_ID->CountViewValue = FormatNumber($this->APPOINTMENT_ID->CountViewValue, $this->APPOINTMENT_ID->formatPattern());
            $this->APPOINTMENT_ID->CellAttrs["class"] = ($this->RowTotalType == RowSummary::PAGE || $this->RowTotalType == RowSummary::GRAND) ? "ew-rpt-grp-aggregate" : "ew-rpt-grp-summary-" . $this->RowGroupLevel;

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->HrefValue = "";

            // STATUS
            $this->STATUS->HrefValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";

            // PATIENT_ID
            $this->PATIENT_ID->HrefValue = "";

            // DOCTOR_ID
            $this->DOCTOR_ID->HrefValue = "";

            // APPOINTMENT_TIME
            $this->APPOINTMENT_TIME->HrefValue = "";
        } else {
            if ($this->RowTotalType == RowSummary::GROUP && $this->RowTotalSubType == RowTotal::HEADER) {
                $this->RowAttrs["data-group"] = $this->APPOINTMENT_DATE->groupValue(); // Set up group attribute
                if ($this->RowGroupLevel >= 2) {
                    $this->RowAttrs["data-group-2"] = $this->STATUS->groupValue(); // Set up group attribute 2
                }
            } else {
                $this->RowAttrs["data-group"] = $this->APPOINTMENT_DATE->groupValue(); // Set up group attribute
                $this->RowAttrs["data-group-2"] = $this->STATUS->groupValue(); // Set up group attribute 2
            }

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->GroupViewValue = $this->APPOINTMENT_DATE->groupValue();
            $this->APPOINTMENT_DATE->GroupViewValue = FormatDateTime($this->APPOINTMENT_DATE->GroupViewValue, $this->APPOINTMENT_DATE->formatPattern());
            $this->APPOINTMENT_DATE->CellCssClass = "ew-rpt-grp-field-1";
            $this->APPOINTMENT_DATE->GroupViewValue = DisplayGroupValue($this->APPOINTMENT_DATE, $this->APPOINTMENT_DATE->GroupViewValue);
            if (!$this->APPOINTMENT_DATE->LevelBreak) {
                $this->APPOINTMENT_DATE->GroupViewValue = "";
            }

            // STATUS
            if (strval($this->STATUS->groupValue()) != "") {
                $this->STATUS->GroupViewValue = $this->STATUS->optionCaption($this->STATUS->groupValue());
            } else {
                $this->STATUS->GroupViewValue = null;
            }
            $this->STATUS->CellCssClass = "ew-rpt-grp-field-2";
            $this->STATUS->GroupViewValue = DisplayGroupValue($this->STATUS, $this->STATUS->GroupViewValue);
            if (!$this->STATUS->LevelBreak) {
                $this->STATUS->GroupViewValue = "";
            }

            // Increment RowCount
            if ($this->RowType == RowType::DETAIL) {
                $this->RowCount++;
            }

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->ViewValue = $this->APPOINTMENT_ID->CurrentValue;
            $this->APPOINTMENT_ID->CellCssClass = ($this->RecordCount % 2 != 1 ? "ew-table-alt-row" : "");

            // PATIENT_ID
            $this->PATIENT_ID->ViewValue = $this->PATIENT_ID->CurrentValue;
            $this->PATIENT_ID->ViewValue = FormatNumber($this->PATIENT_ID->ViewValue, $this->PATIENT_ID->formatPattern());
            $this->PATIENT_ID->CellCssClass = ($this->RecordCount % 2 != 1 ? "ew-table-alt-row" : "");

            // DOCTOR_ID
            $this->DOCTOR_ID->ViewValue = $this->DOCTOR_ID->CurrentValue;
            $this->DOCTOR_ID->ViewValue = FormatNumber($this->DOCTOR_ID->ViewValue, $this->DOCTOR_ID->formatPattern());
            $this->DOCTOR_ID->CellCssClass = ($this->RecordCount % 2 != 1 ? "ew-table-alt-row" : "");

            // APPOINTMENT_TIME
            $this->APPOINTMENT_TIME->ViewValue = $this->APPOINTMENT_TIME->CurrentValue;
            $this->APPOINTMENT_TIME->ViewValue = FormatDateTime($this->APPOINTMENT_TIME->ViewValue, $this->APPOINTMENT_TIME->formatPattern());
            $this->APPOINTMENT_TIME->CellCssClass = ($this->RecordCount % 2 != 1 ? "ew-table-alt-row" : "");

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->HrefValue = "";
            $this->APPOINTMENT_DATE->TooltipValue = "";

            // STATUS
            $this->STATUS->HrefValue = "";
            $this->STATUS->TooltipValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";
            $this->APPOINTMENT_ID->TooltipValue = "";

            // PATIENT_ID
            $this->PATIENT_ID->HrefValue = "";
            $this->PATIENT_ID->TooltipValue = "";

            // DOCTOR_ID
            $this->DOCTOR_ID->HrefValue = "";
            $this->DOCTOR_ID->TooltipValue = "";

            // APPOINTMENT_TIME
            $this->APPOINTMENT_TIME->HrefValue = "";
            $this->APPOINTMENT_TIME->TooltipValue = "";
        }

        // Call Cell_Rendered event
        if ($this->RowType == RowType::TOTAL) {
            // APPOINTMENT_DATE
            $currentValue = $this->APPOINTMENT_DATE->GroupViewValue;
            $viewValue = &$this->APPOINTMENT_DATE->GroupViewValue;
            $viewAttrs = $this->APPOINTMENT_DATE->ViewAttrs;
            $cellAttrs = $this->APPOINTMENT_DATE->CellAttrs;
            $hrefValue = &$this->APPOINTMENT_DATE->HrefValue;
            $linkAttrs = $this->APPOINTMENT_DATE->LinkAttrs;
            $this->cellRendered($this->APPOINTMENT_DATE, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // STATUS
            $currentValue = $this->STATUS->GroupViewValue;
            $viewValue = &$this->STATUS->GroupViewValue;
            $viewAttrs = $this->STATUS->ViewAttrs;
            $cellAttrs = $this->STATUS->CellAttrs;
            $hrefValue = &$this->STATUS->HrefValue;
            $linkAttrs = $this->STATUS->LinkAttrs;
            $this->cellRendered($this->STATUS, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // APPOINTMENT_ID
            $currentValue = $this->APPOINTMENT_ID->CountValue;
            $viewValue = &$this->APPOINTMENT_ID->CountViewValue;
            $viewAttrs = $this->APPOINTMENT_ID->ViewAttrs;
            $cellAttrs = $this->APPOINTMENT_ID->CellAttrs;
            $hrefValue = &$this->APPOINTMENT_ID->HrefValue;
            $linkAttrs = $this->APPOINTMENT_ID->LinkAttrs;
            $this->cellRendered($this->APPOINTMENT_ID, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);
        } else {
            // APPOINTMENT_DATE
            $currentValue = $this->APPOINTMENT_DATE->groupValue();
            $viewValue = &$this->APPOINTMENT_DATE->GroupViewValue;
            $viewAttrs = $this->APPOINTMENT_DATE->ViewAttrs;
            $cellAttrs = $this->APPOINTMENT_DATE->CellAttrs;
            $hrefValue = &$this->APPOINTMENT_DATE->HrefValue;
            $linkAttrs = $this->APPOINTMENT_DATE->LinkAttrs;
            $this->cellRendered($this->APPOINTMENT_DATE, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // STATUS
            $currentValue = $this->STATUS->groupValue();
            $viewValue = &$this->STATUS->GroupViewValue;
            $viewAttrs = $this->STATUS->ViewAttrs;
            $cellAttrs = $this->STATUS->CellAttrs;
            $hrefValue = &$this->STATUS->HrefValue;
            $linkAttrs = $this->STATUS->LinkAttrs;
            $this->cellRendered($this->STATUS, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // APPOINTMENT_ID
            $currentValue = $this->APPOINTMENT_ID->CurrentValue;
            $viewValue = &$this->APPOINTMENT_ID->ViewValue;
            $viewAttrs = $this->APPOINTMENT_ID->ViewAttrs;
            $cellAttrs = $this->APPOINTMENT_ID->CellAttrs;
            $hrefValue = &$this->APPOINTMENT_ID->HrefValue;
            $linkAttrs = $this->APPOINTMENT_ID->LinkAttrs;
            $this->cellRendered($this->APPOINTMENT_ID, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // PATIENT_ID
            $currentValue = $this->PATIENT_ID->CurrentValue;
            $viewValue = &$this->PATIENT_ID->ViewValue;
            $viewAttrs = $this->PATIENT_ID->ViewAttrs;
            $cellAttrs = $this->PATIENT_ID->CellAttrs;
            $hrefValue = &$this->PATIENT_ID->HrefValue;
            $linkAttrs = $this->PATIENT_ID->LinkAttrs;
            $this->cellRendered($this->PATIENT_ID, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // DOCTOR_ID
            $currentValue = $this->DOCTOR_ID->CurrentValue;
            $viewValue = &$this->DOCTOR_ID->ViewValue;
            $viewAttrs = $this->DOCTOR_ID->ViewAttrs;
            $cellAttrs = $this->DOCTOR_ID->CellAttrs;
            $hrefValue = &$this->DOCTOR_ID->HrefValue;
            $linkAttrs = $this->DOCTOR_ID->LinkAttrs;
            $this->cellRendered($this->DOCTOR_ID, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);

            // APPOINTMENT_TIME
            $currentValue = $this->APPOINTMENT_TIME->CurrentValue;
            $viewValue = &$this->APPOINTMENT_TIME->ViewValue;
            $viewAttrs = $this->APPOINTMENT_TIME->ViewAttrs;
            $cellAttrs = $this->APPOINTMENT_TIME->CellAttrs;
            $hrefValue = &$this->APPOINTMENT_TIME->HrefValue;
            $linkAttrs = $this->APPOINTMENT_TIME->LinkAttrs;
            $this->cellRendered($this->APPOINTMENT_TIME, $currentValue, $viewValue, $viewAttrs, $cellAttrs, $hrefValue, $linkAttrs);
        }

        // Call Row_Rendered event
        $this->rowRendered();
        $this->setupFieldCount();
    }

    // Get group count
    public function getGroupCount(mixed ...$args): int
    {
        $key = implode("_", array_map(fn($arg) => strval($arg), $args));
        if ($key == "") {
            return -1;
        } elseif ($key == "0") { // Number of first level groups
            $i = 1;
            while (isset($this->groupCounts[strval($i)])) {
                $i++;
            }
            return $i - 1;
        }
        return isset($this->groupCounts[$key]) ? $this->groupCounts[$key] : -1;
    }

    // Set group count
    public function setGroupCount(int $value, mixed ...$args): void
    {
        $key = implode("_", array_map(fn($arg) => strval($arg), $args));
        if ($key == "") {
            return;
        }
        $this->groupCounts[$key] = $value;
    }

    // Setup field count
    protected function setupFieldCount(): void
    {
        $this->GroupColumnCount = 0;
        $this->SubGroupColumnCount = 0;
        $this->DetailColumnCount = 0;
        if ($this->APPOINTMENT_DATE->Visible) {
            $this->GroupColumnCount += 1;
        }
        if ($this->STATUS->Visible) {
            $this->GroupColumnCount += 1;
            $this->SubGroupColumnCount += 1;
        }
        if ($this->APPOINTMENT_ID->Visible) {
            $this->DetailColumnCount += 1;
        }
        if ($this->PATIENT_ID->Visible) {
            $this->DetailColumnCount += 1;
        }
        if ($this->DOCTOR_ID->Visible) {
            $this->DetailColumnCount += 1;
        }
        if ($this->APPOINTMENT_TIME->Visible) {
            $this->DetailColumnCount += 1;
        }
    }

    // Get export HTML tag
    protected function getExportTag(string $type, bool $custom = false): string
    {
        if ($type == "print" || $custom) { // Printer friendly / custom export
            $pageUrl = $this->pageUrl(false);
            $exportUrl = BuildUrl(GetUrl($pageUrl), "export=" . $type, $custom ? "custom=1" : "");
        } else { // Export API URL
            $exportUrl = GetApiUrl(Config("API_EXPORT_ACTION") . "/" . $type . "/" . $this->TableVar);
        }
        $exportUrl = HtmlEncode($exportUrl);
        if (SameText($type, "excel")) {
            return '<button type="button" class="btn btn-default ew-export-link ew-excel" title="' . HtmlEncode($this->language->phrase("ExportToExcel", true)) . '" data-caption="' . HtmlEncode($this->language->phrase("ExportToExcel", true)) . '" data-ew-action="export" data-export="excel" data-custom="false" data-export-selected="false" data-url="' . $exportUrl . '">' . $this->language->phrase("ExportToExcel") . '</button>';
        } elseif (SameText($type, "word")) {
            return '<button type="button" class="btn btn-default ew-export-link ew-word" title="' . HtmlEncode($this->language->phrase("ExportToWord", true)) . '" data-caption="' . HtmlEncode($this->language->phrase("ExportToWord", true)) . '" data-ew-action="export" data-export="word" data-custom="false" data-export-selected="false" data-url="' . $exportUrl . '">' . $this->language->phrase("ExportToWord") . '</button>';
        } elseif (SameText($type, "pdf")) {
            return '<button type="button" class="btn btn-default ew-export-link ew-pdf" title="' . HtmlEncode($this->language->phrase("ExportToPdf", true)) . '" data-caption="' . HtmlEncode($this->language->phrase("ExportToPdf", true)) . '" data-ew-action="export" data-export="pdf" data-custom="false" data-export-selected="false" data-url="' . $exportUrl . '">' . $this->language->phrase("ExportToPdf") . '</button>';
        } elseif (SameText($type, "html")) {
            return '<button type="button" class="btn btn-default ew-export-link ew-html" title="' . HtmlEncode($this->language->phrase("ExportToHtml", true)) . '" data-caption="' . HtmlEncode($this->language->phrase("ExportToHtml", true)) . '" data-ew-action="export" data-export="html" data-custom="false" data-export-selected="false" data-url="' . $exportUrl . '">' . $this->language->phrase("ExportToHtml") . '</button>';
        } elseif (SameText($type, "email")) {
            return '<button type="button" class="btn btn-default ew-export-link ew-email" title="' . HtmlEncode($this->language->phrase("ExportToEmail", true)) . '" data-caption="' . HtmlEncode($this->language->phrase("ExportToEmail", true)) . '" data-ew-action="email" data-custom="false" data-export-selected="false" data-hdr="' . HtmlEncode($this->language->phrase("ExportToEmail", true)) . '" data-url="' . $exportUrl . '">' . $this->language->phrase("ExportToEmail") . '</button>';
        } elseif (SameText($type, "print")) {
            return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-print\" title=\"" . HtmlEncode($this->language->phrase("PrinterFriendly", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("PrinterFriendly", true)) . "\">" . $this->language->phrase("PrinterFriendly") . "</a>";
        }
    }

    // Set up export options
    protected function setupExportOptions(): void
    {
        // Printer friendly
        $item = $this->ExportOptions->add("print");
        $item->Body = $this->getExportTag("print");
        $item->Visible = false;

        // Export to Excel
        $item = $this->ExportOptions->add("excel");
        $item->Body = $this->getExportTag("excel");
        $item->Visible = true;

        // Export to Word
        $item = $this->ExportOptions->add("word");
        $item->Body = $this->getExportTag("word");
        $item->Visible = false;

        // Export to HTML
        $item = $this->ExportOptions->add("html");
        $item->Body = $this->getExportTag("html");
        $item->Visible = false;

        // Export to PDF
        $item = $this->ExportOptions->add("pdf");
        $item->Body = $this->getExportTag("pdf");
        $item->Visible = false;

        // Export to Email
        $item = $this->ExportOptions->add("email");
        $item->Body = $this->getExportTag("email");
        $item->Visible = false;

        // Drop down button for export
        $this->ExportOptions->UseButtonGroup = true;
        $this->ExportOptions->UseDropDownButton = false;
        if ($this->ExportOptions->UseButtonGroup && IsMobile()) {
            $this->ExportOptions->UseDropDownButton = true;
        }
        $this->ExportOptions->DropDownButtonPhrase = $this->language->phrase("ButtonExport");

        // Add group option item
        $item = $this->ExportOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;

        // Hide options for export
        if ($this->isExport()) {
            $this->ExportOptions->hideAllOptions();
        }
    }

    // Set up search options
    protected function setupSearchOptions(): void
    {
        $pageUrl = $this->pageUrl(false);
        $this->SearchOptions = new ListOptions(TagClassName: "ew-search-option");

        // Button group for search
        $this->SearchOptions->UseDropDownButton = false;
        $this->SearchOptions->UseButtonGroup = true;
        $this->SearchOptions->DropDownButtonPhrase = $this->language->phrase("ButtonSearch");

        // Add group option item
        $item = $this->SearchOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;

        // Hide search options
        if ($this->isExport() || $this->CurrentAction && $this->CurrentAction != "search") {
            $this->SearchOptions->hideAllOptions();
        }
    }

    // Check if any search fields
    public function hasSearchFields(): bool
    {
        return false;
    }

    // Render search options
    protected function renderSearchOptions(): void
    {
        if (!$this->hasSearchFields() && $this->SearchOptions["searchtoggle"]) {
            $this->SearchOptions["searchtoggle"]->Visible = false;
        }
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset(all)
        $breadcrumb->add("summary", $this->TableVar, $url, "", $this->TableVar, true);
    }

    // Setup lookup options
    public function setupLookupOptions(DbField $fld): void
    {
        if ($fld->Lookup && $fld->Lookup->Options === null) {
            // Get default connection and filter
            $conn = $this->getConnection();
            $lookupFilter = "";

            // No need to check any more
            $fld->Lookup->Options = [];

            // Set up lookup SQL and connection
            switch ($fld->FieldVar) {
                case "x_STATUS":
                    break;
                default:
                    $lookupFilter = "";
                    break;
            }

            // Always call to Lookup->getSql so that user can setup Lookup->Options in Lookup_Selecting server event
            $qb = $fld->Lookup->getSqlBuilder(false, "", $lookupFilter, $this);

            // Set up lookup cache
            if (!$fld->hasLookupOptions() && $fld->UseLookupCache && $qb != null && count($fld->Lookup->Options) == 0 && count($fld->Lookup->FilterFields) == 0) {
                $totalCnt = $this->getRecordCount($qb, $conn);
                if ($totalCnt > $fld->LookupCacheCount) { // Total count > cache count, do not cache
                    return;
                }

                // Define a structured and consistent cache key prefix
                $cachePrefix = "lookup.result." . Container($fld->Lookup->LinkTable)->TableVar . ".";

                // Generate a unique cache key using SQL and parameters
                $sqlHash = hash("sha256", $qb->getSQL() . serialize($qb->getParameters()));
                $cacheKey = $cachePrefix . $sqlHash;

                // Fetch rows from cache or database
                $rows = $this->cache->get($cacheKey, fn (ItemInterface $item) => $qb->executeQuery()->fetchAllAssociative());
                $ar = [];
                foreach ($rows as $row) {
                    $row = $fld->Lookup->renderViewRow($row);
                    $key = $row["lf"];
                    if (IsFloatType($fld->Type)) { // Handle float field
                        $key = (float)$key;
                    }
                    $ar[strval($key)] = $row;
                }
                $fld->Lookup->Options = $ar;
            }
        }
    }

    // Render sort
    public function renderFieldHeader(DbField $fld): string
    {
        $sortUrl = "";
        $attrs = "";
        if ($this->PageID != "grid" && $fld->Sortable) {
            $sortUrl = $this->sortUrl($fld);
            $attrs = ' role="button" data-ew-action="sort" data-ajax="' . ($this->UseAjaxActions ? "true" : "false") . '" data-sort-url="' . HtmlEncode($sortUrl) . '" data-sort-type="1"';
            if ($this->ContextClass) { // Add context
                $attrs .= ' data-context="' . HtmlEncode($this->ContextClass) . '"';
            }
        }
        $html = '<div class="ew-table-header-caption"' . $attrs . '>' . $fld->caption() . '</div>';
        if ($sortUrl) {
            $html .= '<div class="ew-table-header-sort">' . $fld->getSortIcon() . '</div>';
        }
        if ($this->PageID != "grid" && !$this->isExport() && $fld->UseFilter) {
            $html .= '<div class="ew-filter-dropdown-btn" data-ew-action="filter" data-table="' . $fld->TableVar . '" data-field="' . $fld->FieldVar .
                '"><div class="ew-table-header-filter" role="button" aria-haspopup="true">' . $this->language->phrase("Filter") .
                (is_array($fld->EditValue) ? sprintf($this->language->phrase("FilterCount"), count($fld->EditValue)) : '') .
                '</div></div>';
        }
        $html = '<div class="ew-table-header-btn">' . $html . '</div>';
        if ($this->UseCustomTemplate) {
            $scriptId = str_replace("{id}", $fld->TableVar . "_" . $fld->Param, "tpc_{id}");
            $html = '<template id="' . $scriptId . '">' . $html . '</template>';
        }
        return $html;
    }

    // Sort URL
    public function sortUrl(DbField $fld): string
    {
        global $httpContext;
        if (
            $this->CurrentAction
            || $this->isExport()
            || $this->DrillDown
            || in_array($fld->Type, [128, 204, 205])
        ) { // Unsortable data type
                return "";
        } elseif ($fld->Sortable) {
            $params = ["order" => $fld->Name, "ordertype" => $fld->getNextSort()];
            if ($httpContext["DashboardReport"]) {
                $params[Config("PAGE_DASHBOARD")] = $httpContext["DashboardReport"];
            }
            $url = BuildUrl($this->CurrentPageName, $params);
            return $this->addMasterUrl($url);
        } else {
            return "";
        }
    }

    // Set up other options
    protected function setupOtherOptions(): void
    {
        // Filter button
        $item = $this->FilterOptions->add("savecurrentfilter");
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"fAppointment_reportsrch\" data-ew-action=\"none\">" . $this->language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = false;
        $item = $this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"fAppointment_reportsrch\" data-ew-action=\"none\">" . $this->language->phrase("DeleteFilter") . "</a>";
        $item->Visible = false;
        $this->FilterOptions->UseDropDownButton = true;
        $this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
        $this->FilterOptions->DropDownButtonPhrase = $this->language->phrase("Filters");

        // Add group option item
        $item = $this->FilterOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
    }

    /**
     * Get page number
     *
     * @return int Page number
     */
    public function getPageNumber(): int
    {
        $pageNumber = ParamInt(Config("TABLE_PAGE_NUMBER"));
        if ($pageNumber > 0) {
            return $pageNumber;
        } else {
            $startGroup = ParamInt(Config("TABLE_START_GROUP"));
            if ($startGroup === null) {
                $startGroup = $this->getStartGroup();
            }
            if ($this->DisplayGroups == 0) { // Cannot calculate, return first page
                return 1;
            } else {
                return ceil($startGroup / $this->DisplayGroups);
            }
        }
    }

    /**
     * Set up start group
     *
     * @param int Page number
     */
    public function setupStartGroup(int $pageNumber): void
    {
        // Set up page number
        if ($pageNumber <= 0) {
            $this->PageNumber = 1;
        } else {
            if ($this->DisplayGroups == 0) { // Cannot calcualte, use first page
                $this->PageNumber = 1;
            } else {
                $maxPage = ceil($this->TotalGroups / $this->DisplayGroups);
                if ($pageNumber > $maxPage) { // Make sure page number is not out of bound
                    $this->PageNumber = max($maxPage, 1);
                }
            }
        }

        // Set up start group
        $this->StartGroup = ($this->PageNumber - 1) * $this->DisplayGroups + 1;
        $this->setStartGroup($this->StartGroup);
    }

    // Reset pager
    protected function resetPager(): void
    {
        // Reset start position (reset command)
        $this->StartGroup = 1;
        $this->setStartGroup($this->StartGroup);
    }

    /**
     * Set up number of groups displayed per page
     *
     */
    public function setupDisplayGroups(): void
    {
        if (Param(Config("TABLE_GROUP_PER_PAGE")) !== null) {
            $wrk = Param(Config("TABLE_GROUP_PER_PAGE"));
            if (is_numeric($wrk)) {
                $this->DisplayGroups = intval($wrk);
            } else {
                if (SameText($wrk, "ALL")) { // Display all groups
                    $this->DisplayGroups = -1;
                } else {
                    $this->DisplayGroups = 3; // Non-numeric, load default
                }
            }
            $this->setGroupPerPage($this->DisplayGroups); // Save to session

            // Reset start position (reset command)
            $this->StartGroup = 1;
            $this->setStartGroup($this->StartGroup);
        } else {
            if ($this->getGroupPerPage() > 0) {
                $this->DisplayGroups = $this->getGroupPerPage(); // Restore from session
            } else {
                $this->DisplayGroups = 3; // Load default
            }
        }
    }

    /**
     * Get sort parameters based on sort links clicked
     *
     * @return string Sort
     */
    public function getSort(): string
    {
        if ($this->DrillDown) {
            return "APPOINTMENT_ID DESC";
        }
        $resetSort = Param("cmd") === "resetsort";
        $orderBy = Param("order", "");
        $orderType = Param("ordertype", "");

        // Check for a resetsort command
        if ($resetSort) {
            $this->setOrderBy("");
            $this->setStartGroup(1);
            $this->APPOINTMENT_ID->setSort("");
            $this->PATIENT_ID->setSort("");
            $this->DOCTOR_ID->setSort("");
            $this->APPOINTMENT_DATE->setSort("");
            $this->APPOINTMENT_TIME->setSort("");
            $this->STATUS->setSort("");

        // Check for an Order parameter
        } elseif ($orderBy != "") {
            $this->CurrentOrder = $orderBy;
            $this->CurrentOrderType = $orderType;
            $this->updateSort($this->APPOINTMENT_ID); // APPOINTMENT_ID
            $this->updateSort($this->PATIENT_ID); // PATIENT_ID
            $this->updateSort($this->DOCTOR_ID); // DOCTOR_ID
            $this->updateSort($this->APPOINTMENT_DATE); // APPOINTMENT_DATE
            $this->updateSort($this->APPOINTMENT_TIME); // APPOINTMENT_TIME
            $this->updateSort($this->STATUS); // STATUS
            $sortSql = $this->sortSql();
            $this->setOrderBy($sortSql);
            $this->setStartGroup(1);
        }

        // Set up default sort
        if ($this->getOrderBy() == "") {
            $useDefaultSort = true;
            if ($this->APPOINTMENT_ID->getSort() != "") {
                $useDefaultSort = false;
            }
            if ($useDefaultSort) {
                $this->APPOINTMENT_ID->setSort("DESC");
                $this->setOrderBy("APPOINTMENT_ID DESC");
            }
        }
        return $this->getOrderBy();
    }

    /**
     * Get filter (Note: following properties are set up)
     * - UserIDFilter => User ID filter
     * - DrillDownInPanel => Drill down in panel
     * - DrillDown => Drill down
     * - SearchCommand => Search command
     * - SearchWhere => Search filter
     * - Table / Field level search object / session variables
     *
     * @return string Filter
     */
    public function getFilter(): string
    {
        global $httpContext;
        $filter = "";

        // Load custom filters
        $this->pageFilterLoad();

        // Extended filter
        $extendedFilter = "";

        // Call Page Selecting event
        $this->pageSelecting($this->SearchWhere);

        // Update filter
        AddFilter($filter, $this->SearchWhere);

        // Add dashboard filter
        $httpContext["DashboardReport"] ??= Param(Config("PAGE_DASHBOARD"));
        if ($httpContext["DashboardReport"]) {
            AddFilter($filter, $this->getDashboardFilter($httpContext["DashboardReport"], $this->TableVar)); // Set up Dashboard Filter
        }
        return $filter;
    }

    // Page Load event
    public function pageLoad(): void
    {
        //Log("Page Load");
    }

    // Page Unload event
    public function pageUnload(): void
    {
        //Log("Page Unload");
    }

    // Page Redirecting event
    public function pageRedirecting(?string &$url): void
    {
        // Example:
        //$url = "your URL";
    }

    // Message Showing event
    // $type = ''|'success'|'danger'|'warning'
    public function messageShowing(string &$message, string $type): void
    {
        if ($type == "success") {
            //$message = "your success message";
        } elseif ($type == "danger") {
            //$message = "your failure message";
        } elseif ($type == "warning") {
            //$message = "your warning message";
        } else {
            //$message = "your message";
        }
    }

    // Page Render event
    public function pageRender(): void
    {
        //Log("Page Render");
    }

    // Page Data Rendering event
    public function pageDataRendering(string &$header): void
    {
        // Example:
        //$header = "your header";
    }

    // Page Data Rendered event
    public function pageDataRendered(string &$footer): void
    {
        // Example:
        //$footer = "your footer";
    }

    // Page Breaking event
    public function pageBreaking(bool &$break, string &$content): void
    {
        // Example:
        //$break = false; // Skip page break, or
        //$content = "<div style=\"break-after:page;\"></div>"; // Modify page break content
    }

    // Page Selecting event
    public function pageSelecting(string &$filter): void
    {
        // Enter your code here
    }

    // Load Filters event
    public function pageFilterLoad(): void
    {
        // Enter your code here
        // Example: Register/Unregister Custom Extended Filter
        //$this->registerFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
        //$this->registerFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
        //$this->unregisterFilter($this-><Field>, 'StartsWithA');
    }

    // Page Filter Validated event
    public function pageFilterValidated(): void
    {
        // Example:
        //$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value
    }

    // Page Filtering event
    public function pageFiltering(ReportField &$field, string &$filter, string $type, string $operator = "", string $value = "", string $condition = "", string $operator2 = "", string $value2 = ""): void
    {
        // Note: ALWAYS CHECK THE FILTER TYPE ($type)! Example:
        //if ($type == "dropdown" && $field->Name == "MyField") // Dropdown filter
        //    $filter = "..."; // Modify the filter
        //if ($type == "extended" && $field->Name == "MyField") // Extended filter
        //    $filter = "..."; // Modify the filter
        //if ($type == "custom" && $operator == "..." && $field->Name == "MyField") // Custom filter, $opr is the custom filter ID
        //    $filter = "..."; // Modify the filter
    }

    // Cell Rendered event
    public function cellRendered(SummaryField|ReportField &$field, mixed $currentValue, mixed &$viewValue, mixed &$viewAttrs, mixed &$cellAttrs, mixed &$hrefValue, mixed &$linkAttrs): void
    {
        //$viewValue = "xxx";
        //$viewAttrs["class"] = "xxx";
    }

    // Form Custom Validate event
    public function formCustomValidate(string &$customError): bool
    {
        // Return error message in $customError
        return true;
    }
}

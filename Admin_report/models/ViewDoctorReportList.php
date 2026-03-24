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

/**
 * Page class
 */
#[AsAlias("ViewDoctorReportList", true)]
class ViewDoctorReportList extends ViewDoctorReport implements PageInterface
{
    use MessagesTrait;
    use FormTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "list";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // Grid form hidden field names
    public string $FormName = "fview_doctor_reportlist";

    // CSS class/style
    public string $CurrentPageName = "ViewDoctorReportList"; // Route action

    // Page URLs
    public string $AddUrl = "";
    public string $EditUrl = "";
    public string $DeleteUrl = "";
    public string $ViewUrl = "";
    public string $CopyUrl = "";
    public string $ListUrl = "";

    // Update URLs
    public string $InlineAddUrl = "";
    public string $InlineCopyUrl = "";
    public string $InlineEditUrl = "";
    public string $GridAddUrl = "";
    public string $GridEditUrl = "";
    public string $MultiEditUrl = "";
    public string $MultiDeleteUrl = "";
    public string $MultiUpdateUrl = "";

    // Page headings
    public string $Heading = "";
    public string $Subheading = "";
    public string $PageHeader = "";
    public string $PageFooter = "";

    // Page layout
    public bool $UseLayout = true;

    // Page terminated
    private bool $terminated = false;
    public array $ChartData = [];

    // Class variables
    public ?ListOptions $ListOptions = null; // List options
    public ?ListOptions $ExportOptions = null; // Export options
    public ?ListOptions $SearchOptions = null; // Search options
    public ?ListOptionsCollection $OtherOptions = null; // Other options
    public ?ListOptions $HeaderOptions = null; // Header options
    public ?ListOptions $FooterOptions = null; // Footer options
    public ?ListOptions $FilterOptions = null; // Filter options
    public ?ListOptions $ImportOptions = null; // Import options
    public ?ListActions $ListActions = null; // List actions
    public int $SelectedCount = 0;
    public int $SelectedIndex = 0;
    public int $DisplayRecords = 20;
    public int $DefaultDisplayRecords = 20;
    public int $PageNumber = 1;
    public int $StartRecord = 0;
    public int $StopRecord = 0;
    public int $TotalRecords = 0;
    public ?int $RecordOffset = null; // Record offset (for View/Edit paging)
    public array $PagerOptions = ["proximity" => 2, "show_dots" => true];
    public string $PageSizes = "10,20,50,-1"; // Page sizes (comma separated)
    public string $UserIDFilter = "";
    public string $DefaultSearchWhere = ""; // Default search WHERE clause
    public string $SearchWhere = ""; // Search WHERE clause
    public bool $SearchByQueryBuilder = false; // Search by QueryBuilder
    public bool $UseExtendedBasicSearch = false;
    public string $SearchPanelClass = "ew-search-panel collapse show"; // Search Panel class
    public int $SearchColumnCount = 0; // For extended search
    public int $SearchFieldsPerRow = 1; // For extended search
    public int $RecordCount = 0; // Record count
    public int $InlineRowCount = 0;
    public int $StartRowCount = 1;
    public array $Attrs = []; // Row attributes and cell attributes
    public int|string $RowIndex = 0; // Row index
    public int $KeyCount = 0; // Key count
    public string $MultiColumnGridClass = "row-cols-md";
    public string $MultiColumnEditClass = "col-12 w-100";
    public string $MultiColumnCardClass = "card h-100 ew-card";
    public string $MultiColumnListOptionsPosition = "bottom-start";
    public bool $MasterRecordExists = false;
    public string $MultiSelectKey = "";
    public string $Command = "";
    public string $UserAction = ""; // User action
    public bool $RestoreSearch = false;
    public ?string $HashValue = null; // Hash value
    public ?SubPages $DetailPages = null;
    public string $TopContentClass = "ew-top";
    public string $MiddleContentClass = "ew-middle";
    public string $BottomContentClass = "ew-bottom";
    public bool $IsModal = false;
    private bool $UseInfiniteScroll = false;

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
        $this->TableVar = 'view_doctor_report';
        $this->TableName = 'view_doctor_report';

        // Table CSS class
        $this->TableClass = "table table-bordered table-hover table-sm ew-table";

        // CSS class name as context
        $this->ContextClass = ConvertToCssClass($this->TableVar);
        AppendClass($this->TableGridClass, $this->ContextClass);

        // Fixed header table
        if (!$this->UseCustomTemplate) {
            $this->setFixedHeaderTable(Config("USE_FIXED_HEADER_TABLE"), Config("FIXED_HEADER_TABLE_HEIGHT"));
        }

        // Initialize
        $httpContext["Page"] = $this;

        // Page URL
        $pageUrl = $this->pageUrl(false);

        // Initialize URLs
        $this->AddUrl = "ViewDoctorReportAdd";
        $this->InlineAddUrl = $this->addMasterUrl(BuildUrl($pageUrl, "action=add"));
        $this->GridAddUrl = $this->addMasterUrl(BuildUrl($pageUrl, "action=gridadd"));
        $this->GridEditUrl = $this->addMasterUrl(BuildUrl($pageUrl, "action=gridedit"));
        $this->MultiEditUrl = $this->addMasterUrl(BuildUrl($pageUrl, "action=multiedit"));
        $this->MultiDeleteUrl = "ViewDoctorReportDelete";
        $this->MultiUpdateUrl = "ViewDoctorReportUpdate";

        // Open connection
        $httpContext["Conn"] ??= $this->getConnection();

        // List options
        $this->ListOptions = new ListOptions(Tag: "td", TableVar: $this->TableVar);

        // Export options
        $this->ExportOptions = new ListOptions(TagClassName: "ew-export-option");

        // Import options
        $this->ImportOptions = new ListOptions(TagClassName: "ew-import-option");

        // Other options
        $this->OtherOptions = new ListOptionsCollection();

        // Grid-Add/Edit
        $this->OtherOptions["addedit"] = new ListOptions(
            TagClassName: "ew-add-edit-option",
            UseDropDownButton: false,
            DropDownButtonPhrase: $this->language->phrase("ButtonAddEdit"),
            UseButtonGroup: true
        );

        // Detail tables
        $this->OtherOptions["detail"] = new ListOptions(TagClassName: "ew-detail-option");
        // Actions
        $this->OtherOptions["action"] = new ListOptions(TagClassName: "ew-action-option");

        // Column visibility
        $this->OtherOptions["column"] = new ListOptions(
            TableVar: $this->TableVar,
            TagClassName: "ew-column-option",
            ButtonGroupClass: "ew-column-dropdown",
            UseDropDownButton: true,
            DropDownButtonPhrase: $this->language->phrase("Columns"),
            DropDownAutoClose: "outside",
            UseButtonGroup: false
        );

        // Filter options
        $this->FilterOptions = new ListOptions(TagClassName: "ew-filter-option");

        // List actions
        $this->ListActions = new ListActions();

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
        if ($this->TableName) {
            return Language()->phrase($this->PageID);
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

    // Set field visibility
    public function setVisibility(): void
    {
        $this->DOCTOR_ID->setVisibility();
        $this->Doctor_Name->setVisibility();
        $this->Specialisation->setVisibility();
        $this->EDUCATION->setVisibility();
        $this->Doctor_Status->setVisibility();
        $this->APPOINTMENT_ID->setVisibility();
        $this->APPOINTMENT_DATE->setVisibility();
        $this->Month_Name->setVisibility();
        $this->Month_Number->setVisibility();
        $this->Year->setVisibility();
        $this->Appointment_Status->setVisibility();
        $this->Total_Patients->setVisibility();
        $this->Avg_Rating->setVisibility();
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
            // Handle modal response
            if ($this->IsModal) { // Show as modal
                $pageName = GetPageName($url);
                $result = ["url" => GetUrl($url), "modal" => "1"];  // Assume return to modal for simplicity
                if (!SameString($pageName, GetPageName($this->getListUrl()))) { // Not List page
                    $result["caption"] = $this->getModalCaption($pageName);
                    $result["view"] = SameString($pageName, "ViewDoctorReportView"); // If View page, no primary button
                } else { // List page
                    $result["error"] = $this->getFailureMessage(); // List page should not be shown as modal => error
                }
                $this->Response = new JsonResponse($result);
            } else {
                $this->Response = new RedirectResponse(GetUrl($url), Config("REDIRECT_STATUS_CODE"));
            }
        }
        return; // Return to controller
    }

    // Get row(s) from array of entities
    protected function getRowsFromEntities(array $entities, bool $first = false): array
    {
        $rows = [];
        if (array_is_list($entities)) {
            foreach ($entities as $entity) {
                $row = $this->getRowFromEntity($entity);
                if ($first) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    // Get row from entity
    protected function getRowFromEntity(BaseEntity $entity): array
    {
        $row = [];
        foreach ($entity as $fldname => $val) {
            if ($this->TableName == Config("USER_TABLE_NAME") && $fldname == Config("PASSWORD_FIELD_NAME")) { // Skip user password field
                continue;
            }
            if (isset($this->Fields[$fldname]) && ($this->Fields[$fldname]->Visible || $this->Fields[$fldname]->IsPrimaryKey)) { // Primary key or Visible
                $fld = $this->Fields[$fldname];
                if ($fld->HtmlTag == "FILE") { // Upload field
                    if (IsEmpty($val)) {
                        $row[$fldname] = null;
                    } else {
                        $key = SessionId() . ServerVar("ENCRYPTION_KEY");
                        if ($fld->DataType == DataType::BLOB) {
                            $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                "/" . $fld->TableVar . "/" . $fld->Param . "/" . $this->getKeyAsString($entity, Config("ROUTE_COMPOSITE_KEY_SEPARATOR"))));
                            $row[$fldname] = ["type" => ContentType($val), "url" => $url, "name" => $fld->Param . ContentExtension($val)];
                        } elseif (!$fld->UploadMultiple || !ContainsString($val, Config("MULTIPLE_UPLOAD_SEPARATOR"))) { // Single file
                            $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                "/" . $fld->TableVar . "/" . Encrypt($fld->uploadPath() . $val, $key)));
                            $row[$fldname] = ["type" => MimeContentType($val), "url" => $url, "name" => $val];
                        } else { // Multiple files
                            $files = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $val);
                            $ar = [];
                            foreach ($files as $file) {
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . Encrypt($fld->uploadPath() . $file, $key)));
                                if (!IsEmpty($file)) {
                                    $ar[] = ["type" => MimeContentType($file), "url" => $url, "name" => $file];
                                }
                            }
                            $row[$fldname] = $ar;
                        }
                    }
                } else {
                    if ($fld->DataType == DataType::MEMO && $fld->MemoMaxLength > 0) {
                        $val = TruncateMemo($val, $fld->MemoMaxLength, $fld->TruncateMemoRemoveHtml);
                    }
                    if ($val instanceof DateTimeInterface) {
                        $val = $val->format(DATE_ATOM);
                    }
                    $row[$fldname] = $val;
                }
            }
        }
        return $row;
    }

    // Hide fields for add/edit
    protected function hideFieldsForAddEdit(): void
    {
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
     * Load records from array of entities
     *
     * @return void
     */
    public function loadRecordsFromArray(array $records, int $totalRecords): void
    {
        // Set up list options
        $this->setupListOptions();
        $this->setupExportOptions();

        // Search options
        $this->setupSearchOptions();

        // Other options
        $this->setupOtherOptions();

        // Set visibility
        $this->setVisibility();

        // Load records
        $this->TotalRecords = $totalRecords;
        $this->StartRecord = 1;
        $this->StopRecord = $this->DisplayRecords;
        $this->Records = $records;
        $this->CurrentRecord = null;

        // Set up pager
        $this->Pager = new Pager(
            $this->CurrentPageName,
            $this->StartRecord,
            $this->PageNumber,
            $this->DisplayRecords,
            $this->TotalRecords,
            $this->PageSizes,
            $this->ContextClass,
            $this->UseAjaxActions,
            $this->PagerOptions,
            $this->AutoHidePager,
            $this->AutoHidePageSizeSelector
        );
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

        // Get CurrentAction
        $this->CurrentAction = Param("action");

        // Is modal
        $this->IsModal = IsModal();

        // Get command
        $this->Command = strtolower(Get("cmd", ""));

        // Handle reset command
        $this->resetCmd();

        // Set up records per page
        $this->setupDisplayRecords();

        // Set up sorting order
        $this->setupSortOrder();

        // Get filter
        $this->Filter = $this->getFilter();

        // Display all records
        if ($this->DisplayRecords <= 0 || ($this->isExport() && $this->ExportAll)) {
            $this->DisplayRecords = -1; // Display all
        }

        // Set up start record position / page number
        if (!($this->isExport() && $this->ExportAll)) {
            $this->setupStartRecord();
            $this->PageNumber = $this->getPageNumber();
        }
    }

    /**
     * Page action
     * - Perform inline/grid insert/update actions
     *
     * @return ?Response
     */
    public function action(): ?Response
    {
        return null; // Continue to load the page
    }

    /**
     * Page run
     *
     * @return void
     */
    public function run(): void
    {
        global $httpContext;

        // Multi column button position
        $this->MultiColumnListOptionsPosition = Config("MULTI_COLUMN_LIST_OPTIONS_POSITION");
        $httpContext["DashboardReport"] ??= Param(Config("PAGE_DASHBOARD"));

        // Use layout
        $this->UseLayout = $this->UseLayout && ParamBool(Config("PAGE_LAYOUT"), true);

        // View
        $this->View = Get(Config("VIEW"));
        $httpContext["ExportType"] = $this->Export; // Get export parameter, used in header
        if ($httpContext["ExportType"] != "") {
            global $httpContext;
            $httpContext["SkipHeaderFooter"] = true;
        }

        // Get grid add count
        $gridaddcnt = Get(Config("TABLE_GRID_ADD_ROW_COUNT"), "");
        if (is_numeric($gridaddcnt) && $gridaddcnt > 0) {
            $this->GridAddRowCount = $gridaddcnt;
        }

        // Set up list options
        $this->setupListOptions();
        $this->setupExportOptions();
        $this->setVisibility();

        // Global Page Loading event (in userfn*.php)
        DispatchEvent(new PageLoadingEvent($this), PageLoadingEvent::class);

        // Page Load event
        if (method_exists($this, "pageLoad")) {
            $this->pageLoad();
        }

        // Hide fields for add/edit
        if (!$this->UseAjaxActions) {
            $this->hideFieldsForAddEdit();
        }
        // Use inline delete
        if ($this->UseAjaxActions) {
            $this->InlineDelete = true;
        }

        // Setup other options
        $this->setupOtherOptions();

        // Set up lookup cache
        $this->setupLookupOptions($this->Doctor_Status);
        $this->setupLookupOptions($this->Appointment_Status);

        // Update form name to avoid conflict
        if ($this->IsModal) {
            $this->FormName = "fview_doctor_reportgrid";
        }

        // Set up infinite scroll
        $this->UseInfiniteScroll = IsInfiniteScroll();

        // Process list action first
        if ($this->processListAction()) { // Ajax request
            $this->terminate();
            return;
        }

        // Set up Breadcrumb
        if (!$this->isExport()) {
            $this->setupBreadcrumb();
        }

        // Hide list options
        if ($this->isExport()) {
            $this->ListOptions->UseDropDownButton = false; // Disable drop down button
            $this->ListOptions->UseButtonGroup = false; // Disable button group
        } elseif ($this->isGridAdd() || $this->isGridEdit() || $this->isMultiEdit() || $this->isConfirm()) {
            $this->ListOptions->hideAllOptions();
            $this->ListOptions->UseDropDownButton = false; // Disable drop down button
            $this->ListOptions->UseButtonGroup = false; // Disable button group
        }

        // Hide options
        if (!(IsEmpty($this->CurrentAction) || $this->isSearch())) {
            $this->FilterOptions->hideAllOptions();
            $this->ImportOptions->hideAllOptions();
        }

        // Hide other options
        if ($this->isExport()) {
            $this->OtherOptions->hideAllOptions();
        }

        // Process filter list
        if ($this->processFilterList()) {
            $this->terminate();
            return;
        }

        // Build filter
        if ($this->isGridAdd()) {
            $this->StartRecord = 1;
            $this->DisplayRecords = $this->GridAddRowCount;
            $this->TotalRecords = $this->DisplayRecords;
            $this->StopRecord = $this->DisplayRecords;
        } elseif (($this->isEdit() || $this->isCopy() || $this->isInlineInserted() || $this->isInlineUpdated()) && $this->UseInfiniteScroll) { // Get current record only
            $this->StartRecord = 1;
            $this->StopRecord = $this->DisplayRecords;
        } elseif (
            $this->UseInfiniteScroll && $this->isGridInserted()
            || $this->UseInfiniteScroll && ($this->isGridEdit() || $this->isGridUpdated())
            || $this->UseInfiniteScroll && $this->isMultiUpdated()
        ) { // Get current records only
            $this->StartRecord = 1;
            $this->StopRecord = $this->DisplayRecords;
        } elseif ($this->isMultiEdit()) {
            $this->StartRecord = 1;
            $this->DisplayRecords = count($this->Records);
            $this->TotalRecords = $this->DisplayRecords;
            $this->StopRecord = $this->DisplayRecords;
        } elseif (!(IsApi() && IsExport())) {
            // Set no record found message
            if ((IsEmpty($this->CurrentAction) || $this->isSearch()) && $this->TotalRecords == 0) {
                if ($this->SearchWhere == "0=101") {
                    $this->setWarningMessage($this->language->phrase("EnterSearchCriteria"));
                } else {
                    $this->setWarningMessage($this->language->phrase("NoRecord"));
                }
            }
        }

        // Set up list action columns
        foreach ($this->ListActions as $listAction) {
            if ($listAction->getVisible()) {
                if ($listAction->Select == ActionType::MULTIPLE) { // Show checkbox column if multiple action
                    $this->ListOptions["checkbox"]->Visible = true;
                } elseif ($listAction->Select == ActionType::SINGLE) { // Show list action column
                    $this->ListOptions["listactions"]->Visible = true;
                }
            }
        }

        // Search options
        $this->setupSearchOptions();

        // Set up search panel class
        if ($this->SearchWhere != "") {
            if ($this->SearchByQueryBuilder) { // Hide search panel if using QueryBuilder
                RemoveClass($this->SearchPanelClass, "show");
            } else {
                AppendClass($this->SearchPanelClass, "show");
            }
        }

        // API list action
        if (IsApi()) {
            if (RouteAction() == Config("API_LIST_ACTION")) {
                if (!$this->isExport()) {
                    $rows = $this->getRowsFromEntities($this->Records);
                    $this->Response = new JsonResponse([
                        "success" => true,
                        "action" => Config("API_LIST_ACTION"),
                        $this->TableVar => $rows,
                        "totalRecordCount" => $this->TotalRecords
                    ]);
                    $this->terminate();
                }
                return;
            } elseif ($this->peekFailureMessage()) {
                $this->Response = new JsonResponse(["error" => $this->getFailureMessage()]);
                $this->terminate();
                return;
            }
        }

        // Render other options
        $this->renderOtherOptions();

        // Set up pager
        if (IsEmpty($this->CurrentAction) || !$this->IsModal && !in_array($this->CurrentAction, ["gridadd", "multiedit"])) {
            $this->Pager = new Pager(
                $this->CurrentPageName,
                $this->StartRecord,
                $this->PageNumber,
                $this->DisplayRecords,
                $this->TotalRecords,
                $this->PageSizes,
                $this->ContextClass,
                $this->UseAjaxActions,
                $this->PagerOptions,
                $this->AutoHidePager,
                $this->AutoHidePageSizeSelector
            );
        }

        // Set ReturnUrl in header if necessary
        if ($returnUrl = (FlashBag()->get("X-Return-Url")[0] ?? "")) {
            $this->Headers->set("X-Return-Url", GetUrl($returnUrl));
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
     * Get row data
     *
     * @return bool
     */
    public function getRowData(): bool
    {
        $isInlineAddOrCopy = $this->isCopy() || $this->isAdd();

        // Initialize
        if ($this->RowCount === null) {
            $this->setupGrid();
            $this->RowCount = 0;
        } else {
            // Reset for template row
            if ($this->RowIndex === '$rowindex$') {
                $this->RecordCount = $this->StartRecord - 1;
                $this->RowIndex = 0;
            }
            // Reset inline add/copy row
            if ($isInlineAddOrCopy && $this->RowIndex == 0) {
                $this->RecordCount = $this->StartRecord - 1;
                $this->RowIndex = 1;
            }
        }

        // Row data exists
        if ($this->RecordCount < $this->StopRecord || $this->RowIndex === '$rowindex$' || $isInlineAddOrCopy && $this->RowIndex == 0) {
            if (
                $this->isGridAdd()
                && $this->RowIndex !== '$rowindex$'
            ) {
                $this->RecordCount++;
            } elseif (
                $this->RowIndex !== '$rowindex$'
                && (!$this->isGridAdd() || $this->CurrentMode == "copy")
                && (!($isInlineAddOrCopy && $this->RowIndex == 0))
            ) {
                $this->RecordCount++;
                $this->fetch($this->RecordCount - $this->StartRecord + 1);
            }
            // Set up row
            $this->setupRow();

            // Skip 1) delete row / empty row for confirm page, 2) hidden row
            if (
                ($this->PageID == "list" || $this->PageID == "grid")
                && ($this->RowAction == "delete"
                || $this->RowAction == "insertdelete"
                || $this->RowAction == "insert" && $this->isConfirm() && $this->emptyRow()
                || $this->RowAction == "hide")
            ) {
                return $this->getRowData(); // Get next row
            }
            return true;
        } else {
            return false;
        }
    }

    // Get page number
    public function getPageNumber(): int
    {
        return ($this->DisplayRecords > 0 && $this->StartRecord > 0) ? ceil($this->StartRecord / $this->DisplayRecords) : 1;
    }

    // Set up number of records displayed per page
    public function setupDisplayRecords(): void
    {
        $pageSize = Get(Config("TABLE_REC_PER_PAGE"));
        if ($pageSize !== null) {
            if (is_numeric($pageSize)) {
                $this->DisplayRecords = (int)$pageSize;
            } else {
                if (SameText($pageSize, "all")) { // Display all records
                    $this->DisplayRecords = -1;
                } else {
                    $this->DisplayRecords = $this->DefaultDisplayRecords; // Non-numeric, load default
                }
            }
            $this->setRecordsPerPage($this->DisplayRecords); // Save to Session
            // Reset start position
            $this->StartRecord = 1;
            $this->setStartRecordNumber($this->StartRecord);
        } else {
            // Restore display records
            if ($this->Command != "json" && $this->getRecordsPerPage() != 0) {
                $this->DisplayRecords = $this->getRecordsPerPage(); // Restore from Session
            } else {
                $this->DisplayRecords = $this->DefaultDisplayRecords; // Load default
                $this->setRecordsPerPage($this->DisplayRecords); // Save default to Session
            }
        }
    }

    // Get list of filters
    public function getFilterList(): string
    {
        // Initialize
        $filterList = "";
        $savedFilterList = "";
        $filterList = Concat($filterList, $this->DOCTOR_ID->AdvancedSearch->toJson(), ","); // Field DOCTOR_ID
        $filterList = Concat($filterList, $this->Doctor_Name->AdvancedSearch->toJson(), ","); // Field Doctor_Name
        $filterList = Concat($filterList, $this->Specialisation->AdvancedSearch->toJson(), ","); // Field Specialisation
        $filterList = Concat($filterList, $this->EDUCATION->AdvancedSearch->toJson(), ","); // Field EDUCATION
        $filterList = Concat($filterList, $this->Doctor_Status->AdvancedSearch->toJson(), ","); // Field Doctor_Status
        $filterList = Concat($filterList, $this->APPOINTMENT_ID->AdvancedSearch->toJson(), ","); // Field APPOINTMENT_ID
        $filterList = Concat($filterList, $this->APPOINTMENT_DATE->AdvancedSearch->toJson(), ","); // Field APPOINTMENT_DATE
        $filterList = Concat($filterList, $this->Month_Name->AdvancedSearch->toJson(), ","); // Field Month_Name
        $filterList = Concat($filterList, $this->Month_Number->AdvancedSearch->toJson(), ","); // Field Month_Number
        $filterList = Concat($filterList, $this->Year->AdvancedSearch->toJson(), ","); // Field Year
        $filterList = Concat($filterList, $this->Appointment_Status->AdvancedSearch->toJson(), ","); // Field Appointment_Status
        $filterList = Concat($filterList, $this->Total_Patients->AdvancedSearch->toJson(), ","); // Field Total_Patients
        $filterList = Concat($filterList, $this->Avg_Rating->AdvancedSearch->toJson(), ","); // Field Avg_Rating
        if ($this->BasicSearch->Keyword != "") {
            $wrk = "\"" . Config("TABLE_BASIC_SEARCH") . "\":\"" . JsEncode($this->BasicSearch->Keyword) . "\",\"" . Config("TABLE_BASIC_SEARCH_TYPE") . "\":\"" . JsEncode($this->BasicSearch->Type) . "\"";
            $filterList = Concat($filterList, $wrk, ",");
        }

        // Return filter list in JSON
        if ($filterList != "") {
            $filterList = "\"data\":{" . $filterList . "}";
        }
        if ($savedFilterList != "") {
            $filterList = Concat($filterList, "\"filters\":" . $savedFilterList, ",");
        }
        return ($filterList != "") ? "{" . $filterList . "}" : "null";
    }

    // Process filter list
    protected function processFilterList(): bool
    {
        if (Post("ajax") == "savefilters") { // Save filter request (Ajax)
            $filters = Post("filters");
            Profile()->setSearchFilters("fview_doctor_reportsrch", $filters);
            $this->Response = new JsonResponse([["success" => true]]); // Success
            return true;
        }
        return false;
    }

    // Apply filter to AdvancedSearch
    protected function applyFilterToAdvancedSearch(string $field, array $filter): void
    {
        if ($advancedSearch = $this->fieldByParam($field)?->AdvancedSearch) {
            $keys = [
                "SearchValue" => "x_",
                "SearchOperator" => "z_",
                "SearchCondition" => "v_",
                "SearchValue2" => "y_",
                "SearchOperator2" => "w_",
            ];
            foreach ($keys as $property => $prefix) {
                $key = $prefix . $field;
                $advancedSearch->$property = $filter[$key] ?? "";
            }
            $advancedSearch->save();
        }
    }

    // Restore list of filters
    protected function restoreFilterList(): void
    {
        // Return if not reset filter
        if (Post("cmd") !== "resetfilter") {
            return;
        }
        $filter = json_decode(Post("filter"), true);
        $this->Command = "search";
        $this->applyFilterToAdvancedSearch("DOCTOR_ID", $filter); // DOCTOR_ID
        $this->applyFilterToAdvancedSearch("Doctor_Name", $filter); // Doctor_Name
        $this->applyFilterToAdvancedSearch("Specialisation", $filter); // Specialisation
        $this->applyFilterToAdvancedSearch("EDUCATION", $filter); // EDUCATION
        $this->applyFilterToAdvancedSearch("Doctor_Status", $filter); // Doctor_Status
        $this->applyFilterToAdvancedSearch("APPOINTMENT_ID", $filter); // APPOINTMENT_ID
        $this->applyFilterToAdvancedSearch("APPOINTMENT_DATE", $filter); // APPOINTMENT_DATE
        $this->applyFilterToAdvancedSearch("Month_Name", $filter); // Month_Name
        $this->applyFilterToAdvancedSearch("Month_Number", $filter); // Month_Number
        $this->applyFilterToAdvancedSearch("Year", $filter); // Year
        $this->applyFilterToAdvancedSearch("Appointment_Status", $filter); // Appointment_Status
        $this->applyFilterToAdvancedSearch("Total_Patients", $filter); // Total_Patients
        $this->applyFilterToAdvancedSearch("Avg_Rating", $filter); // Avg_Rating
        $this->BasicSearch->setKeyword($filter[Config("TABLE_BASIC_SEARCH")] ?? "");
        $this->BasicSearch->setType($filter[Config("TABLE_BASIC_SEARCH_TYPE")] ?? "");
    }

    // Advanced search WHERE clause based on QueryString
    public function advancedSearchWhere(bool $default = false): string
    {
        $where = "";
        $this->buildSearchSql($where, $this->DOCTOR_ID, $default, false); // DOCTOR_ID
        $this->buildSearchSql($where, $this->Doctor_Name, $default, true); // Doctor_Name
        $this->buildSearchSql($where, $this->Specialisation, $default, true); // Specialisation
        $this->buildSearchSql($where, $this->EDUCATION, $default, false); // EDUCATION
        $this->buildSearchSql($where, $this->Doctor_Status, $default, true); // Doctor_Status
        $this->buildSearchSql($where, $this->APPOINTMENT_ID, $default, false); // APPOINTMENT_ID
        $this->buildSearchSql($where, $this->APPOINTMENT_DATE, $default, true); // APPOINTMENT_DATE
        $this->buildSearchSql($where, $this->Month_Name, $default, true); // Month_Name
        $this->buildSearchSql($where, $this->Month_Number, $default, false); // Month_Number
        $this->buildSearchSql($where, $this->Year, $default, true); // Year
        $this->buildSearchSql($where, $this->Appointment_Status, $default, true); // Appointment_Status
        $this->buildSearchSql($where, $this->Total_Patients, $default, false); // Total_Patients
        $this->buildSearchSql($where, $this->Avg_Rating, $default, false); // Avg_Rating

        // Set up search command
        if (!$default && $where != "" && in_array($this->Command, ["", "reset", "resetall"])) {
            $this->Command = "search";
        }
        if (!$default && $this->Command == "search") {
            $this->DOCTOR_ID->AdvancedSearch->save(); // DOCTOR_ID
            $this->Doctor_Name->AdvancedSearch->save(); // Doctor_Name
            $this->Specialisation->AdvancedSearch->save(); // Specialisation
            $this->EDUCATION->AdvancedSearch->save(); // EDUCATION
            $this->Doctor_Status->AdvancedSearch->save(); // Doctor_Status
            $this->APPOINTMENT_ID->AdvancedSearch->save(); // APPOINTMENT_ID
            $this->APPOINTMENT_DATE->AdvancedSearch->save(); // APPOINTMENT_DATE
            $this->Month_Name->AdvancedSearch->save(); // Month_Name
            $this->Month_Number->AdvancedSearch->save(); // Month_Number
            $this->Year->AdvancedSearch->save(); // Year
            $this->Appointment_Status->AdvancedSearch->save(); // Appointment_Status
            $this->Total_Patients->AdvancedSearch->save(); // Total_Patients
            $this->Avg_Rating->AdvancedSearch->save(); // Avg_Rating
        }
        return $where;
    }

    // Query builder rules
    public function queryBuilderRules(): ?string
    {
        return Post("rules") ?? $this->getSessionRules();
    }

    // Quey builder WHERE clause
    public function queryBuilderWhere(string $fieldName = ""): string
    {
        // Get rules by query builder
        $rules = $this->queryBuilderRules();

        // Decode and parse rules
        $where = $rules ? $this->parseRules(json_decode($rules, true), $fieldName) : "";

        // Clear other search and save rules to session
        if ($where && $fieldName == "") { // Skip if get query for specific field
            $this->resetSearchParms();
            $this->DOCTOR_ID->AdvancedSearch->save(); // DOCTOR_ID
            $this->Doctor_Name->AdvancedSearch->save(); // Doctor_Name
            $this->Specialisation->AdvancedSearch->save(); // Specialisation
            $this->EDUCATION->AdvancedSearch->save(); // EDUCATION
            $this->Doctor_Status->AdvancedSearch->save(); // Doctor_Status
            $this->APPOINTMENT_ID->AdvancedSearch->save(); // APPOINTMENT_ID
            $this->APPOINTMENT_DATE->AdvancedSearch->save(); // APPOINTMENT_DATE
            $this->Month_Name->AdvancedSearch->save(); // Month_Name
            $this->Month_Number->AdvancedSearch->save(); // Month_Number
            $this->Year->AdvancedSearch->save(); // Year
            $this->Appointment_Status->AdvancedSearch->save(); // Appointment_Status
            $this->Total_Patients->AdvancedSearch->save(); // Total_Patients
            $this->Avg_Rating->AdvancedSearch->save(); // Avg_Rating
            $this->setSessionRules($rules);
        }

        // Return query
        return $where;
    }

    // Build search SQL
    protected function buildSearchSql(string &$where, DbField $fld, bool $default, bool $multiValue): void
    {
        $fldParm = $fld->Param;
        $fldVal = $default ? $fld->AdvancedSearch->SearchValueDefault : $fld->AdvancedSearch->SearchValue;
        $fldOpr = $default ? $fld->AdvancedSearch->SearchOperatorDefault : $fld->AdvancedSearch->SearchOperator;
        $fldCond = $default ? $fld->AdvancedSearch->SearchConditionDefault : $fld->AdvancedSearch->SearchCondition;
        $fldVal2 = $default ? $fld->AdvancedSearch->SearchValue2Default : $fld->AdvancedSearch->SearchValue2;
        $fldOpr2 = $default ? $fld->AdvancedSearch->SearchOperator2Default : $fld->AdvancedSearch->SearchOperator2;
        $fldVal = ConvertSearchValue($fldVal, $fldOpr, $fld);
        $fldVal2 = ConvertSearchValue($fldVal2, $fldOpr2, $fld);
        $fldOpr = ConvertSearchOperator($fldOpr, $fld, $fldVal);
        $fldOpr2 = ConvertSearchOperator($fldOpr2, $fld, $fldVal2);
        $wrk = "";
        if (Config("SEARCH_MULTI_VALUE_OPTION") == 1 && !$fld->UseFilter || !IsMultiSearchOperator($fldOpr)) {
            $multiValue = false;
        }
        if ($multiValue) {
            $wrk = $fldVal != "" ? GetMultiSearchSql($fld, $fldOpr, $fldVal, $this->Dbid) : ""; // Field value 1
            $wrk2 = $fldVal2 != "" ? GetMultiSearchSql($fld, $fldOpr2, $fldVal2, $this->Dbid) : ""; // Field value 2
            AddFilter($wrk, $wrk2, $fldCond);
        } else {
            $sep = Config("MULTIPLE_OPTION_SEPARATOR");
            if (is_array($fldVal)) {
                $fldVal = implode($sep, $fldVal);
            }
            if (is_array($fldVal2)) {
                $fldVal2 = implode($sep, $fldVal2);
            }
            $wrk = GetSearchSql($fld, $fldVal, $fldOpr, $fldCond, $fldVal2, $fldOpr2, $this->Dbid);
        }
        if ($this->SearchOption == "AUTO" && in_array($this->BasicSearch->getType(), ["AND", "OR"])) {
            $cond = $this->BasicSearch->getType();
        } else {
            $cond = SameText($this->SearchOption, "OR") ? "OR" : "AND";
        }
        AddFilter($where, $wrk, $cond);
    }

    // Show list of filters
    public function showFilterList(): string
    {
        if (!$this->ShowCurrentFilter) {
            return "";
        }

        // Initialize
        $filterList = "";
        $captionClass = $this->isExport("email") ? "ew-filter-caption-email" : "ew-filter-caption";
        $captionSuffix = $this->isExport("email") ? ": " : "";

        // Field DOCTOR_ID
        $filter = $this->queryBuilderWhere("DOCTOR_ID");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->DOCTOR_ID, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->DOCTOR_ID->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Doctor_Name
        $filter = $this->queryBuilderWhere("Doctor_Name");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Doctor_Name, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Doctor_Name->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Specialisation
        $filter = $this->queryBuilderWhere("Specialisation");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Specialisation, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Specialisation->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field EDUCATION
        $filter = $this->queryBuilderWhere("EDUCATION");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->EDUCATION, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->EDUCATION->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Doctor_Status
        $filter = $this->queryBuilderWhere("Doctor_Status");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Doctor_Status, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Doctor_Status->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field APPOINTMENT_ID
        $filter = $this->queryBuilderWhere("APPOINTMENT_ID");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->APPOINTMENT_ID, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->APPOINTMENT_ID->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field APPOINTMENT_DATE
        $filter = $this->queryBuilderWhere("APPOINTMENT_DATE");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->APPOINTMENT_DATE, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->APPOINTMENT_DATE->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Month_Name
        $filter = $this->queryBuilderWhere("Month_Name");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Month_Name, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Month_Name->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Month_Number
        $filter = $this->queryBuilderWhere("Month_Number");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Month_Number, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Month_Number->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Year
        $filter = $this->queryBuilderWhere("Year");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Year, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Year->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Appointment_Status
        $filter = $this->queryBuilderWhere("Appointment_Status");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Appointment_Status, false, true);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Appointment_Status->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Total_Patients
        $filter = $this->queryBuilderWhere("Total_Patients");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Total_Patients, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Total_Patients->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }

        // Field Avg_Rating
        $filter = $this->queryBuilderWhere("Avg_Rating");
        if (!$filter) {
            $this->buildSearchSql($filter, $this->Avg_Rating, false, false);
        }
        if ($filter != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->Avg_Rating->caption() . "</span>" . $captionSuffix . $filter . "</div>";
        }
        if ($this->BasicSearch->Keyword != "") {
            $filterList .= "<div><span class=\"" . $captionClass . "\">" . $this->language->phrase("BasicSearchKeyword") . "</span>" . $captionSuffix . $this->BasicSearch->Keyword . "</div>";
        }

        // Show Filters
        if ($filterList != "") {
            $message = "<div id=\"ew-filter-list\" class=\"callout callout-info d-table\"><div id=\"ew-current-filters\">" .
                $this->language->phrase("CurrentFilters") . "</div>" . $filterList . "</div>";
            $this->messageShowing($message, "");
            return $message;
        } else { // Output empty tag
            return "<div id=\"ew-filter-list\"></div>";
        }
    }

    // Return basic search WHERE clause based on search keyword and type
    public function basicSearchWhere(bool $default = false): string
    {
        $searchStr = "";

        // Fields to search
        $searchFlds = [];
        $searchFlds[] = $this->Doctor_Name;
        $searchFlds[] = $this->Specialisation;
        $searchFlds[] = $this->EDUCATION;
        $searchFlds[] = $this->Month_Name;
        $searchKeyword = $default ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
        $searchType = $default ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;

        // Get search SQL
        if ($searchKeyword != "") {
            $ar = $this->BasicSearch->keywordList($default);
            $searchStr = GetQuickSearchFilter($searchFlds, $ar, $searchType, Config("BASIC_SEARCH_ANY_FIELDS"), $this->Dbid);
            if (!$default && in_array($this->Command, ["", "reset", "resetall"])) {
                $this->Command = "search";
            }
        }
        if (!$default && $this->Command == "search") {
            $this->BasicSearch->setKeyword($searchKeyword);
            $this->BasicSearch->setType($searchType);
        }
        return $searchStr;
    }

    // Check if search parm exists
    protected function checkSearchParms(): bool
    {
        // Check basic search
        if ($this->BasicSearch->issetSession()) {
            return true;
        }
        if ($this->DOCTOR_ID->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Doctor_Name->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Specialisation->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->EDUCATION->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Doctor_Status->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->APPOINTMENT_ID->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->APPOINTMENT_DATE->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Month_Name->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Month_Number->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Year->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Appointment_Status->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Total_Patients->AdvancedSearch->issetSession()) {
            return true;
        }
        if ($this->Avg_Rating->AdvancedSearch->issetSession()) {
            return true;
        }
        return false;
    }

    // Clear all search parameters
    protected function resetSearchParms(): void
    {
        // Clear search WHERE clause
        $this->SearchWhere = "";
        $this->setSearchWhere($this->SearchWhere);

        // Clear basic search parameters
        $this->resetBasicSearchParms();

        // Clear advanced search parameters
        $this->resetAdvancedSearchParms();

        // Clear queryBuilder
        $this->setSessionRules("");
    }

    // Load advanced search default values
    protected function loadAdvancedSearchDefault(): bool
    {
        return false;
    }

    // Clear all basic search parameters
    protected function resetBasicSearchParms(): void
    {
        $this->BasicSearch->unsetSession();
    }

    // Clear all advanced search parameters
    protected function resetAdvancedSearchParms(): void
    {
        $this->DOCTOR_ID->AdvancedSearch->unsetSession();
        $this->Doctor_Name->AdvancedSearch->unsetSession();
        $this->Specialisation->AdvancedSearch->unsetSession();
        $this->EDUCATION->AdvancedSearch->unsetSession();
        $this->Doctor_Status->AdvancedSearch->unsetSession();
        $this->APPOINTMENT_ID->AdvancedSearch->unsetSession();
        $this->APPOINTMENT_DATE->AdvancedSearch->unsetSession();
        $this->Month_Name->AdvancedSearch->unsetSession();
        $this->Month_Number->AdvancedSearch->unsetSession();
        $this->Year->AdvancedSearch->unsetSession();
        $this->Appointment_Status->AdvancedSearch->unsetSession();
        $this->Total_Patients->AdvancedSearch->unsetSession();
        $this->Avg_Rating->AdvancedSearch->unsetSession();
    }

    // Restore all search parameters
    protected function restoreSearchParms(): void
    {
        $this->RestoreSearch = true;

        // Restore basic search values
        $this->BasicSearch->load();

        // Restore advanced search values
        $this->DOCTOR_ID->AdvancedSearch->load();
        $this->Doctor_Name->AdvancedSearch->load();
        $this->Specialisation->AdvancedSearch->load();
        $this->EDUCATION->AdvancedSearch->load();
        $this->Doctor_Status->AdvancedSearch->load();
        $this->APPOINTMENT_ID->AdvancedSearch->load();
        $this->APPOINTMENT_DATE->AdvancedSearch->load();
        $this->Month_Name->AdvancedSearch->load();
        $this->Month_Number->AdvancedSearch->load();
        $this->Year->AdvancedSearch->load();
        $this->Appointment_Status->AdvancedSearch->load();
        $this->Total_Patients->AdvancedSearch->load();
        $this->Avg_Rating->AdvancedSearch->load();
    }

    // Set up sort parameters
    protected function setupSortOrder(): void
    {
        // Load default Sorting Order
        if ($this->Command != "json") {
            $defaultSort = ""; // Set up default sort
            if ($this->getSessionOrderBy() == "" && $defaultSort != "") {
                $this->setSessionOrderBy($defaultSort);
            }
        }

        // Check for "order" parameter
        if (Get("order") !== null) {
            $this->CurrentOrder = Get("order");
            $this->CurrentOrderType = Get("ordertype", "");
            $this->updateSort($this->DOCTOR_ID); // DOCTOR_ID
            $this->updateSort($this->Doctor_Name); // Doctor_Name
            $this->updateSort($this->Specialisation); // Specialisation
            $this->updateSort($this->EDUCATION); // EDUCATION
            $this->updateSort($this->Doctor_Status); // Doctor_Status
            $this->updateSort($this->APPOINTMENT_ID); // APPOINTMENT_ID
            $this->updateSort($this->APPOINTMENT_DATE); // APPOINTMENT_DATE
            $this->updateSort($this->Month_Name); // Month_Name
            $this->updateSort($this->Month_Number); // Month_Number
            $this->updateSort($this->Year); // Year
            $this->updateSort($this->Appointment_Status); // Appointment_Status
            $this->updateSort($this->Total_Patients); // Total_Patients
            $this->updateSort($this->Avg_Rating); // Avg_Rating
            $this->setStartRecordNumber(1); // Reset start position
        }

        // Update field sort
        $this->updateFieldSort();
    }

    /**
     * Handles reset command
     *
     * This method processes the following reset commands:
     * - 'reset':      Resets all search parameters to their default values.
     * - 'resetall':   Resets both search parameters and master/detail parameters.
     * - 'resetsort':  Resets sorting parameters to their default state.
     *
     * @return void
     */
    protected function resetCmd(): void
    {
        // Check if reset command
        if (StartsString("reset", $this->Command)) {
            // Reset search criteria
            if ($this->Command == "reset" || $this->Command == "resetall") {
                $this->resetSearchParms();
            }

            // Reset (clear) sorting order
            if ($this->Command == "resetsort") {
                $orderBy = "";
                $this->setSessionOrderBy($orderBy);
                $this->DOCTOR_ID->setSort("");
                $this->Doctor_Name->setSort("");
                $this->Specialisation->setSort("");
                $this->EDUCATION->setSort("");
                $this->Doctor_Status->setSort("");
                $this->APPOINTMENT_ID->setSort("");
                $this->APPOINTMENT_DATE->setSort("");
                $this->Month_Name->setSort("");
                $this->Month_Number->setSort("");
                $this->Year->setSort("");
                $this->Appointment_Status->setSort("");
                $this->Total_Patients->setSort("");
                $this->Avg_Rating->setSort("");
            }

            // Reset start position
            $this->StartRecord = 1;
            $this->setStartRecordNumber($this->StartRecord);
        }
    }

    // Set up list options
    protected function setupListOptions(): void
    {
        // Add group option item ("button")
        $item = $this->ListOptions->addGroupOption();
        $item->Body = "";
        $item->OnLeft = false;
        $item->Visible = false;

        // List actions
        $item = $this->ListOptions->add("listactions");
        $item->CssClass = "text-nowrap";
        $item->OnLeft = false;
        $item->Visible = false;
        $item->ShowInButtonGroup = false;
        $item->ShowInDropDown = false;

        // "checkbox"
        $item = $this->ListOptions->add("checkbox");
        $item->Visible = false;
        $item->OnLeft = false;
        $item->Header = "<div class=\"form-check\"><input type=\"checkbox\" name=\"key\" id=\"key\" class=\"form-check-input\" data-ew-action=\"select-all-keys\"></div>";
        if ($item->OnLeft) {
            $item->moveTo(0);
        }
        $item->ShowInDropDown = false;
        $item->ShowInButtonGroup = false;

        // "sequence"
        $item = $this->ListOptions->add("sequence");
        $item->CssClass = "text-nowrap";
        $item->Visible = true;
        $item->OnLeft = true; // Always on left
        $item->ShowInDropDown = false;
        $item->ShowInButtonGroup = false;

        // Drop down button for ListOptions
        $this->ListOptions->UseDropDownButton = true;
        $this->ListOptions->DropDownButtonPhrase = $this->language->phrase("ButtonListOptions");
        $this->ListOptions->UseButtonGroup = false;
        if ($this->ListOptions->UseButtonGroup && IsMobile()) {
            $this->ListOptions->UseDropDownButton = true;
        }

        // $this->ListOptions->ButtonClass = ""; // Class for button group

            // Set up list options (to be implemented by extensions)

        // Call ListOptions_Load event
        $this->listOptionsLoad();
        $item = $this->ListOptions[$this->ListOptions->GroupOptionName];
        $item->Visible = $this->ListOptions->groupOptionVisible();
    }

    // Set up export options
    protected function setupExportOptions(): void
    {
        // Page URL for export
        $pageUrl = $this->pageUrl(false);
        
        // Export to PDF
        $item = $this->ExportOptions->add("pdf");
        $item->Body = "<a href=\"" . $pageUrl . "?export=pdf\" class=\"ew-export-link\" data-export=\"pdf\">" . $this->language->phrase("ExportToPdf") . "</a>";
        $item->Visible = true;
        $item->CssClass = "ew-export-link";
        $item->OnLeft = false;
        $item->UseImageAndText = true;

        // Export to Excel
        $item = $this->ExportOptions->add("excel");
        $item->Body = "<a href=\"" . $pageUrl . "?export=excel\" class=\"ew-export-link\" data-export=\"excel\">" . $this->language->phrase("ExportToExcel") . "</a>";
        $item->Visible = true;
        $item->CssClass = "ew-export-link";
        $item->OnLeft = false;
        $item->UseImageAndText = true;

        // Use button group instead of dropdown
        $this->ExportOptions->UseDropDownButton = false;
        $this->ExportOptions->UseButtonGroup = true;
        $this->ExportOptions->ButtonGroupClass = "btn-group";
    }

    // Add "hash" parameter to URL
    public function urlAddHash(string $url, string $hash): string
    {
        return $this->UseAjaxActions ? $url : BuildUrl($url, "hash=" . $hash);
    }

    // Render list options
    public function renderListOptions(): void
    {
        $this->ListOptions->loadDefault();

        // Call ListOptions_Rendering event
        $this->listOptionsRendering();

        // "sequence"
        $opt = $this->ListOptions["sequence"];
        $opt->Body = FormatSequenceNumber($this->RecordCount);
        $pageUrl = $this->pageUrl(false);
        if ($this->CurrentMode == "view") { // Check view mode
        } // End View mode

        // Render list action buttons (single selection)
        $opt = $this->ListOptions["listactions"];
        if ($opt && !$this->isExport() && !$this->CurrentAction) {
            $body = "";
            $links = [];
            foreach ($this->ListActions as $listAction) { // ActionType::SINGLE
                if (in_array($this->RowType, [RowType::VIEW, RowType::PREVIEW])) {
                    $listAction->setFields($this->Fields);
                }
                if ($listAction->Select == ActionType::SINGLE && $listAction->getVisible()) {
                    $caption = $listAction->getCaption();
                    $title = HtmlTitle($caption);
                    $icon = $listAction->Icon ? "<i class=\"" . HtmlEncode(str_replace(" ew-icon", "", $listAction->Icon)) . "\" data-caption=\"" . $title . "\"></i> " : "";
                    $link = "<li><button type=\"button\" class=\"dropdown-item ew-action ew-list-action" . ($listAction->getEnabled() ? "" : " disabled") .
                        "\" data-caption=\"" . $title . "\" data-ew-action=\"submit\" form=\"fview_doctor_reportlist\" data-key=\"" . $this->keyToJson(true) .
                        "\"" . $listAction->toDataAttributes() . ">" . $icon . " " . $caption . "</button></li>";
                    $links[] = $link;
                    if ($body == "") { // Setup first button
                        $body = "<button type=\"button\" class=\"btn btn-default ew-action ew-list-action" . ($listAction->getEnabled() ? "" : " disabled") .
                            "\" title=\"" . $title . "\" data-caption=\"" . $title . "\" data-ew-action=\"submit\" form=\"fview_doctor_reportlist\" data-key=\"" . $this->keyToJson(true) .
                            "\"" . $listAction->toDataAttributes() . ">" . $icon . " " . $caption . "</button>";
                    }
                }
            }
            if (count($links) > 1) { // More than one buttons, use dropdown
                $body = "<button type=\"button\" class=\"dropdown-toggle btn btn-default ew-actions\" title=\"" . HtmlTitle($this->language->phrase("ListActionButton")) . "\" data-bs-toggle=\"dropdown\">" . $this->language->phrase("ListActionButton") . "</button>";
                $content = implode(array_map(fn($link) => "<li>" . $link . "</li>", $links));
                $body .= "<ul class=\"dropdown-menu" . ($opt->OnLeft ? "" : " dropdown-menu-right") . "\">" . $content . "</ul>";
                $body = "<div class=\"btn-group btn-group-sm\">" . $body . "</div>";
            }
            if (count($links) > 0) {
                $opt->Body = $body;
            }
        }

        // "checkbox"
        $opt = $this->ListOptions["checkbox"];

        // Render list options (to be implemented by extensions)

        // Call ListOptions_Rendered event
        $this->listOptionsRendered();
    }

    // Set up other options
    protected function setupOtherOptions(): void
    {
        $options = $this->OtherOptions;
        $option = $options["action"];

        // Show column list for column visibility
        if ($this->UseColumnVisibility) {
            $option = $this->OtherOptions["column"];
            $item = $option->addGroupOption();
            $item->Body = "";
            $item->Visible = $this->UseColumnVisibility;
            $this->createColumnOption($option, "DOCTOR_ID");
            $this->createColumnOption($option, "Doctor_Name");
            $this->createColumnOption($option, "Specialisation");
            $this->createColumnOption($option, "EDUCATION");
            $this->createColumnOption($option, "Doctor_Status");
            $this->createColumnOption($option, "APPOINTMENT_ID");
            $this->createColumnOption($option, "APPOINTMENT_DATE");
            $this->createColumnOption($option, "Month_Name");
            $this->createColumnOption($option, "Month_Number");
            $this->createColumnOption($option, "Year");
            $this->createColumnOption($option, "Appointment_Status");
            $this->createColumnOption($option, "Total_Patients");
            $this->createColumnOption($option, "Avg_Rating");
        }

        // Set up custom actions
        foreach ($this->CustomActions as $name => $action) {
            $this->ListActions[$name] = $action;
        }

        // Set up options default
        foreach ($options as $name => $option) {
            if ($name != "column") { // Always use dropdown for column
                $option->UseDropDownButton = false;
                $option->UseButtonGroup = true;
            }
            //$option->ButtonClass = ""; // Class for button group
            $item = $option->addGroupOption();
            $item->Body = "";
            $item->Visible = false;
        }
        $options["addedit"]->DropDownButtonPhrase = $this->language->phrase("ButtonAddEdit");
        $options["detail"]->DropDownButtonPhrase = $this->language->phrase("ButtonDetails");
        $options["action"]->DropDownButtonPhrase = $this->language->phrase("ButtonActions");

        // Filter button
        $item = $this->FilterOptions->add("savecurrentfilter");
        $item->Body = "<a class=\"ew-save-filter\" data-form=\"fview_doctor_reportsrch\" data-ew-action=\"none\">" . $this->language->phrase("SaveCurrentFilter") . "</a>";
        $item->Visible = true;
        $item = $this->FilterOptions->add("deletefilter");
        $item->Body = "<a class=\"ew-delete-filter\" data-form=\"fview_doctor_reportsrch\" data-ew-action=\"none\">" . $this->language->phrase("DeleteFilter") . "</a>";
        $item->Visible = true;
        $this->FilterOptions->UseDropDownButton = true;
        $this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
        $this->FilterOptions->DropDownButtonPhrase = $this->language->phrase("Filters");

        // Add group option item
        $item = $this->FilterOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;

        // Page header/footer options
        $this->HeaderOptions = new ListOptions(TagClassName: "ew-header-option", UseDropDownButton: false, UseButtonGroup: false);
        $item = $this->HeaderOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
        $this->FooterOptions = new ListOptions(TagClassName: "ew-footer-option", UseDropDownButton: false, UseButtonGroup: false);
        $item = $this->FooterOptions->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
    }

    // Active user filter
    // - Get active users by SQL (SELECT COUNT(*) FROM UserTable WHERE ProfileField LIKE '%"SessionID":%')
    protected function activeUserFilter(): string
    {
        if (UserProfile::$FORCE_LOGOUT_USER_ENABLED) {
            $userProfileField = $this->Fields[Config("USER_PROFILE_FIELD_NAME")];
            return $userProfileField->Expression . " LIKE '%\"" . UserProfile::$SESSION_ID . "\":%'";
        }
        return "0=1"; // No active users
    }

    // Create new column option
    protected function createColumnOption(ListOptions $options, string $name): void
    {
        $field = $this->Fields[$name] ?? null;
        if ($field?->Visible) {
            $item = $options->add($field->Name);
            $item->Body = '<button class="dropdown-item">' .
                '<div class="form-check ew-dropdown-checkbox">' .
                '<div class="form-check-input ew-dropdown-check-input" data-field="' . $field->Param . '"></div>' .
                '<label class="form-check-label ew-dropdown-check-label">' . $field->caption() . '</label></div></button>';
        }
    }

    // Render other options
    public function renderOtherOptions(): void
    {
        $options = $this->OtherOptions;
        $option = $options["action"];
        // Render list action buttons
        foreach ($this->ListActions as $listAction) { // ActionType::MULTIPLE
            if ($listAction->Select == ActionType::MULTIPLE && $listAction->getVisible()) {
                $item = $option->add("custom_" . $listAction->Action);
                $caption = $listAction->getCaption();
                $icon = $listAction->Icon ? '<i class="' . HtmlEncode($listAction->Icon) . '" data-caption="' . HtmlEncode($caption) . '"></i>' . $caption : $caption;
                $item->Body = '<button type="button" class="btn btn-default ew-action ew-list-action" title="' . HtmlEncode($caption) . '" data-caption="' . HtmlEncode($caption) . '" data-ew-action="submit" form="fview_doctor_reportlist"' . $listAction->toDataAttributes() . '>' . $icon . '</button>';
                $item->Visible = true;
            }
        }

        // Hide multi edit, grid edit and other options
        if ($this->TotalRecords <= 0) {
            $option = $options["addedit"];
            $item = $option["gridedit"];
            if ($item) {
                $item->Visible = false;
            }
            $option = $options["action"];
            $option->hideAllOptions();
        }
    }

    // Process list action
    protected function processListAction(): bool
    {
        $users = [];
        $user = "";
        $filter = $this->getFilterFromRecordKeys();
        $userAction = Post("action", "");
        if ($filter != "" && $userAction != "") {
            $conn = $this->getConnection();
            // Clear current action
            $this->CurrentAction = "";
            // Check permission first
            $caption = $userAction;
            $listAction = $this->ListActions[$userAction] ?? null;
            if ($listAction) {
                $this->UserAction = $userAction;
                $caption = $listAction->getCaption();
                if (!$listAction->Allowed) {
                    $errmsg = sprintf($this->language->phrase("CustomActionNotAllowed"), $caption);
                    if (Post("ajax") == $userAction) { // Ajax
                        echo "<p class=\"text-danger\">" . $errmsg . "</p>";
                        return true;
                    } else {
                        $this->setFailureMessage($errmsg);
                        return false;
                    }
                }
            } else {
                // Skip checking, handle by Row_CustomAction
            }
            $rows = $this->loadEntitiesFromFilter($filter);
            $this->SelectedCount = count($rows);
            $this->ActionValue = Post("actionvalue");

            // Call row action event
            if ($this->SelectedCount > 0) {
                if ($this->UseTransaction) {
                    $conn->beginTransaction();
                }
                $this->SelectedIndex = 0;
                foreach ($rows as $row) {
                    $this->SelectedIndex++;
                    if ($listAction) {
                        $processed = $listAction->handle($row, $this);
                        if (!$processed) {
                            break;
                        }
                    }
                    $processed = $this->rowCustomAction($userAction, $row);
                    if (!$processed) {
                        break;
                    }
                }
                if ($processed) {
                    if ($this->UseTransaction) { // Commit transaction
                        if ($conn->isTransactionActive()) {
                            $conn->commit();
                        }
                    }
                    if (!$this->peekSuccessMessage() && !IsEmpty($listAction?->SuccessMessage)) {
                        $this->setSuccessMessage($listAction->SuccessMessage);
                    }
                    if (!$this->peekSuccessMessage()) {
                        $this->setSuccessMessage(sprintf($this->language->phrase("CustomActionCompleted"), $caption)); // Set up success message
                    }
                } else {
                    if ($this->UseTransaction) { // Rollback transaction
                        if ($conn->isTransactionActive()) {
                            $conn->rollback();
                        }
                    }
                    if (!$this->peekFailureMessage()) {
                        $this->setFailureMessage($listAction->FailureMessage);
                    }

                    // Set up error message
                    if ($this->peekSuccessMessage() || $this->peekFailureMessage()) {
                        // Use the message, do nothing
                    } elseif ($this->CancelMessage != "") {
                        $this->setFailureMessage($this->CancelMessage);
                        $this->CancelMessage = "";
                    } else {
                        $this->setFailureMessage(sprintf($this->language->phrase("CustomActionFailed"), $caption));
                    }
                }
            }
            if (Post("ajax") == $userAction) { // Ajax
                if (IsJsonResponse($this->Response)) { // List action returns JSON
                    $this->clearMessages(); // Clear messages
                } else {
                    if ($this->peekSuccessMessage()) {
                        echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
                    }
                    if ($this->peekFailureMessage()) {
                        echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
                    }
                }
                return true;
            }
        }
        return false; // Not ajax request
    }

    // Set up Grid
    protected function setupGrid(): void
    {
        if ($this->ExportAll && $this->isExport()) {
            $this->StopRecord = $this->TotalRecords;
        } else {
            // Set the last record to display
            if ($this->DisplayRecords == -1) {
                $this->DisplayRecords = $this->TotalRecords;
            }
            if ($this->TotalRecords > $this->StartRecord + $this->DisplayRecords - 1) {
                $this->StopRecord = $this->StartRecord + $this->DisplayRecords - 1;
            } else {
                $this->StopRecord = $this->TotalRecords;
            }
        }
        $this->RecordCount = $this->StartRecord - 1;
        if ($this->CurrentRecord !== null) {
            // Nothing to do
        } elseif ($this->isGridAdd() && !$this->AllowAddDeleteRow && $this->StopRecord == 0) { // Grid-Add with no records
            $this->StopRecord = $this->GridAddRowCount;
        } elseif ($this->isAdd() && $this->TotalRecords == 0) { // Inline-Add with no records
            $this->StopRecord = 1;
        }

        // Initialize aggregate
        $this->renderRow(RowType::AGGREGATEINIT);
        if (($this->isGridAdd() || $this->isGridEdit())) { // Render template row first
            $this->RowIndex = '$rowindex$';
        }
    }

    // Set up Row
    protected function setupRow(): void
    {
        if ($this->isGridAdd() || $this->isGridEdit()) {
            if ($this->RowIndex === '$rowindex$') { // Render template row first
                $this->loadRowValues();

                // Set row properties
                $this->resetAttributes();
                $this->RowAttrs->merge(["data-rowindex" => $this->RowIndex, "id" => "r0_view_doctor_report", "data-rowtype" => RowType::ADD]);
                $this->RowAttrs->appendClass("ew-template");
                // Render row
                $this->renderRow(RowType::ADD, false);

                // Render list options
                $this->renderListOptions();

                // Reset record count for template row
                $this->RecordCount--;
                return;
            }
        }

        // Set up key count
        $this->KeyCount = $this->RowIndex;

        // Init row class and style
        $this->resetAttributes();
        $this->CssClass = "";
        if ($this->isCopy() && $this->InlineRowCount == 0 && !$this->loadRow()) { // Inline copy
            $this->CurrentAction = "add";
        }
        if ($this->isAdd() && $this->InlineRowCount == 0 || $this->isGridAdd()) {
            $this->loadRowValues(); // Load default values
            $this->OldKey = [];
            $this->setKey($this->OldKey);
        } elseif ($this->isInlineInserted() && $this->UseInfiniteScroll) {
            // Nothing to do, just use current values
        } elseif (!($this->isCopy() && $this->InlineRowCount == 0)) {
            $this->loadRowValues($this->CurrentRecord); // Load row values
            if ($this->isGridEdit() || $this->isMultiEdit()) {
                $this->OldKey = $this->getKey(true); // Get from CurrentValue
                $this->setKey($this->OldKey);
            }
        }
        $this->RowType = RowType::VIEW; // Render view
        if (($this->isAdd() || $this->isCopy()) && $this->InlineRowCount == 0 || $this->isGridAdd()) { // Add
            $this->RowType = RowType::ADD; // Render add
        }

        // Inline Add/Copy row (row 0)
        if ($this->RowType == RowType::ADD && ($this->isAdd() || $this->isCopy())) {
            $this->InlineRowCount++;
            $this->RecordCount--; // Reset record count for inline add/copy row
            if ($this->TotalRecords == 0) { // Reset stop record if no records
                $this->StopRecord = 0;
            }
        } else {
            // Inline Edit row
            if ($this->RowType == RowType::EDIT && $this->isEdit()) {
                $this->InlineRowCount++;
            }
            $this->RowCount++; // Increment row count
        }

        // Set up row attributes
        $this->OldKey = $this->getKey(true);
        $this->RowAttrs->merge([
            "data-rowindex" => $this->RowCount,
            "data-key" => $this->getOldKeyAsString(),
            "id" => "r" . $this->RowCount . "_view_doctor_report",
            "data-rowtype" => $this->RowType,
            "data-inline" => ($this->isAdd() || $this->isCopy() || $this->isEdit()) ? "true" : "false", // Inline-Add/Copy/Edit
            "class" => ($this->RowCount % 2 != 1) ? "ew-table-alt-row" : "",
        ]);
        if ($this->isAdd() && $this->RowType == RowType::ADD || $this->isEdit() && $this->RowType == RowType::EDIT) { // Inline-Add/Edit row
            $this->RowAttrs->appendClass("table-active");
        }

        // Render row
        $this->renderRow($this->RowType, false);

        // Render list options
        $this->renderListOptions();
    }

    // Load basic search values
    protected function loadBasicSearchValues(): bool
    {
        $keyword = Get(Config("TABLE_BASIC_SEARCH"));
        if ($keyword === null) {
            return false;
        } else {
            $this->BasicSearch->setKeyword($keyword, false);
            if ($this->BasicSearch->Keyword != "" && $this->Command == "") {
                $this->Command = "search";
            }
            $this->BasicSearch->setType(Get(Config("TABLE_BASIC_SEARCH_TYPE"), ""), false);
            return true;
        }
    }

    // Load search values for validation
    protected function loadSearchValues(): bool
    {
        // Load search values
        $hasValue = false;

        // Load query builder rules
        $rules = Post("rules");
        if ($rules && $this->Command == "") {
            $this->QueryRules = $rules;
            $this->Command = "search";
        }

        // DOCTOR_ID
        if ($this->DOCTOR_ID->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->DOCTOR_ID->AdvancedSearch->SearchValue != "" || $this->DOCTOR_ID->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Doctor_Name
        if ($this->Doctor_Name->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Doctor_Name->AdvancedSearch->SearchValue != "" || $this->Doctor_Name->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Specialisation
        if ($this->Specialisation->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Specialisation->AdvancedSearch->SearchValue != "" || $this->Specialisation->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // EDUCATION
        if ($this->EDUCATION->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->EDUCATION->AdvancedSearch->SearchValue != "" || $this->EDUCATION->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Doctor_Status
        if ($this->Doctor_Status->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Doctor_Status->AdvancedSearch->SearchValue != "" || $this->Doctor_Status->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // APPOINTMENT_ID
        if ($this->APPOINTMENT_ID->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->APPOINTMENT_ID->AdvancedSearch->SearchValue != "" || $this->APPOINTMENT_ID->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // APPOINTMENT_DATE
        if ($this->APPOINTMENT_DATE->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->APPOINTMENT_DATE->AdvancedSearch->SearchValue != "" || $this->APPOINTMENT_DATE->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Month_Name
        if ($this->Month_Name->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Month_Name->AdvancedSearch->SearchValue != "" || $this->Month_Name->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Month_Number
        if ($this->Month_Number->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Month_Number->AdvancedSearch->SearchValue != "" || $this->Month_Number->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Year
        if ($this->Year->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Year->AdvancedSearch->SearchValue != "" || $this->Year->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Appointment_Status
        if ($this->Appointment_Status->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Appointment_Status->AdvancedSearch->SearchValue != "" || $this->Appointment_Status->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Total_Patients
        if ($this->Total_Patients->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Total_Patients->AdvancedSearch->SearchValue != "" || $this->Total_Patients->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }

        // Avg_Rating
        if ($this->Avg_Rating->AdvancedSearch->get()) {
            $hasValue = true;
            if (($this->Avg_Rating->AdvancedSearch->SearchValue != "" || $this->Avg_Rating->AdvancedSearch->SearchValue2 != "") && $this->Command == "") {
                $this->Command = "search";
            }
        }
        return $hasValue;
    }

    /**
     * Load entities
     *
     * @param int $offset Offset
     * @param int $rowcnt Maximum number of rows
     * @return array of entity / array
     */
    public function loadRecords(int $offset = -1, int $rowcnt = -1): array
    {
        // Load List page SQL (QueryBuilder)
        $sql = $this->getListSql();

        // Load result set
        if ($offset > -1) {
            $sql->setFirstResult($offset);
        }
        if ($rowcnt > 0) {
            $sql->setMaxResults($rowcnt);
        }
        $entities = $this->loadEntities($sql);

        // Set total number of records
        if (property_exists($this, "TotalRecords") && $rowcnt < 0) {
            $this->TotalRecords = count($entities);
        }

        // Call Records Selected event
        $this->recordsSelected($entities);
        return $entities;
    }

    /**
     * Load row based on key values
     *
     * @return bool
     */
    public function loadRow(): bool
    {
        $result = $this->CurrentRecord !== null;
        if ($result) {
            $this->loadRowValues($this->CurrentRecord); // Load row values
        }
        return $result;
    }

    /**
     * Load row values from result set or record
     *
     * @param ?BaseEntity $row Record
     * @return void
     */
    public function loadRowValues(?BaseEntity $row = null): void
    {
        if ($row instanceof BaseEntity) { // Get array from entity
        }
        $row ??= $this->newRow();

        // Call Row Selected event
        $this->rowSelected($row);
        $this->DOCTOR_ID->setDbValue($row['DOCTOR_ID']);
        $this->Doctor_Name->setDbValue($row['Doctor_Name']);
        $this->Specialisation->setDbValue($row['Specialisation']);
        $this->EDUCATION->setDbValue($row['EDUCATION']);
        $this->Doctor_Status->setDbValue($row['Doctor_Status']);
        $this->APPOINTMENT_ID->setDbValue($row['APPOINTMENT_ID']);
        $this->APPOINTMENT_DATE->setDbValue($row['APPOINTMENT_DATE']);
        $this->Month_Name->setDbValue($row['Month_Name']);
        $this->Month_Number->setDbValue($row['Month_Number']);
        $this->Year->setDbValue($row['Year']);
        $this->Appointment_Status->setDbValue($row['Appointment_Status']);
        $this->Total_Patients->setDbValue($row['Total_Patients']);
        $this->Avg_Rating->setDbValue($row['Avg_Rating']);
    }

    /**
     * Return a row with default values
     *
     * @return BaseEntity
     */
    protected function newRow(): BaseEntity
    {
        $row = new $this->EntityClass();
        if (!IsEmpty($this->DOCTOR_ID->DefaultValue)) {
            $row['DOCTOR_ID'] = intval($this->DOCTOR_ID->DefaultValue);
        }
        if (!IsEmpty($this->Doctor_Name->DefaultValue)) {
            $row['Doctor_Name'] = strval($this->Doctor_Name->DefaultValue);
        }
        if (!IsEmpty($this->Specialisation->DefaultValue)) {
            $row['Specialisation'] = strval($this->Specialisation->DefaultValue);
        }
        if (!IsEmpty($this->EDUCATION->DefaultValue)) {
            $row['EDUCATION'] = strval($this->EDUCATION->DefaultValue);
        }
        if (!IsEmpty($this->Doctor_Status->DefaultValue)) {
            $row['Doctor_Status'] = strval($this->Doctor_Status->DefaultValue);
        }
        if (!IsEmpty($this->APPOINTMENT_ID->DefaultValue)) {
            $row['APPOINTMENT_ID'] = intval($this->APPOINTMENT_ID->DefaultValue);
        }
        if (!IsEmpty($this->APPOINTMENT_DATE->DefaultValue)) {
            $row['APPOINTMENT_DATE'] = $this->APPOINTMENT_DATE->DefaultValue instanceof DateTimeInterface ? $this->APPOINTMENT_DATE->DefaultValue : new DateTimeImmutable($this->APPOINTMENT_DATE->DefaultValue);
        }
        if (!IsEmpty($this->Month_Name->DefaultValue)) {
            $row['Month_Name'] = strval($this->Month_Name->DefaultValue);
        }
        if (!IsEmpty($this->Month_Number->DefaultValue)) {
            $row['Month_Number'] = intval($this->Month_Number->DefaultValue);
        }
        if (!IsEmpty($this->Year->DefaultValue)) {
            $row['Year'] = intval($this->Year->DefaultValue);
        }
        if (!IsEmpty($this->Appointment_Status->DefaultValue)) {
            $row['Appointment_Status'] = strval($this->Appointment_Status->DefaultValue);
        }
        if (!IsEmpty($this->Total_Patients->DefaultValue)) {
            $row['Total_Patients'] = strval($this->Total_Patients->DefaultValue);
        }
        if (!IsEmpty($this->Avg_Rating->DefaultValue)) {
            $row['Avg_Rating'] = strval($this->Avg_Rating->DefaultValue);
        }
        return $row;
    }

    // Load old record
    protected function loadOldRecord(): ?object
    {
        if ($this->CurrentRecord !== null) {
            $this->loadRowValues($this->CurrentRecord);
            return $this->CurrentRecord;
        }
        $this->loadRowValues(); // Load default row values
        return null;
    }

    /**
     * Render row
     *
     * @param RowType $rowType Row type
     * @param bool $resetAttributes Reset attributes
     * @return void
     */
    public function renderRow(RowType $rowType = RowType::VIEW, bool $resetAttributes = true): void
    {
        global $httpContext;

        // Set up row type
        $this->RowType = $rowType;

        // Reset attributes
        if ($resetAttributes) {
            $this->resetAttributes();
        }

        // Initialize URLs
        $this->ViewUrl = $this->getViewUrl();
        $this->EditUrl = $this->getEditUrl();
        $this->InlineEditUrl = $this->getInlineEditUrl();
        $this->CopyUrl = $this->getCopyUrl();
        $this->InlineCopyUrl = $this->getInlineCopyUrl();
        $this->DeleteUrl = $this->getDeleteUrl();

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // DOCTOR_ID

        // Doctor_Name

        // Specialisation

        // EDUCATION

        // Doctor_Status

        // APPOINTMENT_ID

        // APPOINTMENT_DATE

        // Month_Name

        // Month_Number

        // Year

        // Appointment_Status

        // Total_Patients

        // Avg_Rating

        // View row
        if ($this->RowType == RowType::VIEW) {
            // DOCTOR_ID
            $this->DOCTOR_ID->ViewValue = $this->DOCTOR_ID->CurrentValue;

            // Doctor_Name
            $this->Doctor_Name->ViewValue = $this->Doctor_Name->CurrentValue;

            // Specialisation
            $this->Specialisation->ViewValue = $this->Specialisation->CurrentValue;

            // EDUCATION
            $this->EDUCATION->ViewValue = $this->EDUCATION->CurrentValue;

            // Doctor_Status
            if (strval($this->Doctor_Status->CurrentValue) != "") {
                $this->Doctor_Status->ViewValue = $this->Doctor_Status->optionCaption($this->Doctor_Status->CurrentValue);
            } else {
                $this->Doctor_Status->ViewValue = null;
            }

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->ViewValue = $this->APPOINTMENT_ID->CurrentValue;

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->ViewValue = $this->APPOINTMENT_DATE->CurrentValue;
            $this->APPOINTMENT_DATE->ViewValue = FormatDateTime($this->APPOINTMENT_DATE->ViewValue, $this->APPOINTMENT_DATE->formatPattern());

            // Month_Name
            $this->Month_Name->ViewValue = $this->Month_Name->CurrentValue;

            // Month_Number
            $this->Month_Number->ViewValue = $this->Month_Number->CurrentValue;
            $this->Month_Number->ViewValue = FormatNumber($this->Month_Number->ViewValue, $this->Month_Number->formatPattern());

            // Year
            $this->Year->ViewValue = $this->Year->CurrentValue;
            $this->Year->ViewValue = FormatNumber($this->Year->ViewValue, $this->Year->formatPattern());

            // Appointment_Status
            if (strval($this->Appointment_Status->CurrentValue) != "") {
                $this->Appointment_Status->ViewValue = $this->Appointment_Status->optionCaption($this->Appointment_Status->CurrentValue);
            } else {
                $this->Appointment_Status->ViewValue = null;
            }

            // Total_Patients
            $this->Total_Patients->ViewValue = $this->Total_Patients->CurrentValue;
            $this->Total_Patients->ViewValue = FormatNumber($this->Total_Patients->ViewValue, $this->Total_Patients->formatPattern());

            // Avg_Rating
            $this->Avg_Rating->ViewValue = $this->Avg_Rating->CurrentValue;
            $this->Avg_Rating->ViewValue = FormatNumber($this->Avg_Rating->ViewValue, $this->Avg_Rating->formatPattern());

            // DOCTOR_ID
            $this->DOCTOR_ID->HrefValue = "";
            $this->DOCTOR_ID->TooltipValue = "";

            // Doctor_Name
            $this->Doctor_Name->HrefValue = "";
            $this->Doctor_Name->TooltipValue = "";

            // Specialisation
            $this->Specialisation->HrefValue = "";
            $this->Specialisation->TooltipValue = "";

            // EDUCATION
            $this->EDUCATION->HrefValue = "";
            $this->EDUCATION->TooltipValue = "";

            // Doctor_Status
            $this->Doctor_Status->HrefValue = "";
            $this->Doctor_Status->TooltipValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";
            $this->APPOINTMENT_ID->TooltipValue = "";

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->HrefValue = "";
            $this->APPOINTMENT_DATE->TooltipValue = "";

            // Month_Name
            $this->Month_Name->HrefValue = "";
            $this->Month_Name->TooltipValue = "";

            // Month_Number
            $this->Month_Number->HrefValue = "";
            $this->Month_Number->TooltipValue = "";

            // Year
            $this->Year->HrefValue = "";
            $this->Year->TooltipValue = "";

            // Appointment_Status
            $this->Appointment_Status->HrefValue = "";
            $this->Appointment_Status->TooltipValue = "";

            // Total_Patients
            $this->Total_Patients->HrefValue = "";
            $this->Total_Patients->TooltipValue = "";

            // Avg_Rating
            $this->Avg_Rating->HrefValue = "";
            $this->Avg_Rating->TooltipValue = "";
        } elseif ($this->RowType == RowType::SEARCH) {
            // DOCTOR_ID
            $this->DOCTOR_ID->setupEditAttributes();
            $this->DOCTOR_ID->EditValue = $this->DOCTOR_ID->AdvancedSearch->SearchValue;
            $this->DOCTOR_ID->PlaceHolder = RemoveHtml($this->DOCTOR_ID->caption());

            // Doctor_Name
            if ($this->Doctor_Name->UseFilter && !IsEmpty($this->Doctor_Name->AdvancedSearch->SearchValue)) {
                if (is_array($this->Doctor_Name->AdvancedSearch->SearchValue)) {
                    $this->Doctor_Name->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Doctor_Name->AdvancedSearch->SearchValue);
                }
                $this->Doctor_Name->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Doctor_Name->AdvancedSearch->SearchValue);
            }

            // Specialisation
            if ($this->Specialisation->UseFilter && !IsEmpty($this->Specialisation->AdvancedSearch->SearchValue)) {
                if (is_array($this->Specialisation->AdvancedSearch->SearchValue)) {
                    $this->Specialisation->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Specialisation->AdvancedSearch->SearchValue);
                }
                $this->Specialisation->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Specialisation->AdvancedSearch->SearchValue);
            }

            // EDUCATION
            $this->EDUCATION->setupEditAttributes();
            $this->EDUCATION->EditValue = !$this->EDUCATION->Raw ? HtmlDecode($this->EDUCATION->AdvancedSearch->SearchValue) : $this->EDUCATION->AdvancedSearch->SearchValue;
            $this->EDUCATION->PlaceHolder = RemoveHtml($this->EDUCATION->caption());

            // Doctor_Status
            if ($this->Doctor_Status->UseFilter && !IsEmpty($this->Doctor_Status->AdvancedSearch->SearchValue)) {
                if (is_array($this->Doctor_Status->AdvancedSearch->SearchValue)) {
                    $this->Doctor_Status->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Doctor_Status->AdvancedSearch->SearchValue);
                }
                $this->Doctor_Status->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Doctor_Status->AdvancedSearch->SearchValue);
            }

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->setupEditAttributes();
            $this->APPOINTMENT_ID->EditValue = $this->APPOINTMENT_ID->AdvancedSearch->SearchValue;
            $this->APPOINTMENT_ID->PlaceHolder = RemoveHtml($this->APPOINTMENT_ID->caption());

            // APPOINTMENT_DATE
            if ($this->APPOINTMENT_DATE->UseFilter && !IsEmpty($this->APPOINTMENT_DATE->AdvancedSearch->SearchValue)) {
                if (is_array($this->APPOINTMENT_DATE->AdvancedSearch->SearchValue)) {
                    $this->APPOINTMENT_DATE->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->APPOINTMENT_DATE->AdvancedSearch->SearchValue);
                }
                $this->APPOINTMENT_DATE->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->APPOINTMENT_DATE->AdvancedSearch->SearchValue);
            }

            // Month_Name
            if ($this->Month_Name->UseFilter && !IsEmpty($this->Month_Name->AdvancedSearch->SearchValue)) {
                if (is_array($this->Month_Name->AdvancedSearch->SearchValue)) {
                    $this->Month_Name->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Month_Name->AdvancedSearch->SearchValue);
                }
                $this->Month_Name->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Month_Name->AdvancedSearch->SearchValue);
            }

            // Month_Number
            $this->Month_Number->setupEditAttributes();
            $this->Month_Number->EditValue = $this->Month_Number->AdvancedSearch->SearchValue;
            $this->Month_Number->PlaceHolder = RemoveHtml($this->Month_Number->caption());

            // Year
            if ($this->Year->UseFilter && !IsEmpty($this->Year->AdvancedSearch->SearchValue)) {
                if (is_array($this->Year->AdvancedSearch->SearchValue)) {
                    $this->Year->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Year->AdvancedSearch->SearchValue);
                }
                $this->Year->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Year->AdvancedSearch->SearchValue);
            }

            // Appointment_Status
            if ($this->Appointment_Status->UseFilter && !IsEmpty($this->Appointment_Status->AdvancedSearch->SearchValue)) {
                if (is_array($this->Appointment_Status->AdvancedSearch->SearchValue)) {
                    $this->Appointment_Status->AdvancedSearch->SearchValue = implode(Config("FILTER_OPTION_SEPARATOR"), $this->Appointment_Status->AdvancedSearch->SearchValue);
                }
                $this->Appointment_Status->EditValue = explode(Config("FILTER_OPTION_SEPARATOR"), $this->Appointment_Status->AdvancedSearch->SearchValue);
            }

            // Total_Patients
            $this->Total_Patients->setupEditAttributes();
            $this->Total_Patients->EditValue = $this->Total_Patients->AdvancedSearch->SearchValue;
            $this->Total_Patients->PlaceHolder = RemoveHtml($this->Total_Patients->caption());

            // Avg_Rating
            $this->Avg_Rating->setupEditAttributes();
            $this->Avg_Rating->EditValue = $this->Avg_Rating->AdvancedSearch->SearchValue;
            $this->Avg_Rating->PlaceHolder = RemoveHtml($this->Avg_Rating->caption());
        }

        // Call Row Rendered event
        if ($this->RowType != RowType::AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate search
    protected function validateSearch(): bool
    {
        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }

        // Return validate result
        $validateSearch = !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateSearch = $validateSearch && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateSearch;
    }

    // Load advanced search
    public function loadAdvancedSearch(): void
    {
        $this->DOCTOR_ID->AdvancedSearch->load();
        $this->Doctor_Name->AdvancedSearch->load();
        $this->Specialisation->AdvancedSearch->load();
        $this->EDUCATION->AdvancedSearch->load();
        $this->Doctor_Status->AdvancedSearch->load();
        $this->APPOINTMENT_ID->AdvancedSearch->load();
        $this->APPOINTMENT_DATE->AdvancedSearch->load();
        $this->Month_Name->AdvancedSearch->load();
        $this->Month_Number->AdvancedSearch->load();
        $this->Year->AdvancedSearch->load();
        $this->Appointment_Status->AdvancedSearch->load();
        $this->Total_Patients->AdvancedSearch->load();
        $this->Avg_Rating->AdvancedSearch->load();
    }

    // Set up search options
    protected function setupSearchOptions(): void
    {
        $pageUrl = $this->pageUrl(false);
        $this->SearchOptions = new ListOptions(TagClassName: "ew-search-option");

        // Search button
        $item = $this->SearchOptions->add("searchtoggle");
        $searchToggleClass = ($this->SearchWhere != "") ? " active" : " active";
        $item->Body = "<a class=\"btn btn-default ew-search-toggle" . $searchToggleClass . "\" role=\"button\" title=\"" . $this->language->phrase("SearchPanel") . "\" data-caption=\"" . $this->language->phrase("SearchPanel") . "\" data-ew-action=\"search-toggle\" data-form=\"fview_doctor_reportsrch\" aria-pressed=\"" . ($searchToggleClass == " active" ? "true" : "false") . "\">" . $this->language->phrase("SearchLink") . "</a>";
        $item->Visible = true;

        // Show all button
        $item = $this->SearchOptions->add("showall");
        $resetUrl = BuildUrl(GetUrl($pageUrl), "cmd=reset");
        if ($this->UseCustomTemplate || !$this->UseAjaxActions) {
            $item->Body = "<a class=\"btn btn-default ew-show-all\" role=\"button\" title=\"" . $this->language->phrase("ShowAll") . "\" data-caption=\"" . $this->language->phrase("ShowAll") . "\" href=\"" . $resetUrl . "\">" . $this->language->phrase("ShowAllBtn") . "</a>";
        } else {
            $item->Body = "<a class=\"btn btn-default ew-show-all\" role=\"button\" title=\"" . $this->language->phrase("ShowAll") . "\" data-caption=\"" . $this->language->phrase("ShowAll") . "\" data-ew-action=\"refresh\" data-url=\"" . $resetUrl . "\">" . $this->language->phrase("ShowAllBtn") . "</a>";
        }
        $item->Visible = ($this->SearchWhere != $this->DefaultSearchWhere && $this->SearchWhere != "0=101");

        // Advanced search button
        $item = $this->SearchOptions->add("advancedsearch");
        if ($this->ModalSearch && !IsMobile()) {
            $item->Body = "<a class=\"btn btn-default ew-advanced-search\" title=\"" . $this->language->phrase("AdvancedSearch", true) . "\" data-table=\"view_doctor_report\" data-caption=\"" . $this->language->phrase("AdvancedSearch", true) . "\" data-ew-action=\"modal\" data-url=\"ViewDoctorReportSearch\" data-btn=\"SearchBtn\">" . $this->language->phrase("AdvancedSearch", false) . "</a>";
        } else {
            $item->Body = "<a class=\"btn btn-default ew-advanced-search\" title=\"" . $this->language->phrase("AdvancedSearch", true) . "\" data-caption=\"" . $this->language->phrase("AdvancedSearch", true) . "\" href=\"ViewDoctorReportSearch\">" . $this->language->phrase("AdvancedSearch", false) . "</a>";
        }
        $item->Visible = true;

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
        return true;
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
        $breadcrumb->add("list", $this->TableVar, $url, "", $this->TableVar, true);
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
                case "x_Doctor_Status":
                    break;
                case "x_Appointment_Status":
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

    // Set up starting record parameters
    public function setupStartRecord(): void
    {
        $infiniteScroll = false;

        // Set up StartRecord
        $pagerTable = Get(Config("TABLE_PAGER_TABLE_NAME"));
        if ($pagerTable && $pagerTable != $this->TableVar) { // Skip if not paging for this table
            $this->StartRecord = $this->getStartRecordNumber();
        } else { // Set up from query string parameter
            $pageNumber = GetInt(Config("TABLE_PAGE_NUMBER"));
            $startRec = GetInt(Config("TABLE_START_REC"));
            $infiniteScroll = IsInfiniteScroll();
            if ($pageNumber > 0) { // Check for "page" parameter first
                $this->PageNumber = $pageNumber;
                $this->StartRecord = ($this->PageNumber - 1) * $this->DisplayRecords + 1;
                if ($this->StartRecord <= 0) {
                    $this->StartRecord = 1;
                }
            } elseif ($startRec > 0) { // Check for "start" parameter
                $this->StartRecord = $startRec;
            } elseif (!$infiniteScroll) {
                $this->StartRecord = $this->getStartRecordNumber();
            }
        }

        // Check if correct start record counter
        if (!is_numeric($this->StartRecord) || intval($this->StartRecord) <= 0) { // Avoid invalid start record counter
            $this->StartRecord = 1; // Reset start record counter
        } elseif (($this->StartRecord - 1) % $this->DisplayRecords != 0) {
            $this->StartRecord = (int)(($this->StartRecord - 1) / $this->DisplayRecords) * $this->DisplayRecords + 1; // Point to page boundary
        }
        if (!$infiniteScroll) {
            $this->setStartRecordNumber($this->StartRecord);
        }
    }

    // Get page count
    public function pageCount(): int
    {
        return ceil($this->TotalRecords / $this->DisplayRecords);
    }

    // Parse query builder rule
    protected function parseRules(?array $group, string $fieldName = "", string $itemName = ""): string
    {
        if ($group === null) {
            return "";
        }
        $group["condition"] ??= "AND";
        if (!in_array($group["condition"], ["AND", "OR"])) {
            throw new Exception("Unable to build SQL query with condition '" . $group["condition"] . "'");
        }
        if (!is_array($group["rules"] ?? null)) {
            return "";
        }
        $parts = [];
        foreach ($group["rules"] as $rule) {
            if (is_array($rule["rules"] ?? null) && count($rule["rules"]) > 0) {
                $part = $this->parseRules($rule, $fieldName, $itemName);
                if ($part) {
                    $parts[] = "(" . " " . $part . " " . ")" . " ";
                }
            } else {
                $field = $rule["field"];
                $fld = $this->fieldByParam($field);
                $dbid = $this->Dbid;
                if ($fld instanceof ReportField && is_array($fld->DashboardSearchSourceFields)) {
                    $item = $fld->DashboardSearchSourceFields[$itemName] ?? null;
                    if ($item) {
                        $tbl = Container($item["table"]);
                        $dbid = $tbl->Dbid;
                        $fld = $tbl->Fields[$item["field"]];
                    } else {
                        $fld = null;
                    }
                }
                if ($fld && ($fieldName == "" || $fld->Name == $fieldName)) { // Field name not specified or matched field name
                    $fldOpr = array_search($rule["operator"], Config("CLIENT_SEARCH_OPERATORS"));
                    $ope = Config("QUERY_BUILDER_OPERATORS")[$rule["operator"]] ?? null;
                    if (!$ope || !$fldOpr) {
                        throw new Exception("Unknown SQL operation for operator '" . $rule["operator"] . "'");
                    }
                    if ($ope["nb_inputs"] > 0 && isset($rule["value"]) && !IsEmpty($rule["value"]) || IsNullOrEmptyOperator($fldOpr)) {
                        $fldVal = $rule["value"] ?? "";
                        if (is_array($fldVal)) {
                            $fldVal = $fld->isMultiSelect() ? implode(Config("MULTIPLE_OPTION_SEPARATOR"), $fldVal) : $fldVal[0];
                        }
                        $useFilter = $fld->UseFilter; // Query builder does not use filter
                        try {
                            if ($fld instanceof ReportField) { // Search report fields
                                if ($fld->SearchType == "dropdown") {
                                    if (is_array($fldVal)) {
                                        $sql = "";
                                        foreach ($fldVal as $val) {
                                            AddFilter($sql, DropDownFilter($fld, $val, $fldOpr, $dbid), "OR");
                                        }
                                        $parts[] = $sql;
                                    } else {
                                        $parts[] = DropDownFilter($fld, $fldVal, $fldOpr, $dbid);
                                    }
                                } else {
                                    $fld->AdvancedSearch->SearchOperator = $fldOpr;
                                    $fld->AdvancedSearch->SearchValue = $fldVal;
                                    $parts[] = GetReportFilter($fld, false, $dbid);
                                }
                            } else { // Search normal fields
                                if ($fld->isMultiSelect()) {
                                    $fld->AdvancedSearch->SearchValue = ConvertSearchValue($fldVal, $fldOpr, $fld);
                                    $parts[] = $fldVal != "" ? GetMultiSearchSql($fld, $fldOpr, $fld->AdvancedSearch->SearchValue, $this->Dbid) : "";
                                } else {
                                    $fldVal2 = ContainsString($fldOpr, "BETWEEN") ? $rule["value"][1] : ""; // BETWEEN
                                    if (is_array($fldVal2)) {
                                        $fldVal2 = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $fldVal2);
                                    }
                                    $fld->AdvancedSearch->SearchValue = ConvertSearchValue($fldVal, $fldOpr, $fld);
                                    $fld->AdvancedSearch->SearchValue2 = ConvertSearchValue($fldVal2, $fldOpr, $fld);
                                    $parts[] = GetSearchSql(
                                        $fld,
                                        $fld->AdvancedSearch->SearchValue, // SearchValue
                                        $fldOpr,
                                        "", // $fldCond not used
                                        $fld->AdvancedSearch->SearchValue2, // SearchValue2
                                        "", // $fldOpr2 not used
                                        $this->Dbid
                                    );
                                }
                            }
                        } finally {
                            $fld->UseFilter = $useFilter;
                        }
                    }
                }
            }
        }
        $where = "";
        foreach ($parts as $part) {
            AddFilter($where, $part, $group["condition"]);
        }
        if ($where && ($group["not"] ?? false)) {
            $where = "NOT (" . $where . ")";
        }
        return $where;
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

    /**
     * Get filter (Note: following properties are set up)
     * - UserIDFilter => User ID filter
     * - SearchCommand => Search command
     * - SearchWhere => Search filter
     * - Table / Field level search object / session variables
     *
     * @return string Filter
     */
    public function getFilter(): string
    {
        global $httpContext;

        // Search filters
        $filter = "";
        $srchAdvanced = ""; // Advanced search filter
        $srchBasic = ""; // Basic search filter
        $query = ""; // Query builder

        // Set up Dashboard Filter
        if ($httpContext["DashboardReport"]) {
            AddFilter($filter, $this->getDashboardFilter($httpContext["DashboardReport"], $this->TableVar));
        }

        // Get default search criteria
        AddFilter($this->DefaultSearchWhere, $this->basicSearchWhere(true));
        AddFilter($this->DefaultSearchWhere, $this->advancedSearchWhere(true));

        // Get basic search values
        if ($this->loadBasicSearchValues()) {
            $this->setSessionRules(""); // Clear rules for QueryBuilder
        }

        // Get and validate search values for advanced search
        $isAdvancedSearch = false;
        if (IsEmpty(Post("action"))) { // Skip if user action
            $isAdvancedSearch = $this->loadSearchValues();
        }

        // Restore filter list
        $this->restoreFilterList();

        // Clear rules for QueryBuilder
        if ($this->validateSearch() && $isAdvancedSearch) {
            $this->setSessionRules("");
        }

        // Restore search parms from Session if not searching / reset / export
        if (($this->isExport() || $this->Command != "search" && $this->Command != "reset" && $this->Command != "resetall") && $this->Command != "json" && $this->checkSearchParms()) {
            $this->restoreSearchParms();
        }

        // Call Records SearchValidated event
        $this->recordsSearchValidated();

        // Get basic search criteria
        if (!$this->hasInvalidFields()) {
            $srchBasic = $this->basicSearchWhere();
        }

        // Get advanced search criteria
        if (!$this->hasInvalidFields()) {
            $srchAdvanced = $this->advancedSearchWhere();
        }

        // Get query builder criteria
        $query = $httpContext["DashboardReport"] ? "" : $this->queryBuilderWhere();

        // Load search default if no existing search criteria
        if (!$this->checkSearchParms() && !$query) {
            // Load basic search from default
            $this->BasicSearch->loadDefault();
            if ($this->BasicSearch->Keyword != "") {
                $srchBasic = $this->basicSearchWhere(); // Save to session
            }

            // Load advanced search from default
            if ($this->loadAdvancedSearchDefault()) {
                $srchAdvanced = $this->advancedSearchWhere(); // Save to session
            }
        }

        // Restore search settings from Session
        if (!$this->hasInvalidFields()) {
            $this->loadAdvancedSearch();
        }

        // Build search criteria
        if ($query) {
            AddFilter($this->SearchWhere, $query);
            $this->SearchByQueryBuilder = true;
        } else {
            AddFilter($this->SearchWhere, $srchAdvanced);
            AddFilter($this->SearchWhere, $srchBasic);
        }

        // Call Records_Searching event
        $this->recordsSearching($this->SearchWhere);

        // Save search criteria
        if ($this->Command == "search" && !$this->RestoreSearch) {
            $this->setSearchWhere($this->SearchWhere); // Save to Session
            $this->StartRecord = 1; // Reset start record counter
            $this->setStartRecordNumber($this->StartRecord);
        } elseif ($this->Command != "json" && !$query) {
            $this->SearchWhere = $this->getSearchWhere();
        }

        // Add search filter
        AddFilter($filter, $this->SearchWhere);

        // Set up User ID filter
        $this->UserIDFilter = $this->applyUserIDFilters();
        AddFilter($filter, $this->UserIDFilter);

        // Set up filter
        if ($this->Command == "json") {
            $this->UseSessionForListSql = false; // Do not use session for ListSQL
            $this->CurrentFilter = $filter;
        } else {
            $this->setSessionWhere($filter);
            $this->CurrentFilter = "";
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

    // Form Custom Validate event
    public function formCustomValidate(string &$customError): bool
    {
        // Return error message in $customError
        return true;
    }

    // ListOptions Load event
    public function listOptionsLoad(): void
    {
        // Example:
        //$opt = $this->ListOptions->add("new");
        //$opt->Header = "xxx";
        //$opt->OnLeft = true; // Link on left
        //$opt->moveTo(0); // Move to first column
    }

    // ListOptions Rendering event
    public function listOptionsRendering(): void
    {
        //Container("DetailTableGrid")->DetailAdd = (...condition...); // Set to true or false conditionally
        //Container("DetailTableGrid")->DetailEdit = (...condition...); // Set to true or false conditionally
        //Container("DetailTableGrid")->DetailView = (...condition...); // Set to true or false conditionally
    }

    // ListOptions Rendered event
    public function listOptionsRendered(): void
    {
        // Example:
        //$this->ListOptions["new"]->Body = "xxx";
    }

    // Row Custom Action event
    public function rowCustomAction(string $action, BaseEntity $row): bool
    {
        // Return false to abort
        return true;
    }

    // Page Exporting event
    // $doc = export object
    public function pageExporting(object &$doc): bool
    {
        //$doc->Text = "my header"; // Export header
        //return false; // Return false to skip default export and use Row_Export event
        return true; // Return true to use default export and skip Row_Export event
    }

    // Row Export event
    // $doc = export document object
    public function rowExport(object $doc, BaseEntity $row): void
    {
        //$doc->Text .= "my content"; // Build HTML with field value: $row["MyField"] or $this->MyField->ViewValue
    }

    // Page Exported event
    // $doc = export document object
    public function pageExported(object $doc): void
    {
        //$doc->Text .= "my footer"; // Export footer
        //Log($doc->Text);
    }

    // Page Importing event
    public function pageImporting(object &$builder, array &$options): bool
    {
        //var_dump($options); // Show all options for importing
        //$builder = fn($workflow) => $workflow->addStep($myStep);
        //return false; // Return false to skip import
        return true;
    }

    // Row Import event
    public function rowImport(array &$row, int $count): ?bool
    {
        //Log($count); // Import record count
        //var_dump($row); // Import row
        //return null; // Return null to skip import
        //return false; // Return false to indicate import failure
        return true;
    }

    // Page Imported event
    public function pageImported(object $object, array $results): void
    {
        //var_dump($object); // Workflow result object
        //var_dump($results); // Import results
    }
}

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
#[AsAlias("ViewDoctorReportSearch", true)]
class ViewDoctorReportSearch extends ViewDoctorReport implements PageInterface
{
    use MessagesTrait;
    use FormTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "search";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // CSS class/style
    public string $CurrentPageName = "ViewDoctorReportSearch"; // Route action

    // Page headings
    public string $Heading = "";
    public string $Subheading = "";
    public string $PageHeader = "";
    public string $PageFooter = "";

    // Page layout
    public bool $UseLayout = true;

    // Page terminated
    private bool $terminated = false;
    public string $FormClassName = "ew-form ew-search-form";
    public bool $IsModal = false;
    public bool $IsMobileOrModal = false;

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
        $this->TableClass = "table table-striped table-bordered table-hover table-sm ew-desktop-table ew-search-table";

        // Initialize
        $httpContext["Page"] = $this;

        // Open connection
        $httpContext["Conn"] ??= $this->getConnection();
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
     * Page run
     *
     * @return void
     */
    public function run(): void
    {
        global $httpContext;

        // Is modal
        $this->IsModal = IsModal();
        $this->UseLayout = $this->UseLayout && !$this->IsModal;

        // Use layout
        $this->UseLayout = $this->UseLayout && ParamBool(Config("PAGE_LAYOUT"), true);

        // View
        $this->View = Get(Config("VIEW"));
        $this->CurrentAction = Param("action"); // Set up current action
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

        // Set up lookup cache
        $this->setupLookupOptions($this->Doctor_Status);
        $this->setupLookupOptions($this->Appointment_Status);

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Check modal
        if ($this->IsModal) {
            $httpContext["SkipHeaderFooter"] = true;
        }
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;

        // Get action
        $this->CurrentAction = Post("action");
        if ($this->isSearch()) {
            // Build search string for advanced search, remove blank field
            $this->loadSearchValues(); // Get search values
            $srchStr = $this->validateSearch() ? $this->buildAdvancedSearch() : "";
            if ($srchStr != "") {
                $srchStr = "ViewDoctorReportList" . "?" . $srchStr;
                // Do not return Json for UseAjaxActions
                if ($this->IsModal && $this->UseAjaxActions) {
                    $this->IsModal = false;
                }
                $this->terminate($srchStr); // Go to list page
                return;
            }
        }

        // Restore search settings from Session
        if (!$this->hasInvalidFields()) {
            $this->loadAdvancedSearch();
        }

        // Render row for search
        $this->renderRow(RowType::SEARCH);

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

    // Build advanced search
    protected function buildAdvancedSearch(): string
    {
        $srchUrl = "";
        $this->buildSearchUrl($srchUrl, $this->DOCTOR_ID); // DOCTOR_ID
        $this->buildSearchUrl($srchUrl, $this->Doctor_Name); // Doctor_Name
        $this->buildSearchUrl($srchUrl, $this->Specialisation); // Specialisation
        $this->buildSearchUrl($srchUrl, $this->EDUCATION); // EDUCATION
        $this->buildSearchUrl($srchUrl, $this->Doctor_Status); // Doctor_Status
        $this->buildSearchUrl($srchUrl, $this->APPOINTMENT_ID); // APPOINTMENT_ID
        $this->buildSearchUrl($srchUrl, $this->APPOINTMENT_DATE); // APPOINTMENT_DATE
        $this->buildSearchUrl($srchUrl, $this->Month_Name); // Month_Name
        $this->buildSearchUrl($srchUrl, $this->Month_Number); // Month_Number
        $this->buildSearchUrl($srchUrl, $this->Year); // Year
        $this->buildSearchUrl($srchUrl, $this->Appointment_Status); // Appointment_Status
        $this->buildSearchUrl($srchUrl, $this->Total_Patients); // Total_Patients
        $this->buildSearchUrl($srchUrl, $this->Avg_Rating); // Avg_Rating
        if ($srchUrl != "") {
            $srchUrl .= "&";
        }
        $srchUrl .= "cmd=search";
        return $srchUrl;
    }

    // Build search URL
    protected function buildSearchUrl(string &$url, DbField $fld, bool $oprOnly = false): void
    {
        $wrk = "";
        $fldParm = $fld->Param;
        [
            "value" => $fldVal,
            "operator" => $fldOpr,
            "condition" => $fldCond,
            "value2" => $fldVal2,
            "operator2" => $fldOpr2
        ] = $this->getSearchValues($fldParm);
        $fldDataType = $fld->DataType;
        $value = ConvertSearchValue($fldVal, $fldOpr, $fld); // For testing if numeric only
        $value2 = ConvertSearchValue($fldVal2, $fldOpr2, $fld); // For testing if numeric only
        $fldOpr = ConvertSearchOperator($fldOpr, $fld, $value);
        $fldOpr2 = ConvertSearchOperator($fldOpr2, $fld, $value2);
        if (in_array($fldOpr, ["BETWEEN", "NOT BETWEEN"])) {
            $isValidValue = $fldDataType != DataType::NUMBER || $fld->VirtualSearch || IsNumericSearchValue($value, $fldOpr, $fld) && IsNumericSearchValue($value2, $fldOpr2, $fld);
            if ($fldVal != "" && $fldVal2 != "" && $isValidValue) {
                $wrk = $this->searchValueUrl($fld, $fldVal, "x_") . "&" . $this->searchValueUrl($fld, $fldVal2, "y_") . "&z_" . $fldParm . "=" . urlencode($fldOpr);
            }
        } else {
            $isValidValue = $fldDataType != DataType::NUMBER || $fld->VirtualSearch || IsNumericSearchValue($value, $fldOpr, $fld);
            if ($fldVal != "" && $isValidValue && IsValidOperator($fldOpr)) {
                $wrk = $this->searchValueUrl($fld, $fldVal, "x_") . "&z_" . $fldParm . "=" . urlencode($fldOpr);
            } elseif (in_array($fldOpr, ["IS NULL", "IS NOT NULL", "IS EMPTY", "IS NOT EMPTY"]) || ($fldOpr != "" && $oprOnly && IsValidOperator($fldOpr))) {
                $wrk = "z_" . $fldParm . "=" . urlencode($fldOpr);
            }
            $isValidValue = $fldDataType != DataType::NUMBER || $fld->VirtualSearch || IsNumericSearchValue($value2, $fldOpr2, $fld);
            if ($fldVal2 != "" && $isValidValue && IsValidOperator($fldOpr2)) {
                if ($wrk != "") {
                    $wrk .= "&v_" . $fldParm . "=" . urlencode($fldCond) . "&";
                }
                $wrk .= $this->searchValueUrl($fld, $fldVal2, "y_") . "&w_" . $fldParm . "=" . urlencode($fldOpr2);
            } elseif (in_array($fldOpr2, ["IS NULL", "IS NOT NULL", "IS EMPTY", "IS NOT EMPTY"]) || ($fldOpr2 != "" && $oprOnly && IsValidOperator($fldOpr2))) {
                if ($wrk != "") {
                    $wrk .= "&v_" . $fldParm . "=" . urlencode($fldCond) . "&";
                }
                $wrk .= "w_" . $fldParm . "=" . urlencode($fldOpr2);
            }
        }
        if ($wrk != "") {
            if ($url != "") {
                $url .= "&";
            }
            $url .= $wrk;
        }
    }

    // Search value URL
    protected function searchValueUrl(DbField $fld, string|array $value, string $prefix): string
    {
        $fldParm = $fld->Param;
        if (is_array($value)) { // Multiple values
            return implode("&", array_map(fn($val) => $prefix . $fldParm . "[]=" . $val, $value));
        } else {
            return $prefix . $fldParm . "=" . urlencode($value);
        }
    }

    // Load search values for validation
    protected function loadSearchValues(): bool
    {
        // Load search values
        $hasValue = false;

        // DOCTOR_ID
        if ($this->DOCTOR_ID->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Doctor_Name
        if ($this->Doctor_Name->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Specialisation
        if ($this->Specialisation->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // EDUCATION
        if ($this->EDUCATION->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Doctor_Status
        if ($this->Doctor_Status->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // APPOINTMENT_ID
        if ($this->APPOINTMENT_ID->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // APPOINTMENT_DATE
        if ($this->APPOINTMENT_DATE->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Month_Name
        if ($this->Month_Name->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Month_Number
        if ($this->Month_Number->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Year
        if ($this->Year->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Appointment_Status
        if ($this->Appointment_Status->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Total_Patients
        if ($this->Total_Patients->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Avg_Rating
        if ($this->Avg_Rating->AdvancedSearch->get()) {
            $hasValue = true;
        }
        return $hasValue;
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

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // DOCTOR_ID
        $this->DOCTOR_ID->RowCssClass = "row";

        // Doctor_Name
        $this->Doctor_Name->RowCssClass = "row";

        // Specialisation
        $this->Specialisation->RowCssClass = "row";

        // EDUCATION
        $this->EDUCATION->RowCssClass = "row";

        // Doctor_Status
        $this->Doctor_Status->RowCssClass = "row";

        // APPOINTMENT_ID
        $this->APPOINTMENT_ID->RowCssClass = "row";

        // APPOINTMENT_DATE
        $this->APPOINTMENT_DATE->RowCssClass = "row";

        // Month_Name
        $this->Month_Name->RowCssClass = "row";

        // Month_Number
        $this->Month_Number->RowCssClass = "row";

        // Year
        $this->Year->RowCssClass = "row";

        // Appointment_Status
        $this->Appointment_Status->RowCssClass = "row";

        // Total_Patients
        $this->Total_Patients->RowCssClass = "row";

        // Avg_Rating
        $this->Avg_Rating->RowCssClass = "row";

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
            $this->Doctor_Name->setupEditAttributes();
            $this->Doctor_Name->EditValue = !$this->Doctor_Name->Raw ? HtmlDecode($this->Doctor_Name->AdvancedSearch->SearchValue) : $this->Doctor_Name->AdvancedSearch->SearchValue;
            $this->Doctor_Name->PlaceHolder = RemoveHtml($this->Doctor_Name->caption());

            // Specialisation
            $this->Specialisation->setupEditAttributes();
            $this->Specialisation->EditValue = !$this->Specialisation->Raw ? HtmlDecode($this->Specialisation->AdvancedSearch->SearchValue) : $this->Specialisation->AdvancedSearch->SearchValue;
            $this->Specialisation->PlaceHolder = RemoveHtml($this->Specialisation->caption());

            // EDUCATION
            $this->EDUCATION->setupEditAttributes();
            $this->EDUCATION->EditValue = !$this->EDUCATION->Raw ? HtmlDecode($this->EDUCATION->AdvancedSearch->SearchValue) : $this->EDUCATION->AdvancedSearch->SearchValue;
            $this->EDUCATION->PlaceHolder = RemoveHtml($this->EDUCATION->caption());

            // Doctor_Status
            $this->Doctor_Status->EditValue = $this->Doctor_Status->options(false);
            $this->Doctor_Status->PlaceHolder = RemoveHtml($this->Doctor_Status->caption());

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->setupEditAttributes();
            $this->APPOINTMENT_ID->EditValue = $this->APPOINTMENT_ID->AdvancedSearch->SearchValue;
            $this->APPOINTMENT_ID->PlaceHolder = RemoveHtml($this->APPOINTMENT_ID->caption());

            // APPOINTMENT_DATE
            $this->APPOINTMENT_DATE->setupEditAttributes();
            $this->APPOINTMENT_DATE->EditValue = FormatDateTime(UnFormatDateTime($this->APPOINTMENT_DATE->AdvancedSearch->SearchValue, $this->APPOINTMENT_DATE->formatPattern()), $this->APPOINTMENT_DATE->formatPattern());
            $this->APPOINTMENT_DATE->PlaceHolder = RemoveHtml($this->APPOINTMENT_DATE->caption());

            // Month_Name
            $this->Month_Name->setupEditAttributes();
            $this->Month_Name->EditValue = !$this->Month_Name->Raw ? HtmlDecode($this->Month_Name->AdvancedSearch->SearchValue) : $this->Month_Name->AdvancedSearch->SearchValue;
            $this->Month_Name->PlaceHolder = RemoveHtml($this->Month_Name->caption());

            // Month_Number
            $this->Month_Number->setupEditAttributes();
            $this->Month_Number->EditValue = $this->Month_Number->AdvancedSearch->SearchValue;
            $this->Month_Number->PlaceHolder = RemoveHtml($this->Month_Number->caption());

            // Year
            $this->Year->setupEditAttributes();
            $this->Year->EditValue = $this->Year->AdvancedSearch->SearchValue;
            $this->Year->PlaceHolder = RemoveHtml($this->Year->caption());

            // Appointment_Status
            $this->Appointment_Status->EditValue = $this->Appointment_Status->options(false);
            $this->Appointment_Status->PlaceHolder = RemoveHtml($this->Appointment_Status->caption());

            // Total_Patients
            $this->Total_Patients->setupEditAttributes();
            $this->Total_Patients->EditValue = $this->Total_Patients->AdvancedSearch->SearchValue;
            $this->Total_Patients->PlaceHolder = RemoveHtml($this->Total_Patients->caption());

            // Avg_Rating
            $this->Avg_Rating->setupEditAttributes();
            $this->Avg_Rating->EditValue = $this->Avg_Rating->AdvancedSearch->SearchValue;
            $this->Avg_Rating->PlaceHolder = RemoveHtml($this->Avg_Rating->caption());
        }
        if ($this->RowType == RowType::ADD || $this->RowType == RowType::EDIT || $this->RowType == RowType::SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
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
        if (!CheckInteger($this->DOCTOR_ID->AdvancedSearch->SearchValue)) {
            $this->DOCTOR_ID->addErrorMessage($this->DOCTOR_ID->getErrorMessage(false));
        }
        if (!CheckInteger($this->APPOINTMENT_ID->AdvancedSearch->SearchValue)) {
            $this->APPOINTMENT_ID->addErrorMessage($this->APPOINTMENT_ID->getErrorMessage(false));
        }
        if (!CheckDate($this->APPOINTMENT_DATE->AdvancedSearch->SearchValue, $this->APPOINTMENT_DATE->formatPattern())) {
            $this->APPOINTMENT_DATE->addErrorMessage($this->APPOINTMENT_DATE->getErrorMessage(false));
        }
        if (!CheckInteger($this->Month_Number->AdvancedSearch->SearchValue)) {
            $this->Month_Number->addErrorMessage($this->Month_Number->getErrorMessage(false));
        }
        if (!CheckInteger($this->Year->AdvancedSearch->SearchValue)) {
            $this->Year->addErrorMessage($this->Year->getErrorMessage(false));
        }
        if (!CheckInteger($this->Total_Patients->AdvancedSearch->SearchValue)) {
            $this->Total_Patients->addErrorMessage($this->Total_Patients->getErrorMessage(false));
        }
        if (!CheckNumber($this->Avg_Rating->AdvancedSearch->SearchValue)) {
            $this->Avg_Rating->addErrorMessage($this->Avg_Rating->getErrorMessage(false));
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

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("ViewDoctorReportList"), "", $this->TableVar, true);
        $pageId = "search";
        $breadcrumb->add("search", $pageId, $url);
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
}

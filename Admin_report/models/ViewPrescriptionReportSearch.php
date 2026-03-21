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
#[AsAlias("ViewPrescriptionReportSearch", true)]
class ViewPrescriptionReportSearch extends ViewPrescriptionReport implements PageInterface
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
    public string $CurrentPageName = "ViewPrescriptionReportSearch"; // Route action

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
        $this->TableVar = 'view_prescription_report';
        $this->TableName = 'view_prescription_report';

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
        $this->PRESCRIPTION_ID->setVisibility();
        $this->ISSUE_DATE->setVisibility();
        $this->Patient_Name->setVisibility();
        $this->Doctor_Name->setVisibility();
        $this->Specialisation->setVisibility();
        $this->SYMPTOMS->setVisibility();
        $this->DIAGNOSIS->setVisibility();
        $this->DIABETES->setVisibility();
        $this->BLOOD_PRESSURE->setVisibility();
        $this->ADDITIONAL_NOTES->setVisibility();
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
                    $result["view"] = SameString($pageName, "ViewPrescriptionReportView"); // If View page, no primary button
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
        $this->setupLookupOptions($this->DIABETES);

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
                $srchStr = "ViewPrescriptionReportList" . "?" . $srchStr;
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
        $this->buildSearchUrl($srchUrl, $this->PRESCRIPTION_ID); // PRESCRIPTION_ID
        $this->buildSearchUrl($srchUrl, $this->ISSUE_DATE); // ISSUE_DATE
        $this->buildSearchUrl($srchUrl, $this->Patient_Name); // Patient_Name
        $this->buildSearchUrl($srchUrl, $this->Doctor_Name); // Doctor_Name
        $this->buildSearchUrl($srchUrl, $this->Specialisation); // Specialisation
        $this->buildSearchUrl($srchUrl, $this->SYMPTOMS); // SYMPTOMS
        $this->buildSearchUrl($srchUrl, $this->DIAGNOSIS); // DIAGNOSIS
        $this->buildSearchUrl($srchUrl, $this->DIABETES); // DIABETES
        $this->buildSearchUrl($srchUrl, $this->BLOOD_PRESSURE); // BLOOD_PRESSURE
        $this->buildSearchUrl($srchUrl, $this->ADDITIONAL_NOTES); // ADDITIONAL_NOTES
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

        // PRESCRIPTION_ID
        if ($this->PRESCRIPTION_ID->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // ISSUE_DATE
        if ($this->ISSUE_DATE->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // Patient_Name
        if ($this->Patient_Name->AdvancedSearch->get()) {
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

        // SYMPTOMS
        if ($this->SYMPTOMS->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // DIAGNOSIS
        if ($this->DIAGNOSIS->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // DIABETES
        if ($this->DIABETES->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // BLOOD_PRESSURE
        if ($this->BLOOD_PRESSURE->AdvancedSearch->get()) {
            $hasValue = true;
        }

        // ADDITIONAL_NOTES
        if ($this->ADDITIONAL_NOTES->AdvancedSearch->get()) {
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

        // PRESCRIPTION_ID
        $this->PRESCRIPTION_ID->RowCssClass = "row";

        // ISSUE_DATE
        $this->ISSUE_DATE->RowCssClass = "row";

        // Patient_Name
        $this->Patient_Name->RowCssClass = "row";

        // Doctor_Name
        $this->Doctor_Name->RowCssClass = "row";

        // Specialisation
        $this->Specialisation->RowCssClass = "row";

        // SYMPTOMS
        $this->SYMPTOMS->RowCssClass = "row";

        // DIAGNOSIS
        $this->DIAGNOSIS->RowCssClass = "row";

        // DIABETES
        $this->DIABETES->RowCssClass = "row";

        // BLOOD_PRESSURE
        $this->BLOOD_PRESSURE->RowCssClass = "row";

        // ADDITIONAL_NOTES
        $this->ADDITIONAL_NOTES->RowCssClass = "row";

        // View row
        if ($this->RowType == RowType::VIEW) {
            // PRESCRIPTION_ID
            $this->PRESCRIPTION_ID->ViewValue = $this->PRESCRIPTION_ID->CurrentValue;

            // ISSUE_DATE
            $this->ISSUE_DATE->ViewValue = $this->ISSUE_DATE->CurrentValue;
            $this->ISSUE_DATE->ViewValue = FormatDateTime($this->ISSUE_DATE->ViewValue, $this->ISSUE_DATE->formatPattern());

            // Patient_Name
            $this->Patient_Name->ViewValue = $this->Patient_Name->CurrentValue;

            // Doctor_Name
            $this->Doctor_Name->ViewValue = $this->Doctor_Name->CurrentValue;

            // Specialisation
            $this->Specialisation->ViewValue = $this->Specialisation->CurrentValue;

            // SYMPTOMS
            $this->SYMPTOMS->ViewValue = $this->SYMPTOMS->CurrentValue;

            // DIAGNOSIS
            $this->DIAGNOSIS->ViewValue = $this->DIAGNOSIS->CurrentValue;

            // DIABETES
            if (strval($this->DIABETES->CurrentValue) != "") {
                $this->DIABETES->ViewValue = $this->DIABETES->optionCaption($this->DIABETES->CurrentValue);
            } else {
                $this->DIABETES->ViewValue = null;
            }

            // BLOOD_PRESSURE
            $this->BLOOD_PRESSURE->ViewValue = $this->BLOOD_PRESSURE->CurrentValue;
            $this->BLOOD_PRESSURE->ViewValue = FormatNumber($this->BLOOD_PRESSURE->ViewValue, $this->BLOOD_PRESSURE->formatPattern());

            // ADDITIONAL_NOTES
            $this->ADDITIONAL_NOTES->ViewValue = $this->ADDITIONAL_NOTES->CurrentValue;

            // PRESCRIPTION_ID
            $this->PRESCRIPTION_ID->HrefValue = "";
            $this->PRESCRIPTION_ID->TooltipValue = "";

            // ISSUE_DATE
            $this->ISSUE_DATE->HrefValue = "";
            $this->ISSUE_DATE->TooltipValue = "";

            // Patient_Name
            $this->Patient_Name->HrefValue = "";
            $this->Patient_Name->TooltipValue = "";

            // Doctor_Name
            $this->Doctor_Name->HrefValue = "";
            $this->Doctor_Name->TooltipValue = "";

            // Specialisation
            $this->Specialisation->HrefValue = "";
            $this->Specialisation->TooltipValue = "";

            // SYMPTOMS
            $this->SYMPTOMS->HrefValue = "";
            $this->SYMPTOMS->TooltipValue = "";

            // DIAGNOSIS
            $this->DIAGNOSIS->HrefValue = "";
            $this->DIAGNOSIS->TooltipValue = "";

            // DIABETES
            $this->DIABETES->HrefValue = "";
            $this->DIABETES->TooltipValue = "";

            // BLOOD_PRESSURE
            $this->BLOOD_PRESSURE->HrefValue = "";
            $this->BLOOD_PRESSURE->TooltipValue = "";

            // ADDITIONAL_NOTES
            $this->ADDITIONAL_NOTES->HrefValue = "";
            $this->ADDITIONAL_NOTES->TooltipValue = "";
        } elseif ($this->RowType == RowType::SEARCH) {
            // PRESCRIPTION_ID
            $this->PRESCRIPTION_ID->setupEditAttributes();
            $this->PRESCRIPTION_ID->EditValue = $this->PRESCRIPTION_ID->AdvancedSearch->SearchValue;
            $this->PRESCRIPTION_ID->PlaceHolder = RemoveHtml($this->PRESCRIPTION_ID->caption());

            // ISSUE_DATE
            $this->ISSUE_DATE->setupEditAttributes();
            $this->ISSUE_DATE->EditValue = FormatDateTime(UnFormatDateTime($this->ISSUE_DATE->AdvancedSearch->SearchValue, $this->ISSUE_DATE->formatPattern()), $this->ISSUE_DATE->formatPattern());
            $this->ISSUE_DATE->PlaceHolder = RemoveHtml($this->ISSUE_DATE->caption());

            // Patient_Name
            $this->Patient_Name->setupEditAttributes();
            $this->Patient_Name->EditValue = !$this->Patient_Name->Raw ? HtmlDecode($this->Patient_Name->AdvancedSearch->SearchValue) : $this->Patient_Name->AdvancedSearch->SearchValue;
            $this->Patient_Name->PlaceHolder = RemoveHtml($this->Patient_Name->caption());

            // Doctor_Name
            $this->Doctor_Name->setupEditAttributes();
            $this->Doctor_Name->EditValue = !$this->Doctor_Name->Raw ? HtmlDecode($this->Doctor_Name->AdvancedSearch->SearchValue) : $this->Doctor_Name->AdvancedSearch->SearchValue;
            $this->Doctor_Name->PlaceHolder = RemoveHtml($this->Doctor_Name->caption());

            // Specialisation
            $this->Specialisation->setupEditAttributes();
            $this->Specialisation->EditValue = !$this->Specialisation->Raw ? HtmlDecode($this->Specialisation->AdvancedSearch->SearchValue) : $this->Specialisation->AdvancedSearch->SearchValue;
            $this->Specialisation->PlaceHolder = RemoveHtml($this->Specialisation->caption());

            // SYMPTOMS
            $this->SYMPTOMS->setupEditAttributes();
            $this->SYMPTOMS->EditValue = !$this->SYMPTOMS->Raw ? HtmlDecode($this->SYMPTOMS->AdvancedSearch->SearchValue) : $this->SYMPTOMS->AdvancedSearch->SearchValue;
            $this->SYMPTOMS->PlaceHolder = RemoveHtml($this->SYMPTOMS->caption());

            // DIAGNOSIS
            $this->DIAGNOSIS->setupEditAttributes();
            $this->DIAGNOSIS->EditValue = !$this->DIAGNOSIS->Raw ? HtmlDecode($this->DIAGNOSIS->AdvancedSearch->SearchValue) : $this->DIAGNOSIS->AdvancedSearch->SearchValue;
            $this->DIAGNOSIS->PlaceHolder = RemoveHtml($this->DIAGNOSIS->caption());

            // DIABETES
            $this->DIABETES->EditValue = $this->DIABETES->options(false);
            $this->DIABETES->PlaceHolder = RemoveHtml($this->DIABETES->caption());

            // BLOOD_PRESSURE
            $this->BLOOD_PRESSURE->setupEditAttributes();
            $this->BLOOD_PRESSURE->EditValue = $this->BLOOD_PRESSURE->AdvancedSearch->SearchValue;
            $this->BLOOD_PRESSURE->PlaceHolder = RemoveHtml($this->BLOOD_PRESSURE->caption());

            // ADDITIONAL_NOTES
            $this->ADDITIONAL_NOTES->setupEditAttributes();
            $this->ADDITIONAL_NOTES->EditValue = !$this->ADDITIONAL_NOTES->Raw ? HtmlDecode($this->ADDITIONAL_NOTES->AdvancedSearch->SearchValue) : $this->ADDITIONAL_NOTES->AdvancedSearch->SearchValue;
            $this->ADDITIONAL_NOTES->PlaceHolder = RemoveHtml($this->ADDITIONAL_NOTES->caption());
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
        if (!CheckInteger($this->PRESCRIPTION_ID->AdvancedSearch->SearchValue)) {
            $this->PRESCRIPTION_ID->addErrorMessage($this->PRESCRIPTION_ID->getErrorMessage(false));
        }
        if (!CheckDate($this->ISSUE_DATE->AdvancedSearch->SearchValue, $this->ISSUE_DATE->formatPattern())) {
            $this->ISSUE_DATE->addErrorMessage($this->ISSUE_DATE->getErrorMessage(false));
        }
        if (!CheckInteger($this->BLOOD_PRESSURE->AdvancedSearch->SearchValue)) {
            $this->BLOOD_PRESSURE->addErrorMessage($this->BLOOD_PRESSURE->getErrorMessage(false));
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
        $this->PRESCRIPTION_ID->AdvancedSearch->load();
        $this->ISSUE_DATE->AdvancedSearch->load();
        $this->Patient_Name->AdvancedSearch->load();
        $this->Doctor_Name->AdvancedSearch->load();
        $this->Specialisation->AdvancedSearch->load();
        $this->SYMPTOMS->AdvancedSearch->load();
        $this->DIAGNOSIS->AdvancedSearch->load();
        $this->DIABETES->AdvancedSearch->load();
        $this->BLOOD_PRESSURE->AdvancedSearch->load();
        $this->ADDITIONAL_NOTES->AdvancedSearch->load();
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("ViewPrescriptionReportList"), "", $this->TableVar, true);
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
                case "x_DIABETES":
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

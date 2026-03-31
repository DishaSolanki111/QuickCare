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
#[AsAlias("RefundTblView", true)]
class RefundTblView extends RefundTbl implements PageInterface
{
    use MessagesTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "view";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // CSS class/style
    public string $CurrentPageName = "RefundTblView"; // Route action

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
    public ?ListOptions $ExportOptions = null; // Export options
    public ?ListOptionsCollection $OtherOptions = null; // Other options
    public int $DisplayRecords = 1;
    public int $PageNumber = 1;
    public int $StartRecord = 0;
    public int $StopRecord = 0;
    public int $TotalRecords = 0;
    public bool $ViewPaging = false; // Allow view paging
    public ?int $RecordOffset = null; // Record offset (for View paging)
    public array $PagerOptions = ["proximity" => 2, "show_dots" => true];
    public bool $IsModal = false;
    public array $DetailGrids = [];

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
        $this->TableVar = 'refund_tbl';
        $this->TableName = 'refund_tbl';

        // Table CSS class
        $this->TableClass = "table table-striped table-bordered table-hover table-sm ew-view-table";

        // Initialize
        $httpContext["Page"] = $this;

        // Open connection
        $httpContext["Conn"] ??= $this->getConnection();

        // Export options
        $this->ExportOptions = new ListOptions(TagClassName: "ew-export-option");

        // Other options
        $this->OtherOptions = new ListOptionsCollection();

        // Detail tables
        $this->OtherOptions["detail"] = new ListOptions(TagClassName: "ew-detail-option");
        // Actions
        $this->OtherOptions["action"] = new ListOptions(TagClassName: "ew-action-option");

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
        $this->REFUND_ID->setVisibility();
        $this->PAYMENT_ID->setVisibility();
        $this->APPOINTMENT_ID->setVisibility();
        $this->PATIENT_ID->setVisibility();
        $this->REFUND_AMOUNT->setVisibility();
        $this->REFUND_DATE->setVisibility();
        $this->REFUND_STATUS->setVisibility();
        $this->REFUND_REASON->setVisibility();
        $this->REFUND_TXN_ID->setVisibility();
        $this->CREATED_AT->setVisibility();
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
                    $result["view"] = SameString($pageName, "RefundTblView"); // If View page, no primary button
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
        if ($this->isAdd() || $this->isCopy() || $this->isGridAdd()) {
            $this->REFUND_ID->Visible = false;
        }
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
     * Get view record key
     *
     * @return array
     */
    public function getViewRecordKey(): array
    {
        $recordKeys = $this->getRecordKeys();
        return count($recordKeys) > 0
            ? array_values($recordKeys[0]) // Get values only
            : [];
    }

    /**
     * Page init
     *
     * @return void
     */
    public function init(): void
    {
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
        $httpContext["ExportType"] = $this->Export; // Get export parameter, used in header
        if ($httpContext["ExportType"] != "") {
            global $httpContext;
            $httpContext["SkipHeaderFooter"] = true;
        }
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
        $this->setupLookupOptions($this->REFUND_STATUS);

        // Check modal
        if ($this->IsModal) {
            $httpContext["SkipHeaderFooter"] = true;
        }

        // Load pager
        $loadPager = false;
        $returnUrl = "";
        if (($keyValue = Get("REFUND_ID") ?? Route("refundId")) !== null) {
            $this->REFUND_ID->setQueryStringValue($keyValue);
        } elseif (Post("REFUND_ID") !== null) {
            $this->REFUND_ID->setFormValue(Post("REFUND_ID"));
        } elseif (IsApi() && ($keyValue = Key(0)) !== null) {
            $this->REFUND_ID->setQueryStringValue($keyValue);
        } elseif (!$loadPager) {
            $returnUrl = "RefundTblList"; // Return to list
        }

        // Get action
        $this->CurrentAction = "show"; // Display
        switch ($this->CurrentAction) {
            case "show": // Get a record to display
                if (!$this->CurrentRecord) { // No current record
                    if (!$this->peekSuccessMessage() && !$this->peekFailureMessage()) {
                        $this->setFailureMessage($this->language->phrase("NoRecord")); // Set no record message
                    }
                    $this->terminate("RefundTblList"); // Return to list page
                    return;
                } else { // Load current row
                    $this->loadRow();
                }
                break;
        }

        // Setup export options
        $this->setupExportOptions();
        if ($returnUrl != "") {
            $this->terminate($returnUrl);
            return;
        }

        // Set up Breadcrumb
        if (!$this->isExport()) {
            $this->setupBreadcrumb();
        }

        // Render row
        $this->renderRow(RowType::VIEW);

        // Normal return
        if (IsApi()) {
            if (!$this->isExport()) {
                $row = $this->getRowFromEntity($this->CurrentRecord); // Get current record only
                $this->Response = new JsonResponse(["success" => true, "action" => Config("API_VIEW_ACTION"), $this->TableVar => $row]);
                $this->terminate();
            }
            return;
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

    // Set up other options
    protected function setupOtherOptions(): void
    {
        // Disable Add/Edit/Copy/Delete for Modal and UseAjaxActions
        /*
        if ($this->IsModal && $this->UseAjaxActions) {
            $this->AddUrl = "";
            $this->EditUrl = "";
            $this->CopyUrl = "";
            $this->DeleteUrl = "";
        }
        */
        $options = $this->OtherOptions;
        $option = $options["action"];

        // Add
        $item = $option->add("add");
        $addcaption = HtmlTitle($this->language->phrase("ViewPageAddLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-add\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->AddUrl)) . "\">" . $this->language->phrase("ViewPageAddLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-add\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . HtmlEncode(GetUrl($this->AddUrl)) . "\">" . $this->language->phrase("ViewPageAddLink") . "</a>";
        }
        $item->Visible = $this->AddUrl != "";

        // Edit
        $item = $option->add("edit");
        $editcaption = HtmlTitle($this->language->phrase("ViewPageEditLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-edit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->EditUrl)) . "\">" . $this->language->phrase("ViewPageEditLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-edit\" title=\"" . $editcaption . "\" data-caption=\"" . $editcaption . "\" href=\"" . HtmlEncode(GetUrl($this->EditUrl)) . "\">" . $this->language->phrase("ViewPageEditLink") . "</a>";
        }
        $item->Visible = $this->EditUrl != "";

        // Copy
        $item = $option->add("copy");
        $copycaption = HtmlTitle($this->language->phrase("ViewPageCopyLink"));
        if ($this->IsModal) {
            $item->Body = "<a class=\"ew-action ew-copy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" data-ew-action=\"modal\" data-url=\"" . HtmlEncode(GetUrl($this->CopyUrl)) . "\" data-btn=\"AddBtn\">" . $this->language->phrase("ViewPageCopyLink") . "</a>";
        } else {
            $item->Body = "<a class=\"ew-action ew-copy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . HtmlEncode(GetUrl($this->CopyUrl)) . "\">" . $this->language->phrase("ViewPageCopyLink") . "</a>";
        }
        $item->Visible = $this->CopyUrl != "";

        // Delete
        $item = $option->add("delete");
        $url = GetUrl($this->DeleteUrl);
        $item->Body = "<a class=\"ew-action ew-delete\"" .
            ($this->InlineDelete || $this->IsModal ? " data-ew-action=\"inline-delete\"" : "") .
            " title=\"" . HtmlTitle($this->language->phrase("ViewPageDeleteLink")) . "\" data-caption=\"" . HtmlTitle($this->language->phrase("ViewPageDeleteLink")) .
            "\" href=\"" . HtmlEncode($url) . "\">" . $this->language->phrase("ViewPageDeleteLink") . "</a>";
        $item->Visible = $this->DeleteUrl != "";

        // Set up action default
        $option = $options["action"];
        $option->DropDownButtonPhrase = $this->language->phrase("ButtonActions");
        $option->UseDropDownButton = !IsJsonResponse() && true;
        $option->UseButtonGroup = true;
        $item = $option->addGroupOption();
        $item->Body = "";
        $item->Visible = false;
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
        $this->REFUND_ID->setDbValue($row['REFUND_ID']);
        $this->PAYMENT_ID->setDbValue($row['PAYMENT_ID']);
        $this->APPOINTMENT_ID->setDbValue($row['APPOINTMENT_ID']);
        $this->PATIENT_ID->setDbValue($row['PATIENT_ID']);
        $this->REFUND_AMOUNT->setDbValue($row['REFUND_AMOUNT']);
        $this->REFUND_DATE->setDbValue($row['REFUND_DATE']);
        $this->REFUND_STATUS->setDbValue($row['REFUND_STATUS']);
        $this->REFUND_REASON->setDbValue($row['REFUND_REASON']);
        $this->REFUND_TXN_ID->setDbValue($row['REFUND_TXN_ID']);
        $this->CREATED_AT->setDbValue($row['CREATED_AT']);
    }

    /**
     * Return a row with default values
     *
     * @return BaseEntity
     */
    protected function newRow(): BaseEntity
    {
        $row = new $this->EntityClass();
        if (!IsEmpty($this->REFUND_ID->DefaultValue)) {
            $row['REFUND_ID'] = intval($this->REFUND_ID->DefaultValue);
        }
        if (!IsEmpty($this->PAYMENT_ID->DefaultValue)) {
            $row['PAYMENT_ID'] = intval($this->PAYMENT_ID->DefaultValue);
        }
        if (!IsEmpty($this->APPOINTMENT_ID->DefaultValue)) {
            $row['APPOINTMENT_ID'] = intval($this->APPOINTMENT_ID->DefaultValue);
        }
        if (!IsEmpty($this->PATIENT_ID->DefaultValue)) {
            $row['PATIENT_ID'] = intval($this->PATIENT_ID->DefaultValue);
        }
        if (!IsEmpty($this->REFUND_AMOUNT->DefaultValue)) {
            $row['REFUND_AMOUNT'] = strval($this->REFUND_AMOUNT->DefaultValue);
        }
        if (!IsEmpty($this->REFUND_DATE->DefaultValue)) {
            $row['REFUND_DATE'] = $this->REFUND_DATE->DefaultValue instanceof DateTimeInterface ? $this->REFUND_DATE->DefaultValue : new DateTimeImmutable($this->REFUND_DATE->DefaultValue);
        }
        if (!IsEmpty($this->REFUND_STATUS->DefaultValue)) {
            $row['REFUND_STATUS'] = strval($this->REFUND_STATUS->DefaultValue);
        }
        if (!IsEmpty($this->REFUND_REASON->DefaultValue)) {
            $row['REFUND_REASON'] = strval($this->REFUND_REASON->DefaultValue);
        }
        if (!IsEmpty($this->REFUND_TXN_ID->DefaultValue)) {
            $row['REFUND_TXN_ID'] = strval($this->REFUND_TXN_ID->DefaultValue);
        }
        if (!IsEmpty($this->CREATED_AT->DefaultValue)) {
            $row['CREATED_AT'] = $this->CREATED_AT->DefaultValue instanceof DateTimeInterface ? $this->CREATED_AT->DefaultValue : new DateTimeImmutable($this->CREATED_AT->DefaultValue);
        }
        return $row;
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
        $this->AddUrl = $this->getAddUrl();
        $this->EditUrl = $this->getEditUrl();
        $this->CopyUrl = $this->getCopyUrl();
        $this->DeleteUrl = $this->getDeleteUrl();
        $this->ListUrl = $this->getListUrl();
        $this->setupOtherOptions();

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // REFUND_ID

        // PAYMENT_ID

        // APPOINTMENT_ID

        // PATIENT_ID

        // REFUND_AMOUNT

        // REFUND_DATE

        // REFUND_STATUS

        // REFUND_REASON

        // REFUND_TXN_ID

        // CREATED_AT

        // View row
        if ($this->RowType == RowType::VIEW) {
            // REFUND_ID
            $this->REFUND_ID->ViewValue = $this->REFUND_ID->CurrentValue;

            // PAYMENT_ID
            $this->PAYMENT_ID->ViewValue = $this->PAYMENT_ID->CurrentValue;
            $this->PAYMENT_ID->ViewValue = FormatNumber($this->PAYMENT_ID->ViewValue, $this->PAYMENT_ID->formatPattern());

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->ViewValue = $this->APPOINTMENT_ID->CurrentValue;
            $this->APPOINTMENT_ID->ViewValue = FormatNumber($this->APPOINTMENT_ID->ViewValue, $this->APPOINTMENT_ID->formatPattern());

            // PATIENT_ID
            $this->PATIENT_ID->ViewValue = $this->PATIENT_ID->CurrentValue;
            $this->PATIENT_ID->ViewValue = FormatNumber($this->PATIENT_ID->ViewValue, $this->PATIENT_ID->formatPattern());

            // REFUND_AMOUNT
            $this->REFUND_AMOUNT->ViewValue = $this->REFUND_AMOUNT->CurrentValue;
            $this->REFUND_AMOUNT->ViewValue = FormatNumber($this->REFUND_AMOUNT->ViewValue, $this->REFUND_AMOUNT->formatPattern());

            // REFUND_DATE
            $this->REFUND_DATE->ViewValue = $this->REFUND_DATE->CurrentValue;
            $this->REFUND_DATE->ViewValue = FormatDateTime($this->REFUND_DATE->ViewValue, $this->REFUND_DATE->formatPattern());

            // REFUND_STATUS
            if (strval($this->REFUND_STATUS->CurrentValue) != "") {
                $this->REFUND_STATUS->ViewValue = $this->REFUND_STATUS->optionCaption($this->REFUND_STATUS->CurrentValue);
            } else {
                $this->REFUND_STATUS->ViewValue = null;
            }

            // REFUND_REASON
            $this->REFUND_REASON->ViewValue = $this->REFUND_REASON->CurrentValue;

            // REFUND_TXN_ID
            $this->REFUND_TXN_ID->ViewValue = $this->REFUND_TXN_ID->CurrentValue;

            // CREATED_AT
            $this->CREATED_AT->ViewValue = $this->CREATED_AT->CurrentValue;
            $this->CREATED_AT->ViewValue = FormatDateTime($this->CREATED_AT->ViewValue, $this->CREATED_AT->formatPattern());

            // REFUND_ID
            $this->REFUND_ID->HrefValue = "";
            $this->REFUND_ID->TooltipValue = "";

            // PAYMENT_ID
            $this->PAYMENT_ID->HrefValue = "";
            $this->PAYMENT_ID->TooltipValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";
            $this->APPOINTMENT_ID->TooltipValue = "";

            // PATIENT_ID
            $this->PATIENT_ID->HrefValue = "";
            $this->PATIENT_ID->TooltipValue = "";

            // REFUND_AMOUNT
            $this->REFUND_AMOUNT->HrefValue = "";
            $this->REFUND_AMOUNT->TooltipValue = "";

            // REFUND_DATE
            $this->REFUND_DATE->HrefValue = "";
            $this->REFUND_DATE->TooltipValue = "";

            // REFUND_STATUS
            $this->REFUND_STATUS->HrefValue = "";
            $this->REFUND_STATUS->TooltipValue = "";

            // REFUND_REASON
            $this->REFUND_REASON->HrefValue = "";
            $this->REFUND_REASON->TooltipValue = "";

            // REFUND_TXN_ID
            $this->REFUND_TXN_ID->HrefValue = "";
            $this->REFUND_TXN_ID->TooltipValue = "";

            // CREATED_AT
            $this->CREATED_AT->HrefValue = "";
            $this->CREATED_AT->TooltipValue = "";
        }

        // Call Row Rendered event
        if ($this->RowType != RowType::AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Get export HTML tag
    protected function getExportTag(string $type, bool $custom = false): string
    {
        if ($type == "print" || $custom) { // Printer friendly / custom export
            $pageUrl = $this->keyUrl("RefundTblView");
            $exportUrl = BuildUrl(GetUrl($pageUrl), "export=" . $type, $custom ? "custom=1" : "");
        } else { // Export API URL
            $exportUrl = GetApiUrl(Config("API_EXPORT_ACTION") . "/" . $type . "/" . $this->TableVar);
            $exportUrl .= "/" . implode("/", array_values($this->getKey(true)));
        }
        $exportUrl = HtmlEncode($exportUrl);
        if (SameText($type, "excel")) {
            if ($custom) {
                return "<button type=\"button\" class=\"btn btn-default ew-export-link ew-excel\" title=\"" . HtmlEncode($this->language->phrase("ExportToExcel", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToExcel", true)) . "\" form=\"frefund_tblview\" data-url=\"$exportUrl\" data-ew-action=\"export\" data-export=\"excel\" data-custom=\"true\" data-export-selected=\"false\">" . $this->language->phrase("ExportToExcel") . "</button>";
            } else {
                return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-excel\" title=\"" . HtmlEncode($this->language->phrase("ExportToExcel", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToExcel", true)) . "\">" . $this->language->phrase("ExportToExcel") . "</a>";
            }
        } elseif (SameText($type, "word")) {
            if ($custom) {
                return "<button type=\"button\" class=\"btn btn-default ew-export-link ew-word\" title=\"" . HtmlEncode($this->language->phrase("ExportToWord", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToWord", true)) . "\" form=\"frefund_tblview\" data-url=\"$exportUrl\" data-ew-action=\"export\" data-export=\"word\" data-custom=\"true\" data-export-selected=\"false\">" . $this->language->phrase("ExportToWord") . "</button>";
            } else {
                return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-word\" title=\"" . HtmlEncode($this->language->phrase("ExportToWord", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToWord", true)) . "\">" . $this->language->phrase("ExportToWord") . "</a>";
            }
        } elseif (SameText($type, "pdf")) {
            if ($custom) {
                return "<button type=\"button\" class=\"btn btn-default ew-export-link ew-pdf\" title=\"" . HtmlEncode($this->language->phrase("ExportToPdf", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToPdf", true)) . "\" form=\"frefund_tblview\" data-url=\"$exportUrl\" data-ew-action=\"export\" data-export=\"pdf\" data-custom=\"true\" data-export-selected=\"false\">" . $this->language->phrase("ExportToPdf") . "</button>";
            } else {
                return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-pdf\" title=\"" . HtmlEncode($this->language->phrase("ExportToPdf", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToPdf", true)) . "\">" . $this->language->phrase("ExportToPdf") . "</a>";
            }
        } elseif (SameText($type, "html")) {
            return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-html\" title=\"" . HtmlEncode($this->language->phrase("ExportToHtml", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToHtml", true)) . "\">" . $this->language->phrase("ExportToHtml") . "</a>";
        } elseif (SameText($type, "xml")) {
            return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-xml\" title=\"" . HtmlEncode($this->language->phrase("ExportToXml", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToXml", true)) . "\">" . $this->language->phrase("ExportToXml") . "</a>";
        } elseif (SameText($type, "csv")) {
            return "<a href=\"$exportUrl\" class=\"btn btn-default ew-export-link ew-csv\" title=\"" . HtmlEncode($this->language->phrase("ExportToCsv", true)) . "\" data-caption=\"" . HtmlEncode($this->language->phrase("ExportToCsv", true)) . "\">" . $this->language->phrase("ExportToCsv") . "</a>";
        } elseif (SameText($type, "email")) {
            $url = $custom ? ' data-url="' . $exportUrl . '"' : '';
            return '<button type="button" class="btn btn-default ew-export-link ew-email" title="' . $this->language->phrase("ExportToEmail", true) . '" data-caption="' . $this->language->phrase("ExportToEmail", true) . '" form="frefund_tblview" data-ew-action="email" data-custom="false" data-hdr="' . $this->language->phrase("ExportToEmail", true) . '" data-key="' . ArrayToJsonAttribute($this->getViewRecordKey()) . '" data-exported-selected="false"' . $url . '>' . $this->language->phrase("ExportToEmail") . '</button>';
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

        // Export to XML
        $item = $this->ExportOptions->add("xml");
        $item->Body = $this->getExportTag("xml");
        $item->Visible = false;

        // Export to CSV
        $item = $this->ExportOptions->add("csv");
        $item->Body = $this->getExportTag("csv");
        $item->Visible = false;

        // Export to PDF
        $item = $this->ExportOptions->add("pdf");
        $item->Body = $this->getExportTag("pdf");
        $item->Visible = true;

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

        // Hide options if json response
        if (IsJsonResponse()) {
            $this->ExportOptions->hideAllOptions();
        }
    }

    /**
    * Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
    */
    public function exportData(object $doc, array $keys): void
    {
        $records = [];
        if (count($keys) >= 1) {
            $this->REFUND_ID->OldValue = $keys[0];
            $records = $this->loadEntitiesFromFilter($this->getRecordFilter());
        }
        $this->StartRecord = 1;
        $this->StopRecord = 1;
        if (count($records) == 0 || !$doc) {
            echo $this->getHtmlMessage();
            return;
        }

        // Call Page Exporting server event
        $doc->ExportCustom = !$this->pageExporting($doc);

        // Page header
        $header = $this->PageHeader;
        $this->pageDataRendering($header);
        $doc->Text .= $header;
        $this->exportDocument($doc, $records, $this->StartRecord, $this->StopRecord, "view");

        // Page footer
        $footer = $this->PageFooter;
        $this->pageDataRendered($footer);
        $doc->Text .= $footer;

        // Export header and footer
        $doc->exportHeaderAndFooter();

        // Call Page Exported server event
        $this->pageExported($doc);
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("RefundTblList"), "", $this->TableVar, true);
        $pageId = "view";
        $breadcrumb->add("view", $pageId, $url);
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
                case "x_REFUND_STATUS":
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
            $this->PageNumber = $pageNumber ?? $startRec ?? 0; // Record number = page number or start record
            if ($this->PageNumber > 0) {
                $this->StartRecord = $this->PageNumber;
            } else {
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
}

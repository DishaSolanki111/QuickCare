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
#[AsAlias("RefundTblEdit", true)]
class RefundTblEdit extends RefundTbl implements PageInterface
{
    use MessagesTrait;
    use FormTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "edit";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // CSS class/style
    public string $CurrentPageName = "RefundTblEdit"; // Route action

    // Page headings
    public string $Heading = "";
    public string $Subheading = "";
    public string $PageHeader = "";
    public string $PageFooter = "";

    // Page layout
    public bool $UseLayout = true;

    // Page terminated
    private bool $terminated = false;

    // Properties
    public string $FormClassName = "ew-form ew-edit-form overlay-wrapper";
    public bool $IsModal = false;
    public bool $IsMobileOrModal = false;
    public ?string $HashValue = null; // Hash Value
    public int $DisplayRecords = 1;
    public int $PageNumber = 1;
    public int $StartRecord = 0;
    public int $StopRecord = 0;
    public int $TotalRecords = 0;
    public bool $EditPaging = false; // Allow edit paging
    public ?int $RecordOffset = null; // Record offset (for Edit paging)
    public array $PagerOptions = ["proximity" => 2, "show_dots" => true];
    public int $RecordCount = 0;
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
        $this->TableClass = "table table-striped table-bordered table-hover table-sm ew-desktop-table ew-edit-table";

        // Initialize
        $httpContext["Page"] = $this;

        // Open connection
        $httpContext["Conn"] ??= $this->getConnection();

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
                if (
                    SameString($pageName, GetPageName($this->getListUrl()))
                    || SameString($pageName, GetPageName($this->getViewUrl()))
                    || SameString($pageName, GetPageName(CurrentMasterTable()?->getViewUrl() ?? ""))
                ) { // List / View / Master View page
                    if (!SameString($pageName, GetPageName($this->getListUrl()))) { // Not List page
                        $result["caption"] = $this->getModalCaption($pageName);
                        $result["view"] = SameString($pageName, "RefundTblView"); // If View page, no primary button
                    } else { // List page
                        $result["error"] = $this->getFailureMessage(); // List page should not be shown as modal => error
                    }
                } else { // Other pages (add messages and then clear messages)
                    $result = array_merge($this->getMessages(), ["modal" => "1"]);
                    $this->clearMessages();
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
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;
        $loaded = $this->CurrentRecord instanceof BaseEntity;
        $postBack = false;

        // Set up current action and primary key
        if (IsApi()) {
            // Load record
            if ($loaded) {
                // Load key values
                if (($keyValue = Route("refundId") ?? Get("REFUND_ID")) !== null) {
                    $this->REFUND_ID->setQueryStringValue($keyValue);
                    $this->REFUND_ID->setOldValue($this->REFUND_ID->QueryStringValue);
                } elseif (($keyValue = Post("REFUND_ID")) !== null) {
                    $this->REFUND_ID->setFormValue($keyValue);
                    $this->REFUND_ID->setOldValue($this->REFUND_ID->FormValue);
                } elseif (($keyValue = Key(0)) !== null) {
                    $this->REFUND_ID->setQueryStringValue($keyValue);
                    $this->REFUND_ID->setOldValue($this->REFUND_ID->QueryStringValue);
                }
                $loaded = $this->loadRow();
            } else {
                $this->setFailureMessage($this->language->phrase("NoRecord")); // Set no record message
                $this->terminate();
                return;
            }
            $this->CurrentAction = "update"; // Update record directly
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $postBack = true;
        } else {
            if ($this->CurrentAction = Post("action")) { // Get action code
                if (!$this->isShow()) { // Not reload record, handle as postback
                    $postBack = true;
                }

                // Get key from Form
                $this->setKey($this->getFormOldKey(), $this->isShow());
            } else {
                $this->CurrentAction = "show"; // Default action is display

                // Load key from query string or route value
                $loadByQuery = false;
                if (($keyValue = Route("refundId") ?? Get("REFUND_ID")) !== null) {
                    $this->REFUND_ID->setQueryStringValue($keyValue);
                    $loadByQuery = true;
                } else {
                    $this->REFUND_ID->CurrentValue = null;
                }
            }

            // Load result set
            if ($this->isShow()) {
                if (!$this->CurrentRecord) { // No record found
                    if (!$this->peekSuccessMessage() && !$this->peekFailureMessage()) {
                        $this->setFailureMessage($this->language->phrase("NoRecord")); // Set no record message
                    }
                    $this->terminate("RefundTblList"); // Return to list page
                    return;
                } else { // Load current row
                    $loaded = $this->loadRow();
                }
                $this->OldKey = $loaded ? $this->getKey(true) : []; // Get from CurrentValue
            }
        }

        // Process form if post back
        if ($postBack) {
            $this->loadFormValues(); // Get form values
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                if (IsApi()) {
                    $this->Response = new JsonResponse(["success" => false, "version" => PRODUCT_VERSION, "validation" => $this->getValidationErrors()]);
                    $this->terminate();
                    return;
                } else {
                    $this->restoreFormValues();
                    $this->CurrentAction = ""; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "show": // Get a record to display
                if (!$this->IsModal) { // Normal edit page
                    if (!$loaded) {
                        if (!$this->peekSuccessMessage() && !$this->peekFailureMessage()) {
                            $this->setFailureMessage($this->language->phrase("NoRecord")); // Set no record message
                        }
                        $this->terminate("RefundTblList"); // Return to list page
                        return;
                    }
                } else { // Modal edit page
                    if (!$loaded) { // Load record based on key
                        if (!$this->peekFailureMessage()) {
                            $this->setFailureMessage($this->language->phrase("NoRecord")); // No record found
                        }
                        $this->terminate("RefundTblList"); // No matching record, return to list
                        return;
                    }
                } // End modal checking
                break;
            case "update": // Update
                $returnUrl = $this->getReturnUrl();
                if (GetPageName($returnUrl) == "RefundTblList") {
                    $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                }
                if ($this->editRow()) { // Update record based on key
                    CleanUploadTempPaths(SessionId());
                    if (!$this->peekSuccessMessage()) {
                        $this->setSuccessMessage($this->language->phrase("UpdateSuccess")); // Update success
                    }

                    // Handle UseAjaxActions with return page
                    if ($this->IsModal && $this->UseAjaxActions) {
                        $this->IsModal = false;
                        if (GetPageName($returnUrl) != "RefundTblList") {
                            FlashBag()->add("X-Return-Url", $returnUrl); // Save return URL
                            $returnUrl = "RefundTblList"; // Return list page content
                        }
                    }
                    if (IsJsonResponse()) {
                        $this->terminate();
                        return;
                    } else {
                        $this->terminate($returnUrl); // Return to caller
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } elseif ($this->IsModal && $this->UseAjaxActions) { // Return JSON error message
                    $this->Response = new JsonResponse(["success" => false, "validation" => $this->getValidationErrors(), "error" => $this->getFailureMessage()]);
                    $this->terminate();
                    return;
                } elseif (($this->peekFailureMessage()[0] ?? "") == $this->language->phrase("NoRecord")) {
                    $this->terminate($returnUrl); // Return to caller
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Restore form values if update failed
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render row
        $this->renderRow(
            RowType::EDIT
        );

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

    // Get upload files
    protected function getUploadFiles(): void
    {
    }

    // Load form values
    protected function loadFormValues(): void
    {
        $validate = !Config("SERVER_VALIDATE");

        // REFUND_ID
        if (!$this->REFUND_ID->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_ID) ? $this->getInputValue($this->REFUND_ID) : null;
            $this->REFUND_ID->setFormValue($val);
        }

        // PAYMENT_ID
        if (!$this->PAYMENT_ID->IsDetailKey) {
            $val = $this->hasInputValue($this->PAYMENT_ID) ? $this->getInputValue($this->PAYMENT_ID) : null;
            $this->PAYMENT_ID->setFormValue($val, true, $validate);
        }

        // APPOINTMENT_ID
        if (!$this->APPOINTMENT_ID->IsDetailKey) {
            $val = $this->hasInputValue($this->APPOINTMENT_ID) ? $this->getInputValue($this->APPOINTMENT_ID) : null;
            $this->APPOINTMENT_ID->setFormValue($val, true, $validate);
        }

        // PATIENT_ID
        if (!$this->PATIENT_ID->IsDetailKey) {
            $val = $this->hasInputValue($this->PATIENT_ID) ? $this->getInputValue($this->PATIENT_ID) : null;
            $this->PATIENT_ID->setFormValue($val, true, $validate);
        }

        // REFUND_AMOUNT
        if (!$this->REFUND_AMOUNT->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_AMOUNT) ? $this->getInputValue($this->REFUND_AMOUNT) : null;
            $this->REFUND_AMOUNT->setFormValue($val, true, $validate);
        }

        // REFUND_DATE
        if (!$this->REFUND_DATE->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_DATE) ? $this->getInputValue($this->REFUND_DATE) : null;
            $this->REFUND_DATE->setFormValue($val, true, $validate);
            $this->REFUND_DATE->CurrentValue = UnformatDateTime($this->REFUND_DATE->CurrentValue, $this->REFUND_DATE->formatPattern());
        }

        // REFUND_STATUS
        if (!$this->REFUND_STATUS->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_STATUS) ? $this->getInputValue($this->REFUND_STATUS) : null;
            $this->REFUND_STATUS->setFormValue($val);
        }

        // REFUND_REASON
        if (!$this->REFUND_REASON->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_REASON) ? $this->getInputValue($this->REFUND_REASON) : null;
            $this->REFUND_REASON->setFormValue($val);
        }

        // REFUND_TXN_ID
        if (!$this->REFUND_TXN_ID->IsDetailKey) {
            $val = $this->hasInputValue($this->REFUND_TXN_ID) ? $this->getInputValue($this->REFUND_TXN_ID) : null;
            $this->REFUND_TXN_ID->setFormValue($val);
        }

        // CREATED_AT
        if (!$this->CREATED_AT->IsDetailKey) {
            $val = $this->hasInputValue($this->CREATED_AT) ? $this->getInputValue($this->CREATED_AT) : null;
            $this->CREATED_AT->setFormValue($val, true, $validate);
            $this->CREATED_AT->CurrentValue = UnformatDateTime($this->CREATED_AT->CurrentValue, $this->CREATED_AT->formatPattern());
        }
    }

    // Restore form values
    public function restoreFormValues(): void
    {
        $this->REFUND_ID->CurrentValue = $this->REFUND_ID->FormValue;
        $this->PAYMENT_ID->CurrentValue = $this->PAYMENT_ID->FormValue;
        $this->APPOINTMENT_ID->CurrentValue = $this->APPOINTMENT_ID->FormValue;
        $this->PATIENT_ID->CurrentValue = $this->PATIENT_ID->FormValue;
        $this->REFUND_AMOUNT->CurrentValue = $this->REFUND_AMOUNT->FormValue;
        $this->REFUND_DATE->CurrentValue = $this->REFUND_DATE->FormValue;
        $this->REFUND_DATE->CurrentValue = UnformatDateTime($this->REFUND_DATE->CurrentValue, $this->REFUND_DATE->formatPattern());
        $this->REFUND_STATUS->CurrentValue = $this->REFUND_STATUS->FormValue;
        $this->REFUND_REASON->CurrentValue = $this->REFUND_REASON->FormValue;
        $this->REFUND_TXN_ID->CurrentValue = $this->REFUND_TXN_ID->FormValue;
        $this->CREATED_AT->CurrentValue = $this->CREATED_AT->FormValue;
        $this->CREATED_AT->CurrentValue = UnformatDateTime($this->CREATED_AT->CurrentValue, $this->CREATED_AT->formatPattern());
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

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // REFUND_ID
        $this->REFUND_ID->RowCssClass = "row";

        // PAYMENT_ID
        $this->PAYMENT_ID->RowCssClass = "row";

        // APPOINTMENT_ID
        $this->APPOINTMENT_ID->RowCssClass = "row";

        // PATIENT_ID
        $this->PATIENT_ID->RowCssClass = "row";

        // REFUND_AMOUNT
        $this->REFUND_AMOUNT->RowCssClass = "row";

        // REFUND_DATE
        $this->REFUND_DATE->RowCssClass = "row";

        // REFUND_STATUS
        $this->REFUND_STATUS->RowCssClass = "row";

        // REFUND_REASON
        $this->REFUND_REASON->RowCssClass = "row";

        // REFUND_TXN_ID
        $this->REFUND_TXN_ID->RowCssClass = "row";

        // CREATED_AT
        $this->CREATED_AT->RowCssClass = "row";

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

            // PAYMENT_ID
            $this->PAYMENT_ID->HrefValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";

            // PATIENT_ID
            $this->PATIENT_ID->HrefValue = "";

            // REFUND_AMOUNT
            $this->REFUND_AMOUNT->HrefValue = "";

            // REFUND_DATE
            $this->REFUND_DATE->HrefValue = "";

            // REFUND_STATUS
            $this->REFUND_STATUS->HrefValue = "";

            // REFUND_REASON
            $this->REFUND_REASON->HrefValue = "";

            // REFUND_TXN_ID
            $this->REFUND_TXN_ID->HrefValue = "";

            // CREATED_AT
            $this->CREATED_AT->HrefValue = "";
        } elseif ($this->RowType == RowType::EDIT) {
            // REFUND_ID
            $this->REFUND_ID->setupEditAttributes();
            $this->REFUND_ID->EditValue = $this->REFUND_ID->CurrentValue;

            // PAYMENT_ID
            $this->PAYMENT_ID->setupEditAttributes();
            $this->PAYMENT_ID->EditValue = $this->PAYMENT_ID->CurrentValue;
            $this->PAYMENT_ID->PlaceHolder = RemoveHtml($this->PAYMENT_ID->caption());
            if (strval($this->PAYMENT_ID->EditValue) != "" && is_numeric($this->PAYMENT_ID->EditValue)) {
                $this->PAYMENT_ID->EditValue = FormatNumber($this->PAYMENT_ID->EditValue, null);
            }

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->setupEditAttributes();
            $this->APPOINTMENT_ID->EditValue = $this->APPOINTMENT_ID->CurrentValue;
            $this->APPOINTMENT_ID->PlaceHolder = RemoveHtml($this->APPOINTMENT_ID->caption());
            if (strval($this->APPOINTMENT_ID->EditValue) != "" && is_numeric($this->APPOINTMENT_ID->EditValue)) {
                $this->APPOINTMENT_ID->EditValue = FormatNumber($this->APPOINTMENT_ID->EditValue, null);
            }

            // PATIENT_ID
            $this->PATIENT_ID->setupEditAttributes();
            $this->PATIENT_ID->EditValue = $this->PATIENT_ID->CurrentValue;
            $this->PATIENT_ID->PlaceHolder = RemoveHtml($this->PATIENT_ID->caption());
            if (strval($this->PATIENT_ID->EditValue) != "" && is_numeric($this->PATIENT_ID->EditValue)) {
                $this->PATIENT_ID->EditValue = FormatNumber($this->PATIENT_ID->EditValue, null);
            }

            // REFUND_AMOUNT
            $this->REFUND_AMOUNT->setupEditAttributes();
            $this->REFUND_AMOUNT->EditValue = $this->REFUND_AMOUNT->CurrentValue;
            $this->REFUND_AMOUNT->PlaceHolder = RemoveHtml($this->REFUND_AMOUNT->caption());
            if (strval($this->REFUND_AMOUNT->EditValue) != "" && is_numeric($this->REFUND_AMOUNT->EditValue)) {
                $this->REFUND_AMOUNT->EditValue = FormatNumber($this->REFUND_AMOUNT->EditValue, null);
            }

            // REFUND_DATE
            $this->REFUND_DATE->setupEditAttributes();
            $this->REFUND_DATE->EditValue = FormatDateTime($this->REFUND_DATE->CurrentValue, $this->REFUND_DATE->formatPattern());
            $this->REFUND_DATE->PlaceHolder = RemoveHtml($this->REFUND_DATE->caption());

            // REFUND_STATUS
            $this->REFUND_STATUS->EditValue = $this->REFUND_STATUS->options(false);
            $this->REFUND_STATUS->PlaceHolder = RemoveHtml($this->REFUND_STATUS->caption());

            // REFUND_REASON
            $this->REFUND_REASON->setupEditAttributes();
            $this->REFUND_REASON->EditValue = !$this->REFUND_REASON->Raw ? HtmlDecode($this->REFUND_REASON->CurrentValue) : $this->REFUND_REASON->CurrentValue;
            $this->REFUND_REASON->PlaceHolder = RemoveHtml($this->REFUND_REASON->caption());

            // REFUND_TXN_ID
            $this->REFUND_TXN_ID->setupEditAttributes();
            $this->REFUND_TXN_ID->EditValue = !$this->REFUND_TXN_ID->Raw ? HtmlDecode($this->REFUND_TXN_ID->CurrentValue) : $this->REFUND_TXN_ID->CurrentValue;
            $this->REFUND_TXN_ID->PlaceHolder = RemoveHtml($this->REFUND_TXN_ID->caption());

            // CREATED_AT
            $this->CREATED_AT->setupEditAttributes();
            $this->CREATED_AT->EditValue = FormatDateTime($this->CREATED_AT->CurrentValue, $this->CREATED_AT->formatPattern());
            $this->CREATED_AT->PlaceHolder = RemoveHtml($this->CREATED_AT->caption());

            // Edit refer script

            // REFUND_ID
            $this->REFUND_ID->HrefValue = "";

            // PAYMENT_ID
            $this->PAYMENT_ID->HrefValue = "";

            // APPOINTMENT_ID
            $this->APPOINTMENT_ID->HrefValue = "";

            // PATIENT_ID
            $this->PATIENT_ID->HrefValue = "";

            // REFUND_AMOUNT
            $this->REFUND_AMOUNT->HrefValue = "";

            // REFUND_DATE
            $this->REFUND_DATE->HrefValue = "";

            // REFUND_STATUS
            $this->REFUND_STATUS->HrefValue = "";

            // REFUND_REASON
            $this->REFUND_REASON->HrefValue = "";

            // REFUND_TXN_ID
            $this->REFUND_TXN_ID->HrefValue = "";

            // CREATED_AT
            $this->CREATED_AT->HrefValue = "";
        }
        if ($this->RowType == RowType::ADD || $this->RowType == RowType::EDIT || $this->RowType == RowType::SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType != RowType::AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate form
    protected function validateForm(): bool
    {
        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }
        $validateForm = true;
        if ($this->REFUND_ID->Visible) {
            if ($this->REFUND_ID->Required) {
                if (!$this->REFUND_ID->IsDetailKey && IsEmpty($this->REFUND_ID->FormValue)) {
                    $this->REFUND_ID->addErrorMessage(str_replace("%s", $this->REFUND_ID->caption(), $this->REFUND_ID->RequiredErrorMessage));
                }
            }
        }
        if ($this->PAYMENT_ID->Visible) {
            if ($this->PAYMENT_ID->Required) {
                if (!$this->PAYMENT_ID->IsDetailKey && IsEmpty($this->PAYMENT_ID->FormValue)) {
                    $this->PAYMENT_ID->addErrorMessage(str_replace("%s", $this->PAYMENT_ID->caption(), $this->PAYMENT_ID->RequiredErrorMessage));
                }
            }
            if (!CheckInteger($this->PAYMENT_ID->FormValue)) {
                $this->PAYMENT_ID->addErrorMessage($this->PAYMENT_ID->getErrorMessage(false));
            }
        }
        if ($this->APPOINTMENT_ID->Visible) {
            if ($this->APPOINTMENT_ID->Required) {
                if (!$this->APPOINTMENT_ID->IsDetailKey && IsEmpty($this->APPOINTMENT_ID->FormValue)) {
                    $this->APPOINTMENT_ID->addErrorMessage(str_replace("%s", $this->APPOINTMENT_ID->caption(), $this->APPOINTMENT_ID->RequiredErrorMessage));
                }
            }
            if (!CheckInteger($this->APPOINTMENT_ID->FormValue)) {
                $this->APPOINTMENT_ID->addErrorMessage($this->APPOINTMENT_ID->getErrorMessage(false));
            }
        }
        if ($this->PATIENT_ID->Visible) {
            if ($this->PATIENT_ID->Required) {
                if (!$this->PATIENT_ID->IsDetailKey && IsEmpty($this->PATIENT_ID->FormValue)) {
                    $this->PATIENT_ID->addErrorMessage(str_replace("%s", $this->PATIENT_ID->caption(), $this->PATIENT_ID->RequiredErrorMessage));
                }
            }
            if (!CheckInteger($this->PATIENT_ID->FormValue)) {
                $this->PATIENT_ID->addErrorMessage($this->PATIENT_ID->getErrorMessage(false));
            }
        }
        if ($this->REFUND_AMOUNT->Visible) {
            if ($this->REFUND_AMOUNT->Required) {
                if (!$this->REFUND_AMOUNT->IsDetailKey && IsEmpty($this->REFUND_AMOUNT->FormValue)) {
                    $this->REFUND_AMOUNT->addErrorMessage(str_replace("%s", $this->REFUND_AMOUNT->caption(), $this->REFUND_AMOUNT->RequiredErrorMessage));
                }
            }
            if (!CheckNumber($this->REFUND_AMOUNT->FormValue)) {
                $this->REFUND_AMOUNT->addErrorMessage($this->REFUND_AMOUNT->getErrorMessage(false));
            }
        }
        if ($this->REFUND_DATE->Visible) {
            if ($this->REFUND_DATE->Required) {
                if (!$this->REFUND_DATE->IsDetailKey && IsEmpty($this->REFUND_DATE->FormValue)) {
                    $this->REFUND_DATE->addErrorMessage(str_replace("%s", $this->REFUND_DATE->caption(), $this->REFUND_DATE->RequiredErrorMessage));
                }
            }
            if (!CheckDate($this->REFUND_DATE->FormValue, $this->REFUND_DATE->formatPattern())) {
                $this->REFUND_DATE->addErrorMessage($this->REFUND_DATE->getErrorMessage(false));
            }
        }
        if ($this->REFUND_STATUS->Visible) {
            if ($this->REFUND_STATUS->Required) {
                if ($this->REFUND_STATUS->FormValue == "") {
                    $this->REFUND_STATUS->addErrorMessage(str_replace("%s", $this->REFUND_STATUS->caption(), $this->REFUND_STATUS->RequiredErrorMessage));
                }
            }
        }
        if ($this->REFUND_REASON->Visible) {
            if ($this->REFUND_REASON->Required) {
                if (!$this->REFUND_REASON->IsDetailKey && IsEmpty($this->REFUND_REASON->FormValue)) {
                    $this->REFUND_REASON->addErrorMessage(str_replace("%s", $this->REFUND_REASON->caption(), $this->REFUND_REASON->RequiredErrorMessage));
                }
            }
        }
        if ($this->REFUND_TXN_ID->Visible) {
            if ($this->REFUND_TXN_ID->Required) {
                if (!$this->REFUND_TXN_ID->IsDetailKey && IsEmpty($this->REFUND_TXN_ID->FormValue)) {
                    $this->REFUND_TXN_ID->addErrorMessage(str_replace("%s", $this->REFUND_TXN_ID->caption(), $this->REFUND_TXN_ID->RequiredErrorMessage));
                }
            }
        }
        if ($this->CREATED_AT->Visible) {
            if ($this->CREATED_AT->Required) {
                if (!$this->CREATED_AT->IsDetailKey && IsEmpty($this->CREATED_AT->FormValue)) {
                    $this->CREATED_AT->addErrorMessage(str_replace("%s", $this->CREATED_AT->caption(), $this->CREATED_AT->RequiredErrorMessage));
                }
            }
            if (!CheckDate($this->CREATED_AT->FormValue, $this->CREATED_AT->formatPattern())) {
                $this->CREATED_AT->addErrorMessage($this->CREATED_AT->getErrorMessage(false));
            }
        }

        // Return validate result
        $validateForm = $validateForm && !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateForm = $validateForm && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateForm;
    }

    // Update record based on key values
    protected function editRow(): ?bool
    {
        $row = $this->CurrentRecord; // Use current record for update
        if ($row === null) {
            $this->setFailureMessage($this->language->phrase("NoRecord")); // Set no record message
            return false; // Update Failed
        }

        // Clone entity (as old row)
        $oldRow = clone $row;

        // Update entity
        $newRow = $this->getEditRow($row);

        // Validate constraints
        $errors = Validate($newRow);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->fieldByPropertyName($error->getPropertyPath())?->addErrorMessage($error->getMessage());
            }
            return false;
        }

        // Get Entity Manager
        $em = $this->getEntityManager();

        // Call Row Updating event
        $update = method_exists($this, "rowUpdating") ? $this->rowUpdating($oldRow, $newRow) : true;
        $oldPk = $oldRow->identifierValues();
        $newPk = $newRow->identifierValues();
        if ($update) {
            try {
                $updateTableRow = null;
                if ($oldPk === $newPk) { // PK unchanged
                    if ($this->UpdateTable && $this->UpdateTable != $this->TableName) { // Use Update Table if set
                        $id = $oldRow->identifierValues();
                        $updateTableRow = $em->find($this->UpdateTableEntityClass, $id);
                        if (!$updateTableRow) {
                            throw new \RuntimeException("Cannot update: related entity not found.");
                        }
                        $updateTableRow->fromArray($newRow->toArray());
                        $em->detach($newRow);
                    }
                } else {
                    $this->handlePrimaryKeyChange($oldRow, $newRow);
                }
                $em->flush();
                $updated = true;
            } catch (Exception $e) {
                $this->dispatcher->dispatch(new RowUpdateFailedEvent($updateTableRow ?? $newRow, $e));
                $this->setFailureMessage($e->getMessage());
                $updated = false;
            }
            if ($updated) {
            }
        } else {
            if ($update === false) {
                if ($this->peekSuccessMessage() || $this->peekFailureMessage()) {
                    // Use the message, do nothing
                } elseif ($this->CancelMessage != "") {
                    $this->setFailureMessage($this->CancelMessage);
                    $this->CancelMessage = "";
                } else {
                    $this->setFailureMessage($this->language->phrase("UpdateCancelled"));
                }
            } elseif ($update === null) { // Skip update record
                $em->detach($newRow);
            }
            $updated = $update;
        }

        // Call Row_Updated event
        if ($updated && method_exists($this, "rowUpdated")) {
            $this->rowUpdated($oldRow, $newRow);
        }

        // Write JSON response
        if (IsJsonResponse() && $updated) {
            $row = $this->getRowsFromEntities([$newRow], true);
            $table = $this->TableVar;
            $this->Response = new JsonResponse(["success" => true, "action" => Config("API_EDIT_ACTION"), $table => $row]);
        }
        return $updated;
    }

    /**
     * Handle primary key change
     *
     * @param BaseEntity $oldRow
     * @param BaseEntity $newRow
     *
     * @return void
     */
    protected function handlePrimaryKeyChange(BaseEntity $oldRow, BaseEntity $newRow): void
    {
        $em = $this->getEntityManager();
        $meta = $newRow->metadata();
        $uow = $em->getUnitOfWork();

        // Recompute changes for lifecycle events
        $uow->recomputeSingleEntityChangeSet($meta, $newRow);
        $changeSet = $uow->getEntityChangeSet($newRow);

        // Replace entity and meta data if UpdateTable is different from current table
        if ($this->UpdateTable && $this->UpdateTable != $this->TableName) {
            $data = $newRow->toArray();
            $em->detach($newRow);
            $newRow = $this->UpdateTableEntityClass::createFromArray($data);
            $meta = $newRow->metadata();
        }

        // Trigger preUpdate if there are changes
        if ($changeSet) {
            $em->getEventManager()->dispatchEvent(
                \Doctrine\ORM\Events::preUpdate,
                new \Doctrine\ORM\Event\PreUpdateEventArgs($newRow, $em, $changeSet)
            );
            //$em->flush(); // Don't flush here
        }

        // Detach entity to avoid affecting UnitOfWork
        $em->detach($newRow);

        // Prepare data for DBAL update
        $data = [];
        foreach ($changeSet as $field => [, $newValue]) {
            if ($meta->hasField($field)) {
                $data[$newRow->columnName($field)] = $newValue;
            }
        }

        // Build criteria using old identifier values (safe for any entity)
        $criteria = [];
        foreach ($meta->getIdentifierValues($oldRow) as $field => $value) {
            $criteria[$oldRow->columnName($field)] = $value;
        }

        // Execute DBAL update
        if ($data) {
            $tableName = $meta->getTableName();
            $table = ServiceLocator($tableName) ?? $this;
            $affected = $table->update($data, $criteria);

            // Trigger postUpdate only if something was actually updated
            if ($affected > 0) {
                $em->getEventManager()->dispatchEvent(
                    \Doctrine\ORM\Events::postUpdate,
                    new \Doctrine\ORM\Event\PostUpdateEventArgs($newRow, $em, $changeSet)
                );
                //$em->flush(); // Don't flush here
            }
        }
    }

    /**
     * Get edit row
     *
     * @return BaseEntity
     */
    protected function getEditRow(BaseEntity $newRow): BaseEntity
    {
        // Load DbValue
        $this->loadDbValues($newRow);

        // PAYMENT_ID
        if (!$this->PAYMENT_ID->ReadOnly) {
            $newRow->setPaymentId($this->PAYMENT_ID->setDbValueDef($this->PAYMENT_ID->CurrentValue));
        }

        // APPOINTMENT_ID
        if (!$this->APPOINTMENT_ID->ReadOnly) {
            $newRow->setAppointmentId($this->APPOINTMENT_ID->setDbValueDef($this->APPOINTMENT_ID->CurrentValue));
        }

        // PATIENT_ID
        if (!$this->PATIENT_ID->ReadOnly) {
            $newRow->setPatientId($this->PATIENT_ID->setDbValueDef($this->PATIENT_ID->CurrentValue));
        }

        // REFUND_AMOUNT
        if (!$this->REFUND_AMOUNT->ReadOnly) {
            $newRow->setRefundAmount($this->REFUND_AMOUNT->setDbValueDef($this->REFUND_AMOUNT->CurrentValue));
        }

        // REFUND_DATE
        if (!$this->REFUND_DATE->ReadOnly) {
            $newRow->setRefundDate($this->REFUND_DATE->setDbValueDef(UnFormatDateTime($this->REFUND_DATE->CurrentValue, $this->REFUND_DATE->formatPattern())));
        }

        // REFUND_STATUS
        if (!$this->REFUND_STATUS->ReadOnly) {
            $newRow->setRefundStatus($this->REFUND_STATUS->setDbValueDef($this->REFUND_STATUS->CurrentValue));
        }

        // REFUND_REASON
        if (!$this->REFUND_REASON->ReadOnly) {
            $newRow->setRefundReason($this->REFUND_REASON->setDbValueDef($this->REFUND_REASON->CurrentValue));
        }

        // REFUND_TXN_ID
        if (!$this->REFUND_TXN_ID->ReadOnly) {
            $newRow->setRefundTxnId($this->REFUND_TXN_ID->setDbValueDef($this->REFUND_TXN_ID->CurrentValue));
        }

        // CREATED_AT
        if (!$this->CREATED_AT->ReadOnly) {
            $newRow->setCreatedAt($this->CREATED_AT->setDbValueDef(UnFormatDateTime($this->CREATED_AT->CurrentValue, $this->CREATED_AT->formatPattern())));
        }
        $this->Fields->setCurrentValues($newRow);
        return $newRow;
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("RefundTblList"), "", $this->TableVar, true);
        $pageId = "edit";
        $breadcrumb->add("edit", $pageId, $url);
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

    // Form Custom Validate event
    public function formCustomValidate(string &$customError): bool
    {
        // Return error message in $customError
        return true;
    }
}

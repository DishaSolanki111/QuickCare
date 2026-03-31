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
#[AsAlias("RefundTblDelete", true)]
class RefundTblDelete extends RefundTbl implements PageInterface
{
    use MessagesTrait;

    // Page result
    public ?Response $Response = null;

    // Headers
    public HeaderBag $Headers;

    // Page ID
    public string $PageID = "delete";

    // Project ID
    public string $ProjectID = PROJECT_ID;

    // View file path
    public ?string $View = null;

    // Title
    public ?string $Title = null; // Title for <title> tag

    // CSS class/style
    public string $CurrentPageName = "RefundTblDelete"; // Route action

    // Page headings
    public string $Heading = "";
    public string $Subheading = "";
    public string $PageHeader = "";
    public string $PageFooter = "";

    // Page layout
    public bool $UseLayout = true;

    // Page terminated
    private bool $terminated = false;
    public int $PageNumber = 1;
    public int $StartRecord = 0;
    public int $TotalRecords = 0;
    public int $RecordCount = 0;
    public array $RecordKeys = [];
    public int $StartRowCount = 1;

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
        $this->TableClass = "table table-bordered table-hover table-sm ew-table";

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
            $this->Response = new RedirectResponse(GetUrl($url), Config("REDIRECT_STATUS_CODE"));
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

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Check records
        if (empty($this->Records) && !$this->CurrentRecord) {
            $this->terminate("RefundTblList"); // Prevent SQL injection, return to list
            return;
        }
        $this->Records = count($this->Records) > 0 ? $this->Records : ($this->CurrentRecord ? [$this->CurrentRecord] : []);

        // Get action
        if (IsApi()) {
            $this->CurrentAction = "delete"; // Delete record directly
        } elseif (Param("action") !== null) {
            $this->CurrentAction = Param("action") == "delete" ? "delete" : "show";
        } else {
            $this->CurrentAction = $this->InlineDelete ?
                "delete" : // Delete record directly
                "show"; // Display record
        }
        if ($this->isDelete()) {
            if ($this->deleteRows()) { // Delete rows
                if (!$this->peekSuccessMessage()) {
                    $this->setSuccessMessage($this->language->phrase("DeleteSuccess")); // Set up success message
                }
                if (IsJsonResponse()) {
                    $this->terminate();
                    return;
                } else {
                    $this->terminate($this->getReturnUrl()); // Return to caller
                    return;
                }
            } else { // Delete failed
                if (IsJsonResponse()) {
                    $this->terminate();
                    return;
                }
                // Return JSON error message if UseAjaxActions
                if ($this->UseAjaxActions) {
                    $this->Response = new JsonResponse(["success" => false, "error" => $this->getFailureMessage()]);
                    $this->terminate();
                    return;
                }
                if ($this->InlineDelete) {
                    $this->terminate($this->getReturnUrl()); // Return to caller
                    return;
                } else {
                    $this->CurrentAction = "show"; // Display record
                }
            }
        }
        if ($this->isShow()) { // Load records for display
            $this->TotalRecords = count($this->Records);
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
        if ($row = $this->fetch(++$this->RecordCount)) {
            $this->RowCount++;

            // Get the field contents
            $this->loadRowValues($row);

            // Render row
            $this->renderRow(RowType::VIEW);
            return true;
        }
        return false;
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

    // Delete current record
    protected function deleteRow(): ?bool
    {
        $records = $this->Records;
        $this->Records = [];
        try {
            return $this->deleteRows();
        } finally {
            $this->Records = $records;
        }
    }

    // Delete records
    protected function deleteRows(): ?bool
    {
        $rows = count($this->Records) > 0 ? $this->Records : ($this->CurrentRecord ? [$this->CurrentRecord] : []);
        if (count($rows) == 0) {
            $this->setFailureMessage($this->language->phrase("NoRecord")); // No record found
            return false;
        }

        // Get Entity Manager
        $em = $this->getEntityManager();
        if ($this->UseTransaction) {
            $em->beginTransaction();
        }

        // Delete row(s)
        $oldRows = [];
        $successKeys = [];
        $failKeys = [];
        $skipRecords = [];
        $rowindex = 0;
        foreach ($rows as $row) {
            $oldRow = clone $row;
            $oldRows[] = $oldRow;
            $rowindex++;
            $key = $row->identifierValuesAsString();

            // Call row deleting event
            $delete = method_exists($this, "rowDeleting") ? $this->rowDeleting($row) : true;
            if ($delete) { // Delete
                try {
                    $updateTableRow = null;
                    if (!$this->UpdateTable || $this->UpdateTable == $this->TableName) { // Update table is the same as current table
                        $em->remove($row);
                    } else { // Update table is different from current table
                        $id = $row->identifierValues();
                        $updateTableRow = $em->find($this->UpdateTableEntityClass, $id);
                        if (!$updateTableRow) {
                            throw new \RuntimeException("Cannot delete: related entity not found.");
                        }
                        $em->detach($row);
                        $em->remove($updateTableRow);
                    }
                    $em->flush();
                } catch (Exception $e) {
                    $this->dispatcher->dispatch(new RowDeleteFailedEvent($updateTableRow ?? $row, $e));
                    $this->setFailureMessage($e->getMessage());
                    $delete = false;
                }
            }
            if ($delete === null) { // Row skipped
                $skipRecords[] = $rowindex . (!IsEmpty($key) ? ": " . $key : ""); // Record count and key if exists
            } elseif ($delete === false) { // Row not deleted
                if ($this->UseTransaction) {
                    $successKeys = []; // Reset success keys
                    break;
                }
                $failKeys[] = $key;
            } elseif ($delete) { // Row deleted
                if (Config("DELETE_UPLOADED_FILES")) { // Delete old files
                    $this->deleteUploadedFiles($oldRow); // Use old row
                }

                // Call Row Deleted event
                if (method_exists($this, "rowDeleted")) {
                    $this->rowDeleted($oldRow); // Use old row
                }
                $successKeys[] = $key;
            }
        }

        // Any records deleted
        $deleted = count($successKeys) > 0;
        if (!$deleted) {
            // Set up error message
            if ($this->peekSuccessMessage() || $this->peekFailureMessage()) {
                // Use the message, do nothing
            } elseif ($this->CancelMessage != "") {
                $this->setFailureMessage($this->CancelMessage);
                $this->CancelMessage = "";
            } else {
                $this->setFailureMessage($this->language->phrase("DeleteCancelled"));
            }
        }
        if ($deleted) {
            if ($this->UseTransaction) { // Commit transaction
                $em->commit();
            }

            // Set warning message if some records skipped
            if (count($skipRecords) > 0) {
                $this->setWarningMessage(sprintf($this->language->phrase("RecordsSkipped"), count($skipRecords)));
                Log("Records skipped", $skipRecords);
            }

            // Set warning message if delete some records failed
            if (count($failKeys) > 0) {
                $this->setWarningMessage(sprintf($this->language->phrase("DeleteRecordsFailed"), count($successKeys), count($failKeys)));
                Log("Delete records failed", ["success" => $successKeys, "failure" => $failKeys]);
            }
        } else {
            if ($this->UseTransaction) { // Rollback transaction
                $em->rollback();
            }
        }

        // Create JSON response
        if ((IsJsonResponse() || IsInfiniteScroll()) && $deleted) {
            $rows = $this->getRowsFromEntities($oldRows);
            $table = $this->TableVar;
            if (Param("key_m") === null) { // Single delete
                $rows = $rows[0]; // Return object
            }
            $this->Response = new JsonResponse(["success" => true, "action" => Config("API_DELETE_ACTION"), $table => $rows]);
        }
        return $deleted;
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb(): void
    {
        $breadcrumb = Breadcrumb();
        $url = CurrentUrl();
        $breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("RefundTblList"), "", $this->TableVar, true);
        $pageId = "delete";
        $breadcrumb->add("delete", $pageId, $url);
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
}

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
 * Table class for refund_tbl
 */
#[AsAlias("RefundTbl", true)]
#[AsAlias("refund_tbl", true)]
#[AsEntityListener(event: Events::postPersist, method: 'clearCache', entity: Entity\RefundTbl::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clearCache', entity: Entity\RefundTbl::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clearCache', entity: Entity\RefundTbl::class)]
#[AsEntityListener(event: Events::postPersist, method: 'updateLastInsertId', entity: Entity\RefundTbl::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Entity\RefundTbl::class)]
class RefundTbl extends DbTable implements LookupTableInterface
{
    protected array $changeSets = [];
    protected array $snapshots = [];
    protected ?string $sqlSelectFields = null;
    protected ?string $sqlFromDerived = null;
    protected ?string $sqlFrom = null;
    protected ?string $sqlWhere = null;
    protected ?string $sqlGroupBy = null;
    protected ?string $sqlHaving = null;
    protected ?string $sqlOrderBy = null;
    public bool $UseSessionForListSql = true;

    // Entity alias (Use first character of table variable name)
    public string $Alias = 'r';

    // Column CSS classes
    public string $LeftColumnClass = "col-sm-2 col-form-label ew-label";
    public string $RightColumnClass = "col-sm-10";
    public string $OffsetColumnClass = "col-sm-10 offset-sm-2";
    public string $TableLeftColumnClass = "w-col-2";

    // Ajax/Modal
    public bool $UseAjaxActions = false;
    public bool $ModalSearch = false;
    public bool $ModalView = false;
    public bool $ModalAdd = false;
    public bool $ModalEdit = false;
    public bool $ModalUpdate = false;
    public bool $InlineDelete = false;
    public bool $ModalGridAdd = false;
    public bool $ModalGridEdit = false;
    public bool $ModalMultiEdit = false;

    // Fields
    public DbField $REFUND_ID;
    public DbField $PAYMENT_ID;
    public DbField $APPOINTMENT_ID;
    public DbField $PATIENT_ID;
    public DbField $REFUND_AMOUNT;
    public DbField $REFUND_DATE;
    public DbField $REFUND_STATUS;
    public DbField $REFUND_REASON;
    public DbField $REFUND_TXN_ID;
    public DbField $CREATED_AT;

    // Page ID
    public string $PageID = ""; // To be set by subclass

    // Constructor
    public function __construct(
        Language $language,
        AdvancedSecurity $security,
        CSPBuilder $cspBuilder,
        CacheInterface $cache,
        FieldFactory $fieldFactory,
        EventDispatcherInterface $dispatcher,
    ) {
        global $httpContext;
        parent::__construct($language, $security, $cspBuilder, $cache, $fieldFactory, $dispatcher);
        $this->TableVar = "refund_tbl";
        $this->TableName = 'refund_tbl';
        $this->TableType = "TABLE";
        $this->SortType = 1; // Sort Type
        $this->ImportUseTransaction = $this->supportsTransaction() && Config("IMPORT_USE_TRANSACTION");
        $this->UseTransaction = $this->supportsTransaction() && Config("USE_TRANSACTION");
        $this->EntityClass = Entity\RefundTbl::class;
        $this->Dbid = 'DB';
        $this->ExportAll = true;
        $this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)

        // PDF
        $this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
        $this->ExportPageSize = "a4"; // Page size (PDF only)

        // PhpSpreadsheet
        $this->ExportExcelPageOrientation = null; // Page orientation (PhpSpreadsheet only)
        $this->ExportExcelPageSize = null; // Page size (PhpSpreadsheet only)

        // PHPWord
        $this->ExportWordPageOrientation = ""; // Page orientation (PHPWord only)
        $this->ExportWordPageSize = ""; // Page orientation (PHPWord only)
        $this->ExportWordColumnWidth = null; // Cell width (PHPWord only)
        $this->DetailAdd = false; // Allow detail add
        $this->DetailEdit = false; // Allow detail edit
        $this->DetailView = false; // Allow detail view
        $this->ShowMultipleDetails = false; // Show multiple details
        $this->GridAddRowCount = 5;
        $this->AllowAddDeleteRow = true; // Allow add/delete row
        $this->UseAjaxActions = $this->UseAjaxActions || Config("USE_AJAX_ACTIONS");
        $this->UserIDPermission = Config("DEFAULT_USER_ID_PERMISSION"); // Default User ID permission
        $this->BasicSearch = new BasicSearch($this, Session(), $this->language);

        // Create fields
        $this->Fields = $this->fieldFactory->createAll($this);

        // REFUND_ID
        $this->REFUND_ID = $this->Fields['REFUND_ID'];
        $this->REFUND_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // PAYMENT_ID
        $this->PAYMENT_ID = $this->Fields['PAYMENT_ID'];
        $this->PAYMENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // APPOINTMENT_ID
        $this->APPOINTMENT_ID = $this->Fields['APPOINTMENT_ID'];
        $this->APPOINTMENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // PATIENT_ID
        $this->PATIENT_ID = $this->Fields['PATIENT_ID'];
        $this->PATIENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // REFUND_AMOUNT
        $this->REFUND_AMOUNT = $this->Fields['REFUND_AMOUNT'];
        $this->REFUND_AMOUNT->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // REFUND_DATE
        $this->REFUND_DATE = $this->Fields['REFUND_DATE'];
        $this->REFUND_DATE->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));

        // REFUND_STATUS
        $this->REFUND_STATUS = $this->Fields['REFUND_STATUS'];
        $this->REFUND_STATUS->Lookup = new Lookup($this->REFUND_STATUS, 'refund_tbl', false, '', ["","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->REFUND_STATUS->OptionCount = 3;

        // REFUND_REASON
        $this->REFUND_REASON = $this->Fields['REFUND_REASON'];

        // REFUND_TXN_ID
        $this->REFUND_TXN_ID = $this->Fields['REFUND_TXN_ID'];

        // CREATED_AT
        $this->CREATED_AT = $this->Fields['CREATED_AT'];
        $this->CREATED_AT->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));

        // Call Table Load event
        $this->tableLoad();
    }

    // Get field settings
    public function getFieldDefinitions(): array
    {
        return [
            'REFUND_ID' => [
                'FieldVar' => 'x_REFUND_ID', // Field variable name
                'Param' => 'REFUND_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'refundId', // Field entity property name
                'Expression' => '`REFUND_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`REFUND_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`REFUND_ID`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'NO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'IsAutoIncrement' => true,
                'IsPrimaryKey' => true,
                'Nullable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'PAYMENT_ID' => [
                'FieldVar' => 'x_PAYMENT_ID', // Field variable name
                'Param' => 'PAYMENT_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'paymentId', // Field entity property name
                'Expression' => '`PAYMENT_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`PAYMENT_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`PAYMENT_ID`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'APPOINTMENT_ID' => [
                'FieldVar' => 'x_APPOINTMENT_ID', // Field variable name
                'Param' => 'APPOINTMENT_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'appointmentId', // Field entity property name
                'Expression' => '`APPOINTMENT_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`APPOINTMENT_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`APPOINTMENT_ID`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'PATIENT_ID' => [
                'FieldVar' => 'x_PATIENT_ID', // Field variable name
                'Param' => 'PATIENT_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'patientId', // Field entity property name
                'Expression' => '`PATIENT_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`PATIENT_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`PATIENT_ID`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'REFUND_AMOUNT' => [
                'FieldVar' => 'x_REFUND_AMOUNT', // Field variable name
                'Param' => 'REFUND_AMOUNT', // Field parameter name (Table class property name)
                'PropertyName' => 'refundAmount', // Field entity property name
                'Expression' => '`REFUND_AMOUNT`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`REFUND_AMOUNT`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 12, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`REFUND_AMOUNT`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'REFUND_DATE' => [
                'FieldVar' => 'x_REFUND_DATE', // Field variable name
                'Param' => 'REFUND_DATE', // Field parameter name (Table class property name)
                'PropertyName' => 'refundDate', // Field entity property name
                'Expression' => '`REFUND_DATE`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`REFUND_DATE`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 133, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`REFUND_DATE`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'REFUND_STATUS' => [
                'FieldVar' => 'x_REFUND_STATUS', // Field variable name
                'Param' => 'REFUND_STATUS', // Field parameter name (Table class property name)
                'PropertyName' => 'refundStatus', // Field entity property name
                'Expression' => '`REFUND_STATUS`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`REFUND_STATUS`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 9, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`REFUND_STATUS`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'RADIO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>"],
                'OptionCount' => 3,
            ],
            'REFUND_REASON' => [
                'FieldVar' => 'x_REFUND_REASON', // Field variable name
                'Param' => 'REFUND_REASON', // Field parameter name (Table class property name)
                'PropertyName' => 'refundReason', // Field entity property name
                'Expression' => '`REFUND_REASON`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`REFUND_REASON`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 255, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`REFUND_REASON`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Nullable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY"],
            ],
            'REFUND_TXN_ID' => [
                'FieldVar' => 'x_REFUND_TXN_ID', // Field variable name
                'Param' => 'REFUND_TXN_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'refundTxnId', // Field entity property name
                'Expression' => '`REFUND_TXN_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`REFUND_TXN_ID`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 50, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`REFUND_TXN_ID`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY"],
            ],
            'CREATED_AT' => [
                'FieldVar' => 'x_CREATED_AT', // Field variable name
                'Param' => 'CREATED_AT', // Field parameter name (Table class property name)
                'PropertyName' => 'createdAt', // Field entity property name
                'Expression' => '`CREATED_AT`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`CREATED_AT`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 135, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 19, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`CREATED_AT`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN", "IS NULL", "IS NOT NULL"],
            ],
        ];
    }

    // Field Visibility
    public function getFieldVisibility(string $fldParm): bool
    {
        return $this->$fldParm->Visible; // Returns original value
    }

    // Set left column class (must be predefined col-*-* classes of Bootstrap grid system)
    public function setLeftColumnClass(string $class): void
    {
        if (preg_match('/^col\-(\w+)\-(\d+)$/', $class, $match)) {
            $this->LeftColumnClass = $class . " col-form-label ew-label";
            $this->RightColumnClass = "col-" . $match[1] . "-" . strval(12 - (int)$match[2]);
            $this->OffsetColumnClass = $this->RightColumnClass . " " . str_replace("col-", "offset-", $class);
            $this->TableLeftColumnClass = preg_replace('/^col-\w+-(\d+)$/', "w-col-$1", $class); // Change to w-col-*
        }
    }

    // Single column sort
    public function updateSort(DbField $fld): void
    {
        if ($this->CurrentOrder == $fld->Name) {
            $sortField = $fld->Expression;
            $lastSort = $fld->getSort();
            if (in_array($this->CurrentOrderType, ["ASC", "DESC", "NO"])) {
                $curSort = $this->CurrentOrderType;
            } else {
                $curSort = $lastSort;
            }
            $orderBy = in_array($curSort, ["ASC", "DESC"]) ? $sortField . " " . $curSort : "";
            $this->setSessionOrderBy($orderBy); // Save to Session
        }
    }

    // Update field sort
    public function updateFieldSort(): void
    {
        $orderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
        $flds = GetSortFields($orderBy);
        foreach ($this->Fields as $field) {
            $fldSort = "";
            foreach ($flds as $fld) {
                if ($fld[0] == $field->Expression || $fld[0] == $field->VirtualExpression) {
                    $fldSort = $fld[1];
                }
            }
            $field->setSort($fldSort);
        }
    }

    // Render X Axis for chart
    public function renderChartXAxis(string $chartVar, array $chartRow): array
    {
        return $chartRow;
    }

    // Get FROM clause
    public function getSqlFrom(): string
    {
        return $this->sqlFrom ?? "refund_tbl";
    }

    // Set FROM clause
    public function setSqlFrom(string $v): void
    {
        $this->sqlFrom = $v;
    }

    // Get SELECT clause
    public function getSqlSelect(): QueryBuilder // Select
    {
        return $this->getQueryBuilder()->select($this->getSqlSelectFields());
    }

    // Get list of fields
    public function getSqlSelectFields(): string
    {
        if ($this->sqlSelectFields) {
            return $this->sqlSelectFields;
        }
        $fieldNames = [];
        $platform = $this->getConnection()->getDatabasePlatform();
        foreach ($this->Fields as $field) {
            $expr = $field->Expression;
            $customExpr = $field->CustomDataType?->convertToPHPValueSQL($expr, $platform) ?? $expr;
            if ($customExpr != $expr) {
                $fieldNames[] = $customExpr . " AS " . QuotedName($field->Name, $this->Dbid);
            } else {
                $fieldNames[] = $expr;
            }
        }
        return implode(", ", $fieldNames);
    }

    // Set list of fields
    public function setSqlSelectFields(string $v): void
    {
        $this->sqlSelectFields = $v;
    }

    // Get default filter
    public function getDefaultFilter(): string
    {
        return "";
    }

    // Get WHERE clause
    public function getSqlWhere(bool $delete = false): string
    {
        $where = $this->sqlWhere ?? "";
        AddFilter($where, $this->getDefaultFilter());
        if (!$delete && !IsEmpty($this->SoftDeleteFieldName) && $this->UseSoftDeleteFilter) { // Add soft delete filter
            AddFilter($where, $this->Fields[$this->SoftDeleteFieldName]->Expression . " IS NULL");
            if ($this->TimeAware) { // Add time aware filter
                AddFilter($where, $this->Fields[$this->SoftDeleteFieldName]->Expression . " > " . $this->getConnection()->getDatabasePlatform()->getCurrentTimestampSQL(), "OR");
            }
        }
        return $where;
    }

    // Set WHERE clause
    public function setSqlWhere(string $v): void
    {
        $this->sqlWhere = $v;
    }

    // Get GROUP BY clause
    public function getSqlGroupBy(): string
    {
        return $this->sqlGroupBy ?? "";
    }

    // set GROUP BY clause
    public function setSqlGroupBy(string $v): void
    {
        $this->sqlGroupBy = $v;
    }

    // Get HAVING clause
    public function getSqlHaving(): string // Having
    {
        return $this->sqlHaving ?? "";
    }

    // Set HAVING clause
    public function setSqlHaving(string $v): void
    {
        $this->sqlHaving = $v;
    }

    // Get ORDER BY clause
    public function getSqlOrderBy(): string
    {
        return $this->sqlOrderBy ?? "";
    }

    // set ORDER BY clause
    public function setSqlOrderBy(string $v): void
    {
        $this->sqlOrderBy = $v;
    }

    // Apply User ID filters
    public function applyUserIDFilters(string $filter = "", string $id = ""): string
    {
        return $filter;
    }

    // Check if User ID security allows view all
    public function userIDAllow(string $id = ""): bool
    {
        $allow = $this->UserIDPermission;
        return match ($id) {
            "add", "copy", "gridadd", "register", "addopt" => ($allow & Allow::ADD->value) == Allow::ADD->value,
            "edit", "gridedit", "update", "changepassword", "resetpassword" => ($allow & Allow::EDIT->value) == Allow::EDIT->value,
            "delete" => ($allow & Allow::DELETE->value) == Allow::DELETE->value,
            "view" => ($allow & Allow::VIEW->value) == Allow::VIEW->value,
            "search" => ($allow & Allow::SEARCH->value) == Allow::SEARCH->value,
            "lookup" => ($allow & Allow::LOOKUP->value) == Allow::LOOKUP->value,
            default => ($allow & Allow::LIST->value) == Allow::LIST->value
        };
    }

    /**
     * Get record count
     *
     * @param string|QueryBuilder $sql SQL or QueryBuilder
     * @param Connection $c Connection
     * @return int
     */
    public function getRecordCount(string|QueryBuilder $sql, ?Connection $c = null): int
    {
        $cnt = -1;
        $sqlwrk = $sql instanceof QueryBuilder // Query builder
            ? (clone $sql)->resetOrderBy()->getSQL()
            : $sql;
        $pattern = '/^SELECT\s([\s\S]+?)\sFROM\s/i';
        // Skip Custom View / SubQuery / SELECT DISTINCT / ORDER BY
        if (
            in_array($this->TableType, ["TABLE", "VIEW", "LINKTABLE"])
            && preg_match($pattern, $sqlwrk)
            && !preg_match('/\(\s*(SELECT[^)]+)\)/i', $sqlwrk)
            && !preg_match('/^\s*SELECT\s+DISTINCT\s+/i', $sqlwrk)
            && !preg_match('/\s+ORDER\s+BY\s+/i', $sqlwrk)
        ) {
            $sqlcnt = "SELECT COUNT(*) FROM " . preg_replace($pattern, "", $sqlwrk);
        } else {
            $sqlcnt = "SELECT COUNT(*) FROM (" . $sqlwrk . ") COUNT_TABLE";
        }
        $conn = $c ?? $this->getConnection();
        $cnt = $conn->fetchOne($sqlcnt);
        if ($cnt !== false) {
            return (int)$cnt;
        }
        // Unable to get count by SELECT COUNT(*), execute the SQL to get record count directly
        $result = $conn->executeQuery($sqlwrk);
        $cnt = $result->rowCount();
        if ($cnt == 0) { // Unable to get record count, count directly
            while ($result->fetchAssociative()) {
                $cnt++;
            }
        }
        return $cnt;
    }

    // Get QueryBuilder
    public function getSqlBuilder(string $where, string $orderBy = "", bool $delete = false): QueryBuilder
    {
        return $this->buildSelectSql(
            $this->getSqlSelect(),
            $this->getSqlFrom(),
            $this->getSqlWhere($delete),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $where,
            $orderBy
        );
    }

    // Get QueryBuilder (alias)
    public function getSql(string $where, string $orderBy = "", bool $delete = false): QueryBuilder
    {
        return $this->getSqlBuilder($where, $orderBy, $delete);
    }

    // Table SQL
    public function getCurrentSql(bool $delete = false): QueryBuilder
    {
        $filter = $this->CurrentFilter;
        $filter = $this->applyUserIDFilters($filter);
        $sort = $this->getSessionOrderBy();
        return $this->getSql($filter, $sort, $delete);
    }

    /**
     * Table SQL with List page filter
     *
     * @return QueryBuilder
     */
    public function getListSql(): QueryBuilder
    {
        $filter = $this->UseSessionForListSql ? $this->getSessionWhere() : "";
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsSelecting($filter);
        $select = $this->getSqlSelect();
        $from = $this->getSqlFrom();
        $sort = $this->UseSessionForListSql ? $this->getSessionOrderBy() : "";
        $this->Sort = $sort;
        return $this->buildSelectSql(
            $select,
            $from,
            $this->getSqlWhere(),
            $this->getSqlGroupBy(),
            $this->getSqlHaving(),
            $this->getSqlOrderBy(),
            $filter,
            $sort
        );
    }

    // Get ORDER BY clause
    public function getOrderBy(): string
    {
        $orderBy = $this->getSqlOrderBy();
        $sort = $this->getSessionOrderBy();
        if ($orderBy != "" && $sort != "") {
            $orderBy .= ", " . $sort;
        } elseif ($sort != "") {
            $orderBy = $sort;
        }
        return $orderBy;
    }

    // Get record count based on filter
    public function loadRecordCount($filter, $delete = false): int
    {
        $origFilter = $this->CurrentFilter;
        $this->CurrentFilter = $filter;
        if ($delete == false) {
            $this->recordsSelecting($this->CurrentFilter);
        }
        $isCustomView = $this->TableType == "CUSTOMVIEW";
        $select = $isCustomView ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $isCustomView ? $this->getSqlGroupBy() : "";
        $having = $isCustomView ? $this->getSqlHaving() : "";
        $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere($delete), $groupBy, $having, "", $this->CurrentFilter, "");
        $cnt = $this->getRecordCount($sql);
        $this->CurrentFilter = $origFilter;
        return $cnt;
    }

    // Get record count (for current List page)
    public function listRecordCount(): int
    {
        $filter = $this->getSessionWhere();
        AddFilter($filter, $this->CurrentFilter);
        $filter = $this->applyUserIDFilters($filter);
        $this->recordsSelecting($filter);
        $isCustomView = $this->TableType == "CUSTOMVIEW";
        $select = $isCustomView ? $this->getSqlSelect() : $this->getQueryBuilder()->select("*");
        $groupBy = $isCustomView ? $this->getSqlGroupBy() : "";
        $having = $isCustomView ? $this->getSqlHaving() : "";
        $sql = $this->buildSelectSql($select, $this->getSqlFrom(), $this->getSqlWhere(), $groupBy, $having, "", $filter, "");
        $cnt = $this->getRecordCount($sql);
        return $cnt;
    }

    /**
     * Inserts a table row with specified data
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param array<string, mixed> $data (unquoted column name as key)
     * @param array<string, ParameterType> $types (quoted column name as key)
     *
     * @return int|numeric-string The number of affected rows.
     *
     * @throws Exception
     */
    public function insert(array $data, array $types = []): int|string
    {
        $conn = $this->getConnection();
        $table = $conn->quoteSingleIdentifier($this->TableName);

        // Quote the key of $data and set up $types
        $quotedData = [];
        foreach ($data as $key => $value) {
            $quotedKey = $conn->quoteSingleIdentifier($key);
            $quotedData[$quotedKey] = $value instanceof DateTimeInterface ? ConvertToString($value) : $value;
            $types[$quotedKey] ??= $this->Fields[$key]->getParameterType();
        }
        return $conn->insert($table, $quotedData, $types);
    }

    /**
     * Executes an SQL UPDATE statement on a table
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param array<string, mixed> $data (unquoted column name as key)
     * @param array<string, mixed> $criteria (unquoted column name as key)
     * @param array<string, ParameterType> $types (quoted column name as key)
     *
     * @return int|numeric-string The number of affected rows.
     *
     * @throws Exception
     */
    public function update(array $data, array $criteria = [], array $types = []): int|string
    {
        $conn = $this->getConnection();
        $table = $conn->quoteSingleIdentifier($this->TableName);

        // Quote the key of $data and set up $types
        $quotedData = [];
        foreach ($data as $key => $value) {
            $quotedKey = $conn->quoteSingleIdentifier($key);
            $quotedData[$quotedKey] = $value instanceof DateTimeInterface ? ConvertToString($value) : $value;
            $types[$quotedKey] ??= $this->Fields[$key]->getParameterType();
        }

        // Quote the key of $criteria and set up $types
        $quotedCriteria = [];
        foreach ($criteria as $key => $value) {
            $quotedKey = $conn->quoteSingleIdentifier($key);
            $quotedCriteria[$quotedKey] = $value instanceof DateTimeInterface ? ConvertToString($value) : $value;
            $types[$quotedKey] ??= $this->Fields[$key]->getParameterType();
        }
        return $conn->update($table, $quotedData, $quotedCriteria, $types);
    }

    /**
     * Executes an SQL DELETE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param array<string, mixed> $criteria (unquoted column name as key)
     * @param array<string, ParameterType> $types (quoted column name as key)
     *
     * @return int|numeric-string The number of affected rows.
     *
     * @throws Exception
     */
    public function delete(array $criteria = [], array $types = []): int|string
    {
        $conn = $this->getConnection();
        $table = $conn->quoteSingleIdentifier($this->TableName);

        // Quote the key of $criteria and set up $types
        $quotedCriteria = [];
        foreach ($criteria as $key => $value) {
            $quotedKey = $conn->quoteSingleIdentifier($key);
            $quotedCriteria[$quotedKey] = $value instanceof DateTimeInterface ? ConvertToString($value) : $value;
            $types[$quotedKey] ??= $this->Fields[$key]->getParameterType();
        }
        return $conn->delete($table, $quotedCriteria, $types);
    }

    /**
     * Clear cache for this table
     */
    public function clearCache(Entity\RefundTbl $row, LifecycleEventArgs $args): void
    {
        // Clear lookup cache
        $this->cache->clear("lookup.result." . $this->TableVar . ".");

        // Optionally prune expired entries if supported
        if ($this->cache instanceof PruneableInterface) {
            $this->cache->prune();
        }
    }

    // Load DbValue from result set or array
    protected function loadDbValues(?BaseEntity $row)
    {
        if ($row === null) {
            return;
        }
        $this->REFUND_ID->DbValue = $row->getRefundId();
        $this->PAYMENT_ID->DbValue = $row->getPaymentId();
        $this->APPOINTMENT_ID->DbValue = $row->getAppointmentId();
        $this->PATIENT_ID->DbValue = $row->getPatientId();
        $this->REFUND_AMOUNT->DbValue = $row->getRefundAmount();
        $this->REFUND_DATE->DbValue = $row->getRefundDate();
        $this->REFUND_STATUS->DbValue = $row->getRefundStatus();
        $this->REFUND_REASON->DbValue = $row->getRefundReason();
        $this->REFUND_TXN_ID->DbValue = $row->getRefundTxnId();
        $this->CREATED_AT->DbValue = $row->getCreatedAt();
    }

    // Delete uploaded files
    public function deleteUploadedFiles(BaseEntity $row)
    {
        $this->loadDbValues($row);
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter(): string
    {
        return "`REFUND_ID` = @REFUND_ID@";
    }

    /**
     * Get key from CurrentValue/OldValue
     *
     * @param bool $current Current value
     * @return array
     */
    public function getKey(bool $current = false): array
    {
        $keys = [];
        $val = $current ? $this->REFUND_ID->CurrentValue : $this->REFUND_ID->OldValue;
        if (IsEmpty($val)) {
            return [];
        } else {
            // Explicitly convert DateTime objects to string format
            $val = $val instanceof DateTimeInterface ? ConvertToString($val) : $val;
            $keys["REFUND_ID"] = $val;
        }
        return $keys;
    }

    /**
     * Get key as array for use with UrlFor()
     *
     * @param bool $current Current value
     * @return string
     */
    public function getUrlKey(bool $current = false): array
    {
        $keys = ["refundId"];
        $values = array_values($this->getKey($current));
        return count($keys) == count($values) ? array_combine($keys, $values) : [];
    }

    /**
     * Set key
     *
     * @param array|string $key Key
     * @param bool $current Current value
     * @param ?string $keySeparator Key separator
     */
    public function setKey(array|string $key, bool $current = false, ?string $keySeparator = null): void
    {
        $recordKey = [];
        if (is_string($key)) {
            $keySeparator ??= Config("COMPOSITE_KEY_SEPARATOR");
            $ar = explode($keySeparator, $key);
            if (count($ar) == 1) {
                $recordKey["REFUND_ID"] = $ar[0];
            }
        } else {
            $recordKey = $key;
        }
        if (count($recordKey) == 1) {
            foreach ($recordKey as $name => $value) {
                if (isset($this->Fields[$name])) {
                    if ($current) {
                        $this->Fields[$name]->CurrentValue = $value;
                    } else {
                        $this->Fields[$name]->OldValue = $value;
                    }
                }
            }
            $this->OldKey = $recordKey;
        }
    }

    /**
     * Get record key as array
     *
     * @param array|BaseEntity|null $row Row
     * @param bool $current Use current value
     * @return array
     */
    public function getKeyAsArray(array|BaseEntity|null $row = null, bool $current = false): array
    {
        $recordKey = [];
        if (is_array($row) || $row instanceof BaseEntity) {
            $val = $row['REFUND_ID'] ?? null;
        } else {
            $val = !IsEmpty($this->REFUND_ID->OldValue) && !$current ? $this->REFUND_ID->OldValue : $this->REFUND_ID->CurrentValue;
        }
        if (!is_numeric($val)) {
            return []; // Invalid value
        }
        if ($val === null) {
            return []; // Invalid value
        }
        $recordKey["REFUND_ID"] = $val;
        return $recordKey;
    }

    /**
     * Get Key from row as string
     *
     * @param array|BaseEntity|null $row Row
     * @param bool $current Use current value
     * @param ?string $separator Separator
     * @return string
     */
    public function getKeyAsString(array|BaseEntity|null $row = null, bool $current = false, ?string $separator = null): string
    {
        $keys = array_values($this->getKeyAsArray($row, $current)); // DateTime fields are already formatted
        $separator ??= Config("COMPOSITE_KEY_SEPARATOR"); // Use COMPOSITE_KEY_SEPARATOR as default separator
        if ($separator == Config("ROUTE_COMPOSITE_KEY_SEPARATOR")) { // Key as route parameter
            $keys = array_map(fn ($key) => rawurlencode($key), $keys); // URL-encode the values
        }
        return implode($separator, $keys);
    }

    /**
     * Get record filter (as string)
     *
     * @param array|BaseEntity|null $row Row
     * @param bool $current Use current value
     * @return string
     */
    public function getRecordFilter(array|BaseEntity|null $row = null, bool $current = false): string
    {
        $filter = $this->arrayToFilter($this->getKeyAsArray($row, $current));
        return IsEmpty($filter) ? "0=1" : $filter;
    }

    // Return page URL
    public function getReturnUrl(): string
    {
        $referUrl = ReferUrl();
        $referPageName = ReferPageName();
        $name = AddTabId(PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL"));
        // Get referer URL automatically
        if ($referUrl != "" && $referPageName != CurrentPageName() && $referPageName != "login") { // Referer not same page or login page
            Session($name, $referUrl); // Save to Session
        }
        return Session($name) ?? GetUrl("RefundTblList");
    }

    // Set return page URL
    public function setReturnUrl(string $v): void
    {
        Session(AddTabId(PROJECT_NAME . "_" . $this->TableVar . "_" . Config("TABLE_RETURN_URL")), $v);
    }

    // Get modal caption
    public function getModalCaption(string $pageName): string
    {
        return match ($pageName) {
            "RefundTblView" => $this->language->phrase("View"),
            "RefundTblEdit" => $this->language->phrase("Edit"),
            "RefundTblAdd" => $this->language->phrase("Add"),
            default => ""
        };
    }

    // Default route URL
    public function getDefaultRouteUrl(): string
    {
        return "RefundTblList";
    }

    // Current URL
    public function getCurrentUrl(string $param = ""): string
    {
        $url = CurrentPageUrl(false);
        if ($param != "") {
            $url = $this->keyUrl($url, $param);
        } else {
            $url = $this->keyUrl($url, Config("TABLE_SHOW_DETAIL") . "=");
        }
        return $this->addMasterUrl($url);
    }

    // List URL
    public function getListUrl(): string
    {
        return "RefundTblList";
    }

    // View URL
    public function getViewUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("RefundTblView", $params);
        return $this->addMasterUrl($url);
    }

    // Detail view URL
    public function getDetailViewUrl(string $param = ""): string
    {
        $url = $this->keyUrl("RefundTblView", [$param]);
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl(string $param = ""): string
    {
        $url = BuildUrl("RefundTblAdd", $param);
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("RefundTblEdit", $params);
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl(): string
    {
        $url = $this->keyUrl("RefundTblList", "action=edit");
        return $this->addMasterUrl($url);
    }

    // Detail edit URL
    public function getDetailEditUrl(string $param = ""): string
    {
        $url = $this->keyUrl("RefundTblEdit", [$param]);
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("RefundTblAdd", $params);
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl(): string
    {
        $url = $this->keyUrl("RefundTblList", "action=copy");
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl(string $param = ""): string
    {
        if ($this->UseAjaxActions && IsInfiniteScroll() && CurrentPageID() == "list") {
            return $this->keyUrl(GetApiUrl(Config("API_DELETE_ACTION") . "/" . $this->TableVar));
        } else {
            return $this->keyUrl("RefundTblDelete", $param);
        }
    }

    // Add master url
    public function addMasterUrl(string $url): string
    {
        if ($url == "") {
            return "";
        }
        return $url;
    }

    public function keyToJson(bool $htmlEncode = false): string
    {
        $json = "";
        $json .= "\"REFUND_ID\":" . VarToJson($this->REFUND_ID->CurrentValue, "number");
        $json = "{" . $json . "}";
        return $htmlEncode ? HtmlEncode($json) : $json;
    }

    // Add key value to URL
    public function keyUrl(string $url, string|array $params = ""): string
    {
        if ($this->REFUND_ID->CurrentValue !== null) {
            $url .= "/" . $this->rawUrlEncode($this->REFUND_ID->CurrentValue);
        } else {
            return AllowInlineScript() ? "javascript:ew.alert(ew.language.phrase('InvalidRecord'));" : "";
        }
        return BuildUrl($url, $params);
    }

    /**
     * Get record keys from Post/Get/Route
     *
     * @return array
     */
    public function getRecordKeys(): array
    {
        $keys = [];
        if (Param("key_m") !== null) {
            $keys = Param("key_m");
            $cnt = count($keys);
            for ($i = 0; $i < $cnt; $i++) {
                $keys[$i] = [$keys[$i]];
            }
        } else {
            $isApi = IsApi();
            if (($keyValue = Param("REFUND_ID") ?? Route("refundId")) !== null) {
                $keys[] = [$keyValue];
            } elseif ($isApi && ($keyValue = Key(0)) !== null) {
                $keys[] = [$keyValue];
            }
        }

        // Check and set up keys
        $recordKeys = [];
        foreach ($keys as $key) {
            if (count($key) != 1) {
                continue; // Just skip so other keys will still work
            }
            $recordKey = [];
            if (!is_numeric($key[0])) { // REFUND_ID
                continue;
            }
            $recordKey["REFUND_ID"] = $key[0];
            $recordKeys[] = $recordKey;
        }
        return $recordKeys;
    }

    // Get filter from records
    public function getFilterFromRecords(array $rows): string
    {
        return implode(" OR ", array_map(fn($row) => "(" . $this->getRecordFilter($row) . ")", $rows));
    }

    // Get filter from record keys
    public function getFilterFromRecordKeys(): string
    {
        return $this->getFilterFromRecords($this->getRecordKeys());
    }

    /**
     * Load entity by filter
     *
     * @param string|array $filter Filter
     * @return Entity
     */
    public function loadEntity(string|array $filter): BaseEntity
    {
        return $this->loadEntitiesFromFilter($filter)[0] ?? null;
    }

    /**
     * Load entities from filter/sort
     *
     * @param string|array $filter Filter
     * @param string $sort Order By
     * @return array of entities
     */
    public function loadEntitiesFromFilter(string|array $filter, string $sort = ""): array
    {
        if (is_array($filter)) {
            $filter = $this->arrayToFilter($filter);
        }
        $sql = $this->getSql($filter, $sort); // Set up filter (WHERE Clause) / sort (ORDER BY Clause)
        return $this->loadEntities($sql);
    }

    /**
     * Load entities from DBAL query builder
     *
     * @param QueryBuilder $sql
     * @return array of entities
     */
    public function loadEntities(QueryBuilder $sql): array
    {
        $em = $this->getEntityManager();
        $meta = $em->getClassMetadata($this->EntityClass);
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata($this->EntityClass, $this->Alias);
        $customFields = [];
        foreach ($this->getFieldDefinitions() as $column => $def) {
            if (($def["IsCustom"] ?? false) && isset($def["Expression"])) {
                $property = GetFieldName($meta, $column);
                $rsm->addScalarResult($column, $property);
                $customFields[] = $column;
            }
        }
        $query = $em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        // Unable to get results, use DBAL
        if (IsEmpty($results)) {
            $rows = $sql->fetchAllAssociative();
            return array_map([$this->EntityClass, "createFromArray"], $rows);
        }

        // Skip post-processing if no custom fields
        if (!$customFields) {
            return $results;
        }
        $entities = [];
        foreach ($results as $row) {
            if (is_array($row)) {
                $entity = $row[0];
                foreach ($customFields as $field) {
                    if (array_key_exists($field, $row)) {
                        $entity[$field] = $row[$field];
                    }
                }
            } else {
                $entity = $row;
            }
            $entities[] = $entity;
        }
        return $entities;
    }

    // Load row values from record
    public function loadListRowValues(array|BaseEntity $row): void
    {
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

    // Render list content
    public function renderListContent(array|BaseEntity $data, int $totalRecords)
    {
        global $httpContext;
        $container = Container();
        $page = $container->get("RefundTblList");
        if (is_array($data)) {
            $page->loadRecordsFromArray($data, $totalRecords);
        } else {
            $page->loadRecordsFromArray([$data], $totalRecords);
        }
        $view = $container->get("app.view");
        $template = GetClassShortName($page) . ".php"; // View
        $TokenName = Config('CSRF_TOKEN.id'); // Token id/name, e.g. 'submit', 'authenticate'
        $TokenValue = CsrfToken($TokenName); // Cookie name, e.g. 'csrf-token'
        $viewData = [
            'Page' => $page,
            'Title' => $page->Title, // Title
            'Language' => $this->language,
            'Security' => $this->security,
            'TokenNameKey' => Config('CSRF_TOKEN.id_key'), // '_csrf_id', reuse $TokenNameKey for backward compatibility
            'TokenName' => $TokenName, // 'submit' or 'authenticate', reuse $TokenName for backward compatibility
            'TokenValueKey' => Config('CSRF_TOKEN.value_key'), // '_csrf_token'
            'TokenValue' => $TokenValue, // Cookie name, e.g. 'csrf-token'
            'DashboardReport' => $httpContext["DashboardReport"], // Dashboard report
            'SkipHeaderFooter' => $httpContext["SkipHeaderFooter"],
        ];
        try {
            $this->Response = $view->render(new Response(), $template, $viewData);
        } finally {
            $page->terminate(); // Terminate page and clean up
        }
    }

    // Render list row values
    public function renderListRow()
    {
        global $httpContext;

        // Call Row Rendering event
        $this->rowRendering();

        // Common render codes

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

        // Call Row Rendered event
        $this->rowRendered();

        // Save data for Custom Template
        $this->Rows[] = $this->CurrentRecord;
    }

    // Aggregate list row values
    public function aggregateListRowValues()
    {
    }

    // Aggregate list row (for rendering)
    public function aggregateListRow()
    {
        // Call Row Rendered event
        $this->rowRendered();
    }

    // Export data in HTML/CSV/Word/Excel/Email/PDF format
    public function exportDocument(BaseAbstractExport $doc, array $records, int $startRec = 1, int $stopRec = 1, string $exportPageType = "")
    {
        if (count($records) == 0 || !$doc) {
            return;
        }
        if (!$doc->ExportCustom) {
            // Write header
            $doc->exportTableHeader();
            if ($doc->Horizontal) { // Horizontal format, write header
                $doc->beginExportRow();
                if ($exportPageType == "view") {
                    $doc->exportCaption($this->REFUND_ID);
                    $doc->exportCaption($this->PAYMENT_ID);
                    $doc->exportCaption($this->APPOINTMENT_ID);
                    $doc->exportCaption($this->PATIENT_ID);
                    $doc->exportCaption($this->REFUND_AMOUNT);
                    $doc->exportCaption($this->REFUND_DATE);
                    $doc->exportCaption($this->REFUND_STATUS);
                    $doc->exportCaption($this->REFUND_REASON);
                    $doc->exportCaption($this->REFUND_TXN_ID);
                    $doc->exportCaption($this->CREATED_AT);
                } else {
                    $doc->exportCaption($this->REFUND_ID);
                    $doc->exportCaption($this->PAYMENT_ID);
                    $doc->exportCaption($this->APPOINTMENT_ID);
                    $doc->exportCaption($this->PATIENT_ID);
                    $doc->exportCaption($this->REFUND_AMOUNT);
                    $doc->exportCaption($this->REFUND_DATE);
                    $doc->exportCaption($this->REFUND_STATUS);
                    $doc->exportCaption($this->REFUND_REASON);
                    $doc->exportCaption($this->REFUND_TXN_ID);
                    $doc->exportCaption($this->CREATED_AT);
                }
                $doc->endExportRow();
            }
        }
        $recCnt = $startRec - 1;
        $stopRec = $stopRec > 0 ? $stopRec : PHP_INT_MAX;
        $recordIndex = 0;
        while ($recordIndex < count($records) && $recCnt < $stopRec) {
            $row = $records[$recordIndex];
            $recordIndex++;
            $recCnt++;
            if ($recCnt >= $startRec) {
                $rowCnt = $recCnt - $startRec + 1;

                // Page break
                if ($this->ExportPageBreakCount > 0) {
                    if ($rowCnt > 1 && ($rowCnt - 1) % $this->ExportPageBreakCount == 0) {
                        $doc->exportPageBreak();
                    }
                }
                $this->loadListRowValues($row);

                // Render row
                $this->RowType = RowType::VIEW; // Render view
                $this->resetAttributes();
                $this->renderListRow();
                if (!$doc->ExportCustom) {
                    $doc->beginExportRow($rowCnt); // Allow CSS styles if enabled
                    if ($exportPageType == "view") {
                        $doc->exportField($this->REFUND_ID);
                        $doc->exportField($this->PAYMENT_ID);
                        $doc->exportField($this->APPOINTMENT_ID);
                        $doc->exportField($this->PATIENT_ID);
                        $doc->exportField($this->REFUND_AMOUNT);
                        $doc->exportField($this->REFUND_DATE);
                        $doc->exportField($this->REFUND_STATUS);
                        $doc->exportField($this->REFUND_REASON);
                        $doc->exportField($this->REFUND_TXN_ID);
                        $doc->exportField($this->CREATED_AT);
                    } else {
                        $doc->exportField($this->REFUND_ID);
                        $doc->exportField($this->PAYMENT_ID);
                        $doc->exportField($this->APPOINTMENT_ID);
                        $doc->exportField($this->PATIENT_ID);
                        $doc->exportField($this->REFUND_AMOUNT);
                        $doc->exportField($this->REFUND_DATE);
                        $doc->exportField($this->REFUND_STATUS);
                        $doc->exportField($this->REFUND_REASON);
                        $doc->exportField($this->REFUND_TXN_ID);
                        $doc->exportField($this->CREATED_AT);
                    }
                    $doc->endExportRow($rowCnt);
                }
            }

            // Call Row Export server event
            if ($doc->ExportCustom) {
                $this->rowExport($doc, $row);
            }
        }
        if (!$doc->ExportCustom) {
            $doc->exportTableFooter();
        }
    }

    // Render lookup field for view
    public function renderLookupForView(string $name, mixed $value): mixed
    {
        $this->RowType = RowType::VIEW;
        return $value;
    }

    // Render lookup field for edit
    public function renderLookupForEdit(string $name, mixed $value): mixed
    {
        $this->RowType = RowType::EDIT;
        return $value;
    }

    // Add master User ID filter
    public function addMasterUserIDFilter(string $filter, string $currentMasterTable): string
    {
        $filterWrk = $filter;
        return $filterWrk;
    }

    // Add detail User ID filter
    public function addDetailUserIDFilter(string $filter, string $currentMasterTable): string
    {
        $filterWrk = $filter;
        return $filterWrk;
    }

    // Get file data
    public function getFileData(string $fldparm, string $key, bool $resize, int $width = 0, int $height = 0, ?callable $callback = null): Response
    {
        global $httpContext;

        // No binary fields
        return $response;
    }

    // Update last insert ID
    public function updateLastInsertId(Entity\RefundTbl $row, LifecycleEventArgs $args)
    {
        $this->REFUND_ID->setDbValue($row['REFUND_ID']);
    }

    // Save entity change set
    public function preUpdate(Entity\RefundTbl $row, PreUpdateEventArgs $args): void
    {
        $oid = spl_object_id($row);
        $this->changeSets[$oid] = $args->getEntityChangeSet();
    }

    // Store identifiers before removal
    public function preRemove(Entity\RefundTbl $row, LifecycleEventArgs $args): void
    {
        $this->snapshots[spl_object_id($row)] = $row->identifierValues();
    }

    // Table level events

    // Table Load event
    public function tableLoad(): void
    {
        // Enter your code here
    }

    // Records Selecting event
    public function recordsSelecting(string &$filter): void
    {
        // Enter your code here
    }

    // Records Selected event
    public function recordsSelected(array $rows): void
    {
        //Log("Records Selected");
    }

    // Records Search Validated event
    public function recordsSearchValidated(): void
    {
        // Example:
        //$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value
    }

    // Records Searching event
    public function recordsSearching(string &$filter): void
    {
        // Enter your code here
    }

    // Row Selected event
    public function rowSelected(BaseEntity $row): void
    {
        //Log("Row Selected");
    }

    // Row Inserting event
    public function rowInserting(?BaseEntity $oldRow, BaseEntity $newRow): ?bool
    {
        // Enter your code here
        // To cancel, set return value to false
        // To skip for grid insert/update, set return value to null
        return true;
    }

    // Row Inserted event
    public function rowInserted(?BaseEntity $oldRow, BaseEntity $newRow): void
    {
        //Log("Row Inserted");
    }

    // Row Updating event
    public function rowUpdating(BaseEntity $oldRow, BaseEntity $newRow): ?bool
    {
        // Enter your code here
        // To cancel, set return value to false
        // To skip for grid insert/update, set return value to null
        return true;
    }

    // Row Updated event
    public function rowUpdated(BaseEntity $oldRow, BaseEntity $newRow): void
    {
        //Log("Row Updated");
    }

    // Row Update Conflict event
    public function rowUpdateConflict(BaseEntity $oldRow, BaseEntity $newRow): bool
    {
        // Enter your code here
        // To ignore conflict, set return value to false
        return true;
    }

    // Grid Inserting event
    public function gridInserting(): bool
    {
        // Enter your code here
        // To reject grid insert, set return value to false
        return true;
    }

    // Grid Inserted event
    public function gridInserted(array $rows): void
    {
        //Log("Grid Inserted");
    }

    // Grid Updating event
    public function gridUpdating(array $rows): bool
    {
        // Enter your code here
        // To reject grid update, set return value to false
        return true;
    }

    // Grid Updated event
    public function gridUpdated(array $oldRows, array $newRows): void
    {
        //Log("Grid Updated");
    }

    // Row Deleting event
    public function rowDeleting(BaseEntity $row): ?bool
    {
        // Enter your code here
        // To cancel, set return value to false
        // To skip for grid insert/update, set return value to null
        return true;
    }

    // Row Deleted event
    public function rowDeleted(BaseEntity $row): void
    {
        //Log("Row Deleted");
    }

    // Email Sending event
    public function emailSending(Email $email, array $args): bool
    {
        //var_dump($email, $args); exit();
        return true;
    }

    // Lookup Selecting event
    public function lookupSelecting(DbField $field, string &$filter): void
    {
        //var_dump($field->Name, $field->Lookup, $filter); // Uncomment to view the filter
        // Enter your code here
    }

    // Row Rendering event
    public function rowRendering(): void
    {
        // Enter your code here
    }

    // Row Rendered event
    public function rowRendered(): void
    {
        // To view properties of field class, use:
        //var_dump($this-><FieldName>);
    }

    // User ID Filtering event
    public function userIdFiltering(string &$filter): void
    {
        // Enter your code here
    }
}

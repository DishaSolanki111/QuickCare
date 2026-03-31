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
 * Table class for view_payment_report
 */
#[AsAlias("ViewPaymentReport", true)]
#[AsAlias("view_payment_report", true)]
#[AsEntityListener(event: Events::postPersist, method: 'clearCache', entity: Entity\ViewPaymentReport::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clearCache', entity: Entity\ViewPaymentReport::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clearCache', entity: Entity\ViewPaymentReport::class)]
#[AsEntityListener(event: Events::postPersist, method: 'updateLastInsertId', entity: Entity\ViewPaymentReport::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Entity\ViewPaymentReport::class)]
class ViewPaymentReport extends DbTable implements LookupTableInterface
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
    public string $Alias = 'v';

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
    public DbChart $Chart1;
    public DbChart $Chart2;
    public DbChart $Chart3;

    // Fields
    public DbField $PAYMENT_ID;
    public DbField $TRANSACTION_ID;
    public DbField $Patient_Name;
    public DbField $Doctor_Name;
    public DbField $AMOUNT;
    public DbField $PAYMENT_MODE;
    public DbField $Payment_Status;
    public DbField $PAYMENT_DATE;
    public DbField $Day_Name;
    public DbField $Week_Number;
    public DbField $Month_Number;
    public DbField $Month_Name;
    public DbField $Year;

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
        $this->TableVar = "view_payment_report";
        $this->TableName = 'view_payment_report';
        $this->TableType = "VIEW";
        $this->SortType = 1; // Sort Type
        $this->ImportUseTransaction = $this->supportsTransaction() && Config("IMPORT_USE_TRANSACTION");
        $this->UseTransaction = $this->supportsTransaction() && Config("USE_TRANSACTION");
        $this->EntityClass = Entity\ViewPaymentReport::class;
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
        $this->UseColumnVisibility = true;
        $this->UserIDPermission = Config("DEFAULT_USER_ID_PERMISSION"); // Default User ID permission
        $this->BasicSearch = new BasicSearch($this, Session(), $this->language);

        // Create fields
        $this->Fields = $this->fieldFactory->createAll($this);

        // PAYMENT_ID
        $this->PAYMENT_ID = $this->Fields['PAYMENT_ID'];
        $this->PAYMENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // TRANSACTION_ID
        $this->TRANSACTION_ID = $this->Fields['TRANSACTION_ID'];

        // Patient_Name
        $this->Patient_Name = $this->Fields['Patient_Name'];
        $this->Patient_Name->Lookup = new Lookup($this->Patient_Name, 'view_payment_report', true, 'Patient_Name', ["Patient_Name","","",""], '', "", [], [], [], [], [], [], false, '', '', "");

        // Doctor_Name
        $this->Doctor_Name = $this->Fields['Doctor_Name'];
        $this->Doctor_Name->Lookup = new Lookup($this->Doctor_Name, 'view_payment_report', true, 'Doctor_Name', ["Doctor_Name","","",""], '', "", [], [], [], [], [], [], false, '', '', "");

        // AMOUNT
        $this->AMOUNT = $this->Fields['AMOUNT'];
        $this->AMOUNT->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // PAYMENT_MODE
        $this->PAYMENT_MODE = $this->Fields['PAYMENT_MODE'];
        $this->PAYMENT_MODE->Lookup = new Lookup($this->PAYMENT_MODE, 'view_payment_report', true, 'PAYMENT_MODE', ["PAYMENT_MODE","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->PAYMENT_MODE->OptionCount = 4;

        // Payment_Status
        $this->Payment_Status = $this->Fields['Payment_Status'];
        $this->Payment_Status->Lookup = new Lookup($this->Payment_Status, 'view_payment_report', false, '', ["","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->Payment_Status->OptionCount = 3;

        // PAYMENT_DATE
        $this->PAYMENT_DATE = $this->Fields['PAYMENT_DATE'];
        $this->PAYMENT_DATE->Lookup = new Lookup($this->PAYMENT_DATE, 'view_payment_report', true, 'PAYMENT_DATE', ["PAYMENT_DATE","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->PAYMENT_DATE->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));

        // Day_Name
        $this->Day_Name = $this->Fields['Day_Name'];
        $this->Day_Name->Lookup = new Lookup($this->Day_Name, 'view_payment_report', true, 'Day_Name', ["Day_Name","","",""], '', "", [], [], [], [], [], [], false, '', '', "");

        // Week_Number
        $this->Week_Number = $this->Fields['Week_Number'];
        $this->Week_Number->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Month_Number
        $this->Month_Number = $this->Fields['Month_Number'];
        $this->Month_Number->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Month_Name
        $this->Month_Name = $this->Fields['Month_Name'];
        $this->Month_Name->Lookup = new Lookup($this->Month_Name, 'view_payment_report', true, 'Month_Name', ["Month_Name","","",""], '', "", [], [], [], [], [], [], false, '', '', "");

        // Year
        $this->Year = $this->Fields['Year'];
        $this->Year->Lookup = new Lookup($this->Year, 'view_payment_report', true, 'Year', ["Year","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->Year->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Chart1
        $this->Chart1 = new DbChart(
            $this->language,
            $this,
            'Chart1',
            'Chart1',
            'Month_Name',
            'AMOUNT',
            1002,
            '',
            0,
            'SUM',
            400,
            400
        );
        $this->Chart1->Position = 4;
        $this->Chart1->PageBreakType = "before";
        $this->Chart1->YAxisFormat = ["Number"];
        $this->Chart1->YFieldFormat = ["Number"];
        $this->Chart1->SortType = 0;
        $this->Chart1->SortSequence = "";
        $this->Chart1->SqlXField = "`Month_Name`";
        $this->Chart1->SqlYField = "SUM(`AMOUNT`)";
        $this->Chart1->SqlSeriesField = "''";
        $this->Chart1->SqlGroupBy = "`Month_Name`";
        $this->Chart1->SqlOrderBy = "";
        $this->Chart1->SeriesDateType = "";
        $this->Chart1->ID = "view_payment_report_Chart1"; // Chart ID
        $this->Chart1->setParameters([
            ["type", "1002"],
            ["seriestype", "0"]
        ]); // Chart type / Chart series type
        $this->Chart1->setParameters([
            ["caption", $this->Chart1->caption()],
            ["xaxisname", $this->Chart1->xAxisName()]
        ]); // Chart caption / X axis name
        $this->Chart1->setParameter("yaxisname", $this->Chart1->yAxisName()); // Y axis name
        $this->Chart1->setParameters([
            ["shownames", "1"],
            ["showvalues", "1"],
            ["showhovercap", "1"]
        ]); // Show names / Show values / Show hover
        $this->Chart1->setParameter("alpha", DbChart::getDefaultAlpha()); // Chart alpha (datasets background color)
        $this->Chart1->setParameters([["options.plugins.legend.labels.pointStyleWidth",null]]);
        $this->Charts[$this->Chart1->ID] = $this->Chart1;

        // Chart2
        $this->Chart2 = new DbChart(
            $this->language,
            $this,
            'Chart2',
            'Chart2',
            'PAYMENT_MODE',
            'AMOUNT',
            1005,
            '',
            0,
            'SUM',
            400,
            400
        );
        $this->Chart2->Position = 4;
        $this->Chart2->PageBreakType = "before";
        $this->Chart2->YAxisFormat = ["Number"];
        $this->Chart2->YFieldFormat = ["Number"];
        $this->Chart2->SortType = 0;
        $this->Chart2->SortSequence = "";
        $this->Chart2->SqlXField = "`PAYMENT_MODE`";
        $this->Chart2->SqlYField = "SUM(`AMOUNT`)";
        $this->Chart2->SqlSeriesField = "''";
        $this->Chart2->SqlGroupBy = "`PAYMENT_MODE`";
        $this->Chart2->SqlOrderBy = "";
        $this->Chart2->SeriesDateType = "";
        $this->Chart2->ID = "view_payment_report_Chart2"; // Chart ID
        $this->Chart2->setParameters([
            ["type", "1005"],
            ["seriestype", "0"]
        ]); // Chart type / Chart series type
        $this->Chart2->setParameters([
            ["caption", $this->Chart2->caption()],
            ["xaxisname", $this->Chart2->xAxisName()]
        ]); // Chart caption / X axis name
        $this->Chart2->setParameter("yaxisname", $this->Chart2->yAxisName()); // Y axis name
        $this->Chart2->setParameters([
            ["shownames", "1"],
            ["showvalues", "1"],
            ["showhovercap", "1"]
        ]); // Show names / Show values / Show hover
        $this->Chart2->setParameter("alpha", DbChart::getDefaultAlpha()); // Chart alpha (datasets background color)
        $this->Chart2->setParameters([["options.plugins.legend.labels.pointStyleWidth",null]]);
        $this->Charts[$this->Chart2->ID] = $this->Chart2;

        // Chart3
        $this->Chart3 = new DbChart(
            $this->language,
            $this,
            'Chart3',
            'Chart3',
            'Day_Name',
            'AMOUNT',
            1004,
            '',
            0,
            'SUM',
            400,
            400
        );
        $this->Chart3->Position = 4;
        $this->Chart3->PageBreakType = "before";
        $this->Chart3->YAxisFormat = ["Number"];
        $this->Chart3->YFieldFormat = ["Number"];
        $this->Chart3->SortType = 0;
        $this->Chart3->SortSequence = "";
        $this->Chart3->SqlXField = "`Day_Name`";
        $this->Chart3->SqlYField = "SUM(`AMOUNT`)";
        $this->Chart3->SqlSeriesField = "''";
        $this->Chart3->SqlGroupBy = "`Day_Name`";
        $this->Chart3->SqlOrderBy = "";
        $this->Chart3->SeriesDateType = "";
        $this->Chart3->ID = "view_payment_report_Chart3"; // Chart ID
        $this->Chart3->setParameters([
            ["type", "1004"],
            ["seriestype", "0"]
        ]); // Chart type / Chart series type
        $this->Chart3->setParameters([
            ["caption", $this->Chart3->caption()],
            ["xaxisname", $this->Chart3->xAxisName()]
        ]); // Chart caption / X axis name
        $this->Chart3->setParameter("yaxisname", $this->Chart3->yAxisName()); // Y axis name
        $this->Chart3->setParameters([
            ["shownames", "1"],
            ["showvalues", "1"],
            ["showhovercap", "1"]
        ]); // Show names / Show values / Show hover
        $this->Chart3->setParameter("alpha", DbChart::getDefaultAlpha()); // Chart alpha (datasets background color)
        $this->Chart3->setParameters([["options.plugins.legend.labels.pointStyleWidth",null]]);
        $this->Charts[$this->Chart3->ID] = $this->Chart3;

        // Call Table Load event
        $this->tableLoad();
    }

    // Get field settings
    public function getFieldDefinitions(): array
    {
        return [
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
                'HtmlTag' => 'NO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'IsAutoIncrement' => true,
                'Nullable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'TRANSACTION_ID' => [
                'FieldVar' => 'x_TRANSACTION_ID', // Field variable name
                'Param' => 'TRANSACTION_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'transactionId', // Field entity property name
                'Expression' => '`TRANSACTION_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`TRANSACTION_ID`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 36, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`TRANSACTION_ID`', // Virtual field expression (used in ListSQL)
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
            'Patient_Name' => [
                'FieldVar' => 'x_Patient_Name', // Field variable name
                'Param' => 'Patient_Name', // Field parameter name (Table class property name)
                'PropertyName' => 'patientName', // Field entity property name
                'Expression' => '`Patient_Name`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Patient_Name`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 41, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Patient_Name`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Nullable' => false,
                'Required' => true,
                'Sortable' => false,
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY"],
            ],
            'Doctor_Name' => [
                'FieldVar' => 'x_Doctor_Name', // Field variable name
                'Param' => 'Doctor_Name', // Field parameter name (Table class property name)
                'PropertyName' => 'doctorName', // Field entity property name
                'Expression' => '`Doctor_Name`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Doctor_Name`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 41, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Doctor_Name`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Nullable' => false,
                'Required' => true,
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY"],
            ],
            'AMOUNT' => [
                'FieldVar' => 'x_AMOUNT', // Field variable name
                'Param' => 'AMOUNT', // Field parameter name (Table class property name)
                'PropertyName' => 'amount', // Field entity property name
                'Expression' => '`AMOUNT`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`AMOUNT`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 12, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`AMOUNT`', // Virtual field expression (used in ListSQL)
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
            'PAYMENT_MODE' => [
                'FieldVar' => 'x_PAYMENT_MODE', // Field variable name
                'Param' => 'PAYMENT_MODE', // Field parameter name (Table class property name)
                'PropertyName' => 'paymentMode', // Field entity property name
                'Expression' => '`PAYMENT_MODE`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`PAYMENT_MODE`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`PAYMENT_MODE`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'RADIO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>"],
                'OptionCount' => 4,
            ],
            'Payment_Status' => [
                'FieldVar' => 'x_Payment_Status', // Field variable name
                'Param' => 'Payment_Status', // Field parameter name (Table class property name)
                'PropertyName' => 'paymentStatus', // Field entity property name
                'Expression' => '`Payment_Status`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Payment_Status`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 9, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Payment_Status`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'RADIO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Nullable' => false,
                'Required' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>"],
                'OptionCount' => 3,
            ],
            'PAYMENT_DATE' => [
                'FieldVar' => 'x_PAYMENT_DATE', // Field variable name
                'Param' => 'PAYMENT_DATE', // Field parameter name (Table class property name)
                'PropertyName' => 'paymentDate', // Field entity property name
                'Expression' => '`PAYMENT_DATE`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`PAYMENT_DATE`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 133, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`PAYMENT_DATE`', // Virtual field expression (used in ListSQL)
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
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'Day_Name' => [
                'FieldVar' => 'x_Day_Name', // Field variable name
                'Param' => 'Day_Name', // Field parameter name (Table class property name)
                'PropertyName' => 'dayName', // Field entity property name
                'Expression' => '`Day_Name`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Day_Name`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 9, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Day_Name`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Sortable' => false,
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY", "IS NULL", "IS NOT NULL"],
            ],
            'Week_Number' => [
                'FieldVar' => 'x_Week_Number', // Field variable name
                'Param' => 'Week_Number', // Field parameter name (Table class property name)
                'PropertyName' => 'weekNumber', // Field entity property name
                'Expression' => '`Week_Number`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Week_Number`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 2, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Week_Number`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Sortable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN", "IS NULL", "IS NOT NULL"],
            ],
            'Month_Number' => [
                'FieldVar' => 'x_Month_Number', // Field variable name
                'Param' => 'Month_Number', // Field parameter name (Table class property name)
                'PropertyName' => 'monthNumber', // Field entity property name
                'Expression' => '`Month_Number`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Month_Number`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 2, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Month_Number`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Sortable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN", "IS NULL", "IS NOT NULL"],
            ],
            'Month_Name' => [
                'FieldVar' => 'x_Month_Name', // Field variable name
                'Param' => 'Month_Name', // Field parameter name (Table class property name)
                'PropertyName' => 'monthName', // Field entity property name
                'Expression' => '`Month_Name`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Month_Name`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 9, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Month_Name`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'UseFilter' => true,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY", "IS NULL", "IS NOT NULL"],
            ],
            'Year' => [
                'FieldVar' => 'x_Year', // Field variable name
                'Param' => 'Year', // Field parameter name (Table class property name)
                'PropertyName' => 'year', // Field entity property name
                'Expression' => '`Year`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Year`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 4, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Year`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'TEXT', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'Sortable' => false,
                'UseFilter' => true,

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
        return $this->sqlFrom ?? "view_payment_report";
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
    public function clearCache(Entity\ViewPaymentReport $row, LifecycleEventArgs $args): void
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
        $this->PAYMENT_ID->DbValue = $row->getPaymentId();
        $this->TRANSACTION_ID->DbValue = $row->getTransactionId();
        $this->Patient_Name->DbValue = $row->getPatientName();
        $this->Doctor_Name->DbValue = $row->getDoctorName();
        $this->AMOUNT->DbValue = $row->getAmount();
        $this->PAYMENT_MODE->DbValue = $row->getPaymentMode();
        $this->Payment_Status->DbValue = $row->getPaymentStatus();
        $this->PAYMENT_DATE->DbValue = $row->getPaymentDate();
        $this->Day_Name->DbValue = $row->getDayName();
        $this->Week_Number->DbValue = $row->getWeekNumber();
        $this->Month_Number->DbValue = $row->getMonthNumber();
        $this->Month_Name->DbValue = $row->getMonthName();
        $this->Year->DbValue = $row->getYear();
    }

    // Delete uploaded files
    public function deleteUploadedFiles(BaseEntity $row)
    {
        $this->loadDbValues($row);
    }

    // Record filter WHERE clause
    protected function sqlKeyFilter(): string
    {
        return "";
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
        $keys = [];
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
            if (count($ar) == 0) {
            }
        } else {
            $recordKey = $key;
        }
        if (count($recordKey) == 0) {
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
        return Session($name) ?? GetUrl("ViewPaymentReportList");
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
            "ViewPaymentReportView" => $this->language->phrase("View"),
            "ViewPaymentReportEdit" => $this->language->phrase("Edit"),
            "ViewPaymentReportAdd" => $this->language->phrase("Add"),
            default => ""
        };
    }

    // Default route URL
    public function getDefaultRouteUrl(): string
    {
        return "ViewPaymentReportList";
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
        return "ViewPaymentReportList";
    }

    // View URL
    public function getViewUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPaymentReportView", $params);
        return $this->addMasterUrl($url);
    }

    // Detail view URL
    public function getDetailViewUrl(string $param = ""): string
    {
        $url = $this->keyUrl("ViewPaymentReportView", [$param]);
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl(string $param = ""): string
    {
        $url = BuildUrl("ViewPaymentReportAdd", $param);
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPaymentReportEdit", $params);
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl(): string
    {
        $url = $this->keyUrl("ViewPaymentReportList", "action=edit");
        return $this->addMasterUrl($url);
    }

    // Detail edit URL
    public function getDetailEditUrl(string $param = ""): string
    {
        $url = $this->keyUrl("ViewPaymentReportEdit", [$param]);
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPaymentReportAdd", $params);
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl(): string
    {
        $url = $this->keyUrl("ViewPaymentReportList", "action=copy");
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl(string $param = ""): string
    {
        if ($this->UseAjaxActions && IsInfiniteScroll() && CurrentPageID() == "list") {
            return $this->keyUrl(GetApiUrl(Config("API_DELETE_ACTION") . "/" . $this->TableVar));
        } else {
            return $this->keyUrl("ViewPaymentReportDelete", $param);
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
        $json = "{" . $json . "}";
        return $htmlEncode ? HtmlEncode($json) : $json;
    }

    // Add key value to URL
    public function keyUrl(string $url, string|array $params = ""): string
    {
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
        }

        // Check and set up keys
        $recordKeys = [];
        foreach ($keys as $key) {
            if (count($key) != 0) {
                continue; // Just skip so other keys will still work
            }
            $recordKey = [];
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

        // Use DBAL to fetch rows
        $rows = $sql->fetchAllAssociative();

        // Instantiate entities using fromArray()
        return array_map([$this->EntityClass, "createFromArray"], $rows);
    }

    // Load row values from record
    public function loadListRowValues(array|BaseEntity $row): void
    {
        $this->PAYMENT_ID->setDbValue($row['PAYMENT_ID']);
        $this->TRANSACTION_ID->setDbValue($row['TRANSACTION_ID']);
        $this->Patient_Name->setDbValue($row['Patient_Name']);
        $this->Doctor_Name->setDbValue($row['Doctor_Name']);
        $this->AMOUNT->setDbValue($row['AMOUNT']);
        $this->PAYMENT_MODE->setDbValue($row['PAYMENT_MODE']);
        $this->Payment_Status->setDbValue($row['Payment_Status']);
        $this->PAYMENT_DATE->setDbValue($row['PAYMENT_DATE']);
        $this->Day_Name->setDbValue($row['Day_Name']);
        $this->Week_Number->setDbValue($row['Week_Number']);
        $this->Month_Number->setDbValue($row['Month_Number']);
        $this->Month_Name->setDbValue($row['Month_Name']);
        $this->Year->setDbValue($row['Year']);
    }

    // Render list content
    public function renderListContent(array|BaseEntity $data, int $totalRecords)
    {
        global $httpContext;
        $container = Container();
        $page = $container->get("ViewPaymentReportList");
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

        // PAYMENT_ID

        // TRANSACTION_ID

        // Patient_Name

        // Doctor_Name

        // AMOUNT

        // PAYMENT_MODE

        // Payment_Status

        // PAYMENT_DATE

        // Day_Name

        // Week_Number

        // Month_Number

        // Month_Name

        // Year

        // PAYMENT_ID
        $this->PAYMENT_ID->ViewValue = $this->PAYMENT_ID->CurrentValue;

        // TRANSACTION_ID
        $this->TRANSACTION_ID->ViewValue = $this->TRANSACTION_ID->CurrentValue;

        // Patient_Name
        $this->Patient_Name->ViewValue = $this->Patient_Name->CurrentValue;

        // Doctor_Name
        $this->Doctor_Name->ViewValue = $this->Doctor_Name->CurrentValue;

        // AMOUNT
        $this->AMOUNT->ViewValue = $this->AMOUNT->CurrentValue;
        $this->AMOUNT->ViewValue = FormatNumber($this->AMOUNT->ViewValue, $this->AMOUNT->formatPattern());

        // PAYMENT_MODE
        if (strval($this->PAYMENT_MODE->CurrentValue) != "") {
            $this->PAYMENT_MODE->ViewValue = $this->PAYMENT_MODE->optionCaption($this->PAYMENT_MODE->CurrentValue);
        } else {
            $this->PAYMENT_MODE->ViewValue = null;
        }

        // Payment_Status
        if (strval($this->Payment_Status->CurrentValue) != "") {
            $this->Payment_Status->ViewValue = $this->Payment_Status->optionCaption($this->Payment_Status->CurrentValue);
        } else {
            $this->Payment_Status->ViewValue = null;
        }

        // PAYMENT_DATE
        $this->PAYMENT_DATE->ViewValue = $this->PAYMENT_DATE->CurrentValue;
        $this->PAYMENT_DATE->ViewValue = FormatDateTime($this->PAYMENT_DATE->ViewValue, $this->PAYMENT_DATE->formatPattern());

        // Day_Name
        $this->Day_Name->ViewValue = $this->Day_Name->CurrentValue;

        // Week_Number
        $this->Week_Number->ViewValue = $this->Week_Number->CurrentValue;
        $this->Week_Number->ViewValue = FormatNumber($this->Week_Number->ViewValue, $this->Week_Number->formatPattern());

        // Month_Number
        $this->Month_Number->ViewValue = $this->Month_Number->CurrentValue;
        $this->Month_Number->ViewValue = FormatNumber($this->Month_Number->ViewValue, $this->Month_Number->formatPattern());

        // Month_Name
        $this->Month_Name->ViewValue = $this->Month_Name->CurrentValue;

        // Year
        $this->Year->ViewValue = $this->Year->CurrentValue;
        $this->Year->ViewValue = FormatNumber($this->Year->ViewValue, $this->Year->formatPattern());

        // PAYMENT_ID
        $this->PAYMENT_ID->HrefValue = "";
        $this->PAYMENT_ID->TooltipValue = "";

        // TRANSACTION_ID
        $this->TRANSACTION_ID->HrefValue = "";
        $this->TRANSACTION_ID->TooltipValue = "";

        // Patient_Name
        $this->Patient_Name->HrefValue = "";
        $this->Patient_Name->TooltipValue = "";

        // Doctor_Name
        $this->Doctor_Name->HrefValue = "";
        $this->Doctor_Name->TooltipValue = "";

        // AMOUNT
        $this->AMOUNT->HrefValue = "";
        $this->AMOUNT->TooltipValue = "";

        // PAYMENT_MODE
        $this->PAYMENT_MODE->HrefValue = "";
        $this->PAYMENT_MODE->TooltipValue = "";

        // Payment_Status
        $this->Payment_Status->HrefValue = "";
        $this->Payment_Status->TooltipValue = "";

        // PAYMENT_DATE
        $this->PAYMENT_DATE->HrefValue = "";
        $this->PAYMENT_DATE->TooltipValue = "";

        // Day_Name
        $this->Day_Name->HrefValue = "";
        $this->Day_Name->TooltipValue = "";

        // Week_Number
        $this->Week_Number->HrefValue = "";
        $this->Week_Number->TooltipValue = "";

        // Month_Number
        $this->Month_Number->HrefValue = "";
        $this->Month_Number->TooltipValue = "";

        // Month_Name
        $this->Month_Name->HrefValue = "";
        $this->Month_Name->TooltipValue = "";

        // Year
        $this->Year->HrefValue = "";
        $this->Year->TooltipValue = "";

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
                } else {
                    $doc->exportCaption($this->Patient_Name);
                    $doc->exportCaption($this->Doctor_Name);
                    $doc->exportCaption($this->AMOUNT);
                    $doc->exportCaption($this->PAYMENT_MODE);
                    $doc->exportCaption($this->PAYMENT_DATE);
                    $doc->exportCaption($this->Day_Name);
                    $doc->exportCaption($this->Month_Name);
                    $doc->exportCaption($this->Year);
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
                    } else {
                        $doc->exportField($this->Patient_Name);
                        $doc->exportField($this->Doctor_Name);
                        $doc->exportField($this->AMOUNT);
                        $doc->exportField($this->PAYMENT_MODE);
                        $doc->exportField($this->PAYMENT_DATE);
                        $doc->exportField($this->Day_Name);
                        $doc->exportField($this->Month_Name);
                        $doc->exportField($this->Year);
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
    public function updateLastInsertId(Entity\ViewPaymentReport $row, LifecycleEventArgs $args)
    {
        $this->PAYMENT_ID->setDbValue($row['PAYMENT_ID']);
    }

    // Save entity change set
    public function preUpdate(Entity\ViewPaymentReport $row, PreUpdateEventArgs $args): void
    {
        $oid = spl_object_id($row);
        $this->changeSets[$oid] = $args->getEntityChangeSet();
    }

    // Store identifiers before removal
    public function preRemove(Entity\ViewPaymentReport $row, LifecycleEventArgs $args): void
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

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
 * Table class for view_patient_report
 */
#[AsAlias("ViewPatientReport", true)]
#[AsAlias("view_patient_report", true)]
#[AsEntityListener(event: Events::postPersist, method: 'clearCache', entity: Entity\ViewPatientReport::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clearCache', entity: Entity\ViewPatientReport::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clearCache', entity: Entity\ViewPatientReport::class)]
#[AsEntityListener(event: Events::postPersist, method: 'updateLastInsertId', entity: Entity\ViewPatientReport::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Entity\ViewPatientReport::class)]
class ViewPatientReport extends DbTable implements LookupTableInterface
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

    // Fields
    public DbField $PATIENT_ID;
    public DbField $Patient_Name;
    public DbField $GENDER;
    public DbField $BLOOD_GROUP;
    public DbField $PHONE;
    public DbField $EMAIL;
    public DbField $Total_Appointments;
    public DbField $Completed_Appointments;
    public DbField $Upcoming_Appointments;
    public DbField $Cancelled_Appointments;
    public DbField $Last_Visit;
    public DbField $First_Visit;
    public DbField $Total_Prescriptions;
    public DbField $Total_Amount_Paid;
    public DbField $Avg_Rating_Given;

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
        $this->TableVar = "view_patient_report";
        $this->TableName = 'view_patient_report';
        $this->TableType = "VIEW";
        $this->SortType = 1; // Sort Type
        $this->ImportUseTransaction = $this->supportsTransaction() && Config("IMPORT_USE_TRANSACTION");
        $this->UseTransaction = $this->supportsTransaction() && Config("USE_TRANSACTION");
        $this->EntityClass = Entity\ViewPatientReport::class;
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

        // PATIENT_ID
        $this->PATIENT_ID = $this->Fields['PATIENT_ID'];
        $this->PATIENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Patient_Name
        $this->Patient_Name = $this->Fields['Patient_Name'];

        // GENDER
        $this->GENDER = $this->Fields['GENDER'];
        $this->GENDER->Lookup = new Lookup($this->GENDER, 'view_patient_report', false, '', ["","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->GENDER->OptionCount = 3;

        // BLOOD_GROUP
        $this->BLOOD_GROUP = $this->Fields['BLOOD_GROUP'];
        $this->BLOOD_GROUP->Lookup = new Lookup($this->BLOOD_GROUP, 'view_patient_report', false, '', ["","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->BLOOD_GROUP->OptionCount = 8;

        // PHONE
        $this->PHONE = $this->Fields['PHONE'];
        $this->PHONE->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // EMAIL
        $this->EMAIL = $this->Fields['EMAIL'];

        // Total_Appointments
        $this->Total_Appointments = $this->Fields['Total_Appointments'];
        $this->Total_Appointments->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Completed_Appointments
        $this->Completed_Appointments = $this->Fields['Completed_Appointments'];
        $this->Completed_Appointments->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // Upcoming_Appointments
        $this->Upcoming_Appointments = $this->Fields['Upcoming_Appointments'];
        $this->Upcoming_Appointments->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // Cancelled_Appointments
        $this->Cancelled_Appointments = $this->Fields['Cancelled_Appointments'];
        $this->Cancelled_Appointments->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // Last_Visit
        $this->Last_Visit = $this->Fields['Last_Visit'];
        $this->Last_Visit->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));

        // First_Visit
        $this->First_Visit = $this->Fields['First_Visit'];
        $this->First_Visit->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));

        // Total_Prescriptions
        $this->Total_Prescriptions = $this->Fields['Total_Prescriptions'];
        $this->Total_Prescriptions->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");

        // Total_Amount_Paid
        $this->Total_Amount_Paid = $this->Fields['Total_Amount_Paid'];
        $this->Total_Amount_Paid->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // Avg_Rating_Given
        $this->Avg_Rating_Given = $this->Fields['Avg_Rating_Given'];
        $this->Avg_Rating_Given->DefaultErrorMessage = $this->language->phrase("IncorrectFloat");

        // Call Table Load event
        $this->tableLoad();
    }

    // Get field settings
    public function getFieldDefinitions(): array
    {
        return [
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
                'HtmlTag' => 'NO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'IsAutoIncrement' => true,
                'Nullable' => false,

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
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

                // 'UseAdvancedSearch' => true,
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "STARTS WITH", "NOT STARTS WITH", "LIKE", "NOT LIKE", "ENDS WITH", "NOT ENDS WITH", "IS EMPTY", "IS NOT EMPTY"],
            ],
            'GENDER' => [
                'FieldVar' => 'x_GENDER', // Field variable name
                'Param' => 'GENDER', // Field parameter name (Table class property name)
                'PropertyName' => 'gender', // Field entity property name
                'Expression' => '`GENDER`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`GENDER`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 6, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`GENDER`', // Virtual field expression (used in ListSQL)
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
            'BLOOD_GROUP' => [
                'FieldVar' => 'x_BLOOD_GROUP', // Field variable name
                'Param' => 'BLOOD_GROUP', // Field parameter name (Table class property name)
                'PropertyName' => 'bloodGroup', // Field entity property name
                'Expression' => '`BLOOD_GROUP`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`BLOOD_GROUP`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 3, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`BLOOD_GROUP`', // Virtual field expression (used in ListSQL)
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
                'OptionCount' => 8,
            ],
            'PHONE' => [
                'FieldVar' => 'x_PHONE', // Field variable name
                'Param' => 'PHONE', // Field parameter name (Table class property name)
                'PropertyName' => 'phone', // Field entity property name
                'Expression' => '`PHONE`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`PHONE`', // Field expression (used in basic search SQL)
                'Type' => 20, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 20, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`PHONE`', // Virtual field expression (used in ListSQL)
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
            'EMAIL' => [
                'FieldVar' => 'x_EMAIL', // Field variable name
                'Param' => 'EMAIL', // Field parameter name (Table class property name)
                'PropertyName' => 'email', // Field entity property name
                'Expression' => '`EMAIL`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`EMAIL`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 50, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`EMAIL`', // Virtual field expression (used in ListSQL)
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
            'Total_Appointments' => [
                'FieldVar' => 'x_Total_Appointments', // Field variable name
                'Param' => 'Total_Appointments', // Field parameter name (Table class property name)
                'PropertyName' => 'totalAppointments', // Field entity property name
                'Expression' => '`Total_Appointments`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Total_Appointments`', // Field expression (used in basic search SQL)
                'Type' => 20, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 21, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Total_Appointments`', // Virtual field expression (used in ListSQL)
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
            'Completed_Appointments' => [
                'FieldVar' => 'x_Completed_Appointments', // Field variable name
                'Param' => 'Completed_Appointments', // Field parameter name (Table class property name)
                'PropertyName' => 'completedAppointments', // Field entity property name
                'Expression' => '`Completed_Appointments`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Completed_Appointments`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 23, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Completed_Appointments`', // Virtual field expression (used in ListSQL)
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
            'Upcoming_Appointments' => [
                'FieldVar' => 'x_Upcoming_Appointments', // Field variable name
                'Param' => 'Upcoming_Appointments', // Field parameter name (Table class property name)
                'PropertyName' => 'upcomingAppointments', // Field entity property name
                'Expression' => '`Upcoming_Appointments`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Upcoming_Appointments`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 23, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Upcoming_Appointments`', // Virtual field expression (used in ListSQL)
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
            'Cancelled_Appointments' => [
                'FieldVar' => 'x_Cancelled_Appointments', // Field variable name
                'Param' => 'Cancelled_Appointments', // Field parameter name (Table class property name)
                'PropertyName' => 'cancelledAppointments', // Field entity property name
                'Expression' => '`Cancelled_Appointments`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Cancelled_Appointments`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 23, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Cancelled_Appointments`', // Virtual field expression (used in ListSQL)
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
            'Last_Visit' => [
                'FieldVar' => 'x_Last_Visit', // Field variable name
                'Param' => 'Last_Visit', // Field parameter name (Table class property name)
                'PropertyName' => 'lastVisit', // Field entity property name
                'Expression' => '`Last_Visit`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`Last_Visit`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 133, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`Last_Visit`', // Virtual field expression (used in ListSQL)
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
            'First_Visit' => [
                'FieldVar' => 'x_First_Visit', // Field variable name
                'Param' => 'First_Visit', // Field parameter name (Table class property name)
                'PropertyName' => 'firstVisit', // Field entity property name
                'Expression' => '`First_Visit`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`First_Visit`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 133, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`First_Visit`', // Virtual field expression (used in ListSQL)
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
            'Total_Prescriptions' => [
                'FieldVar' => 'x_Total_Prescriptions', // Field variable name
                'Param' => 'Total_Prescriptions', // Field parameter name (Table class property name)
                'PropertyName' => 'totalPrescriptions', // Field entity property name
                'Expression' => '`Total_Prescriptions`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Total_Prescriptions`', // Field expression (used in basic search SQL)
                'Type' => 20, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 21, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Total_Prescriptions`', // Virtual field expression (used in ListSQL)
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
            'Total_Amount_Paid' => [
                'FieldVar' => 'x_Total_Amount_Paid', // Field variable name
                'Param' => 'Total_Amount_Paid', // Field parameter name (Table class property name)
                'PropertyName' => 'totalAmountPaid', // Field entity property name
                'Expression' => '`Total_Amount_Paid`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Total_Amount_Paid`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 34, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Total_Amount_Paid`', // Virtual field expression (used in ListSQL)
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
            'Avg_Rating_Given' => [
                'FieldVar' => 'x_Avg_Rating_Given', // Field variable name
                'Param' => 'Avg_Rating_Given', // Field parameter name (Table class property name)
                'PropertyName' => 'avgRatingGiven', // Field entity property name
                'Expression' => '`Avg_Rating_Given`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`Avg_Rating_Given`', // Field expression (used in basic search SQL)
                'Type' => 131, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 14, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`Avg_Rating_Given`', // Virtual field expression (used in ListSQL)
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
        return $this->sqlFrom ?? "view_patient_report";
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
    public function clearCache(Entity\ViewPatientReport $row, LifecycleEventArgs $args): void
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
        $this->PATIENT_ID->DbValue = $row->getPatientId();
        $this->Patient_Name->DbValue = $row->getPatientName();
        $this->GENDER->DbValue = $row->getGender();
        $this->BLOOD_GROUP->DbValue = $row->getBloodGroup();
        $this->PHONE->DbValue = $row->getPhone();
        $this->EMAIL->DbValue = $row->getEmail();
        $this->Total_Appointments->DbValue = $row->getTotalAppointments();
        $this->Completed_Appointments->DbValue = $row->getCompletedAppointments();
        $this->Upcoming_Appointments->DbValue = $row->getUpcomingAppointments();
        $this->Cancelled_Appointments->DbValue = $row->getCancelledAppointments();
        $this->Last_Visit->DbValue = $row->getLastVisit();
        $this->First_Visit->DbValue = $row->getFirstVisit();
        $this->Total_Prescriptions->DbValue = $row->getTotalPrescriptions();
        $this->Total_Amount_Paid->DbValue = $row->getTotalAmountPaid();
        $this->Avg_Rating_Given->DbValue = $row->getAvgRatingGiven();
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
        return Session($name) ?? GetUrl("ViewPatientReportList");
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
            "ViewPatientReportView" => $this->language->phrase("View"),
            "ViewPatientReportEdit" => $this->language->phrase("Edit"),
            "ViewPatientReportAdd" => $this->language->phrase("Add"),
            default => ""
        };
    }

    // Default route URL
    public function getDefaultRouteUrl(): string
    {
        return "ViewPatientReportList";
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
        return "ViewPatientReportList";
    }

    // View URL
    public function getViewUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPatientReportView", $params);
        return $this->addMasterUrl($url);
    }

    // Detail view URL
    public function getDetailViewUrl(string $param = ""): string
    {
        $url = $this->keyUrl("ViewPatientReportView", [$param]);
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl(string $param = ""): string
    {
        $url = BuildUrl("ViewPatientReportAdd", $param);
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPatientReportEdit", $params);
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl(): string
    {
        $url = $this->keyUrl("ViewPatientReportList", "action=edit");
        return $this->addMasterUrl($url);
    }

    // Detail edit URL
    public function getDetailEditUrl(string $param = ""): string
    {
        $url = $this->keyUrl("ViewPatientReportEdit", [$param]);
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("ViewPatientReportAdd", $params);
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl(): string
    {
        $url = $this->keyUrl("ViewPatientReportList", "action=copy");
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl(string $param = ""): string
    {
        if ($this->UseAjaxActions && IsInfiniteScroll() && CurrentPageID() == "list") {
            return $this->keyUrl(GetApiUrl(Config("API_DELETE_ACTION") . "/" . $this->TableVar));
        } else {
            return $this->keyUrl("ViewPatientReportDelete", $param);
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
        $this->PATIENT_ID->setDbValue($row['PATIENT_ID']);
        $this->Patient_Name->setDbValue($row['Patient_Name']);
        $this->GENDER->setDbValue($row['GENDER']);
        $this->BLOOD_GROUP->setDbValue($row['BLOOD_GROUP']);
        $this->PHONE->setDbValue($row['PHONE']);
        $this->EMAIL->setDbValue($row['EMAIL']);
        $this->Total_Appointments->setDbValue($row['Total_Appointments']);
        $this->Completed_Appointments->setDbValue($row['Completed_Appointments']);
        $this->Upcoming_Appointments->setDbValue($row['Upcoming_Appointments']);
        $this->Cancelled_Appointments->setDbValue($row['Cancelled_Appointments']);
        $this->Last_Visit->setDbValue($row['Last_Visit']);
        $this->First_Visit->setDbValue($row['First_Visit']);
        $this->Total_Prescriptions->setDbValue($row['Total_Prescriptions']);
        $this->Total_Amount_Paid->setDbValue($row['Total_Amount_Paid']);
        $this->Avg_Rating_Given->setDbValue($row['Avg_Rating_Given']);
    }

    // Render list content
    public function renderListContent(array|BaseEntity $data, int $totalRecords)
    {
        global $httpContext;
        $container = Container();
        $page = $container->get("ViewPatientReportList");
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

        // PATIENT_ID

        // Patient_Name

        // GENDER

        // BLOOD_GROUP

        // PHONE

        // EMAIL

        // Total_Appointments

        // Completed_Appointments

        // Upcoming_Appointments

        // Cancelled_Appointments

        // Last_Visit

        // First_Visit

        // Total_Prescriptions

        // Total_Amount_Paid

        // Avg_Rating_Given

        // PATIENT_ID
        $this->PATIENT_ID->ViewValue = $this->PATIENT_ID->CurrentValue;

        // Patient_Name
        $this->Patient_Name->ViewValue = $this->Patient_Name->CurrentValue;

        // GENDER
        if (strval($this->GENDER->CurrentValue) != "") {
            $this->GENDER->ViewValue = $this->GENDER->optionCaption($this->GENDER->CurrentValue);
        } else {
            $this->GENDER->ViewValue = null;
        }

        // BLOOD_GROUP
        if (strval($this->BLOOD_GROUP->CurrentValue) != "") {
            $this->BLOOD_GROUP->ViewValue = $this->BLOOD_GROUP->optionCaption($this->BLOOD_GROUP->CurrentValue);
        } else {
            $this->BLOOD_GROUP->ViewValue = null;
        }

        // PHONE
        $this->PHONE->ViewValue = $this->PHONE->CurrentValue;
        $this->PHONE->ViewValue = FormatNumber($this->PHONE->ViewValue, $this->PHONE->formatPattern());

        // EMAIL
        $this->EMAIL->ViewValue = $this->EMAIL->CurrentValue;

        // Total_Appointments
        $this->Total_Appointments->ViewValue = $this->Total_Appointments->CurrentValue;
        $this->Total_Appointments->ViewValue = FormatNumber($this->Total_Appointments->ViewValue, $this->Total_Appointments->formatPattern());

        // Completed_Appointments
        $this->Completed_Appointments->ViewValue = $this->Completed_Appointments->CurrentValue;
        $this->Completed_Appointments->ViewValue = FormatNumber($this->Completed_Appointments->ViewValue, $this->Completed_Appointments->formatPattern());

        // Upcoming_Appointments
        $this->Upcoming_Appointments->ViewValue = $this->Upcoming_Appointments->CurrentValue;
        $this->Upcoming_Appointments->ViewValue = FormatNumber($this->Upcoming_Appointments->ViewValue, $this->Upcoming_Appointments->formatPattern());

        // Cancelled_Appointments
        $this->Cancelled_Appointments->ViewValue = $this->Cancelled_Appointments->CurrentValue;
        $this->Cancelled_Appointments->ViewValue = FormatNumber($this->Cancelled_Appointments->ViewValue, $this->Cancelled_Appointments->formatPattern());

        // Last_Visit
        $this->Last_Visit->ViewValue = $this->Last_Visit->CurrentValue;
        $this->Last_Visit->ViewValue = FormatDateTime($this->Last_Visit->ViewValue, $this->Last_Visit->formatPattern());

        // First_Visit
        $this->First_Visit->ViewValue = $this->First_Visit->CurrentValue;
        $this->First_Visit->ViewValue = FormatDateTime($this->First_Visit->ViewValue, $this->First_Visit->formatPattern());

        // Total_Prescriptions
        $this->Total_Prescriptions->ViewValue = $this->Total_Prescriptions->CurrentValue;
        $this->Total_Prescriptions->ViewValue = FormatNumber($this->Total_Prescriptions->ViewValue, $this->Total_Prescriptions->formatPattern());

        // Total_Amount_Paid
        $this->Total_Amount_Paid->ViewValue = $this->Total_Amount_Paid->CurrentValue;
        $this->Total_Amount_Paid->ViewValue = FormatNumber($this->Total_Amount_Paid->ViewValue, $this->Total_Amount_Paid->formatPattern());

        // Avg_Rating_Given
        $this->Avg_Rating_Given->ViewValue = $this->Avg_Rating_Given->CurrentValue;
        $this->Avg_Rating_Given->ViewValue = FormatNumber($this->Avg_Rating_Given->ViewValue, $this->Avg_Rating_Given->formatPattern());

        // PATIENT_ID
        $this->PATIENT_ID->HrefValue = "";
        $this->PATIENT_ID->TooltipValue = "";

        // Patient_Name
        $this->Patient_Name->HrefValue = "";
        $this->Patient_Name->TooltipValue = "";

        // GENDER
        $this->GENDER->HrefValue = "";
        $this->GENDER->TooltipValue = "";

        // BLOOD_GROUP
        $this->BLOOD_GROUP->HrefValue = "";
        $this->BLOOD_GROUP->TooltipValue = "";

        // PHONE
        $this->PHONE->HrefValue = "";
        $this->PHONE->TooltipValue = "";

        // EMAIL
        $this->EMAIL->HrefValue = "";
        $this->EMAIL->TooltipValue = "";

        // Total_Appointments
        $this->Total_Appointments->HrefValue = "";
        $this->Total_Appointments->TooltipValue = "";

        // Completed_Appointments
        $this->Completed_Appointments->HrefValue = "";
        $this->Completed_Appointments->TooltipValue = "";

        // Upcoming_Appointments
        $this->Upcoming_Appointments->HrefValue = "";
        $this->Upcoming_Appointments->TooltipValue = "";

        // Cancelled_Appointments
        $this->Cancelled_Appointments->HrefValue = "";
        $this->Cancelled_Appointments->TooltipValue = "";

        // Last_Visit
        $this->Last_Visit->HrefValue = "";
        $this->Last_Visit->TooltipValue = "";

        // First_Visit
        $this->First_Visit->HrefValue = "";
        $this->First_Visit->TooltipValue = "";

        // Total_Prescriptions
        $this->Total_Prescriptions->HrefValue = "";
        $this->Total_Prescriptions->TooltipValue = "";

        // Total_Amount_Paid
        $this->Total_Amount_Paid->HrefValue = "";
        $this->Total_Amount_Paid->TooltipValue = "";

        // Avg_Rating_Given
        $this->Avg_Rating_Given->HrefValue = "";
        $this->Avg_Rating_Given->TooltipValue = "";

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
                    $doc->exportCaption($this->PATIENT_ID);
                    $doc->exportCaption($this->Patient_Name);
                    $doc->exportCaption($this->GENDER);
                    $doc->exportCaption($this->BLOOD_GROUP);
                    $doc->exportCaption($this->PHONE);
                    $doc->exportCaption($this->EMAIL);
                    $doc->exportCaption($this->Total_Appointments);
                    $doc->exportCaption($this->Completed_Appointments);
                    $doc->exportCaption($this->Upcoming_Appointments);
                    $doc->exportCaption($this->Cancelled_Appointments);
                    $doc->exportCaption($this->Last_Visit);
                    $doc->exportCaption($this->First_Visit);
                    $doc->exportCaption($this->Total_Prescriptions);
                    $doc->exportCaption($this->Total_Amount_Paid);
                    $doc->exportCaption($this->Avg_Rating_Given);
                } else {
                    $doc->exportCaption($this->PATIENT_ID);
                    $doc->exportCaption($this->Patient_Name);
                    $doc->exportCaption($this->GENDER);
                    $doc->exportCaption($this->BLOOD_GROUP);
                    $doc->exportCaption($this->PHONE);
                    $doc->exportCaption($this->EMAIL);
                    $doc->exportCaption($this->Total_Appointments);
                    $doc->exportCaption($this->Completed_Appointments);
                    $doc->exportCaption($this->Upcoming_Appointments);
                    $doc->exportCaption($this->Cancelled_Appointments);
                    $doc->exportCaption($this->Last_Visit);
                    $doc->exportCaption($this->First_Visit);
                    $doc->exportCaption($this->Total_Prescriptions);
                    $doc->exportCaption($this->Total_Amount_Paid);
                    $doc->exportCaption($this->Avg_Rating_Given);
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
                        $doc->exportField($this->PATIENT_ID);
                        $doc->exportField($this->Patient_Name);
                        $doc->exportField($this->GENDER);
                        $doc->exportField($this->BLOOD_GROUP);
                        $doc->exportField($this->PHONE);
                        $doc->exportField($this->EMAIL);
                        $doc->exportField($this->Total_Appointments);
                        $doc->exportField($this->Completed_Appointments);
                        $doc->exportField($this->Upcoming_Appointments);
                        $doc->exportField($this->Cancelled_Appointments);
                        $doc->exportField($this->Last_Visit);
                        $doc->exportField($this->First_Visit);
                        $doc->exportField($this->Total_Prescriptions);
                        $doc->exportField($this->Total_Amount_Paid);
                        $doc->exportField($this->Avg_Rating_Given);
                    } else {
                        $doc->exportField($this->PATIENT_ID);
                        $doc->exportField($this->Patient_Name);
                        $doc->exportField($this->GENDER);
                        $doc->exportField($this->BLOOD_GROUP);
                        $doc->exportField($this->PHONE);
                        $doc->exportField($this->EMAIL);
                        $doc->exportField($this->Total_Appointments);
                        $doc->exportField($this->Completed_Appointments);
                        $doc->exportField($this->Upcoming_Appointments);
                        $doc->exportField($this->Cancelled_Appointments);
                        $doc->exportField($this->Last_Visit);
                        $doc->exportField($this->First_Visit);
                        $doc->exportField($this->Total_Prescriptions);
                        $doc->exportField($this->Total_Amount_Paid);
                        $doc->exportField($this->Avg_Rating_Given);
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
    public function updateLastInsertId(Entity\ViewPatientReport $row, LifecycleEventArgs $args)
    {
        $this->PATIENT_ID->setDbValue($row['PATIENT_ID']);
    }

    // Save entity change set
    public function preUpdate(Entity\ViewPatientReport $row, PreUpdateEventArgs $args): void
    {
        $oid = spl_object_id($row);
        $this->changeSets[$oid] = $args->getEntityChangeSet();
    }

    // Store identifiers before removal
    public function preRemove(Entity\ViewPatientReport $row, LifecycleEventArgs $args): void
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

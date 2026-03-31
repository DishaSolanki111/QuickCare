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
 * Table class for Appointment_report
 */
#[AsAlias("AppointmentReport", true)]
#[AsAlias("Appointment_report", true)]
class AppointmentReport extends ReportTable implements LookupTableInterface
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

    // Summary properties
    protected string $sqlAggregate = "";

    // Group SQL
    protected string $sqlFirstGroupField = "";
    protected string $sqlOrderByGroup = "";

    // Entity alias (Use first character of table variable name)
    public string $Alias = 'a';

    // Column CSS classes
    public string $LeftColumnClass = "col-sm-2 col-form-label ew-label";
    public string $RightColumnClass = "col-sm-10";
    public string $OffsetColumnClass = "col-sm-10 offset-sm-2";
    public string $TableLeftColumnClass = "w-col-2";
    public bool $ShowGroupHeaderAsRow = false;
    public bool $ShowCompactSummaryFooter = true;
    public array $GroupingFields = ["APPOINTMENT_DATE","STATUS"];

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

    // Fields
    public DbField $APPOINTMENT_ID;
    public DbField $PATIENT_ID;
    public DbField $DOCTOR_ID;
    public DbField $SCHEDULE_ID;
    public DbField $CREATED_AT;
    public DbField $APPOINTMENT_DATE;
    public DbField $APPOINTMENT_TIME;
    public DbField $STATUS;

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
        $this->TableVar = "Appointment_report";
        $this->TableName = 'Appointment_report';
        $this->TableType = "REPORT";
        $this->TableReportType = "summary"; // Report Type
        $this->SortType = 1; // Sort Type
        $this->EntityClass = Entity\AppointmentReport::class;
        $this->ReportSourceTable = 'appointment_tbl'; // Report source table
        $this->ReportServiceClass = 'AppointmentReportService'; // Report service class name
        $this->Dbid = 'DB';
        $this->ExportAll = true;
        $this->ExportPageBreakCount = 0; // Page break per every n record (report only)

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
        $this->UserIDPermission = Config("DEFAULT_USER_ID_PERMISSION"); // Default User ID permission

        // Create fields
        $this->Fields = $this->fieldFactory->createAll($this);

        // APPOINTMENT_ID
        $this->APPOINTMENT_ID = $this->Fields['APPOINTMENT_ID'];
        $this->APPOINTMENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");
        $this->APPOINTMENT_ID->SourceTableVar = 'appointment_tbl';

        // PATIENT_ID
        $this->PATIENT_ID = $this->Fields['PATIENT_ID'];
        $this->PATIENT_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");
        $this->PATIENT_ID->SourceTableVar = 'appointment_tbl';

        // DOCTOR_ID
        $this->DOCTOR_ID = $this->Fields['DOCTOR_ID'];
        $this->DOCTOR_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");
        $this->DOCTOR_ID->SourceTableVar = 'appointment_tbl';

        // SCHEDULE_ID
        $this->SCHEDULE_ID = $this->Fields['SCHEDULE_ID'];
        $this->SCHEDULE_ID->DefaultErrorMessage = $this->language->phrase("IncorrectInteger");
        $this->SCHEDULE_ID->SourceTableVar = 'appointment_tbl';

        // CREATED_AT
        $this->CREATED_AT = $this->Fields['CREATED_AT'];
        $this->CREATED_AT->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));
        $this->CREATED_AT->SourceTableVar = 'appointment_tbl';

        // APPOINTMENT_DATE
        $this->APPOINTMENT_DATE = $this->Fields['APPOINTMENT_DATE'];
        $this->APPOINTMENT_DATE->GroupingFieldId = 1;
        $this->APPOINTMENT_DATE->ShowGroupHeaderAsRow = $this->ShowGroupHeaderAsRow;
        $this->APPOINTMENT_DATE->ShowCompactSummaryFooter = $this->ShowCompactSummaryFooter;
        $this->APPOINTMENT_DATE->GroupByType = "";
        $this->APPOINTMENT_DATE->GroupInterval = "0";
        $this->APPOINTMENT_DATE->GroupSql = "";
        $this->APPOINTMENT_DATE->DefaultErrorMessage = str_replace("%s", $httpContext["DATE_FORMAT"], $this->language->phrase("IncorrectDate"));
        $this->APPOINTMENT_DATE->SourceTableVar = 'appointment_tbl';

        // APPOINTMENT_TIME
        $this->APPOINTMENT_TIME = $this->Fields['APPOINTMENT_TIME'];
        $this->APPOINTMENT_TIME->DefaultErrorMessage = str_replace("%s", DateFormat(4), $this->language->phrase("IncorrectTime"));
        $this->APPOINTMENT_TIME->SourceTableVar = 'appointment_tbl';

        // STATUS
        $this->STATUS = $this->Fields['STATUS'];
        $this->STATUS->GroupingFieldId = 2;
        $this->STATUS->ShowGroupHeaderAsRow = $this->ShowGroupHeaderAsRow;
        $this->STATUS->ShowCompactSummaryFooter = $this->ShowCompactSummaryFooter;
        $this->STATUS->GroupByType = "";
        $this->STATUS->GroupInterval = "0";
        $this->STATUS->GroupSql = "";
        $this->STATUS->Lookup = new Lookup($this->STATUS, 'Appointment_report', false, '', ["","","",""], '', "", [], [], [], [], [], [], false, '', '', "");
        $this->STATUS->OptionCount = 3;
        $this->STATUS->SourceTableVar = 'appointment_tbl';

        // Chart1
        $this->Chart1 = new DbChart(
            $this->language,
            $this,
            'Chart1',
            'Chart1',
            'APPOINTMENT_DATE',
            'APPOINTMENT_ID',
            1004,
            '',
            0,
            'COUNT',
            800,
            400
        );
        $this->Chart1->Position = 4;
        $this->Chart1->PageBreakType = "before";
        $this->Chart1->YAxisFormat = [""];
        $this->Chart1->YFieldFormat = [""];
        $this->Chart1->SortType = 0;
        $this->Chart1->SortSequence = "";
        $this->Chart1->SqlXField = "`APPOINTMENT_DATE`";
        $this->Chart1->SqlYField = "COUNT(`APPOINTMENT_ID`)";
        $this->Chart1->SqlSeriesField = "''";
        $this->Chart1->SqlGroupBy = "`APPOINTMENT_DATE`";
        $this->Chart1->SqlOrderBy = "";
        $this->Chart1->SeriesDateType = "";
        $this->Chart1->XAxisDateFormat = 0;
        $this->Chart1->ID = "Appointment_report_Chart1"; // Chart ID
        $this->Chart1->setParameters([
            ["type", "1004"],
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

        // Call Table Load event
        $this->tableLoad();
    }

    // Get field settings
    public function getFieldDefinitions(): array
    {
        return [
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
                'HtmlTag' => 'NO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'IsAutoIncrement' => true,
                'IsPrimaryKey' => true,
                'Nullable' => false,
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'DOCTOR_ID' => [
                'FieldVar' => 'x_DOCTOR_ID', // Field variable name
                'Param' => 'DOCTOR_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'doctorId', // Field entity property name
                'Expression' => '`DOCTOR_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`DOCTOR_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`DOCTOR_ID`', // Virtual field expression (used in ListSQL)
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'SCHEDULE_ID' => [
                'FieldVar' => 'x_SCHEDULE_ID', // Field variable name
                'Param' => 'SCHEDULE_ID', // Field parameter name (Table class property name)
                'PropertyName' => 'scheduleId', // Field entity property name
                'Expression' => '`SCHEDULE_ID`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`SCHEDULE_ID`', // Field expression (used in basic search SQL)
                'Type' => 3, // Field type
                'DataType' => DataType::NUMBER, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::INTEGER, // Field Doctrine parameter type
                'Size' => 11, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`SCHEDULE_ID`', // Virtual field expression (used in ListSQL)
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN", "IS NULL", "IS NOT NULL"],
            ],
            'APPOINTMENT_DATE' => [
                'FieldVar' => 'x_APPOINTMENT_DATE', // Field variable name
                'Param' => 'APPOINTMENT_DATE', // Field parameter name (Table class property name)
                'PropertyName' => 'appointmentDate', // Field entity property name
                'Expression' => '`APPOINTMENT_DATE`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`APPOINTMENT_DATE`", 0, "DB"), // Field expression (used in basic search SQL)
                'Type' => 133, // Field type
                'DataType' => DataType::DATE, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 0, // Date time format
                'VirtualExpression' => '`APPOINTMENT_DATE`', // Virtual field expression (used in ListSQL)
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'APPOINTMENT_TIME' => [
                'FieldVar' => 'x_APPOINTMENT_TIME', // Field variable name
                'Param' => 'APPOINTMENT_TIME', // Field parameter name (Table class property name)
                'PropertyName' => 'appointmentTime', // Field entity property name
                'Expression' => '`APPOINTMENT_TIME`', // Field expression (used in SQL)
                'BasicSearchExpression' => CastDateFieldForLike("`APPOINTMENT_TIME`", 4, "DB"), // Field expression (used in basic search SQL)
                'Type' => 134, // Field type
                'DataType' => DataType::TIME, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 10, // Field size
                'DateTimeFormat' => 4, // Date time format
                'VirtualExpression' => '`APPOINTMENT_TIME`', // Virtual field expression (used in ListSQL)
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
                'SearchOperators' => ["=", "<>", "IN", "NOT IN", "<", "<=", ">", ">=", "BETWEEN", "NOT BETWEEN"],
            ],
            'STATUS' => [
                'FieldVar' => 'x_STATUS', // Field variable name
                'Param' => 'STATUS', // Field parameter name (Table class property name)
                'PropertyName' => 'status', // Field entity property name
                'Expression' => '`STATUS`', // Field expression (used in SQL)
                'BasicSearchExpression' => '`STATUS`', // Field expression (used in basic search SQL)
                'Type' => 200, // Field type
                'DataType' => DataType::STRING, // Field data type (DataType::XXX)
                'ParameterType' => ParameterType::STRING, // Field Doctrine parameter type
                'Size' => 9, // Field size
                'DateTimeFormat' => -1, // Date time format
                'VirtualExpression' => '`STATUS`', // Virtual field expression (used in ListSQL)
                'IsVirtual' => false, // Virtual field
                'ForceSelection' => false, // Autosuggest force selection
                'VirtualSearch' => false, // Search as virtual field
                'ViewTag' => 'FORMATTED TEXT', // View Tag
                'HtmlTag' => 'RADIO', // HTML Tag
                'IsUpload' => false, // Is upload field
                'InputTextType' => 'text',
                'Raw' => true,
                'SearchOperators' => ["=", "<>", "IS NULL", "IS NOT NULL"],
                'OptionCount' => 3,
            ],
        ];
    }

    // Field Visibility
    public function getFieldVisibility(string $fldParm): bool
    {
        return $this->$fldParm->Visible; // Returns original value
    }

    // Single column sort
    protected function updateSort(DbField $fld): void
    {
        if ($this->CurrentOrder == $fld->Name) {
            $sortField = $fld->Expression;
            $lastSort = $fld->getSort();
            if (in_array($this->CurrentOrderType, ["ASC", "DESC", "NO"])) {
                $curSort = $this->CurrentOrderType;
            } else {
                $curSort = $lastSort;
            }
            $fld->setSort($curSort);
            $lastOrderBy = in_array($lastSort, ["ASC", "DESC"]) ? $sortField . " " . $lastSort : "";
            $curOrderBy = in_array($curSort, ["ASC", "DESC"]) ? $sortField . " " . $curSort : "";
            if ($fld->GroupingFieldId == 0) {
                $this->setDetailOrderBy($curOrderBy); // Save to Session
            }
        } else {
            if ($fld->GroupingFieldId == 0) {
                $fld->setSort("");
            }
        }
    }

    // Get Sort SQL
    protected function sortSql(): string
    {
        $dtlSortSql = $this->getDetailOrderBy(); // Get ORDER BY for detail fields from session
        $grps = [];
        foreach ($this->Fields as $fld) {
            if (in_array($fld->getSort(), ["ASC", "DESC"])) {
                $fldsql = $fld->Expression;
                if ($fld->GroupingFieldId > 0) {
                    if ($fld->GroupSql != "") {
                        $grps[$fld->GroupingFieldId] = str_replace("%s", $fldsql, $fld->GroupSql) . " " . $fld->getSort();
                    } else {
                        $grps[$fld->GroupingFieldId] = $fldsql . " " . $fld->getSort();
                    }
                }
            }
        }
        $sortSql = implode(", ", array_values($grps));
        if ($dtlSortSql != "") {
            if ($sortSql != "") {
                $sortSql .= ", ";
            }
            $sortSql .= $dtlSortSql;
        }
        return $sortSql;
    }

    // First Group Field
    public function getSqlFirstGroupField(bool $alias = false): string
    {
        if ($this->sqlFirstGroupField != "") {
            return $this->sqlFirstGroupField;
        }
        $firstGroupField = $this->APPOINTMENT_DATE;
        $expr = $firstGroupField->Expression;
        if ($firstGroupField->GroupSql != "") {
            $expr = str_replace("%s", $firstGroupField->Expression, $firstGroupField->GroupSql);
            if ($alias) {
                $expr .= " AS " . QuotedName($firstGroupField->getGroupName(), $this->Dbid);
            }
        }
        return $expr;
    }

    public function setSqlFirstGroupField(string $v): void
    {
        $this->sqlFirstGroupField = $v;
    }

    // Select Group
    public function getSqlSelectGroup(int $index = 0): QueryBuilder
    {
        $groupField = $this->Fields[$this->GroupingFields[$index]];
        $expr = $groupField->Expression;
        if ($groupField->GroupSql != "") {
            $expr = str_replace("%s", $groupField->Expression, $groupField->GroupSql);
        }
        return $this->getQueryBuilder()->select($expr)->distinct();
    }

    // Order By Group
    public function getSqlOrderByGroup(): string
    {
        if ($this->sqlOrderByGroup != "") {
            return $this->sqlOrderByGroup;
        }
        return $this->getSqlFirstGroupField() . " DESC";
    }

    public function setSqlOrderByGroup(string $v): void
    {
        $this->sqlOrderByGroup = $v;
    }

    // Select Aggregate
    public function getSqlSelectAggregate(): QueryBuilder
    {
        return $this->getQueryBuilder()->select("COUNT(*) AS cnt_appointment_id");
    }

    // Aggregate
    public function getSqlAggregate(): string
    {
        return $this->sqlAggregate != "" ? $this->sqlAggregate : "";
    }

    public function setSqlAggregate(string $v): void
    {
        $this->sqlAggregate = $v;
    }

    // Select Count
    public function getSqlSelectCount(): QueryBuilder
    {
        return $this->getQueryBuilder()->select("COUNT(*)");
    }

    // Render X Axis for chart
    public function renderChartXAxis(string $chartVar, array $chartRow): array
    {
        return $chartRow;
    }

    // Get FROM clause
    public function getSqlFrom(): string
    {
        return $this->sqlFrom ?? "appointment_tbl";
    }

    // Set FROM clause
    public function setSqlFrom(string $v): void
    {
        $this->sqlFrom = $v;
    }

    // Get SELECT clause
    public function getSqlSelect(): QueryBuilder
    {
        $select = $this->getQueryBuilder()->select($this->getSqlSelectFields());
        $groupField = $this->APPOINTMENT_DATE;
        if ($groupField->GroupSql != "") {
            $expr = str_replace("%s", $groupField->Expression, $groupField->GroupSql) . " AS " . QuotedName($groupField->getGroupName(), $this->Dbid);
            $select->addSelect($expr);
        }
        $groupField = $this->STATUS;
        if ($groupField->GroupSql != "") {
            $expr = str_replace("%s", $groupField->Expression, $groupField->GroupSql) . " AS " . QuotedName($groupField->getGroupName(), $this->Dbid);
            $select->addSelect($expr);
        }
        return $select;
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

    // Record filter WHERE clause
    protected function sqlKeyFilter(): string
    {
        return "`APPOINTMENT_ID` = @APPOINTMENT_ID@";
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
        $val = $current ? $this->APPOINTMENT_ID->CurrentValue : $this->APPOINTMENT_ID->OldValue;
        if (IsEmpty($val)) {
            return [];
        } else {
            // Explicitly convert DateTime objects to string format
            $val = $val instanceof DateTimeInterface ? ConvertToString($val) : $val;
            $keys["APPOINTMENT_ID"] = $val;
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
        $keys = ["appointmentId"];
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
                $recordKey["APPOINTMENT_ID"] = $ar[0];
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
            $val = $row['APPOINTMENT_ID'] ?? null;
        } else {
            $val = !IsEmpty($this->APPOINTMENT_ID->OldValue) && !$current ? $this->APPOINTMENT_ID->OldValue : $this->APPOINTMENT_ID->CurrentValue;
        }
        if (!is_numeric($val)) {
            return []; // Invalid value
        }
        if ($val === null) {
            return []; // Invalid value
        }
        $recordKey["APPOINTMENT_ID"] = $val;
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
        return Session($name) ?? GetUrl("");
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
            "" => $this->language->phrase("View"),
            "" => $this->language->phrase("Edit"),
            "" => $this->language->phrase("Add"),
            default => ""
        };
    }

    // Default route URL
    public function getDefaultRouteUrl(): string
    {
        return "AppointmentReport";
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
        return "";
    }

    // View URL
    public function getViewUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("", $params);
        return $this->addMasterUrl($url);
    }

    // Detail view URL
    public function getDetailViewUrl(string $param = ""): string
    {
        $url = $this->keyUrl("", [$param]);
        return $this->addMasterUrl($url);
    }

    // Add URL
    public function getAddUrl(string $param = ""): string
    {
        $url = BuildUrl("", $param);
        return $this->addMasterUrl($url);
    }

    // Edit URL
    public function getEditUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("", $params);
        return $this->addMasterUrl($url);
    }

    // Inline edit URL
    public function getInlineEditUrl(): string
    {
        $url = $this->keyUrl("", "action=edit");
        return $this->addMasterUrl($url);
    }

    // Detail edit URL
    public function getDetailEditUrl(string $param = ""): string
    {
        $url = $this->keyUrl("", [$param]);
        return $this->addMasterUrl($url);
    }

    // Copy URL
    public function getCopyUrl(string $param = ""): string
    {
        $params = [];
        if ($param != "") {
            $params[] = $param;
        }
        $url = $this->keyUrl("", $params);
        return $this->addMasterUrl($url);
    }

    // Inline copy URL
    public function getInlineCopyUrl(): string
    {
        $url = $this->keyUrl("", "action=copy");
        return $this->addMasterUrl($url);
    }

    // Delete URL
    public function getDeleteUrl(string $param = ""): string
    {
        if ($this->UseAjaxActions && IsInfiniteScroll() && CurrentPageID() == "list") {
            return $this->keyUrl(GetApiUrl(Config("API_DELETE_ACTION") . "/" . $this->TableVar));
        } else {
            return $this->keyUrl("", $param);
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
        $json .= "\"APPOINTMENT_ID\":" . VarToJson($this->APPOINTMENT_ID->CurrentValue, "number");
        $json = "{" . $json . "}";
        return $htmlEncode ? HtmlEncode($json) : $json;
    }

    // Add key value to URL
    public function keyUrl(string $url, string|array $params = ""): string
    {
        if ($this->APPOINTMENT_ID->CurrentValue !== null) {
            $url .= "/" . $this->rawUrlEncode($this->APPOINTMENT_ID->CurrentValue);
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
            if (($keyValue = Param("APPOINTMENT_ID") ?? Route("appointmentId")) !== null) {
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
            if (!is_numeric($key[0])) { // APPOINTMENT_ID
                continue;
            }
            $recordKey["APPOINTMENT_ID"] = $key[0];
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
    public function updateLastInsertId(Entity\AppointmentReport $row, LifecycleEventArgs $args)
    {
        $this->APPOINTMENT_ID->setDbValue($row['APPOINTMENT_ID']);
    }

    // Save entity change set
    public function preUpdate(Entity\AppointmentReport $row, PreUpdateEventArgs $args): void
    {
        $oid = spl_object_id($row);
        $this->changeSets[$oid] = $args->getEntityChangeSet();
    }

    // Store identifiers before removal
    public function preRemove(Entity\AppointmentReport $row, LifecycleEventArgs $args): void
    {
        $this->snapshots[spl_object_id($row)] = $row->identifierValues();
    }

    // Table level events

    // Table Load event
    public function tableLoad(): void
    {
        // Enter your code here
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

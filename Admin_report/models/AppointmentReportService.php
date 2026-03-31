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
 * Summary Report Service classes
 */

// Report View
class AppointmentReportReportView {

    public function __construct(
        public readonly array $appointmentDateGroups,
        public readonly int $groupCount,
        public readonly array $pageSummary,
        public readonly int $pageCount,
        public readonly array $grandSummary,
        public readonly int $grandCount,
    ) {}

    public function groups(): array {
        return $this->appointmentDateGroups;
    }
}

// AppointmentDate Group View
class AppointmentReportAppointmentDateGroupView {

    public function __construct(
        public readonly DateTimeInterface|string $AppointmentDate,
        public readonly array $statusGroups,
        public readonly array $summary,
        public readonly int $recordCount,
        public readonly array $rows,
    ) {}

    public function groupValue(): DateTimeInterface|string {
        return $this->AppointmentDate;
    }

    public function groups(): array {
        return $this->statusGroups;
    }
}

// Status Group View
class AppointmentReportStatusGroupView {

    public function __construct(
        public readonly ?string $Status,
        public readonly array $details,
        public readonly array $summary,
        public readonly int $recordCount,
    ) {}

    public function groupValue(): ?string {
        return $this->Status;
    }
}

// Detail View
class AppointmentReportDetailView {

    public function __construct(
        public readonly int $appointmentId,
        public readonly int $patientId,
        public readonly int $doctorId,
        public readonly DateTimeInterface|string $appointmentTime,
    ) {}
}

// Service
class AppointmentReportService extends AppointmentReport
{

    public function __construct(
        Language $language,
        AdvancedSecurity $security,
        CSPBuilder $cspBuilder,
        CacheInterface $cache,
        FieldFactory $fieldFactory,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($language, $security, $cspBuilder, $cache, $fieldFactory, $dispatcher);
    }

    /**
     * Get report data
     *
     * @return AppointmentReportReportView
     */
    public function getReportData(string $where, string $orderBy, int $page = 1, int $pageSize = 3): AppointmentReportReportView
    {
        // Get total count from SQL directly
        $totalCount = 0;
        $sql = $this->buildReportSql($this->getSqlSelectCount(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $where, "");
        $result = $sql->executeQuery();
        if ($result && $cnt = $result->fetchOne()) {
            $totalCount = $cnt;
        }

        // Grand summary
        $grandSummary = [];

        // Get total from SQL directly
        $qb = $this->buildReportSql($this->getSqlSelectAggregate(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $where, "");
        $sql = $this->getSqlAggregate() ? sprintf($this->getSqlAggregate(), $qb->getSQL()) : $qb->getSQL();
        $result = $this->getConnection()->executeQuery($sql);
        $aggregateRecord = $result?->fetchAssociative() ?? [];
        if (count($aggregateRecord) > 0) {
            $smry = new ReportSummary();
            $smry->count = $aggregateRecord["cnt_appointment_id"] ?? 0;
            $grandSummary["APPOINTMENT_ID"] = $smry;
        } else { // Cannot get from SQL directly
            $sql = $this->buildReportSql($this->getSqlSelect(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $where, "");
            $result = $sql->executeQuery();
            $detailRecords = $result?->fetchAllAssociative() ?? [];
            $totalCount = count($detailRecords);
            $smry = new ReportSummary();
            $smry->count = $this->APPOINTMENT_ID->getCount($detailRecords, false);
            $grandSummary["APPOINTMENT_ID"] = $smry;
        }

        // Page summary
        $pageCount = 0;
        $pageSummary = [];

        // Get total number of groups
        $sql = $this->buildReportSql($this->getSqlSelectGroup(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $where, "");
        $groupCount = $this->getRecordCount($sql);

        // Get groups for the page
        $groups = [];
        if ($pageSize <= 0) {
            $pageSize = $groupCount;
        }
        if ($groupCount > 0) {
            $maxPage = ceil($groupCount / $pageSize);
            if ($page <= 0) {
                $page = 1;
            } elseif ($page > $maxPage) {
                $page = $maxPage;
            }
            $groupFld = $this->Fields[$this->GroupingFields[0]];
            $grpSort = UpdateSortFields($this->getSqlOrderByGroup(), $orderBy, 2); // Get grouping field only
            $sql = $this->buildReportSql($this->getSqlSelectGroup(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderByGroup(), $where, $grpSort);
            if ($pageSize > 0) {
                $sql->setFirstResult(max($page - 1, 0) * $pageSize)->setMaxResults($pageSize);
            }
            $groupRecords = $sql->executeQuery()->fetchAllNumeric();
            foreach ($groupRecords as $groupRecord) {
                $group = $this->getAppointmentDateGroupView($groupRecord[0], $where, $orderBy);
                $pageSummary = $this->accumSummary($pageSummary, $group->summary);
                $pageCount += $group->recordCount;
                $groups[] = $group;
            }
        }
        return new AppointmentReportReportView(
            $groups,
            $groupCount,
            $pageSummary,
            $pageCount,
            $grandSummary,
            $totalCount,
        );
    }

    /**
     * Get chart data
     *
     * @return array
     */
    public function getChartData(DbChart $chart, string $where, string $orderBy): array
    {
        $chartService = new ChartService($chart, $this);
        return $chartService->getChartData($where, $orderBy);
    }

    // Get Group View
    protected function getAppointmentDateGroupView(mixed $groupValue, string $where, string $orderBy): AppointmentReportAppointmentDateGroupView
    {
        $recordCount = 0;
        $summary = [];

        // Get group records
        $groupFld = $this->Fields["APPOINTMENT_DATE"];
        AddFilter($where, $groupFld->searchExpression() . " = " . QuotedValue($groupValue, $groupFld->searchDataType(), $this->Dbid));
        $sql = $this->buildReportSql($this->getSqlSelect(), $this->getSqlFrom(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $where, $orderBy);
        $result = $sql->executeQuery();
        $records = $result?->fetchAllAssociative() ?? [];
        $groups = [];
        $nextGroupFld = $this->Fields["STATUS"];
        $nextGroupFld->getDistinctValues($records, $nextGroupFld->getSort());
        foreach ($nextGroupFld->DistinctValues as $distinctValue) {
            $nextGroupFld->setGroupValue($distinctValue); // Set group value
            $nextGroupFld->getDistinctRecords($records, $nextGroupFld->groupValue());
            $group = $this->getStatusGroupView($nextGroupFld->groupValue(), $nextGroupFld->Records);
            $groups[] = $group;
            $recordCount += $group->recordCount;
            $summary = $this->accumSummary($summary, $group->summary);
        }
        return new AppointmentReportAppointmentDateGroupView(
            $groupValue,
            $groups,
            $summary,
            $recordCount,
            $records,
        );
    }

    // Get Group View
    protected function getStatusGroupView(mixed $groupValue, array $records): AppointmentReportStatusGroupView
    {
        $recordCount = 0;
        $summary = [];
        $details = [];
        foreach ($records as $detailRecord) {
            $detail = new AppointmentReportDetailView(
                $detailRecord["APPOINTMENT_ID"],
                $detailRecord["PATIENT_ID"],
                $detailRecord["DOCTOR_ID"],
                $detailRecord["APPOINTMENT_TIME"],
            );
            $details[] = $detail;
            $recordCount += 1;
            $detailSummary = [];
            $smry = new ReportSummary();
            $smry->recordCount = 1; // count
            $smry->count = 1; // count
            $detailSummary["APPOINTMENT_ID"] = $smry;
            $smry = new ReportSummary();
            $smry->recordCount = 1; // count
            $smry->count = 1; // count
            $detailSummary["PATIENT_ID"] = $smry;
            $smry = new ReportSummary();
            $smry->recordCount = 1; // count
            $smry->count = 1; // count
            $detailSummary["DOCTOR_ID"] = $smry;
            $smry = new ReportSummary();
            $smry->recordCount = 1; // count
            $smry->count = 1; // count
            $detailSummary["APPOINTMENT_TIME"] = $smry;
            $summary = $this->accumSummary($summary, $detailSummary);
        }
        return new AppointmentReportStatusGroupView(
            $groupValue,
            $details,
            $summary,
            $recordCount,
        );
    }

    // Accumulate summary
    protected function accumSummary(array $summary1, array $summary2): array
    {
        $smry = [];

        // APPOINTMENT_ID
        $smry["APPOINTMENT_ID"] = AccumulateSummary(
            $summary1["APPOINTMENT_ID"] ?? new ReportSummary(),
            $summary2["APPOINTMENT_ID"] ?? new ReportSummary()
        );
        return $smry;
    }
}

<?php

namespace PHPMaker2026\Project2\Db\Entity;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use PHPMaker2026\Project2\AdvancedUserInterface;
use PHPMaker2026\Project2\AdvancedSecurity;
use PHPMaker2026\Project2\UserProfile;
use PHPMaker2026\Project2\UserRepository;
use PHPMaker2026\Project2\CustomEntityRepository;
use PHPMaker2026\Project2\DefaultSequenceGenerator;
use PHPMaker2026\Project2\UuidGenerator;
use PHPMaker2026\Project2\Entity as BaseEntity;
use function PHPMaker2026\Project2\Config;
use function PHPMaker2026\Project2\EntityManager;
use function PHPMaker2026\Project2\ConvertToBool;
use function PHPMaker2026\Project2\ConvertToString;
use function PHPMaker2026\Project2\SameDateTime;
use function PHPMaker2026\Project2\RemoveXss;
use function PHPMaker2026\Project2\HtmlDecode;
use function PHPMaker2026\Project2\HashPassword;
use function PHPMaker2026\Project2\PhpEncrypt;
use function PHPMaker2026\Project2\PhpDecrypt;
use function PHPMaker2026\Project2\Security;
use function PHPMaker2026\Project2\IsEmpty;
use InvalidArgumentException;

/**
 * Entity class for 'Appointment_report' table
 */
#[Entity]
#[Table('Appointment_report')]
class AppointmentReport extends BaseEntity
{
    #[Id]
    #[Column(name: 'APPOINTMENT_ID', type: 'integer', unique: true, insertable: false, updatable: false)]
    #[GeneratedValue]
    private int $appointmentId;

    #[Column(name: 'PATIENT_ID', type: 'integer')]
    private int $patientId;

    #[Column(name: 'DOCTOR_ID', type: 'integer')]
    private int $doctorId;

    #[Column(name: 'SCHEDULE_ID', type: 'integer')]
    private int $scheduleId;

    #[Column(name: 'CREATED_AT', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $createdAt;

    #[Column(name: 'APPOINTMENT_DATE', type: 'date')]
    private DateTimeInterface $appointmentDate;

    #[Column(name: 'APPOINTMENT_TIME', type: 'time')]
    private DateTimeInterface $appointmentTime;

    #[Column(name: 'STATUS', type: 'string', nullable: true)]
    private ?string $status;

    public function __construct()
    {
        $this->status = 'SCHEDULED';
    }

    public function getAppointmentId(): int
    {
        return $this->appointmentId;
    }

    public function setAppointmentId(int $value): static
    {
        $this->appointmentId = $value;
        return $this;
    }

    public function getPatientId(): int
    {
        return $this->patientId;
    }

    public function setPatientId(int $value): static
    {
        $this->patientId = $value;
        return $this;
    }

    public function getDoctorId(): int
    {
        return $this->doctorId;
    }

    public function setDoctorId(int $value): static
    {
        $this->doctorId = $value;
        return $this;
    }

    public function getScheduleId(): int
    {
        return $this->scheduleId;
    }

    public function setScheduleId(int $value): static
    {
        $this->scheduleId = $value;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $value): static
    {
        if (!$this->isInitialized('createdAt') || !SameDateTime($this->createdAt, $value)) {
            $this->createdAt = $value;
        }
        return $this;
    }

    public function getAppointmentDate(): DateTimeInterface
    {
        return $this->appointmentDate;
    }

    public function setAppointmentDate(DateTimeInterface $value): static
    {
        if (!$this->isInitialized('appointmentDate') || !SameDateTime($this->appointmentDate, $value)) {
            $this->appointmentDate = $value;
        }
        return $this;
    }

    public function getAppointmentTime(): DateTimeInterface
    {
        return $this->appointmentTime;
    }

    public function setAppointmentTime(DateTimeInterface $value): static
    {
        if (!$this->isInitialized('appointmentTime') || !SameDateTime($this->appointmentTime, $value)) {
            $this->appointmentTime = $value;
        }
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $value): static
    {
        if (!in_array($value, ["SCHEDULED", "COMPLETED", "CANCELLED"])) {
            throw new InvalidArgumentException("Invalid 'STATUS' value");
        }
        $this->status = $value;
        return $this;
    }
}

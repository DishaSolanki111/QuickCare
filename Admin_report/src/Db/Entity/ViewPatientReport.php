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
 * Entity class for 'view_patient_report' table
 */
#[Entity]
#[Table('view_patient_report')]
class ViewPatientReport extends BaseEntity
{
    #[Column(name: 'PATIENT_ID', type: 'integer', insertable: false)]
    #[GeneratedValue]
    private int $patientId;

    #[Column(name: 'Patient_Name', type: 'string', insertable: false, updatable: false)]
    private string $patientName;

    #[Column(name: 'GENDER', type: 'string')]
    private string $gender;

    #[Column(name: 'BLOOD_GROUP', type: 'string')]
    private string $bloodGroup;

    #[Column(name: 'PHONE', type: 'bigint')]
    private string $phone;

    #[Column(name: 'EMAIL', type: 'string')]
    private string $email;

    #[Column(name: 'Total_Appointments', type: 'bigint', insertable: false, updatable: false)]
    private string $totalAppointments;

    #[Column(name: 'Completed_Appointments', type: 'decimal', nullable: true, insertable: false, updatable: false)]
    private ?string $completedAppointments;

    #[Column(name: 'Upcoming_Appointments', type: 'decimal', nullable: true, insertable: false, updatable: false)]
    private ?string $upcomingAppointments;

    #[Column(name: 'Cancelled_Appointments', type: 'decimal', nullable: true, insertable: false, updatable: false)]
    private ?string $cancelledAppointments;

    #[Column(name: 'Last_Visit', type: 'date', nullable: true, insertable: false, updatable: false)]
    private ?DateTimeInterface $lastVisit;

    #[Column(name: 'First_Visit', type: 'date', nullable: true, insertable: false, updatable: false)]
    private ?DateTimeInterface $firstVisit;

    #[Column(name: 'Total_Prescriptions', type: 'bigint', insertable: false, updatable: false)]
    private string $totalPrescriptions;

    #[Column(name: 'Total_Amount_Paid', type: 'decimal', nullable: true, insertable: false, updatable: false)]
    private ?string $totalAmountPaid;

    #[Column(name: 'Avg_Rating_Given', type: 'decimal', nullable: true, insertable: false, updatable: false)]
    private ?string $avgRatingGiven;

    #[Id]
    #[Column(type: 'integer')]
    private int $id; // Fake primary key

    public function getPatientId(): int
    {
        return $this->patientId;
    }

    public function setPatientId(int $value): static
    {
        $this->patientId = $value;
        return $this;
    }

    public function getPatientName(): string
    {
        return HtmlDecode($this->patientName);
    }

    public function setPatientName(string $value): static
    {
        $this->patientName = RemoveXss($value);
        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $value): static
    {
        if (!in_array($value, ["MALE", "FEMALE", "OTHER"])) {
            throw new InvalidArgumentException("Invalid 'GENDER' value");
        }
        $this->gender = $value;
        return $this;
    }

    public function getBloodGroup(): string
    {
        return $this->bloodGroup;
    }

    public function setBloodGroup(string $value): static
    {
        if (!in_array($value, ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"])) {
            throw new InvalidArgumentException("Invalid 'BLOOD_GROUP' value");
        }
        $this->bloodGroup = $value;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $value): static
    {
        $this->phone = $value;
        return $this;
    }

    public function getEmail(): string
    {
        return HtmlDecode($this->email);
    }

    public function setEmail(string $value): static
    {
        $this->email = RemoveXss($value);
        return $this;
    }

    public function getTotalAppointments(): string
    {
        return $this->totalAppointments;
    }

    public function setTotalAppointments(string $value): static
    {
        $this->totalAppointments = $value;
        return $this;
    }

    public function getCompletedAppointments(): ?string
    {
        return $this->completedAppointments;
    }

    public function setCompletedAppointments(?string $value): static
    {
        $this->completedAppointments = $value;
        return $this;
    }

    public function getUpcomingAppointments(): ?string
    {
        return $this->upcomingAppointments;
    }

    public function setUpcomingAppointments(?string $value): static
    {
        $this->upcomingAppointments = $value;
        return $this;
    }

    public function getCancelledAppointments(): ?string
    {
        return $this->cancelledAppointments;
    }

    public function setCancelledAppointments(?string $value): static
    {
        $this->cancelledAppointments = $value;
        return $this;
    }

    public function getLastVisit(): ?DateTimeInterface
    {
        return $this->lastVisit;
    }

    public function setLastVisit(?DateTimeInterface $value): static
    {
        if (!$this->isInitialized('lastVisit') || !SameDateTime($this->lastVisit, $value)) {
            $this->lastVisit = $value;
        }
        return $this;
    }

    public function getFirstVisit(): ?DateTimeInterface
    {
        return $this->firstVisit;
    }

    public function setFirstVisit(?DateTimeInterface $value): static
    {
        if (!$this->isInitialized('firstVisit') || !SameDateTime($this->firstVisit, $value)) {
            $this->firstVisit = $value;
        }
        return $this;
    }

    public function getTotalPrescriptions(): string
    {
        return $this->totalPrescriptions;
    }

    public function setTotalPrescriptions(string $value): static
    {
        $this->totalPrescriptions = $value;
        return $this;
    }

    public function getTotalAmountPaid(): ?string
    {
        return $this->totalAmountPaid;
    }

    public function setTotalAmountPaid(?string $value): static
    {
        $this->totalAmountPaid = $value;
        return $this;
    }

    public function getAvgRatingGiven(): ?string
    {
        return $this->avgRatingGiven;
    }

    public function setAvgRatingGiven(?string $value): static
    {
        $this->avgRatingGiven = $value;
        return $this;
    }
}

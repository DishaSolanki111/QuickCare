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
 * Entity class for 'view_payment_report' table
 */
#[Entity]
#[Table('view_payment_report')]
class ViewPaymentReport extends BaseEntity
{
    #[Column(name: 'PAYMENT_ID', type: 'integer', insertable: false, updatable: false)]
    #[GeneratedValue]
    private int $paymentId;

    #[Column(name: 'TRANSACTION_ID', type: 'string')]
    private string $transactionId;

    #[Column(name: 'Patient_Name', type: 'string', insertable: false, updatable: false)]
    private string $patientName;

    #[Column(name: 'Doctor_Name', type: 'string', insertable: false, updatable: false)]
    private string $doctorName;

    #[Column(name: 'AMOUNT', type: 'decimal')]
    private string $amount;

    #[Column(name: 'PAYMENT_MODE', type: 'string')]
    private string $paymentMode;

    #[Column(name: 'Payment_Status', type: 'string')]
    private string $paymentStatus;

    #[Column(name: 'PAYMENT_DATE', type: 'date')]
    private DateTimeInterface $paymentDate;

    #[Column(name: 'Day_Name', type: 'string', nullable: true, insertable: false, updatable: false)]
    private ?string $dayName;

    #[Column(name: 'Week_Number', type: 'integer', nullable: true, insertable: false, updatable: false)]
    private ?int $weekNumber;

    #[Column(name: 'Month_Number', type: 'integer', nullable: true, insertable: false, updatable: false)]
    private ?int $monthNumber;

    #[Column(name: 'Month_Name', type: 'string', nullable: true, insertable: false, updatable: false)]
    private ?string $monthName;

    #[Column(name: 'Year', type: 'integer', nullable: true, insertable: false, updatable: false)]
    private ?int $year;

    #[Id]
    #[Column(type: 'integer')]
    private int $id; // Fake primary key

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }

    public function setPaymentId(int $value): static
    {
        $this->paymentId = $value;
        return $this;
    }

    public function getTransactionId(): string
    {
        return HtmlDecode($this->transactionId);
    }

    public function setTransactionId(string $value): static
    {
        $this->transactionId = RemoveXss($value);
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

    public function getDoctorName(): string
    {
        return HtmlDecode($this->doctorName);
    }

    public function setDoctorName(string $value): static
    {
        $this->doctorName = RemoveXss($value);
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $value): static
    {
        $this->amount = $value;
        return $this;
    }

    public function getPaymentMode(): string
    {
        return $this->paymentMode;
    }

    public function setPaymentMode(string $value): static
    {
        if (!in_array($value, ["CREDIT CARD", "GOOGLE PAY", "UPI", "NET BANKING"])) {
            throw new InvalidArgumentException("Invalid 'PAYMENT_MODE' value");
        }
        $this->paymentMode = $value;
        return $this;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $value): static
    {
        if (!in_array($value, ["COMPLETED", "FAILED", "REFUNDED"])) {
            throw new InvalidArgumentException("Invalid 'Payment_Status' value");
        }
        $this->paymentStatus = $value;
        return $this;
    }

    public function getPaymentDate(): DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(DateTimeInterface $value): static
    {
        if (!$this->isInitialized('paymentDate') || !SameDateTime($this->paymentDate, $value)) {
            $this->paymentDate = $value;
        }
        return $this;
    }

    public function getDayName(): ?string
    {
        return HtmlDecode($this->dayName);
    }

    public function setDayName(?string $value): static
    {
        $this->dayName = RemoveXss($value);
        return $this;
    }

    public function getWeekNumber(): ?int
    {
        return $this->weekNumber;
    }

    public function setWeekNumber(?int $value): static
    {
        $this->weekNumber = $value;
        return $this;
    }

    public function getMonthNumber(): ?int
    {
        return $this->monthNumber;
    }

    public function setMonthNumber(?int $value): static
    {
        $this->monthNumber = $value;
        return $this;
    }

    public function getMonthName(): ?string
    {
        return HtmlDecode($this->monthName);
    }

    public function setMonthName(?string $value): static
    {
        $this->monthName = RemoveXss($value);
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $value): static
    {
        $this->year = $value;
        return $this;
    }
}

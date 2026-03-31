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
 * Entity class for 'refund_tbl' table
 */
#[Entity]
#[Table('refund_tbl')]
class RefundTbl extends BaseEntity
{
    #[Id]
    #[Column(name: 'REFUND_ID', type: 'integer', unique: true, insertable: false)]
    #[GeneratedValue]
    private int $refundId;

    #[Column(name: 'PAYMENT_ID', type: 'integer')]
    private int $paymentId;

    #[Column(name: 'APPOINTMENT_ID', type: 'integer')]
    private int $appointmentId;

    #[Column(name: 'PATIENT_ID', type: 'integer')]
    private int $patientId;

    #[Column(name: 'REFUND_AMOUNT', type: 'decimal')]
    private string $refundAmount;

    #[Column(name: 'REFUND_DATE', type: 'date')]
    private DateTimeInterface $refundDate;

    #[Column(name: 'REFUND_STATUS', type: 'string')]
    private string $refundStatus;

    #[Column(name: 'REFUND_REASON', type: 'string')]
    private string $refundReason;

    #[Column(name: 'REFUND_TXN_ID', type: 'string')]
    private string $refundTxnId;

    #[Column(name: 'CREATED_AT', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->refundStatus = 'PENDING';
        $this->refundReason = 'Patient cancelled appointment';
    }

    public function getRefundId(): int
    {
        return $this->refundId;
    }

    public function setRefundId(int $value): static
    {
        $this->refundId = $value;
        return $this;
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }

    public function setPaymentId(int $value): static
    {
        $this->paymentId = $value;
        return $this;
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

    public function getRefundAmount(): string
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(string $value): static
    {
        $this->refundAmount = $value;
        return $this;
    }

    public function getRefundDate(): DateTimeInterface
    {
        return $this->refundDate;
    }

    public function setRefundDate(DateTimeInterface $value): static
    {
        if (!$this->isInitialized('refundDate') || !SameDateTime($this->refundDate, $value)) {
            $this->refundDate = $value;
        }
        return $this;
    }

    public function getRefundStatus(): string
    {
        return $this->refundStatus;
    }

    public function setRefundStatus(string $value): static
    {
        if (!in_array($value, ["PENDING", "PROCESSED", "REJECTED"])) {
            throw new InvalidArgumentException("Invalid 'REFUND_STATUS' value");
        }
        $this->refundStatus = $value;
        return $this;
    }

    public function getRefundReason(): string
    {
        return HtmlDecode($this->refundReason);
    }

    public function setRefundReason(string $value): static
    {
        $this->refundReason = RemoveXss($value);
        return $this;
    }

    public function getRefundTxnId(): string
    {
        return HtmlDecode($this->refundTxnId);
    }

    public function setRefundTxnId(string $value): static
    {
        $this->refundTxnId = RemoveXss($value);
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
}

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
 * Entity class for 'view_prescription_report' table
 */
#[Entity]
#[Table('view_prescription_report')]
class ViewPrescriptionReport extends BaseEntity
{
    #[Column(name: 'PRESCRIPTION_ID', type: 'integer', insertable: false)]
    #[GeneratedValue]
    private int $prescriptionId;

    #[Column(name: 'ISSUE_DATE', type: 'date')]
    private DateTimeInterface $issueDate;

    #[Column(name: 'Patient_Name', type: 'string', insertable: false, updatable: false)]
    private string $patientName;

    #[Column(name: 'Doctor_Name', type: 'string', insertable: false, updatable: false)]
    private string $doctorName;

    #[Column(name: 'Specialisation', type: 'string')]
    private string $specialisation;

    #[Column(name: 'SYMPTOMS', type: 'text')]
    private string $symptoms;

    #[Column(name: 'DIAGNOSIS', type: 'text')]
    private string $diagnosis;

    #[Column(name: 'DIABETES', type: 'string')]
    private string $diabetes;

    #[Column(name: 'BLOOD_PRESSURE', type: 'smallint')]
    private int $bloodPressure;

    #[Column(name: 'ADDITIONAL_NOTES', type: 'text')]
    private string $additionalNotes;

    #[Id]
    #[Column(type: 'integer')]
    private int $id; // Fake primary key

    public function getPrescriptionId(): int
    {
        return $this->prescriptionId;
    }

    public function setPrescriptionId(int $value): static
    {
        $this->prescriptionId = $value;
        return $this;
    }

    public function getIssueDate(): DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(DateTimeInterface $value): static
    {
        if (!$this->isInitialized('issueDate') || !SameDateTime($this->issueDate, $value)) {
            $this->issueDate = $value;
        }
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

    public function getSpecialisation(): string
    {
        return HtmlDecode($this->specialisation);
    }

    public function setSpecialisation(string $value): static
    {
        $this->specialisation = RemoveXss($value);
        return $this;
    }

    public function getSymptoms(): string
    {
        return HtmlDecode($this->symptoms);
    }

    public function setSymptoms(string $value): static
    {
        $this->symptoms = RemoveXss($value);
        return $this;
    }

    public function getDiagnosis(): string
    {
        return HtmlDecode($this->diagnosis);
    }

    public function setDiagnosis(string $value): static
    {
        $this->diagnosis = RemoveXss($value);
        return $this;
    }

    public function getDiabetes(): string
    {
        return $this->diabetes;
    }

    public function setDiabetes(string $value): static
    {
        if (!in_array($value, ["NO", "TYPE-1", "TYPE-2", "PRE-DIABTIC"])) {
            throw new InvalidArgumentException("Invalid 'DIABETES' value");
        }
        $this->diabetes = $value;
        return $this;
    }

    public function getBloodPressure(): int
    {
        return $this->bloodPressure;
    }

    public function setBloodPressure(int $value): static
    {
        $this->bloodPressure = $value;
        return $this;
    }

    public function getAdditionalNotes(): string
    {
        return HtmlDecode($this->additionalNotes);
    }

    public function setAdditionalNotes(string $value): static
    {
        $this->additionalNotes = RemoveXss($value);
        return $this;
    }
}

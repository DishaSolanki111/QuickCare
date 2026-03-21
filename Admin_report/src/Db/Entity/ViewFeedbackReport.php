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
 * Entity class for 'view_feedback_report' table
 */
#[Entity]
#[Table('view_feedback_report')]
class ViewFeedbackReport extends BaseEntity
{
    #[Column(name: 'FEEDBACK_ID', type: 'integer', insertable: false)]
    #[GeneratedValue]
    private int $feedbackId;

    #[Column(name: 'Patient_Name', type: 'string', insertable: false, updatable: false)]
    private string $patientName;

    #[Column(name: 'Doctor_Name', type: 'string', insertable: false, updatable: false)]
    private string $doctorName;

    #[Column(name: 'Specialisation', type: 'string')]
    private string $specialisation;

    #[Column(name: 'RATING', type: 'integer')]
    private int $rating;

    #[Column(name: 'COMMENTS', type: 'string')]
    private string $comments;

    #[Column(name: 'APPOINTMENT_DATE', type: 'date')]
    private DateTimeInterface $appointmentDate;

    #[Id]
    #[Column(type: 'integer')]
    private int $id; // Fake primary key

    public function getFeedbackId(): int
    {
        return $this->feedbackId;
    }

    public function setFeedbackId(int $value): static
    {
        $this->feedbackId = $value;
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

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $value): static
    {
        $this->rating = $value;
        return $this;
    }

    public function getComments(): string
    {
        return HtmlDecode($this->comments);
    }

    public function setComments(string $value): static
    {
        $this->comments = RemoveXss($value);
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
}

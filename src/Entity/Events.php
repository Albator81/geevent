<?php

namespace App\Entity;

use App\Enum\Type;
use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6)]
    private ?string $longitude = null;

    #[ORM\Column]
    private ?\DateTime $start_datetime = null;

    #[ORM\Column]
    private ?\DateTime $end_datetime = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $created_by = null;

    /**
     * @var Collection<int, Sponsors>
     */
    #[ORM\ManyToMany(targetEntity: Sponsors::class, mappedBy: 'event')]
    private Collection $sponsors;

    #[ORM\Column(enumType: Type::class)]
    private ?Type $type = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'eventsJoined')]
    private Collection $participants;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $cover = null;

    public function __construct()
    {
        $this->sponsors = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getStartDatetime(): ?\DateTime
    {
        return $this->start_datetime;
    }

    public function setStartDatetime(\DateTime $start_datetime): static
    {
        $this->start_datetime = $start_datetime;

        return $this;
    }

    public function getEndDatetime(): ?\DateTime
    {
        return $this->end_datetime;
    }

    public function setEndDatetime(\DateTime $end_datetime): static
    {
        $this->end_datetime = $end_datetime;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return Collection<int, Sponsors>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsors $sponsor): static
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
            $sponsor->addEvent($this);
        }

        return $this;
    }

    public function removeSponsor(Sponsors $sponsor): static
    {
        if ($this->sponsors->removeElement($sponsor)) {
            $sponsor->removeEvent($this);
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addEventsJoined($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeEventsJoined($this);
        }

        return $this;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function setCover($cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function __toString(): string
    {
        $typeLabel = $this->type->value;
        $date = $this->start_datetime->format('d/m/Y');
        return sprintf('[%s %s] %s', $typeLabel, $date, $this->title);
    }
}

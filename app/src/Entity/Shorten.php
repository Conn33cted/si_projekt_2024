<?php
/**
 * Shorten entity.
 */

namespace App\Entity;

use App\Repository\ShortenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Shorten.
 */
#[ORM\Entity(repositoryClass: ShortenRepository::class)]
class Shorten
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Shorten input.
     */
    #[ORM\Column(length: 255)]
    private ?string $shortenIn = null;

    /**
     * Shorten output.
     */
    #[ORM\Column(length: 191, unique: true)]
    private ?string $shortenOut = null;

    /**
     * Addition date.
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addDate = null;

    /**
     * User.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    /**
     * Guest.
     */
    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: Guest::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'shorten_guest')]
    private ?Guest $guest = null;

    /**
     * Tags.
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'shorten')]
    private Collection $tags;

    /**
     * Click counter.
     */
    #[ORM\Column]
    private ?int $clickCounter = null;

    /**
     * Blocked status.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isBlocked = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get shorten input.
     *
     * @return string|null Shorten input
     */
    public function getShortenIn(): ?string
    {
        return $this->shortenIn;
    }

    /**
     * Set shorten input.
     *
     * @param string $shortenIn Shorten input
     *
     * @return self
     */
    public function setShortenIn(string $shortenIn): self
    {
        $this->shortenIn = $shortenIn;

        return $this;
    }

    /**
     * Get shorten output.
     *
     * @return string|null Shorten output
     */
    public function getShortenOut(): ?string
    {
        return $this->shortenOut;
    }

    /**
     * Set shorten output.
     *
     * @param string $shortenOut Shorten output
     *
     * @return self
     */
    public function setShortenOut(string $shortenOut): self
    {
        $this->shortenOut = $shortenOut;

        return $this;
    }

    /**
     * Get addition date.
     *
     * @return \DateTimeInterface|null Addition date
     */
    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    /**
     * Set addition date.
     *
     * @param \DateTimeInterface $addDate Addition date
     *
     * @return self
     */
    public function setAddDate(\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param User|null $user User
     *
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get guest user.
     *
     * @return Guest|null Guest
     */
    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    /**
     * Set guest.
     *
     * @param Guest|null $guest Guest
     *
     * @return self
     */
    public function setGuest(?Guest $guest): self
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return Collection<int, Tag> Tags
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag Tag
     *
     * @return self
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addShorten($this);
        }

        return $this;
    }

    /**
     * Remove tag.
     *
     * @param Tag $tag Tag
     *
     * @return self
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeShorten($this);
        }

        return $this;
    }

    /**
     * Get click counter.
     *
     * @return int|null Click counter
     */
    public function getClickCounter(): ?int
    {
        return $this->clickCounter;
    }

    /**
     * Set click counter.
     *
     * @param int $clickCounter Click counter
     *
     * @return self
     */
    public function setClickCounter(int $clickCounter): self
    {
        $this->clickCounter = $clickCounter;

        return $this;
    }

    /**
     * Get blocked status.
     *
     * @return bool Blocked status
     */
    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    /**
     * Set blocked status.
     *
     * @param bool $isBlocked Blocked status
     *
     * @return self
     */
    public function setBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}

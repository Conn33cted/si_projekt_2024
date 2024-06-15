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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $shortenIn = null;

    #[ORM\Column(length: 191, unique: true)]
    private ?string $shortenOut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addDate = null;

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

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'shorten')]
    private Collection $tags;

    #[ORM\Column]
    private ?int $clickCounter = null;

    /**
     *Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Function get Id.
     *
     * @return int|null Return Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Function Get Shorten In.
     *
     * @return string|null Return Shorten In
     */
    public function getShortenIn(): ?string
    {
        return $this->shortenIn;
    }

    /**
     * Function Set Shorten In.
     *
     * @param string $shortenIn New Shorten In
     *
     * @return $this Return this
     */
    public function setShortenIn(string $shortenIn): self
    {
        $this->shortenIn = $shortenIn;

        return $this;
    }

    /**
     * Function get Shorten Out.
     *
     * @return string|null Return shorten out
     */
    public function getShortenOut(): ?string
    {
        return $this->shortenOut;
    }

    /**
     * Function Set Shorten Out.
     *
     * @param string $shortenOut New Shorten Out
     *
     * @return $this Return entity
     */
    public function setShortenOut(string $shortenOut): self
    {
        $this->shortenOut = $shortenOut;

        return $this;
    }

    /**
     * Function get Add Date.
     *
     * @return \DateTimeImmutable|null Return addDate
     */
    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    /**
     * Function Set Add Date.
     *
     * @param \DateTimeInterface $addDate New Add Date
     *
     * @return $this Return Entity
     */
    public function setAddDate(\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * Function Get User.
     *
     * @return User|null Return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Function Set User.
     *
     * @param User|null $user New User
     *
     * @return $this Return Entity
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Function get Guest User.
     *
     * @return Guest|null Return Guest
     */
    public function getGuest(): ?Guest
    {
        return $this->guest;
    }

    /**
     * Function Set Guest.
     *
     * @param Guest|null $guest Guest
     *
     * @return $this Return Entity
     */
    public function setGuest(?Guest $guest): self
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * @return Collection<int, Tag> Return Tags
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Function Add Tag.
     *
     * @param Tag $tag New Tag
     *
     * @return $this Return Entity
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
     * Function Remove Tag.
     *
     * @param Tag $tag Tag To Remove
     *
     * @return $this Return entity
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeShorten($this);
        }

        return $this;
    }

    /**
     * Function get Click Counter.
     *
     * @return int|null Return ClickCounter
     */
    public function getClickCounter(): ?int
    {
        return $this->clickCounter;
    }

    /**
     * Function Set Click Counter.
     *
     * @param int $clickCounter New Number
     *
     * @return $this Return Entity
     */
    public function setClickCounter(int $clickCounter): self
    {
        $this->clickCounter = $clickCounter;

        return $this;
    }
}

<?php

/**
 * Entity Guest.
 */

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Guest.
 */
#[ORM\Entity(repositoryClass: GuestRepository::class)]
class Guest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $guestEmail = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'integer')]
    private int $creationCount = 0;

    /**
     * Get the ID of the guest.
     *
     * @return int|null ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the guest email.
     *
     * @return string|null Guest email
     */
    public function getGuestEmail(): ?string
    {
        return $this->guestEmail;
    }

    /**
     * Set the guest email.
     *
     * @param string $guestEmail Guest email
     *
     * @return $this
     */
    public function setGuestEmail(string $guestEmail): self
    {
        $this->guestEmail = $guestEmail;

        return $this;
    }

    /**
     * Get the guest identifier.
     *
     * @return string|null Guest identifier
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Set the guest identifier.
     *
     * @param string $identifier Guest identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get the creation count.
     *
     * @return int Creation count
     */
    public function getCreationCount(): int
    {
        return $this->creationCount;
    }

    /**
     * Set the creation count.
     *
     * @param int $creationCount Creation count
     *
     * @return $this
     */
    public function setCreationCount(int $creationCount): self
    {
        $this->creationCount = $creationCount;

        return $this;
    }
}

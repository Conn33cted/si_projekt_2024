<?php

/**Entity Guest*/

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

    /**
     * Function getId.
     *
     * @return int|null Return of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Function getGuestEmail.
     *
     * @return string|null Return of GuestEmail
     */
    public function getGuestEmail(): ?string
    {
        return $this->guestEmail;
    }

    /**
     * Set Guest Email.
     *
     * @param string $guestEmail Guest email
     *
     * @return $this Entity
     */
    public function setGuestEmail(string $guestEmail): self
    {
        $this->guestEmail = $guestEmail;

        return $this;
    }
}

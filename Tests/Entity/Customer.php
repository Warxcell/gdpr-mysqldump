<?php
declare(strict_types=1);

namespace Arxy\GdprDump\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="customers")
 */
class Customer
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="first_name")
     */
    private $firstName;

    /**
     * @var string|null
     * @ORM\Column(name="last_name")
     */
    private $lastName;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", name="birth_date")
     */
    private $birthDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }


    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
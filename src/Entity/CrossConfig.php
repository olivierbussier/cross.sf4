<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CrossConfigRepository")
 */
class CrossConfig
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEdition;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEdition(): ?DateTimeInterface
    {
        return $this->dateEdition;
    }

    public function setDateEdition(DateTimeInterface $dateEdition): self
    {
        $this->dateEdition = $dateEdition;

        return $this;
    }
}

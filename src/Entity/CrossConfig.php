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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienTrail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienCross;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienMarche;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lienPetit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fTrail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fCross;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fMarche;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fPetit;

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

    public function getLienTrail(): ?string
    {
        return $this->lienTrail;
    }

    public function setLienTrail(string $lienTrail): self
    {
        $this->lienTrail = $lienTrail;

        return $this;
    }

    public function getLienCross(): ?string
    {
        return $this->lienCross;
    }

    public function setLienCross(string $lienCross): self
    {
        $this->lienCross = $lienCross;

        return $this;
    }

    public function getLienMarche(): ?string
    {
        return $this->lienMarche;
    }

    public function setLienMarche(string $lienMarche): self
    {
        $this->lienMarche = $lienMarche;

        return $this;
    }

    public function getLienPetit(): ?string
    {
        return $this->lienPetit;
    }

    public function setLienPetit(string $lienPetit): self
    {
        $this->lienPetit = $lienPetit;

        return $this;
    }

    public function getFTrail(): ?bool
    {
        return $this->fTrail;
    }

    public function setFTrail(bool $fTrail): self
    {
        $this->fTrail = $fTrail;

        return $this;
    }

    public function getFCross(): ?bool
    {
        return $this->fCross;
    }

    public function setFCross(bool $fCross): self
    {
        $this->fCross = $fCross;

        return $this;
    }

    public function getFMarche(): ?bool
    {
        return $this->fMarche;
    }

    public function setFMarche(bool $fMarche): self
    {
        $this->fMarche = $fMarche;

        return $this;
    }

    public function getFPetit(): ?bool
    {
        return $this->fPetit;
    }

    public function setFPetit(bool $fPetit): self
    {
        $this->fPetit = $fPetit;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultatRepository")
 */
class Resultat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $anneeCross;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $course;

    /**
     * @ORM\Column(type="integer")
     */
    private $classement;

    /**
     * @ORM\Column(type="integer")
     */
    private $dossard;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $temps;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $ecart;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $vitesse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    private $fichier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeCross(): ?int
    {
        return $this->anneeCross;
    }

    public function setAnneeCross(int $anneeCross): self
    {
        $this->anneeCross = $anneeCross;

        return $this;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(string $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getClassement(): ?int
    {
        return $this->classement;
    }

    public function setClassement(int $classement): self
    {
        $this->classement = $classement;

        return $this;
    }

    public function getDossard(): ?int
    {
        return $this->dossard;
    }

    public function setDossard(int $dossard): self
    {
        $this->dossard = $dossard;

        return $this;
    }

    public function getTemps(): ?string
    {
        return $this->temps;
    }

    public function setTemps(string $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getEcart(): ?string
    {
        return $this->ecart;
    }

    public function setEcart(string $ecart): self
    {
        $this->ecart = $ecart;

        return $this;
    }

    public function getVitesse(): ?string
    {
        return $this->vitesse;
    }

    public function setVitesse(string $vitesse): self
    {
        $this->vitesse = $vitesse;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * @param mixed $fichier
     */
    public function setFichier($fichier): void
    {
        $this->fichier = $fichier;
    }
}

<?php

namespace App\Services;

use App\Entity\CrossConfig;
use App\Repository\CrossConfigRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DateService
{

    private $dateEdition;

    public function __construct(RegistryInterface $doctrine)
    {

        /**
         * @var $configRepo CrossConfigRepository
         */
        $configRepo  = $doctrine->getRepository(CrossConfig::class);
        $blogs = $configRepo->getDateEdition();
        $this->dateEdition = $blogs->getDateEdition();
    }

    public function getDate()
    {
        return $this->dateEdition;
    }
}

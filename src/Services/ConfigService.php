<?php

namespace App\Services;

use App\Entity\CrossConfig;
use App\Repository\CrossConfigRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ConfigService
{
    /** @var CrossConfig $config */
    private $config;

    public function __construct(RegistryInterface $doctrine)
    {

        /**
         * @var $configRepo CrossConfigRepository
         */
        $configRepo  = $doctrine->getRepository(CrossConfig::class);
        $this->config = $configRepo->getConfig();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getDateEdition()
    {
        return $this->config->getDateEdition();
    }
}

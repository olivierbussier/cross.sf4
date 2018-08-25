<?php

namespace App\Twig;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Twig\Extension\AbstractExtension;
use Twig_Function;

class CustomExtensions extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('check_droits'      , array($this, 'checkDroits')),
            new Twig_Function('readfile'          , array($this, 'readFile')),
            new Twig_Function('fileExists'        , array($this, 'fileExists')),
            new Twig_Function('inscription_status', array($this, 'statusInscription')),
            new Twig_Function('rationalizeFilename', array($this, 'rationalizeFilename')),
        );
    }

    public function readFile(string $filename)
    {
        return file_get_contents('./' . $filename);
    }

    public function fileExists(string $filename)
    {
        return file_exists($filename);
    }

    public function checkDroits(string $droit)
    {
        return true;
    }

    /**
     * Retourne le status d'inscription de l'utilisateur en cours
     * Les valeurs retournées par cette fonction
     *
     * NOT_STARTED 0
     * INITIALIZED 1
     * INCOMPLETE  2
     * COMPLETE    3
     * VALIDATED   4
     *
     * @return bool
     */
    public function statusInscription(AdherentRepository $adh)
    {
        dump($adh->findAll());
        return true;
    }

    public function rationalizeFilename($file)
    {
        // Convert spaces, caracteres accentués  to '_'

        $retfile = str_replace(' ','_',$file);
        //$retfile = str_replace('-','_',$retfile);

        return $retfile;
    }
}
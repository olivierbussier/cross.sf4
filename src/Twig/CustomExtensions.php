<?php

namespace App\Twig;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Twig\Extension\AbstractExtension;
use Twig_Function;

class CustomExtensions extends AbstractExtension
{
    /**
     * @return array|Twig_Function[]
     */
    public function getFunctions()
    {
        return array(
            new Twig_Function('getPubs'            , array($this, 'getPubs')),
            new Twig_Function('readfile'           , array($this, 'readFile')),
            new Twig_Function('fileExists'         , array($this, 'fileExists'))
        );
    }

    /**
     * @param $rep
     * @return array
     */
    public function getPubs($rep)
    {

        $pattern = ".$rep/*.{"
            . "[jJ][pP][gG],"
            . "[jJ][pP][eE][gG],"
            . "[pP][nN][gG],"
            . "[bB][mM][pP],"
            . "[gG][iI][fF]"
            . "}";

        $res = glob($pattern, GLOB_BRACE);
        $tabFiles = [];
        foreach ($res as $v) {
            $tabFiles[] = $rep . '/' . pathinfo($v, PATHINFO_BASENAME);
        }
        return $tabFiles;
    }

    /**
     * @param string $filename
     * @return bool|string
     */
    public function readFile(string $filename)
    {
        return file_get_contents('./' . $filename);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function fileExists(string $filename)
    {
        return file_exists($filename);
    }
}
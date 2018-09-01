<?php

namespace App\Classes\Galerie;

class GalConfig
{

    public static function getVidFiles($rep)
    {
        $R = "$rep/*.{"
            . "[aA][vV][iI],"
            . "[mM][pP][gG],"
            . "[mM][pP][eE][gG],"
            . "[mM][kK][vV],"
            . "[wW][mM][vV],"
            . "[mM][oO][vV],"
            . "[mM]4[vV],"
            . "[fF][lL][vV],"
            . "[mM][pP]4"
            . "}";

        $res = glob($R, GLOB_BRACE);
        //sort($res);
        return $res;
    }

    public static function getImgFiles($rep)
    {
        $R = "$rep/*.{"
            . "[jJ][pP][gG],"
            . "[jJ][pP][eE][gG],"
            . "[pP][nN][gG],"
            . "[bB][mM][pP],"
            . "[mM][pP]4,"
            . "[lL][nN][kK]"
            . "}";

        $res = glob($R, GLOB_BRACE);
        $dst = [];

        foreach ($res as $v) {
            $dst[] = self::getBaseName($v);
        }
        //sort($dst);
        return $dst;
    }

    public static function getDir($rep)
    {
        $v = "$rep/*@*";

        $res = glob($v, GLOB_BRACE);
        $dst = [];

        foreach ($res as $v) {
            $dst[] = self::getBaseName($v);
        }
        rsort($dst);
        return $dst;
    }

    public static function extractFilename($item)
    {
        return substr(strrchr($item, '/'), 1);
    }

    public static function convertTitle($name)
    {
        // Suppression des caracteres de début jusqu'au -
        // et conversion en UTF8

        return substr(strrchr($name, '@'), 1);
        //return utf8_encode(substr(strrchr($name, '@'), 1));
    }

    public static function getDirName($file)
    {
        return pathinfo($file, PATHINFO_DIRNAME);
    }

    public static function getBaseName($file)
    {
        return pathinfo($file, PATHINFO_BASENAME);
    }

    public static function getFileName($file)
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    public static function getExt($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    public static function setExt($file, $ext)
    {
        return pathinfo($file, PATHINFO_FILENAME) . '.' . $ext;
    }

    public static function displayInfoText($path)
    {
        global $src;

        $str = '';
        $id = @file($path.'/'.'info.txt');
        if ($id) {
            foreach ($id as $k) {
                $str .= $k.' ';
            }
            return $str;
        } else {
            return '';
        }
    }

}

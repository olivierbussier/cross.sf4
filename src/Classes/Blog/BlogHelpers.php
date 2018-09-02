<?php

namespace App\Classes\Blog;

class BlogHelpers {

    public const AUCUNE  = 0;
    public const DESSUS  = 1;
    public const DESSOUS = 2;
    public const GAUCHE  = 3;
    public const DROITE  = 4;

    public const POSITIONS = [
        self::AUCUNE  => '',
        self::DESSUS  => 'dessus',
        self::DESSOUS => 'dessous',
        self::GAUCHE  => 'gauche',
        self::DROITE  => 'droite',
    ];

    /**
     * Redimentionne la photo "$filename" qui vient d'être uploadée
     * pour la ref de blog "RefBlog".
     * Vérifie si une photo existe déjà, la supprime au besoin
     * Crée deux images : une vignette et la photo originale
     *
     * @param $filename
     * @param int $largeur
     * @return bool|string
     */
    public static function StorePhoto($filename, $repCible, $largeur)
    {

        // Lecture de l'image dans son dossier d'origine
        // En fonction de son type
        // ---------------------------------------------

        $sz = getimagesize($filename);

        switch ($sz['mime']) {
            case "image/jpeg":
                $imarray=@imagecreatefromjpeg($filename);
                break;
            case "image/gif":
                $imarray=@imagecreatefromgif($filename);
                break;
            case "image/png":
                $imarray=@imagecreatefrompng($filename);
                break;
            default:
                return false; // Type image non supporté
                break;
        }

        if ($imarray == false) {
            return false;  // Erreur de lecture du fichier
        }

        // Mise aux bonnes dimensions
        // --------------------------
        $OriW = $sz[0];
        $OriH = $sz[1];

        // la largeur souhaitée est donnée en paramètre
        // On caclule le ration pour établir les dimensions
        // en conséquence

        $ratio = $largeur / $OriW;
        $NewW  = $OriW * $ratio;
        $NewH  = $OriH * $ratio;

        // Création du bitmap de l'image

        $NewIm = imagecreatetruecolor($NewW, $NewH);

        // Copie resamplée de l'image source vers la nouvelle image

        if (($NewIm == false) ||
            (imagecopyresampled(
                    $NewIm,
                    $imarray,
                    0,
                    0,         // X,Y cible
                    0,
                    0,         // X,Y source
                    $NewW,
                    $NewH, // W,H cible
                    $OriW,
                    $OriH  // W,H source
                ) == false)) {
            return false;
        }

        // Construction du nom de fichier cible à base de la date et heure

        $dt         = date("Ymd-Hms-");
        $filetarget = $dt."blog.jpg";

        // Ectiture du résultat dans le fichier cible

        if (imagejpeg($NewIm, "$repCible/".$filetarget)==false) {
            return false;
        }

        return $filetarget;
    }
}
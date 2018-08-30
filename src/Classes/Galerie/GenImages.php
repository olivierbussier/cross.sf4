<?php

namespace App\Classes\Galerie;

class GenImages
{

    private $exif_orient = [
        1 => [0, 'top', 'left side'],
        2 => [999, 'top', 'right side'],
        3 => [180, 'bottom', 'right side'],
        4 => [999, 'bottom', 'left side'],
        5 => [999, 'left side', 'top'],
        6 => [270, 'right side', 'top'],
        7 => [999, 'right side', 'bottom'],
        8 => [90, 'left side', 'bottom']
    ];

    const GALOK = 1;
    const GALNO = 2;
    const GALKO = 3;

    private $conf = [];

    public function __construct($config)
    {
        $this->conf = $config;
    }

    /**************************************************************************/
    function TestExif($name)
        /**************************************************************************/
    {
        $exif = @exif_read_data($name, 'IFD0');

        return ($exif == false) ? false : true;
    }

    private function log($status, $msg)
    {

    }

    /**************************************************************************/
    function ExifOrientation($name)
        /**************************************************************************/
    {
        if ($this->TestExif($name)) {
            $exif = @exif_read_data($name, 0, true);
            $o = @$exif['IFD0']['Orientation'];
            if ($o == '')
                return false;
            $this->log(self::GALOK, "exif : $o");
            return $this->exif_orient[$o][0];
        } else
            return false;
    }

    /**************************************************************************/
    function CreateDir($dir, $imgrep)
        /**************************************************************************/
    {
        // Si le répèrtoire de thumbnails n'existe pas (1er accès)
        // Il est crée
        $target = $this->conf['galerie.path_images'] . '/' . $dir . '/' . $imgrep; //"images/$dir/$imgrep";
        //v('t=',$target);

        if (!file_exists($target)) {
            mkdir($target);
            $this->log(self::GALOK, "Directory '$target' crée");
        }
    }

    /**************************************************************************/
    function GenImage($type, $imgrep, $basename)
        /**************************************************************************/
    {
        // Recherche du fichier source

        $pathSource = $this->conf['galerie.path_img'];
        $fileName = pathinfo($basename, PATHINFO_FILENAME);
        $target = urldecode($pathSource .'/' . $imgrep . '/' . $fileName);

        $youtube = false;

        if (file_exists("$target.jpg")) {
            $ficsourc = "$target.jpg";
        } else if (file_exists("$target.png")) {
            $ficsourc = "$target.png";
        } else if (file_exists("$target.bmp")) {
            $ficsourc = "$target.bmp";
        } else if (file_exists("$target.lnk")) {
            // Si c'est une video youtube, on va chercher une image
            // sur le site a partir de l'id récupéré dans le fichier LNK
            $th = file("$target.lnk");
            // Recherche de l'image sur youtube, on remplace le 'ficsourc'
            // par ce nouveau nom
            $ficsourc = "http://img.youtube.com/vi/" . trim($th[0]) . "/0.jpg";
            // ficthumb pointe sur le nom de thumbnail a créer
            // On tente d'ouvrir le fichier
            $youtube = true;
        } else if (file_exists("$target.mp4")) {
            $this->log(self::GALNO, "mp4 sans gif animé : '$target'");
            // FFMPEG REQUIS
            echo "run ffmpeg";
            return false;
        } else {
            $this->log(self::GALKO, "Pas de fichier source '$type,$imgrep,$basename'");
            // Rien trouvé
            return false;
        }

        $res = getimagesize($ficsourc);

        $width = $res[0];
        $height = $res[1];
        $mime = $res['mime'];

        // Ajustement dimensions du thumb, en gardant le même rapport
        // hauteur x largeur. La hauteur cible est définie dans le
        // fichier inc_galerie.php

        $angle = $this->ExifOrientation($ficsourc);
        // 90 = 270
        // 270 = 90

        switch ($angle) {
            case false:
            case 0:
            case 180:
            case 999:
                // R.A.F
                break;
            case 90:
            case 270:
                // Echange width et height
                $this->log(self::GALNO, "rotate angle ($angle)");
                $tmp = $width;
                $width = $height;
                $height = $tmp;
                break;
        }

        $imgRatio = $width / $height;

        switch ($type) {
            case 'thumb':
                $imgMH = $this->conf['galerie.thumbHeight'];
                $imgMW = $this->conf['galerie.thumbWidth'];
                $quality = $this->conf['galerie.thumbQuality'];;
                break;
            case 'sized':
                $imgMH = $this->conf['galerie.sizedHeight'];
                $imgMW = $this->conf['galerie.sizedWidth'];
                $quality = $this->conf['galerie.sizedQuality'];;
                break;
        }
        if ($imgMW / $imgRatio <= $imgMH) {
            $newWidth = $imgMW;
            $newHeight = $imgMW / $imgRatio;
        } else {
            $newHeight = $imgMH;
            $newWidth = $imgMH * $imgRatio;
        }

        // On crée l'image cible en fonction du type d'image source

        switch ($mime) {
            case 'image/bmp':
                $imsrc = imagecreatefrombmp($ficsourc);
                break;
            case 'image/jpeg':
                $imsrc = imagecreatefromjpeg($ficsourc);
                break;
            case 'image/png':
                $imsrc = imagecreatefrompng($ficsourc);
                break;
        }

        if ($angle != 0 && $angle != 999) {
            // H et W inversées en attendant le rotate
            $imCible = imagescale($imsrc, $newHeight, $newWidth);
            imagedestroy($imsrc);
            $imCible = imagerotate($imCible, $angle, 0);
        } else {
            $imCible = imagescale($imsrc, $newWidth, $newHeight);
            imagedestroy($imsrc);

        }

        if ($youtube) {

            $yt = imagecreatefrompng('im/yt.png');
            // Incrustation icone youtube
            imagecopy($imCible,
                $yt,
                $newWidth / 2 - 40, $newHeight / 2 - 40,
                0, 0, 80, 80);
            imagedestroy($yt);
        }
        // Sauvegarde de l'image créée dans le répèrtoire
        // thumb correspondant a l'image source
        // Sauvegarde du fichier créé


        $fl = $this->conf['galerie.path_images'] . '/' . $type . '/' . $imgrep . '/' . $fileName . '.jpg';
        imagejpeg($imCible, $fl, $quality);

        $this->log(self::GALOK, "Image '$fl' créée");

        http_response_code(200);
        header('Content-Type: image/jpeg');
        // Affichage de l'image
        imagejpeg($imCible);
        imagedestroy($imCible);

        return true;
    }

    public function compute($type,$repertoire,$image)
    {
        switch ($type) {
            case 'thumb':
                // Générer un thumbnail dans thumb/imgrep
                $this->CreateDir('thumb', $repertoire);
                $this->GenImage('thumb', $repertoire, $image);
                break;
            case 'sized':
                // Générer une image réduite dans sized/imgrep
                $this->CreateDir('sized', $repertoire);
                $this->GenImage('sized', $repertoire, $image);
                break;
        }
    }
}
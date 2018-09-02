<?php

namespace App\Controller;


use App\Classes\Galerie\GalConfig;
use App\Classes\Galerie\GenImages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GalerieController extends AbstractController
{

    /**
     * Cette route est parsée si un fichier thumnail n'est pas trouvé
     *
     * @Route("/galerie/thumb/{repertoire}/{image}", name="display_thumb")
     * @param $repertoire
     * @param $image
     */
    public function thumbNotFoundException($repertoire='' , $image='')
    {
        if ($repertoire == '' || $image == '') {
            $this->redirectToRoute('galeries');
        }
        // Si on arrive la, c'est que le thumbnail n'existe pas, il faut le fabriquer
        // On récupère le fichier a générer

        $conf = $this->getParameter('conf');
        $gal = new GenImages($conf);

        $gal->compute('thumb',$repertoire,$image);
    }

    /**
     * Affichage d'une image
     *
     * @param string $repertoire
     * @param string $image
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/galerie/display/{repertoire}/{image}", name="display_img")
     */
    public function display_image($repertoire = '', $image = '')
    {
        if ($image == '' || $repertoire == '') {
            $this->redirectToRoute('galeries');
        }

        $conf = $this->getParameter('conf');
        $base = $this->getParameter('kernel.project_dir') . '/public';
        $path = $conf['galerie.path_img'];

        $files = GalConfig::getImgFiles($base . '/' . $path . '/' . $repertoire);
        $idx = array_search($image, $files);

        if ($idx > 0) {
            $prev = pathinfo($files[$idx-1], PATHINFO_BASENAME);
        } else {
            $prev = '';
        }
        if ($idx < count($files)-1) {
            $next = pathinfo($files[$idx+1], PATHINFO_BASENAME);
        } else {
            $next = '';
        }

        $ext = GalConfig::getExt($image);
        switch ($ext) {
            case 'mp4': $type = 'video';
                $fname = $image;
                break;
            case 'jpg':
            case 'png': $type = 'image';
                $fname = $image;
                break;
            case 'lnk': $type = 'youtube';
                $r = file($base . '/' . $path . '/' . $repertoire . '/' .$image);
                $fname = $r[0];
                break;
        }

        return $this->render('galerie/displayImg.html.twig',[
            'baseImages' => $conf['galerie.path_img'],
            'repertoire' => $repertoire,
            'prevFile'   => $prev,
            'filename'   => $fname,
            'nextFile'   => $next,
            'type'       => $type
        ]);
    }

    /**
     * Affichage d'une galerie séléctionnée
     * Si {slug} est vide alors redirection vers la route d'index des galeries
     *
     * @param Request $request
     * @param string $repertoire
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/galerie/{repertoire}/{page}", name="galerie")
     */
    public function galerie(Request $request, $repertoire = '', $page = 1)
    {
        if ($repertoire == '') {
            $this->redirectToRoute('galeries');
        }

        $conf = $this->getParameter('conf');
        $base = $this->getParameter('kernel.project_dir') . '/public';
        $path = $conf['galerie.path_img'];
        $nbParPage = $conf['galerie.nb_par_page'];

        $FirstFile = ($page-1) * $nbParPage;
        $LastFile  = ($page  ) * $nbParPage;

        // Récupération de la liste des fichiers images du répèrtoire

        $files = GalConfig::getImgFiles($base . '/' . $path . '/' . $repertoire);
        $nbFiles = count($files);

        if ($nbFiles > $conf['galerie.nb_par_page']) {
            $nbPages = ceil($nbFiles / $conf['galerie.nb_par_page']);

            // Si le nombre de vignettes  afficher est > au nombre de vignettes par page
            // Alors on affiche une zone d'index de pagination


            // Affichage des vignettes correspondant a la page en cours

            // Construction de l'url part pour sized
        } else {
            $nbPages = 1;
        }

        $extrfiles = array_slice($files, $FirstFile, $nbParPage);

        $gal_array = [];

        $rep_thumb = $conf['galerie.path_thumb'];
        $rep_sized = $conf['galerie.path_sized'];

        foreach ($extrfiles as $k => $fil) {
            $gal = [];
            $gal['name'] = $fil;
            switch (GalConfig::getExt($fil)) {
                case 'mp4':
                    $finThumb = GalConfig::setExt($fil, 'gif');
                    break;
                case 'png':
                    $finThumb = GalConfig::setExt($fil, 'jpg');
                    break;
                default:
                    $finThumb = $fil;
                    break;
            }

            $gal['thumb'] = $rep_thumb . '/' . $repertoire . '/' . $finThumb;
            $gal['sized'] = $rep_sized . '/' . $repertoire . '/' . $fil;
            $gal_array[] = $gal;
        }

        return $this->render('galerie/oneGalerie.html.twig', [
            'baseImagesThumb' => $rep_thumb,
            'baseImagesSized' => $rep_sized,
            'repertoire' => $repertoire,
            'page' => $page,
            'nbPages' => $nbPages,
            'files' => $gal_array
        ]);
    }

    /**
     * Index de toutes les galeries disponibles
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/galerie/", name="galeries")
     */
    public function galeries(Request $request)
    {
        // Lister les galeries

        $conf = $this->getParameter('conf');
        $base = $this->getParameter('kernel.project_dir') . '/public';
        $path = $conf['galerie.path_img'];
        $dirs = GalConfig::GetDir($base . '/' .$path);

        // Construction des galeries a afficher

        $gal_array = [];

        foreach ($dirs as $v) {
            $gal = [];
            $gal['protected'] = false;
            $gal['path'] = $v;
            $gal['title'] = GalConfig::convertTitle($v);
            $files = GalConfig::getImgFiles($path . '/' . $v);
            $rndfile = $files[array_rand($files)];
            if (GalConfig::getExt($rndfile) == 'mp4') {
                $rndfile = GalConfig::setExt($rndfile,'gif');
            }
            $gal['rndfile'] = $rndfile;
            $gal['random']  = rand(0, 99999999);
            $gal['text']    = GalConfig::displayInfoText($path . '/' . $v);
            $gal_array[] = $gal;
        }
        return $this->render('galerie/allGaleries.html.twig', [
            'baseImagesThumb'   => $conf['galerie.path_thumb'],
            'baseImagesSized'   => $conf['galerie.path_sized'],
            'dirs'              => $gal_array
        ]);
    }
    /**
     * Index de toutes les galeries disponibles
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/intranet/admin_galeries/", name="admin_galeries")
     */
    public function admin_galeries(Request $request)
    {
        // Lister les galeries

        $conf = $this->getParameter('conf');
        $base = $this->getParameter('kernel.project_dir') . '/public';
        $path = $conf['galerie.path_img'];
        $dirs = GalConfig::GetDir($base . '/' .$path);

        // Construction des galeries a afficher

        $gal_array = [];

        foreach ($dirs as $v) {
            $gal = [];
            $gal['path'] = $v;
            $gal['title'] = GalConfig::convertTitle($v);
            $files = GalConfig::getImgFiles($path . '/' . $v);
            $file = [];
            foreach ($files as $v) {
                $file[] = ['name' => $v, 'rand' => rand(0, 99999999)];

            }
            $gal['files'] = $file;
            $gal_array[] = $gal;
        }
        return $this->render('intranet/admin_galeries.html.twig', [
            'baseImagesThumb'   => $conf['galerie.path_thumb'],
            'baseImagesSized'   => $conf['galerie.path_sized'],
            'dirs'              => $gal_array
        ]);
    }
}

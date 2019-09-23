<?php

namespace App\Controller;

use App\Entity\CrossConfig;
use App\Entity\Resultat;
use App\Form\CrossConfigEditType;
use App\Form\SaisieResultatsType;
use App\Repository\CrossConfigRepository;
use App\Repository\ResultatRepository;
use DateTime;
use mysql_xdevapi\Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IntranetController extends AbstractController
{
    /**
     * Utilisé pour convertir les formats les plus courants sous la forme hh:mm:ss
     *
     * @param $field
     * @param $val
     * @return bool
     */
    private function convertTemps($val)
    {
        $val = trim($val);

        if ($val == "" || $val == '0' || $val == '0/0/0')
            $val="00:00:00";

        if (!($res = DateTime::createFromFormat('G i s', $val)) &&
            !($res = DateTime::createFromFormat('H i s', $val)) &&
            !($res = DateTime::createFromFormat('H\/i\/s', $val)) &&
            !($res = DateTime::createFromFormat('G\/i\/s', $val)) &&
            !($res = DateTime::createFromFormat('G:i:s', $val)) &&
            !($res = DateTime::createFromFormat('H:i:s', $val)) &&
            !($res = DateTime::createFromFormat('H\ \h\ i\ \m\n\ s\ \s', $val)) &&
            !($res = DateTime::createFromFormat('G\ \h\ i\ \m\n\ s\ \s', $val))) {
            return 'XX:XX:XX';
        } else {
            return $res->format("H:i:s");
        }
    }

    private function calcVitesse(string $temps, float $distance)
    {
        $t = explode(":",$temps);
        $dt = ($t[0] + $t[1]/60 + $t[2]/3600);
        $vitesse = $distance / $dt;
        return sprintf("%02.2fKm/h",$vitesse);
    }

    /**
     * @Route("/intranet", name="intranet")
     */
    public function index()
    {
        return $this->render('intranet/index.html.twig');
    }

    /**
     * @Route("/intranet/admin_config", name="intranet_config")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function crossConfig(RegistryInterface $doctrine, Request $request)
    {
        /** @var CrossConfigRepository $configRepo */
        $configRepo = $doctrine->getRepository(CrossConfig::class);
        $em = $doctrine->getManager();

        $date = $configRepo->getConfig();

        $form = $this->createForm(CrossConfigEditType::class, $date);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $cfg = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            $em->persist($cfg);
            $em->flush();

            //return $this->redirectToRoute('task_success');
        }

        return $this->render('intranet/admin_config.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/intranet/admin_resultats", name="intranet_admin_resultats")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_resultats(RegistryInterface $doctrine,Request $request)
    {
        $message = '';

        $courses = $doctrine->getRepository(Resultat::class)->getAllCourses();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(SaisieResultatsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();
            /**
             * @var UploadedFile $file
             */
            $file = $form['Fichier']->getData();

            $anneeImport = $result->getAnneeCross();

            try {
                $fd = $file->move('/temp', "file$anneeImport.csv");

                if($lignes = file($fd->getPathname())) {
                    /**
                     * @var $resRepo ResultatRepository
                     */
                    $resRepo = $doctrine->getRepository(Resultat::class);
                    $resRepo->deleteAnnee($anneeImport);
                }


                foreach($lignes as $ligne) {

                    $value = explode(";", $ligne);

                    $annee     = intval($value[0]);
                    if ($annee == 0 ||
                       ($anneeImport != 0 && $annee != $anneeImport))
                        continue;

                    $result = new Resultat();

                    $temps = $this->convertTemps(trim($value[4]));
                    $ecart = $this->convertTemps(trim($value[5]));
                    $course = $value[1];

                    switch($course) {
                        case 'Cross 3Km':
                            $dist = 3;
                            break;
                        case 'Cross 10Km':
                            $dist = 10;
                            break;
                        case 'Trail 14.5Km':
                            $dist = 15;
                            break;
                        default:
                            throw new Exception("Course inconnue");
                            break;
                    }
                    $vitesse = $this->calcVitesse($temps, $dist);

                    $result->setAnneeCross  ($annee    )
                           ->setCourse      (trim($value[1]))
                           ->setClassement  (trim($value[2]))
                           ->setDossard     (trim($value[3]))
                           ->setTemps       ($temps)
                           ->setEcart       ($ecart)
                           ->setVitesse     ($vitesse)
                           ->setNom         (trim($value[6]))
                           ->setPrenom      (trim($value[7]))
                           ->setCategorie   (trim($value[8]))
                           ->setSexe        (trim($value[9]))
                           ->setVille       (trim($value[10]));

                    $em->persist($result);

                }
                $em->flush();

                $message = 'Données transférées';
                return $this->render('intranet/admin_resultats_result.html.twig',[
                    'message' => $message
                ]);
            } catch (FileException $e) {
                dump($e);
            }
        }

        return $this->render('intranet/admin_resultats.html.twig',[
            'courses' => $courses,
            'formResult' => $form->createView(),
            'message' => $message
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Resultat;
use App\Form\SaisieResultatsType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IntranetController extends AbstractController
{
    /**
     * @Route("/intranet", name="intranet")
     */
    public function index()
    {
        return $this->render('intranet/index.html.twig');
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
            $file = $form['Fichier']->getData();
            $annee = $result->getAnneeCross();
            try {
                $fd = $file->move('/temp', "file$annee.csv");

                if(!$lignes = file($fd->getPathname())) {
                    dump("Une erreur c'est produite lors de l'ouverture du fichier.");
                } else {
                    $doctrine->getRepository(Resultat::class)->deleteAnnee($annee);
                }


                foreach($lignes as $ligne) {

                    $value = explode(";", $ligne);

                    $annee     = intval($value[0]);
                    if ($annee == 0)
                        continue;

                    $result = new Resultat();

                    $result->setAnneeCross  ($annee    )
                           ->setCourse      (trim($value[1]))
                           ->setClassement  (trim($value[2]))
                           ->setDossard     (trim($value[3]))
                           ->setTemps       (trim($value[4]))
                           ->setEcart       (trim($value[5]))
                           ->setVitesse     (trim($value[6]))
                           ->setNom         (trim($value[7]))
                           ->setPrenom      (trim($value[8]))
                           ->setCategorie   (trim($value[9]))
                           ->setSexe        (trim($value[10]))
                           ->setVille       (trim($value[11]));

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
/*


        if(!empty($_POST['annee_supp'])) {

        $annee_cross = $_POST['annee_supp'];
        $requete = db_query("select count(*) as nb from ".$db_prefix."classement where annee_cross = '".$annee_cross."'");
        $result = db_nextrow($requete);

        if ($result['nb'] > 0)	{
        db_query("Delete from @#@classement where annee_cross = '".$annee_cross."'");
        $result = db_nextrow($requete);
        if($result['nb'] == 0) {
        echo "La suppression des résultats a été réussie avec succès.<br>Rappel année résultat choisie : ".$annee_cross."<br><br>";
        } else {
        echo "La suppression des résultats a échoué, veuillez réessayer<br><br>";
        }
        } else {
        echo "Il n'existe aucune données pour cette année..<br>Rappel année résultat choisie : ".$annee_cross."<br><br>";
        }
        }

        if(!empty($_POST['date_cross']))
        {
        $date_cross = utf8_decode($_POST['date_cross']);
        $requete = db_query("select count(*) as nb from @#@date_cross");
        $result = db_nextrow($requete);
        if($result['nb']>0)	{
        db_query("UPDATE @#@date_cross SET date ='".$date_cross."' Where id = 'date'");
        } else {
        db_query("INSERT INTO @#@date_cross ('date','".$date_cross."'");
        }
        }

        $res = db_query("select * from @#@date_cross");
        $d = db_nextrow($res);
        $dateCross = $d['date'];

        $root_path="../"; include $root_path."header.php";?>

 */
        return $this->render('intranet/admin_resultats.html.twig',[
            'courses' => $courses,
            'formResult' => $form->createView(),
            'message' => $message
        ]);
    }
    /**
     * @Route("/intranet/admin_blog", name="intranet_admin_blog")
     */
    public function admin_blog()
    {
        return $this->render('intranet/admin_blog.html.twig');
    }
}

<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Resultat;
use App\Form\ChoixCourseType;
use App\Form\EcrireType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="root")
     * @param RegistryInterface $doctrine
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(RegistryInterface $doctrine)
    {
        $posts = $doctrine->getRepository(Blog::class)->getallPosts();
        return $this->render(
            'pages/index.html.twig', [
            'posts' => $posts
        ]);
    }


    // Courses

    /**
     * @Route("/trail", name="trail")
     */
    public function trail()
    {
        return $this->render('pages/trail.html.twig');
    }

    /**
     * @Route("/10km", name="10km")
     */
    public function c10km()
    {
        return $this->render('pages/c10km.html.twig');
    }

    /**
     * @Route("/marche", name="marche")
     */
    public function marche()
    {
        return $this->render('pages/marche.html.twig');
    }

    /**
     * @Route("/3km", name="3km")
     */
    public function c3km()
    {
        return $this->render('pages/c3km.html.twig');
    }

    /**
     * @Route("/jeunes", name="jeunes")
     */
    public function jeunes()
    {
        return $this->render('pages/jeunes.html.twig');
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription()
    {
        return $this->render('pages/inscription.html.twig');
    }

    /**
     * @Route("/resultats", name="resultats")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resultats(RegistryInterface $doctrine, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $resultat = new Resultat();

        $resultRepo = $doctrine->getRepository(Resultat::class);

        $courses = [];
        foreach ($resultRepo->getAllCourses() as $k => $v) {
            $courses[$v['course']] = $v['course'];
        }

        $annees = [];
        foreach ($resultRepo->getAnneesCourses() as $k => $v) {
            $annees[$v['anneeCross']] = $v['anneeCross'];
        }

        $formChoix = $this->createFormBuilder()
            ->add('anneeCross',ChoiceType::class, [ 'choices' => $annees  ])
            ->add('course',ChoiceType::class, [ 'choices' => $courses ])
            ->add('Afficher les résultats', SubmitType::class, [
                'label' => 'Afficher les résultats',
                'attr' => [
                    'class' => 'btn-block btn success'
                ]
            ])
            ->getForm();
        ;

        $formChoix->handleRequest($request);

        $resultat  = 0;

        if ($formChoix->isSubmitted() && $formChoix->isValid()) {
            $choixAnnee  = $formChoix['anneeCross']->getData();
            $choixCourse = $formChoix['course']->getData();

            $resultat = $resultRepo->getCourse($choixAnnee,$choixCourse);

        }

        return $this->render('pages/resultats.html.twig', [
            'formChoix' => $formChoix->createView(),
            'courses' => $courses,
            'annees' => $annees,
            'resultats' => $resultat
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('pages/contact.html.twig');
    }

    /**
     * @Route("/liens", name="liens")
     */
    public function liens()
    {
        return $this->render('pages/liens.html.twig');
    }

    /**
     * @Route("/whoswho", name="whoswho")
     */
    public function whoswho()
    {
        return $this->render('pages/whoswho.html.twig');
    }

    /**
     * @Route("/ecrire", name="ecrire")
     */
    public function ecrire(Request $request,Swift_Mailer $mailer)
    {
        $form = $this->createForm(EcrireType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            $message = (new Swift_Message('You Got Mail!'))
                ->setFrom($contactFormData['from'])
                ->setTo('contact@cross-biviers.fr')
                ->setBody(
                    $contactFormData['message'],
                    'text/plain'
                    )
            ;

            $res = $mailer->send($message);
            $this->addFlash('info', 'Message envoyé');
            return $this->redirectToRoute('ecrire');
        }

        return $this->render('pages/ecrire.html.twig',[
            'formEcrire' => $form->createView()
        ]);
    }
}

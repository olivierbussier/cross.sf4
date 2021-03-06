<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\CrossConfig;
use App\Entity\Resultat;
use App\Form\ChoixCourseType;
use App\Form\EcrireType;
use App\Repository\CrossConfigRepository;
use Swift_Mailer;
use Swift_Message;
use Swift_TransportException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="root")
     * @param RegistryInterface $doctrine
     * @return Response
     */
    public function index(RegistryInterface $doctrine)
    {
        $posts = $doctrine->getRepository(Blog::class)->getAllPosts();

        $conf = $this->getParameter('conf');
        $dirImages = $conf['blog.images'];

        // affichage de toutes les images du rep pub

        $pubs = glob('public/imp/*.*');

        return $this->render(
            'pages/index.html.twig', [
                'imblog' => $dirImages,
                'posts' => $posts
        ]);
    }
    /**
     * @Route("/preview/{blogId}", name="root_preview")
     * @param RegistryInterface $doctrine
     * @param string $blogId
     * @return Response
     */
    public function indexPreviewBlog(RegistryInterface $doctrine, $blogId = '')
    {
        if ($blogId == '') {
            $this->redirectToRoute('root');
        }

        $post = $doctrine->getRepository(Blog::class)->find($blogId);

        $conf = $this->getParameter('conf');
        $dirImages = $conf['blog.images'];

        return $this->render(
            'pages/index_preview.html.twig', [
            'post' => $post,
            'imblog' => $dirImages
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
     * @param RegistryInterface $doctrine
     * @return Response
     */
    public function inscription(RegistryInterface $doctrine)
    {
        $configRepo = $doctrine->getRepository(CrossConfig::class);

        /** @var CrossConfigRepository $configRepo */
        $config = $configRepo->getConfig();

        return $this->render('pages/inscription.html.twig', [
            'liens' => $config
        ]);
    }

    /**
     * @Route("/resultats", name="resultats")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function ecrire(Request $request,Swift_Mailer $mailer)
    {
        $form = $this->createForm(EcrireType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            $message = (new Swift_Message('Mail de la part du site web \'cross-biviers.fr\''))
                ->setFrom($contactFormData['from'])
                ->setTo('contact@cross-biviers.fr')
                ->setBody(
                    $contactFormData['message'],
                    'text/plain'
                    )
            ;

            try {
                $res = $mailer->send($message, $fail);
                $this->addFlash('info', "$res : Message envoyé");
                return $this->redirectToRoute('ecrire');

            } catch (Swift_TransportException $e) {
                return $this->render('pages/error.html.twig',[
                    'err' => $e
                ]);
            }
        }

        return $this->render('pages/ecrire.html.twig',[
            'formEcrire' => $form->createView()
        ]);
    }
}

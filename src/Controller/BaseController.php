<?php

namespace App\Controller;

use App\Entity\Blog;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="root")
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
     */
    public function resultats()
    {
        return $this->render('pages/resultats.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('pages/resultats.html.twig');
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
    public function ecrire()
    {
        return $this->render('pages/ecrire.html.twig');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function admin_resultats()
    {
        return $this->render('intranet/admin_resultats.html.twig');
    }
    /**
     * @Route("/intranet/admin_blog", name="intranet_admin_blog")
     */
    public function admin_blog()
    {
        return $this->render('intranet/admin_blog.html.twig');
    }
}

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
}

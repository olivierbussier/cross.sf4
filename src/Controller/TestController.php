<?php

namespace App\Controller;

use App\Form\TestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Choice;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $form = $this->createForm(TestType::class);

        return $this->render(
            'pages/test.html.twig', [
                'form' => $form->createView()
        ]);
    }
}

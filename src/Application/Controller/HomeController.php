<?php

namespace App\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('application/home/index.html.twig');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * Display home page
     * 
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        // Renvoie une vue affichant la page d'accueil
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/create", name="create")
     */
    public function create(): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $currentUser = $this->getUser();

        // Récupère la liste des quiz publiés par l'utilisateur connecté
        $quizzes = $currentUser->getPlayer()->getQuizzes();

        // Renvoie une vue affichant la liste des quiz créés par l'utilisateur actuellement connecté
        // en vue de les modifier
        return $this->render('main/create.html.twig', [
            'quizzes' => $quizzes
        ]);
    }
}

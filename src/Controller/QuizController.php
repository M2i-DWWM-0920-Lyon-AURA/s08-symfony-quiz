<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz_list")
     */
    public function list(QuizRepository $repository): Response
    {
        $quizzes = $repository->findAll();

        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
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
        // Récupère tous les quiz
        $quizzes = $repository->findAll();

        // Renvoie une vue affichant une liste des quiz
        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/quiz/{id}", name="quiz_single", requirements={"id"="\d+"})
     */
    public function single(int $id, QuizRepository $repository, QuestionRepository $questionRepository): Response
    {
        // Récupère le quiz concerné
        $quiz = $repository->find($id);

        // Si le quiz n'existe pas, renvoie une erreur 404
        if (is_null($quiz)) {
            throw $this->createNotFoundException('Quiz #' . $id . ' does not exist.');
        }

        // Récupère la première question du quiz, c'est-à-dire...
        $firstQuestion = $questionRepository->findOneBy([
            // ...appartenant au quiz...
            'quiz' => $quiz,
            // ...et avec le rang 1
            '_rank' => 1
        ]);

        // Renvoie une vue affichant le quiz concerné
        return $this->render('quiz/single.html.twig', [
            'quiz' => $quiz,
            'first_question' => $firstQuestion
        ]);
    }
}

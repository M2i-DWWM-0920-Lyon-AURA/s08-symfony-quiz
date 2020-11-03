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
        $quizzes = $repository->findAll();

        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/quiz/{id}", name="quiz_single", requirements={"id"="\d+"})
     */
    public function single(int $id, QuizRepository $repository, QuestionRepository $questionRepository): Response
    {
        $quiz = $repository->find($id);

        if (is_null($quiz)) {
            throw $this->createNotFoundException('Quiz #' . $id . ' does not exist.');
        }

        $firstQuestion = $questionRepository->findOneBy([
            'quiz' => $quiz,
            '_rank' => 1
        ]);

        return $this->render('quiz/single.html.twig', [
            'quiz' => $quiz,
            'first_question' => $firstQuestion
        ]);
    }
}

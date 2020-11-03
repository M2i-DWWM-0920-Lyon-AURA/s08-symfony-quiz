<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Score;
use App\Repository\QuizRepository;
use App\Repository\ScoreRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quiz", name="quiz_")
 */
class QuizController extends AbstractController
{
    protected QuizRepository $repository;

    public function __construct(QuizRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a list of quizzes available to play
     * 
     * @Route("", name="list")
     */
    public function list(): Response
    {
        // Récupère tous les quiz
        $quizzes = $this->repository->findAll();

        // Renvoie une vue affichant une liste des quiz
        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Display details of a single quiz
     * 
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
     */
    public function single(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
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

    /**
     * Display quiz result
     * 
     * @Route("/{id}/result", name="result", requirements={"id"="\d+"})
     */
    public function result(Quiz $quiz, SessionInterface $session, EntityManagerInterface $manager, ScoreRepository $scoreRepository)
    {
        // Récupère l'utilisateur actuellement connecté
        $currentUser = $this->getUser();

        // Récupère le score de l'utilisateur et le remet à zéro
        $score = $session->get('score');
        $session->set('score', 0);

        // Si l'utilisateur est bien connecté
        if (!is_null($currentUser)) {
            // Tente de récupérer le score précédent du joueur au quiz
            $scoreObject = $scoreRepository->findOneBy([
                'quiz' => $quiz,
                'player' => $currentUser->getPlayer()
            ]);
            // Si le joueur n'a pas encore joué à ce quiz
            if (is_null($scoreObject)) {
                // Crée un nouveau score à envoyer en BDD
                $scoreObject = new Score();
                $scoreObject
                    ->setValue($score)
                    ->setQuiz($quiz)
                    ->setPlayer($currentUser->getPlayer())
                ;
            // Sinon
            } else {
                // Met le score précédent à jour
                $scoreObject
                    ->setValue($score)
                ;
            }
            // Envoie le score en BDD
            $manager->persist($scoreObject);
            $manager->flush();

            // Ajoute un message à afficher sur la page
            $this->addFlash(
                'info',
                'Votre score a été enregistré!'
            );
        // Sinon
        } else {
            // Ajoute un message à afficher sur la page
            $this->addFlash(
                'warning',
                'Votre score n\'a pas été enregistré... Connectez-vous pour profiter pleinement de notre super application!'
            );
        }

        // Renvoie une vue affichant le résultat au quiz concerné
        return $this->render('quiz/result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'questionCount' => count($quiz->getQuestions())
        ]);
    }
}

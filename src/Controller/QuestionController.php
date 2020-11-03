<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * Display a single question for the user to answer
     * 
     * @Route("/question/{id}", name="question_single", requirements={"id"="\d+"})
     */
    public function single(int $id, QuestionRepository $repository, SessionInterface $session): Response
    {
        // Récupère la question concernée
        $question = $repository->find($id);

        // Si la question n'existe pas, renvoie une erreur 404
        if (is_null($question)) {
            throw $this->createNotFoundException('Question #' . $id . ' does not exist.');
        }

        // Si on a atteint la première question d'un quiz, initialiser le score à zéro
        if ($question->get_rank() === 1) {
            $session->set('score', 0);
        }

        // Renvoie une vue affichant la question concernée
        return $this->render('question/single.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * Process answer given by user to previous question
     * 
     * @Route("/question/{id}/give-answer", name="question_give-answer", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function giveAnswer(int $id, Request $request, QuestionRepository $repository, SessionInterface $session)
    {
        // Récupère la question concernée
        $question = $repository->find($id);

        // Si la question n'existe pas, renvoie une erreur 404
        if (is_null($question)) {
            throw $this->createNotFoundException('Question #' . $id . ' does not exist.');
        }

        // Utilise l'objet Request pour récupérer le contenu du formulaire envoyé par l'utilisateur
        // Remplace: $userAnswer = (int)$_POST['answer']
        $userAnswer = (int)$request->request->get('answer');

        // Si la réponse donnée par l'utilisateur est juste
        if ($question->getRightAnswer()->getId() === $userAnswer) {
            // TODO: Augmente le score de 1
            $score = $session->get('score');
            $session->set('score', $score + 1);

            // Ajoute un message à afficher sur la prochaine page
            $this->addFlash(
                'success',
                'Bravo! C\'était la bonne réponse!'
            );
        // Sinon
        } else {
            // Ajoute un message à afficher sur la prochaine page
            $this->addFlash(
                'danger',
                'Hé non!'
            );
        }

        // Récupère la question suivante, c'est-à-dire...
        $nextQuestion = $repository->findOneBy([
            // ...appartenant au même quiz...
            'quiz' => $question->getQuiz(),
            // ...et avec le rang suivant
            '_rank' => $question->get_rank() + 1
        ]);

        // Redirige sur la page résultat si la question suivante n'existe pas
        if (is_null($nextQuestion)) {
            return $this->redirectToRoute('quiz_result', ['id' => $question->getQuiz()->getId()]);
        }

        // Redirige sur la page de la question suivante
        return $this->redirectToRoute('question_single', [
            'id' => $nextQuestion->getId()
        ]);
    }
}

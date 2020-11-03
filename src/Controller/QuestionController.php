<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question/{id}", name="question_single", requirements={"id"="\d+"})
     */
    public function single(int $id, QuestionRepository $repository): Response
    {
        // Récupère la question concernée
        $question = $repository->find($id);

        // Si la question n'existe pas, renvoie une erreur 404
        if (is_null($question)) {
            throw $this->createNotFoundException('Question #' . $id . ' does not exist.');
        }

        // Renvoie une vue affichant la question concernée
        return $this->render('question/single.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * @Route("/question/{id}/give-answer", name="question_give-answer", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function giveAnswer(int $id, Request $request, QuestionRepository $repository)
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
            // TODO: Augment le score de 1

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

        // TODO: Redirige sur la page résultat si la question suivante n'existe pas

        // Redirige sur la page de la question suivante
        return $this->redirectToRoute('question_single', [
            'id' => $nextQuestion->getId()
        ]);
    }
}

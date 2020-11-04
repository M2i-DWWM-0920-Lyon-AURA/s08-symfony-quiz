<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/question", name="question_")
 */
class QuestionController extends AbstractController
{
    /**
     * Display a single question for the user to answer
     * 
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
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
     * @Route("/{id}/give-answer", name="give-answer", requirements={"id"="\d+"}, methods={"POST"})
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

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/new/{id}", name="new_form", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function newForm(Quiz $quiz)
    {
        $this->denyAccessUnlessGranted('edit', $quiz);

        // Crée une nouvelle question à injecter dans le formulaire
        $question = new Question();

        // Crée un nouvel objet permettant de paramétrer le formulaire
        $form = $this->createForm(QuestionType::class, $question);

        // Renvoie une nouvelle vue contenant le formulaire
        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/new/{id}", name="new", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function new(Quiz $quiz, Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('edit', $quiz);

        $question = new Question();

        $form = $this->createForm(QuestionType::class, $question);

        // Laisse l'objet gérer la requête
        $form->handleRequest($request);
        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            $question
                ->set_rank( count($quiz->getQuestions()) + 1 )
                ->setQuiz($quiz)
            ;

            $manager->persist($question);
            $manager->flush();
        } else {
            // Renvoie une nouvelle vue contenant le formulaire
            return $this->render('question/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Redirige sur la page "création"
        return $this->redirectToRoute('quiz_update_form', [ 'id' => $quiz->getId() ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/update/{id}", name="update_form", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function updateForm(Question $question)
    {
        $this->denyAccessUnlessGranted('edit', $question->getQuiz());

        // Crée un nouvel objet permettant de paramétrer le formulaire
        $form = $this->createForm(QuestionType::class, $question);

        // Renvoie une nouvelle vue contenant le formulaire
        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/update/{id}", name="update", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function update(Question $question, Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('edit', $question->getQuiz());

        $form = $this->createForm(QuestionType::class, $question);

        // Laisse l'objet gérer la requête
        $form->handleRequest($request);
        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            $manager->persist($question);
            $manager->flush();
        } else {
            // Renvoie une nouvelle vue contenant le formulaire
            return $this->render('question/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Redirige sur la page "création"
        return $this->redirectToRoute('quiz_update_form', [ 'id' => $question->getQuiz()->getId() ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/delete", name="delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Question $question, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('edit', $question->getQuiz());

        $manager->remove($question);
        $manager->flush();

        // Redirige sur la page "création"
        return $this->redirectToRoute('quiz_update_form', [ 'id' => $question->getQuiz()->getId() ]);
    }
}

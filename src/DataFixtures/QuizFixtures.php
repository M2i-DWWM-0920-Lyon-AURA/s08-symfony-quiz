<?php

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class QuizFixtures extends Fixture
{
    private $manager;

    protected function makeQuiz(string $title, string $description): Quiz
    {
        $quiz = new Quiz();
        $quiz
            ->setTitle($title)
            ->setDescription($description)
        ;

        // Marque l'objet comme prêt à être envoyé en BDD
        $this->manager->persist($quiz);

        return $quiz;
    }

    protected function makeQuestion(string $description, int $rank, Quiz $quiz): Question
    {
        $question = new Question();
        $question
            ->setDescription($description)
            ->setRank($rank)
            ->setQuiz($quiz)
        ;

        // Marque l'objet comme prêt à être envoyé en BDD
        $this->manager->persist($question);

        return $question;
    }

    protected function makeAnswer(string $description, int $rank, Question $question): Answer
    {
        $answer = new Answer();
        $answer
            ->setDescription($description)
            ->setRank($rank)
            ->setQuestion($question)
        ;
 
        // Marque l'objet comme prêt à être envoyé en BDD
        $this->manager->persist($answer);

        return $answer;
   }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // Crée les quiz
        $quizzes = [
            $this->makeQuiz('Divers faits étonnants', 'Etonnez-vous avec ces petites choses de la vie quotidienne que vous ignorez probablement!'),
            $this->makeQuiz('The Big Bang Theory', 'Êtes-vous un vrai fan de The Big Bang Theory? Pour le savoir, un seul moyen: répondez à ce quiz ultime sur la série!'),
        ];

        // Crée les questions
        $questions = [
            $this->makeQuestion('Combien de joueurs y a-t-il dans une équipe de football?', 1, $quizzes[0]),
        ];

        // Crée les réponses
        $answers = [
            $this->makeAnswer('5', 1, $questions[0]),
            $this->makeAnswer('7', 2, $questions[0]),
            $this->makeAnswer('11', 3, $questions[0]),
            $this->makeAnswer('235', 4, $questions[0]),
        ];

        // Associe la bonne réponse à chaque question
        $questions[0]->setRightAnswer($answers[2]);

        // Envoie tous les objets marqués comme prêts à être envoyés dans la BDD
        $manager->flush();
    }
}

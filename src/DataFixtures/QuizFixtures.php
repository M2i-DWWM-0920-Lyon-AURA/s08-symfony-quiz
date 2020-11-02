<?php

namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Player;
use App\Entity\Question;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class QuizFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function makeUser(string $email, string $password): User
    {
        $user = new User();
        $user
            ->setEmail($email)
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $password
            ))
        ;

        return $user;
    }

    protected function makePlayer(string $name, User $user): Player
    {
        $player = new Player();
        $player
            ->setName($name)
            ->setUser($user)
        ;

        return $player;
    }

    protected function makeQuiz(string $title, string $description, Player $player): Quiz
    {
        $quiz = new Quiz();
        $quiz
            ->setTitle($title)
            ->setDescription($description)
            ->setAuthor($player)
        ;

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
 
        return $answer;
   }

    public function load(ObjectManager $manager)
    {
        // Crée des nouveaux utilisateurs
        $users = [
            $this->makeUser('jeanpierre@test.com', 'jeanpierre'),
            $this->makeUser('madeleine@test.com', 'madeleine'),
        ];

        // Crée les profils des joueurs
        $players = [
            $this->makePlayer('JP le bg du 69', $users[0]),
            $this->makePlayer('Madodo73', $users[1]),
        ];

        // Crée les quiz
        $quizzes = [
            $this->makeQuiz('Divers faits étonnants', 'Etonnez-vous avec ces petites choses de la vie quotidienne que vous ignorez probablement!', $players[0]),
            $this->makeQuiz('The Big Bang Theory', 'Êtes-vous un vrai fan de The Big Bang Theory? Pour le savoir, un seul moyen: répondez à ce quiz ultime sur la série!', $players[1]),
        ];

        // Crée les questions
        $questions = [
            $this->makeQuestion('Combien de joueurs y a-t-il dans une équipe de football?', 1, $quizzes[0]),
            $this->makeQuestion('Combien de temps la lumière du soleil met-elle pour nous parvenir?', 2, $quizzes[0]),
            $this->makeQuestion('En 1582, le pape Grégoire XIII a décidé de réformer le calendrier instauré par Jules César. Mais quel était le premier mois du calendrier julien?', 3, $quizzes[0]),
            $this->makeQuestion('Lequel de ces signes du zodiaque n\'est pas un signe d\'Eau?', 4, $quizzes[0]),
            $this->makeQuestion('Combien de doigts ai-je dans mon dos?', 5, $quizzes[0]),
            $this->makeQuestion('Quel langage fictif Howard parle-t-il?', 1, $quizzes[1]),
            $this->makeQuestion('Quel est le seul acteur de la série qui possède un doctorat dans la vraie vie?', 2, $quizzes[1]),
            $this->makeQuestion('Dans quel appartement Penny et Leonard vivent-ils?', 3, $quizzes[1]),
            $this->makeQuestion('Combien de fois Sheldon doit-il frapper à une porte et dire le nom d\'une personne avant d\'entrer?', 4, $quizzes[1]),
            $this->makeQuestion('Quel groupe de rock alternatif canadien a créé le générique musical de The Big Bang Theory?', 5, $quizzes[1]),
        ];

        // Crée les réponses
        $answers = [
            $this->makeAnswer('5', 1, $questions[0]),
            $this->makeAnswer('7', 2, $questions[0]),
            $this->makeAnswer('11', 3, $questions[0]),
            $this->makeAnswer('235', 4, $questions[0]),
            $this->makeAnswer('15 secondes', 1, $questions[1]),
            $this->makeAnswer('8 minutes', 2, $questions[1]),
            $this->makeAnswer('2 heures', 3, $questions[1]),
            $this->makeAnswer('3 mois', 4, $questions[1]),
            $this->makeAnswer('Janvier', 1, $questions[2]),
            $this->makeAnswer('Février', 2, $questions[2]),
            $this->makeAnswer('Mars', 3, $questions[2]),
            $this->makeAnswer('Avril', 4, $questions[2]),
            $this->makeAnswer('Le Verseau', 1, $questions[3]),
            $this->makeAnswer('Le Cancer', 2, $questions[3]),
            $this->makeAnswer('Le Scorpion', 3, $questions[3]),
            $this->makeAnswer('Les Poissons', 4, $questions[3]),
            $this->makeAnswer('2', 1, $questions[4]),
            $this->makeAnswer('3', 2, $questions[4]),
            $this->makeAnswer('4', 3, $questions[4]),
            $this->makeAnswer('5, comme tout le monde', 4, $questions[4]),
            $this->makeAnswer('L\'eflque', 1, $questions[5]),
            $this->makeAnswer('Le Valyrien', 2, $questions[5]),
            $this->makeAnswer('Le Klingon', 3, $questions[5]),
            $this->makeAnswer('Le Serpentard', 4, $questions[5]),
            $this->makeAnswer('Kaley Cuoco', 1, $questions[6]),
            $this->makeAnswer('Mayim Bialik', 2, $questions[6]),
            $this->makeAnswer('Johnny Galecki', 3, $questions[6]),
            $this->makeAnswer('Jim Parsons', 4, $questions[6]),
            $this->makeAnswer('3A', 1, $questions[7]),
            $this->makeAnswer('3B', 2, $questions[7]),
            $this->makeAnswer('4A', 3, $questions[7]),
            $this->makeAnswer('4B', 4, $questions[7]),
            $this->makeAnswer('Une', 1, $questions[8]),
            $this->makeAnswer('Deux', 2, $questions[8]),
            $this->makeAnswer('Trois', 3, $questions[8]),
            $this->makeAnswer('Quatre', 4, $questions[8]),
            $this->makeAnswer('Barenaked Ladies', 1, $questions[9]),
            $this->makeAnswer('Static in Stereo', 2, $questions[9]),
            $this->makeAnswer('Brundlefly', 3, $questions[9]),
        ];

        // Marque tous les objets créés comme prêts à être envoyés en base de données
        foreach (\array_merge($users, $players, $quizzes, $questions, $answers) as $object) {
            $manager->persist($object);
        }

        // Associe la bonne réponse à chaque question
        $questions[0]->setRightAnswer($answers[2]);
        $questions[1]->setRightAnswer($answers[5]);
        $questions[2]->setRightAnswer($answers[10]);
        $questions[3]->setRightAnswer($answers[12]);
        $questions[4]->setRightAnswer($answers[19]);
        $questions[5]->setRightAnswer($answers[22]);
        $questions[6]->setRightAnswer($answers[25]);
        $questions[7]->setRightAnswer($answers[30]);
        $questions[8]->setRightAnswer($answers[34]);
        $questions[9]->setRightAnswer($answers[36]);
        
        // Envoie tous les objets marqués comme prêts à être envoyés dans la BDD
        $manager->flush();
    }
}

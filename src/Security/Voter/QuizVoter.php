<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuizVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit', 'view'])
            && $subject instanceof \App\Entity\Quiz;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $token->getUser();
        // Si l'utilisateur n'est pas authentifié, renvoie faux
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Définit la logique personnalisée permettant à Symfony de déterminer
        // si un utilisateur a accès à la ressource demandée
        switch ($attribute) {
            // Dans le cas d'une modification de la ressource
            case 'edit':
                // Pour un quiz donné, autorise uniquement son auteur
                return $subject->getAuthor() == $user->getPlayer();
            // Dans le cas d'un accès à la ressouce sans modification
            case 'view':
                // Tout le monde est autorisé
                return true;
        }

        // Si une modalité d'accès non prévue (autre que lecture ou modification),
        // refuse l'accès
        return false;
    }
}

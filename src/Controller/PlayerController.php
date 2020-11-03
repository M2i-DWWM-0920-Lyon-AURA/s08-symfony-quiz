<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayerController extends AbstractController
{
    /**
     * @Route("/player/{id}", name="player_single", requirements={"id"="\d+"})
     */
    public function single(int $id, PlayerRepository $repository): Response
    {
        $player = $repository->find($id);

        return $this->render('player/single.html.twig', [
            'player' => $player,
        ]);
    }
}

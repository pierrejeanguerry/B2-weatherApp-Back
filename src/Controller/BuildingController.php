<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class BuildingController extends AbstractController
{
    #[Route('/api/building/list', name: 'building_list', methods: ['POST'])]
    public function index(#[CurrentUser()] User $user, Request $request): Response
    {
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $buildings = $user->getBuildings();
        return $this->json([
            'message' => 'ok',
            'list_building' => $buildings,
            ], Response::HTTP_OK);
            
        
        // Récupérer les bâtiments de l'utilisateur connecté
        // $buildings = $user->getBuilding(); // Supposons que la méthode getBuildings() récupère les bâtiments de l'utilisateur

        // Si vous souhaitez renvoyer une réponse JSON avec les bâtiments de l'utilisateur
        
    }
    
}

<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\User;
use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RoomController extends AbstractController
{
    #[Route('/api/room/list', name: 'room_list', methods: ['POST'])]
    public function index(#[CurrentUser()] User $user, Request $request, BuildingRepository $repo): Response
    {
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        $building = $repo->findOneBy(['id' => $body["building_id"]]);
        $rooms = $building->getRooms();
        return $this->json([
            'message' => 'ok',
            'list_room' => $rooms,
            ], Response::HTTP_OK);  
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class StationController extends AbstractController
{
    #[Route('/api/station/list', name: 'station_list', methods: ['POST'])]
    public function index(#[CurrentUser()] User $user, Request $request, RoomRepository $repo): Response
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
        $room = $repo->findOneBy(['id' => $body["room_id"]]);
        $stations = $room->getStations();
        return $this->json([
            'message' => 'ok',
            'list_station' => $stations,
            ], Response::HTTP_OK);  
    }
}

<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BuildingRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RoomController extends AbstractController
{
    #[Route('/api/room/list', name: 'room_list', methods: ['GET'])]
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

    #[Route('/api/room/create', name: 'room_create', methods: ["POST"])]
    public function create(#[CurrentUser()] User $user, Request $request, EntityManagerInterface $manager, BuildingRepository $repo): Response
    {
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $manager->getConnection()->beginTransaction();

        try{
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $room = new Room;
            $building = $repo->findOneBy(['id' => $body['id_building'], 'user' => $user->getId()]);
            $room->setName($body['name_room'])
            ->setBuilding($building);
            $manager->persist($room);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'room created',
                ], Response::HTTP_CREATED);
        } 
        catch (Exception $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'Bad Request',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

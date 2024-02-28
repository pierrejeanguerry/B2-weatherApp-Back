<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\User;
use App\Repository\RoomRepository;
use App\Repository\StationRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class StationController extends AbstractController
{
    #[Route('/api/station/list', name: 'station_list', methods: ['GET'])]
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

    #[Route('/api/station/create', name: 'station_create', methods: ["POST"])]
    public function create(#[CurrentUser()] User $user, Request $request, EntityManagerInterface $manager, 
    StationRepository $repo, RoomRepository $roomRepo): Response
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
            $t = microtime(true);
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $room = $roomRepo->findOneBy(['id' => $body['id_room']]);
            $station = $repo->findOneBy(['token' => $body['token_station']]);
            $station
            ->setRoom($room)
            ->setActivationDate(new \DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setName($body['name_station']);
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'station created',
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

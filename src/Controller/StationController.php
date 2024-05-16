<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoomRepository;
use App\Repository\StationRepository;
use App\Service\AuthManager;
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
    #[Route('/api/station/list', name: 'station_list', methods: ['POST'])]
    public function index(#[CurrentUser()] User $user, Request $request, RoomRepository $repo, AuthManager $auth): Response
    {
        if (!$auth->checkAuth($user, $request)) {
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
    StationRepository $repo, RoomRepository $roomRepo, AuthManager $auth): Response
    {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
              'message' => 'missing credentials',
              ], Response::HTTP_UNAUTHORIZED);
          }

        $manager->getConnection()->beginTransaction();

        try{
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $room = $roomRepo->findOneBy(['id' => $body['id_room']]);
            $station = $repo->findOneBy(['mac' => $body['mac_address']]);
            if ($station->getState() != 0)
                return $this->json([
                    'message' => 'Station already used',
                    ], Response::HTTP_UNAUTHORIZED);
            $station
            ->setRoom($room)
            ->setActivationDate(new \DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setState(1)
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

    #[Route('/api/station/delete', name: 'station_delete', methods: ["POST"])]
    public function delete(#[CurrentUser()] User $user, Request $request, EntityManagerInterface $manager, 
    StationRepository $repo, RoomRepository $roomRepo, AuthManager $auth): Response
    {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
              'message' => 'missing credentials',
              ], Response::HTTP_UNAUTHORIZED);
          }

        $manager->getConnection()->beginTransaction();

        try{
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $room = $roomRepo->findOneBy(['id' => $body['id_room']]);
            $station = $repo->findOneBy(['mac' => $body['mac_address']]);
            $station
            ->setRoom(null)
            ->setActivationDate(null)
            ->setState(0)
            ->setName(null);
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'station deleted',
                ], Response::HTTP_ACCEPTED);
        } 
        catch (Exception $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'Bad Request',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

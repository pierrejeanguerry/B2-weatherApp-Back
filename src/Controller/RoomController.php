<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuthManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use function Symfony\Component\Clock\now;

class RoomController extends AbstractController
{
    #[Route('/api/room/list', name: 'room_list', methods: ['POST'])]
    public function index(#[CurrentUser()] User $user, Request $request, BuildingRepository $repo, AuthManager $auth, EntityManagerInterface $manager): Response
    {
        if (!$auth->checkAuth($user, $request)) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $manager->getConnection()->beginTransaction();
        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $building = $repo->findOneBy(['id' => $body["building_id"]]);
            $building->setDate(now());
            $rooms = $building->getRooms();
            $manager->persist($building);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'ok',
                'list_room' => $rooms,
                ], Response::HTTP_OK);  
        } catch (UniqueConstraintViolationException $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    #[Route('/api/room/create', name: 'room_create', methods: ["POST"])]
    public function create(#[CurrentUser()] User $user, Request $request, EntityManagerInterface $manager, BuildingRepository $repo, AuthManager $auth): Response
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

    #[Route('/api/room/delete', name: 'room_delete', methods: ["POST"])]
    public function delete(#[CurrentUser()] User $user,Request $request, EntityManagerInterface $manager, RoomRepository $repo, AuthManager $auth): Response
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
            $room = $repo->findOneBy(['id' => $body['id_building']]);
            if ($room->getStations() != null){
                return $this->json([
                    'message' => 'room is not empty',
                    ], Response::HTTP_CONFLICT);
            }
            $repo->remove($room);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'room delete',
                ], Response::HTTP_OK);
        } 
        catch (Exception $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'Bad Request',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

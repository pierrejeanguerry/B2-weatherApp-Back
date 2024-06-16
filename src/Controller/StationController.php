<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\BuildingRepository;
use App\Repository\ReadingRepository;
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
    public function index(#[CurrentUser()] User $user, Request $request, BuildingRepository $repo, ReadingRepository $readingRepo, AuthManager $auth, EntityManagerInterface $manager): Response
    {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $manager->getConnection()->beginTransaction();

        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        $building = $repo->findOneBy(['id' => $body["building_id"]]);
        $stations = $building->getStations();
        try {
            foreach ($stations as $station) {
                $readings = $readingRepo->findBy(['station' => $station]);
                if (!empty($readings)) {
                    $latestReading = end($readings);
                    $readingTime = $latestReading->getDate();
                    $currentTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
                    $currentTime->sub(new \DateInterval('PT1H'));
                    $readingTime->setTimeZone(new DateTimeZone('Europe/Paris'));
                    if ($readingTime < $currentTime) {
                        $station->setState(0);
                        $manager->persist($station);
                        $manager->flush();
                    }
                }
            }
            $manager->getConnection()->commit();
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
            ], Response::HTTP_CONFLICT);
        }
        return $this->json([
            'message' => 'ok',
            'list_station' => $stations,
        ], Response::HTTP_OK);
    }

    #[Route('/api/station/create', name: 'station_create', methods: ["POST"])]
    public function create(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        StationRepository $repo,
        BuildingRepository $buildingRepo,
        AuthManager $auth
    ): Response {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $manager->getConnection()->beginTransaction();

        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);

            $building = $buildingRepo->findOneBy(['id' => $body['id_building']]);
            $station = $repo->findOneBy(['mac' => $body['mac_address']]);

            if ($station && $station->getState()) {
                return $this->json([
                    'message' => 'Station already used',
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!$station) {
                $station = new Station();
                $station->setMac($body['mac_address']);
            }

            $station
                ->setBuilding($building)
                ->setActivationDate(new \DateTime('now', new DateTimeZone('Europe/Paris')))
                ->setState(0)
                ->setName($body['name_station']);
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'station created',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            print_r($e->getMessage());
            return $this->json([
                'message' => 'Bad Request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/station/delete', name: 'station_delete', methods: ["POST"])]
    public function delete(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        StationRepository $repo,
        BuildingRepository $buildingRepo,
        AuthManager $auth
    ): Response {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $manager->getConnection()->beginTransaction();

        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $building = $buildingRepo->findOneBy(['id' => $body['id_building']]);
            $station = $repo->findOneBy(['mac' => $body['mac_address']]);
            $station
                ->setBuilding(null)
                ->setActivationDate(null)
                ->setState(0)
                ->setName(null);
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'station deleted',
            ], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'Bad Request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('api/station/update', name: 'station_update', methods: ["POST"])]
    public function update(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        AuthManager $auth,
        StationRepository $repo,
        BuildingRepository $building_repo
    ) {

        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $manager->getConnection()->beginTransaction();

        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $station = $repo->findOneBy(['mac' => $body["mac_address"]]);
            if ($body['new_name'] != "")
                $station->setName($body['new_name']);
            if ($body['newBuilding_id'] !== 0) {
                $building = $building_repo->findOneBy(["id" => $body["newBuilding_id"]]);
                if ($building)
                    $station->setBuilding($building);
            }
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'station updated'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
            ], Response::HTTP_CONFLICT);
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Service\AuthManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

use function Symfony\Component\Clock\now;

class BuildingController extends AbstractController
{
    #[Route('/api/buildings', name: 'building_list', methods: ['GET'], priority: 2)]
    public function index(
        #[CurrentUser()] User $user, 
        Request $request,
        AuthManager $auth
        ): Response
    {
        if (($authResponse = $auth->checkAuth($user, $request)) !== null)
            return $authResponse;

        $buildings = $user->getBuildings();
        return $this->json([
            'message' => 'ok',
            'list_building' => $buildings,
        ], Response::HTTP_OK);
    }

    #[Route('/api/buildings', name: 'building_create', methods: ["POST"], priority: 2)]
    public function create(
        #[CurrentUser()] User $user, 
        Request $request, 
        EntityManagerInterface $manager,
        AuthManager $auth
    ): Response
    {
        if (($authResponse = $auth->checkAuth($user, $request)) !== null)
            return $authResponse;

        $manager->getConnection()->beginTransaction();

        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $building = new Building;
            $building->setName($body['name_building'])
                ->setUser($user);
            $manager->persist($building);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'building created',
            ], Response::HTTP_CREATED);
        } catch (UniqueConstraintViolationException $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/buildings/{id}', name: 'building_delete', methods: ["DELETE"], priority: 2)]
    public function delete(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        BuildingRepository $buildingRepo,
        int $id,
        AuthManager $auth
    ): Response
    {
        if (($authResponse = $auth->checkAuth($user, $request)) !== null)
            return $authResponse;
        $manager->getConnection()->beginTransaction();
        try {
            $building = $buildingRepo->findOneBy(['id' => $id]);
            if (!$building->getStations()->isEmpty()) {
                return $this->json([
                    'message' => 'building is not empty',
                ], Response::HTTP_FORBIDDEN);
            }
            $manager->remove($building);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'building deleted',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\Reading;
use App\Entity\User;
use App\Repository\ReadingRepository;
use App\Repository\StationRepository;
use App\Service\AuthManager;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ReadingController extends AbstractController
{
    #[Route('/api/reading/send', name: 'send_reading')]
    public function send(Request $request, EntityManagerInterface $manager, StationRepository $stationRepo): Response
    {
        $manager->getConnection()->beginTransaction();
        try {

            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $station = $stationRepo->findOneBy(["mac" => $body['mac_address']]);
            $reading = new Reading();
            $station->setState(1);
            if ($body["temperature"] <= -50 || $body["temperature"] >= 50)
                $body["temperature"] = null;
            if ($body['altitude'] <= -1000 || $body['altitude'] >= 5000)
                $body['altitude'] = null;
            if ($body['pressure'] <= 900 || $body['pressure'] >= 1100)
                $body['pressure'] = null;
            if ($body['humidity'] < 0 || $body['humidity'] > 100)
                $body['humidity'] = null;
            if ($body['temperature'] == null || $body['altitude'] == null || $body['pressure'] == null || $body['humidity'] == null)
                $station->setState(2);

            $reading
                ->setTemperature($body['temperature'])
                ->setAltitude($body['altitude'])
                ->setPressure($body['pressure'])
                ->setHumidity($body['humidity'])
                //->setDate(new \DateTime('now', new DateTimeZone('Europe/Paris')))
                ->setDate(new \DateTime())
                ->setStation($station);
            $manager->persist($reading);
            $manager->persist($station);
            $manager->flush();
            $manager->getConnection()->commit();
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            // var_dump(print_r($e->getMessage()));
            return $this->json([
                'message' => 'Bad Request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json([
            'message' => 'CREATED',
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/reading/{days}/list', name: 'days_list_reading')]
    public function list(
        #[CurrentUser()] User $user,
        Request $request,
        StationRepository $stationRepo,
        AuthManager $auth,
        int $days,
        ReadingRepository $repo
    ): Response {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $station = $stationRepo->findOneBy(["mac" => $body['mac_address']]);
            $readings = $repo->findRecentReadingsByStation($station->getId(), $days);
        } catch (Exception $e) {
            return $this->json([
                'message' => 'Bad Request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'ok',
            'list_readings' => $readings,
        ], Response::HTTP_OK);
    }

    #[Route('/api/reading/list', name: 'list_reading')]
    public function days_list(
        #[CurrentUser()] User $user,
        Request $request,
        StationRepository $stationRepo,
        AuthManager $auth
    ): Response {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $station = $stationRepo->findOneBy(["mac" => $body['mac_address']]);
            $readings = $station->getReadings();
        } catch (Exception $e) {
            return $this->json([
                'message' => 'Bad Request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'ok',
            'list_readings' => $readings,
        ], Response::HTTP_OK);
    }
}

<?php

namespace App\Controller;

use App\Entity\Reading;
use App\Entity\Station;
use App\Repository\StationRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReadingController extends AbstractController
{
    #[Route('/reading', name: 'app_reading')]
    public function index(): Response
    {
        return $this->render('reading/index.html.twig', [
            'controller_name' => 'ReadingController',
        ]);
    }

    #[Route('/api/reading/send', name: 'send_reading')]
    public function send(Request $request, EntityManagerInterface $manager, StationRepository $stationRepo): Response
    {
        $manager->getConnection()->beginTransaction();
        try{

            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $station = $stationRepo->findOneBy(["mac" => $body['mac_address']]);
            $reading = new Reading();
            $reading
            ->setTemperature($body['temperature'])
            ->setAltitude($body['altitude'])
            ->setPressure($body['pressure'])
            ->setHumidity($body['humidity'])
            ->setDate(new \DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setStation($station);
            $manager->persist($reading);
            $manager->flush();
            $manager->getConnection()->commit();
        }
        catch (Exception $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'Bad Request',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->json([
            'message' => 'CREATED',
            ], Response::HTTP_CREATED);
    }


}


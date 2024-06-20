<?php
namespace App\Service;

use App\Entity\Station;
use App\Repository\ReadingRepository;
use DateTimeZone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class StateManager
{
    public function refreshStateStations(EntityManagerInterface $manager, Collection $stations, ReadingRepository $readingRepo){
        foreach ($stations as $station) {
        
            $id = $station->getId();
            $readings = $readingRepo->findBy(["id" => $id]);
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
    }
}
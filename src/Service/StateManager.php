<?php

namespace App\Service;

use App\Repository\ReadingRepository;
use DateTimeZone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class StateManager
{
    private EntityManagerInterface $manager;
    private ReadingRepository $readingRepo;

    public function refreshStationsState(Collection $stations)
    {
        foreach ($stations as $station) {

            $id = $station->getId();
            $readings = $this->readingRepo->findBy(["id" => $id]);
            if (!empty($readings)) {
                $latestReading = end($readings);
                $readingTime = $latestReading->getDate();
                $currentTime = new \DateTime('now', new DateTimeZone('Europe/Paris'));
                $currentTime->sub(new \DateInterval('PT1H'));
                $readingTime->setTimeZone(new DateTimeZone('Europe/Paris'));
                if ($readingTime < $currentTime) {
                    $station->setState(0);
                    $this->manager->persist($station);
                    $this->manager->flush();
                }
            }
        }
        $this->manager->getConnection()->commit();
    }
}

<?php

namespace App\EventListener;

use App\Entity\TEncounterEnc;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;

#[AsEntityListener(event: Events::postPersist, entity: TEncounterEnc::class)]
#[AsEntityListener(event: Events::postUpdate, entity: TEncounterEnc::class)]
class EncounterScoreListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postPersist(TEncounterEnc $encounter, PostPersistEventArgs $event): void
    {
        $this->updateTeamScores($encounter);
    }

    public function postUpdate(TEncounterEnc $encounter, PostUpdateEventArgs $event): void
    {
        $this->updateTeamScores($encounter);
    }

    private function updateTeamScores(TEncounterEnc $encounter): void
    {
        if ($encounter->getTeamBlue()) {
            $encounter->getTeamBlue()->updateScore();
            $this->entityManager->persist($encounter->getTeamBlue());
        }
        
        if ($encounter->getTeamGreen()) {
            $encounter->getTeamGreen()->updateScore();
            $this->entityManager->persist($encounter->getTeamGreen());
        }
        
        $this->entityManager->flush();
    }
}

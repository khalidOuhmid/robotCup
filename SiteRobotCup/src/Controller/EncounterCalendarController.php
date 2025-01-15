<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Repository\EncounterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class EncounterCalendarController extends AbstractController
{
    #[Route('/calendar/{year}/{week}', name: 'app_encounter_calendar', defaults: ['year' => null, 'week' => null])]
    public function index(EncounterRepository $encounterRepository, ?string $year = null, ?string $week = null): Response
    {
        // Utiliser la date actuelle si aucune année ou semaine n'est fournie
        if ($year === null || !is_numeric($year)) {
            // Récupérer l'année courante
            $year = (new \DateTime())->format('Y');
        }

        if ($week === null || !is_numeric($week)) {
            // Récupérer la semaine courante
            $week = (new \DateTime())->format('W');
        }

        // Initialiser les dates de début et de fin de la semaine
        $startDate = new \DateTime();
        $startDate->setISODate((int)$year, (int)$week);
        
        $endDate = new \DateTime();

        $encounters = $encounterRepository
            ->createQueryBuilder('e')
            ->leftJoin('e.timeSlot', 't')
            ->where('t.dateBegin BETWEEN :start AND :end')
            ->setParameter('start', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('end', $endDate->format('Y-m-d 23:59:59'))
            ->orderBy('t.dateBegin', 'ASC')
            ->getQuery()
            ->getResult();

        $calendar = [];
        foreach ($encounters as $encounter) {
            $date = $encounter->getDateBegin()->format('Y-m-d');
            $time = $encounter->getDateBegin()->format('H:i');
            if (!isset($calendar[$date])) {
                $calendar[$date] = [];
            }
            $calendar[$date][$time][] = $encounter;
        }

        $previousWeek = (clone $startDate)->modify('-1 week');
        $nextWeek = (clone $startDate)->modify('+1 week');

        return $this->render('encounters/calendar.html.twig', [
            'calendar' => $calendar,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousWeek' => $previousWeek,
            'nextWeek' => $nextWeek
        ]);
    }
    #[Route('/calendar/export', name: 'app_calendar_export_ical')]
public function exportIcal(EncounterRepository $encounterRepository): Response
{
    // Récupérer l'année en cours
    $currentYear = (new \DateTime())->format('Y');

    // Générer le contenu iCal pour l'année en cours
    $icalContent = $this->generateIcalContent($encounterRepository, $currentYear);

    $response = new Response($icalContent);
    $response->headers->set('Content-Type', 'text/calendar');
    $response->headers->set('Content-Disposition', 'attachment; filename="calendar.ics"');

    return $response;
}

private function generateIcalContent(EncounterRepository $encounterRepository, string $year): string
{
    $icalContent = "BEGIN:VCALENDAR\r\n";
    $icalContent .= "VERSION:2.0\r\n";
    $icalContent .= "PRODID:-//Your Company//Your App//EN\r\n";

    // Récupérer les rencontres de l'année en cours
    $encounters = $encounterRepository->createQueryBuilder('e')
        ->leftJoin('e.SLT_ID', 't')
        ->where('YEAR(t.SLT_DATE_BEGIN) = :year')
        ->setParameter('year', $year)
        ->getQuery()
        ->getResult();

    foreach ($encounters as $encounter) {
        $icalContent .= "BEGIN:VEVENT\r\n";
        $icalContent .= "UID:" . uniqid() . "@yourapp.com\r\n"; // Utiliser un identifiant unique
        $icalContent .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $icalContent .= "DTSTART:" . $encounter->getSLT_ID()->getSLT_DATE_BEGIN()->format('Ymd\THis\Z') . "\r\n";
        $icalContent .= "DTEND:" . $encounter->getSLT_ID()->getSLT_DATE_END()->format('Ymd\THis\Z') . "\r\n";
        $icalContent .= "SUMMARY:" . $encounter->getTeamBlue()->getName() . " vs " . $encounter->getTeamGreen()->getName() . "\r\n";
        $icalContent .= "LOCATION:" . $encounter->getField()->getName() . "\r\n";
        $icalContent .= "END:VEVENT\r\n";
    }

    $icalContent .= "END:VCALENDAR";

    return $icalContent;
}



}

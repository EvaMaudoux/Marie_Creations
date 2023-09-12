<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('admin/agenda', name: 'app_adminCalendar')]
    public function adminAgenda(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();

        $ateliers = [];

        foreach($events as $event){
            $ateliers[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        $data = json_encode($ateliers);

        return $this->render('admin/agenda_admin.html.twig', compact('data'));
    }


    #[Route('ateliers/agenda', name: 'app_agenda')]
    public function agenda(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();
        $ateliers = [];

        foreach($events as $event){
            $workshop = $event->getWorkshop();
            $price = $maxCapacity = $descriptionWorkshop = $workshopId = '';
            $reservations = $event->getReservations();
            if ($workshop) {
                $price = $workshop->getPrice();
                $maxCapacity = $workshop->getMaxCapacity();
                $descriptionWorkshop = $workshop->getDescription();
                $workshopId = $workshop->getId();
            }

            $ateliers[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
                'price' => $price,
                'maxCapacity' => $maxCapacity,
                'descriptionWorkshop' => $descriptionWorkshop,
                'workshopId' => $workshopId,
                'reservations' => $reservations,
            ];
        }

        $data = json_encode($ateliers);

        return $this->render('agenda/agenda.html.twig', compact('data'));
    }

    #[Route('ateliers/agenda/{id}', name: 'workshop_show')]
    public function showWorkshop(Calendar $calendar): Response {
        $workshop = $calendar->getWorkshop();

        if ($workshop) {
            $price = $workshop->getPrice();
            $maxCapacity = $workshop->getMaxCapacity();
            $description = $workshop->getDescription();

            // Utilisation des valeurs récupérées
            return $this->render('calendar/show.html.twig', [
                'calendar' => $calendar,
                'workshop' => $workshop,
                'price' => $price,
                'maxCapacity' => $maxCapacity,
                'description' => $description,
            ]);
        }
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

}

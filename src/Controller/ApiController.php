<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/{id}/edit', name: 'api_event_edit')]
    public function majEvent(?Calendar $calendar, Request $request, EntityManagerInterface $entityManager): Response
    {
        // On récupère les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            // Les données sont complètes
            // On initialise un code
            $code = 200;

            // On vérifie si l'id existe
            if(!$calendar){
                // On instancie un rendez-vous
                $calendar = new Calendar;

                // On change le code
                $code = 201;
            }

            // On hydrate l'objet avec les données
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $calendar->setEnd(new DateTime($donnees->start));
            }else{
                $calendar->setEnd(new DateTime($donnees->end));
            }
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            $entityManager -> persist($calendar);
            $entityManager -> flush();

            // On retourne le code
            return new Response('Modification ok', $code);
        } else {
            // Les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }
        return $this->render('admin/agenda_admin.html.twig');
    }

    #[Route('/maj-available-seats', name: 'api_maj_available_seats')]
    public function majAvailableSeats(Request $request, ReservationRepository $reservationRepo): Response
    {
        // Récupérez l'ID de l'atelier à partir de la requête AJAX
        $workshopId = $request->query->get('workshopId');

        // Utilisez le $workshopId pour obtenir les réservations actuelles pour cet atelier
        // Vous devrez peut-être injecter votre repository de réservations ici
        $reservations = $reservationRepo->findBy(['workshop' => $workshopId]);

        // Créez un tableau avec les données à renvoyer au format JSON
        $data = [
            'reservations' => $reservations,
        ];

        // Convertissez le tableau en JSON et renvoyez-le
        return new Response(json_encode($data));
    }

   }

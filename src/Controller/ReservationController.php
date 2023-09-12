<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\CalendarRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
     #[Route('/nouvelle-reservation', name: 'app_new_reservation')]
    public function createReservation(CalendarRepository $calendarRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Obtenez l'ID de l'atelier depuis la requête (ajustez selon votre logique)
        $workshopId = $request->request->get('workshop_id');

        // Récupérez l'atelier depuis la base de données
        $workshop = $calendarRepository->find($workshopId);

        if (!$workshop) {
            return new Response(json_encode(['message' => 'Atelier introuvable']), 404);
        }

        $existingReservation = $user->getReservations()->filter(function (Reservation $reservation) use ($workshopId) {
            return $reservation->getWorkshop()->getId() === $workshopId;
        });

        if (!$existingReservation->isEmpty()) {
            return new Response(json_encode(['message' => 'Vous avez déjà réservé cet atelier', 'alreadyReserved' => true]), 400);
        }

        if ($user instanceof User) {
            // Récupérez l'atelier depuis la base de données
            $workshop = $calendarRepository->find($workshopId);

            if (!$workshop) {
                return new Response(json_encode(['message' => 'Atelier introuvable']), 404);
            }

            // Créez une nouvelle réservation
            $reservation = new Reservation();
            $reservation->setCreatedAt(new \DateTimeImmutable());
            $reservation->setStatus('en attente');
            $reservation->setUserId($user);
            $reservation->setWorkshop($workshop);
            $workshop->addReservation($reservation);

            // Persistez et enregistrez la réservation dans la base de données
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Ajout de l'atelier réservé à l'utilisateur dans le stockage local
            $existingReservedWorkshopIds[] = $workshopId;
            $response = new Response(json_encode(['message' => 'Réservation créée avec succès', 'alreadyReserved' => false]), 200);
            $response->headers->setCookie(new Cookie('reservedWorkshopIds', json_encode($existingReservedWorkshopIds)));
            return $response;
        } else {
            // Gérer le cas où $user n'est pas une instance de User
            return new Response(json_encode(['message' => 'Utilisateur non connecté']), 403);

        }
    }


    #[Route('/profil/mes-reservations', name: 'app_my_reservations')]
    public function myReservations(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // Récupérez les réservations de l'utilisateur depuis la base de données (ajustez selon votre modèle)
        $reservations = $user->getReservations();

        return $this->render('user/myReservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /* Afficher les réservations d'un utilisateur
    #[Route('/reservations/user/{id}', name: 'reservations_for_user')]
    public function reservationsForUser(ReservationRepository $reservationRepo, EntityManagerInterface $manager,): Response
    {
        $user = $this->getUser();
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $reservations = $user->getReservations();

        return $this->render('reservation/list.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }
    */


}

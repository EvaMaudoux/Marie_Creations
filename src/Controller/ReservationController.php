<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    // Afficher toutes les réservations
    #[Route('/reservations', name: 'app_reservations')]
    public function reservations(ReservationRepository $reservationRepo): Response
    {
        $reservations = $reservationRepo ->findAll();
        return $this->render('reservation/index.html.twig', [
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

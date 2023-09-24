<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\SubscriptionNotif;
use App\Entity\User;
use App\Repository\CalendarRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
     #[Route('/nouvelle-reservation', name: 'app_new_reservation')]
    public function createReservation(CalendarRepository $calendarRepository, Request $request, EntityManagerInterface $entityManager, WebPush $webPush): Response
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

            // Envoyez la notification après une réservation réussie
            $this->sendNotification($entityManager, $webPush);

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

    #[Route('/send-notification', name: 'send_notification')]
    public function sendNotification(EntityManagerInterface $entityManager, WebPush $webPush): Response
    {
        // Récupérez tous les abonnements depuis la base de données
        $subscriptions = $entityManager->getRepository(SubscriptionNotif::class)->findAll();
        // var_dump($subscriptions);
        // Je m'authentifie auprès du serveur Push avec ma clé privée et ma clé publique générées
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:contact@marie-creations.be',
                'publicKey' => 'BL2uSX9-tfxlcYdm157dv-xf_5o7kDo8DfOfHgWUcymTGE6xv5GA_9DwoJdIAOV5JlM8GXR6uAzBIUMjo0fPHMc',
                'privateKey' => 'mG2brPkLhhP5Q2Buf_WLXcGS7FxgeSV9pMnHkh0WGgg',
            ],
        ];

        // Créez un objet WebPush
        $webPush = new WebPush($auth);
        //var_dump($auth);

        // Envoyez la notification à tous les abonnés
        foreach ($subscriptions as $subscription) {
            // chaque notification est placée dans une file d'attente
            $webPush->queueNotification(
            // récupération des données authentifiantes de chaque abonnement (de chaque utilisateur)
                Subscription::create([
                    'endpoint' => $subscription->getEndpoint(),
                    'publicKey' => $subscription->getPublicKey(),
                    'authToken' => $subscription->getAuthToken(),
                ]),
                json_encode([
                    'title' => 'Merci pour votre réservation !',
                    'body' => 'Votre inscription à l\'atelier a bien été prise en compte et est désormais en attente de confirmation par Marie.',
                ])
            );
            //var_dump($subscription->getEndpoint());
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                var_dump("[v] Message sent successfully for subscription {$endpoint}.");
                // var_dump($webPush);
            } else {
                var_dump("[x] Message failed to sent for subscription : {$report->getReason()}");
            }
        }
        return new Response(json_encode(['success' => true, 'message' => 'Notification']));

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

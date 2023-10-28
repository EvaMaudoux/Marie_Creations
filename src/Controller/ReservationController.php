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
use Symfony\Component\Security\Core\User\UserInterface;

class ReservationController extends AbstractController
{
     #[Route('/nouvelle-reservation', name: 'app_new_reservation')]
     public function createReservation(CalendarRepository $calendarRepository, Request $request, EntityManagerInterface $entityManager, WebPush $webPush): Response
     {
         $user = $this->getUser();
         $workshopId = $request->request->get('workshop_id');
         $workshop = $calendarRepository->find($workshopId);

         if (!$workshop) {
             return new Response(json_encode(['message' => 'Atelier introuvable']), 404);
         }

         // Vérifiez si l'utilisateur a déjà réservé cet atelier
         if ($user instanceof User) {
             $existingReservation = $user->getReservations()->filter(function (Reservation $reservation) use ($workshopId) {
                 return $reservation->getWorkshop()->getId() === $workshopId;
             });

             if (!$existingReservation->isEmpty()) {
                 return new Response(json_encode(['message' => 'Vous avez déjà réservé cet atelier', 'alreadyReserved' => true]), 400);
             }

             // Utilisation de la transaction pour éviter la duplication
             $entityManager->beginTransaction();

             try {
                 $reservation = new Reservation();
                 $reservation->setCreatedAt(new \DateTimeImmutable());
                 $reservation->setStatus('en attente');
                 $reservation->setUserId($user);
                 $reservation->setWorkshop($workshop);
                 $workshop->addReservation($reservation);

                 $entityManager->persist($reservation);
                 $entityManager->flush();

                 $entityManager->commit();

                 $latestReservation = $reservation;

                 $this->sendNotificationNewReservation($entityManager, $webPush, $user, $latestReservation);

                 $response = new Response(json_encode(['message' => 'Réservation créée avec succès', 'alreadyReserved' => false]), 200);
                 $response->headers->setCookie(new Cookie('reservedWorkshopIds', json_encode([$workshopId])));

                 return $response;
             } catch (\Exception $e) {
                 $entityManager->rollback();
                 return new Response(json_encode(['message' => 'Une erreur s\'est produite lors de la réservation']), 500);
             }
         } else {
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
    public function sendNotificationNewReservation(EntityManagerInterface $entityManager, WebPush $webPush, UserInterface $user, Reservation $reservation): Response
    {
        // Récupérez toutes les souscriptions de l'utilisateur actuel
        $subscriptions = $entityManager->getRepository(SubscriptionNotif::class)->findBy(['user' => $user]);

        if (empty($subscriptions)) {
            return new Response(json_encode(['success' => false, 'message' => 'User is not subscribed or has no reservations.']));
        }

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:contact@marie-creations.be',
                'publicKey' => 'BL2uSX9-tfxlcYdm157dv-xf_5o7kDo8DfOfHgWUcymTGE6xv5GA_9DwoJdIAOV5JlM8GXR6uAzBIUMjo0fPHMc',
                'privateKey' => 'mG2brPkLhhP5Q2Buf_WLXcGS7FxgeSV9pMnHkh0WGgg',
            ],
        ];

        // Créez un objet WebPush
        $webPush = new WebPush($auth);

        foreach ($subscriptions as $subscription) {
            $subscriptionData = [
                'endpoint' => $subscription->getEndpoint(),
                'publicKey' => $subscription->getPublicKey(),
                'authToken' => $subscription->getAuthToken(),
            ];

            // Placez la notification dans la file d'attente pour chaque abonnement de l'utilisateur
            $webPush->queueNotification(
                Subscription::create($subscriptionData),
                json_encode([
                    'title' => 'Merci pour votre réservation ' . $user->getFirstName() . '!',
                    'body' => 'Votre inscription à l\'atelier " ' . $reservation->getWorkshop()->getTitle() . ' " a bien été prise en compte et est désormais en attente de confirmation par Marie.',
                ])
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                var_dump("[v] Message sent successfully for subscription {$endpoint}.");
            } else {
                var_dump("[x] Message failed to sent for subscription : {$report->getReason()}");
            }
        }

        return new Response(json_encode(['success' => true, 'message' => 'Notifications sent.']));
    }

}

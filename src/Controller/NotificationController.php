<?php

namespace App\Controller;

use App\Entity\SubscriptionNotif;
use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
/*
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
*/
}

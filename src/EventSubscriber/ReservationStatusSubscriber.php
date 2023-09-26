<?php

namespace App\EventSubscriber;

use App\Entity\Reservation;
use App\Entity\SubscriptionNotif;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class ReservationStatusSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Vérifiez si l'entité est une instance de Reservation
        if ($entity instanceof Reservation) {
            $entityManager = $args->getObjectManager();

            // Obtenez l'ancien statut de la réservation avant la mise à jour
            $changeset = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);
            $oldStatus = $changeset['status'][0] ?? null;
            $newStatus = $changeset['status'][1] ?? null;

            // Vérifiez si le statut a changé
            if ($oldStatus !== $newStatus) {
                // Le statut a changé, vous pouvez maintenant envoyer la notification
                $this->sendNotificationStatusChange($entityManager, $entity, $newStatus);
            }
        }
    }

    // Fonction pour envoyer la notification
    private function sendNotificationStatusChange($entityManager, $reservation, $newStatus)
    {

        // Récupérez toutes les souscriptions de l'utilisateur actuel
        $user = $reservation->getUserId();

        if (!$user) {
            return; // L'utilisateur n'est pas associé à cette réservation, aucune notification à envoyer
        }

        $subscriptions = $entityManager->getRepository(SubscriptionNotif::class)->findBy(['user' => $user]);

        /// / Code pour envoyer la notification ici
        // Vous pouvez personnaliser le message en fonction du nouveau statut
        $title = 'Votre réservation est maintenant ' . $newStatus . ' ! ';
        $message = 'Bonjour ' . $user->getFirstName() . ' ! Votre réservation pour l\'atelier ' . $reservation->getWorkshop()->getTitle() . ' est désormais '. $newStatus . '. Pour plus d\'informations, consultez la page "mes réservations".';


        if (empty($subscriptions)) {
            return; // Pas de souscriptions, aucune notification à envoyer
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
                    'title' => $title,
                    'body' => $message,
                ])
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                var_dump("[v] Message sent successfully for subscription {$endpoint}.");
            } else {
                var_dump("[x] Message failed to sent for subscription: {$report->getReason()}");
            }
        }
    }
}
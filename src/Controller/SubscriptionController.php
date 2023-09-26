<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\SubscriptionNotif;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function saveSubscription(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Vérifiez si l'utilisateur est authentifié
        $user = $this->getUser();
        var_dump($user);

        if ($user === null) {
            return new Response(json_encode(['success' => false, 'message' => 'User not authenticated.']), 401);
        }

        // Vérifiez si le contenu de la requête est au format JSON
        if ($request->headers->get('Content-Type') === 'application/json') {
            // Récupérez les données JSON de la requête
            $data = json_decode($request->getContent(), true);

            // Vérifiez si le décodage JSON a réussi
            if ($data !== null) {
                // Créez une nouvelle entité SubscriptionNotif et affectez-lui les données
                $subscription = new SubscriptionNotif();
                $subscription->setEndpoint($data['endpoint']);
                $subscription->setPublicKey($data['keys']['p256dh']);
                $subscription->setAuthToken($data['keys']['auth']);
                $subscription->setUser($user);

                // Essayez de persister l'entité en base de données
                try {
                    $entityManager->persist($subscription);
                    $entityManager->flush();
                } catch (UniqueConstraintViolationException $e) {
                    // Si une violation de contrainte d'unicité se produit, cela signifie qu'un enregistrement similaire existe déjà
                    // Vous pouvez ignorer cette exception ou gérer d'une autre manière
                    return new Response(json_encode(['success' => false, 'message' => 'Subscription already exists.']));
                }

                // Retournez une réponse JSON en cas de succès
                return new Response(json_encode(['success' => true, 'message' => 'Subscription saved successfully.']));
            }
        }

        // En cas d'erreur ou de contenu JSON invalide, retournez une réponse d'erreur
        return new Response(json_encode(['success' => false, 'message' => 'Invalid JSON.']), 400, [
            'Content-Type' => 'application/json',
        ]);
    }
}

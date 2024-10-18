<?php
// src/EventListener/ApiKeyListener.php
namespace App\EventListener;

use App\Attribute\RequireApiKey;
use App\Service\CheckApiKeyClientService;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyListener
{
    private $checkApiKeyService;

    public function __construct(CheckApiKeyClientService $checkApiKeyService)
    {
        $this->checkApiKeyService = $checkApiKeyService;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // $controller peut être une classe ou une closure
        if (!is_array($controller)) {
            return;
        }

        $reflectionClass = new \ReflectionClass($controller[0]);
        $reflectionMethod = $reflectionClass->getMethod($controller[1]);

        $classHasAttribute = $reflectionClass->getAttributes(RequireApiKey::class);
        $methodHasAttribute = $reflectionMethod->getAttributes(RequireApiKey::class);

        if ($classHasAttribute || $methodHasAttribute) {
            $request = $event->getRequest();
            $apiKey = $request->headers->get('X-API-KEY'); // Ajustez le nom de l'en-tête si nécessaire

            $client = $this->checkApiKeyService->checkKey($apiKey);

            if (!$client) {
                // Créer une réponse JSON personnalisée si la clé API est invalide
                $response = new JsonResponse(
                    ['message' => 'Clé API client invalide'], // Le message JSON
                    Response::HTTP_FORBIDDEN // Code de statut HTTP 403
                );

                // Arrêter l'exécution du contrôleur et renvoyer la réponse JSON
                $event->setController(function() use ($response) {
                    return $response;
                });

                // Arrêter la propagation de l'événement
                return;
            }

            // Optionnel : stocker l'objet client dans les attributs de la requête
            $request->attributes->set('client', $client);
        }
    }
}


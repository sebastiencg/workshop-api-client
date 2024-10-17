<?php
// src/EventListener/ApiKeyListener.php
namespace App\EventListener;

use App\Attribute\RequireApiKey;
use App\Service\CheckApiKeyClientService;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
                throw new AccessDeniedHttpException('Clé API client invalide');
            }

            // Optionnel : stocker l'objet client dans les attributs de la requête
            $request->attributes->set('client', $client);
        }
    }
}

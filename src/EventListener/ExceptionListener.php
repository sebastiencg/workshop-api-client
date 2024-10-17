<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // Initialiser la réponse JSON
        $response = new JsonResponse();

        // Personnaliser le message et le code d'état en fonction de l'exception
        if ($exception instanceof AccessDeniedHttpException) {
            $response->setStatusCode(403);
            $message = 'Clé API client invalide';
        } elseif ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $message = $exception->getMessage();
        } else {
            $response->setStatusCode(500);
            $message = 'Une erreur interne est survenue';
        }

        // Définir le contenu de la réponse JSON
        $response->setData([
            'error' => true,
            'message' => $message,
        ]);

        // Remplacer la réponse par défaut par la réponse JSON
        $event->setResponse($response);
    }
}

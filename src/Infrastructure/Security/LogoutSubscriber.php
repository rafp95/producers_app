<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        // Sprawdzamy, czy to zapytanie do API (opcjonalnie, tu mamy jeden firewall)
        if ($event->getRequest()->getPathInfo() !== '/api/logout') {
            return;
        }

        $response = new JsonResponse([
            'message' => 'Wylogowano pomyślnie.',
            'status' => 200,
        ], Response::HTTP_OK);

        $event->setResponse($response);
    }
}

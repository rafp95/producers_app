<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
       
        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
            'path' => $request->getPathInfo()
        ]);
        
        $status = match (true) {
            $exception instanceof AccessDeniedException => JsonResponse::HTTP_FORBIDDEN,
            $exception instanceof AuthenticationException => JsonResponse::HTTP_UNAUTHORIZED,
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        };

        // Przygotowujemy uproszczoną odpowiedź dla użytkownika
        $responseData = [
            'message' => $exception->getMessage(),
            'code' => $status,
        ];

        // Tworzymy odpowiedź JSON
        $response = new JsonResponse($responseData, $status);

        // Wysyłamy odpowiedź do klienta, przerywając dalsze propagowanie zdarzenia
        $event->setResponse($response);
    }
}

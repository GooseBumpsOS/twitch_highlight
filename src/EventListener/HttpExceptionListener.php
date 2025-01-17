<?php

declare(strict_types=1);


namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $response = new JsonResponse(['error' => $exception->getMessage()]);
            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
        }
    }
}
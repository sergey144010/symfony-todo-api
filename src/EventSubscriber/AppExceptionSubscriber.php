<?php

namespace App\EventSubscriber;

use App\Exceptions\AppException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AppExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 5],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AppException) {
            return;
        }

        $data = [
            'status' => 'fail',
            'data' => [
                'message' => $exception->getMessage(),
            ],
        ];

        $response = new JsonResponse($data, JsonResponse::HTTP_CONFLICT);
        $event->setResponse($response);
    }
}

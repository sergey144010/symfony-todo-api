<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof ValidationFailedException ||
            $exception->getPrevious() instanceof ValidationFailedException
        ) {
            /** @var ValidationFailedException $validationFailedException */
            $validationFailedException = ($exception instanceof ValidationFailedException)
                ? $exception
                : $exception->getPrevious()
            ;

            $errors = [];
            foreach ($validationFailedException->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response = new JsonResponse(
                [
                    'status' => 'fail',
                    'data' => [
                        'message' => 'Validation failed',
                        'errors' => $errors
                    ],
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );

            $event->setResponse($response);
        }
    }
}

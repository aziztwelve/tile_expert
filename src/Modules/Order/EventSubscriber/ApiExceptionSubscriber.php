<?php

declare(strict_types=1);

namespace App\Modules\Order\EventSubscriber;

use App\Modules\Order\Exception\ArticleNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        match (true) {
            $exception instanceof ArticleNotFoundException =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 404)),

            default =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500)),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\EventSubscriber;

use App\Modules\Order\Exception\ArticleNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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

            // 422 — ошибки бизнес-валидации (DTO)
            $exception instanceof ValidationFailedException =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->formatValidationErrors($exception),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)),

            // 400 — некорректный запрос
            $exception instanceof BadRequestHttpException =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST)),

            // 404 — бизнес-сущность не найдена
            $exception instanceof ArticleNotFoundException =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND)),

            // остальные HTTP-исключения (403, 401 и т.д.)
            $exception instanceof HttpExceptionInterface =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode())),

            // 500 — всё остальное
            default =>
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => 'Internal server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR)),
        };
    }

    private function formatValidationErrors(ValidationFailedException $exception): array
    {
        $errors = [];

        foreach ($exception->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $errors;
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class JsendController extends AbstractController
{
    /** @phpstan-ignore-next-line */
    public function respondSuccess(null|array|object $data = null): JsonResponse
    {
        return $this->json(
            [
                'status' => 'success',
                'data' => $data
            ],
            Response::HTTP_OK
        );
    }

    /** @phpstan-ignore-next-line */
    public function respondFail(
        array|object $data,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return $this->json(
            [
                'status' => 'fail',
                'data' => $data
            ],
            $statusCode
        );
    }

    public function respondError(
        string $message,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return $this->json(
            [
                'status' => 'error',
                'message' => $message,
            ],
            $statusCode
        );
    }
}

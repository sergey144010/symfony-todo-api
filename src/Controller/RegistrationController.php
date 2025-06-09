<?php

namespace App\Controller;

use App\Service\Registration\RegistrationDto;
use App\Service\Registration\RegistrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class RegistrationController extends JsendController
{
    #[OA\Post(
        summary: 'Регистрация нового пользователя',
        tags: ['Аутентификация'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Успешная регистрация пользователя',
        content: new OA\JsonContent(ref: '#/components/schemas/SuccessRegistrationResponse')
    )]
    #[OA\Response(
        response: 400,
        description: 'Ошибки валидации',
        content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
    )]
    #[OA\Response(
        response: 500,
        description: 'Ошибка сервера',
        content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')
    )]
    #[Route('/api/register', name: 'api_register', methods: [Request::METHOD_POST])]
    public function register(
        #[MapRequestPayload] RegistrationDto $dto,
        RegistrationService $registrationService,
    ): JsonResponse {
        $user = $registrationService->registration($dto);

        return $this->respondSuccess(
            [
                'message' => 'User registered successfully',
                'id' => $user->getId(),
            ]
        );
    }
}

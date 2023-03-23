<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\GetFormErrorsService;
use App\Service\SignUpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly SignUpService $signUpService,
        private readonly GetFormErrorsService $errorsService
    ) {}

    #[Route(
        '/register',
        name: 'register',
        methods: ['POST']
    )]
    public function register(Request $request,): JsonResponse
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(UserType::class);

        $form->submit($data);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $errors = $this->errorsService->getErrorsFromForm($form);
            return $this->json([
                'message' => "Unable to create new user: " . implode(". ", $errors)
            ]);
        }

        return $this->json([
            'message' => $this->signUpService->signUp($form)
        ]);
    }
}

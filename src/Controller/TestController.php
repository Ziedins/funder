<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/test', name: 'app_test')]
    public function index(): JsonResponse
    {
        return new JsonResponse("Okay");
    }
}

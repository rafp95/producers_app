<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): Response
    {
        return new Response('', Response::HTTP_UNAUTHORIZED);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Ta metoda może pozostać pusta - zostanie przechwycona przez firewall
    }
}

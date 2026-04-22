<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Symfony\Action\DocumentationAction;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ApiDocController
{
    public function __construct(
        private DocumentationAction $documentationAction
    ) {
    }

    #[Route('/api_docs/index.html', name: 'api_doc_custom', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return ($this->documentationAction)($request);
    }
}

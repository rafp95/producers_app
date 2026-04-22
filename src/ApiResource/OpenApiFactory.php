<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        
        // Add login endpoint to OpenAPI
        $paths = $openApi->getPaths();
        $loginPath = new Model\PathItem(
            post: new Model\Operation(
                tags: ['Security'],
                responses: [
                    '200' => [
                        'description' => 'Zalogowano pomyślnie.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Zalogowano pomyślnie.'],
                                        'status' => ['type' => 'integer', 'example' => 200],
                                        'user' => ['type' => 'string', 'example' => 'rest'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Błędne dane logowania.',
                    ],
                ],
                                description: 'Umożliwia uzyskanie sesji po podaniu loginu i hasła.',
                requestBody: new Model\RequestBody(
                    description: 'The login data',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => ['type' => 'string', 'example' => 'rest'],
                                    'password' => ['type' => 'string', 'example' => 'vKTUeyrt1!'],
                                ],
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $paths->addPath('/api/login', $loginPath);

        // Add logout endpoint to OpenAPI
        $logoutPath = new Model\PathItem(
            get: new Model\Operation(
                tags: ['Security'],
                responses: [
                    '200' => [
                        'description' => 'Wylogowano pomyślnie.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Wylogowano pomyślnie.'],
                                        'status' => ['type' => 'integer', 'example' => 200],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            description: 'Kończy bieżącą sesję użytkownika.',
            ),
        );
        $paths->addPath('/api/logout', $logoutPath);

        $openApi = $openApi->withPaths($paths);

        return $openApi;
    }
}

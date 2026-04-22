<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\ProducerProvider;
use App\State\ProducerProcessor;
use ArrayObject;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Parameter;

  #[ApiResource(
    operations: array(
        new GetCollection(
            uriTemplate: '/get_all',
            openapi: new Operation(
                responses: [
                    '200' => [
                        'description' => 'Lista producentów',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => [
                                                'type' => 'integer',
                                                'description' => 'Unikalny identyfikator producenta',
                                                'example' => 1
                                            ],
                                            'name' => [
                                                'type' => 'string',
                                                'description' => 'Nazwa producenta',
                                                'example' => 'Producent Testowy'
                                            ],
                                            'createdAt' => [
                                                'type' => 'string',
                                                'format' => 'date-time',
                                                'description' => 'Data utworzenia producenta (ISO 8601)',
                                                'example' => '2024-04-22T11:19:00'
                                            ],
                                            'updatedAt' => [
                                                'type' => 'string',
                                                'format' => 'date-time',
                                                'description' => 'Data ostatniej aktualizacji (ISO 8601)',
                                                'example' => '2024-04-22T11:19:00'
                                            ]
                                        ],
                                        'required' => ['id', 'name']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'Pobiera listę producentów',
                description: 'Zwraca listę producentów z paginacją. Każdy producer zawiera datę utworzenia i aktualizacji.',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        description: 'Numer strony (domyślnie: 1)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'default' => 1]
                    ),
                    new Parameter(
                        name: 'limit',
                        in: 'query',
                        description: 'Liczba wyników na stronę (domyślnie: 10, max: 100)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 10]
                    )
                ]
            ),
            normalizationContext: array('groups' => array('producer:read')),
            provider: ProducerProvider::class
        ),
        new Post(
            uriTemplate: '/create_one',
            openapi: new Operation(
                responses: [
                    '201' => [
                        'description' => 'Producent został utworzony',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'integer',
                                            'description' => 'Unikalny identyfikator nowego producenta',
                                            'example' => 1
                                        ],
                                        'name' => [
                                            'type' => 'string',
                                            'description' => 'Nazwa utworzonego producenta',
                                            'example' => 'Nowy Producent'
                                        ],
                                        'createdAt' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'description' => 'Data utworzenia producenta (ISO 8601)',
                                            'example' => '2024-04-22T11:21:00'
                                        ],
                                    ],
                                    'required' => ['id', 'name', 'createdAt']
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'Tworzy producenta',
                description: 'Tworzy nowego producenta. W odpowiedzi zwraca utworzony obiekt z datą utworzenia.',
                requestBody: new RequestBody(
                    content: new ArrayObject(array(
                        'application/json' => array(
                            'schema' => array(
                                'type' => 'object',
                                'required' => array('name'),
                                'properties' => array(
                                    'name' => array(
                                        'type' => 'string',
                                        'example' => 'Nowy Producent'
                                    )
                                )
                            )
                        )
                    ))
                )
            ),
            normalizationContext: array('groups' => array('producer:read')),
            denormalizationContext: array('groups' => array('producer:write')),
            processor: ProducerProcessor::class
        )
    ),
    paginationEnabled: true
 )]
class ProducerResource
{
    #[Groups(['producer:read'])]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Groups(['producer:read', 'producer:write'])]
    public string $name = '';

    #[Groups(['producer:read'])]
    public ?string $createdAt = null;

    #[Groups(['producer:read'])]
    public ?string $updatedAt = null;

    public function __construct(
        ?int $id = null,
        string $name = '',
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}

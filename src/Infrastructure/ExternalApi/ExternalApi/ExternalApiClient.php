<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalApi\ExternalApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class ExternalApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl,
        private string $username,
        private string $password
    ) {
    }

    public function request(string $method, string $path, array $options = []): ResponseInterface
    {
        $options['auth_basic'] = [$this->username, $this->password];
        $url = $this->baseUrl . $path;

        return $this->httpClient->request($method, $url, $options);
    }
}

<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function request($method, $uri, $parameters = [])
    {
        return $this->client->request($method, $uri, $parameters);
    }
}

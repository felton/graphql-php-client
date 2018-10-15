<?php

namespace GraphQLClient;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    protected $httpClient;

    public function __construct(string $url)
    {
        $this->httpClient = new GuzzleClient(['base_uri' => $url]);
    }

    public function query(string $query, array $variables = [])
    {
        $queryData = [
            'query' => $query,
            'variables' => $variables,
        ];

        $resp = $this->httpClient->request('POST', '', ['json' => $queryData]);

        return $resp->getBody()->getContents();
    }
}

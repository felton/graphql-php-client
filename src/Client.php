<?php

namespace GraphQLClient;

use GuzzleHttp\Client as GuzzleClient;

use GuzzleHttp\Psr7\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Client
{
    //use Request;

    protected $httpClient;

    protected $url;

    protected $options;

    public function __construct(string $url, array $options = [])
    {
        $this->httpClient = new GuzzleClient();
        $this->url = $url;
        $this->options = $this->resolveOptions($options);
    }

    public function query(string $query = '', array $variables = [])
    {
        $queryData = [
            'query' => $query,
            'variables' => $variables,
        ];

        //$resp = $this->httpClient->request('POST', '', ['json' => $queryData]);

        $request = new Request('POST', $this->url);

        $response = $this->httpClient->send($request, ['json' => $queryData]);

        return $response->getBody()->getContents();
    }

    protected function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return $resolver->resolve($options);
    }
}

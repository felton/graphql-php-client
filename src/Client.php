<?php

namespace GraphQLClient;

use GraphQLClient\Traits\Request;
use GraphQLClient\Traits\Response;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Client
{
    use Request;
    use Response;

    /**
     * Guzzle HTTP client
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * GraphQL Server URL
     *
     * @var string
     */
    protected $url;

    /**
     * Guzzle client options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Client constructor
     *
     * @param string $url     Endpoint url
     * @param array  $options client options
     */
    public function __construct(string $url, array $options = [])
    {
        $this->setOptions($options);
        $this->httpClient = $this->buildClient($this->getOptions());
        $this->setUrl($url);
    }

    /**
     * execute GraphQL query
     *
     * @param string $query     GraphQL Query
     * @param array  $variables possible variables for use in query
     *
     * @return string           Response text from a GraphQL server
     */
    public function query(string $query = '', array $variables = [])
    {
        $queryData = [
            'query' => $query,
        ];

        if ($variables) {
            $queryData['variables'] = $variables;
        }

        $this->request = $this->buildRequest($queryData);

        try {
            $this->response = $this->httpClient->sendRequest($this->request);
        } catch (\Http\Client\Exception\TransferException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $this->handleResponse();
    }

    /**
     * Set default client options
     *
     * @param array $options Guzzle Options
     *
     * @return array          Array of resolved options
     */
    protected function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        // $resolver->setDefined(array_keys($options))
        return $resolver->resolve($options);
    }

    /**
     * buildClient creates a guzzle client, but has support for using any
     * httplug compatible client. @see http://httplug.io/
     *
     * @param array $options Client options
     *
     * @return \Http\Client\HttpClient HTTP client
     */
    protected function buildClient(array $options = [])
    {
        $guzzle = new GuzzleClient($options);

        return new GuzzleAdapter($guzzle);
    }

    /**
     * getter for client options
     *
     * @return array client options
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $this->resolveOptions($options);
    }

    public function setUrl($url = '')
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}

<?php

namespace GraphQLClient;

use GraphQLClient\Traits\Request;
use GraphQLClient\Traits\Response;
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
        $this->httpClient = $this->buildClient();
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

        return $this->handleResponse($this->getOptions()['json']);
    }

    /**
     * buildClient creates a guzzle client, but has support for using any
     * httplug compatible client. @see http://httplug.io/
     *
     * @return \Http\Client\HttpClient HTTP client
     */
    protected function buildClient()
    {
        return new GuzzleAdapter();
    }

    /**
     * Set default client options
     *
     * @param array $options Client Options
     *
     * @return array          Array of resolved options
     */
    protected function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefaults([
                'request' => function (OptionsResolver $requestResolver) {
                    $requestResolver
                        ->setDefaults([
                           'method' => 'POST',
                            'headers' => [
                                'Content-Type' => 'application/json',
                            ],
                        ])
                        ->setAllowedValues('method', ['POST', 'GET'])
                        ->setAllowedTypes('headers', 'array');
                },
                'client' => function (OptionsResolver $clientResolver) {
                },
                'json' => true,

            ])
            ->setAllowedTypes('json', 'bool');

        $this->configureOptions($resolver);

        // Resolve all options here
        return $resolver->resolve($options);
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

    /**
     * [configureOptions description]
     *
     * @param OptionsResolver $resolver [description]
     */
    protected function configureOptions(OptionsResolver $resolver) : void
    {
    }
}

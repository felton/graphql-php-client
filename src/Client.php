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
        $this->httpClient = $this->buildClient($this->getOptions()['client'] ?? []);
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
    protected function buildClient(array $options = [])
    {
        return GuzzleAdapter::createWithConfig($options);
    }

    /**
     * Set default client options
     *
     * Client Options:
     * `request` - An array that has a `method` and `headers` element,
     *             these get sent with the request
     * `client` - An array that gets sent to the Guzzle client to be configured
     *            This will be removed in the next release
     * `json`   - Flag that determines whether an array or text is returned from a
     *            response
     *
     * @param array $options Client Options
     *
     * @return array          Array of resolved options
     */
    protected function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'request' => function (OptionsResolver $requestResolver) {
                $requestResolver->setDefaults([
                   'method' => 'POST',
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ])
                ->setAllowedValues('method', ['POST', 'GET'])
                ->setAllowedTypes('headers', 'array');
            },
            'json' => true,
        ])
        ->setDefined('client')
        ->setAllowedTypes('json', 'bool')
        ->setAllowedTypes('client', 'array');

        // Configure options, if necessary
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

    /**
     * setter for client options
     *
     * @param array $options client options
     */
    public function setOptions(array $options = [])
    {
        $this->options = $this->resolveOptions($options);
    }

    /**
     * setter for client url
     *
     * @param string $url endpoint
     */
    public function setUrl($url = '')
    {
        $this->url = $url;
    }

    /**
     * getter for url endpoint
     *
     * @return string current url endpoint
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Split option configuration into its own method so sub-classes can override
     * if necessary.
     *
     * @param OptionsResolver $resolver OptionsResolver from resolveOptions()
     */
    protected function configureOptions(OptionsResolver $resolver) : void
    {
    }
}

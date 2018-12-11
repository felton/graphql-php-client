<?php

namespace GraphQLClient\Exception;

class QueryException extends \RuntimeException
{
    /**
     * Error data returned in the GraphQL response
     *
     * @var array
     */
    protected $errorData = [];

    /**
     * All successful data that was returned before the
     * error occurred
     *
     * @var array
     */
    protected $successfulData = [];

    /**
     * Body of the response
     *
     * @var string
     */
    protected $responseBody = '';

    /**
     * QueryException constructor
     *
     * @param array $errorData      Errors returned from a GraphQL query request
     * @param array $successfulData Any executed data returned before the error occurred
     */
    public function __construct($errorData, $successfulData = [], $responseBody = '')
    {
        $this->errorData = $errorData;
        $this->successfulData = $successfulData;
        $this->responseBody = $responseBody;

        $messages = implode(' - ', $this->errorColumn('message'));
        parent::__construct($messages);
    }

    /**
     * Returns the `locations` fields in the Error response
     *
     * @return array `locations` fields
     */
    public function getLocations()
    {
        return $this->errorColumn('locations');
    }

    /**
     * Returns the `path` field in the Error response
     *
     * @return array `path` fields
     */
    public function getPath()
    {
        return $this->errorColumn('path');
    }

    /**
     * Returns the `extensions` field in the Error response
     *
     * @return array `extensions` fields
     */
    public function getExtensions()
    {
        return $this->errorColumn('extensions');
    }

    /**
     * Returns any data that was retrieved before the error occurred
     *
     * @return array Successful data
     */
    public function getData()
    {
        return $this->successfulData;
    }

    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Returns a column of fields from the Error response
     *
     * @param mixed $key column of values to return
     *
     * @return array column of values
     */
    protected function errorColumn($key)
    {
        return array_column($this->errorData, $key);
    }
}

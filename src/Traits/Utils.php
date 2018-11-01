<?php

namespace GraphQLClient\Traits;

trait Utils
{
    /**
     * Check specific header and value exist for the current response, does a
     * position check on value in case of entries like:
     * `Content-Type: application/json; charset=UTF-8`
     *
     * @param string $valueToCheck header value
     * @param string $headerName   header name
     *
     * @return boolean If header name and value exist
     */
    public function hasHeader($valueToCheck = '', $headerName = 'Content-Type')
    {
        $headerValues = $this->getResponse()->getHeader($headerName);

        foreach ($headerValues as $headerValue) {
            if (strpos($headerValue, $valueToCheck) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * encodes a value/array to JSON.
     *
     * @param mixed $values values being encoded
     *
     * @throws UnexpectedValueException
     *
     * @return string/boolean Returns a JSON encoded string on success or FALSE on failure
     */
    public function encodeJson($values)
    {
        $json = json_encode($values);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON encoding error: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * decode a JSON string to an object or array
     *
     * @param string  $json    string being decoded
     * @param boolean $asArray if True, then convert into associative array
     *
     * @throws UnexpectedValueException
     *
     * @return mixed  Value encoded in json in either object, or array
     */
    public function decodeJson($json, $asArray = true)
    {
        $values = json_decode($json, $asArray);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON decoding error: ' . json_last_error_msg());
        }

        return $values;
    }
}

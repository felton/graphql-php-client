<?php

namespace GraphQLClient\Traits;

trait Utils
{
    protected function hasHeader($valueToCheck = '', $headerName = 'Content-Type')
    {
        $headerValues = $this->getResponse()->getHeader($headerName);

        return in_array($valueToCheck, $headerValues);
    }

    protected function encodeJson($values)
    {
        $json = json_encode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON encoding error: ' . json_last_error_msg());
        }

        return $json;
    }

    protected function decodeJson($json, $asArray = false)
    {
        $values = json_decode($json, $asArray);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON decoding error: ' . json_last_error_msg());
        }

        return $values;
    }
}

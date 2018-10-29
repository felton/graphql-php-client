<?php

namespace GraphQLClient\Traits;

trait Utils
{
    /**
     * [hasHeader description]
     *
     * @param string $valueToCheck [description]
     * @param string $headerName   [description]
     *
     * @return boolean [description]
     */
    protected function hasHeader($valueToCheck = '', $headerName = 'Content-Type')
    {
        $headerValues = $this->getResponse()->getHeader($headerName);

        return in_array($valueToCheck, $headerValues);
    }

    /**
     * [encodeJson description]
     *
     * @param [type] $values [description]
     *
     * @return [type] [description]
     */
    protected function encodeJson($values)
    {
        $json = json_encode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON encoding error: ' . json_last_error_msg());
        }

        return $json;
    }

    /**
     * [decodeJson description]
     *
     * @param [type]  $json    [description]
     * @param boolean $asArray [description]
     *
     * @return [type]  [description]
     */
    protected function decodeJson($json, $asArray = false)
    {
        $values = json_decode($json, $asArray);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('JSON decoding error: ' . json_last_error_msg());
        }

        return $values;
    }
}

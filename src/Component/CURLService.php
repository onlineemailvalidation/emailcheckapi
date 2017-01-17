<?php

namespace EmailCheckBumm\Component;

class CURLService
{
    private static function getDefaultCurl($options = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if(!is_array($options)) {
            $options = array();
        }

        $headers = @$options[CURLOPT_HTTPHEADER];
        if(!is_array($headers)) {
            $headers = array('Accept: application/json');
        } else {
            $headers = array_merge($headers, array('Accept: application/json'));
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        
        curl_setopt_array($ch, $options);

        return $ch;
    }

    private static function buildQuery($query)
    {
        if ($query) {
            $queryString = '';
            if (is_array($query)) {
                $queryString = http_build_query($query);
            } else {
                $query = strval($query);
                $queryString = trim($query);
                $queryString = ltrim($query, '?');
            }

            return $queryString;
        }

        return null;
    }

    private static function execute($ch, CURLResponse &$response)
    {
        $result = curl_exec($ch);
        $success = true;

        if ($result === false || curl_errno($ch) !== 0) {
            $response->setErrorCode(curl_errno($ch));
            $response->setErrorText(curl_error($ch));
            $success = false;
        } else {
            $response->setContent($result);
        }

        $response->setStatusCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $response->setTotalTime(curl_getinfo($ch, CURLINFO_TOTAL_TIME));
        $response->setConnectTime(curl_getinfo($ch, CURLINFO_CONNECT_TIME));

        curl_close($ch);
    }

    public static function post($url, $query = null, $options = array())
    {
        $queryString = self::buildQuery($query);
        $url = trim($url);

        $ch = self::getDefaultCurl($options);

        $response = new CURLResponse();
        $response->setUrl($url);

        if ($queryString) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
            $response->setQuery($queryString);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        self::execute($ch, $response);

        return $response;
    }

    public static function get($url, $query = null, $options = array())
    {
        $queryString = self::buildQuery($query);
        $url = trim($url);

        $ch = self::getDefaultCurl($options);

        $response = new CURLResponse();

        if ($queryString) {
            curl_setopt($ch, CURLOPT_URL, rtrim($url, '?').'?'.$queryString);
            $response->setQuery($queryString);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        $response->setUrl($url);

        curl_setopt($ch, CURLOPT_URL, $url);

        self::execute($ch, $response);

        return $response;
    }
}

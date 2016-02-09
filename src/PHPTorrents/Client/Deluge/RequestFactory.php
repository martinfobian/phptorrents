<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents\Client\Deluge;

use \Amp\Artax\Client,
    \Amp\Artax\Request,
    \Vio\PHPTorrents\Client\Deluge\DelugeException as DelugeException;

class RequestFactory
{
    const TARGET_URI = '%s://%s:%s/json';
    const METHOD_AUTH = 'auth.login';

    private $connectionClient;

    public function __construct(\Vio\PHPTorrents\ClientConnection $connection)
    {
        $this->connectionClient = $connection;
    }
    
    public function finalRequest($method, array $params = array())
    {
        $cookie = $this->authenticate();

        return $this->performRequest($method, $params, array('Cookie' => array($cookie)));
    }

    public function authenticate($final = true)
    {
        $response = $this->performRequest(self::METHOD_AUTH, array($this->
                connectionClient->getPassword()));

        if ($response->hasHeader('Set-Cookie'))
        {
            $cookies = $response->getHeader('Set-Cookie');
            preg_match_all('#_session_id=(.*?);#', $cookies[0], $matches);

            return ($final == true ? (isset($matches[0][0]) ? $matches[0][0] : '') : true);
        }
        else
        {
            if($final == true)
            {
                throw new DelugeException(sprintf('"%s": No cookie string received, authentication failed',
                    self::METHOD_AUTH));
            }
            else
            {
                return false;
            }
        }
    }
    private function performRequest($method, array $params = array(), array $headers =
        array())
    {
        $headers = array_merge(array('Content-Type' => 'application/json; charset=utf-8',
                'Accept-Encoding' => 'gzip, identity'), $headers);

        $request = (new \Amp\Artax\Request)
          ->setUri(sprintf(self::TARGET_URI, $this->
            connectionClient->getProtocol(), $this->connectionClient->getHost(), $this->
            connectionClient->getPort()))->setAllHeaders($headers)->setMethod('POST')->
            setBody(json_encode(array(
            'method' => $method,
            'params' => $params,
            'id' => rand())));
        $response = \Amp\wait((new \Amp\Artax\Client)->request($request));

        if ($response->getStatus() === 200)
        {
            $body = $response->getBody();

            if (json_decode($body, true) !== null)
            {
                $responseBody = json_decode($body, true);
                if (!is_array($responseBody['error']))
                {
                    return $response;
                }
                else
                {
                    throw new DelugeException(sprintf('"%s": Error intercepted during request: #%s %s',
                        $method, $responseBody['error']['code'], $responseBody['error']['message']));
                }
            }
            else
            {
                throw new DelugeException(sprintf('"%s": Invalid response received. Expecting JSON, "%s" given',
                    $method, print_r($response->getBody(), true)));
            }
        }
        else
        {
            throw new DelugeException(sprintf('"%s": Incorrect HTTP status received. Expecting 200, "%s" given. Reason: "%s"',
                $method, $response->getStatus(), $response->getReason()));
        }
    }
}

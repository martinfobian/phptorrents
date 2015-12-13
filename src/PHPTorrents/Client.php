<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents;

class Client
{
    const CLIENT_DELUGE = 'Deluge';

    private $currentClient;
    
    public function __construct($client)
    {
        $this->currentClient = $client;
    }
    public function connect(ClientConnection $connection)
    {
        $classPath = '\\' . implode('\\', array(
            __NAMESPACE__,
            'Client',
            $this->currentClient,
            'ClientAdapter'
        ));
        return new $className($connection);
    }
}

<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 * @git https://github.com/veoweb/PHPTorrents
 */

namespace Vio\PHPTorrents;

class Client 
{
    const CLIENT_DELUGE = 'Deluge';
    const CLIENT_TRANSMISSION = 'Transmission';
    
    private $connection;
    
    public function __construct(ClientConnection $connection)
    {
        $this->connection = $connection;    
    }
    public function build($client)
    {
        $className = '\\Vio\\PHPTorrents\\Client\\' . $client . '\\ClientAdapter';
        return new $className($this->connection);    
    }
}
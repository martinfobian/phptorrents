<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents\Client\Deluge;

use \Vio\PHPTorrents\Torrent as Torrent,
    \Vio\PHPTorrents\TorrentNotFoundException,
    \Vio\PHPTorrents\Client\Deluge\ClientFactory,
    \Vio\PHPTorrents\Client\Deluge\RequestFactory;

class ClientAdapter extends ClientFactory
{
    public function __construct(\Vio\PHPTorrents\ClientConnection $connection)
    {
	$this->client = new RequestFactory($connection);
    }
    public function addTorrent(Torrent $torrent)
    {
        $result = (object) json_decode($this->_addTorrent($torrent), true);
        
        if(!empty($result->result))
        {
            return $this->getTorrent($result->result);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"addTorrent": invalid torrent provided. Expecting valid Torrent instance, "%s" given', print_r($torrent, true)
            ));
        }
    }
    public function getTorrent($hash = null, $exceptional = true)
    {
        $result = (object) json_decode($this->_getTorrents(array($hash)), true);
        $torrentEntry = $result->result[$hash];
        
        if(is_array($torrentEntry) && array_key_exists('hash', $torrentEntry))
        {
            return $this->mapTorrent($torrentEntry);
        }
        else
        {
            if($exceptional)
            {
                throw new TorrentNotFoundException(sprintf(
                    '"getTorrent": expecting valid and existing hash, "%s" given',
                    $hash
                ));
            }
            else
            {
                return false;
            }
        }
    }
    public function torrentExists($hash = null)
    {
        return false !== $this->getTorrent($hash, false);
    }
    public function getTorrents()
    {
        $result = (object) json_decode($this->_getTorrents(), true);
        $torrents = array();
        
        foreach($result->result as $torrent)
        {
            array_push($torrents, $this->mapTorrent($torrent));
        }
        return $torrents;
    }
    public function pauseTorrent(Torrent $torrent)
    {
        $this->_pauseTorrent($torrent);
        return $this->getTorrent($torrent->getHashString());
    }
    public function startTorrent(Torrent $torrent)
    {
        $this->_startTorrent($torrent);
        return $this->getTorrent($torrent->getHashString());
    }
    public function queueTorrent(Torrent $torrent, $target)
    {
        $this->_queueTorrent($torrent, $target);
        return $this->getTorrent($torrent->getHashString());
    }
    public function deleteTorrent(Torrent $torrent, $removeData = false)
    {
        return $this->_deleteTorrent($torrent, $removeData);
    }
}

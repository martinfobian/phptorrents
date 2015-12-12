<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 * @git https://github.com/veoweb/PHPTorrents
 */
 
namespace Vio\PHPTorrents\Client\Deluge;

use \Vio\PHPTorrents\ClientController as ClientController,
    \Vio\PHPTorrents\Torrent as Torrent,
    \Vio\PHPTorrents\Client\Deluge\DelugeException;

abstract class ClientFactory extends ClientController
{
	const METHOD_LIST = 'core.get_torrents_status';
	const METHOD_ADD_TORRENT = 'core.add_torrent_url';
	const METHOD_ADD_MAGNET = 'core.add_torrent_magnet';
	const METHOD_START = 'core.resume_torrent';
	const METHOD_PAUSE = 'core.pause_torrent';
	const METHOD_DELETE = 'core.remove_torrent';
	const METHOD_START_ALL = 'core.resume_all_torrents';
    const METHOD_PAUSE_ALL = 'core.pause_all_torrents';
    
    const METHOD_QUEUE_TOP = 'core.queue_top';
    const METHOD_QUEUE_BOTTOM = 'core.queue_bottom';
    const METHOD_QUEUE_UP = 'core.queue_up';
    const METHOD_QUEUE_DOWN = 'core.queue_down';
    
	public $client;

	public function _getTorrents(array $hashes = array())
	{
		$arguments = array(
			(count($hashes) > 0 ? array('id' => $hashes) : null),
			array(
                'name', 'state', 'files', 'eta', 'hash', 'download_payload_rate', 'status',
                'upload_payload_rate', 'total_wanted', 'total_uploaded', 'total_done', 'error_code'
            )
		);
		
		try
        {
            return $this->client->finalRequest(self::METHOD_LIST, $arguments)->getBody();
        }
        catch(HTTPException $e)
        {
            throw new DelugeException($e->getMessage());
        }
	}
    public function _deleteTorrent(Torrent $torrent, $removeData = false)
    {
		if(!is_null($torrent))
        {
    		$arguments = array(
    			$torrent->getHashString(),
    			$removeData
    		);
            
    		try
            {
                return $this->client->finalRequest(self::METHOD_DELETE, $arguments)->getBody();
            }
            catch(HTTPException $e)
            {
                throw new ClientException($e->getMessage());
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"%s": Valid Torrent instance expected, "%s" given',
                'deleteTorrent', print_r($torrent, true)
            ));
        }
    }
	public function _addTorrent(Torrent $torrent = null)
	{
        if(!is_null($torrent) && method_exist($torrent, 'getAddParams'))
        {
    		$parameters = $torrent->getAddParams();
    		$method = $parameters['type'] == 'magnet' ? self::METHOD_ADD_MAGNET : self::METHOD_ADD_TORRENT;
    		$arguments = array(
    			$parameters['href'],
    			array()
    		);
    		
    		try
            {
                return $this->client->finalRequest($method, $arguments)->getBody();
            }
            catch(HTTPException $e)
            {
                throw new DelugeException($e->getMessage());
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"%s": Valid Torrent instance expected, "%s" given',
                'addTorrent', print_r($torrent, true)
            ));
        }
	}
    public function _startTorrent(Torrent $torrent = null)
    {
        if(!is_null($torrent))
        {
            try
            {
                return $this->client->finalRequest(self::METHOD_START, array(array(
                    $torrent->getHashString()
                )));
            }
            catch(HTTPException $e)
            {
                throw new DelugeException($e->getMessage());
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"%s": Valid Torrent instance expected, "%s" given',
                'startTorrent', print_r($torrent, true)
            ));
        }
    }
    public function _pauseTorrent(Torrent $torrent = null)
    {
        if(!is_null($torrent))
        {
            try
            {
                return $this->client->finalRequest(self::METHOD_PAUSE, array(array(
                    $torrent->getHashString()
                )));
            }
            catch(HTTPException $e)
            {
                throw new DelugeException($e->getMessage());
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"%s": Valid Torrent instance expected, "%s" given',
                'pauseTorrent', print_r($torrent, true)
            ));
        }
    }
    public function _queueTorrent(Torrent $torrent = null, $target)
    {
        $directionMap = array(
            'up' => self::METHOD_QUEUE_UP,
            'down' => self::METHOD_QUEUE_DOWN,
            'top' => self::METHOD_QUEUE_TOP,
            'bottom' => self::METHOD_QUEUE_BOTTOM
        );
        
        if(array_key_exists($target, $directionMap) && !is_null($torrent))
        {
            try
            {
                return $this->client->finalRequest($directionMap[$target], array(array(
                    $torrent->getHashString()
                )));
            }
            catch(HTTPException $e)
            {
                throw new DelugeException($e->getMessage());
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                '"%s": Direction expected, "%s%" given', 
                'queueTorrent', $target
            ));
        }
    }
    public function mapTorrent($torrent)
    {
        $torrent = (object) $torrent;
        $object = $this->buildTorrent($torrent->hash, $this)
            ->setName($torrent->name)
            ->setSize($torrent->total_wanted)
            ->setDownloadSpeed($torrent->download_payload_rate)
            ->setUploadSpeed($torrent->upload_payload_rate)
            ->setStatus($torrent->state)
            ->setBytesUploaded($torrent->total_uploaded)
            ->setBytesDownloaded($torrent->total_done);
            
        foreach($torrent->files as $file)
        {
            $file = (object) $file;
            $object->addFile($this->buildFile($file->path, $file->size));
        }
        return $object;
    }
}
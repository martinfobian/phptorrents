<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents;

abstract class TorrentController
{
    const STATUS_DOWNLOADING = 'downloading';
    const STATUS_SEEDING = 'seeding';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETE = 'complete';

    const TYPE_MAGNET = 'magnet';
    const TYPE_TORRENTFILE = 'torrentFile';

    public $magnetURI = false;
    public $torrentURI = false;
    public $adapter;

    public function setMagnet($magnetURI)
    {
        $this->magnetURI = $magnetURI;

        return $this;
    }
    public function setTorrentUrl($torrentURI)
    {
        $this->torrentURI = $torrentURI;

        return $this;
    }
    public function getAddParams()
    {
        if ($this->magnetURI !== false || $this->torrentURI !== false)
        {
            $type = self::TYPE_TORRENTFILE;
            $href = $this->torrentURI;

            if ($this->magnetURI !== false)
            {
                $type = self::TYPE_MAGNET;
                $href = $this->magnetURI;
            }

            return array('type' => $type, 'href' => $href);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": At least a Torrent object with magnet or torrent URI expected, none given',
                "getAddParams"));
        }
    }
    public function __call($method, $arguments = array())
    {
        $action = $method . 'Torrent';
        if (!is_null($this->adapter) && method_exists($this->adapter, $action))
        {
            $arguments = $arguments;
            array_unshift($arguments, $this);
            return call_user_func_array(array($this->adapter, $action), $arguments);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": No valid method found, "%s" given',
                '__call', $method));
        }
    }
}

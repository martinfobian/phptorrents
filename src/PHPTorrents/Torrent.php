<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents;

use \Vio\PHPTorrents\TorrentController as TorrentController;

class Torrent extends TorrentController
{
    private $hashString = '';
    private $status = '';
    private $downloadSpeed = 0;
    private $uploadSpeed = 0;
    private $percentDone = 0.0;
    private $bytesDownloaded = 0;
    private $bytesUploaded = 0;
    private $seedRatio = 0.0;
    private $size = 0;
    private $name = '';
    private $ETA;
    private $files = array();

    public function __construct($hash = '', $adapter = null)
    {
        if (!empty($hash))
        {
            $this->hashString = $hash;
        }
        if (null !== $adapter)
        {
            $this->adapter = $adapter;
        }
    }
    public function setHashString($hash)
    {
        $this->hashString = $hash;
        return $this;
    }
    public function getHashString()
    {
        return $this->hashString;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setSize($bytes)
    {
        if (is_int($bytes) && $bytes > -1)
        {
            $this->size = $bytes;
            return $this;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": Invalid torrent size provided. Expected int > -1, "%s" given',
                'setSize', $bytes));
        }
    }
    public function getSize()
    {
        return $this->size;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setDownloadSpeed($bytesPerSecond)
    {
        if (is_int($bytesPerSecond) && $bytesPerSecond > -1)
        {
            $this->downloadSpeed = $bytesPerSecond;
            return $this;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": Invalid count of bytes provided. Should be > -1, given %s',
                'setDownloadSpeed', $bytesPerSecond));
        }
    }
    public function getDownloadSpeed()
    {
        return $this->downloadSpeed;
    }
    public function setUploadSpeed($bytesPerSecond)
    {
        if (is_int($bytesPerSecond) && $bytesPerSecond > -1)
        {
            $this->uploadSpeed = $bytesPerSecond;
            return $this;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": Invalid count of bytes provided. Should be > -1, given %s',
                'setUploadSpeed', $bytesPerSecond));
        }
    }
    public function getUploadSpeed()
    {
        return $this->uploadSpeed;
    }
    public function setBytesUploaded($bytes)
    {
        if (is_int($bytes) && $bytes > -1)
        {
            $this->bytesUploaded = $bytes;
            $downloaded = $this->bytesDownloaded;

            $seedRatio = function ()use($downloaded, $bytes)
            {
                $downloaded = ($downloaded === 0) ? 1 : $downloaded;

                if (0 == $downloaded && 0 == $bytes)
                {
                    return 0;
                }

                return (float)number_format(($bytes / $downloaded), 2, '.', '');
            }
            ;

            $this->seedRatio = $seedRatio();
            return $this;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": Invalid count of bytes provided. Should be > -1, given %s',
                'setBytesUploaded', $bytes));
        }
    }
    public function getBytesUploaded()
    {
        return $this->bytesUploaded;
    }
    public function setBytesDownloaded($bytes)
    {
        if (is_int($bytes) && $bytes > -1)
        {
            $this->bytesDownloaded = $bytes;
            $size = $this->size;

            $percentDone = function ()use($size, $bytes)
            {
                $size = ($size === 0) ? 1 : $size;

                if ($size === 0 && $bytes === 0)
                {
                    return 0;
                }
                return (float)number_format(($bytes / $size) * 100, 2, '.', '');
            }
            ;
            $this->percentDone = $percentDone();

            if (100 == $this->percentDone || $this->size == $bytes)
            {
                $this->status = self::STATUS_COMPLETE;
            }
            $this->ETA = (int)number_format(($this->size / (0 == $this->downloadSpeed ? 1 :
                $this->downloadSpeed)) * 100, 2, '.', '');
            return $this;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('"%s": Invalid count of bytes provided. Should be > -1, given %s',
                'setBytesDownloaded', $bytes));
        }
    }
    public function getBytesDownloaded()
    {
        return $this->bytesDownloaded;
    }
    public function getETA()
    {
        return $this->ETA;
    }
    public function addFile(File $file)
    {
        array_push($this->files, $file);
        return $this;
    }
    public function getFiles()
    {
        return $this->files;
    }
    public function isComplete()
    {
        return (($this->status === self::STATUS_COMPLETE) || ($this->percentDone === 100) ||
            ($this->bytesDownloaded === $this->size));
    }
}

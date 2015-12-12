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

class File
{
    private $bytes = 0;
    private $name = null;
    
    public function __construct($name, $bytes)
    {
        if(is_int($bytes) && $bytes > -1)
        {
            $this->name = $name;
            $this->bytes = $bytes;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf(
                'Incorrect size provided. Expecting integer > -1, "%s" given',
                $bytes
            ));
        }
    }
    public function getName()
    {
        return $this->name;
    }
    public function getSize()
    {
        return $this->size;
    }
}
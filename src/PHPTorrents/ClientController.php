<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */

namespace Vio\PHPTorrents;

use \Vio\PHPTorrents\Torrent as Torrent,
    \Vio\PHPTorrents\File as File;
    
abstract class ClientController
{
    public function buildTorrent($hash, $utility = null)
    {
        return new Torrent($hash, $utility);
    }
    public function buildFile($path, $size = 0)
    {
        return new File($path, $size);
    }
}

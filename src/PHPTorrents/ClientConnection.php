<?php

/**
 * @author Jan Willem van Os
 * @email info@jwvanos.com
 * @copyright 2015
 * @package Vio
 * @project TorrentsPHP
 */
 
namespace Vio\PHPTorrents;

class ClientConnection
{
	private $_protocol	= 'http';
	private $_host		= 'localhost';
	private $_port;
	private $_password;
	
	public function __call($method, $arguments)
	{
		$action = substr($method, 0, 3) == 'get' ? 'get' : 'set';
		$property = '_' . strtolower(substr($method, 3));
		
		if(property_exists($this, $property))
		{
			if($action == 'get')
			{
				return $this->{$property};
			}
			else
			{
				$this->{$property} = $arguments[0];
			}
		}
		return $this;
	}
}

# PHPTorrents
Library for communicating with various Torrent Clients in PHP.
Currently only <strong>Deluge</strong> is supported.

<h3>Installation</h3>
Installation via Composer, just add this to your composer.json file:

```json  
{
  "require": {
    "vioweb/phptorrents": "dev-master"
  }
}
```
  
..or a stand-alone installation

```
$ git clone https://github.com/vioweb/phptorrents.git
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

<h3>Usage</h3>

Since the installation will be provided by Composer, always include the `autoload.php` file in your project:

```php
require_once dirname(__FILE__) . '/vendor/autoload.php';
```

Use the `ClientConnection` class to initiate a connection between PHPTorrents, and your torrent client:

```php
use \Vio\PHPTorrents\ClientConnection;

$connection = (new ClientConnection)
  ->setHost('localhost')
  ->setProtocol('http') //edit to 'https' when using SSL
  ->setPort(8112) // default Deluge port
  ->setPassword('deluge'); //default Deluge password
```

Build a 'Client' object to initiate the adapters and classes:

```php
use \Vio\PHPTorrents\Client;

$client = (new Client($connection))
  ->build(Client::CLIENT_DELUGE;
```

Now you will be able to use the available features of PHPTorrents. See the examples below.

<h3>Examples</h3>

<h5>#1 List torrents</h5>

```php
<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use \Vio\PHPTorrents\ClientConnection,
    \Vio\PHPTorrents\Client;
    
$connection = (new ClientConnection)
  ->setHost('localhost')
  ->setProtocol('http') //edit to 'https' when using SSL
  ->setPort(8112) // default Deluge port
  ->setPassword('deluge'); //default Deluge password
  
$client = (new Client($connection))
  ->build(Client::CLIENT_DELUGE;
  
$torrentList = $client->getTorrents();

foreach($torrentList as $torrent)
{
  // See \Vio\PHPTorrents\Torrent.php for available properties
  echo sprintf(
    "Name: %s\n" . 
    "Hash: %s\n" . 
    "Size in bytes: %s . \n\n",
    $torrent->getName(),
    $torrent->getHashString(),
    $torrent->getSize()
  );
}
?>
```

<h5>#2 Adding a new torrent</h5>

```php
<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use \Vio\PHPTorrents\ClientConnection,
    \Vio\PHPTorrents\Client,
    \Vio\PHPTorrents\Torrent;
    
$connection = (new ClientConnection)
  ->setHost('localhost')
  ->setProtocol('http') //edit to 'https' when using SSL
  ->setPort(8112) // default Deluge port
  ->setPassword('deluge'); //default Deluge password
  
$client = (new Client($connection))
  ->build(Client::CLIENT_DELUGE;

/*
 * Adding torrent by Magnet URI
 */
$magnetURI = 'magnet:?xt=urn:btih:1619ecc9373c3639f4ee3e261638f29b33a6cbd6&dn=Ubuntu+14.10+i386+%28Desktop+ISO%29&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Fopen.demonii.com%3A1337&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969&tr=udp%3A%2F%2Fexodus.desync.com%3A6969';

$newTorrent = $client->addTorrent((new Torrent)
  ->setMagnet($magnetURI)
);

print $newTorrent->getHashString();

/*
 * ..or by .torrent file
 */
$torrentURI = 'http://releases.ubuntu.com/15.10/ubuntu-15.10-desktop-amd64.iso.torrent';

$newTorrent = $client->addTorrent((new Torrent)
  ->setTorrentUrl($torrentURI)
);

print $newTorrent->getHashString();

// Recheck?
print $client->torrentExists($newTorrent->getHashString()) == true ? 'Exists' : 'Does not exist';
```

Note: the `Torrent` instance can be used for adding new torrents and processing existing ones. The `get` methods are only available when using `$client->getTorrent($hash)` or when returned from another process. For example: before adding a new torrent via `$client->addTorrent(Torrent $torrent)`, the `get` methods are not available, as the instance was created manually. After adding a torrent via the `addTorrent` function, its return is a generated `Torrent` instance, and therefore `get` methods will be available.

<h5>#3 Using methods on torrent object(s)</h5>

```php
<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use \Vio\PHPTorrents\ClientConnection,
    \Vio\PHPTorrents\Client,
    \Vio\PHPTorrents\Torrent;
    
$connection = (new ClientConnection)
  ->setHost('localhost')
  ->setProtocol('http') //edit to 'https' when using SSL
  ->setPort(8112) // default Deluge port
  ->setPassword('deluge'); //default Deluge password
  
$client = (new Client($connection))
  ->build(Client::CLIENT_DELUGE;

/*
 * Lets request a recently added torrent
 * and apply some methods
 * Note: all torrents are identifier by their hashStrings
 */
$hash = '1619ecc9373c3639f4ee3e261638f29b33a6cbd6';

$torrent = $client->getTorrent($hash);

// Lets start the download
$torrent->start();

// Now, let's pause
$torrent->pause();

// Move the torrent up or down
// or to the top or bottom
$torrent->queue('top');

// Now, lets delete
$removeAllData = false; // when defining true, all the downloaded files will be deleted
$torrent->delete($removeAllData);
```




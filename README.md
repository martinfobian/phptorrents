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
    "Name: %s" . \n
    "Hash: %s" . \n
    "Size in bytes: %s . \n\n",
    $torrent->getName(),
    $torrent->getHashString(),
    $torrent->getSize()
  );
}
?>
```

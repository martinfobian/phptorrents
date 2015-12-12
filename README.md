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
$connection = (new ClientConnection)
  ->setHost('localhost')
  ->setProtocol('http') //edit to 'https' when using SSL
  ->setPort(8112) // default Deluge port
  ->setPassword('deluge'); //default Deluge password
```

Build a 'Client' object to initiate the adapters and classes:

```php
$client = (new Client($connection))
  ->build(Client::CLIENT_DELUGE;
```

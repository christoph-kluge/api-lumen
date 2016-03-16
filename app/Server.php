<?php namespace App;

use Laravel\Lumen\Application;
use React\EventLoop\Factory;
use React\Http\Request;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

/**
 * @package Server.php
 * @author  Christoph Kluge <work@christoph-kluge.eu>
 * @since   08.03.16
 */
class Server
{

  /**
   * @var Application
   */
  protected $app;

  /**
   * @var string
   */
  protected $host;

  /**
   * @var int
   */
  protected $port;

  /**
   * @param Application $app
   * @param string      $host
   * @param int         $port
   * @return Server
   */
  public function __construct(Application $app, $host, $port)
  {
    $this->app  = $app;
    $this->host = $host;
    $this->port = $port;
  }

  /**
   * Running HTTP Server
   */
  public function run()
  {
    $loop = Factory::create();
    $loop->addPeriodicTimer(
      5,
      function () {
        $memory    = memory_get_usage() / 1024;
        $formatted = number_format($memory, 3) . ' K';
        echo "Current memory usage: {$formatted}\n";
      }
    );


    $socket = new SocketServer($loop);
    $http   = new HttpServer($socket, $loop);
    $http->on(
      'request',
      function (Request $request, $response) {
        # some logging for the request
        echo date('Y-m-d\TH:i:s') . ' ' . 'm=' .$request->getMethod() . ', r="' . $request->getPath() . '", q="' . implode('&', $request->getQuery()) . '"' . PHP_EOL;

        # dispatch request to internal request handler
        with(new RequestHandler($this->app, $this->host, $this->port))->handle($request, $response);
      }
    );
    $socket->listen($this->port);

    $loop->run();
  }
}
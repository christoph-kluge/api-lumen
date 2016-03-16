<?php namespace App;

use Laravel\Lumen\Application;
use React\Http\Request;
use React\Http\Response;

/**
 * @package RequestHandler.php
 * @author  Christoph Kluge <work@christoph-kluge.eu>
 * @since   08.03.16
 */
class RequestHandler
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
   * @var string
   */
  protected $request_body;

  /**
   * @var array
   */
  protected $post_params;

  /**
   * @param Application $app
   * @param string      $host binding host
   * @param int         $port binding port
   */
  public function __construct(Application $app, $host, $port)
  {
    $this->app  = $app;
    $this->host = $host;
    $this->port = $port;
  }

  protected function isSecure(array $headers)
  {
    # mostly copied/symplyfied from symfony/request
    $forwarded = null;
    if (isset($headers['X_FORWARDED_PROTO'])) {
      $forwarded = $headers['X_FORWARDED_PROTO'];
    } elseif (isset($headers['HTTPS'])) {
      $forwarded = $headers['HTTPS'];
    }

    if ($forwarded && in_array(strtolower(current(explode(',', $forwarded))), ['https', 'on', 'ssl', '1'])) {
      return true;
    }

    return false;
  }

  protected function getRequestUri(array $headers, $path)
  {
    $protocol = 'http://';
    if ($this->isSecure($headers)) {
      $protocol = 'https://';
    }

    $http_host = $protocol . $this->host;
    if (isset($headers['Host'])) {
      $http_host = $protocol . $headers['Host'];
    }
    return $http_host . $path;
  }

  protected function handleRequest(Request $request, Response $response)
  {
    # initialize params for illuminate/request
    $uri     = $this->getRequestUri($request->getHeaders(), $request->getPath());
    $method  = $request->getMethod();
    $params  = array_merge($request->getQuery(), $this->post_params);
    $cookies = [];
    $files   = [];
    $server  = [];
    $content = $this->request_body;

    $httpRequest = \Illuminate\Http\Request::create($uri, $method, $params, $cookies, $files, $server, $content);

    try {
      # dispatch request agaianst illuminate/application
      $httpResponse = $this->app->dispatch($httpRequest);

      # modify illuminate/request headers into react/request headers
      $headers = [];
      $header  = explode("\r\n", (string)$httpResponse->headers);
      foreach ($header as $line) {
        if (empty($line) || strpos($line, ':') === false) {
          continue;
        }
        list($name, $value) = explode(':', $line, 2);
        $headers[trim($name)] = trim($value);
      }

      # write response to socket connection
      $response->writeHead($httpResponse->getStatusCode(), $headers);
      $response->end($httpResponse->getContent());

    } catch (\Exception $e) {
      $response->writeHead(500);
      $response->end($e->getMessage());
    }
  }

  public function handle(Request $request, Response $response)
  {
    $this->post_params = [];

    $request->on(
      'data',
      function ($body) use ($request, $response) {
        $this->request_body = $body;
        parse_str($body, $this->post_params);

        $this->handleRequest($request, $response);
      }
    );
  }
}
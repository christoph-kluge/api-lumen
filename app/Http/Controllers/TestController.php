<?php namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;

/**
 * Short Description
 * @package TestController.php
 * @author  Christoph Kluge <work@christoph-kluge.eu>
 * @since   06.03.16
 */
class TestController extends Controller
{

  use Helpers;

  public function index()
  {
    $data = [
      'someKey'    => 'value1',
      'anotherKey' => 'another_value',
    ];
    return json_encode($data);
//    return $this->response->array($data);
  }
}
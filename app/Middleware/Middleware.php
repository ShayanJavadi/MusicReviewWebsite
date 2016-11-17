<?php

namespace SMA\Middleware;

class Middleware
{
  protected $container;

  public function __construct($container)
  {
    //grab container
    $this->container = $container;
  }
}

 ?>

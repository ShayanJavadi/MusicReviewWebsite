<?php

namespace SMA\Middleware;

class CsrfViewMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    //this helps us not have to make a token for every form and just call this instead
    $this->container->view->getEnvironment()->addGlobal('csrf', [
      'field' => '
      <input type="hidden" name="'. $this->container->csrf->getTokenNameKey().'" value="'. $this->container->csrf->getTokenName() . '">
      <input type="hidden" name="'. $this->container->csrf->getTokenValueKey().'" value="'. $this->container->csrf->getTokenValue() . '">

      ',

    ]);

    //go to next middleware, standard for all middleware
    $response = $next($request, $response);
    return $response;

  }
}
 ?>

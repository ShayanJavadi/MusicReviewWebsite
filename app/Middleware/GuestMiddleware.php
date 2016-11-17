<?php

namespace SMA\Middleware;

class GuestMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    if ($this->container->auth->isLoggedIn()) {
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //go to next middleware, standard for all middleware
    $response = $next($request, $response);
    return $response;

  }
}
 ?>

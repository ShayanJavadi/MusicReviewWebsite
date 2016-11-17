<?php

namespace SMA\Middleware;

class AuthMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    //check if use is not signed in
    if (!$this->container->auth->isLoggedIn()) {
    return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //flash a message
    //redirect

    //go to next middleware, standard for all middleware
    $response = $next($request, $response);
    return $response;

  }
}
 ?>

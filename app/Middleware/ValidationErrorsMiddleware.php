<?php

namespace SMA\Middleware;

class ValidationErrorsMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    //make errors global so we can output them anywhere
    if (isset($_SESSION['errors'])) {
      $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
    };

    //go to next middleware, standard for all middleware
    $response = $next($request, $response);
    return $response;

  }
}

 ?>

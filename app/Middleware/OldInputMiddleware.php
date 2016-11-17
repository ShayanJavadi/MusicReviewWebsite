<?php

namespace SMA\Middleware;

class OldInputMiddleware extends Middleware
{
  public function __invoke($request, $response, $next)
  {
    //make submitted forms stay if there are errors
    $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
    //we set this request second since we will have it only after the first time around
    $_SESSION['old'] = $request->getParams();

    //go to next middleware, standard for all middleware
    $response = $next($request, $response);
    return $response;

  }
}

 ?>

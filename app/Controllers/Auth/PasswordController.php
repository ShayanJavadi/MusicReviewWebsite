<?php

namespace SMA\Controllers\Auth;

use SMA\Models\User;
use SMA\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
  public function getChangePassword($request, $response)
  {
    //redner the page
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\changepassword.twig');
  }

  public function postChangePassword($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'passwordOld' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->user()->password),
      'password' => v::noWhitespace()->notEmpty(),
    ]);
    if ($validation->failed()) {
      //redirect if failed with message
      $this->flash->addMessage('error', 'Could not change password');
      return $response->withRedirect($this->router->pathfor('auth.changepassword'));
    }

    $this->auth->user()->setPassword($request->getParam('password'));

    $this->flash->addMessage('info', 'Your password has been changed');
    return $response->withRedirect($this->router->pathfor('home'));
  }

}


 ?>

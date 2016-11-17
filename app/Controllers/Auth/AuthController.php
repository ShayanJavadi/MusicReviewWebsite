<?php

namespace SMA\Controllers\Auth;

use SMA\Models\User;
use SMA\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
  public function getSignOut($request, $response)
  {
    //unset cookies
    $this->auth->signOut();
    //redirect back to home with message
    $this->flash->addMessage('info', 'You have been signed out');
    return $response->withRedirect($this->router->pathFor('home'));
  }
  //renders the signup page
  public function getSignUp($request, $response)
  {
    // var_dump($request->getAttribute('csrf_value'));
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\signup.twig');
  }

  //validates form and signs up if validated
  public function postSignUp($request, $response)
  {

    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
      'name' => v::notEmpty()->alpha(),
      'password' => v::noWhitespace()->notEmpty(),
    ]);
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('auth.signup'));
    }
    //calls create on User with params from the form
    $user = User::create([
      'email' => $request->getParam('email'),
      'name' => $request->getParam('name'),
      'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),

    ]);

    $this->auth->attempt($user->email, $request->getParam('password'));

    //redirect back to home
    $this->flash->addMessage('info', 'You have been signed up');
    return $response->withRedirect($this->router->pathFor('home'));
  }

  //render sign in page
  public function getSignIn($request, $response)
  {
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\signin.twig');
  }


  public function postSignIn($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'email' => v::noWhitespace()->notEmpty(),
      'password' => v::noWhitespace()->notEmpty(),
    ]);
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('auth.signin'));
    }

    //pass auth the submited values
    $auth = $this->auth->attempt(
    $request->getParam('email'),
    $request->getParam('password')
    );

    if (!$auth) {
      //redirect back if there are problems
      $this->flash->addMessage('error', 'Could not sign in with those details');
      return $response->withRedirect($this->router->pathFor('auth.signin'));
    }

    //redirect to home with message
    $this->flash->addMessage('info', 'You have been signed in');
    return $response->withRedirect($this->router->pathFor('home'));

  }
}


 ?>

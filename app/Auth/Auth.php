<?php

namespace SMA\Auth;

use SMA\Models\User;

//handles authenticating /signing in/out
class Auth
{
  public function user()
  {
    if (isset($_SESSION['user'])) {
      //grab the user
      return User::find($_SESSION['user']);
    }
    
  }
  public function isLoggedIn()
  {
    //check to see if user is logged in
    return isset($_SESSION['user']);
  }
  public function attempt($email, $password)
  {
    //grab the user by email
    $user = User::where('email', $email)->first();
    //if !user doesn't exist return false
    if (!$user) {
      return false;
    }
    //verify the password for the user
    if (password_verify($password, $user->password)) {
      //set into a session
      $_SESSION['user'] = $user->id;
      return true;
    }
    //failed attempt
    return false;
  }

  public function signOut()
  {
    unset($_SESSION['user']);
  }
}


 ?>

<?php

namespace SMA\Validation\Rules;

use SMA\Models\User;
use Respect\Validation\Rules\AbstractRule;

//make our custom rules using the AbstractRule class
class matchesPassword extends AbstractRule
{
  protected $password;

  public function __construct($password)
  {
    $this->password = $password;
  }

  public function validate($input)
  {
    //if 1 then already taken
    return password_verify($input, $this->password);
  }
}


 ?>

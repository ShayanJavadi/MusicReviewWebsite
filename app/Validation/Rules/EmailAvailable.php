<?php

namespace SMA\Validation\Rules;

use SMA\Models\User;
use Respect\Validation\Rules\AbstractRule;

//make our custom rules using the AbstractRule class
class EmailAvailable extends AbstractRule
{
  public function validate($input)
  {
    //if 1 then already taken
    return User::where('email', $input)->count() === 0;
  }
}


 ?>

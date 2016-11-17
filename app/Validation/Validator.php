<?php

namespace SMA\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 *
 */
class Validator
{
  protected $errors;
  public function validate($request, $rules )
  {
    unset($_SESSION['errors']);
    foreach ($rules as $field => $rule) {
      try {
        //uppercases the first letter of the field so it's user friendly
        $rule->setName(ucfirst($field))->assert($request->getParam($field));
      } catch (NestedValidationException $e) {
        //match errors to field
        $this->errors[$field] = $e->getMessages();
      }
    }
    $_SESSION['errors'] = $this->errors;

    //return this so we can chain
    return $this;
  }

  public function failed()
  {
    //checks to see if errors are empty or not
    return !empty($this->errors);
  }
}

 ?>

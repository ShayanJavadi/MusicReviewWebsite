<?php

namespace SMA\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

//must be class name + exception so respect can find it
class ArtistAvailableException extends ValidationException
{
  //our excpetion that will be passeed
  public static $defaultTemplates = [
    self::MODE_DEFAULT => [
      self::STANDARD => 'Artist already registered.'
    ],
  ];
}

 ?>

<?php

namespace SMA\Validation\Rules;

use SMA\Models\Artist;
use Respect\Validation\Rules\AbstractRule;

//make our custom rules using the AbstractRule class
class ArtistAvailable extends AbstractRule
{
  public function validate($input)
  {
    //if 1 then already taken
    return Artist::where('artist_name', $input)->count() === 0;
  }
}


 ?>

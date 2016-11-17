<?php

namespace SMA\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
  protected $fillable = [
    'artist_name',
    'description',
    'artist_picture'
  ];
}


 ?>

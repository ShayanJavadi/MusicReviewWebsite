<?php

namespace SMA\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
  protected $fillable = [
    'album_name',
    'release_date',
    'genre',
    'rating',
    'album_art',
    'artist_id',
  ];
}


 ?>

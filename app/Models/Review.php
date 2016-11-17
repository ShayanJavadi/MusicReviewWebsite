<?php

namespace SMA\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  protected $fillable = [
    'text',
    'review_artist_id',
    'review_album_id',
    'rating',
    'genre',
    'spotify_iframe',
  ];
}


 ?>

<?php

namespace SMA\Models;

use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
  protected $fillable = [
    'room_name',
    'room_password',
    'room_owner',
    'clients',
    'chat_log',
  ];

  public function setPassword($password)
  {
    $this->update([
      'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
  }
}


 ?>

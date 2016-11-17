<?php

namespace SMA\Controllers\Album;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;

class AlbumController extends Controller
{
  public function index($request, $response)
  {

    // $user = User::where('email', 'shayan@gmail.com')->first();
    // var_dump($user->email);
    // die();

    return $this->view->render($response, 'home.twig');
  }

}


 ?>

<?php

namespace SMA\Controllers;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;

class HomeController extends Controller
{
  public function index($request, $response)
  {
    //grab the three recent reviews
    $latestReviews = Review::orderBy('created_at', 'DESC')->take(7)->get();
    //get the albums and artists from the reviews
    foreach ($latestReviews as $review) {
      $albums[] = Album::where('id', $review['review_album_id'])->get();
      $artists[] = Artist::where('id', $review['review_artist_id'])->get();
    }

    //This is a pretty spaghetti/hackey solution that needs to be replaced later
    //get new covers
    for ($i=0; $i < 7; $i++) {
      //get associated artists names, album names , and album arts
      $newReviewArts[] = $albums[$i][0]['album_art'];
      $newAlbumNames[] = $albums[$i][0]['album_name'];
      $newArtists[] = $artists[$i][0]['artist_name'];
      $newAlbumReleaseDates[] =  $albums[$i][0]['release_date'];
    }
    //make them global
    $this->container->view->getEnvironment()->addGlobal('newReviewArts', $newReviewArts);
    $this->container->view->getEnvironment()->addGlobal('newAlbumNames', $newAlbumNames);
    $this->container->view->getEnvironment()->addGlobal('newArtists', $newArtists);


    return $this->view->render($response, 'home.twig');
  }

  public function getContact($request, $response)
  {

    // $user = User::where('email', 'shayan@gmail.com')->first();
    // var_dump($user->email);
    // die();

    return $this->view->render($response, 'contact.twig');
  }
}


 ?>

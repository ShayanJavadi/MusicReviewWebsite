<?php

namespace SMA\Controllers\Search;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;

class SearchController extends Controller
{

  public function getSearch($request, $response)
  {
    //search types that are allowed
    $searchTypes = array('Artist', 'Album', 'Review');
    //check get values to make sure they are the right type or are not empty
    if ((!in_array($_GET['searchType'], $searchTypes) || $_GET['searchInput'] == null) ) {

      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    if ($_GET['searchType'] == "Artist") {
      //get artist with requested name
      $artist = Artist::where('artist_name', ucfirst($_GET['searchInput']))->get();
      if (count($artist) != 0 ) {
        //if artist is not empty take user to artist page
        return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=". $_GET['searchInput']);
      }
      $this->flash->addMessage('error', 'Artist "' . $_GET['searchInput']. '"' .  ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }

    if ($_GET['searchType'] == "Album") {
      //get the album
      $album = Album::where('album_name', ucfirst($_GET['searchInput']))->first();
      //get the artist that owns the album
      $artist = Artist::where('id', ucfirst($album->artist_id))->first();
      if (count($artist) != 0 ) {
        //if artist is not empty take user to artist page
        return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=". $artist->artist_name);
      }
      $this->flash->addMessage('error', 'Album "' . $_GET['searchInput']. '"' . ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }

    if ($_GET['searchType'] == "Review") {
      //get the album
      $album = Album::where('album_name', ucfirst($_GET['searchInput']))->first();
      //get the artist that owns the album
      $artist = Artist::where('id', ucfirst($album->artist_id))->first();
      if (count($artist) != 0 ) {
        //if artist is not empty take user to artist page
        return $response->withRedirect($this->router->pathfor("review") . "?artist=". $artist->artist_name . "&album=" . $album->album_name);
      }
      $this->flash->addMessage('error', 'Review for the album "' . $_GET['searchInput']. '"' . ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //return $this->view->render($response, 'home.twig');
  }

  public function postSearch($request, $response)
  {

    //grab search input + search type

    return $this->view->render($response, 'home.twig');
  }

}


 ?>

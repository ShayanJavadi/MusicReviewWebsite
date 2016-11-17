<?php

namespace SMA\Controllers\Artist;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;

class ArtistController extends Controller
{
  public function getArtist($request, $response)
  {
    //get the artist
    $requestedArtist = Artist::where('artist_name', $_GET['artist'])->first();
    //if nothing returns, go back to home
    if ($requestedArtist == NULL) {
      $this->flash->addMessage('error', 'Artist ' . $_GET['artist']. ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //grab the albums by the artist
    $requestedArtistAlbums  = Album::where('artist_id', $requestedArtist['id'])->get();
    // var_dump($requestedArtist['artist_name']); alcest
    // var_dump($requestedArtistAlbums[0]['album_name']);shelter
    //put all albums in an array
    foreach ($requestedArtistAlbums as $album) {
      $ArtistAlbums[] = $album['attributes'];
    }
    //sort them
    // var_dump(sort$ArtistAlbums[1]['attributes']);
    // die();

    foreach ($ArtistAlbums as $Album) {
      $releaseDates[] = $Album['release_date'];
    }

    array_multisort($releaseDates, SORT_ASC, $ArtistAlbums);

    // var_dump($ArtistAlbums);
    // die();
    //make it global
    $this->container->view->getEnvironment()->addGlobal('Artist', $requestedArtist);
    $this->container->view->getEnvironment()->addGlobal('ArtistAlbums', $ArtistAlbums);
    //render page
    return $this->view->render($response, 'templates/artist.twig');
  }

}

 ?>

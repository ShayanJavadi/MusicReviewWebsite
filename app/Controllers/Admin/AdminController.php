<?php


namespace SMA\Controllers\Admin;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;
use Respect\Validation\Validator as v;

class AdminController extends Controller
{
  public function getNewArtist($request, $response)
  {
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\admin\add-artist.twig');
  }
  public function postNewArtist($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'artist_name' => v::notEmpty()->artistAvailable(),
      'description' => v::notEmpty(),
    ]);
    //check if image was uploaded
    if (!file_exists($_FILES['artist_picture']['tmp_name']) || !is_uploaded_file($_FILES['artist_picture']['tmp_name'])) {
      $_SESSION['errors'] += ['artist_picture' => ['No image was uploaded.']];
      return $response->withRedirect($this->container->router->pathFor('newAlbum'));
    }


    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('newArtist'));
    }
    $image = $_FILES['artist_picture'];
    //calls create on User with params from the form
    $artist = Artist::create([
      'artist_name' => $request->getParam('artist_name'),
      'description' => $request->getParam('description'),
    ]);
    //image location
    $artistPictureLocation = "images/" . $artist->id . "/";
    //gotta do this after creating since we're using id and it wont be created if we do it in the create query
    $artist->artist_picture = $artistPictureLocation . $artist->id . ".jpg";
    $artist->save();
    //create directory if it is not created already
    if(!is_dir($artistPictureLocation)) {
      mkdir($artistPictureLocation,  0777, true);
    }
    //move file to directory
    move_uploaded_file($image['tmp_name'], $artistPictureLocation . $artist->id . ".jpg");


    //take user to the chat room
    $this->flash->addMessage('info', 'Artist created successfully. ');
    return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=". $request->getParam('artist_name') );
  }

  public function getDeleteArtist($request, $response)
  {
    //<a href="{{ path_for('chat.delete-room')}}?room={{ chatrooms }}">
    //grab the chatroom that is being requested to be deleted
    if (!(isset($_GET['artist']))) {
      $this->flash->addMessage('error', 'No artist was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    $artist = Artist::where('artist_name', $_GET['artist'])->first();

    if ($artist == NULL) {
      $this->flash->addMessage('error', 'Artist does not exist.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }

    $artist->delete();
    unlink("images/" . $artist->id . "/" . $artist->id . ".jpg");
    //add message and redirect back to the profile
    $this->flash->addMessage('info', 'Artist deleted successfully. ');
    return $response->withRedirect($this->container->router->pathFor('home'));
  }

  public function getUpdateArtist($request, $response)
  {
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
      $ArtistAlbums[] = $album;
    }
    //sort them

    //make it global
    $this->container->view->getEnvironment()->addGlobal('Artist', $requestedArtist);
    $this->container->view->getEnvironment()->addGlobal('ArtistAlbums', $ArtistAlbums);

    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\admin\update-artist.twig');
  }
  public function postUpdateArtist($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'artist_name' => v::notEmpty(),
      'description' => v::notEmpty(),
    ]);
    //make sure image is selected
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('updateArtist') . "?artist=". $request->getParam('artist_name'));
    }

    //you can't change the value that we use to get the artist so
    //I passed id through a hidden input since it wont be changing
    $artist = Artist::where('id', $request->getParam('id'))->first();

    $artist->artist_name = $request->getParam('artist_name');
    $artist->description = $request->getParam('description');
    $artist->save();
    //if a file is uploaded then change picture to it
    //else keep the old image
    if (file_exists($_FILES['artist_picture']['tmp_name']) || is_uploaded_file($_FILES['artist_picture']['tmp_name'])) {
      $artistPictureLocation = "images/" . $artist->id . "/" . $artist->id . ".jpg";
      $artist->artist_picture = $artistPictureLocation;
      $artist->save();
      //delete previous picture
      if(file_exists($artistPictureLocation)) {
        unlink($artistPictureLocation);
      }
      //add new
      $image = $_FILES['artist_picture'];
      move_uploaded_file($image['tmp_name'], $artistPictureLocation);
      //take user to the chat room
      $this->flash->addMessage('info', 'Artist updated successfully. ');
      return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=". $request->getParam('artist_name'));
    }
    //redriect back to artist
    $this->flash->addMessage('info', 'Artist updated successfully. ');
    return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=". $request->getParam('artist_name'));
  }

  public function getNewAlbum($request, $response)
  {
    $artists = Artist::select('artist_name', 'id')->orderBy('artist_name')->get();

    unset($_SESSION['errors']);
    $this->container->view->getEnvironment()->addGlobal('Artists', $artists);
    return $this->view->render($response, 'templates\admin\add-album.twig');
  }

  public function postNewAlbum($request, $response)
  {

    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'album_name' => v::notEmpty(),
      'genre' => v::notEmpty(),
      // 'album_art' => v::image(),
    ]);

    //make sure an image is uploaded
    if (!file_exists($_FILES['album_art']['tmp_name']) || !is_uploaded_file($_FILES['album_art']['tmp_name'])) {
      $_SESSION['errors'] += ['album_art' => ['No image was uploaded.']];
      return $response->withRedirect($this->container->router->pathFor('newAlbum'));
    }
    $image = $_FILES['album_art'];

    list($width, $height) = getimagesize($image['tmp_name']);
    //make sure the image is a square
    if ($width/$height != 1) {
      $_SESSION['errors'] += ['album_art' => ['Image must be a square (1200x1200, 800x800... etc).']];
      return $response->withRedirect($this->container->router->pathFor('newAlbum'));
    }
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('newAlbum'));
    }



    $artistAlbums = Album::where('artist_id', $request->getParam('id'))->get();
    //check if the requeted album already exists or not
    foreach ($artistAlbums as $album) {
      if (strtolower($album['album_name']) == strtolower($request->getParam('album_name'))) {
        //return back if it does
        $this->flash->addMessage('error', 'Album already exists. ');
        return $response->withRedirect($this->router->pathfor('newAlbum'));
      }
    }
    //get artist
    $artist = Artist::where('id', $request->getParam('id'))->first();

    //make only the first letter uppercase
    $album_name = ucwords(strtolower(($request->getParam('album_name'))));
    //varible for image destination
    $albumArtLocation = "images/" . $artist->id . "/covers/";
    //calls create on User with params from the form
    $album = Album::create([
      'album_name' => $album_name,
      'release_date' => $request->getParam('release_date'),
      'genre' => $request->getParam('genre'),
      'rating' => $request->getParam('rating'),
      'album_art' => $albumArtLocation . $album_name . ".jpg",
      'artist_id' => $request->getParam('id'),
    ]);

    //create directory if it is not created already
    if(!is_dir($albumArtLocation)) {
      mkdir($albumArtLocation,  0777, true);
    }
    //move file to directory
    move_uploaded_file($image['tmp_name'], $albumArtLocation . $album->id . ".jpg");
    //redirect to the artist page that owns the album
    $this->flash->addMessage('info', 'Album created successfully. ');
    return $response->withRedirect($this->router->pathfor("artist.artist") . "?artist=" . $artist->artist_name);
  }

  public function getDeleteAlbum($request, $response)
  {
    //check if get values are set to prevent unauth use
    if (!(isset($_GET['artist']))) {
      $this->flash->addMessage('error', 'No artist was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    if (!(isset($_GET['album']))) {
      $this->flash->addMessage('error', 'No album was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //get the artist
    $artist = Artist::where('artist_name', $_GET['artist'])->first();
    //get the album that matches with the artist and the requeted name
    $album = Album::where([
      ['album_name', '=', $_GET['album']],
      ['artist_id', '=', $artist['id']],
      ])->first();
    //check if get values are set to prevent unauth use
    if ($artist == NULL || $album == NULL) {
      $this->flash->addMessage('error', 'Artist or album does not exist.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //delete the album
    $album->delete();
    //delete the cover
    unlink("images/" . $artist->id . "/covers/" . $album->id . ".jpg");
    //add message and redirect back to the update page
    $this->flash->addMessage('info', 'Album deleted successfully. ');
    return $response->withRedirect($this->container->router->pathfor("updateArtist") . "?artist=" . $_GET['artist']);
  }

  public function getNewReview($request, $response)
  {

    //get albums that haven't been reviewed
    $albums = Album::where('is_reviewed', NULL)->orderBy('album_name')->get();
    unset($_SESSION['errors']);
    // $this->container->view->getEnvironment()->addGlobal('Artists', $artists);
    $this->container->view->getEnvironment()->addGlobal('Albums', $albums);
    return $this->view->render($response, 'templates\admin\add-review.twig');
  }

  public function postNewReview($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'text' => v::notEmpty(),
      'spotify_iframe' => v::notEmpty(),
    ]);
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('newReview'));
    }
    //extract the spotify url from the embedded code
    $spotify_iframe = $request->getParam('spotify_iframe');
    preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $spotify_iframe, $spotify_iframe);
    $spotify_iframe = array_shift( $spotify_iframe );
    //grab the requested album and it's values
    $album = Album::where('id', $request->getParam('id'))->first();
    //updating album rating if review rating doesn't match it
    if ($album->rating != $request->getParam('rating')) {
      $album->rating = $request->getParam('rating');
      $album->save();
    }
    //create new review
    $review = Review::create([
      'text' => $request->getParam('text'),
      'review_artist_id' => $album->artist_id,
      'review_album_id' => $request->getParam('id'),
      'rating' => $request->getParam('rating'),
      'genre' => $album->genre,
      'spotify_iframe' => $spotify_iframe[0],
    ]);
    //set album to reviewed
    $album->is_reviewed = 1;
    $album->save();
    //grab artist for url
    $artist = Artist::where('id', $album->artist_id)->first();
    //take user to review
    $this->flash->addMessage('info', 'Review created successfully. ');
    return $response->withRedirect($this->router->pathfor("review") . "?artist=". $artist->artist_name . "&album=" . $album->album_name);
  }

  public function getUpdateReview($request, $response)
  {
    if (!(isset($_GET['artist']))) {
      $this->flash->addMessage('error', 'No artist was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    if (!(isset($_GET['album']))) {
      $this->flash->addMessage('error', 'No album was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }

    $Artist = Artist::where('artist_name', $_GET['artist'])->first();
    $Album = Album::where('album_name', $_GET['album'])->first();
    $Review = Review::where('review_album_id', $Album->id)->first();


    //if nothing returns, go back to home
    if ($Review == NULL) {
      $this->flash->addMessage('error', 'Review for ' . $_GET['artist'] . '-' . $_GET['album'] . ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //break the review text into paragraphs
    $reviewText = (explode("\\n", $Review->text));

    $this->container->view->getEnvironment()->addGlobal('Artist', $Artist);
    $this->container->view->getEnvironment()->addGlobal('Album', $Album);
    $this->container->view->getEnvironment()->addGlobal('Review', $Review);
    $this->container->view->getEnvironment()->addGlobal('ReviewText', $reviewText);
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\admin\update-review.twig');
  }

  public function postUpdateReview($request, $response)
  {

    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'spotify_iframe' => v::notEmpty(),
      'text' => v::notEmpty()
    ]);
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('updateReview') . "?artist=". $request->getParam('artist_name') . "&album=" . $request->getParam('album_name'));
    }

    //you can't change the value that we use to get the artist so
    //I passed id through a hidden input since it wont be changing
    $review = Review::where('spotify_iframe', $request->getParam('spotify_iframe'))->first();

    $review->rating = $request->getParam('rating');
    $review->spotify_iframe = $request->getParam('spotify_iframe');
    $review->text = $request->getParam('text');
    $review->save();
    //take user to the chat room
    $this->flash->addMessage('info', 'Review updated successfully. ');
    return $response->withRedirect($this->router->pathfor('review') . "?artist=". $request->getParam('artist_name') . "&album=" . $request->getParam('album_name'));
  }

  public function getDeleteReview($request, $response)
  {
    //check if get values are set to prevent unauth use
    if (!(isset($_GET['artist']))) {
      $this->flash->addMessage('error', 'No artist was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    if (!(isset($_GET['album']))) {
      $this->flash->addMessage('error', 'No album was selected.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    $artist = Artist::where('artist_name', $_GET['artist'])->first();
    //get the album
    $album = Album::where('artist_id', $artist->id )->first();
    //get the review that matches with the album
    $review = Review::where('review_album_id', $album->id)->first();

    //check if get values are set to prevent unauth use
    if ($review == null) {
      $this->flash->addMessage('error', 'Review does not exist.');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }

    $album->is_reviewed = null;
    $album->save();
    //delete the album
    $review->delete();
    //add message and redirect back to the update page
    $this->flash->addMessage('info', 'Review deleted successfully. ');
    return $response->withRedirect($this->container->router->pathfor("artist.artist") . "?artist=" . $_GET['artist']);
  }

}


 ?>

<?php
use SMA\Middleware\AuthMiddleware;
use SMA\Middleware\GuestMiddleware;
//setName gives a name so we can point back to it somewhere else
//route for homepage
$app->get('/', 'HomeController:index')->setName('home');
//route for artist
$app->get('/artist', 'ArtistController:getArtist')->setName('artist.artist');
//route for album
$app->get('/album', 'AlbumController:getAlbum')->setName('album');
//route for review
$app->get('/review', 'ReviewController:getReview')->setName('review');
//route for reviews
$app->get('/reviews', 'ReviewController:getReviews')->setName('reviews');
//route for contact
$app->get('/contact', 'HomeController:getContact')->setName('contact');
//sign in routes
$app->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
$app->post('/signin', 'AuthController:postSignIn');
//search routes
$app->get('/search', 'SearchController:getSearch')->setName('search');
// $app->post('/signin', 'AuthController:postSignIn');




//sign up routes
// $app->get('/signup', 'AuthController:getSignUp')->setName('auth.signup');
// $app->post('/signup', 'AuthController:postSignUp');

$app->group('', function(){
  //admin urls
  //add artist
  $this->get('/add-artist', 'AdminController:getNewArtist')->setName('newArtist');
  $this->post('/add-artist', 'AdminController:postNewArtist');
  //update artist
  $this->get('/update-artist', 'AdminController:getUpdateArtist')->setName('updateArtist');
  $this->post('/update-artist', 'AdminController:postUpdateArtist');
  //delete artist
  $this->get('/delete-artist', 'AdminController:getDeleteArtist')->setName('deleteArtist');

  //add album
  $this->get('/add-album', 'AdminController:getNewAlbum')->setName('newAlbum');
  $this->post('/add-album', 'AdminController:postNewAlbum');
  // //update album
  // $this->post('/update-artist', 'AdminController:getUpdateArtist')->setName('updateAlbum');
  //delete album
  $this->get('/delete-album', 'AdminController:getDeleteAlbum')->setName('deleteAlbum');
  //post review
  $this->get('/add-review', 'AdminController:getNewReview')->setName('newReview');
  $this->post('/add-review', 'AdminController:postNewReview');
  //update review
  $this->get('/update-review', 'AdminController:getUpdateReview')->setName('updateReview');
  $this->post('/update-review', 'AdminController:postUpdateReview');
  //delete review
  $this->get('/delete-review', 'AdminController:getDeleteReview')->setName('deleteReview');
})->add(new AuthMiddleware($container));
 ?>

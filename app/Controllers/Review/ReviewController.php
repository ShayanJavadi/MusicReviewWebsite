<?php

namespace SMA\Controllers\Review;

use SMA\Models\Review;
use SMA\Models\Artist;
use SMA\Models\Album;
use SMA\Controllers\Controller;
use Slim\views\Twig as View;

class ReviewController extends Controller
{
  public function getReview($request, $response)
  {
    //grab album
    $requestedAlbum = Album::where('album_name', $_GET['album'])->first();
    //grab artist
    $requestedArtist = Artist::where('id', $requestedAlbum->artist_id)->first();
    //protected the get link from showing the review with different artist info
    if ($requestedArtist->artist_name != $_GET['artist']) {
      $this->flash->addMessage('error', 'Review for the album ' . $_GET['album']. ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    //grab review
    $requestedReview = Review::where('review_album_id', $requestedAlbum['id'])->first();
    //throw error if review does not exist
    if ($requestedReview == NULL) {
      $this->flash->addMessage('error', 'Review for the album ' . $_GET['album']. ' does not exist');
      return $response->withRedirect($this->container->router->pathFor('home'));
    }
    $reviewText = (explode("\\n", $requestedReview['text']));



    //make them global
    $this->container->view->getEnvironment()->addGlobal('Artist', $requestedArtist);
    $this->container->view->getEnvironment()->addGlobal('Album', $requestedAlbum);
    $this->container->view->getEnvironment()->addGlobal('Review', $requestedReview);
    $this->container->view->getEnvironment()->addGlobal('ReviewText', $reviewText);

    return $this->view->render($response, 'templates/review.twig');
  }

  public function getReviews($request, $response)
  {
    //grab the three recent reviews
    $latestReviews = Review::orderBy('created_at', 'DESC')->take(10)->get();
    //get the albums and artists from the reviews

    foreach ($latestReviews as $review) {
      $albums[] = Album::where('id', $review['review_album_id'])->get();
      $artists[] = Artist::where('id', $review['review_artist_id'])->get();
    }


    //get new covers
    for ($i=0; $i < count($latestReviews); $i++) {
      //get associated artists names, album names , and album arts
      $newReviewArts[] = $albums[$i][0]['album_art'];
      $newAlbumNames[] = $albums[$i][0]['album_name'];
      $newArtists[] = $artists[$i][0]['artist_name'];
      $reviewSnippet[] = self::getSnippet($latestReviews[$i]['text']);
    }


    //make them global
    $this->container->view->getEnvironment()->addGlobal('reviewSnippets', $reviewSnippet);
    $this->container->view->getEnvironment()->addGlobal('newReviewArts', $newReviewArts);
    $this->container->view->getEnvironment()->addGlobal('newAlbumNames', $newAlbumNames);
    $this->container->view->getEnvironment()->addGlobal('newArtists', $newArtists);
    //just in case there's less than 10
    $this->container->view->getEnvironment()->addGlobal('reviewCount', count($latestReviews) - 1);

    return $this->view->render($response, 'reviews.twig');
  }

  public function getSnippet($sentence, $count = 100) {
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
  }
}


 ?>

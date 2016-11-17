<?php

use Respect\Validation\Validator as v;

session_start();

//requiring the autoload
 require __DIR__ . '/../vendor/autoload.php';
 // $user = new SMA\Models\User;
 // var_dump($user);
 // die();

//making our slim app
$app = new \Slim\App([
  'settings' => [
    //changed these to stop middleware from acting up
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
      //db settings for elequont
    'db' => [
      'driver' => 'mysql',
      'host' => 'localhost',
      'database' => 'bmr',
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci'

    ]
  ],
]);

$container = $app->getContainer();
//use laravel outside of laravel basically
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
  return $capsule;
};

$container['auth'] = function ($container ) {
  return new \SMA\Auth\Auth;
};

//create our container
$container['view'] = function ($container) {
  //views is a twig object and we pass our views dir to it
  $view = new \Slim\Views\Twig(__DIR__ . '/../views', [
    'cache' => false,
  ]);


  $view->addExtension(new \Slim\Views\TwigExtension(
    $container->router,
    $container->request->getUri()
  ));

  //make auth global
  //this also prevents us from having to query for the user over and over again
  $view->getEnvironment()->addGlobal('auth', [
    //set these names to those methods
    'isLoggedIn' => $container->auth->isLoggedIn(),
    'user' => $container->auth->user(),
  ]);
  //make the flash messages global
  $view->getEnvironment()->addGlobal('flash', $container->flash);

//return view so we can do things with it
  return $view;
};

// TODO: change this messy controller set up
// It's probably possible to make it cleaner using dependancy injection
//register controllers
$container['validator'] = function ($container) {
  return new \SMA\Validation\Validator;
};

$container['AuthController'] = function ($container) {
  return new \SMA\Controllers\Auth\AuthController($container);
};

$container['AdminController'] = function ($container) {
  return new \SMA\Controllers\Admin\AdminController($container);
};

$container['HomeController'] = function ($container) {
  return new \SMA\Controllers\HomeController($container);
};

$container['SearchController'] = function ($container) {
  return new \SMA\Controllers\Search\SearchController($container);
};

$container['ArtistController'] = function ($container) {
  return new \SMA\Controllers\Artist\ArtistController($container);
};

$container['AlbumController'] = function ($container) {
  return new \SMA\Controllers\Album\AlbumController($container);
};

$container['ReviewController'] = function ($container) {
  return new \SMA\Controllers\Review\ReviewController($container);
};
//register csrf
$container['csrf'] = function ($container ) {
  return new \Slim\Csrf\Guard;
};

//register slim-flash
$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages();
};
//add middleware
$app->add(new \SMA\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \SMA\Middleware\OldInputMiddleware($container));
$app->add(new \SMA\Middleware\CsrfViewMiddleware($container));

//csrf is now on!
$app->add($container->csrf);

v::with('SMA\\Validation\\Rules');
//require our routes file
require __DIR__ . '/routes.php';
 ?>

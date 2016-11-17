<?php

namespace SMA\Controllers\Chat;

use SMA\Models\User;
use SMA\Models\Chatroom;
use SMA\Controllers\Controller;
use Respect\Validation\Validator as v;

class ChatRoomController extends Controller
{
protected $chatroomsOwned;
public function getCreateRoom($request, $response)
{
  unset($_SESSION['errors']);
  return $this->view->render($response, 'templates\chat\create-room.twig');
}

public function postCreateRoom($request, $response)
{
  $validation = $this->validator->validate($request, [
    //custom class name = name of the rule
    'roomName' => v::notEmpty()->roomNameAvailable(),
    'roomPassword' => v::noWhitespace()->notEmpty(),
  ]);
  if ($validation->failed()) {
    //redirect if failed
    return $response->withRedirect($this->router->pathfor('chat.create-room'));
  }
  //calls create on User with params from the form
  $newChatroom = Chatroom::create([
    'room_name' => $request->getParam('roomName'),
    'room_owner' => $_SESSION['user'],
    'room_password' => password_hash($request->getParam('roomPassword'), PASSWORD_DEFAULT),
  ]);
  //take user to the chat room
  $this->flash->addMessage('info', 'Room created successfully. ');
  return $response->withRedirect($this->router->pathfor("chat.chat-room") . "?room=". $request->getParam('roomName') );
}

public function getDeleteRoom($request, $response)
{

  //grab the chatroom that is being requested to be deleted
  $chatroom = Chatroom::where('room_name', $_GET['room'])->first();
  // delete the log
  unlink("logs/" . $chatroom['room_name'] . "log.json");
  //delete the chatroom from db
  $chatroom->delete();
  //add message and redirect back to the profile
  $this->flash->addMessage('info', 'Room deleted successfully. ');
  return $response->withRedirect($this->container->router->pathFor('profile'));
}

  public function profile($request, $response)
  {
    //grab all the chatrooms that the user owns
    $chatrooms = Chatroom::where('room_owner', $_SESSION['user'])->get(['room_name']);
    //make each one uppercase
    foreach ($chatrooms as $chatroom) {
      $chatroomsOwned[] = ucfirst($chatroom['room_name']);
    }
    //sort them
    sort($chatroomsOwned);
    //make them global
    $this->container->view->getEnvironment()->addGlobal('chatroomsOwned', $chatroomsOwned);
    //render the profile
    return $this->view->render($response, 'profile.twig');
  }

  public function getRoom($request, $response)
  {
    //grab the room that is requested
    $currentRoom = Chatroom::where('room_name', $_GET['room'])->first();
    //grab requested room's owner
    $ownerOfRequestedRoom = $currentRoom['room_owner'];
    //if the user doesn't own the room
    //prompt them to type in the password
    if ($ownerOfRequestedRoom != $_SESSION['user']) {
      $this->flash->addMessage('error', "You must enter the room's password first");
      return $response->withRedirect($this->router->pathFor('chat.join-room'));
    }

    //make the rooms name global
    $this->container->view->getEnvironment()->addGlobal('currentRoom', $currentRoom);
    //render the room
    return $this->view->render($response, 'templates\chat\chat-room.twig');
  }

  public function getJoinRoom($request, $response)
  {
    unset($_SESSION['errors']);
    return $this->view->render($response, 'templates\chat\join-room.twig');
  }

  public function postJoinRoom($request, $response)
  {
    $validation = $this->validator->validate($request, [
      //custom class name = name of the rule
      'roomName' => v::notEmpty(),
      'roomPassword' => v::noWhitespace()->notEmpty(),
    ]);
    if ($validation->failed()) {
      //redirect if failed
      return $response->withRedirect($this->router->pathfor('chat.join-room'));
    }

    $requestedRoom = Chatroom::where('room_name', $request->getParam('roomName'))->first();

    $roomPassword = $requestedRoom['room_password'];
    // $roomClients = $requestedRoom['clients'];
    // TODO: add people that are in chat
    if (password_verify($request->getParam('roomPassword'), $roomPassword)) {
      // $requestedRoom->clients = $requestedRoom->clients. "," .$roomClients ;
      // $requestedRoom->save();
      $this->flash->addMessage('info', 'Room joined successfully. ');
      return $response->withRedirect($this->router->pathfor("chat.chat-room") . "?room=". $request->getParam('roomName') );
    }

    $this->flash->addMessage('error', 'Could not join room with those details');
    return $response->withRedirect($this->router->pathFor('chat.join-room'));

  }
}


 ?>

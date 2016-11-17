<?php

date_default_timezone_set('America/Chicago');
$date = date('h:i:s A');
$chat = $_POST['chat'];
$chatroom = $_POST['chatroom'];
$author = $_POST['author'];

$chatroom = str_replace(' ', '', $chatroom);


$posts = [

  'author' => $author,
  'timestamp' => $date,
  'text' => $chat

];

$filename = "logs/" . $chatroom . "log.json";
// create the file if needed
if (!(file_exists($filename))) {
  $handle = fopen($filename, 'w+');
}

$handle = fopen($filename, 'r+');

if ($handle)
{
    // seek to the end
    fseek($handle, 0, SEEK_END);
    // are we at the end of is the file empty
    if (ftell($handle) > 0)
    {
        // move back a byte
        fseek($handle, -1, SEEK_END);
        // add the trailing comma
        fwrite($handle, ',', 1);
        // add the new json string
        fwrite($handle, json_encode($posts) . ']');
    }
    else
    {
        // write the first event inside an array
        fwrite($handle, json_encode(array($posts)));
    }
        // close the handle on the file
        fclose($handle);
}
?>

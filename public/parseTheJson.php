<?php
//get chatroom name from js
$chatroom = $_POST['chatroom'];
//get rid of spaces
$chatroom = str_replace(' ', '', $chatroom);
//get contents of log
$string = file_get_contents("logs/" . $chatroom . "log.json");

//decode and go through each line
$json_a = json_decode($string, true);
foreach ($json_a as $lines => $line) {
    $text[] = "<div><b>".$line['timestamp']. "-" . "<span class='lineAuthor'>" . ucfirst($line['author']) . "</span>:</b>" ." ".$line['text']."</div>";
}
//output lines
foreach ($text as $line ) {
  echo $line;
  echo "<br>";
}


 ?>

<?php
$urlWithIframe = '<iframe src="https://embed.spotify.com/?uri=spotify%3Aalbum%3A2gvhiM1BMdlxMvIQZPPdZC" width="300" height="380" frameborder="0" allowtransparency="true"></iframe>';
preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $urlWithIframe, $spotifyUrl);

print_r($spotifyUrl[0]);
$spotifyUrl = array_shift( $spotifyUrl );

var_dump($spotifyUrl[0]);
 ?>

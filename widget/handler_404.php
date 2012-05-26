<?php

function widget_handler_404 ( &$data ) 
{

  echo '<h1>The item you requested was not found</h1>';

  echo "\n";
  echo "\n";
  echo '<blockquote>';
  echo $_SERVER['REQUEST_URI'];
  echo '</blockquote>';

  echo "\n";
  echo "\n";
  echo '<p>';
  echo 'It may have been moved, taken offline, or you may have typed in the wrong address.';
  echo '</p>';
  

}

?>
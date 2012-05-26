<?php

function macro_footer ( &$data ) 
{

}


function render_footer ( $data ) 
{

  echo '<div id="footer">';

  echo '<p>';

  echo '<a href="/about/mme.html">';
  echo 'About MME';
  echo '</a>';

  echo ' - ';

  echo '<a href="/about/descriptions.html">';
  echo 'Collection Level Descriptions';
  echo '</a>';

  echo ' - ';

  echo '<a href="/about/credits.html">';
  echo 'Credits';
  echo '</a>';

  echo ' - ';

  echo '<a href="/about/contact.html">';
  echo 'Contact';
  echo '</a>';
  echo '</p>';

  echo '</div>';

}

?>
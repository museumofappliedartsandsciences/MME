<?php

function widget_terms ( $data )
{

  echo '<div id="terms" data-query="' . $data['query'] . '" data-id="' . $data['id'] . '"></div>';

}

?>
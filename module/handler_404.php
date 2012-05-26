<?php

function module_handler_404 ( &$data ) 
{

  // clear any node that was set before getting redirected here

  global $node;
  unset ( $node );

  header ( 'HTTP/1.0 404 Not Found' ); 

  core_set_data ( 'title', 'Nothing Found' ) ;
  core_set_template ( 'index' ) ;

}

?>
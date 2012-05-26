<?php

function module_js ( &$data )
{

  if ( ! $data['__key'] )
    {
      core_set_module ( 'handler_404' );
      return 1;
    }

  $f = $data['__key'];

  if ( substr ( $f, -3, 3 ) == '.js' && file_exists ( CORE_ROOT . 'htdocs/js/' . $f ) )
    {
      header ( 'Content-type: text/javascript' );
      readfile ( CORE_ROOT . 'htdocs/js/' . $f );
      exit;
    }


  // nothing found
  core_set_module ( 'handler_404' );
  return 1;

}

?>
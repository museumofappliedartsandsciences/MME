<?php

function module_css ( &$data )
{

  if ( ! $data['__key'] )
    {
      core_set_module ( 'handler_404' );
      return 1;
    }

  $f = $data['__key'];

  if ( substr ( $f, -4, 4 ) == '.css' && file_exists ( CORE_ROOT . 'htdocs/css/' . $f ) )
    {
      header ( 'Content-type: text/css' );
      readfile ( CORE_ROOT . 'htdocs/css/' . $f );
      exit;
    }


  // nothing found
  core_set_module ( 'handler_404' );
  return 1;

}

?>
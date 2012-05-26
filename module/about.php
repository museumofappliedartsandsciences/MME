<?php

function module_about ( &$data )
{

  if ( ! $data['__key'] )
    {
      $data['__key'] = 'mme';
    }

  $f = SITE_ROOT . 'data/about/' . str_replace ( '/', '', $data['__key'] ) . '.html';

  if ( ! file_exists ( $f ) )
    {
      core_set_module ( 'handler_404' );
    }

  $data['html'] = file_get_contents ( $f );

}

?>
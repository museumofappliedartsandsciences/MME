<?php

// phm api
// username: museumex
// password: Meeyo7se
// key 7b990d057664707

define ( 'PHM_API_KEY', '7b990d057664707' );

function module_terms ( &$data )
{

  if ( $data['q'] != '' )
    {

      $url = 'http://api.powerhousemuseum.com/api/v1/term/json/';
      $url .= '?api_key=' . PHM_API_KEY;
      $url .= '&term=' . urlencode ( trim ( $data['q'] ) );

      $s = file_get_contents ( $url );

      header ( 'Content-type: application/json' );
      echo $s;
      exit;
    }

  if ( $data['id'] && is_numeric ( $data['id'] ) )
    {

      $url = 'http://api.powerhousemuseum.com/api/v1/term/' . $data['id'] . '/json/';
      $url .= '?api_key=' . PHM_API_KEY;

      $s = file_get_contents ( $url );

      header ( 'Content-type: application/json' );
      echo $s;
      exit;
    }


  if ( $data['__key'] != '' )
    {
      //header ( 'Location: /terms#' . $data['__key'] );
      //exit;
      if ( is_numeric ( $data['__key'] ) )
        {
          $data['id'] = $data['__key'];
        }
      elseif ( strpos ( $data['__key'], '-' ) !== false && is_numeric ( substr ( $data['__key'], 0, strpos ( $data['__key'], '-' ) ) ) )
        {
          $data['id'] =substr ( $data['__key'], 0, strpos ( $data['__key'], '-' ) );
        }
      else
        {
          $data['query'] = urldecode ( $data['__key'] );
        }
    }


  core_head_add ( 'jquery' );
  core_head_add ( 'underscore.js' );
  core_set_template ( 'terms' );
}

?>
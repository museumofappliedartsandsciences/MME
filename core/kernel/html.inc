<?php

function html_form_start ( $m = '', $p = '' ) 
{

  $host = ( isset ( $_SERVER['HTTP_HOST'] ) )
    ? $_SERVER['HTTP_HOST']
    : SITE_URL; 

  if ( substr ( $host, 0, 7 ) == 'http://' )
    {
      $host = substr ( $host, 7 );
    }

  if ( isset ( $p['__secure'] ) && defined ( 'ENABLE_SSL' ) && ENABLE_SSL )
    {

      // go to or maintain secure connection
      $url = 'https://' . $host;
    }
  else
    {
      $url = ( isset ( $p['__absolute'] ) )
        ? 'http://' . $host 
        : '/';
    }

  $url .= ( $m != '' )
    ? $m
    : '';

  if ( isset ( $p['__secure'] ) )
    {
      unset ( $p['__secure'] );
    }

  if ( isset ( $p['__insecure'] ) )
    {
      unset ( $p['__insecure'] );
    }

  if ( isset ( $p['__absolute'] ) )
    {
      unset ( $p['__absolute'] );
    }

  $s = "\n";
  $s .= '<form action="' . $url . '"';
  
  $p['__method'] = ( isset ( $p['__method'] ) )
    ? $p['__method'] 
    : 'post';

  $s .= ' method="' . $p['__method'] . '"';

  unset ( $p['__method'] );

  if ( isset ( $p['__name'] ) )
    {
      $s .= ' name="' . $p['__name'] . '"';
      unset ( $p['__name'] );
    }

  if ( isset ( $p['__id'] ) )
    {
      $s .= ' id="' . $p['__id'] . '"';
      unset ( $p['__id'] );
    }

  if ( isset ( $p['__class'] ) )
    {
      $s .= ' class="' . $p['__class'] . '"';
      unset ( $p['__class'] );
    }

  if ( isset ( $p['__onsubmit'] ) )
    {
      $s .= ' onsubmit="' . $p['__onsubmit'] . '"';
      unset ( $p['__onsubmit'] );
    }

  if ( isset ( $p['__target'] ) )
    {
      $s .= ' target="' . $p['__target'] . '"';
      unset ( $p['__target'] );
    }

  // encoding type eg. multipart/form-data
  if ( isset ( $p['__encoding'] ) )
    {
      $s .=  ' enctype="' . $p['__encoding'] . '"';
      unset ( $p['__encoding'] );
    }
  
  $s .= '>' . "\n";
   
  if ( is_array( $p ) && sizeof ( $p > 0 ) )
    {
      foreach ( $p as $k=>$v )
        {
          $s .= ( $v != '' )
            ? '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars ( $v ) . '" />' . "\n"
            : '';
        }
    }
  
  return $s;
}

function core_form_start ( $m = '', $p = '' ) 
{
  return html_form_start( $m, $p );
}

function html_url ( $node, $p = false )
{

  $host = ( $_SERVER['HTTP_HOST'] )
    ? $_SERVER['HTTP_HOST']
    : SITE_URL; 

  if ( substr ( $host, 0, 7 ) == 'http://' )
    {
      $host = substr ( $host, 7 );
    }

  if ( ! $p )
    {
      $p = array();
    }

  // process __xx directives for the url

  // _secure=>true enables SSL mode if ENABLE_SSL from config.inc set
  if ( isset ( $p['__secure'] ) && defined ( 'ENABLE_SSL' ) && ENABLE_SSL )
    {
      // go to secure connection
      $s = trim ( 'https://' . $host, '/' );
    }
  else
    {
      // _absolute=>true generates absolute URLs
      $s = ( isset ( $p['__absolute'] ) )
        ? trim ( 'http://' . $host, '/' ) 
        : '';
    }

  unset ( $p['__secure'] );
  unset ( $p['__insecure'] );
  unset ( $p['__absolute'] );
  
  // generate nice urls based on passed node , or slugs/uri for numeric
  // node_id

  if ( $node && ! is_array ( $node ) && ! is_numeric ( $node ) && ! ( strpos ( $node, '/' ) === true ) )
    {

      // if it's a non-slashed word (ie slug or module name), link
      // directly to it

      $url = '/' . $node;

    }
  elseif ( is_array ( $node ) )
    {

      // if we were passed a whole node, use the info in that

      if ( substr ( $node['uri'], 0, 4 ) == 'home' )
        {
          $node['uri'] = substr ( $node['uri'], 4 );
          $node['uri'] = trim ( $node['uri'], '/' );
        }

      $url = '/' . $node['uri'];

    }

  elseif ( $node && is_numeric ( $node ) )
    {

      // if we were passed a node_id, fetch it and link to it's uri
      $node = db_node_fetch ( $node );

      if ( substr ( $node['uri'], 0, 4 ) == 'home' )
        {
          $node['uri'] = substr ( $node['uri'], 4 );
        }

      $node['uri'] = trim ( $node['uri'], '/' );
      $url = '/' . $node['uri'];

    }

  elseif ( $node && ! is_numeric ( $node ) )
    {

      // if it's a full path, link to that path

      if ( substr ( $node['uri'], 0, 4 ) == 'home' )
        {
          $node['uri'] = substr ( $node['uri'], 4 );
        }

      $node['uri'] = trim ( $node['uri'], '/' );
      $url = '/' . $node['uri'];

    }

  if ( $url && $url != '' ) 
    {
      $s .= $url;
    } 
  elseif ( $node == 1 ) 
    {
      // 	  $s .= '?';
    } 
  elseif ( $p != '' && $node != '' ) 
    {
      //	  $s .= '?m=' . $node;
      $s .= $node;
    } 
  else
    {
      $s .= $node;
    }

  if ( is_array ( $node ) && $node['type'] == 'file' )
    {

      // if file, append file extension to URI

      $mime = format_mime_extension ( $node['file_mime'] );

      if ( $mime )
        {
          $s .= '.' . $mime;
        }
    }

  /* experimental */
  if ( is_array ( $node ) && $node['type'] == 'image' )
    {

      // if image, generate correct uri including resize info

      if ( $p['x'] )
        {
          $node['x'] = $p['x'];
          unset ( $p['x'] );
        }

      if ( $p['y'] && is_numeric ( $p['y'] ) )
        {
          $node['y'] = $p['y'];
          unset ( $p['y'] );
        }

      // generate resize info based on passed params
      if ( $node['file_mime'] == 'image/gif' && ! $node['x'] && ! $node['y'] )
        {
          // pass unresized gif thru as is
          $s .= '.gif';
        }
      elseif ( $node['file_mime'] == 'image/png' && ! $node['x'] && ! $node['y'] )
        {
          // pass unresized png thru as is
          $s .= '.png';
        }
      elseif ( ! $node['x'] && ! $node['y'] ) 
        {
          // pass unresized jpeg thru as is
          $s .= '.jpg';
        }
      else
        {
          // otherwise x and/or y have been passed - generate url for
          // jpg resize image

          // will look something like this

          // /.../image-slug.x256.jpg

          // /.../image-slug.x256y192.jpg

          $s .= '.';

          if ( isset ( $node['x'] ) )
            {
              $s .= 'x' . $node['x'];
            }

          if ( isset ( $node['y'] ) )
            {
              $s .= 'y' . $node['y'];
            }

          $s .= '.jpg';
        }

      // any other arguments in $p will be appended to image url as
      // ?key=value

    }

  if ( isset ( $p['__key'] ) )
    {
      $s .= '/' . $p['__key'];
      unset ( $p['__key'] );
    }
  
  if ( is_array( $p ) )	
    {
      foreach ( $p as $k => $v )
        {
          if ( $v != '' )
            {
              $s .= ( strpos ( $s, '?' ) ) ? '&amp;' : '?';
              $s .= $k . '=' . htmlspecialchars ( $v );
            }
        } 
    }
  elseif ( $p )
    {
      $s .= ( strpos ( $s, '?' ) ) ? '&amp;' : '?';
      $s .= $p;
    }

  return $s;

}

function core_url ( $m, $p = '', $secure = 0 ) 
{
  return html_url ( $m, $p, $secure );
}

function html_image_url ( $node, $p = false )
{


  // depricated style, second param was true if this function should
  // return an absolute url. now param is style similar to html_url
  if ( ! is_array ( $p ) && $p )
    {
      $p = array();
      $p['absolute'] = true;
    }

  // dpericated naming, should be called with $p['absolute']
  if ( isset ( $p['__absolute'] ) )
    {
      $p['absolute'] = true;
    }


  if ( $p['x'] )
    {
      $node['x'] = $p['x'];
    }

  if ( $p['y'] && is_numeric ( $p['y'] ) )
    {
      $node['y'] = $p['y'];
    }

  if ( ! is_array ( $node ) )
    {
      $node = db_node_fetch ( $node );
    }

  if ( $p['absolute'] )
    {
      // see if we're running from httpd or cli
      $url = ( $_SERVER['HTTP_HOST'] )
        ? 'http://' . $_SERVER['HTTP_HOST'] . '/'
        : SITE_URL; //  SITE_URL is defined in runscript.php
    }
  else
    {
      $url = '/';
    }

  $url .= PATH_IMAGES;

  if ( $node['file_mime'] == 'image/gif' && ( $node['x'] == '' || $node['x'] == 'src' ) )
    {
      $url .= $node['node_id'] . '.gif';
    }
  elseif ( $node['file_mime'] == 'image/png' && ( $node['x'] == '' || $node['x'] == 'src' ) )
    {
      $url .= $node['node_id'] . '.png';
    }
  elseif ( $node['x'] == '' || $node['x'] == 'src' ) 
    {
      $url .= $node['node_id'] . '.jpg';
    }
  else
    {

      $url .= $node['node_id'];
	  
      $url .= 'x' . $node['x'];

      if ( isset ( $node['y'] ) )
        {
          $url .= 'y' . $node['y'];
        }
	  
      if ( isset ( $node['r'] ) )
        {
          $url .= 'r' . $node['r'];
        }

      $url .= '.jpg';

    }

  return $url;

}

?>
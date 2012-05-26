<?php

require CORE_ROOT . 'kernel/define.inc';
require CORE_ROOT . 'kernel/db.inc';
require CORE_ROOT . 'kernel/user.inc';
require CORE_ROOT . 'kernel/session.inc';
require CORE_ROOT . 'kernel/validate.inc';
require CORE_ROOT . 'kernel/format.inc';
require CORE_ROOT . 'kernel/html.inc';

function core_set_data ( $module, $data = '' ) 
{

  global $page;
  $page[$module] = $data;

}

function core_data ( $module ) 
{

  global $page;
  if ( isset ( $page[$module] ) )
    {
      return $page[$module];
    }

}

function core_set_module ( $module, $data = false ) 
{
  global $m;
  $m = $module;
  if ( $data )
    {
      core_set_data ( $module, $data );
    }
}

function core_set_title ( $title = '' ) 
{
  global $page;
  $page['title'] = $title;
}

function core_set_template ( $template ) 
{
  global $page;
  $page['template'] = $template;
}

function core_parse_macro ( $macro )
{

  $args = false;

  if ( strpos( $macro, '{' ) ) 
    {
      // parse json style params

      // macro parameters can be expressed as a dictionary, e.g:
      // home_news { limit: 4, [key: value] }
      // home_news { limit: 4, category_id: 1234 }

      // this implementaion is pretty simple, doesn't allow quoted
      // values, values with colons or commas

      $args = array();

      // args[0] is name of macro
      $args[0] = substr ( $macro, 0, strpos ( $macro, ' ' ) );

      $macro_s = substr ( $macro, strpos ( $macro, ' ' ) );
      $macro_s = trim ( $macro_s, '{} ' );
      $macro_s = explode ( ',', $macro_s );

      if ( $macro_s )
        {
          foreach ( $macro_s as $k => $v )
            {
              list ( $key, $value ) = explode ( ':', $v );
              $args[trim($key)] = trim ( $value );
            }
          unset ( $macro_s );
        }

      $macro = $args[0];

    }
  elseif ( strpos( $macro, ' ' ) ) 
    {
      // else parse as space separated params
      $args = explode( ' ', $macro );
      $macro = $args[0]; // slug of macro
    }

  return $args;

}

/**
 * Add item for inclusion in <head /> of template via page_head
 * template macro/module. Types may be css, js - loaded from
 * htdocs/css, htdocs/js, or 'inline' - literal code to add to
 * template. if a single argument is provided, it's assumed to be a
 * literal inline
 */
function core_head_add ( $type, $s='' )
{

  if ( $type == 'jquery' )
    {
      $type = 'js';
      $s = 'jquery-1.4.4.min.js';
    }

  elseif ( $type && $s == '' )
    {
      if ( substr ( $type, -3, 3 ) == '.js' )
        {
          $s = $type;
          $type = 'js';
        }
      elseif ( substr ( $type, -4, 4 ) == '.css' )
        {
          $s = $type;
          $type = 'css';
        }
    }


  global $__page_head;
  if ( $type && $s )
    {
      $__page_head[$type][$s] = $s;
    }
  elseif ( $type )
    {
      $__page_head['inline'][] = $type;
    }
}

/**
 * Return all head items, or if $type specified, specific section
 */
function core_head ( $type = false ) 
{

  global $__page_head;

  if ( $type )
    {
      return ( isset ( $__page_head[$type] ) )
        ? $__page_head[$type]
        : false;
    }
  else
    {
      return $__page_head;
    }

}

function core_die ( $title, $s = '' ) 
{

  $s = $title . "\n" . $s . "\n\n";

  global $m;
  $s .= 'Module: ' . $m . "\n" . "\n";

  global $page;
  $s .= 'Module Data: ' . $m . "\n" . "\n";
  $s .= format_array2str ( $page[$m] ) . "\n" . "\n";

  $s .= format_array2str ( $_REQUEST, 'REQUEST' ) . "\n";
  $s .= format_array2str ( $_SESSION, 'SESSION' ) . "\n";
  $s .= format_array2str ( $_ENV, 'ENV' ) . "\n";
  $s .= format_array2str ( $_SERVER, 'SERVER' ) . "\n";
  
  if ( defined ( 'PRODUCTION' ) && PRODUCTION )
    {
      @mail( 
            EMAIL_WEBMASTER,
            $title,
            $s,
            'From: ' . EMAIL_WEBMASTER,
            '-f' . EMAIL_WEBMASTER_ADDRESS
             );
	  
      header ( 'Location: /error' );
      exit;
    }

  spit ( $s );
  exit;

}

function core_module_exists ( $m ) 
{
  return ( file_exists( CORE_ROOT . 'module/' . $m ) || file_exists( CORE_ROOT . 'module/' . $m . '.php' ) || file_exists( SITE_ROOT . 'module/' . $m ) || file_exists( SITE_ROOT . 'module/' . $m . '.php' ) );
}

function core_widget_exists ( $m ) 
{
  return ( file_exists( CORE_ROOT . 'widget/' . $m ) || file_exists( SITE_ROOT . 'widget/' . $m ) );
}

function core_load ( $unit, $code ) 
{

  if ( file_exists ( SITE_ROOT . $unit . '/' . $code . '.php' ) && is_file ( SITE_ROOT . $unit . '/' . $code . '.php' ) )
    {
      $f = SITE_ROOT . $unit . '/' . $code . '.php';
    }
  elseif ( file_exists ( CORE_ROOT . $unit . '/' . $code ) && is_file ( CORE_ROOT . $unit . '/' . $code ) )
    {
      $f = CORE_ROOT . $unit . '/' . $code;
    }
  elseif ( file_exists ( CORE_ROOT . $unit . '/' . $code . '.php' ) && is_file ( CORE_ROOT . $unit . '/' . $code . '.php' ) )
    {
      $f = CORE_ROOT . $unit . '/' . $code . '.php';
    }
  elseif ( $unit == 'include' && file_exists ( SITE_ROOT . $unit . '/' . $code ) )
    {
      $f = SITE_ROOT . $unit . '/' . $code;
    }
  elseif ( $unit == 'include' && file_exists ( SITE_ROOT . $unit . '/' . $code . '.inc' ) )
    {
      $f = SITE_ROOT . $unit . '/' . $code . '.inc';
    }
  else 
    {
      return false;
    }

  include_once ( $f );

  return true;

}

function core_template_fetch ( $t ) 
{

  $f = false;

  if ( file_exists( SITE_ROOT . 'templates/' . $t . '.html' ) ) 
    {
      $f = SITE_ROOT . 'templates/' . $t . '.html';
    }
  elseif ( file_exists( SITE_ROOT . 'templates/' . $t ) ) 
    {
      $f = SITE_ROOT . 'templates/' . $t;
    }

  if ( $f )
    {
      return file_get_contents ( $f );
    }
  else 
    {
      return ' <!--# module #-->';
    }

}

/**
 * 
 * Analyse the HTTP_REQUEST_VARS and convert them to internal format
 *
 * Because javascript doesn't like php style array names as var names in forms
 * we allow a different naming scheme in html forms to allow easier javascript coding
 * without affecting the internal naming scheme
 * param would be converted to $data[param]
 * paramMore would be converted to $data[param][more]
 */
function intercept_request_vars () 
{

  //  $request = $_REQUEST;

  if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
    {
      $request = $_REQUEST;
    }
  else
    {
      $request = $_GET;
    }

  // php 4.3 and later.
  if ( is_array ( $_FILES ) )
    {
      $request = array_merge ( $request, $_FILES );
    }

  if ( is_array ( $request ) ) 
    {

      $data = array();

      foreach ( $request as $k => $v ) 
        {
          // xxx=yyy is converted to $data[xxx]=yyy
          // xxxNnn=yyy is converted to $data[xxx][nnn]=yyy
				
          // if $k has capitals, $data[$k] is an array and we take the string starting with the cap as a sub-key 
          if ( ereg ('[A-Z]', $k ) )
            {
              $var = &$data;
              // while there is a chunk with a cap in it
              while ( ereg ('[A-Z]', $k ) )
                {
                  // find the first cap
                  $i = 0;
                  while ( ! ereg ('[A-Z]', substr ( $k, $i, 1 ) ) ) 
                    {
                      $i++;
                    }

                  // everything before the cap becomes a key
                  $kk = substr ( $k, 0, $i );

                  if ( ! is_array ( $var[$kk] ) )
                    {
                      $var[$kk] = array();
                    }

                  $var = &$var[$kk];

                  // consume the chunk
                  $k = substr ( $k, $i );

                  // make first char lowercase and look for next chunk
                  $k = substr_replace ( $k, strtolower ( substr ( $k, 0, 1 ) ), 0, 1); 

                  // if no more caps left, assign the value
                  if ( ! ereg ('[A-Z]', $k ) )
                    {
                      $kk = strtolower ( $k );
                      $var[$kk] = $v;
                    }
                  $i = 0;
                }
            }
          elseif ( $k != '' )
            {
              $data[$k] = $v;
            }
        }
    }

  unset ( $data[''] );

  return $data;
  
}

?>
<?php

function format_autolink ( $s = false ) 
{

  // put http:// in front of anything starting with www
  $s = ereg_replace("((www.)([a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/]))", "http://\\1", $s ); 

  // ?
  $s = ereg_replace("((ftp://|http://|https://){2})([a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/])", "http://\\3", $s ); 

  // create the <a> </a> wrapper around the link
  $s = ereg_replace("(((ftp://|http://|https://){1})[a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/])", "<a href=\"\\1\">\\1</a>", $s ); 

  // get rid of double links ( a normal a href that has been autolinked
  $s = ereg_replace("((<a href=\"<a href=\")(((ftp://|http://|https://){1})[a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/]\">))((((ftp://|http://|https://)([a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/]))))(</a>\">)", "<a href=\"\\6\">", $s ); 

  // autolink email addresses
  $s = ereg_replace("([_\.0-9A-Za-z-]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[a-z]{2,3})","<a href=\"mailto:\\1\">\\1</a>", $s ); 

  return $s;

}

function format_datetime2unix ( $s ) 
{
  return mktime( 
                substr ( $s, 8, 2 ), 
                substr ( $s, 10, 2 ), 
                substr ( $s, 12, 2 ), 
                substr ( $s, 4, 2 ), 
                substr ( $s, 6, 2 ), 
                substr ( $s, 0, 4 )
                 );
}

// depricated naming, use datetime2unix
function format_timestamp2unix ( $s ) 
{
  return mktime( 
                substr ( $s, 8, 2 ), 
                substr ( $s, 10, 2 ), 
                substr ( $s, 12, 2 ), 
                substr ( $s, 4, 2 ), 
                substr ( $s, 6, 2 ), 
                substr ( $s, 0, 4 )
                 );
}

function format_unix2datetime ( $s )
{
  return date ( 'YmdHis', $s );
}

// depricated naming, use unix2datetime
function format_unix2timestamp ( $s )
{
  return date ( 'YmdHis', $s );
}

function format_datetime ( $d = false, $f = 'D j M Y' ) 
{
  if ( $d && $d != '00000000000000' )
    {
      return date( $f, format_datetime2unix ( $d ) );
    }
}

function format_time ( $d = '', $f = 'H:i' ) 
{

  if ( strlen ( $d ) == 4 )
    {
      // HHMM
      return date( $f, format_datetime2unix ( '19720211' . $d . '00' ) );
    }

  elseif ( strlen ( $d ) == 6 )
    {
      // HHMMSS
      return date( $f, format_datetime2unix ( '19720211' . $d ) );
    }

  elseif ( strlen ( $d ) == 14 )
    {
      // YYYYMMDDHHMMSS
      return date( $f, format_datetime2unix ( $d ) );
    }

}

function format_hm2dec ( $t )
{

  // convert hm time (2h30m) to decimal hours (2.5)

  $t = str_replace ( ' ', '' , $t );

  if ( preg_match_all ( "/(^([0-9.]*)[h])?(([0-9.]*)[m])?$/si", $t, $m, PREG_PATTERN_ORDER ) )
    {

      if ( $m[0][0] )
        {
          $d['hour'] = $m[2][0];
          $d['minute'] = $m[4][0];

          $decimal = $d['hour'] + ( $d['minute'] / 60 );
        }
      else 
        {
          if ( is_numeric ( $t ) )
            {
              $decimal = $t;
            }
          else
            {
              return false;
            }
        }
    }

  return ( $decimal ) ? trim ( $decimal ) : false;

}


function format_dec2hm ( $d )
{

  // convert decimal hours (2.5) to hm time (2h30m)

  $d = str_replace ( ' ', '' , $d );

  $time = $d * 60;
  
  $hour = sprintf ( "%d", $time / 60 );
  $minute = $time % 60;

  return trim ( ( ( $hour > 0 ) ? $hour . 'h ' : '' ) . ( ( $minute ) ? ( $minute . 'm' ) : '' ) );

}

/**
 * display human readable version of time supplied in seconds
 */
function format_seconds ( $seconds = false ) 
{

  if ( ! $seconds )
    {
      return false;
    }

  $seconds = round ( $seconds );

  $exp = 1; 

  while( $seconds >= pow ( 60, $exp ) ) 
    { 
      $exp++; 
    } 

  $unit = array ( 's', 'm', 'h' );

  $s = '';

  for ( $i = $exp; $i > 0; $i-- )
    {

      $s .= ' ';
      // get the number for h m s each time
      $s .= floor ( $seconds / pow ( 60, ( $i - 1 ) ) ) . $unit[$i-1];

      // subtract the time has been counted.
      $seconds = $seconds - floor ( $seconds / pow ( 60, ( $i - 1 ) ) ) * pow ( 60, ( $i - 1 ) );

    }
	  
  return trim ( $s );

}

function format_age ( $seconds, $long=false ) 
{

  $d = false;
  $h = false;
  $m = false;

  $s = '';

  if ( $seconds > ( 24 * 60 * 60 ) )
    {
      $d = floor ( $seconds / ( 24 * 60 * 60 ) );
      $seconds = $seconds - (  $d * 24 * 3600 );
    }
  elseif ( $seconds > ( 3600 ) )
    {
      $h = floor ( $seconds / 3600 );
      $seconds = $seconds - ( $h * 3600 );
    }
  elseif ( $seconds > ( 60 ) )
    {
      $m = floor ( $seconds / 60 );
      $seconds = $seconds - $m * 60;
    }
  else
    {
      $s .= ( $seconds > 0 )
        ? $seconds . ( ( $long ) ? ' seconds ' : 's ' )
        : '';
    }

  if ( $d > 0 )
    {
      $s .= ( $d == 1 )
        ? $d . ( ( $long ) ? ' day ' : 'd ' )
        : '';

      $s .= ( $d > 1 )
        ? $d . ( ( $long ) ? ' days ' : 'd ' )
        : '';
    }

  if ( $h >= 1 )
    {
      $s .= ( $h == 1 )
        ? $h . ( ( $long ) ? ' hour ' : 'h ' )
        : '';

      $s .= ( $h > 1 )
        ? $h . ( ( $long ) ? ' hours ' : 'h ' )
        : '';
    }

  if ( $m > 0 )
    {
      $s .= ( $m == 1 )
        ? $m . ( ( $long ) ? ' minute ' : 'm ' )
        : '';

      $s .= ( $m > 1 )
        ? $m . ( ( $long ) ? ' minutes ' : 'm ' )
        : '';
    }

  return trim ( $s );

}

function format_bytes ( $size ) 
{
  $exp = 1; 
  while($size >= pow(1024, $exp)) 
    { 
      $exp++; 
    } 
  $ext = array("b", "Kb", "Mb", "Gb", "Tb"); 
  $summary = round(($size * 100) / pow(1024, ($exp - 1))) / 100; 

  if ( $exp <= 2 )
    {
      // show K's without decimals
      return number_format ( $summary, 0 ) . $ext[$exp - 1];
    }
  else
    {
      return sprintf("%.2f%s", $summary, $ext[$exp - 1]);
    }
}

function format_slug ( $s = '' ) 
{

  $s = trim ( $s );
  $s = strtolower ( $s );
  $s = ereg_replace ( "[[:space:]]+", "-", $s );
  $s = ereg_replace ( "[^_0-9A-Za-z\-]", "", $s );
  $s = str_replace ( ".", "-", $s );
  $s = str_replace ( ":", "-", $s );
  $s = str_replace ( "--", "-", $s );
  return $s;

}

function brief ( &$nodes )
{
  if ( is_array ( $nodes ) )
    {
      echo '<div style="text-align: left; border: 1px solid black; color: black; background: white;">';
      echo '<pre>';
      foreach ( $nodes as $k => $v )
        {
          echo $v['node_id'];
          echo "\t" . $v['type'];
          echo "\t" . $v['status'];
          echo "\t" . $v['uri'];
          echo "\t" . $v['title'];
          echo "\n";
        }
      echo '</pre>';
      echo '</div>';
    }
}

function spit ( $data, $name = '' )
{

  echo ( ! isset ( $_SERVER['SHELL'] ) )
    ? '<pre style="text-align: left; color: #000; background: #ff0; border: 1px solid #c00;">' 
    : '';

  echo ( is_array ( $data ) ) 
    ? format_array2str ( $data, $name )
    : $data; 

  echo ( ! isset ( $_SERVER['SHELL'] ) )
    ? '</pre>' 
    : "\n";

}

function spitt ( $data, $name = '' )
{

  echo ( ! $_SERVER['SHELL'] ) 
    ? '<textarea style="width: 100%; height: 128px; background: #ffe;" onfocus="this.select();">' 
    : "\n";

  echo ( is_array ( $data ) ) 
    ? format_array2str ( $data, $name )
    : $data; 

  echo ( ! $_SERVER['SHELL'] ) 
    ? '</textarea>' 
    : "\n";

}

function spitw ( $data, $name = '' ) 
{
  // spit with wordwrap
  
  echo ( ! $_SERVER['SHELL'] ) 
    ? '<pre style="text-align: left; color: #000; background: #fff; border: 1px solid #c00;">' 
    : "\n";

  echo  ( is_array ( $data ) ) 
    ? wordwrap ( format_array2str ( $data, $name ) )
    : wordwrap ( $data ); 

  echo ( ! $_SERVER['SHELL'] ) 
    ? '</pre>' 
    : "\n";

}

function format_truncate ( $s, $ln = 32 )
{
  return  ( strlen( $s ) > $ln ) 
    ? trim ( substr ( $s, 0, $ln ) ) . '...' 
    : $s;
}

function format_dump_array ( $a, $t ) 
{

  $s = '';
  $count = 0;

  foreach ( $a as $k => $v )
    {

      if ($count++ > 0)
        {
          $s .= ',';
        }

      $s .= "\n";

      $s .= str_repeat ("\t", $t) . "'$k' => ";

      if ( is_array( $v ) ) 
        {
          $s .= "array (";
          $s .= format_dump_array ($v, $t+1);
          $s .= "\n" . str_repeat ("\t", $t) .")";
        }
      else 
        {
          $v = ereg_replace("'", "\\'", $v);
          $s .= "'$v'";
        }
    }
  return $s;
}

function format_array2str ( $arr, $name = 'data' ) 
{
  if ( is_array ( $arr ) ) 
    {
      $s = '';
      $s .= '$' . $name . ' = array( ';
      $s .= format_dump_array ( $arr, 1 );
      $s .= ');';
    } 
  else 
    {
      $s = $a . ' = array();';
    }

  $s .= "\n";

  return $s;

}


// return uri to icon for given mime type
function format_mime_icon ( $mime = false )
{

  global $__MIME;
  if ( ! $__MIME )
    {
      // only load in mime data actually needed required
      include_once CORE_ROOT . 'kernel/mime.inc';
    }

  return ( $__MIME[$mime] && $__MIME[$mime]['extension'] )
    ? '/images/icon/' . $__MIME[$mime]['extension'] . '.png'
    : '/images/icon/generic.png';

}


// return file extension for given mime type
function format_mime_extension ( $mime = false )
{

  global $__MIME;
  if ( ! $__MIME )
    {
      // only load in mime data actually needed required
      include_once CORE_ROOT . 'kernel/mime.inc';
    }

  return ( $__MIME[$mime] && $__MIME[$mime]['extension'] )
    ? $__MIME[$mime]['extension']
    : false;

}

// return file extension for given mime type
function format_extension_mime ( $extension )
{

  global $__MIME;
  if ( ! $__MIME )
    {
      // only load in mime data actually needed required
      include_once CORE_ROOT . 'kernel/mime.inc';
    }

  foreach ( $__MIME as $k => $v )
    {
      if ( $v['extension'] == $extension )
        {
          return $k;
        }
    }

  return false;

}

// return file extension for given mime type
function format_mime_title ( $mime = false )
{

  global $__MIME;
  if ( ! $__MIME )
    {
      // only load in mime data actually needed required
      include_once CORE_ROOT . 'kernel/mime.inc';
    }

  return ( $__MIME[$mime] && $__MIME[$mime]['extension'] )
    ? $__MIME[$mime]['title']
    : $mime;

}

function format_password ( $options = false )
{

  // return a random password

  // if options is an integer, it's the length of password to return

  // if options is an array, it can have ['length'] and ['invalid']
  // parameters to set length and characters to exclude from the
  // password.

  if ( ! $options )
    {
      $options = array();
    }

  if ( is_numeric ( $options ) )
    {
      $length = $options;
      $options = array( 'length' => $length );
    }

  if ( ! $options['length'] )
    {
      $options['length'] = 6;
    }

  if ( ! $options['invalid'] )
    {
      $options['invalid'] = '1liIoO05sS';
    }

  if ( ! is_array ( $options['invalid'] ) )
    {
      $options['invalid'] = str_split ( $options['invalid'] );
    }

  
  $password = '';
	
  // try and generate one from the system
  $password = @exec ( '/usr/bin/pwgen -n' );

  // remove confusing characters
  $password = str_replace ( $options['invalid'], '', $password );

  // fill out remaining character
  while ( strlen( $password ) < $options['length'] ) 
    {

      switch ( rand ( 1, 3 ) ) 
        {
        case 1:
          $password .= chr ( rand ( 48, 57 ) );
          break;

        case 2:
        case 3:
          $password .= chr ( rand ( 97, 122 ) );
          break;
        }

      // remove confusing characters
      $password = str_replace ( $options['invalid'], '', $password );

    }

  return $password;

}

?>
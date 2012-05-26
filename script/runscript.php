#! /usr/bin/php -q
<?php

set_time_limit(0);
error_reporting ( E_ALL & ~E_NOTICE & ~E_DEPRECATED );
//ini_set ( 'log_errors', false );

if ( ! isset ( $_SERVER['argv'][1] ) )
  {
    // no script name given, nothing to do
    exit;
  }


define ( 'SITE_ROOT', dirname( dirname(__FILE__) ) . '/' );

if ( ! file_exists ( SITE_ROOT . 'config/config.inc' ) )
  {
    exit;
  }

require_once SITE_ROOT . 'config/config.inc';

// SITE_URL is normally defined in define.inc, but there is no server
// ENV variables available for offline scripts to make it from.
define ( 'SITE_URL', 'http://' . SITE_URL_DISPLAY  .'/' );

if ( ! file_exists ( CORE_ROOT . 'kernel/core.inc' ) )
  {
    exit;
  }

require CORE_ROOT . 'kernel/core.inc';

$m = $_SERVER['argv'][1];
$m = ( substr ( $m, -4, 4 ) == '.php' ) 
  ? substr ( $m, 0, strlen ( $m ) - 4 )
  : $m;

$p = isset ( $_SERVER['argv'] )
  ? $_SERVER['argv']
  : false;

core_load ( 'script', $m );

$fn = 'script_' . $m;
if ( function_exists( $fn ) ) 
  {
    $fn ( $p ); 
  }

?>
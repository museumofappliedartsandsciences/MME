<?php

date_default_timezone_set ( 'Australia/Sydney' );

if ( isset ( $_SERVER['SERVER_NAME'] ) )
  {
    define( 'SITE_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/' );
  }

// these are enabled in config.inc by technician using
// /admin_capabilities

if ( ! defined ( 'DEFAULT_PAGE' ) )
  {
    define ( 'DEFAULT_PAGE', '' );
  }

if ( isset ( $_SERVER['SERVER_NAME'] ) )
  {
    define ( 'COOKIE_URL', '.' . $_SERVER['SERVER_NAME'] ); 
    define ( 'COOKIE_NAME', 'cms' ); 
  }

// hardcoded workflow states
define ( 'STATUS_LIVE', 1 ); // ONLINE
define ( 'STATUS_PREVIEW', 2 ); // IN PROGRESS/BEING WORKED ON
define ( 'STATUS_TRASH', 3 ); // IN TRASH, AWAITING DELETION
define ( 'STATUS_ARCHIVE', 4 ); //IN ARCHIVE
define ( 'STATUS_PENDING', 5 ); // AWAITING APPROVAL

// magic statuses for function calls
define ( 'STATUS_ALL', -1 );

?>
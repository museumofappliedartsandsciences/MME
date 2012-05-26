<?php

if ( ! file_exists ( CORE_ROOT . 'kernel/core.inc' ) )
  {
    echo 'No core found. Please check configuration for this site';
    exit;
  }

header ( "Expires: " . gmdate("D d M Y H:i:s") . ' GMT' );
header ( "Last-modified: " . gmdate("D d M Y H:i:s") . ' GMT' );
header ( "Cache-Control: no-cache" );

require CORE_ROOT . "kernel/core.inc";

// set up user session
session_init();

/**
 * Dispatcher - interprets and handles incoming requests We get sent
 * here from 404 errors, so all requests to URIs that are not static
 * files will be handled by index.php and thus index.inc . Apache's
 * ErrorDocument MUST be set up to send all 404 errors to /index.php -
 * i.e: ErrorDocument 404 /index.php
 */

$p = array();
$p['__key'] = false;
$p['__value'] = false;

// $m is the name of the module to execute for this uri
global $m;
$m = false;

if ( isset ( $_REQUEST['m'] ) )
  {
    // old style, get module name from 'm'
    $m = $_REQUEST['m'];
  }
elseif ( $_SERVER['REQUEST_METHOD'] == 'POST' )
  {
    // new style, get module name from request; trim off leading slash
    $m = substr ( $_SERVER['REQUEST_URI'], 1 );
    $pos = strpos ( $m, '/' );
    if ( $pos !== false )
      {
        $p['__key'] = substr ( $m, $pos + 1 );
        $m = substr ( $m, 0, $pos );
      }
    unset ( $pos );
  }

// if $m hasn't been explicitly set by get or post, try and determine
// what it should be

if ( ! $m ) 
  { 

    $REQUEST_URI = $_SERVER['REQUEST_URI'];

    // can $m be extrapolated from the uri?

    $m = false;

    // remove leading and trailing slashes
    $uri = trim ( $REQUEST_URI, '/' );
    $uri = str_replace ( '%20', ' ', $uri );
    $uri = trim ( $uri );

    if ( substr ( $uri, 0, 9 ) == 'index.php' )
      {
        $uri = substr ( $uri, 9 );
      } 

    // if /index.html on the end, strip it off to simulate a folder
    // index
    if ( substr ( $uri, -10, 10 ) == 'index.html' )
      {
        $uri = substr ( $uri, 0, strlen ( $uri ) - 10 );
      }

    // if .html on the end, strip it off to simulate a page
    if ( substr ( $uri, -5, 5 ) == '.html' )
      {
        $uri = substr ( $uri, 0, strlen ( $uri) - 5 );
      }

    // is there a module that matches that path
    if ( ! $m ) 
      {

        // slashed url path - is first item a module e.g.
        // "/module?some=foo&params=bar" - pass $data[some]="foo" and
        // $data[params]="bar" to the module.
        if ( strpos ( $uri, '/' ) )
          {
            $i = substr ( $uri, 0, strpos ( $uri, '/' ) );
            $i = str_replace ( '-', '_', $i );
            if ( core_module_exists ( $i ) )
              {
                $m = $i;
                if ( strpos ( $uri, '?' ) )
                  {
                    $i = explode( '?', $uri );
                    $p['__key'] = substr ( $i[0], strpos ( $i[0], '/' ) + 1 );
                    if ( $i[1] != '' )
                      {
                        $p['__value'] = $i[1];
                      }
                  }
                else
                  {
                    $p['__key'] = substr ( $uri, strpos ( $uri, '/' ) + 1 );
                  }
              }
            elseif ( strpos ( $uri, '?' ) )
              {
                // if not a module it's /some/node/uri?with=somevalue

                $i = explode( '?', $uri );
                $foo = explode( '&', $i[1] );
                foreach ($foo as $k=>$v )
                  {
                    $a = substr ( $v, 0, strpos ( $v, '=' ) );
                    $b = substr ( $v, strpos ( $v, '=' ) + 1 );
					  
                    // put in to _GET so it gets evaluated to nested array
                    $_GET[$a] = $b;
                  }
              }
          }
        else
          {
            // if /module/some/params?extra=foo, then extract the
            // key-value pairs after the ? first

            // uri with a query - /module?someparam or
            // /module?foo=bar&bar=foo - set the module and let the
            // interceptor take care of figuring out the params
            $i = explode( '?', $uri );
            $i[0] = str_replace ( '-', '_', $i[0] );
            if ( ! strpos( $i[0], '/' ) && core_module_exists ( $i[0] ) )
              {
                $m = $i[0];
              }

            // if there is a request like '/module?123' then use the 123
            // as a primary key for they module (allows cleaner urls)
            if ( sizeof ( $i ) == 2 && ( strpos ( $i[1], '=' ) === false ) )
              {
                $p['__key'] = $i[1];
              }

          }
      }

    if ( is_array ( $i ) && isset ( $i[1] ) ) 
      {
        $foo = explode( '&', $i[1] );

        foreach ($foo as $k=>$v )
          {
            $a = substr ( $v, 0, strpos ( $v, '=' ) );
            $b = substr ( $v, strpos ( $v, '=' ) + 1 );

            // put in to _GET so it gets evaluated to nested array
            $_GET[$a] = $b;
          }

      }

    if ( ! $m ) 
      {
        $i = explode( '?', $uri );
		  
        if ( is_array ( $i ) && isset ( $i[1] ) ) 
          {

            $uri = substr ( $uri, 0, strpos ( $uri, '?' ) );
            $foo = explode( '&', $i[1] );

            foreach ( $foo as $k => $v )
              {
                $a = substr ( $v, 0, strpos ( $v, '=' ) );
                $b = substr ( $v, strpos ( $v, '=' ) + 1 );

                // put in to _GET so it gets evaluated to nested array
                $_GET[$a] = $b;
              }

          }

        if ( ! $uri )
          {
            $m = DEFAULT_PAGE;
          }

      }

    // if nothing appropriate so far, there is no hope
    if ( ! $m ) 
      {
        $m = 'handler_404';
      } 
    else
      {

        // If something was found, let the client browser know,
        // since we most probably were redirected here by a 404
        // error (how the CMS gets index.php to manage most of it's
        // requests) and we want it to look like a valid URL, not a
        // page not found.

        header ( 'HTTP/1.0 200 OK' ); 
      }

  }

/**
 * import parameters in
 */

// merge here because some parameters may have been generated by the
// interception code above

$p = ( isset ( $p ) && is_array ( $p ) ) 
  ? array_merge ( $p, intercept_request_vars() ) 
  : intercept_request_vars();

if ( $m != '' && isset ( $p ) && is_array ( $p ) ) 
  {
    core_set_data ( $m, $p );
  }

if ( ! $m && defined ( 'DEFAULT_PAGE' ) && DEFAULT_PAGE )
  {
    $m = DEFAULT_PAGE;
  }


if ( defined ( 'PRODUCTION' ) && ! PRODUCTION )
  {
    index_log ( "\n" );
    index_log ( date ( 'Y-m-d H:i:s' ) . "\t" . $_SERVER['REQUEST_METHOD'] . "\t" . $_SERVER['REQUEST_URI'] );
    if ( sizeof ( $_REQUEST ) > 0 )
      {
        index_log ( trim ( format_array2str ( $_REQUEST ) ) );
      }
  }


// Module processing

$page['title'] = SITE_NAME;
$page['template'] = 'index';

// list of modules, used to build js and css includes for page head
// after all modules are done, so modules can set load order
$_manifest = false;

$_previous_m = false;

$MORE = 1;
while ( $MORE != 0 && $_previous_m != $m ) 
  {

    if ( defined ( 'PRODUCTION' ) && ! PRODUCTION )
      {
        index_log ( ' --> ' . $m );
      }
  
    $_previous_m = $m;

    core_load( 'include', $m );

    // if the module exists, do it

    if ( core_load( 'module', $m ) ) 
      {

        if ( ! isset ( $page[$m] ) )
          {
            $page[$m] = array();
          }

        $page[$m]['__this'] = $m;

        // determine access function
        $access_func = false;

        if ( function_exists ( 'access_' . $m ) )
          {
            $access_func = 'access_' . $m;
          }

        // determine module function

        if ( function_exists ( 'module_' . $m ) )
          {
            $module_func = 'module_' . $m;
          }
        else
          {
            $module_func = false;
          }

        // if access allowed, execute the module
        if ( function_exists( $access_func ) )
          {
            //is access allowed?
            if ( $access_func ( $page[$m] ) ) 
              {
                // do the module
                $MORE = $module_func ( $page[$m] );
              }
            else 
              {
                // Access denied
                if ( user() ) 
                  {
                    // Logged in user - drop to no access module...
                    core_set_module ( 'no_access' );
                    $MORE = 1;
                  } 
                else
                  { 
                    // Unknown user - save params, go to please log in screen for validation
                    $data = array ( 'target'=>$m, 'params'=>$p );
                    core_set_module ( 'login', $data );
                    $MORE = 1;
                  }
              }
          }
        elseif ( $module_func )
          {
            $MORE = $module_func ( $page[$m] );
          }
        else
          {
            // no module function found that matches $m
            $d = array();
            $d['m'] = $m;
            $d['data'] = $page[$m];
            core_set_module ( 'handler_404', $d );
            $MORE = 1; // go back and run the nothing found module
          }
      }
    else
      {
        // no module file exists for $m
        $d = array();
        $d['m'] = $m;
        $d['data'] = $page[$m];
        core_set_module ( 'handler_404', $d );
        $MORE = 1; // go back and run the nothing found module
      }

    // ensure css and js for the final module is be loaded
    $_manifest[$m] = $m;

  }

/*
 * Parse template, process inline macros (module-widget combinations)
 * and execute main (final $m) and widget
 */

// Get template, parse and process macros
$template = core_template_fetch ( $page['template'] );

/*
 * process inline macro modules
 */

$__macro = array();
$last_end = 0;

while ( $start = strpos ( $template , "<!--#", $last_end ) ) 
  {

    $end = strpos ( $template, "#-->", $last_end) + 3;
    $s = substr ( $template, $last_end, $start - $last_end );
    $last_end = $end + 1;

    // tidy macro and split into args
    $macro_length = $end - $start;
    $macro = substr ( $template, $start , $macro_length + 1 );
    $macro = str_replace ( '<!--#', '', $macro );
    $macro = str_replace ( '#-->', '', $macro );
    $macro = trim ( $macro );

    $args = ''; // clear the args from last time thru

    if ( $macro == 'module' ) 
      {
        continue;
      }

    $args = core_parse_macro ( $macro );
    $macro = substr ( $macro, 0, strpos ( $macro . ' ', ' ' ) );

    // load code if required, get module to do whatever it has to
    // includes are only functions libraries, no code should be
    // executed here

    if ( core_load ( 'macro', $macro ) )
      {

        if ( ! isset ( $__macro[$macro] ) )
          {
            $__macro[$macro] = array();
          }
        $__macro[$macro]['__this'] = $macro;

        // see if css and js for the module should be loaded
        $_manifest[$macro] = $macro;

        $fn = 'macro_' . $macro;

        if ( function_exists ( $fn ) )
          {
            // merge $args with core_data for the module
            if ( is_array ( $args ) )
              {
                foreach ( $args as $k => $v )
                  {
                    $__macro[$macro][$k] = $v;
                  }
              }
            $fn ( $__macro[$macro] );
          }
      }

    $macro = false;

  }

/*
 * output template width macros rendered inline
 */


if ( defined ( 'CONTENT_TYPE' ) ) 
  {
    header( 'Content-type: ' . CONTENT_TYPE );
  }

if ( $_manifest )
  {

    foreach ( $_manifest as $v )
      {

        // skip modules that definately shouldn't be adding stuff

        if ( $v == 'analytics' )
          {
            continue;
          }


        core_head_add ( 'css', $v . '.css' );
        core_head_add ( 'js', $v . '.js' );

      }
  }

$last_end = 0;
while ( $start = strpos ( $template , "<!--#", $last_end ) ) 
  {

    $end = strpos ( $template, "#-->", $last_end) + 3;
    $s = substr ( $template, $last_end, $start - $last_end );
    if ( $s != '' && $s != ' ' )
      {
        echo $s;
      }
    $last_end = $end + 1;

    // tidy macro and split into args
    $macro_length = $end - $start;
    $macro = substr ( $template, $start , $macro_length + 1 );
    $macro = str_replace ( '<!--#', '', $macro );
    $macro = str_replace ( '#-->', '', $macro );
    $macro = trim ( $macro );

    $args = ''; // clear the args from last time thru

    if ( $macro == 'module' ) 
      {

        // do the widget for the main module in the page
        $macro = $m;
        $widget_func = 'widget_' . $macro;

        if ( core_load ( 'widget', $macro ) && function_exists ( $widget_func ) ) 
          {
            $widget_func ( $page[$macro] );
          }

      }
    else
      {

        $args = core_parse_macro ( $macro );
        $macro = substr ( $macro, 0, strpos ( $macro . ' ', ' ' ) );
		  
        if ( isset ( $__macro[$macro] ) )
          {

            $fn = 'render_' . $macro;

            if ( function_exists ( $fn ) ) 
              { 

                // merge $args with core_data for the macro -- there may
                // be more than one instance of a macro in a page, so do
                // this again here

                if ( is_array ( $args ) )
                  {
                    foreach ( $args as $k => $v )
                      {
                        $__macro[$macro][$k] = $v;
                      }
                  }
                $fn ( $__macro[$macro] );
              }
          }

        $macro = false;
        $access_func = false;

      }

  }

// emit remainder of the template
echo substr ( $template, $last_end );

// logging
if ( defined ( 'PRODUCTION' ) && ! PRODUCTION )
  {

    index_log ( '' );

    if ( $__index_log_handle )
      {
        fclose ( $__index_log_handle );
      }
	  
  }


function index_log ( $s = false )
{

  if ( ! $s )
    {
      return false;
    }

  if ( ! ( defined ( 'PRODUCTION' ) && ! PRODUCTION ) )
    {
      return false;
    }

  global $__index_log_handle;
	  
  if ( ! $__index_log_handle )
    {
      $__index_log_handle = @fopen ( SITE_ROOT . 'log/index.log', 'a+' );
    }
	  
  if ( $__index_log_handle )
    {
      if ( is_array ( $s ) )
        {
          fwrite ( $__index_log_handle, format_array2str ( $s ) . "\n" );
        }
      else
        {
          fwrite ( $__index_log_handle, $s . "\n" );
        }
    }

}

?>
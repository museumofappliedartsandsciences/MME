<?php

/**
 * return information about  - a specific field $f if requested,
 * otherwise the entire array 
 */
function user ( $f = false ) 
{ 

  global $__USER; 
  
  if ( ! $__USER ) 
    {
      return false;
    } 

  if ( $f ) 
    {
      return ( $__USER[$f] );
    }

  return $__USER;
 
}

/**
 * erase current user data
 */
function user_clear () 
{
  global $__USER;
  unset ( $__USER );
}

/**
 * load requested user as current user
 */
function user_init ( $user_id = false ) 
{

  if ( ! $user_id && ! user() )
    {
      return false;
    }

  if ( ! $user_id && user() )
    {
      $user_id = user('user_id');
    }

  global $__USER;
  $sql = "SELECT * FROM user WHERE user_id=" . $user_id . "";
  $__USER = db_exec_one ( $sql );

}

function user_validate_login ( $username, $password ) 
{

  if ( $username == '' || $password == '' )
    {
      return false;
    }

  $sql = "SELECT user_id FROM user ";
  $sql .= " WHERE username='" . addslashes ( $username ) . "' ";
  $sql .= " AND password='" . addslashes ( md5 ( $password ) ) . "' ";
  $user_id = db_exec_one ( $sql, 'user_id' );

  if ( ! $user_id )
    {
      return false;
    }

  return true;

}

function user_login ( $username, $password ) 
{

  if ( ! user_validate_login ( $username, $password ) )
    {
      return false;
    }

  $sql = "SELECT user_id FROM user ";
  $sql .= " WHERE username='" . addslashes ( $username ) . "' ";
  $sql .= " AND password='" . addslashes ( md5 ( $password ) ) . "' ";
  $user_id = db_exec_one ( $sql, 'user_id' );

  if ( ! $user_id )
    {
      return false;
    }

  session_set ( 'user_id', $user_id );
  session_set ( 'logged_in', 1 );

  user_init ( $user_id );
  return true;

}

function user_logout () 
{

  session_set ( 'logged_in', '0' );
  user_clear();

}


function user_admin ( $user = false ) 
{

  if ( user() && user('admin') == '1' )
    {
      return true;
    }

  return false;

}

?>

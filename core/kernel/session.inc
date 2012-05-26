<?php

function session ( $f = false ) 
{

  // $f is field key to return, otherwise entire session array returned.

  global $__SESSION; 
  
  if ( ! $__SESSION ) 
    {
      return false;
    }

  if ( $f ) 
    {
      return ( $__SESSION[$f] );
    }

  return $__SESSION;
  
}

function session_set ( $key, $value ) 
{

  global $__SESSION;
  $__SESSION[$k] = $v;
  $sql = "UPDATE session SET " . $key . "='" . addslashes ( $value ) . "' WHERE cookie = '" . addslashes ( $__SESSION['cookie'] ) . "'";
  db_exec ( $sql );

}

function session_init() 
{
  
  global $__SESSION;

  // is there a persistant session cookie?
  if ( isset ( $_COOKIE['p' . COOKIE_NAME] ) ) 
    {

      // get session details
      $sql = "SELECT * FROM session WHERE cookie = '" . addslashes ( $_COOKIE['p' . COOKIE_NAME] ) . "'";
      $__SESSION = db_exec_one ( $sql );

      if ( $__SESSION ) 
        {
          // reset persistent cookie
          setcookie ( 'p' . COOKIE_NAME, $__SESSION['cookie'], time() + 9999999, '/', COOKIE_URL );
        }
      else
        {
          // if no record in db (eg after a purge), clear session and
          // cookies and we'll issue a new one
          unset ( $__SESSION );
          unset ( $_COOKIE['p' . COOKIE_NAME] );
          unset ( $_COOKIE['t' . COOKIE_NAME] );
          setcookie ( 'p' . COOKIE_NAME, '', 0, '/', COOKIE_URL );
          setcookie ( 't' . COOKIE_NAME, '', 0, '/', COOKIE_URL );
        }

    }
  else
    {

      // persistent cookie not present - first visit generate and
      // issue session ID, set up default session settings and store
      // in db
      $__SESSION = array();
      $__SESSION['cookie'] = md5 ( uniqid( time() ) );
      $__SESSION['remember'] = 1;
      $__SESSION['user_id'] = '0';
      $__SESSION['last_activity'] = time();
      $__SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
      $__SESSION['visit_count'] = 1; // count this visit
	
      setcookie ( 'p' . COOKIE_NAME, $__SESSION['cookie'], time() + 9999999, '/', COOKIE_URL ); // track you long time GI
      $__SESSION['session_id'] = db_unique_id ( 'session' );

      $sql = '';
      $sql .= "INSERT INTO session ";
      $sql .= " ( session_id, user_id, cookie, remember, last_activity, remote_addr, visit_count ) ";
      $sql .= " VALUES ( ";
      $sql .= $__SESSION['session_id'] . ", ";
      $sql .= $__SESSION['user_id'] . ", ";
      $sql .= "'" . $__SESSION['cookie'] . "', ";
      $sql .= "'" . $__SESSION['remember'] . "', ";
      $sql .= "'" . $__SESSION['last_activity'] . "', ";
      $sql .= "'" . $__SESSION['remote_addr'] . "', ";
      $sql .= "1";
      $sql .= " ) ";
      db_exec ( $sql );

    }

  if ( isset ( $_COOKIE[ 't' . COOKIE_NAME] ) )
    {

      // just mark time of last activity
      $sql = "UPDATE session ";
      $sql .= " SET last_activity = " . time() . " ";
      $sql .= " WHERE cookie = '" . $__SESSION['cookie'] . "'";

    }
  else
    {

      // temporary cookie not present - issue fresh cookie and record
      // in db, write updated info to db, date of this visit, increase
      // visit count
	
      $__SESSION['last_activity'] = time();
      $__SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
      $__SESSION['visit_count'] ++;
	
      // if the session is associated with a user check it's remember
      // setting and take appropriate action
	  
      switch ( $__SESSION['remember'] ) 
        {

        case 1 :
          // remember username, but log out when current session goes
          // - since a new session has just been created, set to not
          // logged in.
          $__SESSION['logged_in'] = false;
          break;

        case 2 :
          // keep logged in indefinitely from their computer - nothing
          // to do, the session is already logged in and has user_id
          break;

        default :
          // keep logged in only for browser session and don't
          // remember username
          $__SESSION['logged_in'] = false;
          $__SESSION['user_id'] = '0';
          $__SESSION['remember'] = 0;
          break;	
		
        }

      if ( 0 == $__SESSION['user_id'] )
        {
          $__SESSION['user_id'] = 0;
        }

      setcookie ( 't' . COOKIE_NAME, $__SESSION['cookie'],  0, '/', COOKIE_URL );

      $sql = '';
      $sql .= "UPDATE session SET ";
      $sql .= "user_id=" . $__SESSION['user_id'] . ", ";
      $sql .= "last_activity='" . $__SESSION['last_activity'] . "', ";
      $sql .= "logged_in='" . $__SESSION['logged_in'] . "', ";
      $sql .= "remote_addr='" . $__SESSION['remote_addr'] . "', "; 
      $sql .= "visit_count='" . $__SESSION['visit_count'] . "', ";
      $sql .= "remember=" . $__SESSION['remember'] . " ";
      $sql .= "WHERE cookie='" . addslashes ( $__SESSION['cookie'] ) . "' ";
      db_exec ( $sql );

    }

  // if the session is associated with a user and they're logged in,
  // load base user data and setup user environment

  if ( $__SESSION['logged_in'] )
    {
      // load userdata into global user array ( accessible via user() function )
      user_init ( session ( 'user_id' ) );
    }		
  
}

function session_expired_clear() 
{

  // remove all non-logged in sessions older than 30 days from the
  // session database

  $t = time() - ( 60 * 60 * 24 * 30 ); // 30 days ago
  $sql = "DELETE FROM session ";
  $sql .= " WHERE last_activity < " . $t . "";
  // $sql .= " AND logged_in != 1 ";
  db_exec ( $sql );

}		

?>
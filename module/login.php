<?php

core_load ( 'include', 'oai' );
 
function module_login ( &$data ) 
{

  if ( user () ) 
    {
      core_set_module ( DEFAULT_PAGE );
      return 1;
    }


  if ( $data['__key'] == 'password' )
    {
      $data['view'] = 'password';
    }

  if ( $data['view'] == 'password_process' )
    {

      $data['email'] = trim ( $data['email'] );
      $data['email'] = trim ( $data['email'], ' ' );
	  
      if ( $data['email'] && $data['email'] != '' ) 
        {

          $sql = "SELECT user_id FROM user ";
          $sql .= " WHERE ( email = '" . addslashes ( $data['email'] ) . "' AND username != '' ) ";
          $sql .= " OR ( username = '" . addslashes ( $data['email'] ) . "' AND password != '' ) ";
          $sql .= " GROUP BY user_id ";
		  
          $u = db_exec ( $sql, 'user_id' );

          if ( $u )
            {
              foreach ( $u as $k => $v )
                {
                  login_password_reset ( $v['user_id'] );
                }

              core_set_data ( 'title', 'Password Changed' ) ;
              $data['view'] = 'password_processed';

            }
          else
            {
              $data['view'] = 'password';
              $data['error'] = 'no_match';
            }
        }
    }

  if ( ! $data['view'] && $_SERVER['REQUEST_METHOD'] == 'POST' ) 
    {


      if ( $data['username'] == '' ) 
        {
          $data['error']['username'] = 'Please enter your username';
        }

      if ( $data['password'] == '' ) 
        {
          $data['error']['password'] = 'Please enter your password';
        }

      if ( ! user_login ( $data['username'], $data['password'] ) )
        { 
          $data['error']['failed'] = true;
        }
	  
      if ( ! $data['error'] )
        {
	  
          // login is ok - work out what page to show them and send
          // them there

          if ( ! isset ( $data['remember'] ) )
            {
              // if remember is not ticked
              $data['remember'] = '0';
            }

          session_set( 'remember', $data['remember'] );

          if ( $data['target'] || $data['params'] ) 
            {
              // shortcut param
              if ( strpos ( $data['target'], '?' ) )
                {
                  $t = $data['target'];
                  $data['target'] = substr ( $data['target'], 0, strpos ( $t, '?' ) );
                  $data['params']['__key'] = substr ( $t, strpos ( $t, '?' ) + 1 );
                  core_set_module ( $data['target'], $data['params'] );
                  return 1;
                }

              if ( is_array ( $data['params'] ) )
                {
                  core_set_module ( $data['target'], $data['params'] );
                  return 1;
                }
 
              // retrieve serialized pre-login vars
              $params = unserialize ( urldecode ( $data['params'] ) );

              if ( core_module_exists ( $data['target'] ) )
                {
                  core_set_module ( $data['target'], $params );
                }
              else
                {
                  core_set_module ( 'page', array ( '__key' => $data['target'] ) );
                }

              return 1;

            }
          else 
            {
              core_set_module ( DEFAULT_PAGE );
              return 1;
            }
        }

    }

  core_set_template ( 'index' );

  $data['focus'] = ( $data['username'] ) 
    ? 'password' 
    : 'username';

  core_head_add ( 'jquery' );

  $s = '';
  $s .= '<script language="javascript" type="text/javascript">' . "\n";;
  $s .= '$(document).ready(function() { ';
  $s .=  ' $(\'#login-' .$data['focus'] . '\').select();' . "\n";
  $s .= '});' . "\n";
  $s .=  '</script>';
  core_head_add ( $s );

  core_set_data ( 'title', 'Log In' );

}


function login_password_reset ( $user_id ) 
{

  
  $u = oai_user ( $user_id );

  srand ((double) microtime() * 1000000);
	
  // try and generate one from the system
  $password = '';
  $password = @exec ( '/usr/bin/pwgen -n' );

  // if pwgen wasn't installed
  while ( strlen( $password ) < 6 ) 
    {
      switch ( rand ( 1,3 ) ) 
        {
        case 1:
          $password .= chr (rand(50,56));
          break;

        case 2:
        case 3:
          $password .= chr (rand(97,122));
          break;
        }
      $password = trim ( $password, '1liIoO05sS' ); // remove confusing characters
    }

  $sql = "UPDATE user ";
  $sql .= " SET ";
  $sql .= " password = '" . addslashes ( md5 ( $password ) ) . "' ";
  $sql .= " WHERE user_id = " . $user_id . " ";
  db_exec ( $sql );

  $subject = SITE_NAME .': New Password';
  $s = 'Here is your new password for ' . SITE_NAME . "\n";

  $s .= "\n";
  $s .= 'Username: ' . $u['username']  . "\n";
  $s .= 'Password: ' . $password . "\n";
  $s .= "\n";

  $s .= 'To log in, click here' . "\n";
  $s .= html_url ( 'login', array ( '__absolute' => true ) );

  if ( $u['name_first'] || $u['name_last'] )
    {
      $email = trim ( $u['name_first'] . ' ' . $u['name_last'] ) . ' <' . $u['email'] . '>';
    }
  else
    {	
      $email = $u['email'];
    }

  @mail ( 
         $email, 
         'New Password for ' . SITE_NAME, 
         $s,
         "From: " . EMAIL_WEBMASTER,
         '-f' . EMAIL_WEBMASTER_ADDRESS
          );

  return true;

}

?>
<?php

core_load ( 'include', 'oai.inc' );

function access_user_edit ( &$data )
{

  return user_admin();

}

function module_user_edit ( &$data )
{

  if ( $data['view'] == 'update' && oai_user_validate ( $data['user'] ) )
    {

      $user = $data['user'];

      if ( $user['user_id'] && isset ( $user['password'] ) && $user['password'] != '' )
        {
          $sql = "UPDATE user ";
          $sql .= " SET ";
          $sql .= " password = '" . addslashes ( md5 ( $user['password'] ) ) . "' ";
          $sql .= " WHERE user_id = " . $user['user_id'] . " ";
          db_exec ( $sql );
        }

      if ( ! $user['user_id'] )
        {
          $user['user_id'] = db_unique_id ( 'user' );
          $sql = "INSERT INTO user ";
          $sql .= " ( user_id ) ";
          $sql .= " VALUES ";
          $sql .= " ( " . $user['user_id'] . " )";
          db_exec ( $sql );

          if ( isset ( $user['password'] ) )
            {
              $sql = "UPDATE user ";
              $sql .= " SET ";
              $sql .= " username = '" . addslashes ( $user['username'] ) . "', ";
              $sql .= " slug = '" . addslashes ( $user['username'] ) . "', ";
              $sql .= " password = '" . addslashes ( md5 ( $user['password'] ) ) . "' ";
              $sql .= " WHERE user_id = " . $user['user_id'] . " ";
              db_exec ( $sql );
            }

        }


      $user['admin'] = ( $user['admin'] == '1' )
        ? '1'
        : '0';

      $user['national'] = ( $user['national'] == '1' )
        ? '1'
        : '0';

      $old = oai_user ( $user['user_id'] );

      $sql = "UPDATE user ";
      $sql .= " SET ";
      $sql .= " title = '" . addslashes ( $user['title'] ) . "', ";
      $sql .= " description = '" . addslashes ( $user['description'] ) . "', ";
      $sql .= " title_alternate = '" . addslashes ( $user['title_alternate'] ) . "', ";
      $sql .= " title_abbreviated = '" . addslashes ( $user['title_abbreviated'] ) . "', ";
      $sql .= " national = '" . addslashes ( $user['national'] ) . "', ";
      $sql .= " slug = '" . addslashes ( $old['username'] ) . "', ";
      $sql .= " email = '" . addslashes ( $user['email'] ) . "', ";
      $sql .= " email_contact = '" . addslashes ( $user['email_contact'] ) . "', ";

      $sql .= " address_street = '" . addslashes ( $user['address_street'] ) . "', ";
      $sql .= " address_city = '" . addslashes ( $user['address_city'] ) . "', ";
      $sql .= " address_state = '" . addslashes ( $user['address_state'] ) . "', ";
      $sql .= " address_postcode = '" . addslashes ( $user['address_postcode'] ) . "', ";
      $sql .= " address_country = '" . addslashes ( $user['address_country'] ) . "', ";

      $sql .= " postal_street = '" . addslashes ( $user['postal_street'] ) . "', ";
      $sql .= " postal_city = '" . addslashes ( $user['postal_city'] ) . "', ";
      $sql .= " postal_state = '" . addslashes ( $user['postal_state'] ) . "', ";
      $sql .= " postal_postcode = '" . addslashes ( $user['postal_postcode'] ) . "', ";
      $sql .= " postal_country = '" . addslashes ( $user['postal_country'] ) . "', ";

      $sql .= " phone = '" . addslashes ( $user['phone'] ) . "', ";
      $sql .= " url = '" . addslashes ( $user['url'] ) . "', ";	
      $sql .= " oai_url = '" . addslashes ( $user['oai_url'] ) . "', ";
      $sql .= " admin = '" . addslashes ( $user['admin'] ) . "' ";
      $sql .= " WHERE user_id = " . $user['user_id'] . " ";
      db_exec ( $sql );


      core_set_module ( 'oai', array ( 'user_id' => $data['user']['user_id'] ) );
      return true;
    }

  if ( $data['user_id'] && is_numeric ( $data['user_id'] ) )
    {
      $data['user'] = oai_user ( $data['user_id'] );
    }

  core_head_add ( 'jquery' );
  core_head_add ( 'oai.css' );

}

function oai_user_validate ( &$user )
{

  $user['__error'] = false;

  if ( ! $user['title'] )
    {
      $user['__error']['title'] = true;
    }

  if ( ! $user['email'] || ! validate_email ( $user['email'] ) )
    {
      $user['__error']['email'] = true;
    }

  if ( ! $user['user_id'] )
    {
      if ( ! $user['username'] )
        {
          $user['__error']['username'] = true;
        }

      $sql = "SELECT * FROM user where username='" . addslashes ( $user['username'] ) . "'";
      $r = db_exec_one ( $sql );
      if ( $r )
        {
          $user['__error']['username'] = 'The username you selected is already in use';;
        }

      if ( ! $user['password'] )
        {
          $user['__error']['password'] = true;
        }
    }

  return ( $user['__error'] )
    ? false
    : true;

}

?>
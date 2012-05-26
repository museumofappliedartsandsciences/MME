<?php
 
function access_account ( $data ) 
{

  return ( user() )
    ? true
    : false;

}
function module_account ( &$data ) 
{
 
  // process incoming views
  if ( $data['view'] == 'update' && account_validate ( $data['user'] ) )
    {

      // all OK, update and go back to options

      $user_id = user('user_id');

      $sql = "UPDATE user ";
      $sql .= " SET ";
      if ( $data['user']['password']['new'] )
        {
          $sql .= " password = '" . addslashes ( md5 ( $data['user']['password']['new'] ) ) . "', ";
        }	
      $sql .= " title = '" . addslashes ( $data['user']['title'] ) . "', ";
      $sql .= " title_alternate = '" . addslashes ( $data['user']['title_alternate'] ) . "', ";
      $sql .= " title_abbreviated = '" . addslashes ( $data['user']['title_abbreviated'] ) . "', ";
      $sql .= " description = '" . addslashes ( $data['user']['description'] ) . "', ";
      $sql .= " email = '" . addslashes ( $data['user']['email'] ) . "', ";
      $sql .= " email_contact = '" . addslashes ( $data['user']['email_contact'] ) . "', ";

      $sql .= " address_street = '" . addslashes ( $data['user']['address_street'] ) . "', ";
      $sql .= " address_city = '" . addslashes ( $data['user']['address_city'] ) . "', ";
      $sql .= " address_state = '" . addslashes ( $data['user']['address_state'] ) . "', ";
      $sql .= " address_postcode = '" . addslashes ( $data['user']['address_postcode'] ) . "', ";
      $sql .= " address_country = '" . addslashes ( $data['user']['address_country'] ) . "', ";

      $sql .= " postal_street = '" . addslashes ( $data['user']['postal_street'] ) . "', ";
      $sql .= " postal_city = '" . addslashes ( $data['user']['postal_city'] ) . "', ";
      $sql .= " postal_state = '" . addslashes ( $data['user']['postal_state'] ) . "', ";
      $sql .= " postal_postcode = '" . addslashes ( $data['user']['postal_postcode'] ) . "', ";
      $sql .= " postal_country = '" . addslashes ( $data['user']['postal_country'] ) . "', ";

      $sql .= " phone = '" . addslashes ( $data['user']['phone'] ) . "', ";
      $sql .= " url = '" . addslashes ( $data['user']['url'] ) . "', ";
      $sql .= " oai_url = '" . addslashes ( $data['user']['oai_url'] ) . "', ";
      $sql .= " oai_harvest_auto = '" . $data['user']['oai_harvest_auto'] . "' ";
      $sql .= " WHERE user_id = " . user('user_id') . " ";

      db_exec ( $sql );

      user_init ( user('user_id') );

      core_set_module ( 'oai', array ( 'user_id'=>user('user_id') ) );
      return 1;

    } 

  if ( ! $data['view'] ) 
    {
      $data['user'] = user();
      unset ( $data['user']['password'] );
    }

}

function account_validate ( &$user )
{

  if ( $user['email'] == '' ) 
    {
      $user['__error']['email'] = 'Please enter your email address';
    } 
  elseif ( ! validate_email ( $user['email'] ) ) 
    {
      $user['__error']['email'] = 'Please enter a valid email address';
    } 

  if ( $user['password']['current'] )
    {

      if ( ! $user['password']['current'] )
        {
          $user['__error']['password']['current'] = 'Enter your current password';
        } 
      elseif ( ! user_validate_login ( user('username'), $user['password']['current'] ) )
        {
          $user['__error']['password']['current'] = 'Enter your current password';
        } 

      if ( ! $user['password']['new'] )
        {
          $user['__error']['password']['new'] = 'Please choose a new password';
        } 

      if ( ! validate_password ( $user['password']['new'] ) )
        {
          $user['__error']['password']['new'] = 'Enter a password at least 6 characters long, containing a mix of numbers and letters';
        } 

      if ( ! $user['password']['confirm'] || $user['password']['confirm'] != $user['password']['new'] )
        {
          $user['__error']['password']['confirm'] = 'Confirm your new password';
        } 

    }

  $user['oai_harvest_auto'] = ( $user['oai_harvest_auto'] == '1' )
    ? '1'
    : '0';

  return ( $user['__error'] )
    ? false
    : true;

}

?>
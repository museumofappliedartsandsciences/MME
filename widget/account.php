<?php

function widget_account ( &$data )
{

  if ( $data['view'] == 'updated' )
    {
      account_updated ( $data );
    }
  else
    {
      account_form ( $data );
    } 

}


function account_updated ( $data )
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Change your Account';
  echo '</h2>';

  echo '<p>';
  echo 'Your account has been updated.';
  echo '</p>';
  
  echo core_form_start ( $data['__this'] );
  echo '<input type="submit" value="OK" />';
  echo '</form>';

  echo '</div>';

}


function account_form ( $data ) 
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Change your Account';
  echo '</h2>';

  echo core_form_start ( $data['__this'], array ( 'view'=>'update' ) );

  echo '<table>';

  echo '<tr>';
  echo '<td colspan="2">';
  echo '<p>';
  echo '<strong>';
  echo 'Update your details';
  echo '</strong>';
  echo '</p>';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['title'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['title'];
      echo '</span>';
    }
  else
    {
      echo 'Organisation';
      echo '<span class="alert">*</span>';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userTitle" value="' . $data['user']['title'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['title_alternate'] )
    {
      echo '<span class="alert">';
      echo 'Alternate Name';
      echo '</span>';
    }
  else
    {
      echo 'Alternate Name';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userTitle_alternate" value="' . $data['user']['title_alternate'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['title_abbreviated'] )
    {
      echo '<span class="alert">';
      echo 'Abbreviated Name';
      echo '</span>';
    }
  else
    {
      echo 'Abbreviated Name';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userTitle_abbreviated" value="' . $data['user']['title_abbreviated'] . '" />';
  echo '</td>';
  echo '</tr>';



  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['email'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['email'];
      echo '</span>';
    }
  else
    {
      echo 'Account Email';
      echo '<span class="alert">*</span>';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userEmail" value="' . $data['user']['email'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['email_contact'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['email_contact'];
      echo '</span>';
    }
  else
    {
      echo 'Contact Email';
      echo '<span class="alert">*</span>';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userEmail_contact" value="' . $data['user']['email_contact'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['phone'] )
    {
      echo '<span class="alert">';
      echo 'Phone';
      echo '</span>';
    }
  else
    {
      echo 'Phone';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPhone" id="userPhone" value="' . $data['user']['phone'] . '" size="48" maxlength="255" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['url'] )
    {
      echo '<span class="alert">';
      echo 'Website URL';
      echo '</span>';
    }
  else
    {
      echo 'Website URL';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userUrl" id="userUrl" value="' . $data['user']['url'] . '" size="48" maxlength="255" />';
  echo '</td>';
  echo '</tr>';



  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['description'] )
    {
      echo '<span class="alert">';
      echo 'Description';
      echo '</span>';
    }
  else
    {
      echo 'Description';
    }
  echo '</td>';
  echo '<td>';
  echo '<textarea name="userDescription" id="userDescription">' . $data['user']['description'] . '</textarea>';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['address_street'] )
    {
      echo '<span class="alert">';
      echo 'Street';
      echo '</span>';
    }
  else
    {
      echo 'Street';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userAddress_street" id="userAddress_street" value="' . $data['user']['address_street'] . '" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['address_city'] )
    {
      echo '<span class="alert">';
      echo 'City';
      echo '</span>';
    }
  else
    {
      echo 'City';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userAddress_city" id="userAddress_city" value="' . $data['user']['address_city'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['address_state'] )
    {
      echo '<span class="alert">';
      echo 'State';
      echo '</span>';
    }
  else
    {
      echo 'State';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userAddress_state" id="userAddress_state" value="' . $data['user']['address_state'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['address_postcode'] )
    {
      echo '<span class="alert">';
      echo 'Postcode';
      echo '</span>';
    }
  else
    {
      echo 'Postcode';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userAddress_postcode" id="userAddress_postcode" value="' . $data['user']['address_postcode'] . '" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['address_country'] )
    {
      echo '<span class="alert">';
      echo 'Country';
      echo '</span>';
    }
  else
    {
      echo 'Country';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userAddress_country" id="userAddress_country" value="' . $data['user']['address_country'] . '" />';
  echo '</td>';
  echo '</tr>';



  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['postal_street'] )
    {
      echo '<span class="alert">';
      echo 'Street';
      echo '</span>';
    }
  else
    {
      echo 'Street';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPostal_street" id="userPostal_street" value="' . $data['user']['postal_street'] . '" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['postal_city'] )
    {
      echo '<span class="alert">';
      echo 'City';
      echo '</span>';
    }
  else
    {
      echo 'City';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPostal_city" id="userPostal_city" value="' . $data['user']['postal_city'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['postal_state'] )
    {
      echo '<span class="alert">';
      echo 'State';
      echo '</span>';
    }
  else
    {
      echo 'State';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPostal_state" id="userPostal_state" value="' . $data['user']['postal_state'] . '" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['postal_postcode'] )
    {
      echo '<span class="alert">';
      echo 'Postcode';
      echo '</span>';
    }
  else
    {
      echo 'Postcode';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPostal_postcode" id="userPostal_postcode" value="' . $data['user']['postal_postcode'] . '" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['postal_country'] )
    {
      echo '<span class="alert">';
      echo 'Country';
      echo '</span>';
    }
  else
    {
      echo 'Country';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userPostal_country" id="userPostal_country" value="' . $data['user']['postal_country'] . '" />';
  echo '</td>';
  echo '</tr>';




  echo '<tr>';
  echo '<td>';
  if ( $data['user']['__error']['oai_url'] )
    {
      echo '<span class="alert">';
      echo 'OAI Harvest URL';
      echo '</span>';
    }
  else
    {
      echo 'OAI Harvest URL';
    }
  echo '</td>';
  echo '<td>';
  echo '<input type="text" name="userOai_url" id="userOai_url" value="' . $data['user']['oai_url'] . '" size="48" maxlength="127" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';
  echo 'Enable Automatic Harvesting';
  echo '</td>';
  echo '<td>';
  echo '<input type="checkbox" name="userOai_harvest_auto" id="userOai_harvest_auto" value="1" ' . ( ( $data['user']['oai_harvest_auto'] == '1' ) ? 'checked="checked"' : '' ) . ' />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td colspan="2">';
  echo '<p>';
  echo '<strong>';
  echo 'Complete the fields below to change your password';
  echo '</strong>';
  echo '</p>';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';

  if ( $data['user']['__error']['password']['current'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['password']['current'];
      echo '</span>';
    }
  else
    {
      echo 'Current Password';
    }


  echo '</td>';
  echo '<td>';
  echo '<input type="password" name="userPasswordCurrent" value="' . $data['user']['password']['current'] . '" autocomplete="off" />';
  echo '</td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>';

  if ( $data['user']['__error']['password']['new'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['password']['new'];
      echo '</span>';
    }
  else
    {
      echo 'New Password';
    }

  echo '</td>';
  echo '<td>';
  echo '<input type="password" name="userPasswordNew" value="' . $data['user']['password']['new'] . '" autocomplete="off" />';
  echo '</td>';
  echo '</tr>';

  echo "\n";  
  echo "\n";  


  echo '<tr>';
  echo '<td>';

  if ( $data['user']['__error']['password']['confirm'] )
    {
      echo '<span class="alert">';
      echo $data['user']['__error']['password']['confirm'];
      echo '</span>';
    }
  else
    {
      echo 'Confirm Password';
    }

  echo '</td>';
  echo '<td>';
  echo '<input type="password" name="userPasswordConfirm" value="' . $data['user']['password']['confirm'] . '" autocomplete="off" />';
  echo '</td>';
  echo '</tr>';


  echo '<tr>';
  echo '<td>';
  echo '</td>';
  echo '<td>';
  echo '<input type="submit" value="Save Changes" />';
  echo '</td>';
  echo '</form>';
  echo '</tr>';

  echo '</table>';

  echo '</div>';

}

?>
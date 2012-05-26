<?php

core_load ( 'widget', 'oai' );

function widget_user_edit ( $data )
{
  oai_path ( $data );
  user_edit_form ( $data );
}

function user_edit_form ( $data )
{

  echo '<div class="formbox">';

  echo core_form_start ( $data['__this'], array ( 'view'=>'update' ) ); 

  echo '<input type="hidden" name="userUser_id" value="' . $data['user']['user_id'] . '" />';

  echo '<p>';
  echo '<label for="userUsername">';
  if ( $data['user']['__error']['username'] )
    {
      echo '<span class="alert">';
      echo 'Username';
      echo '</span>';
    }
  else
    {
      echo 'Username';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';

  if ( ! $data['user']['user_id'] )
    {
      echo '<input type="text" name="userUsername" id="userUsername" value="' . $data['user']['username'] . '" size="48" maxlength="127" />';
    }
  else
    {
      echo '<b>' . $data['user']['username'] . '</b>';
    }

  echo '</p>';
  
  if ( $data['user']['user_id'] )
    {
      echo '<p>';
      echo '<label for="userPassword">';
      echo 'Set New Password';
      echo '</label>';
      echo '<input type="text" name="userPassword" id="userPassword" value="" size="48" maxlength="127" style="width: 8em;" />';
      echo '</p>';
    }
  else
    {
      echo '<p>';
      echo '<label for="userPassword">';
      if ( $data['user']['__error']['password'] )
        {
          echo '<span class="alert">';
          echo 'Password';
          echo '</span>';
        }
      else
        {
          echo 'Password';
          echo '<span class="alert">*</span>';
        }
      echo '</label>';
      echo '<input type="text" name="userPassword" id="userPassword" value="' . $data['user']['password'] . '" size="48" maxlength="127" />';
      echo '</p>';
    }


  echo '<p>';
  echo '<label for="userTitle">';
  if ( $data['user']['__error']['title'] )
    {
      echo '<span class="alert">';
      echo 'Organisation';
      echo '</span>';
    }
  else
    {
      echo 'Organisation';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="userTitle" id="userTitle" value="' . $data['user']['title'] . '" size="48" maxlength="127" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userTitle_alternate">';
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
  echo '</label>';
  echo '<input type="text" name="userTitle_alternate" id="userTitle_alternate" value="' . $data['user']['title_alternate'] . '" size="48" maxlength="127" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userTitle_abbreviated">';
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
  echo '</label>';
  echo '<input type="text" name="userTitle_abbreviated" id="userTitle_abbreviated" value="' . $data['user']['title_abbreviated'] . '" size="48" maxlength="127" />';
  echo '</p>';


  echo '<p>';
  echo '<label for="userEmail">';
  if ( $data['user']['__error']['email'] )
    {
      echo '<span class="alert">';
      echo 'Account Email';
      echo '</span>';
    }
  else
    {
      echo 'Account Email';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="userEmail" id="userEmail" value="' . $data['user']['email'] . '" size="48" maxlength="127" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userEmail_contact">';
  if ( $data['user']['__error']['email_contact'] )
    {
      echo '<span class="alert">';
      echo 'Contact Email';
      echo '</span>';
    }
  else
    {
      echo 'Email_Contact';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="userEmail_contact" id="userEmail_contact" value="' . $data['user']['email_contact'] . '" size="48" maxlength="127" />';
  echo '</p>';


  echo '<p>';
  echo '<label for="userPhone">';
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
  echo '</label>';
  echo '<input type="text" name="userPhone" id="userPhone" value="' . $data['user']['phone'] . '" size="48" maxlength="255" />';
  echo '</p>';



  echo '<p>';
  echo '<label for="userUrl">';
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
  echo '</label>';
  echo '<input type="text" name="userUrl" id="userUrl" value="' . $data['user']['url'] . '" size="48" maxlength="255" />';
  echo '</p>';




  echo '<p>';
  echo '<label for="userDescription">';
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
  echo '</label>';
  echo '<textarea name="userDescription" id="userDescription">' . $data['user']['description'] . '</textarea>';
  echo '</p>';


  echo '<p>';
  echo '<label for="userAddress_street">';
  if ( $data['user']['__error']['street'] )
    {
      echo '<span class="alert">';
      echo 'Street';
      echo '</span>';
    }
  else
    {
      echo 'Street';
    }
  echo '</label>';
  echo '<input type="text" name="userAddress_street" id="userAddress_street" value="' . $data['user']['address_street'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userAddress_city">';
  if ( $data['user']['__error']['city'] )
    {
      echo '<span class="alert">';
      echo 'City';
      echo '</span>';
    }
  else
    {
      echo 'City';
    }
  echo '</label>';
  echo '<input type="text" name="userAddress_city" id="userAddress_city" value="' . $data['user']['address_city'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userAddress_state">';
  if ( $data['user']['__error']['state'] )
    {
      echo '<span class="alert">';
      echo 'State';
      echo '</span>';
    }
  else
    {
      echo 'State';
    }
  echo '</label>';
  echo '<select name="userAddress_state" id="userAddress_state">';
  
  echo '<option value="">';
  echo 'Select...';
  echo '</option>';
  foreach ( array ( 'New South Wales', 'Victoria', 'Queensland', 'South Australia', 'Western Australia', 'Tasmania', 'Northern Territory', 'Australian Capital Territory' ) as $state )
    {
      echo '<option value="' . $state . '" ' .  ( ( $data['user']['address_state'] == $state ) ? ' selected="selected"' : '' ) . '>';
      echo $state;
      echo '</option>';
    }
  echo '</select>';
  //  echo '<input type="text" name="userAddress_state" id="userAddress_state" value="' . $data['user']['address_state'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userAddress_postcode">';
  if ( $data['user']['__error']['postcode'] )
    {
      echo '<span class="alert">';
      echo 'Postcode';
      echo '</span>';
    }
  else
    {
      echo 'Postcode';
    }
  echo '</label>';
  echo '<input type="text" name="userAddress_postcode" id="userAddress_postcode" value="' . $data['user']['address_postcode'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userAddress_country">';
  if ( $data['user']['__error']['country'] )
    {
      echo '<span class="alert">';
      echo 'Country';
      echo '</span>';
    }
  else
    {
      echo 'Country';
    }
  echo '</label>';
  echo '<input type="text" name="userAddress_country" id="userAddress_country" value="' . $data['user']['address_country'] . '" size="48" maxlength="255" />';
  echo '</p>';





  echo '<p>';
  echo '<label for="userPostal_street">';
  if ( $data['user']['__error']['street'] )
    {
      echo '<span class="alert">';
      echo 'Street';
      echo '</span>';
    }
  else
    {
      echo 'Street';
    }
  echo '</label>';
  echo '<input type="text" name="userPostal_street" id="userPostal_street" value="' . $data['user']['postal_street'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userPostal_city">';
  if ( $data['user']['__error']['city'] )
    {
      echo '<span class="alert">';
      echo 'City';
      echo '</span>';
    }
  else
    {
      echo 'City';
    }
  echo '</label>';
  echo '<input type="text" name="userPostal_city" id="userPostal_city" value="' . $data['user']['postal_city'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userPostal_state">';
  if ( $data['user']['__error']['state'] )
    {
      echo '<span class="alert">';
      echo 'State';
      echo '</span>';
    }
  else
    {
      echo 'State';
    }
  echo '</label>';
  echo '<input type="text" name="userPostal_state" id="userPostal_state" value="' . $data['user']['postal_state'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userPostal_postcode">';
  if ( $data['user']['__error']['postcode'] )
    {
      echo '<span class="alert">';
      echo 'Postcode';
      echo '</span>';
    }
  else
    {
      echo 'Postcode';
    }
  echo '</label>';
  echo '<input type="text" name="userPostal_postcode" id="userPostal_postcode" value="' . $data['user']['postal_postcode'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userPostal_country">';
  if ( $data['user']['__error']['country'] )
    {
      echo '<span class="alert">';
      echo 'Country';
      echo '</span>';
    }
  else
    {
      echo 'Country';
    }
  echo '</label>';
  echo '<input type="text" name="userPostal_country" id="userPostal_country" value="' . $data['user']['postal_country'] . '" size="48" maxlength="255" />';
  echo '</p>';



  echo '<p>';
  echo '<label for="userAdmin">';
  echo 'National Institution';
  echo '</label>';

  echo '<input type="checkbox" name="userNational" id="userNational" value="1"';
  echo ( $data['user']['national'] > 0 )
    ? ' checked="checked"'
    : '';
  echo ' />';
  echo '</p>';


  echo '<p>';
  echo '<label for="userOai_url">';
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
  echo '</label>';
  echo '<input type="text" name="userOai_url" id="userOai_url" value="' . $data['user']['oai_url'] . '" size="48" maxlength="127" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="userAdmin">';
  echo 'Global Admin';
  echo '</label>';

  echo '<input type="checkbox" name="userAdmin" id="userAdmin" value="1"';
  echo ( $data['user']['admin'] > 0 )
    ? ' checked="checked"'
    : '';
  echo ' />';
  echo '</p>';



  echo '<p>';
  echo '</p>';

  echo '<p>';
  echo '<label>';
  echo '</label>';
  echo '<input type="submit" name="submit" value="Save Changes" />';
  echo '</p>';
  echo '</form>';
  
  echo ( $data['user']['user_id'] )
    ? html_form_start ( 'oai', array ( 'user_id'=>$data['user']['user_id'] ) )
    : html_form_start ( 'oai', array ( 'user_id'=>( ( $data['user']['user_id'] ) ? $data['user']['user_id'] : $data['user_id'] ) ) );
  echo '<p>';
  echo '<label>';
  echo '</label>';
  echo '<input type="submit" value="Cancel" />';
  echo '</p>';
  echo '</form>';

  echo '</div>';
  
}

?>
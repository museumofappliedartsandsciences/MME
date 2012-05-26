<?php

function widget_login ( $data ) 
{

  if ( $data['view'] == 'password_processed' ) 
    {
      login_password_processed ( $data );
    }
  elseif ( $data['view'] == 'password' )
    {
      login_password_form ( $data );
    }
  else
    {
      login_form ( $data );
    }
}

function login_form ( &$data )
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Login';
  echo '</h2>';

  if ( $data['error']['failed'] ) 
    {
      echo '<p class="alert">';
      echo 'The login you entered is incorrect, please try again.';
      echo '</p>';
    }

  echo "\n";
  echo html_form_start ( $data['__this'], array ( '__name'=>'login' ) );

  if ( $data['error']['in_use'] )
    {
      echo '<p class="alert">';
      echo $data['error']['in_use']; 
      echo '</p>';
    } 

  echo "\n";
  echo '<p>';
  echo '<label for="login-username">';
  echo 'Username';
  echo '</label>';
  echo "\n";
  echo '<input type="text" name="username" id="login-username" value="' . $data['username'] . '" />';
  echo '</p>';


  echo "\n";
  echo '<p>';
  echo '<label for="login-password">';
  echo 'Password';
  echo '</label>';
  echo '<input type="password" name="password" id="login-password" value="" />';
  echo '</p>';


  echo "\n";
  echo '<p>';
  echo '<label for="login-remember">';
  echo 'Keep me logged in';
  echo '</label>';
  echo '<input type="checkbox" name="remember" id="login-remember" value="2"' . ( ( $data['remember'] == '2' ) ? 'checked' : '' ) . ' />';
  echo '</p>';

  echo "\n";
  echo '<p>';
  echo '<label>';
  echo '<a href="/login/password">';
  echo 'Lost your password? Click Here';
  echo '</a>';
  echo '</label>';

  echo '<input type="submit" id="login-submit" value="Login" />';
  echo '</p>';
  
  echo "\n";
  echo '</form>';

  echo '</div>';

}


function login_password_processed ( $data ) 
{

  echo "\n";
  echo '<div class="formbox">';

  echo "\n";
  echo '<h2>';
  echo 'Retrieve Lost Password';
  echo '</h2>';

  echo "\n";
  echo '<p>';
  echo 'Your new password has been emailed to you';
  echo '</p>';

  echo "\n";
  echo '<p>';
  echo '<a href="' . $data['__this'] . '">Click here to log in</a>';
  echo '</p>';

  echo "\n";
  echo '</div>';
  
}


function login_password_form ( $data ) 
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Reset Password';
  echo '</h2>';

  echo html_form_start ( $data['__this'], array ( 'view'=>'password_process' ) );

  if ( $data['error'] )
    {
      echo '<p>';
      echo '<span class="alert">';
      echo 'A login matching "' . $data['email'] . '" could not be found - please try again.';
      echo '</span>';
      echo '</p>';
    }

  echo '<p>';
  echo 'Please enter your username or email address';
  echo '</p>';

  echo '<p>';
  echo '<input type="text" value="' . $data['email'] . '" name="email"  />';
  echo ' ';
  echo '<input type="submit" value="Reset Password" />';
  echo '</p>';

  echo '</form>';

  echo '</div>';

}

?>
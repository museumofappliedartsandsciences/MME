<?php

function validate_email ( $email ) 
{
  return ( eregi ( "^[0-9a-z]([-_.']?[0-9a-z_])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,8}$", $email ) );
}

function validate_password ( $s ) 
{

  return ( 
          strlen ( $s ) >= 5 
          && !ereg( "[^0-9a-zA-Z]", $s ) 
          && ereg( "[0-9]", $s )
          && ereg( "[a-zA-Z]", $s )
           );
}


/**
 * is $url valid and can be connected to?
 */
function validate_url ($url) 
{

  $url = ereg_replace( ' ', '', $url );
  $url = ereg_replace( 'http://', '', $url );

  // Do url string validate here, return false if fail

  $url = 'http://'.$url;

  if ( USE_NETWORK ) 
    {
      if ( @fopen($url, "r") || @fopen( ($url.'/') , "r" ) ) 
        {
          return true;
        } 
      else
        {
          return false;
        }
    }
  else
    {
      return true;
    }

}

function validate_hm ( $hm )
{

  // $hm is a hours/minutes formatted like 2h30m

  $hm = str_replace ( ' ', '' , $hm );

  if ( ! $hm ) 
    {
      return false;
    }

  //2m5m
  if ( ereg ( "([0-9.]*[m]){2,}", $hm ) || ereg ( "([0-9.]*[h]){2,}", $hm )  )
    {
      return false;
    }

  //m5h2
  if ( ereg ( "(^[a-z])(.*)", $hm ) )
    {
      return false;
    }

  //5m5h
  if ( ( strpos ( $hm, 'h' ) > strpos ( $hm, 'm' ) ) && strpos ( $hm, 'm' ) !== false ) 
    {
      return false;
    }

  return true;

}


?>
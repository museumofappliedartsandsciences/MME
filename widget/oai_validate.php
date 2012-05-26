<?php

core_load ( 'widget', 'oai' );

function widget_oai_validate ( $data )
{

  oai_path ( $data );

  if ( $data['view'] == 'processed' || $data['view'] == 'committed' )
    {
      oai_validate_processed ( $data );
    }
  else
    {
      oai_validate_form ( $data );
    }


}

function oai_validate_processed ( &$data )
{

  echo '<div class="formbox">';

  if ( $data['view'] == 'committed' )
    {
      echo '<h2>';
      echo 'Feed Imported';
      echo '</h2>';
    }
  else
    {
      echo '<h2>';
      echo 'Validation Results';
      echo '</h2>';
    }

  if ( $data['result']['error'] )
    {
      foreach ( $data['result']['error'] as $error )
        {
          echo '<p class="alert">';
          echo $error;
          echo '</p>';
        }
      echo '</div>';
      return false;
    }

  if ( ! $data['result']['collection'] )
    {
      echo '</div>';
      return false;
    }

  if ( $data['view'] == 'committed' )
    {
      echo '<p>';
      echo 'Your XML has been harvested in to this site. You can <a href="/oai/' . $data['user']['username'] . '">view the records on your main page</a>.';
      echo '</p>';
    }
  else
    {
      echo '<p>';
      echo 'Your XML has been parsed successfully. Below, each collection found is listed, showing the data the will be imported when your feed is processed.';
      echo '</p>';
    }

  echo '</div>';

  $p = oai_validate_pattern ();
  $code = array ( 'related','subjects', 'coverage' );
  foreach ( $data['result']['collection'] as $collection )
    {
      echo '<hr />';
      echo '<table id="validation">';
      foreach ( $p as $k => $v )
        {
          echo '<tr>';
          echo '<td>';
          echo $k;
          echo '</td>';

          echo '<td style="background: #cfc;">';
          if ( in_array ( $k, $code ) )
            {
              echo '<pre>';
              echo ( is_array ( $collection[$k] ) ) 
                ? oai_array2str ( $collection[$k] )
                : $collection[$k]; 
              echo '</pre>';
            }
          else
            {
              echo ( is_array ( $collection[$k] ) ) 
                ? oai_array2str ( $collection[$k] )
                : $collection[$k]; 
            }
          echo '</td>';

          echo '<td>';
          echo '</td>';	
          echo '</tr>';
        }

      echo '</table>';
    }

}

function oai_array2str ( $arr ) 
{
  if ( is_array ( $arr ) ) 
    {
      $s .= oai_dump_array ( $arr, 1 );
    } 
  else 
    {
      $s = '';
    }

  $s .= "\n";

  return $s;

}

function oai_dump_array ( $a, $t ) 
{

  $s = '';
  $count = 0;

  foreach ( $a as $k => $v )
    {

      $s .= "\n";

      $s .= str_repeat (" ", $t) . $k . ': ';

      if ( is_array( $v ) ) 
        {
          $s .= oai_dump_array ($v, $t+1);
        }
      else 
        {
          $s .= $v;
        }
    }
  return $s;
}



function oai_validate_form ( &$data )
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Manage your OAI XML Feed';
  echo '</h2>';


  if ( $data['__error'] )
    {
      echo '<p class="alert">';
      echo $data['__error'];
      echo '</p>';
    }
  else
    {
      echo '<p>';
      echo 'Use this screen to both test and import collections from your feed.';
      echo '</p>';
    }

  echo core_form_start ( $data['__this'], array ( 'view'=>'process', '__encoding'=>'multipart/form-data' ) );
  
  if ( user_admin() )
    {
      echo '<input type="hidden" name="user_id" value="' . $data['user_id'] . '" />';  
    }

  echo '<div id="url">';
  echo '<p>';
  echo '<strong>';
  echo 'OAI Harvest URL';
  echo '</strong>';
  echo '<p>';
  echo 'This defaults to the OAI Harvest URL set in your <a href="/account">Account</a>. You can enter a different url here for convenience but it will not be saved.';
  echo '</p>';
  echo '</p>';

  echo '<p>';
  echo '<input type="text" name="url" value="' . $data['url'] . '" />';
  echo '</p>';
  echo '</div>';

  echo '<p>';
  echo 'You can manually upload an XML file here. If you do, the file\'s contents will be used instead of the URL above.';
  echo '</p>';

  echo '<input type="hidden" name="max_file_size" value="5000000" />';
  echo '<input type="file" name="file" />';
  echo '</p>';

  echo '<br />';

  echo '<p>';
  echo '<input type="submit" name="action" value="Validate My Feed" />';
  echo '</p>';

  echo '<p>';
  echo '<input type="submit" name="action" value="Import Collections from My Feed" />';
  echo '</p>';

  echo '<br />';

  echo '<p>';
  echo ' <a href="/' . $data['__this'] . '/oai.xml">Download sample XML</a>. This file is provided that you can use as a starting point for developing your feed. ';
  echo '</p>';

  echo '</div>';

}

?>
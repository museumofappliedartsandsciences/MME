<?php

core_load ( 'widget', 'oai' );

function widget_collection_upload ( $data )
{

  oai_path ( $data );

  if ( $data['view'] == 'preview' )
    {
      collection_upload_preview ( $data );
    }
  elseif ( $data['view'] == 'committed' )
    {
      collection_upload_committed ( $data );
    }
  else
    {
      collection_upload_form ( $data );
    }


}

function collection_upload_committed ( &$data )
{

  echo '<h2>';
  echo 'Uploaded Committed';
  echo '</h2>';

  echo '<p>';
  if ( $data['count'] == '0' )
    {
      echo 'No items were processed';
    }
  else
    {
      echo $data['count'] . ( ( $data['count'] == 1 ) ? ' item has' : ' items have' ). ' been processed';
    }
  echo '</p>';

}

function collection_upload_form ( &$data )
{

  echo '<div class="formbox">';

  echo '<h2>';
  echo 'Upload Collection Data';
  echo '</h2>';

  //  echo '<h3>Upload from Excel</h3>';
  echo core_form_start ( $data['__this'], array ( 'view'=>'process', '__encoding'=>'multipart/form-data' ) );

  if ( user_admin() )
    {
      echo '<input type="hidden" name="user_id" value="' . $data['user_id'] . '" />';  
    }

  echo '<input type="hidden" name="max_file_size" value="5000000" />';
  echo '<input type="file" name="file" />';
  echo '<input type="submit" name="Upload" value="Upload" />';
  if ( $data['file']['__error'] )
    {
      echo '<h2 class="alert">';
      echo $data['file']['__error'];
      echo '</h2>';
    }
  echo '</form>';
  echo '<ul>';
  echo '<li><a href="/' . $data['__this'] . '/oai-upload-template.xls">Download the Template here</a></li>';
  echo '<li>Enter your data in the Excel template';
  echo '<li>Save the file as Tab Separated: File -> Save As -> Select Save as type: "Text (Tab Delimited)"';
  echo '<li>Click Browse, select your file then click Upload.';
  echo '<li>Note: Import will not work if the attached template is changed. Empty  columns must be kept and left blank.</li>';
  echo '</ul>';

  echo '</div>';

}


function collection_upload_preview ( &$data )
{

  echo '<div class="formbox">';

  echo core_form_start ( $data['__this'], array ( 'view'=>'commit' ) );

  if ( user_admin() )
    {
      echo '<input type="hidden" name="user_id" value="' . $data['user_id'] . '" />';  
    }


  $valid = true;

  foreach ( $data['headers'] as $k => $header )
    {
      if ( $data['uploaded_headers'][$k] != $header )
        {
          $valid = false;
        }
    }

  if ( $valid )
    {
    }
  
  if ( $data['items'] )
    {

      $keys = $data['keys'];

      echo '<div class="dataset" id="dataset">';
      echo '<table>';

      if ( ! $valid )
        {
          echo '<tr style="background: #aaa;">';
          echo '<td colspan="2">';
          echo '<h3>';
          echo 'Headers';
          echo '</h3>';
          echo '</td>';	
          echo '</tr>';	

          foreach ( $data['headers'] as $k => $header )
            {
              echo '<tr>';
              echo '<td>';
              echo $header;
              echo '</td>';	

              if ( $data['uploaded_headers'][$k] != $header )
                {
                  echo '<td style="background: #ff0;">';
                  echo 'Mismatched Header: ' . $data['uploaded_headers'][$k];
                  echo '</td>';	
                }
              else
                {
                  echo '<td style="background: #090; color: #fff;">';
                  echo $data['uploaded_headers'][$k];
                  echo '</td>';	
                }
              echo '</tr>';  
            }
        }
      else
        {
          echo '<tr style="background: #ddd;">';
          echo '<td class="check">';
          echo '<input type="checkbox" />';
          echo '</td>';	
          echo '<td>';
          echo '<input type="submit" value="Commit Data" />';
          echo ' Please scroll down and review your data in this window before committing';
          echo '</td>';	
          echo '</tr>';	
        }
      foreach ( $data['items'] as $id => $item )
        {

          echo '<tr style="background: #aaa;">';
          echo '<td>';
          echo '<input type="checkbox" name="itemI' . $id . 'Commit" value="1" />Commit';
          echo '</td>';	

          echo '<td>';
          echo '<h3>';
          echo $item['name_primary'];
          echo '</h3>';
          echo '</td>';	

          echo '</tr>';	

          foreach ( $keys as $k => $key )
            {
              echo '<input type="hidden" name="itemI' . $id . ucfirst ( $key ) . '" value="' . htmlentities ( $item[$key] ) . '" />';
              echo '<tr>';
              echo '<td>';
              echo $key;
              echo '</td>';	

              if ( $key =='id_local' && $item['exists'] )
                {
                  echo '<td style="background: #fc0;">';
                  echo nl2br ( $item[$key] );
                  echo '<strong style="float: right;">ID is already in database. Commit will update existing data</strong>';
                  echo '</td>';	
                }
              elseif ( $data['header_missing'][$k] )
                {
                  echo '<td style="background: #fc0;">';
                  echo 'Column Missing - Please amend spreadsheet and re-upload';
                  echo '</td>';	
                }
              else
                {
                  echo  '<td>';
                  echo nl2br ( $item[$key] );
                  echo '</td>';	
                }
              echo '</tr>';	
            }

        }

      echo '</table>';
      echo '</div>';

    }


  echo '</form>';

  echo '</div>';

}

?>
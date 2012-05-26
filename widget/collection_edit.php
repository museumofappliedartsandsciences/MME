<?php

core_load ( 'widget', 'oai' );

function widget_collection_edit ( $data )
{
  oai_path ( $data );
  collection_edit_form ( $data );
}

function collection_edit_form ( $data )
{

  echo '<div class="formbox">';

  echo core_form_start ( $data['__this'], array ( 'view'=>'update', '__id'=>'editform' ) ); 


  echo '<input type="hidden" name="collection_id" value="' . $data['collection']['collection_id'] . '" />';
  echo '<input type="hidden" name="collectionCollection_id" value="' . $data['collection']['collection_id'] . '" />';
  echo '<input type="hidden" name="collectionUser_id" value="' . ( ( $data['collection']['user_id'] ) ? $data['collection']['user_id'] : $data['user_id'] ) . '" />';

  echo '<input type="hidden" name="collectionSubjects" value="" />';
  echo '<input type="hidden" name="collectionCoverageTemporal" value="" />';
  echo '<input type="hidden" name="collectionCoverageSpatial" value="" />';
  echo '<input type="hidden" name="collectionRelated" value="" />';

  echo '<h2>';
  echo 'Names';
  echo '</h2>';

  echo '<p>';
  echo '<label for="collectionName_primary">';
  if ( $data['collection']['__error']['name_primary'] )
    {
      echo '<span class="alert">';
      echo 'Name Primary';
      echo '</span>';
    }
  else
    {
      echo 'Name Primary';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionName_primary" id="collectionName_primary" value="' . $data['collection']['name_primary'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionName_alternate">';
  if ( $data['collection']['__error']['name_alternate'] )
    {
      echo '<span class="alert">';
      echo 'Name alternate';
      echo '</span>';
    }
  else
    {
      echo 'Name Alternate';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionName_alternate" id="collectionName_alternate" value="' . $data['collection']['name_alternate'] . '" size="48" maxlength="255" />';
  echo '</p>';


  echo '<p>';
  echo '<label for="collectionName_abbreviated">';
  if ( $data['collection']['__error']['name_abbreviated'] )
    {
      echo '<span class="alert">';
      echo 'Name Abbreviated';
      echo '</span>';
    }
  else
    {
      echo 'Name Abbreviated';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionName_abbreviated" id="collectionName_abbreviated" value="' . $data['collection']['name_abbreviated'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<h2>';
  echo 'IDs';
  echo '</h2>';


  echo '<p>';
  echo '<label for="collectionId_local">';
  if ( $data['collection']['__error']['id_local'] )
    {
      echo '<span class="alert">';
      echo 'ID Local';
      echo '</span>';
    }
  else
    {
      echo 'ID Local';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionId_local" id="collectionId_local" value="' . $data['collection']['id_local'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionId_purl">';
  if ( $data['collection']['__error']['id_purl'] )
    {
      echo '<span class="alert">';
      echo 'ID Purl';
      echo '</span>';
    }
  else
    {
      echo 'ID Purl';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionId_purl" id="collectionId_purl" value="' . $data['collection']['id_purl'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionId_uri">';
  if ( $data['collection']['__error']['id_uri'] )
    {
      echo '<span class="alert">';
      echo 'ID URI';
      echo '</span>';
    }
  else
    {
      echo 'ID Uri';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<input type="text" name="collectionId_uri" id="collectionId_uri" value="' . $data['collection']['id_uri'] . '" size="48" maxlength="255" />';
  echo '</p>';

  echo '<h2>';
  echo 'Descriptions';
  echo '</h2>';

  echo '<p>';
  echo '<label for="collectionDescription_brief">';
  if ( $data['collection']['__error']['description_brief'] )
    {
      echo '<span class="alert">';
      echo 'Brief Description';
      echo '</span>';
    }
  else
    {
      echo 'Brief Description';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_brief" id="collectionDescription_brief">' . $data['collection']['description_brief'] . '</textarea>';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionDescription_full">';
  if ( $data['collection']['__error']['description_full'] )
    {
      echo '<span class="alert">';
      echo 'Full Description';
      echo '</span>';
    }
  else
    {
      echo 'Full Description';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_full" id="collectionDescription_full" rows="24">' . $data['collection']['description_full'] . '</textarea>';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionDescription_significance">';
  if ( $data['collection']['__error']['description_significance'] )
    {
      echo '<span class="alert">';
      echo 'Significance';
      echo '</span>';
    }
  else
    {
      echo 'Significance';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_significance" id="collectionDescription_significance" rows="24">' . $data['collection']['description_significance'] . '</textarea>';
  echo '</p>';


  echo '<p>';
  echo '<label for="collectionDescription_rights">';
  if ( $data['collection']['__error']['description_rights'] )
    {
      echo '<span class="alert">';
      echo 'Rights';
      echo '</span>';
    }
  else
    {
      echo 'Rights';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_rights" id="collectionDescription_rights">' . $data['collection']['description_rights'] . '</textarea>';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionDescription_access">';
  if ( $data['collection']['__error']['description_access'] )
    {
      echo '<span class="alert">';
      echo 'Access';
      echo '</span>';
    }
  else
    {
      echo 'Access';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_access" id="collectionDescription_access">' . $data['collection']['description_access'] . '</textarea>';
  echo '</p>';

  echo '<p>';
  echo '<label for="collectionDescription_note">';
  if ( $data['collection']['__error']['description_note'] )
    {
      echo '<span class="alert">';
      echo 'Note';
      echo '</span>';
    }
  else
    {
      echo 'Note';
      // echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo '<textarea name="collectionDescription_note" id="collectionDescription_note">' . $data['collection']['description_note'] . '</textarea>';
  echo '</p>';



  echo '<h2>';
  echo 'Subjects';
  echo '</h2>';

  echo '<ul id="subjects" class="options">';
  echo '</ul>';
  
  echo '<input type="button" name="" class="add" value="+" />';

  echo '<h2>';
  echo 'Coverage';
  echo '</h2>';

  echo '<h3>';
  echo 'Temporal';
  echo '</h3>';

  echo '<ul id="temporal" class="options">';
  echo '</ul>';
  
  echo '<input type="button" name="" class="add" value="+" />';

  echo '<h3>';
  echo 'Spatial';
  echo '</h3>';

  echo '<ul id="spatial" class="options">';
  echo '</ul>';
  
  echo '<input type="button" name="" class="add" value="+" />';

  echo '<h2>';
  echo 'Related';
  echo '</h2>';

  echo '<ul id="related" class="options">';
  echo '</ul>';
  
  echo '<input type="button" name="" class="add" value="+" />';


  echo '<p>';
  echo '&nbsp;';
  echo '</p>';

  // echo '<p>';
  // echo '<label for="collectionDate_accessioned">';
  // if ( $data['collection']['__error']['date_accessioned'] )
  //       {
  //         echo '<span class="alert">';
  //         echo 'Date Accessioned';
  //         echo '</span>';
  //       }
  // else
  //       {
  //         echo 'Date Created';
  //         // echo '<span class="alert">*</span>';
  //       }
  // echo '</label>';

  // collection_datepicker ( 'collectionDate_accessioned', $data['collection']['date_accessioned'] );
  // echo '</p>';

  // echo '<p>';
  // echo '<label for="collectionDate_modified">';
  // if ( $data['collection']['__error']['date_modified'] )
  //       {
  //         echo '<span class="alert">';
  //         echo 'Date Modified';
  //         echo '</span>';
  //       }
  // else
  //       {
  //         echo 'Date Modified';
  //         // echo '<span class="alert">*</span>';
  //       }
  // echo '</label>';

  // collection_datepicker ( 'collectionDate_modified', $data['collection']['date_modified'] );
  // echo '</p>';



  echo '<p>';
  echo '<label>';
  echo '</label>';
  echo '<input type="submit" name="submit" value="Save Changes" />';
  echo '</p>';
  echo '</form>';

  echo ( $data['collection'] && $data['collection']['collection_id'] )
    ? html_form_start ( 'oai', array ( 'collection_id'=>$data['collection']['collection_id'] ) )
    : html_form_start ( 'oai', array ( 'user_id'=>( ( $data['collection']['user_id'] ) ? $data['collection']['user_id'] : $data['user_id'] ) ) );
  echo '<p>';
  echo '<label>';
  echo '</label>';
  echo '<input type="submit" value="Cancel" />';
  echo '</p>';
  echo '</form>';

  echo '</div>';
  
}


function collection_datepicker ( $key, $datetime = false )
{

  // set nice default values for dates - default is one week


  if ( $datetime && strlen ( $datetime ) == 14 )
    {
      $s = $datetime;
      $datetime = '';
      $datetime['year'] = substr( $s, 0, 4 );
      $datetime['month'] = substr( $s, 4, 2 );
      $datetime['day'] = substr( $s, 6, 2 );
      $datetime['hour'] = substr( $s, 8, 2 );
      $datetime['minute'] = substr( $s, 10, 2 );
      $datetime['second'] = substr( $s, 12 , 2 );
    }
  elseif ( $datetime && strlen ( $datetime ) == 20 )
    {
      //2008-10-20T13:00:00Z

      $s = $datetime;
      $datetime = '';
      $datetime['year'] = substr( $s, 0, 4 );
      $datetime['month'] = substr( $s, 5, 2 );
      $datetime['day'] = substr( $s, 8, 2 );
      $datetime['hour'] = substr( $s, 11, 2 );
      $datetime['minute'] = substr( $s, 14, 2 );
      $datetime['second'] = substr( $s, 17 , 2 );
    }
  
  echo '<select name="' . $key . 'Year">';
  if ( ! $datetime )
    {
      echo '<option value="--">';
      echo 'YYYY';
      echo '</option>';
    }
  for ( $i=1900; $i <= date('Y'); $i++ ) 
    {
      echo '<option value="' . sprintf ('%04d', $i ) . '"';
      if ( $datetime && $datetime['year'] == $i ) 
        {
	  echo ' selected="selected"';
	}
	
      echo '>';
      echo sprintf ( '%04d', $i );
      echo '</option>';
    }
  echo '</select>' . "\n";



  echo '<select name="' . $key . 'Month">';
  if ( ! $datetime )
    {
      echo '<option value="--">';
      echo 'MM';
      echo '</option>';
    }
  for ( $i=1; $i <= 12; $i++ ) 
    {
      echo '<option value="' . sprintf ('%02d', $i ) . '"';
      if ( $datetime && $datetime['month'] == $i ) 
        {
	  echo ' selected="selected"';
	}
	
      echo '>';
      echo sprintf ( '%02d', $i );
      echo '</option>';
    }
  echo '</select>' . "\n";


  echo '<select name="' . $key . 'Day">';
  if ( ! $datetime )
    {
      echo '<option value="--">';
      echo 'DD';
      echo '</option>';
    }
  for ( $i=1; $i <= 31; $i++ ) 
    {
      echo '<option value="' . sprintf ('%02d', $i ) . '"';
      if ( $datetime && $datetime['day'] == $i ) 
        {
	  echo ' selected="selected"';
	}
	
      echo '>';
      echo sprintf ( '%02d', $i );
      echo '</option>';
    }
  echo '</select>' . "\n";


  echo ':';

  echo '<select name="' . $key . 'Hour">';
  if ( ! $datetime )
    {
      echo '<option value="--">';
      echo 'hh';
      echo '</option>';
    }
  for ( $i=0; $i < 24; $i++ ) 
    {
      echo '<option value="' . sprintf ('%02d', $i ) . '"';
      if ( $datetime && $datetime['hour'] == $i ) 
        {
	  echo ' selected="selected"';
	}
	
      echo '>';
      echo sprintf ( '%02d', $i );
      echo '</option>';
    }
  echo '</select>' . "\n";

  echo '<select name="' . $key . 'Minute">';

  if ( ! $datetime )
    {
      echo '<option value="--">';
      echo 'mm';
      echo '</option>';
    }

  for ( $i=0; $i < 60; $i++ ) 
    {
      echo '<option value="' . sprintf ( '%02d', $i ) . '"';
      if ( $datetime && $datetime['minute'] == $i ) 
        {
	  echo ' selected="selected"';
	}
	
      echo '>';
      echo sprintf ( '%02d', $i );
      echo '</option>';
    }

  echo '</select>' . "\n";
  echo '<strong>Z</strong> (UTC Time)';


}

?>
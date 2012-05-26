<?php

core_load ( 'include', 'oai.inc' );

function access_collection_upload ( &$data ) 
{
  return ( user() );
}

function module_collection_upload ( &$data ) 
{

  core_set_template ( 'index' );
  core_set_title ( 'Upload Data' );
  core_head_add ( 'jquery' );
  core_head_add ( 'oai.css' );

  /*
   * access control
   */
  if ( ! user_admin() || ! $data['user_id'] )
    {
      $data['user_id'] = user('user_id');
    }

  // mapping

  // our names
  $data['keys'] = array ( 
                         '0' => 'name_primary',
                         '1' => 'name_alternate',
                         '2' => 'name_abbreviated',
                         '3' => 'id_local',
                         '4' => 'id_uri',
                         '5' => 'description_brief',
                         '6' => 'description_full',
                         '7' => 'description_significance',
                         '8' => 'description_rights',
                         '9' => 'description_access',
                         '10' => 'description_note',
                         '11' => 'subject_local',
                         '12' => 'subject_pont',
                         '13' => 'subject_scot',
                         '14' => 'subject_apt',
                         '15' => 'subject_person_org',
                         '16' => 'date_from',
                         '17' => 'date_to',
                         '18' => 'date_text',
                         '19' => 'places',
                         '20' => 'collection_relation_type',
                         '21' => 'related_collection_mme_uri',
                         '22' => 'related_collection_name');

  // upload file headers
  $data['headers'] = array( 
                           '0' => 'name_primary',
                           '1' => 'name_alternative',
                           '2' => 'name_abbreviated',
                           '3' => 'ID_local',
                           '4' => 'online',
                           '5' => 'description_brief',
                           '6' => 'description_full',
                           '7' => 'description_significance',
                           '8' => 'description_copyright',
                           '9' => 'description_access_rights',
                           '10' => 'description_notes',
                           '11' => 'subject_local',
                           '12' => 'subject_pont',
                           '13' => 'subject_scot',
                           '14' => 'subject_APT',
                           '15' => 'subject_person_organisation',
                           '16' => 'date_from',
                           '17' => 'date_to',
                           '18' => 'date_text',
                           '19' => 'places',
                           '20' => 'collection_relation_type',
                           '21' => 'related_collection_mme_uri',
                           '22' => 'related_collection_name');

  $data['match'] = false;



  if ( $data['view'] == 'process' && collection_upload_process ( $data ) )
    {
      $data['view'] = 'preview';
    }

  if ( $data['view'] == 'commit' )
    {
      $data['count'] = collection_upload_commit ( $data['user_id'], $data['item'] );
      $data['view'] = 'committed';
    }

  if ( $data['user_id'] )
    {
      $data['user'] = oai_user ( $data['user_id'] );
    }


  if ( $data['__key'] == 'oai-upload-template.xls' )
    {
      collection_upload_template ( $data );
      exit;
    }

}

function collection_upload_process ( &$data )
{

  if ( ! $data['file'] )
    {
      $data['file']['__error'] = 'No file uploaded';
      return false;
    }

  if ( ! is_uploaded_file ( $data['file']['tmp_name'] ) )
    {
      $data['file']['__error'] = 'No file uploaded';
      return false;
    }

  if ( $data['file']['type'] == 'text/csv' )
    {

      $lines = false;

      if ( ( $fh = fopen ( $data['file']['tmp_name'], "r" ) ) !== FALSE ) 
        {
          while ( ( $r = fgetcsv ( $fh, 1000, "," ) ) !== FALSE ) 
            {
              $lines[] = $r;
            }
          fclose ( $fh );
        }
    }
  elseif ( $data['file']['type'] == 'text/plain' )
    {

      $s = file_get_contents ( $data['file']['tmp_name'] );
	  
      if ( $s == '' )
        {
          return false;
        }
	  
      $lines = explode ( "\n", $s );
    }
  else
    {
      $data['file']['__error'] = 'Bad File Type';
      return false;
    }
	  
  if ( ! is_array ( $lines ) )
    {
      return false;
    }
  
  $keys = $data['keys'];
  $data['uploaded_headers'] = ( is_array ( $lines[0] ) )
    ? $lines[0]
    : explode ( "\t", trim( $lines[0] ) );

  unset ( $lines[0] );

  foreach ( $lines as $line )
    {

      if ( ! is_array ( $line ) )
        {
          $line = trim ( $line );	
          if ( $line == '' )
            {
              // disregard blank line
              continue;
            }	
          $d = explode ( "\t", $line );
        }
      else
        {
          if ( implode ( '', $line ) == '' )
            {
              // disregard blank line
              continue;
            }
          $d = $line;
        }

      // if ( $d[0] == 'identifierTypeLocal' )
      // 	{
      // 	  // disregard header line
      // 	  continue;
      // 	}

      $item = array();
	  
      foreach ( $keys as $id => $key )
        {
          $s = $d[$id];
          $s = trim ( $s );
          $s = trim ( $s, '"' );
          $item[$key] = asciify ( $s );
        }

      $sql = "SELECT * FROM collection ";
      $sql .= " WHERE id_local='" . addslashes ( $item['id_local'] ) . "'";
      $sql .= " AND user_id='" . addslashes ( $data['user_id'] ) . "'";

      $r = db_exec ( $sql );
      if ( $r )
        {
          $item['exists'] = true;
        }

      $data['items'][] = $item;

    }

  return true;

}

function collection_upload_commit ( $user_id, $items = false )
{

  if ( ! $items || ! is_array ( $items ) )
    {
      return false;
    }

  $count = false;

  foreach ( $items as $item )
    {
  
      if ( ! $item['commit'] )
        {
          continue;
        }

      $count += 1;

      $c = array();

      // exists already?
      $sql = "SELECT collection_id FROM collection ";
      $sql .= " WHERE id_local='" . addslashes ( $item['id_local'] ) . "'";
      $sql .= " AND user_id='" . addslashes ( $user_id ) . "'";
      $collection_id = db_exec_one ( $sql, 'collection_id' );

      if ( $collection_id )
        {
          $c['collection_id'] = $collection_id;
        }

      $c['id_local'] = trim ( $item['id_local'] );
      $c['id_uri'] = trim ( $item['id_uri'] );
      $c['name_primary'] = trim ( $item['name_primary'] );
      $c['name_alternate'] = trim ( $item['name_alternate'] );
      $c['name_abbreviated'] = trim ( $item['name_abbreviated'] );
      $c['description_brief'] = trim ( $item['description_brief'] );
      $c['description_full'] = trim ( $item['description_full'] );
      $c['description_significance'] = trim ( $item['description_significance'] );
      $c['description_access'] = trim ( $item['description_access'] );
      $c['description_rights'] = trim ( $item['description_rights'] );
      $c['description_note'] = trim ( $item['description_note'] );
      // $c['id_uri'] = trim ( $item['identifier_uri'] );

      foreach ( array ( 'local','pont','apt','scot','person_org' ) as $type )
        {
          $c['subjects'][$type] = depipe ( $item['subject_' . $type] );
        }

      $c['coverage']['spatial']['text'] = depipe ( $item['places'] );

      $c['coverage']['temporal'] = array();
      $c['coverage']['temporal']['from'][] = $item['date_from'];
      $c['coverage']['temporal']['to'][] = $item['date_to'];
      $c['coverage']['temporal']['text'][] = $item['date_text'];

      $related_type = depipe ( $item['collection_relation_type'] );
      $related_uri = depipe ( $item['related_collection_mme_uri'] );
      $related_name = depipe ( $item['related_collection_name'] );
      
      $subs = array();
      if ( is_array ( $related_type ) )
        {
          foreach ( $related_type as $k => $v )
            {
              $subs[] = array ( 'type'=>$v, 'key'=>$related_uri[$k], 'value'=>$related_name[$k] );
            }
        }

      $c['related'] = $subs;

      $c['user_id'] = $user_id;
      oai_collection_update ( $c );
      oai_collection_subjects_set ( $c['collection_id'], $c['subjects'], true );

    }

  return $count;

}

function depipe ( $s )
{
  $s = str_replace ( ' |', '|', trim ( $s ) );
  $s = str_replace ( '| ', '|', $s );	
  $a = explode ( '|', $s );
  return $a;
}

function asciify ( $s )
{
  $s = str_replace(chr(130), ',', $s);    // baseline single quote
  $s = str_replace(chr(131), 'NLG', $s);  // florin
  $s = str_replace(chr(132), '"', $s);    // baseline double quote
  $s = str_replace(chr(133), '...', $s);  // ellipsis
  $s = str_replace(chr(134), '**', $s);   // dagger (a second footnote)
  $s = str_replace(chr(135), '***', $s);  // double dagger (a third footnote)
  $s = str_replace(chr(136), '^', $s);    // circumflex accent
  $s = str_replace(chr(137), 'o/oo', $s); // permile
  $s = str_replace(chr(138), 'Sh', $s);   // S Hacek
  $s = str_replace(chr(139), '<', $s);    // left single guillemet
  $s = str_replace(chr(140), 'OE', $s);   // OE ligature
  $s = str_replace(chr(145), "'", $s);    // left single quote
  $s = str_replace(chr(146), "'", $s);    // right single quote
  $s = str_replace(chr(147), '"', $s);    // left double quote
  $s = str_replace(chr(148), '"', $s);    // right double quote
  $s = str_replace(chr(149), '-', $s);    // bullet
  $s = str_replace(chr(150), '-', $s);    // endash
  $s = str_replace(chr(151), '--', $s);   // emdash
  $s = str_replace(chr(152), '~', $s);    // tilde accent
  $s = str_replace(chr(153), '(TM)', $s); // trademark ligature
  $s = str_replace(chr(154), 'sh', $s);   // s Hacek
  $s = str_replace(chr(155), '>', $s);    // right single guillemet
  $s = str_replace(chr(156), 'oe', $s);   // oe ligature
  $s = str_replace(chr(159), 'Y', $s);    // Y Dieresis
  return $s;
}


function collection_upload_template ( $data )
{

  header ( 'Content-Disposition: attachment; filename="oai-upload-template.xls"' );
  header ( 'Content-type: application/vnd.ms-excel' );
  readfile ( SITE_ROOT . 'data/oai/oai-upload-template.xls' );
  exit;

}

?>
<?php

core_load ( 'include', 'oai.inc' );

function access_oai_validate ( &$data ) 
{
  return ( user() );
}

function module_oai_validate ( &$data ) 
{

  core_set_template ( 'index' );
  core_set_title ( 'Validate Feed' );
  core_head_add ( 'jquery' );
  core_head_add ( 'oai.css' );

  if ( ! user_admin() || ! $data['user_id'] )
    {
      $data['user_id'] = user('user_id');
    }

  // for testing
  // $xml = file_get_contents ( 'http://mme.baiter.com.au/test2.xml' );
  // $f = 'http://mme.baiter.com.au/test2.xml';
  // $data['result'] = oai_xml_validate ( $f );
  // $data['view'] = 'processed';

  $data['__error'] = false;
  $data['url'] = trim ( $data['url'] );

  if ( $data['action'] )
    {
      $data['view'] = substr ( strtolower ( $data['action'] ), 0, strpos ( $data['action'], ' ' ) );
    }

  if ( $data['file'] && $data['file']['error'] == 0 )
    {
      if ( ! is_uploaded_file ( $data['file']['tmp_name'] ) )
        {
          $data['__error'] = 'No file uploaded';
          return false;
        }
      $xml_f = $data['file']['tmp_name'];
    }
  elseif ( isset ( $data['url'] ) && $data['url'] != '' )
    {
      $xml_f = $data['url'];
    }
 
  if ( $data['view'] == 'validate' && ! $data['__error'] )
    {
      $data['result'] = oai_xml_validate ( $xml_f );
      $data['view'] = 'processed';
    }

  if ( $data['view'] == 'import' && ! $data['__error'] )
    {
      $data['result'] = oai_xml_validate ( $xml_f );
      $data['count'] = oai_validate_commit ( $data['user_id'], $data['result']['collection'] );
      $data['view'] = 'committed';
    }


  if ( ! $data['view'] && ! $data['__error'] && ! isset ( $data['url'] ) && $data['url'] == '' && user('oai_url') != '' )
    {
      $data['url'] = user('oai_url');
    }

  if ( $data['__key'] == 'oai.xml' )
    {
      oai_xml_template ( $data );
      exit;
    }

  // for widget/oai
  $data['user'] = user();

}

function oai_xml_template ( $data )
{

  header ( 'Content-Disposition: attachment; filename="oai.xml"' );
  header ( 'Content-type: text/plain' );
  readfile ( SITE_ROOT . 'data/oai/oai.xml' );
  exit;

}

function oai_xml_validate ( $f )
{
  $r = array();
  $r['error'] = false;
  $collections = 0;

  $xml = @simplexml_load_file( $f );

  if ( ! $xml )
    {
      $r['error'][] = 'Could not load XML - it is empty or malformed/';
      return $r;
    }


  // $xml = new SimpleXMLElement( $s );  
  $foo = $xml->xpath('/registryObjects');

  if ( $foo === false )
    {
      $r['error'][] = 'No registryObjects';
      return $r;
    }

  $objs = $xml->xpath('/registryObjects/registryObject');
  
  if ( $objs === false || sizeof ( $objs ) == 0 )
    {
      $r['error'][] = 'No registryObject found ';
      return $r;
    }

  while ( list ( $k, $obj ) = each ( $objs ) )
    {
      if ( sizeof ( $obj->collection ) == 0 )
        {
          // no collection in this registryObject
          continue;
        }

      foreach ( $obj->collection as $collection ) 
        {
          if ( ( $collection['type'] ) == 'collection' )
            {

              $collections ++;

              $c = array();

              $c['date_accessioned'] = trim ( $collection->dateAccessioned );
              $c['date_modified'] = trim ( $collection->dateModified );

              foreach ($collection->identifier as $id )
                {
                  $c['id_' . $id['type']] = trim ( $id );
                }

              foreach ($collection->name as $name )
                {
                  $c['name_' . $name['type']] = trim ( $name->namePart );
                }

              $c['subjects'] = array();
              foreach ($collection->subject as $subject ) 
                {
                  $subject_a = str_replace ( ' |', '|', trim ( $subject ) );
                  $subject_a = str_replace ( '| ', '|', $subject_a );	
                  $subject_a = explode ( '|', $subject_a );

                  if ( is_array ( $c['subjects'][trim ($subject['type'] )] ) )
                    {
                      $subject_a = array_merge ( $c['subjects'][trim ($subject['type'] )], $subject_a );
                    }
                  $c['subjects'][trim ($subject['type'] )] = $subject_a;
                }


              foreach ($collection->description as $description )
                {
                  if ( $description['type'] == 'accessRights' ) {
                    $description['type'] = 'access';
                  }
                  $c['description_' . $description['type']] = trim ( $description );
                }

              $c['related'] = false;

              if ($collection->relatedObject ) 
                {
                  foreach ($collection->relatedObject as $relatedObject ) 
                    {
                      $rob = array();
                      $rob['key'] = trim ( $relatedObject->key );
                      $rob['type'] = trim ( $relatedObject->relation['type'] );
                      $rob['description'] = trim ( $relatedObject->relation->description );
                      $c['related'][] = $rob;
                    }
                }

              if ( $collection->coverage->temporal->date )
                {
                  foreach ($collection->coverage->temporal->date as $date ) 
                    {
                      $c['coverage']['temporal'][trim ( $date['type'] )][] = trim ( $date );
                    }
                }

              if ( $collection->coverage->spatial )
                {
                  foreach ($collection->coverage->spatial as $loc )
                    {
                      $c['coverage']['spatial'][trim ( $loc['type'] )][] = trim ( $loc );
                    }
                }
            }
          $r['collection'][] = $c;
        }
    }

  if ( $collections == 0 )
    {
      $r['error'][] = 'No collections found';
    }

  return $r;

}

function oai_validate_pattern ()
{

  $p = array ( 
              'date_accessioned' => true,
              'date_modified' => true,
              'id_local' => true,
              'id_purl' => false,
              'id_uri' => false,
              'name_primary' => false,
              'name_alternate' => false,
              'name_abbreviated' => false,
              'subjects' => false,
              'description_brief' => false,
              'description_full' => false,
              'description_significance' => false,
              'description_rights' => false,
              'description_access' => false,
              'description_note' => false,
              'related' => false,
              'coverage' => false
               );

  return $p;

}

function oai_validate_commit ( $user_id, $items = false )
{

  if ( ! $items || ! is_array ( $items ) )
    {
      return false;
    }

  $count = false;

  foreach ( $items as $item )
    {

      // if you add checkboxes later

      // if ( ! $item['commit'] )
      //     {
      //       continue;
      //     }

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



?>
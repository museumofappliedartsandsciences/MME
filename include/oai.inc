<?php

function oai_users ( $q = false )
{

  if ( ! is_array ( $q ) )
    {
      $q = array ( 'collection_status'=>STATUS_LIVE );
    }

  if ( ! isset ( $q['collection_status'] ) )
    {
      $q['collection_status'] = STATUS_LIVE;
    }

  if ( $q['collection_status'] == STATUS_ALL )
    {
      $sql = "SELECT *, ";
      $sql .= " ( SELECT COUNT(*) FROM collection c WHERE c.user_id = u.user_id ) AS collections ";
      $sql .= " FROM user u ";		
      $sql .= " GROUP BY u.user_id ";		
      $sql .= " ORDER BY title ";		
    }
  else
    {
      $sql = "SELECT *, ";
      $sql .= " ( SELECT COUNT(*) FROM collection c WHERE c.user_id = u.user_id AND c.status=" . $q['collection_status'] . " ) AS collections ";
      $sql .= " FROM user u ";		
      $sql .= " GROUP BY u.user_id ";		
      $sql .= " ORDER BY title ";		
    }

    $users = db_exec ( $sql, 'user_id' );
    return $users;
}


function oai_user_id ( $username )
{
  $sql = "SELECT user_id AS user_id FROM user ";		
  $sql .= " WHERE username='" . addslashes ( $username ) . "'";
  $user_id = db_exec_one ( $sql, 'user_id' );
  return $user_id;
}


function oai_user ( $user_id = false )
{

  if ( ! $user_id || ! is_numeric ( $user_id ) )
    {
      return false;
    }

  $sql = "SELECT * FROM user ";		
  $sql .= " WHERE user_id=" . addslashes ( $user_id ) . "";
  $user = db_exec_one ( $sql );
  return $user;

}

function oai_collections ( $q )
{

// ALTER TABLE  `collection` ADD FULLTEXT (
// `name_primary` ,
// `name_alternate` ,
// `name_abbreviated` ,
// `description_brief` ,
// `description_full`
// );

  
  if ( ! is_array ( $q ) )
    {
      $q = array ( 'user_id'=>$q, 'status'=>STATUS_LIVE );
    }

  if ( ! isset ( $q['user_id'] ) || ! is_numeric ( $q['user_id'] ) )
    {
      $q['user_id'] = false;
    }

  if ( ! isset ( $q['search'] ) )
    {
      $q['search'] = false;
    }

  if ( ! isset ( $q['count'] ) )
    {
      $q['count'] = false;
    }

  if ( ! isset ( $q['status'] ) )
    {
      $q['status'] = STATUS_LIVE;
    }

  if ( ! isset ( $q['k'] ) || ! in_array ( $q['k'], array ( 'id_local', 'name_primary', 'date_updated' ) ) )
    {
      $q['k'] = 'name_primary';
    }

  if ( ! isset ( $q['o'] ) )
    {
      $q['o'] = '1';
    }

  $q['o'] = ( $q['o'] == -1 )
    ? '-1'
    : '1';

  if ( $q['q'] )
    {

      $sql = "SELECT cid, SUM(score) as total_score ";
      $sql .= " FROM  "; 
      $sql .= " ( "; 
      $sql .= " SELECT c.collection_id as cid, NULL as widget_sid,  "; 
      $sql .= "   MATCH ( c.name_primary, c.name_alternate, c.name_abbreviated, c.index_description, c.index_meta ) AGAINST('" . addslashes ( $q['q'] ) . "') AS score  "; 
      $sql .= " FROM collection c "; 
      $sql .= " WHERE MATCH( c.name_primary, c.name_alternate, c.name_abbreviated, c.index_description, c.index_meta ) AGAINST('" . addslashes ( $q['q'] ) . "') "; 
      $sql .= " AND c.status = " . STATUS_LIVE;

      // $sql .= " UNION "; 
      // $sql .= " SELECT c.sid as page_sid,c.title as page_title,w.sid as widget_sid,  "; 
      // $sql .= "   MATCH(w.content) AGAINST('" . addslashes ( $q['q'] ) . "') AS score  "; 
      // $sql .= " FROM page p "; 
      // $sql .= "   inner join widget_to_page wtp on c.sid = wtp.page_sid "; 
      // $sql .= "   inner join widget w on w.sid = wtp.widget_sid "; 
      // $sql .= " WHERE MATCH(w.content) AGAINST('" . addslashes ( $q['q'] ) . "') "; 

      $sql .= " ) AS sub_query "; 
      $sql .= " GROUP BY cid "; 
      $sql .= " ORDER BY total_score DESC ";

      $r = db_exec ( $sql );

      $collections = false;

      if ( $r )
        {
          $collections = array();
          foreach ( $r as $c )
            {
              $collections[$c['cid']] = oai_collection ( $c['cid'] );
              $collections[$c['cid']]['score'] = $c['total_score'];
            }
        }

      return $collections;

    }

  //

  if ( $q['count'] )
    {
      $sql = "SELECT COUNT(*) AS count FROM collection ";		
    }
  else
    {
      $sql = "SELECT collection_id FROM collection ";		
    }

  $logic = 'WHERE';

  if ( $q['user_id'] )
    {
      $sql .= " " . $logic . " user_id=" . $q['user_id'] . "";
      $logic = 'AND';
    }

  if ( $q['status'] && $q['status'] != STATUS_ALL )
    {
      $sql .= " " . $logic . " status=" . $q['status'] . "";
      $logic = 'AND';
    }

  if ( $q['count'] )
    {
      $count = db_exec_one ( $sql, 'count' );
      return $count;
    }
      
  if ( $q['k'] )
    {
      $sql .= " ORDER BY " . $q['k'];
      if ( $q['o'] == -1 )
        {
          $sql .= " DESC";
        }
    }

  $r = db_exec_keys ( $sql, 'collection_id' );

  $collections = false;
  if ( $r )
    {
      $collections = array();
      foreach ( $r as $collection_id )
        {
          $collections[$collection_id] = oai_collection ( $collection_id );
        }
    }

  return $collections;

}

function oai_collection ( $collection_id = false )
{
  
  if ( ! $collection_id || ! is_numeric ( $collection_id ) )
    {
      return false;
    }

  $sql = "SELECT * FROM collection ";		
  $sql .= " WHERE collection_id=" . addslashes ( $collection_id ) . "";
  $collection = db_exec_one ( $sql );

  $collection['subjects'] = oai_collection_subjects ( $collection_id );


  $collection['coverage'] = array();

  $sql = "SELECT * FROM collection_coverage ";		
  $sql .= " WHERE collection_id=" . addslashes ( $collection_id ) . "";
  $sql .= " ORDER BY mode, type ";

  $r = db_exec ( $sql );

  if ( $r )
    {
      foreach ( $r as $v )
        {
          $collection['coverage'][$v['mode']][$v['type']][] = $v['value'];
        }
    }

  $sql = "SELECT type, `key`, description FROM collection_related ";		
  $sql .= " WHERE collection_id=" . addslashes ( $collection_id ) . "";
  $sql .= " ORDER BY type ";

  $collection['related'] = db_exec ( $sql );

  $collection['name_primary'] = ( $collection['name_primary'] ) 
    ? $collection['name_primary']
    : '[Untitled]';

  return $collection;

}

function oai_collection_validate ( &$collection )
{

  foreach ( $collection as $k => $v )
    {
      if ( is_array ( $v ) )
        {
          continue;
        }
      $collection[$k] = trim ( $v );
    }

  $collection['__error'] = false;

  if ( ! $collection['name_primary'] )
    {
      $collection['__error']['name_primary'] = true;
    }

  // if ( ! $collection['id_uri'] )
  // 	{
  // 	  $collection['__error']['id_uri'] = true;
  // 	}

  if ( ! $collection['user_id'] || ! is_numeric ( $collection['user_id'] ) )
    {
      $collection['__error']['user_id'] = true;
    }

  return ( $collection['__error'] )
    ? false
    : true;

}

function oai_collection_id ( $s )
{

  $sql = "SELECT collection_id FROM collection ";		
  $sql .= " WHERE id_purl='" . addslashes ( $s ) . "'";
  $sql .= " AND id_purl !='' ";

  $collection_id = db_exec_one ( $sql, 'collection_id' );

  return $collection_id;

}

function oai_collection_update ( &$collection )
{

  if ( ! isset ( $collection['collection_id'] ) || ! $collection['collection_id'] )
    {

      // if no collection id, see if local or purl exists

      $collection['collection_id'] = oai_collection_id ( $collection['id_purl'] );

      if ( ! $collection['collection_id'] )
        {
          $collection['collection_id'] = db_unique_id ( 'collection' );
          $sql = "INSERT INTO collection ";
          $sql .= " ( collection_id, status, date_created ) ";
          $sql .= " VALUES ";
          $sql .= " ( " . $collection['collection_id'] . ", " . STATUS_PREVIEW . ", '" . date ( 'YmdHis' ) . "' )";
          db_exec ( $sql );
        }
    }


  $sql = "UPDATE collection ";
  $sql .= " SET ";
  // datetime updated in this db
  $sql .= " date_updated = '" . date ( 'YmdHis' ) . "', ";
  // dates from harvest
  $sql .= " date_accessioned = '" . addslashes ( $collection['date_accessioned'] ) . "', ";
  $sql .= " date_modified = '" . addslashes ( $collection['date_modified'] ) . "', ";

  $sql .= " id_local = '" . addslashes ( $collection['id_local'] ) . "', ";
  $sql .= " id_purl = '" . addslashes ( $collection['id_purl'] ) . "', ";
  $sql .= " id_uri = '" . addslashes ( $collection['id_uri'] ) . "', ";

  $sql .= " name_primary = '" . addslashes ( $collection['name_primary'] ) . "', ";
  $sql .= " name_alternate = '" . addslashes ( $collection['name_alternate'] ) . "', ";
  $sql .= " name_abbreviated = '" . addslashes ( $collection['name_abbreviated'] ) . "', ";
  $sql .= " description_brief = '" . addslashes ( $collection['description_brief'] ) . "', ";
  $sql .= " description_full = '" . addslashes ( $collection['description_full'] ) . "', ";
  $sql .= " description_significance = '" . addslashes ( $collection['description_significance'] ) . "', ";
  $sql .= " description_rights = '" . addslashes ( $collection['description_rights'] ) . "', ";
  $sql .= " description_access = '" . addslashes ( $collection['description_access'] ) . "', ";
  $sql .= " description_note = '" . addslashes ( $collection['description_note'] ) . "', ";


  $sql .= " user_id = " . $collection['user_id'] . " ";
  $sql .= " WHERE collection_id = " . $collection['collection_id'] . " ";
  db_exec ( $sql );


  $sql = "DELETE FROM collection_coverage WHERE collection_id=" . $collection['collection_id'] . "";
  db_exec ( $sql );

  if ( isset ( $collection['coverage'] ) && is_array ( $collection['coverage'] ) )
    {
      foreach ( $collection['coverage'] as $mode => $coverage )
        {
          if ( ! is_array ( $coverage ) )
            {
              continue;
            }
          foreach ( $coverage as $type=>$values )
            {
              foreach ( $values as $value )
                {
                  if ( $value == '' )
                    {
                      continue;
                    }
                  $sql = "INSERT INTO collection_coverage ( collection_id, mode, type, value ) ";
                  $sql .= " VALUES ( " . $collection['collection_id'] . ", '" . addslashes ( $mode ) . "', '" . addslashes ( $type ) . "', '" . addslashes ( $value ) . "' )";
                  db_exec ( $sql );
                }
            }
        }
    }

  $sql = "DELETE FROM collection_related WHERE collection_id=" . $collection['collection_id'] . "";
  db_exec ( $sql );

  if ( isset ( $collection['related'] ) && is_array ( $collection['related'] ) )
    {
      foreach ( $collection['related'] as $mode => $related )
        {
          $sql = "INSERT INTO collection_related ( collection_id, `key`, type, description ) ";
          $sql .= " VALUES ( " . $collection['collection_id'] . ", '" . addslashes ( $related['key'] ) . "', '" . addslashes ( $related['type'] ) . "', '" . addslashes ( $related['value'] ) . "' )";
          db_exec ( $sql );
        }
    }





  // update search indexes

  $meta = array();

  if ( isset ( $collection['coverage'] ) && is_array ( $collection['coverage'] ) )
    {
      foreach ( $collection['coverage'] as $mode => $coverage )
        {
          if ( ! is_array ( $coverage ) )
            {
              continue;
            }
          foreach ( $coverage as $type=>$values )
            {
              if ( $type == 'from' || $type == 'to' )
                {
                  continue;
                }
              foreach ( $values as $value )
                {
                  $v = str_replace ( ', ', ',', $value );
                  $v = explode ( ',', $v );
                  $meta = array_merge ( $meta, $v );
                }
            }
        }
    }

  if ( isset ( $collection['related'] ) && is_array ( $collection['related'] ) )
    {
      foreach ( $collection['related'] as $mode => $related )
        {
          $meta[] = $related['value'];
        }
    }

  if ( isset ( $collection['subjects'] ) && is_array ( $collection['subjects'] ) )
    {
      foreach ( $collection['subjects'] as $mode => $subjects )
        {
          if ( is_array ( $subjects ) )
            {
              foreach ( $subjects as $subject )
                {
                  $meta[] = $subject;
                }
            }
        }
    }

  // dedup
  $m = array();
  foreach ( $meta as $v )
    {
      if ( $v == '' )
        {
          continue;
        }
      $m[$v] = $v;
    }
  $meta = implode ( ' ', $m );
      
  $sql = "UPDATE collection ";
  $sql .= " SET ";

  if ( $collection['description_full'] != '' ) 
    {
      // $sql .= " index_description = '" . addslashes ( substr ( $collection['description_full'], 0, 255 ) ) . "', ";
      $sql .= " index_description = '" . addslashes ( $collection['description_full'] ) . "', ";
    }
  else
    {
      // $sql .= " index_description = '" . addslashes ( substr ( $collection['description_brief'], 0, 255 ) ) . "', ";
      $sql .= " index_description = '" . addslashes ( $collection['description_brief'] ) . "', ";
    }

  $sql .= " index_meta = '" . addslashes ( $meta ) . "' ";
  $sql .= " WHERE collection_id = " . $collection['collection_id'] . " ";
  db_exec ( $sql );


  return $collection['collection_id'];

}



function oai_collection_status ( $collection_id, $status )
{

  if ( ! $collection_id || ! is_numeric ( $collection_id ) )
    {
      return false;
    }

  if ( ! in_array ( $status, array ( STATUS_PREVIEW, STATUS_PENDING, STATUS_LIVE, STATUS_ARCHIVE ) ) )
    {
      return false;
    }

  $sql = "UPDATE collection ";
  $sql .= " SET ";
  $sql .= " status = " . $status . " ";
  $sql .= " WHERE collection_id = " . $collection_id . " ";
  db_exec ( $sql );

}

function oai_collection_subjects ( $collection_id = false )
{

  if ( ! $collection_id )
    {
      return false;
    }

  $sql = "SELECT type, subject FROM collection_subject r";
  $sql .= " LEFT JOIN subject t ON t.subject_id = r.subject_id ";
  $sql .= " WHERE r.collection_id = " . $collection_id;
  $sql .= " ORDER BY subject";

  $r = db_exec ( $sql );

  if ( ! $r )
    {
      return false;
    }

  $subjects = array ();
  foreach ( $r as $v )
    {
      $subjects[$v['type']][] = $v['subject'];
    }

  return $subjects;
  
}




function oai_collection_delete ( $collection_id )
{

  if ( ! isset ( $collection_id ) || ! $collection_id )
    {
      return false;
    }

  $sql = "DELETE FROM collection ";
  $sql .= " WHERE collection_id = " . $collection_id . " ";
  db_exec ( $sql );

  $sql = "DELETE FROM collection_coverage WHERE collection_id=" . $collection_id . "";
  db_exec ( $sql );

  $sql = "DELETE FROM collection_related WHERE collection_id=" . $collection_id . "";
  db_exec ( $sql );

  $sql = "DELETE FROM collection_subject WHERE collection_id=" . $collection_id . "";
  db_exec ( $sql );

}



function oai_collection_subjects_set ( $collection_id, $subjects = false )
{

  // $subjects = array(
  // 					'local' => array (
  // 									  '0' => 'Toggles',
  // 									  '1' => 'Netsuke',
  // 									  '2' => 'Photographs',
  // 									  '3' => 'British Empire',
  // 									  '4' => 'Chinese Culture',
  // 									  '5' => 'Chinese Peoples',
  // 									  '6' => 'Chinese Temples',
  // 									  '7' => 'Domestic Life'
  // 									  ),
  // 					'scot' => array (
  // 									 '0' => 'Modernism'
  // 									 ));

  // http://forge.mysql.com/wiki/TagSchema

  // http://www.pui.ch/phred/archives/2005/04/tags-database-schemas.html

  $sql = "DELETE FROM collection_subject WHERE collection_id=" . $collection_id . "";
  db_exec ( $sql );

  if ( ! $subjects || ! is_array ( $subjects ) )
    {
      return false;
    }

  foreach ( $subjects as $type => $subs )
    {

      if ( ! is_array ( $subs ) )
        {
          continue;
        }

      $subjected = array();

      foreach ( $subs as $v )
        {

          $v = trim ( $v );

          if ( ! $v )
            {
              continue;
            }

          if ( ! isset ( $subjected[$v] ) )
            {

              // avoid duplicates
              $subjected[$v] = $v;

              $sql = "SELECT subject_id FROM subject WHERE subject='" . addslashes ( $v ) . "'";
              $subject_id = db_exec_one ( $sql, 'subject_id' );

              if ( ! $subject_id )
                {
                  $subject_id = db_unique_id ( 'subject' );
                  $sql = "INSERT INTO subject ( subject_id, subject ) VALUES ( $subject_id, '" . addslashes ( $v ) . "' )";
                  db_exec ( $sql );
                }

              $sql = "INSERT INTO collection_subject ( subject_id, type, collection_id ) VALUES ( $subject_id, '" . addslashes ( $type ) . "', " . $collection_id . " )";
              db_exec ( $sql );

            }

        }

    }


  if ( ! $nocleanup )
    {
      // this can be slow, so when doing bulk updates, you can disable i
      $sql = "SELECT t.subject_id AS subject_id FROM subject t ";
      $sql .= " LEFT JOIN collection_subject n ON t.subject_id = n.subject_id ";
      $sql .= " GROUP BY t.subject_id ";
      $sql .= " HAVING COUNT(DISTINCT(n.collection_id))=0 ";

      $r = db_exec_keys ( $sql, 'subject_id' );
      if ( $r )
        {
          $sql = "DELETE FROM subject WHERE subject_id IN (" . implode ( $r, ',' ) . ")";
          db_exec ( $sql );
        }

    }

}

function oai_subjects_clean_orphans ()
{

  // delete orphaned subjects

  $sql = "SELECT t.subject_id AS subject_id FROM subject t ";
  $sql .= " LEFT JOIN collection_subject n ON t.subject_id = n.subject_id ";
  $sql .= " GROUP BY t.subject_id ";
  $sql .= " HAVING COUNT(DISTINCT(n.collection_id))=0 ";

  $r = db_exec_keys ( $sql, 'subject_id' );
  if ( $r )
    {
      $sql = "DELETE FROM subject WHERE subject_id IN (" . implode ( $r, ',' ) . ")";
      db_exec ( $sql );
    }


}

function oai_subjects ()
{
 
  $sql = "SELECT DISTINCT(type) AS type FROM collection_subject ";
  $r = db_exec_keys ( $sql, 'type' );
  return $r;

}

function oai_xml_validate ( $f )
{

  $r = array();
  $r['error'] = false;
  $collections = 0;

  $xml = @simplexml_load_file( $f );

  if ( ! $xml )
    {
      $r['error'][] = 'Could not load XML - it is empty or malformed';
      return $r;
    }

  // determine type of xml file

  //var_dump ( $xml );


  if ( isset ( $xml->ListRecords ) )
    {
      // KES variant
      return oai_xml_validate_kes_oai ( $f );
    }

  // default
  return oai_xml_validate_mme_oai ( $f );

  return false;

}


function oai_xml_validate_mme_oai ( $f )
{

  $r = array();
  $r['error'] = false;
  $collections = 0;

  $xml = @simplexml_load_file( $f );


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

function oai_xml_validate_kes_oai ( $f )
{

  $r = array();
  $r['error'] = false;
  $collections = 0;

  $xml = @simplexml_load_file( $f );
  $objs = array();
  foreach ( $xml->ListRecords->record as $record )
    {
      $header = $record->header;
      $obj = $record->metadata->registryObjects->registryObject;

      if ( sizeof ( $obj->collection ) == 0 )
        {
          // no collection in this registryObject
          continue;
        }

      $objs[] = $obj;
    }

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


?>
<?php

core_load ( 'include', 'oai.inc' );

function script_oai_harvest ( $data )
{

  $x = file_get_contents ( SITE_ROOT . 'import/mme-test.xml' );
  $xml = new SimpleXMLElement($x);  
  $objs = $xml->xpath('/registryObjects/registryObject');

  while ( list ( $k, $obj ) = each ( $objs ) )
    {

      echo trim ( $obj->key ) . "\n";
      echo trim ( $obj->originatingSource ) . "\n";

      foreach ($obj->collection as $collection )
        {

          if ( ( $collection['type'] ) == 'collection' )
            {

              $c = array();

              $c['date_accessioned'] = trim ( $collection->dateAccessioned );
              $c['date_modified'] = trim ( $collection->dateModified );

              foreach ($collection->identifier as $id ) {
                $c['id_' . $id['type']] = trim ( $id );
              }

              foreach ($collection->name as $name ) {
                $c['name_' . $name['type']] = trim ( $name->namePart );
              }

              $c['subjects'] = array();
              foreach ($collection->subject as $subject ) {
                $subject_a = str_replace ( ' |', '|', trim ( $subject ) );
                $subject_a = str_replace ( '| ', '|', $subject_a );	
                $subject_a = explode ( '|', $subject_a );

                if ( is_array ( $c['subjects'][trim ($subject['type'] )] ) )
                  {
                    $subject_a = array_merge ( $c['subjects'][trim ($subject['type'] )], $subject_a );
                  }
                $c['subjects'][trim ($subject['type'] )] = $subject_a;
              }


              foreach ($collection->description as $description ) {
                if ( $description['type'] == 'accessRights' ) {
                  $description['type'] = 'access';
                }
                $c['description_' . $description['type']] = trim ( $description );
              }

              $c['related'] = false;

              foreach ($collection->relatedObject as $relatedObject ) {
                $rob = array();
                $rob['key'] = trim ( $relatedObject->key );
                $rob['type'] = trim ( $relatedObject->relation['type'] );
                $rob['description'] = trim ( $relatedObject->relation->description );
                $c['related'][] = $rob;
              }

              foreach ($collection->coverage->temporal->date as $date ) {
                $c['coverage']['temporal'][trim ( $date['type'] )][] = trim ( $date );
              }

              foreach ($collection->coverage->spatial as $loc ) {
                $c['coverage']['spatial'][trim ( $loc['type'] )][] = trim ( $loc );
              }
            }

          $c['collection_id'] = oai_collection_id ( $c['id_purl'] );

          echo ( $c['collection_id'] )
            ? '- ' . $c['name_primary']
            : '* ' . $c['name_primary'];
          echo "\n";

          $c['user_id'] = 1000;
          oai_collection_update ( $c );
          oai_collection_subjects_set ( $c['collection_id'], $c['subjects'] );

        }
    }

  exit;

  $users = oai_users();

  if ( ! $users )
    {
      return false;
    }

  foreach ( $users as $user )
    {

      if ( $user['oai_url'] == '' )
        {
          continue;
        }


      $s = file_get_contets ( $user['oai_url'] );

      if ( $s )
        {
          oai_xml_decode ( $s );
        }

      // foreach object create/update


    }

}

?>
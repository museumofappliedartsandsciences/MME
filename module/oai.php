<?php

// phm api
// username: museumex
// password: Meeyo7se

// for museumex account
define ( 'PHM_API_KEY', '7b990d057664707' );

// for psi6030 account
define ( 'PHM_API_KEY', '198e1b16437e775' );

core_load ( 'include', 'phm_widget.php' );

function module_oai ( &$data )
{

  // /activity/user/collection

  if ( $data['__key'] == 'index.xml' )
    {
      oai_complete_xml ( $data['user_id'] );
      exit;
    }

  if ( $data['__key'] == 'index.json' || $data['__key'] == 'json' )
    {
      oai_index_json();
      exit;
    }

  if ( strrpos ( $data['__key'], '.' ) !== false )
    {
      $data['format'] = substr ( $data['__key'], strrpos ( $data['__key'], '.' ) + 1 );
      $data['__key'] = substr ( $data['__key'], 0, strrpos ( $data['__key'], '.' ) );
    }

  if ( $data['__key'] != '' )
    {
      $path = explode ( '/', $data['__key'] );
    }

  if ( $path[1] == 'contact' || $path[2] == 'contact' )
    {
      $data['view'] = 'contact';
    }

  if ( is_numeric ( $path[1] ) )
    {
      $data['collection_id'] = $path[1]; // will be numeric uid of collection
    }

  if ( $path[0] )
    {
      $data['user_id'] = oai_user_id ( $path[0] ); // determine party_id from username
    }
 
  if ( $data['status'] == 'export' && user() && ( user_admin() || $data['user_id'] == user('user_id') ) && $data['status'] && is_array ( $data['items'] ) )
    {
      oai_collection_export_tsv ( $data['items'] );
      exit;
    }

  if ( user() && ( user_admin() || $data['user_id'] == user('user_id') ) && $data['status'] && is_array ( $data['items'] ) )
    {
      // bulk status, admin only
      foreach ( $data['items'] as $collection_id )
        {
          $collection = oai_collection ( $collection_id );

          if ( $data['status'] == STATUS_TRASH && user_admin() )
            {
              oai_collection_delete ( $collection_id );
            }
          else
            {
              oai_collection_status ( $collection_id, $data['status'] );
            }
        }
      unset ( $data['status'] );
      unset ( $data['items'] );
    }
  elseif ( user() && $data['status'] && $data['collection_id'] && is_numeric ( $data['collection_id'] ) )
    {
      // single status
      $collection = oai_collection ( $data['collection_id'] );

      if ( $data['status'] == STATUS_TRASH && user_admin() )
        {
          oai_collection_delete ( $data['collection_id'] );
          unset ( $path[1] );
          unset ( $data['collection_id'] );
          $data['user_id'] = $collection['user_id'];
        }
      elseif ( user_admin() || $collection['user_id'] == user('user_id' ) )
        {
          oai_collection_status ( $data['collection_id'], $data['status'] );
        }
      unset ( $data['status'] );
    }

  if ( $data['collection_id'] )
    {
      $data['collection'] = oai_collection ( $data['collection_id'] );
      if ( ! $data['collection'] )
        {
          unset ( $data['collection'] );
        }
      else
        {
          $data['user_id'] = $data['collection']['user_id'];
        }

    }

  if ( $data['user_id'] )
    {
      $data['user'] = oai_user ( $data['user_id'] );
      if ( ! $data['k'] )
        {
          $data['k'] = 'name_primary';
          $data['o'] = '1';
        }
    }
  else
    {
      if ( ! $data['k'] )
        {
          $data['k'] = 'name_primary';
          $data['o'] = '1';
        }
    } 

  if ( $data['q'] == '-1' )
    {
      $data['q'] = '';
      setcookie('q', false );
    }      
  elseif ( $data['q'] != '' )
    {
      setcookie('q', $data['q']);
    }
  else
    {
      $data['q'] = $_COOKIE['q'];
    }


  if ( $data['view'] == 'contact_process' )
    {
      if ( oai_user_contact_validate ( $data['form'] ) )
        {
          oai_user_contact_process( $data );
          $data['view'] = 'contact_processed';
        }
      else
        {
          $data['view'] = 'contact';
        }
    }

  if ( $data['user'] && $data['view'] == 'contact' )
    {
      // nop
    }
  elseif ( $data['user'] && ! $data['collection'] )
    {
      if ( user_admin() )
        {
          $data['collections'] = oai_collections ( array ( 'user_id'=>$data['user_id'], 'status'=>STATUS_ALL, 'k'=>$data['k'], 'o'=>$data['o'] ) );
          $data['collection_count'] = oai_collections ( array ( 'count'=>true, 'user_id'=>$data['user_id'], 'status'=>STATUS_ALL ) );
        }
      elseif ( user() && user('user_id') == $data['user_id'] )
        {
          $data['collections'] = oai_collections ( array ( 'user_id'=>$data['user_id'], 'status'=>STATUS_ALL, 'k'=>$data['k'], 'o'=>$data['o'] ) );
          $data['collection_count'] = oai_collections ( array ( 'count'=>true, 'user_id'=>$data['user_id'], 'status'=>STATUS_ALL ) );
        }
      else
        {
          $data['collections'] = oai_collections ( array ( 'user_id'=>$data['user_id'], 'k'=>$data['k'], 'o'=>$data['o'] ) );
          $data['collection_count'] = oai_collections ( array ( 'count'=>true, 'user_id'=>$data['user_id'] ) );
        }
    }

  if ( ! $data['user'] )
    {
      if ( user_admin() )
        {
          $data['users'] = oai_users ( array ( 'collection_status'=>STATUS_ALL ) );
        }
      else
        {
          $data['users'] = oai_users();
        }

      $data['collections'] = oai_collections ( array ( 'q'=> ( ( $data['view'] == 'search' ) ? $data['q'] : false ), 'status'=>STATUS_LIVE, 'k'=>'name_primary' ) );

      if ( $data['q'] )
        {
          $sql = "SELECT user_id, slug, title, url ";
          $sql .= " FROM user WHERE title LIKE '%" . addslashes ( $data['q'] ) . "%' ";
          $sql .= " ORDER BY title";
          $data['institutions'] = db_exec ( $sql, 'user_id' );
        }


    }

  if ( $data['format'] == 'xml' )
    {
      if ( $data['user'] )
        {
          oai_user_collections_xml ( $data['user_id'] );
          exit;
        }
    }

  if ( $data['format'] == 'json' )
    {
      if ( $data['collection'] )
        {
          oai_collection_json ( $data['collection'] );
          exit;
        }
      if ( $data['user'] )
        {
          oai_user_collections_json ( $data['user_id'] );
          exit;
        }
    }


  $s = array();

  if ( $data['user'] )
    {
      $s[] = $data['user']['title'];
    }

  if ( $data['collection'] )
    {
      $s[] = $data['collection']['name_primary'];
    }

  // if ( user_admin() )
  //   {
  //     $sql = "SELECT COUNT(*) AS count FROM collection c ";
  //   }
  // else
  //   {
  $sql = "SELECT COUNT(*) AS count FROM collection c WHERE c.status=" . STATUS_LIVE;
  // }

  if ( ! $data['user'] )
    {
      $data['collection_count'] = db_exec_one ( $sql, 'count' );
    }

  core_set_title ( implode ( ' | ', $s ) );
  core_head_add ( 'jquery' );
  core_head_add ( 'jquery.highlight-3.js' );
  core_head_add ( 'phm_image_grid.js' );
  core_head_add ( 'dataset.js' );
  core_head_add ( 'phm_image_grid.css' );

}

function oai_user_collections_xml ( $user_id )
{

  $user = oai_user ( $user_id );

  $collections = oai_collections ( array ( 'user_id'=>$user_id, 'status'=>STATUS_LIVE ) );

  header ( 'Content-type: application/xml' );

  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<registryObjects xmlns="http://ands.org.au/standards/rif-cs/registryObjects" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ands.org.au/standards/rif-cs/registryObjects http://services.ands.org.au/documentation/rifcs/schema/registryObjects.xsd">' . "\n\n";;

  oai_user_xml ( $user );

  echo "\n";
  echo '<registryObject group="Museum Metadata Exchange">' . "\n";
  //echo '<registryObject group="' . $user['title'] . '">' . "\n";
  
  echo '<key>';
  echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] ) ) . '.xml';
  echo '</key>';

  echo "\n";
  echo '<originatingSource type="authoritative">';
  echo $user['title'];
  echo '</originatingSource>';

  if ( is_array ( $collections ) )
    {
      foreach ( $collections as $collection )
        {
          oai_collection_object_xml ( $collection, $user );
        }
    }

  echo '</registryObject>';
  
  echo "\n";
  echo '</registryObjects>' . "\n";
  exit;

}


function oai_complete_xml ( $user_id = false )
{

  if ( $user_id && ! is_numeric ( $user_id ) )
    {
      $user_id = oai_user_id ( $user_id );
    }

  $users = oai_users ();

  if ( $user_id )
    {
      foreach ( $users as $k => $v )
        {
          if ( $v['user_id'] != $user_id )
            {
              unset ( $users[$k] );
            }
        }
    }

  header ( 'Content-type: application/xml' );

  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

  echo '<registryObjects xsi:schemaLocation="http://ands.org.au/standards/rif-cs/registryObjects http://services.ands.org.au/home/orca/schemata/registryObjects.xsd" xmlns="http://ands.org.au/standards/rif-cs/registryObjects" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
  echo "\n\n";

  echo "\n\n";
  echo file_get_contents ( SITE_ROOT . 'data/oai/service-record.xml' );
  echo "\n\n";

  foreach ( $users as $user )
    {

      oai_user_xml ( $user );

      $collections = oai_collections ( array ( 'user_id'=>$user['user_id'], 'status'=>STATUS_LIVE ) );

      if ( is_array ( $collections ) )
        {
          foreach ( $collections as $collection )
            {
              echo "\n";
              echo "\n";
              echo '<registryObject group="Museum Metadata Exchange">' . "\n";
              //echo '<registryObject group="' . $user['title'] . '">';

              echo "\n";
              echo '<key>';
              //echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] ) );
              echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] . '/' . $collection['collection_id'] ) );
              echo '</key>';

              echo "\n";
              echo '<originatingSource type="authoritative">';
              echo $user['title'];
              echo '</originatingSource>';

              oai_collection_object_xml ( $collection, $user );

              echo "\n";
              echo "\n";
              echo '</registryObject>';
            }
        }
      echo "\n";
      echo "\n";
    }
  
  echo "\n";
  echo "\n";
  echo '</registryObjects>' . "\n";

  exit;

}


function oai_collection_object_xml ( $collection, $user )
{
  echo "\n";
  echo "\n";
  echo '<collection type="collection">';

  echo "\n";
  // echo '<dateAccessioned>';
  // echo oai_encode ( $collection['date_accessioned'] );
  // echo '</dateAccessioned>';

  // echo "\n";
  // echo '<dateModified>';
  // echo oai_encode ( $collection['date_modified'] );
  // echo '</dateModified>';

  if ( $collection['id_purl'] != '' )
    {
      echo "\n";
      echo '<identifier type="purl">';
      echo oai_encode ( $collection['id_purl'] );
      echo '</identifier>';
    }

  if ( $collection['id_uri'] != '' )
    {
      echo "\n";
      echo '<identifier type="uri">';
      echo oai_encode ( $collection['id_uri'] );
      echo '</identifier>';
    }

  echo "\n";
  echo '<identifier type="local">';
  echo oai_encode ( $collection['id_local'] );
  echo '</identifier>';

  echo "\n";
  echo '<name type="primary">';
  echo '<namePart>';
  echo oai_encode ( $collection['name_primary'] );
  echo '</namePart>';
  echo '</name>';

  if ( $collection['name_alternate'] != '' )
    {
      echo "\n";
      echo '<name type="alternate">';
      echo '<namePart>';
      echo oai_encode ( $collection['name_alternate'] );
      echo '</namePart>';
      echo '</name>';
    }

  if ( $collection['name_abbreviated'] != '' )
    {
      echo "\n";
      echo '<name type="abbreviated">';
      echo '<namePart>';
      echo oai_encode ( $collection['name_abbreviated'] );
      echo '</namePart>';
      echo '</name>';
    }

  echo "\n";
  echo '<relatedObject>';
  echo '<key>';
  echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] ) );
  echo '</key>';
  echo '<relation type="isManagedBy"/>';
  echo '</relatedObject>';

  echo "\n";
  // echo '<relatedObject>' . "\n";
  // echo '<key>http://museumex.org</key>' . "\n";
  // echo '<relation type="isLocatedIn"/>' . "\n";
  // echo '</relatedObject>' . "\n";

  echo "\n";
  echo '<relatedObject>' . "\n";
  echo '<key>http://museumex.org</key>' . "\n";
  echo '<relation type="isAvailableThrough">' . "\n";
  echo '<description>The Museum Metadata Exchange is an aggregator service which provides a finding aid for researchers and others by describing collections held by Museums and other collecting institutions in Australia. The service gives an overview of the collection and details of the holding institution.</description>' . "\n";
  echo '</relation>' . "\n";
  echo '</relatedObject>' . "\n";


  echo "\n";
  echo '<location>';
  echo '<address>';
  echo '<electronic type="url">';
  echo '<value>';
  echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] . '/' . $collection['collection_id'] ) );
  echo '</value>';
  echo '</electronic>';
  echo '</address>';
  echo '</location>';
  echo "\n";

  if ( is_array ( $collection['subjects'] ) )
    {
      foreach ( $collection['subjects'] as $type => $subjects )
        {
          foreach ( $subjects as $sxx )
            {
              echo "\n";
              echo '<subject type="' . $type . '">';
              echo oai_encode ( $sxx );
              echo '</subject>';
            }
          // echo "\n";
          // echo '<subject type="' . $type . '">';
          // echo oai_encode ( implode ( '|',$subjects ) );
          // echo '</subject>';
        }
    }

  echo "\n";
  echo '<description type="brief">';
  echo oai_encode( $collection['description_brief'] );

  echo '</description>';

  echo "\n";
  echo '<description type="full">';
  echo oai_encode ( $collection['description_full'] );
  echo '</description>';

  if ( $collection['description_significance'] != '' )
    {
      echo "\n";
      echo '<description type="significance">';
      echo oai_encode ( $collection['description_significance'] );
      echo '</description>';
    }

  echo "\n";
  echo '<description type="rights">';
  if ( $collection['description_rights'] == '' )
    {
      echo 'Some material included in this collection may be subject to copyright';
    }
  else
    {
      echo oai_encode ( $collection['description_rights'] );
    }
  echo '</description>';

  echo "\n";
  echo '<description type="accessRights">';
  echo oai_encode ( $collection['description_rights'] );
  echo '</description>';

  if ( $collection['description_note'] != '' )
    {
      echo "\n";
      echo '<description type="note">';
      echo oai_encode ( $collection['description_note'] );
      echo '</description>';
    }

  if ( is_array ( $collection['coverage'] ) )
    {
      echo "\n";
      foreach ( $collection['coverage'] as $mode => $coverage )
        {
          if ( $mode == 'temporal' )
            {
              echo "\n";
              echo '<coverage>';
              echo "\n";
              echo '<temporal>';
            }
          foreach ( $coverage as $type => $values )
            {
              foreach ( $values as $value )
                {
                  if ( $mode == 'temporal' )
                    {
                      // map from internal to external type
                      if ( $type == 'from' )
                        {
                          $type = 'dateFrom';
                        }
                      if ( $type == 'to' )
                        {
                          $type = 'dateTo';
                        }
                      echo "\n";
                      echo '<date type="' . $type . '" dateFormat="UTC">';
                      echo oai_encode ( $value );
                      echo '</date>';
                    }
                  if ( $mode == 'spatial' )
                    {
                      echo "\n";
                      echo '<coverage>';
                      echo "\n";
                      echo '<spatial type="' . $type . '">';
                      echo oai_encode ( $value );
                      echo '</spatial>';
                      echo "\n";
                      echo '</coverage>';
                    }
                }
            }
          if ( $mode == 'temporal' )
            {
              echo "\n";
              echo '</temporal>';
              echo "\n";
              echo '</coverage>';
            }
        }
    }


  if ( is_array ( $collection['related'] ) )
    {
      foreach ( $collection['related'] as $object )
        {
          if ( $object['description'] == '' )
            {
              continue;
            }
          echo '<relatedObject>';

          echo '<key>';
          echo $object['key'];
          echo '</key>';

          echo '<relation type="' . $object['type'] . '">';
          echo '<description>';
          echo str_replace ( '&', '&amp;', oai_encode ( $object['description'] ) );
          echo '</description>';
          echo '</relation>';

          echo '</relatedObject>';
        }
    }

  echo "\n";
  echo '</collection>';
  echo "\n";

}

function oai_encode ( $s )
{
  // $s = mb_convert_encoding( $s, 'UTF-8' );
  // $s = str_replace ( '&ndash;', '&#8211;', $s );
  $s = strip_tags ($s );
  $s = str_replace ( '&', '&amp;', $s );
  return $s;
}


function oai_user_xml ( $user )
{
  // echo '<!-- begin party object -->' . "\n"; 

  echo "\n";
  echo "\n";
  echo '<registryObject group="Museum Metadata Exchange">' . "\n";
  //echo '<registryObject group="' . $user['title'] . '">' . "\n";

  echo '<key>';
  echo html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] ) );
  echo '</key>';
  echo "\n";

  echo '<originatingSource>http://museumex.org</originatingSource>' . "\n";

  echo '<party type="group">' . "\n";

  echo '<name type="primary">' . "\n";
  echo '<namePart>';
  echo oai_encode ( $user['title'] );
  echo '</namePart>' . "\n";
  echo '</name>' . "\n";

  echo '<name type="alternate">' . "\n";
  echo '<namePart>';
  echo oai_encode ( $user['title_alternate'] );
  echo '</namePart>' . "\n";
  echo '</name>' . "\n";

  echo '<name type="abbreviated">' . "\n";
  echo '<namePart>';
  echo oai_encode ( $user['title_abbreviated'] );
  echo '</namePart>' . "\n";
  echo '</name>' . "\n";

  // echo '<!-- location -->' . "\n"; 

  echo '<location>' . "\n";  
  echo '<address>' . "\n";

  echo '<electronic type="url">' . "\n";
  echo '<value>';
  echo $user['url'];
  echo '</value>' . "\n";
  echo '</electronic>' . "\n";

  echo '<electronic type="email">' . "\n";
  echo '<value>';
  echo oai_encode ( $user['email'] );
  echo '</value>' . "\n";
  echo '</electronic>' . "\n";


  $addr =  array ( $user['address_street'] , $user['address_city'], $user['address_state'], $user['address_postcode'], $user['address_country'] );
  if ( trim ( implode ( $addr ) ) != '' )
    {
      $structured = ( sizeof ( $addr ) == 5 );

      echo '<physical type="streetAddress">' . "\n";

      if ( $user['address_street'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'streetName'  :'text' ) . '">';
          echo oai_encode ( $user['address_street'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['address_city'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'suburbOrPlaceOrLocality'  :'text' ) . '">';
          echo oai_encode ( $user['address_city'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['address_state'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'stateOrTerritory'  :'text' ) . '">';
          echo oai_encode ($user['address_state'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['address_postcode'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'postCode'  :'text' ) . '">';
          echo oai_encode ( $user['address_postcode'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['address_country'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'country'  :'text' ) . '">';
          echo oai_encode ( $user['address_country'] );
          echo '</addressPart>' . "\n";
        }

      echo '</physical>' . "\n";
    }

  $addr =  array ( $user['postal_street'] , $user['postal_city'], $user['postal_state'], $user['postal_postcode'], $user['postal_country'] );
  if ( trim ( implode ( $addr ) ) != '' )
    {
      $structured = ( sizeof ( $addr ) == 5 );
      echo '<physical type="postalAddress">' . "\n";

      if ( $user['postal_street'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'streetName'  :'text' ) . '">';
          echo oai_encode ( $user['postal_street'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['postal_city'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'suburbOrPlaceOrLocality'  :'text' ) . '">';
          echo oai_encode ( $user['postal_city'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['postal_state'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'stateOrTerritory'  :'text' ) . '">';
          echo oai_encode ($user['postal_state'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['postal_postcode'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'postCode'  :'text' ) . '">';
          echo oai_encode ( $user['postal_postcode'] );
          echo '</addressPart>' . "\n";
        }

      if ( $user['postal_country'] != '' )
        {
          echo '<addressPart type="' . ( ( $structured ) ? 'country'  :'text' ) . '">';
          echo oai_encode ( $user['postal_country'] );
          echo '</addressPart>' . "\n";
        }

      echo '</physical>' . "\n";
    }

  echo '</address>' . "\n";
  echo '</location>' . "\n";


  // echo '<!-- description -->' . "\n"; 

  echo '<description type="brief">';
  echo oai_encode ( $user['description'] );
  echo '</description>' . "\n";
  
  echo '</party>' . "\n";
  echo '</registryObject>' . "\n";

  echo "\n";
  echo "\n";

}

// json

function oai_index_json ()
{

  $users = oai_users ();

  $j = array();

  foreach ( $users as $user )
    {
      $j[] = array (
                    'key'=>$user['username'],
                    'title'=>$user['title'],
                    'collections'=>$user['collections'],
                    'url'=> html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$user['username'] ) )
                    );

    }
  header ( 'Content-type: application/json' );
  echo json_encode ( $j, true );
  exit;

}

function oai_user_collections_json ( $user_id )
{

  $u = oai_user ( $user_id );
  $user = array (
                 'key'=>$u['username'],
                 'title'=>$u['title'],
                 'description'=>$u['description'],
                 'url'=>$u['url'],
                 'address_street'=>$u['address_street'],
                 'address_city'=>$u['address_city'],
                 'address_state'=>$u['address_state'],
                 'address_postcode'=>$u['address_postcode'],
                 'address_country'=>$u['address_country'],
                 'address_street'=>$u['address_street']
                 );

  $c = oai_collections ( array ( 'user_id'=>$user_id, 'status'=>STATUS_LIVE ) );
  $user['collections'] = array();

  if ( is_array ( $c ) )
    {
      foreach ( $c as $x )
        {
          $user['collections'][] = array ( 
                                          'collection_id'=> $x['collection_id'], 
                                          'title'=> $x['name_primary'],
                                          'url'=> html_url ( 'oai', array ( '__absolute'=>true, '__key'=>$u['username'] . '/' . $x['collection_id'] ) )
                                          

                                           );

          // unset ( $collection['status'] );
          // unset ( $collection['user_id'] );
          // unset ( $collection['index_description'] );
          // unset ( $collection['index_meta'] );
        }
    }

  //$user['collections'] = $collections;
  echo json_encode ( $user, true );

  exit;

}

function oai_collection_json ( $collection )
{
  
  header ( 'Content-type: application/json' );

  unset ( $collection['status'] );
  unset ( $collection['index_meta'] );
  unset ( $collection['index_description'] );
  unset ( $collection['user_id'] );

  echo json_encode ( $collection, true ); 
  exit;

}


// tsv

function oai_collection_export_tsv ( $items )
{


  // http://www.daniweb.com/web-development/php/threads/100451

  // spit ( $items );
  // spit (  oai_collection ( 1684 ) );
  // exit;

  // testing
  // foreach ( $items as $collection_id )
  //   {
  //     $c = oai_collection ( $collection_id );
  //     spit ( $c );
  //   }
  // exit;

  ob_start();
  echo '<table>' . "\n";

  echo '<tr>';
  echo '<td>name_primary</td>';
  echo '<td>name_alternative</td>';
  echo '<td>name_abbreviated</td>';
  echo '<td>ID_local</td>';
  echo '<td>online</td>';
  echo '<td>description_brief</td>';
  echo '<td>description_full</td>';
  echo '<td>description_significance</td>';
  echo '<td>description_copyright</td>';
  echo '<td>description_access_rights</td>';
  echo '<td>description_notes</td>';
  echo '<td>subject_local</td>';
  echo '<td>subject_pont</td>';
  echo '<td>subject_scot</td>';
  echo '<td>subject_APT</td>';
  echo '<td>subject_person_organisation</td>';
  echo '<td>date_from</td>';
  echo '<td>date_to</td>';
  echo '<td>date_text</td>';
  echo '<td>places</td>';
  echo '<td>collection_relation_type</td>';
  echo '<td>related_collection_mme_uri</td>';
  echo '<td>related_collection_name</td>';
  echo '</tr>' . "\r\n";

  if ( ! $items )
    {
      exit;
    }
  $collections = array();
  foreach ( $items as $collection_id )
    {
      $c = oai_collection ( $collection_id );

      echo '<tr>';

      echo '<td>';
      echo $c['name_primary'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['name_alternate'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['name_abbreviated'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['id_local'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['id_uri'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_brief'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_full'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_significance'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_rights'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_access'] . "\t";
      echo '</td>';

      echo '<td>';
      echo $c['description_note'] . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['subjects'] ) && isset ( $c['subjects']['local'] ) && is_array ( $c['subjects']['local'] ) )
        ? implode ( '|', $c['subjects']['local'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['subjects'] ) && isset ( $c['subjects']['pont'] ) && is_array ( $c['subjects']['pont'] ) )
        ? implode ( '|', $c['subjects']['pont'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['subjects'] ) && isset ( $c['subjects']['scot'] ) && is_array ( $c['subjects']['scot'] ) )
        ? implode ( '|', $c['subjects']['scot'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['subjects'] ) && isset ( $c['subjects']['apt'] ) && is_array ( $c['subjects']['apt'] ) )
        ? implode ( '|', $c['subjects']['apt'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['subjects'] ) && isset ( $c['subjects']['person_org'] ) && is_array ( $c['subjects']['person_org'] ) )
        ? implode ( '|', $c['subjects']['person_org'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['coverage'] ) && isset ( $c['coverage']['temporal'] ) && isset ( $c['coverage']['temporal']['dateFrom'] ) && isset ( $c['coverage']['temporal']['dateFrom'][0] ) )
        ? implode ( '; ', $c['coverage']['temporal']['dateFrom'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['coverage'] ) && isset ( $c['coverage']['temporal'] ) && isset ( $c['coverage']['temporal']['dateTo'] ) && isset ( $c['coverage']['temporal']['dateTo'][0] ) )
        ? implode ( '; ', $c['coverage']['temporal']['dateTo'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['coverage'] ) && isset ( $c['coverage']['temporal'] ) && isset ( $c['coverage']['temporal']['text'] ) && isset ( $c['coverage']['temporal']['text'][0] ) )
        ? implode ( '; ', $c['coverage']['temporal']['text'] ) . "\t"
        : '' . "\t";
      echo '</td>';

      echo '<td>';
      echo ( isset ( $c['coverage'] ) && isset ( $c['coverage']['spatial'] ) && isset ( $c['coverage']['spatial']['text'] ) && isset ( $c['coverage']['spatial']['text'][0] ) )
        ? implode ( '; ', $c['coverage']['spatial']['text'] ). "\t"
        : '' . "\t";
      echo '</td>';
      

      echo '<td>';
      if ( is_array ( $c['related'] ) )
        {
          $a = array();
          foreach ( $c['related'] as $r )
            {
              $a[] = $r['type'];
            }
          echo implode ( '|', $a );
        }
      echo '</td>';

      echo '<td>';
      if ( is_array ( $c['related'] ) )
        {
          $a = array();
          foreach ( $c['related'] as $r )
            {
              $a[] = ( $r['key'] == 'http://' ) 
                ? ''
                : $r['key'];
            }
          echo implode ( '|', $a );
        }
      echo '</td>';

      echo '<td>';
      if ( is_array ( $c['related'] ) )
        {
          $a = array();
          foreach ( $c['related'] as $r )
            {
              $a[] = $r['description'];
            }
          echo implode ( '|', $a );
        }
      echo '</td>';

      // related mme
     
      echo '</tr>';
      echo "\r\n";

    }

  echo '</table>' . "\n";

  $output = ob_get_clean();

  // Convert to UTF-16LE
  $output = mb_convert_encoding($output, 'UTF-16LE', 'UTF-8'); 

  // Prepend BOM
  @unlink( SITE_ROOT . 'cache/mme.xls'); 
  $fh = fopen( SITE_ROOT . 'cache/mme.xls', "w");
  fwrite($fh, "\xFF\xFE");
  fwrite($fh, $output);
  fclose($fh);

  header("Content-type: application/x-msexcel"); 
  header('Content-Disposition: attachment;  filename="mme.xls"');
  readfile( SITE_ROOT . 'cache/mme.xls'); 
  @unlink( SITE_ROOT . 'cache/mme.xls'); 

  exit;

}


function oai_user_contact_validate ( &$form )
{

  if ( ! $form['name'] ) 
    {
      $form['__error']['name'] = true;
    }

  if ( $form['email'] == '' ) 
    {
      $form['__error']['email'] = true;
    }
  elseif ( ! validate_email( $form['email'] ) ) 
    {
      $form['__error']['email'] = true;
    }

  if ( ! $form['message'] ) 
    {
      $form['__error']['message'] = true;
    } 

  return ( $form['__error'] )
    ? false
    : true;

}

function oai_user_contact_process ( $data ) 
{

  $s = 'Date: ' . date ( 'D d F Y H:i:s T' );
  
  $s .= "\n";
  $s .= 'From: ' . $data['form']['name'] . ' <' . $data['form']['email'] . '>';
  
  $user = oai_user ( $data['user_id'] );
  $collection = oai_collection ( $data['collection_id'] );

  if ( $data['collection_id'] )
    {
      $s .= "\n";
      $s .= "\n";
      $s .= 'Regarding: ' . $collection['name_primary']. "\n";
      $s .= "\n";
      $s .= html_url ( 'oai', array ( '__absolute'=>true ) ) . '/' . $user['username'] . '/' . $collection['collection_id'];
      $s .= "\n";
    }

  $s .= "\n";
  $s .= "\n";
  $s .= wordwrap ( stripslashes ( $data['form']['message'] ) );

  mail ( 
        $user['email_contact'],
        'Message via Museum Metadata Exchange',
        $s, 
        'From: ' . $data['form']['name'] . ' <' . $data['form']['email'] . '>',
        '-f' . $data['form']['email']
         );

  mail ( 
        EMAIL_WEBMASTER,
        '[copy] Message via Museum Metadata Exchange',
        $s, 
        'From: ' . $data['form']['name'] . ' <' . $data['form']['email'] . '>',
        '-f' . $data['form']['email']
         );

}

?>
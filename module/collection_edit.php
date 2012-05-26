<?php

core_load ( 'include', 'oai.inc' );

function access_collection_edit ( &$data )
{
  return ( user() );
}

function module_collection_edit ( &$data )
{

  if ( $data['view'] == 'update' )
    {

      // unpack incoming data
      if ( $data['collection']['subjects'] != '' && ! is_array ( $data['collection']['subjects'] ) )
        {
          $s = explode ( "\n", trim ( $data['collection']['subjects'] ) );
          $subs = array();
          foreach ( $s as $v )
            {
              list ( $type, $value ) = explode ( '|', $v );
              if ( ! $subs[$type] )
                {
                  $subs[$type] = array();
                }
              $subs[$type][] = trim ( $value );
            }
          $data['collection']['subjects'] = $subs;
        }

      // unpack incoming data

      if ( $data['collection']['related'] != '' && ! is_array ( $data['collection']['related'] ) )
        {
          $s = explode ( "\n", trim ( $data['collection']['related'] ) );
          $subs = array();
          foreach ( $s as $v )
            {
              list ( $type, $key, $value ) = explode ( '|', trim ( $v ) );
              $subs[] = array ( 'type'=>$type, 'key'=>$key, 'value'=>$value );
            }
          $data['collection']['related'] = $subs;
        }

      if ( $data['collection']['coverage']['temporal'] != '' && ! is_array ( $data['collection']['coverage']['temporal'] ) )
        {
          $s = explode ( "\n", trim ( $data['collection']['coverage']['temporal'] ) );
          $subs = array();
          foreach ( $s as $v )
            {
              list ( $type, $value ) = explode ( '|', $v );
              if ( ! $subs[$type] )
                {
                  $subs[$type] = array();
                }
              $subs[$type][] = trim ( $value );
            }
          $data['collection']['coverage']['temporal'] = $subs;
        }

      if ( $data['collection']['coverage']['spatial'] != '' && ! is_array ( $data['collection']['coverage']['spatial'] ) )
        {
          $s = explode ( "\n", trim ( $data['collection']['coverage']['spatial'] ) );
          $subs = array();
          foreach ( $s as $v )
            {
              list ( $type, $value ) = explode ( '|', $v );
              if ( ! $subs[$type] )
                {
                  $subs[$type] = array();
                }
              $subs[$type][] = trim ( $value );
            }
          $data['collection']['coverage']['spatial'] = $subs;
        }
    }  

  if ( $data['view'] == 'update' && oai_collection_validate ( $data['collection'] ) )
    {

      $data['collection']['date_accessioned'] = format_array_to_utc ( $data['collection']['date_accessioned'] );
      $data['collection']['date_modified'] = format_array_to_utc ( $data['collection']['date_modified'] ); 

      oai_collection_update ( $data['collection'] );
      oai_collection_subjects_set ( $data['collection']['collection_id'], $data['collection']['subjects'] );

      core_set_module ( 'oai', array ( 'collection_id' => $data['collection']['collection_id'] ) );
      return true;
    }

  if ( substr ( $data['__key'], -5, 5 ) == '.json' )
    {
      if ( $data['__key'] != '_new.json' )
        {
          $data['collection_id'] = substr ( $data['__key'], 0, strlen ( $data['__key'] ) -5 );
        }
      $data['view'] = 'json';
    }

  if ( $data['view'] == 'json' )
    {

      $j = array();

	  
      $collection = oai_collection ( $data['collection_id'] );

      if ( $collection )
        {
          // override so we can change the description to value for jquery
          $sql = "SELECT type, `key`, description AS value FROM collection_related ";		
          $sql .= " WHERE collection_id=" . addslashes ( $data['collection_id'] ) . "";
          $sql .= " ORDER BY type ";
          $collection['related'] = db_exec ( $sql );
        }

      $j['collection'] = $collection;
      $j['subjects'] = oai_subjects();

      $sql = "SELECT DISTINCT(type) AS type FROM collection_related ";		
      $j['related'] = db_exec_keys ( $sql, 'type' );

      $sql = "SELECT DISTINCT(type) AS type FROM collection_coverage ";		
      $sql .= " WHERE mode='temporal' ";
      $j['temporal'] = db_exec_keys ( $sql, 'type' );

      $sql = "SELECT DISTINCT(type) AS type FROM collection_coverage ";		
      $sql .= " WHERE mode='spatial' ";
      $j['spatial'] = db_exec_keys ( $sql, 'type' );


      header ( 'Content-type: application/json' );
      echo json_encode ( $j, 1 );
      exit;

    }

  if ( ! $data['view'] && $data['collection_id'] && is_numeric ( $data['collection_id'] ) )
    {
      $data['collection'] = oai_collection ( $data['collection_id'] );

      if ( ! user_admin() && $data['collection']['user_id'] != user('user_id') )
        {
          core_set_module ( 'handler_404' );
          return false;
        }
      $data['user_id'] = $data['collection']['user_id'];

    }

  if ( ! $data['collection'] )
    {

      $data['collection'] = array();
      $data['collection']['name_primary'] = 'New Collection';

	  
      if ( $data['user_id'] && user_admin() )
        {
          $data['collection']['user_id'] = $data['user_id'];
        }
      else
        {
          $data['collection']['user_id'] = user('user_id');;
        }
    }

  if ( $data['user_id'] )
    {
      $data['user'] = oai_user ( $data['user_id'] );
    }

  core_head_add ( 'jquery' );
  core_head_add ( 'underscore.js' );
  core_head_add ( 'oai.css' );

  core_head_add ( 'terms-picker.js' );
  core_head_add ( 'terms-picker.css' );

}

function format_array_to_utc ( $a = false )
{

  if ( ! $a )
    {
      return false;
    }

  if ( ! is_array ( $a ) )
    {
      return false;
    }

  //2008-10-20T13:00:00Z
  if ( strpos ( implode ( '', $a ), '-' ) !== false )
    {
      // any '-' values mean no value set, and invalid date, but
      // silent ignore
      return false;
    }
  
  $s =  sprintf ( '%04d', $a['year'] )
    . '-' 
    . sprintf ( '%02d', $a['month'] )
    . '-'
    . sprintf ('%02d', $a['day'] )
    . 'T'
    . sprintf ('%02d', $a['hour'] )
    . ':'
    . sprintf ('%02d', $a['minute'] )
    . ':'
    . '00'
    . 'Z';

  return $s;

}

?>
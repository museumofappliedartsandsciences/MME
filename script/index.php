<?php

core_load ( 'include','oai.inc' );

function script_index ( $data )
{

  $sql = "SELECT collection_id FROM collection";
  $r = db_exec_keys ( $sql, 'collection_id' );

  foreach ( $r as $id )
    {

      $collection = oai_collection ( $id );
      echo $collection['name_primary'] . "\n";

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
          //$sql .= " index_description = '" . addslashes ( substr ( $collection['description_full'], 0, 255 ) ) . "', ";
          $sql .= " index_description = '" . addslashes ( $collection['description_full'] ) . "', ";
        }
      else
        {
          $sql .= " index_description = '" . addslashes ( $collection['description_brief'] ) . "', ";
        }

      $sql .= " index_meta = '" . addslashes ( $meta ) . "' ";
      $sql .= " WHERE collection_id = " . $collection['collection_id'] . " ";
      db_exec ( $sql );
      

    }

}

?>
<?php

core_load ( 'include', 'oai.inc' );

function access_user_delete ( &$data )
{

  return user_admin();

}

function module_user_delete ( &$data )
{

  if ( $data['user_id'] && is_numeric ( $data['user_id'] ) )
    {
      $sql = "DELETE FROM user ";
      $sql .= " WHERE user_id = " . $data['user_id'] . " ";
      db_exec ( $sql );

      $collections = oai_collections ( $data['user_id'] );
      if ( $collections )
        {
          foreach ( $collections as $collection )
            {
              oai_collection_delete ( $collection_id );
            }
        }

    }

  core_set_module ( 'oai', array ( 'user_id' => $data['user']['user_id'] ) );
  return true;

}


?>
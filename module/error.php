<?php

function module_error ( $data = '' ) 
{

  core_set_data ( 'title', 'Error' ) ;
  core_set_template ( 'index' ) ;

  return 0;

}

?>
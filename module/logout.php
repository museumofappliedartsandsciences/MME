<?php

function module_logout ( &$data ) 
{	

  user_logout();
  header ( 'Location: /' );

}

?>

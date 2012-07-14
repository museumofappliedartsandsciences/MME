<?php

core_load ( 'include', 'oai.inc' );

function script_xml_test ( &$data )
{

  $f = SITE_ROOT . 'data/oai/kes-sample.xml';
  $r = oai_xml_validate ( $f );
  spit ( $r );

}

?>
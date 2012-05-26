<?php

function macro_page_head ( &$data )
{

}

function render_page_head ( $data )
{

  $r = core_head();

  if ( isset ( $r['css'] ) && is_array ( $r['css'] ) )
    {
      foreach ( $r['css'] as $k => $v )
        {
          if ( file_exists ( SITE_ROOT . 'htdocs/css/' . $v ) || file_exists ( CORE_ROOT . 'htdocs/css/' . $v ) )
            {
              echo "\n";
              echo  '<link rel="stylesheet" href="/css/' . $v . '" type="text/css" />';
            }
        }
    }
  
  if ( isset ( $r['js'] ) && is_array ( $r['js'] ) )
    {
      foreach ( $r['js'] as $k => $v )
        {

          // allow for parameters being passed to the .js file
          // (prototype does this)

          $f = ( strpos ( $v, '?' ) !== false )
            ? substr ( $v, 0, strpos ( $v, '?' ) )
            : $v;
		  
          if ( file_exists ( SITE_ROOT . 'htdocs/js/' . $f ) || file_exists ( CORE_ROOT . 'htdocs/js/' . $f ) )
            {
              echo "\n";
              echo  '<script type="text/javascript" src="/js/' . $v . '"></script>';
            }
        }
    }
  
  if ( isset ( $r['inline'] ) && is_array ( $r['inline'] ) )
    {
      foreach ( $r['inline'] as $k => $v )
        {
          if ( $v == '' )
            {
              continue;
            }
          echo "\n";
          echo  $v;
        }
    }

}

?>
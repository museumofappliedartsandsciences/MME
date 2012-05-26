<?php

function render_page ( $data )
{

  // for when page is used as an inline ie <!--# page [field] #-->

  $page = core_data ( 'page' );

  global $node;

  unset ( $data[0] ); // macro slug

  $s = '';

  foreach ( $data as $k => $token )
    {

      if ( ! is_numeric ( $k ) )
        {
          continue;
        }

      switch ( $token )
        {

        case 'site_name' :
          if ( defined ( 'SITE_NAME' ) )
            {
              $s .= SITE_NAME;
            }
          break;

        case 'title' :
          if ( isset ( $node['title'] ) )
            {
              $s .= $node['title'];
            }
          elseif ( core_data ( 'title' ) )
            {
              $s .= core_data ( 'title' );
            }
          break;

        case 'keywords' :
          $s .= $node['keywords'];
          break;

        case 'body' :
          if ( $node['body'] )
            {
              $s .= ( $node['format'] != 'html' )
                ? $node['html']
                : $node['body'];
            }
          break;

        case 'summary' :
          $s .= $node['summary'];
          break;

        case 'created' :
          $s .= 'Created ';
          $s .= format_datetime ( $node['modified_date'], 'd M Y' );
          break;

        case 'credit' :
          $s .= $node['credit_author'];
          break;

        case 'modified' :
          $s .= 'Modified ';
          $s .= format_datetime ( $node['modified_date'], 'd M Y' );
          break;

        default:
          if ( isset ( $page[$data[1]] ) )
            {
              $s .= $page[$data[1]];
            }
          else
            {
              $s .= ' ' . $token . ' ';
              $trailer = $token;
            }

        }
    }

  $s = trim ( $s );

  if ( $trailer )
    {
      $s = trim ( $s, $trailer );
      $s = trim ( $s );
    }

  echo $s;

}

?>
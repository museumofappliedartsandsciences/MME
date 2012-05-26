<?php

function widget_oai ( $data )
{
 
  echo "\n";
  oai_path ( $data );
  echo "\n";
  oai_searchbar ( $data );
  
  echo "\n";
  echo "\n";
  if ( $data['users'] )
    {
      echo '<div id="front">';
      echo '<div id="sidebar">';
      oai_user_list ( $data );

      // echo '<ul>';
      // echo '<li>';
      // echo '<a href="/oai/index.xml">RIF-CS XML Feed</a>';
      // echo '</li>';
      // echo '</ul>';

      echo '</div>';

      echo '<div id="data">';
      oai_institutions_dataset_simple ( $data );
      oai_collection_dataset_simple ( $data );

      echo '</div>';

      echo '<div class="clear"></div>';
      echo '</div>';

      // echo '<div id="formats">';
      // echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>'index.json' ) ) . '">JSON</a>';
      // echo '</div>';

    }

  if ( $data['collection'] )
    {
      // oai_user_detail ( $data );
      oai_collection_detail ( $data );

      // echo '<div id="formats">';
      // echo '<a href="' . core_url ( $data['__this'], array ( '__key'=> $data['user']['username'] . '/' . $data['collection']['collection_id'] . '.json' ) ) . '">JSON</a>';
      // echo '</div>';

    }
  elseif ( $data['user'] && $data['view'] == 'contact' )
    {
      oai_user_detail ( $data );
      oai_user_contact_form ( $data );
    }
  elseif ( $data['user'] && $data['view'] == 'contact_processed' )
    {
      oai_user_detail ( $data );
      oai_user_contact_processed ( $data );
    }
  elseif ( $data['user'] )
    {
      oai_user_detail ( $data );
      oai_collection_dataset ( $data );

      // echo '<div id="formats">';
      // echo '<a href="' . core_url ( $data['__this'], array ( '__key'=> $data['user']['username'] . '.json' ) ) . '">JSON</a>';
      // echo '</div>';
    }

}

function oai_path ( $data )
{

  $data['__app'] = 'oai'; // because shared by other modules

  echo '<div id="path">';

  echo '<h3>';
  echo '<a href="/">';
  echo 'MME';
  echo '</a>';
  echo ' // ';
  echo '<a href="/' . $data['__app'] . '">';
  echo 'OAI';
  echo '</a>';

  if ( $data['user'] )
    {
      echo ' &rarr; ';
      echo '<a href="/' . $data['__app'] . '/' . $data['user']['username'] . '">';
      echo $data['user']['title'];
      echo '</a>';
    }

  if ( $data['collection'] )
    {
      echo ' &rarr; ';
      echo '<a href="/' . $data['__app'] . '/' . $data['user']['username'] . '/' . $data['collection']['collection_id'] . '">';
      echo $data['collection']['name_primary'];
      echo '</a>';
    }

  if ( $data['__this'] == 'oai_validate' )
    {
      echo ' &rarr; ';
      echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '/oai_validate">';
      echo 'Manage Feed';
      echo '</a>';
    }

  echo '</h3>';

  echo '<strong>';
  if ( $data['collection_count'] )
    {
      echo $data['collection_count'] . ' Collections';

      if ( $data['user'] )
        {
          echo ' - ';
          echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '.xml">';
          echo 'XML';
          echo '</a>';

          echo ' - ';
          echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '.json">';
          echo 'JSON';
          echo '</a>';
        }
      else
        {
          echo ' - ';
          echo '<a href="/oai/index.xml">XML</a>';
          echo ' - ';
          echo '<a href="/oai/index.json">JSON</a>';
        }
    }
  elseif ( $data['collection'] )
    {
      echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '/' . $data['collection']['collection_id']. '.json">';
      echo 'JSON';
      echo '</a>';
    }
  echo '</strong>';


  echo '</div>';

}

function oai_searchbar ( $data )
{

  echo '<div id="search">';
 
  echo core_form_start ( $data['__this'], array ( 'view'=>'search' ) );

  if ( $data['q'] && $data['view'] == 'search' && $data['collections'] )
    {
      echo sizeof ( $data['collections'] ) . ' result' . ( ( sizeof ( $data['collections'] ) == 1 ) ? '' : 's' ) . ' found for ';
    }

  echo '<input type="text" name="q" value="' . $data['q'] . '" />';
  echo '<input type="submit" value="Search Collections" />';

  if ( $data['q'] )
    {
      echo '<a href="/oai?q=-1">Clear</a>';
    }

  echo '</form>';



  echo '</div>';

}


function oai_user_dataset ( $data )
{

  if ( ! $data['users'] )
    {
      return false;
    }

  echo '<div class="dataset">';
  echo '<table>';

  foreach ( $data['users'] as $user )
    {
      echo '<tr>';

      echo '<td>';
      echo '<h3>';
      echo '<a href="/' . $data['__this'] . '/' . $user['username'] . '">';
      echo $user['title'];
      echo '</a>';
      echo '</h3>';
      echo '</td>';

      echo '<td class="dataset-numeric">';
      if ( $user['collections'] > 0 )
        {
          echo $user['collections'] . ' Collection' . ( ( $user['collections'] == 1 ) ? '' : 's' );
        }
      echo '</td>';

      echo '</tr>';
    }
  echo '</table>';
  echo '</div>';

}

function oai_user_list ( $data )
{

  if ( ! $data['users'] )
    {
      return false;
    }
  
  $states['National'] = array();;

  foreach ( $data['users'] as $user )
    {
      if ( $user['national'] )
        {
          $states['National'][] = $user;
        }

      if ( $user['address_state'] != '' )
        {
          $states[$user['address_state']][] = $user;
        }
    }

  ksort ( $states );
  $nsw = $states['New South Wales'] ;
  $national = $states['National'] ;
  $states = array_merge ( array( 'National' => $national, 'New South Wales' => $nsw ), $states ); 

  foreach ( $states as $state => $users )
    {

      echo '<h3>';
      echo '<a href="#">';
      echo $state;
      echo '</a>';
      echo '</h3>';

      echo '<ul>';

      foreach ( $users as $user )
        {
          echo '<li>';
          echo '<a href="/' . $data['__this'] . '/' . $user['username'] . '">';
          echo $user['title'];
          echo ' ';
          if ( $user['collections'] > 0 )
            {
              echo '<em>';
              echo '<span>';
              echo $user['collections'];
              echo '</span>';
              echo '</em>';
            }
          echo '</a>';
          echo '</li>';

        }
      echo '</ul>';
    }

  // echo '<ul id="museums-filter">';
  // echo '<li>';
  // echo '<a href="#national">';
  // echo 'All Museums are shown. Click here to only show National Museums';
  // echo '</a>';
  // echo '</li>';

  // echo '<li>';
  // echo '<a href="#all">';
  // echo 'Only National museums are shown. Click here to show all Museums';
  // echo '</a>';
  // echo '</li>';

  // echo '</ul>';


}

function oai_user_detail ( $data )
{

  if ( ! $data['user'] )
    {
      return false;
    }

  echo '<div class="formbox" id="header">';


  echo '<h2>';
  echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '">';
  echo $data['user']['title'];
  echo '</a>';
  echo '</h2>';

  echo '<table id="formbox-tools">';
  echo '<tr>';
  if ( user_admin ( user() ) )
    {
      echo '<td class="formbox-tool">';
      echo core_form_start ( 'user_edit', array ( 'user_id'=> $data['user']['user_id'] ) );
      echo '<input type="submit" value="Edit User" />';
      echo '</form>';
      echo '</td>';

      echo '<td class="formbox-tool">';
      echo core_form_start ( 'user_delete', array ( 'user_id'=> $data['user']['user_id'] ) );
      echo '<input type="submit" class="confirm" value="Delete User" />';
      echo '</form>';
      echo '</td>';
    }

  if ( user_admin ( user() ) || user('user_id') == $data['user']['user_id'] )
    {
      echo '<td class="formbox-tool">';
      echo '<a href="/oai_validate">';
      echo 'Manage Feed';
      echo '</a>';
      echo '</td>';

      echo '<td class="formbox-tool">';
      echo '<a href="' . html_url ( 'collection_upload', array ( 'user_id'=> $data['user']['user_id'] ) ) . '">';
      echo 'Upload';
      echo '</a>';
      echo '</td>';

      echo '<td class="formbox-tool">';
      echo core_form_start ( 'collection_edit', array ( 'user_id'=> $data['user']['user_id'] ) );
      echo '<input type="submit" value="Add Collection" />';
      echo '</form>';
      echo '</td>';
    }


  // echo '<td class="formbox-tool">';
  // if ( $data['collections'] )
  //   {
  //     echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '.xml">';
  //     echo 'XML Feed';
  //     echo '</a>';
  //     // echo '<br />';
  //     // echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '.txt">';
  //     // echo 'Download TSV';
  //     // echo '</a>';
  //   }
  // echo '</td>';

  echo '</tr>';
  echo '</table>';


  
  echo '<p>';
  if ( $data['user']['description'] )
    {
      echo nl2br ( $data['user']['description'] );
    }
  echo '</p>';

  echo '<ul id="header-postal">';

  foreach ( array ( 'postal_street', 'postal_city', 'postal_state', 'postal_postcode', 'postal_country' ) as $k )
    {
      if ( isset ( $data['user'][$k] ) && $data['user'][$k] != '' )
        {
          echo '<li>';
          echo $data['user'][$k];
          echo '</li>';
        }
    }

  echo '</ul>';

  echo '<ul id="header-address">';

  if (  ( $data['user']['url'] ) )
    {
      echo '<li>';
      echo '<strong>';
      echo '<a href="' . $data['user']['url'] . '" target="_new">' . str_replace ( 'http://', '', $data['user']['url'] ) . '</a>';
      echo '</strong>';
      echo '</li>';
    }

  foreach ( array ( 'address_street', 'address_city', 'address_state', 'address_postcode', 'address_country' ) as $k )
    {
      if ( isset ( $data['user'][$k] ) && $data['user'][$k] != '' )
        {
          echo '<li>';
          echo $data['user'][$k];
          echo '</li>';
        }
    }

  if ( $data['user']['phone'] )
    {
      echo '<li>';
      echo $data['user']['phone'];
      echo '</li>';
    }

  if ( $data['user']['email_contact'] && $data['view'] != 'contact' )
    {
      echo '<li>';
      echo '<strong>';
      echo '<a href="/oai/' . $data['user']['username'] . '/contact">Send Email</a>';
      echo '</strong>';
      echo '</li>';
    }

  echo '</ul>';


  echo '<div class="clear"></div>';
  echo '</div>';

}


function oai_collection_dataset ( $data )
{

  echo '<div class="dataset" id="dataset">';

  if ( user_admin ( user() ) || $data['user']['user_id'] == user('user_id' ) )
    {
      echo core_form_start ( $data['__this'], array ( 'user_id'=> $data['user']['user_id'] ) );
    }

  echo '<table>';

  echo '<thead>';
  echo '<tr>';

  if ( user_admin ( user() ) || $data['user']['user_id'] == user('user_id' ) )
    {
      echo '<td class="check">';
      echo '<input type="checkbox" />';
      echo '</td>';
    }


  echo '<td>';
  echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>$data['user']['username'], 'k'=>'id_local', 'o'=> ( ( $data['k'] !='id_local') ? 1 : ( ( $data['o'] == 1 ) ? -1 : 1 ) ), 'base'=>$data['base'], 'limit'=>$data['limit'] ) ) . '">';
  echo 'ID&nbsp;Local';
  echo '</a>';
  echo '</td>';

  echo '<td>';
  echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>$data['user']['username'], 'k'=>'name_primary', 'o'=> ( ( $data['k'] !='name_primary') ? 1 : ( ( $data['o'] == 1 ) ? -1 : 1 ) ), 'base'=>$data['base'], 'limit'=>$data['limit'] ) ) . '">'; 
  echo 'Title';
  echo '</a>';
  echo '</td>';

  echo '<td>';
  echo 'Description Brief';
  echo '</td>';

  echo '<td>';
  echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>$data['user']['username'], 'k'=>'date_updated', 'o'=> ( ( $data['k'] !='date_updated') ? 1 : ( ( $data['o'] == 1 ) ? -1 : 1 ) ), 'base'=>$data['base'], 'limit'=>$data['limit'] ) ) . '">'; 
  echo 'Updated'; 
  echo '</a>';
  echo '</td>';

  // echo '<td>';
  // echo '</td>';

  echo '</tr>';
  echo '</thead>';

  if ( $data['collections'] )
    {
      foreach ( $data['collections'] as $collection )
        {
          echo '<tr>';

          if ( user_admin ( user() ) || $data['user']['user_id'] == user('user_id' ) )
            {
              echo '<td class="check">';
              echo '<input type="checkbox" name="itemsI' . $collection['collection_id'] . '" value="' . $collection['collection_id'] . '" />';
              echo '</td>';
            }

          echo '<td>';
          echo $collection['id_local'];
          echo '</td>';

          echo '<td class="title">';

          if ( user_admin() || user('user_id') == $data['user_id'] )
            {
              if ( $collection['status'] == STATUS_PREVIEW )
                {
                  echo '<h4 class="preview">';
                }
              elseif ( $collection['status'] == STATUS_ARCHIVE )
                {
                  echo '<h4 class="archive">';
                }
              else
                {
                  echo '<h4>';
                }
            }
          else
            {
              echo '<h4>';
            }

          echo '<a href="' . core_url ( $data['__this'], array ( '__key'=> $data['user']['username'] . '/' . $collection['collection_id'] ) ) . '">';
          echo ( $collection['name_primary'] ) 
            ? $collection['name_primary']
            : '[Untitled]';
          echo '</a>';
          echo '</h4>';
          echo '</td>';

          echo '<td>';
          echo format_truncate ( $collection['description_brief'], 1024 );

          $s = false;

          if ( $collection['coverage'] && $collection['coverage']['spatial'] && $collection['coverage']['spatial']['text'] )
            {
              $s[] = implode ( '; ', array_unique ( $collection['coverage']['spatial']['text'] ) );
            }

          $temporal = false;
		  
          if ( $collection['coverage'] && $collection['coverage']['temporal'] )
            {
              if ( $collection['coverage']['temporal']['from'] )
                {
                  $temporal[] = $collection['coverage']['temporal']['from'][0];
                }

              if ( $collection['coverage']['temporal']['to'] )
                {
                  $temporal[] = $collection['coverage']['temporal']['to'][0];
                }

              if ( is_array ( $temporal ) )
                {
                  $s[] =  implode ( ' - ', $temporal );
                }
            }

          if ( $s )
            {
              echo '<br />';
              echo 'Coverage: ';
              echo implode ( '; ', $s );
            }

          echo '</td>';

          echo '<td>';
          if ( $data['q'] )
            {
              echo $collection['score'];
            }
          else
            {
              echo str_replace ( ' ', '&nbsp;', format_datetime ( $collection['date_updated'], 'd M Y' ) );
            }
          echo '</td>';

          // echo '<td>';
          // echo $collection['identifier_url'];
          // echo '</td>';

          echo '</tr>';
        }
    }

  if ( user_admin ( user() ) || $data['user']['user_id'] == user('user_id' ) )
    {

      echo '<tr>';
      echo '<td colspan="6">';
      echo '<select name="status">';	  
      echo '<option value="">Choose Action...</option>';
      echo '<option value="' . STATUS_LIVE . '">Publish</option>';
      echo '<option value="' . STATUS_ARCHIVE . '">Unpublish</option>';
      echo '<option value="">--</option>';
      echo '<option value="export">Download As TSV</option>';
      if ( user_admin() )
        {
          echo '<option value="">--</option>';
          echo '<option value="' . STATUS_TRASH . '">Delete</option>';
        }
      echo '</select>';
      echo '<input type="submit" value="Selected Items" />';
      echo '</td>';
      echo '</tr>';

    }

  echo '</table>';

  if ( user_admin() )
    {
      echo '</form>';
    }

  echo '</div>';

}


function oai_collection_dataset_simple ( $data )
{

  echo '<div class="dataset simple">';

  echo '<table>';

  echo '<thead>';

  echo '<td>';
  // echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>$data['user']['username'], 'k'=>'name_primary', 'o'=> ( ( $data['k'] !='name_primary') ? 1 : ( ( $data['o'] == 1 ) ? -1 : 1 ) ), 'base'=>$data['base'], 'limit'=>$data['limit'] ) ) . '">'; 
  echo 'Title';
  // echo '</a>';
  echo '</td>';

  echo '<td>';
  echo 'Museum';
  echo '</td>';

  echo '<td>';
  echo 'Description';
  echo '</td>';

  if ( $data['q'] )
    {
      echo '<td class="dataset-numeric">';
      echo 'Score';
    }
  else
    {
      echo '<td>';
      // echo '<a href="' . core_url ( $data['__this'], array ( '__key'=>$data['user']['username'], 'k'=>'date_updated', 'o'=> ( ( $data['k'] !='date_updated') ? 1 : ( ( $data['o'] == 1 ) ? -1 : 1 ) ), 'base'=>$data['base'], 'limit'=>$data['limit'] ) ) . '">'; 
      echo 'Updated';
      // echo '</a>';
      echo '</td>';
    }

  echo '</tr>';
  echo '</thead>';

  if ( $data['collections'] )
    {
      foreach ( $data['collections'] as $collection )
        {
          echo '<tr>';

          echo '<td>';
          echo '<h4>';
          echo '<a href="' . core_url ( $data['__this'], array ( '__key'=> $data['users'][$collection['user_id']]['username'] . '/' . $collection['collection_id'], 'q'=>$data['q'] ) ) . '">';
          echo ( $collection['name_primary'] ) 
            ? $collection['name_primary']
            : '[Untitled]';
          echo '</a>';
          echo '</h4>';
          echo '</td>';

          echo '<td>';
          echo '<a href="/' . $data['__this'] . '/' . $data['users'][$collection['user_id']]['username'] . '">';
          echo $data['users'][$collection['user_id']]['title'];
          echo '</a>';
          echo '</td>';


          echo '<td>';
          echo format_truncate ( $collection['description_brief'], 128 );

          $s = false;
          if ( $collection['coverage'] && $collection['coverage']['spatial'] && $collection['coverage']['spatial']['text'] )
            {
              $s[] = implode ( '; ', array_unique ( $collection['coverage']['spatial']['text'] ) );
            }

          $temporal = false;
          if ( $collection['coverage'] && $collection['coverage']['temporal'] )
            {
              if ( $collection['coverage']['temporal']['from'] )
                {
                  $temporal[] = $collection['coverage']['temporal']['from'][0];
                }

              if ( $collection['coverage']['temporal']['to'] )
                {
                  $temporal[] = $collection['coverage']['temporal']['to'][0];
                }

              if ( is_array ( $temporal ) )
                {
                  $s[] =  implode ( ' - ', $temporal );
                }
            }

          if ( $s )
            {
              echo '<br />Coverage: ' . implode ( '; ', $s ) . '';
            }

          echo '</td>';

          if ( $data['q'] )
            {
              echo '<td class="dataset-numeric">';
              echo round ($collection['score'], 2 );
              echo '</td>';
            }
          else
            {
              echo '<td>';
              echo str_replace ( ' ', '&nbsp;', format_datetime ( $collection['date_updated'], 'd M Y' ) );
              echo '</td>';
            }

          echo '</tr>';
        }
    }


  echo '</table>';

  echo '</div>';

}


function oai_institutions_dataset_simple ( $data )
{

  // display museums that match search term above collections

  if ( ! $data['institutions'] )
    {
      return false;
    }

  echo '<div class="dataset">';

  echo '<table>';

  echo '<thead>';

  echo '<td>';
  echo 'Museum';
  echo '</td>';

  echo '<td>';
  echo '';
  echo '</td>';

  echo '</tr>';
  echo '</thead>';

  if ( $data['institutions'] )
    {
      foreach ( $data['institutions'] as $user )
        {
          echo '<tr>';

          echo '<td style="width: 200px;">';
          echo '<h4>';
          echo '<a href="/' . $data['__this'] . '/' . $data['users'][$collection['user_id']]['username'] . '">';
          echo $user['title'];
          echo '</a>';
          echo '</h4>';
          echo '</td>';

          echo '<td>';
          if ( $user['url'] )
            {
              echo '<a href="' . $user['url'] . '" target="_new">';
              echo $user['url'];
              echo '</a>';
            }
          echo '</td>';

          echo '</tr>';
        }
    }


  echo '</table>';

  echo '</div>';

}

// didactic version of dataset
function oai_collection_datalist ( $data )
{

  echo '<div class="dataset">';
  echo '<table>';

  // echo '<thead>';
  // echo '<tr>';

  // echo '<td>';
  // // echo '<h2>';
  // // echo 'Collections';
  // // echo '</h2>';
  // echo '</td>';


  // echo '</tr>';
  // echo '</thead>';

  if ( $data['collections'] )
    {
      foreach ( $data['collections'] as $collection )
        {
          echo '<tr>';
          echo '<td colspan="4">';

          if ( user_admin() || user('user_id') == $data['user_id'] )
            {
              if ( $collection['status'] == STATUS_PREVIEW )
                {
                  echo '<h4 class="preview">';
                }
              elseif ( $collection['status'] == STATUS_ARCHIVE )
                {
                  echo '<h4 class="archive">';
                }
              else
                {
                  echo '<h4>';
                }
            }
          else
            {
              echo '<h4>';
            }

          echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '/' . $collection['collection_id'] . '">';
          echo ( $collection['name_primary'] ) 
            ? $collection['name_primary']
            : '[Untitled]';
          echo '</a>';
          echo '</h4>';
          echo $collection['description_brief'];
          echo '</td>';

          echo '</tr>';
        }
    }

  echo '</table>';
  echo '</div>';

}


function oai_collection_detail ( $data )
{

  if ( ! $data['collection'] )
    {
      return false;
    }

  if ( $data['view'] == 'contact_processed' )
    {
      oai_user_contact_processed ( $data );
    }
  elseif ( $data['view'] == 'contact' )
    {
      oai_user_contact_form ( $data );
    }

  echo '<div class="collection formbox">';

  if ( $data['user']['email_contact'] && $data['view'] != 'contact' )
    {
      echo '<div class="email-link">';
      echo '<a href="/oai/' . $data['user']['username'] . '/' . $data['collection']['collection_id'] . '/contact">Send Email to <strong>' . $data['user']['title'] . '</strong></a>';
      echo '</div>';
    }

  if ( user_admin ( user() ) || $data['collection']['user_id'] == user('user_id' ) )
    {

      if ( user_admin() )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_TRASH ) );
          echo '<input type="submit" class="confirm" value="DELETE" />';
          echo '</form>';
          echo '</div>';
        }

      if ( $data['collection']['status'] != STATUS_LIVE )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_LIVE ) );
          echo '<input type="submit" value="Publish" />';
          echo '</form>';
          echo '</div>';
        }

      if ( $data['collection']['status'] != STATUS_ARCHIVE )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_ARCHIVE ) );
          echo '<input type="submit" value="Unpublish" />';
          echo '</form>';
          echo '</div>';
        }

      echo '<div class="formbox-tool">';
      echo core_form_start ( 'collection_edit', array ( 'collection_id'=> $data['collection']['collection_id'] ) );
      echo '<input type="submit" value="Edit Collection" />';
      echo '</form>';
      echo '</div>';

      echo '<div class="clear"></div>';
    }


  $collection = $data['collection'];

  if ( $data['user']['username'] == 'phm' )
    {

      $api_key = PHM_API_KEY;
      $thumb_width = 60;
      $thumb_height	= 60;
      $cols = 11;
      $rows = 2;
      $v_space = 6;
      $h_space = 6;
      $random = false;
      $parameters	= false;

      $gridSize = new GridSize($cols, $rows, $v_space, $h_space);
      $widget = new GridWidget($api_key, $gridSize, $random);
      $widget->set_size($thumb_width, $thumb_height);
      //$widget->set_filter( 'id', '736');
      $widget->enable_cache( SITE_ROOT . 'cache/phm_image_grid');

      echo '<div id="images">';
      echo $widget->render( 1 );
      echo '</div>';
    }

  if ( $data['user']['username'] == 'mv' )
    {

      if ( $collection['id_uri'] != ''  )
        {
          $path = explode ( '/', $collection['id_uri'] );
          $id = $path[5];
          if ( $id != '' )
            {
              echo '<div id="images">';
              collection_render_image_mv ( $id );
              echo '</div>';
            }
        }

    }

  echo '<h2>';
  echo 'Names';
  echo '</h2>';



  if ( $collection['name_primary'] )
    {
      echo '<p>';
      echo '<label for="collectionName_primary">';
      echo 'Name Primary';
      echo '</label>';
      echo '<span>';
      echo $collection['name_primary'];
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['name_alternate'] )
    {
      echo '<p>';
      echo '<label for="collectionName_alternate">';
      echo 'Name Alternate';
      echo '</label>';
      echo '<span>';
      echo $collection['name_alternate'];
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['name_abbreviated'] )
    {
      echo '<p>';
      echo '<label for="collectionName_abbreviated">';
      echo 'Name Abbreviated';
      echo '</label>';
      echo $collection['name_abbreviated'];
      echo '</p>';
    }

  echo '<h2>';
  echo 'IDs';
  echo '</h2>';

  if ( $collection['id_key'] )
    {
      echo '<p>';
      echo '<label for="collectionId_key">';
      echo 'ID Key';
      echo '</label>';
      echo '<span>';
      echo $collection['id_key'];
      echo '</span>';	
      echo '</p>';
    }

  if ( $collection['id_local'] )
    {
      echo '<p>';
      echo '<label for="collectionId_local">';
      echo 'ID Local';
      echo '</label>';
      echo '<span>';
      echo $collection['id_local'];
      echo '</span>';	
      echo '</p>';
    }

  if ( $collection['id_purl'] )
    {
      echo '<p>';
      echo '<label for="collectionId_purl">';
      echo 'ID Purl';
      echo '</label>';
      echo '<span>';
      echo '<a href="' . $collection['id_puri'] . '" target="_new">';
      echo $collection['id_purl'];
      echo '</a>';
      echo '</span>';
      echo '</p>';
    }

  echo '<p>';
  echo '<label for="collectionId_uri">';
  echo 'ID Uri';
  echo '</label>';
  echo '<span>';
  if ( $collection['id_uri'] )
    {
      echo '<a href="' . $collection['id_uri'] . '" target="_new">';
      echo $collection['id_uri'];
      echo '</a>';
    }
  else
    {
      echo 'Offline Only';
    }
  echo '</span>';
  echo '</p>';

  echo '<h2>';
  echo 'Descriptions';
  echo '</h2>';


  if ( $collection['description_brief'] )
    {
      echo '<p>';
      echo '<label for="collectionDescription_brief">';
      echo 'Brief Description';
      echo '</label>';
      echo '<span>';	
      echo nl2br ( $collection['description_brief'] );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['description_full'] )
    {
      echo '<p class="description">';
      echo '<label for="collectionDescription_full">';
      echo 'Full Description';
      echo '</label>';
      echo '<span>';
      echo nl2br ( $collection['description_full'] );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['description_significance'] )
    {
      echo '<p class="description">';
      echo '<label for="collectionDescription_significance">';
      echo 'Significance';
      echo '</label>';
      echo '<span>';
      echo nl2br ( $collection['description_significance'] );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['description_rights'] )
    { 
      echo '<p>';
      echo '<label for="collectionDescription_rights">';
      echo 'Rights';
      echo '</label>';
      echo '<span>';
      echo nl2br ( $collection['description_rights'] );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['description_access'] )
    {
      echo '<p>';
      echo '<label for="collectionDescription_access">';
      echo 'Access';
      echo '</label>';
      echo '<span>';
      echo nl2br ( $collection['description_access'] );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['description_note'] )
    {
      echo '<p>';
      echo '<label for="collectionDescription_note">';
      echo 'Note';
      echo '</label>';
      echo '<span>';
      echo nl2br ( $collection['description_note'] );	
      echo '</span>';
      echo '</p>';
    }

  if ( is_array ( $collection['subjects'] ) )
    {

      echo '<h2>';
      echo 'Subjects';
      echo '</h2>';

      foreach ( $collection['subjects'] as $type => $subjects )
        {
          if ( is_array ( $subjects ) )
            {
              foreach ( $subjects as $kk => $vv )
                {
                  $subjects[$kk] = ucfirst ( $vv );
                }
            }
          echo '<p>';
          echo '<label>';
          echo $type;
          echo '</label>';
          echo '<span>';
          echo implode ( '; ', $subjects );
          echo '</span>';
          echo '</p>';
        }
    }

  if ( is_array ( $collection['coverage'] ) )
    {

      foreach ( $collection['coverage'] as $mode => $coverage )
        {

          echo '<h2>';
          echo 'Coverage ' . ucfirst ( $mode );
          echo '</h2>';

          $s = false;
          $c =  $coverage;

          // specific ordering for these fields.
          foreach ( array ( 'from', 'to', 'text' ) as $type )
            {
              if ( isset ( $c[$type] ) )
                {
                  echo '<p>';
                  echo '<label>';
                  echo ucfirst( $type ) . '';
                  echo '</label>';
                  echo '<span>';
                  echo implode ( '; ', $c[$type] );
                  echo '</span>';
                  echo '</p>';
                  unset ( $c[$type] );
                }
            }

          // remaining fields, if any, free order
          if ( sizeof ( $c ) > 0 )
            {
              foreach ( $c as $type => $values )
                {
                  if ( ! is_array ( $values ) )
                    {
                      continue;
                    }
                  foreach ( $values as $value )
                    {
                      echo '<p>';
                      echo '<label>';
                      echo ucfirst( $type ) . '';
                      echo '</label>';
                      echo '<span>';
                      echo $value;
                      // echo implode ( '; ', $value );
                      echo '</span>';
                      echo '</p>';
                    }
                }
            }
        }
    }

  if ( is_array ( $collection['related'] ) )
    {
      echo '<h2>';
      echo 'Related Collections';
      echo '</h2>';

      foreach ( $collection['related'] as $object )
        {
          echo '<p>';
          echo '<label>';
          echo $object['type'];
          echo '</label>';
          echo '<span>';
          if (  $object['key'] != '' )
            {
              echo '<a href="' .$object['key'] . '">';
              echo $object['description'];
              echo '</a>';
            }
          else
            {
              echo $object['description'];
            }
          echo '</span>';
          echo '</p>';
        }
    }


  echo '<h2>';
  echo 'Dates';
  echo '</h2>';

  if ( $collection['date_updated'] )
    {
      echo '<p>';
      echo '<label>';
      echo 'Date Record Updated';
      echo '</label>';
      echo '<span>';
      echo format_datetime ( $collection['date_updated'], 'Y-m-d H:i' );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['date_created'] )
    {
      echo '<p>';
      echo '<label>';
      echo 'Date Created in MME';
      echo '</label>';
      echo '<span>';
      echo format_datetime ( $collection['date_created'], 'Y-m-d H:i' );
      echo '</span>';
      echo '</p>';
    }

  if ( $collection['date_modified'] )
    {
      echo '<p>';
      echo '<label>';
      echo 'Date Record Modified';
      echo '</label>';
      echo '<span>';
      echo $collection['date_modified'];
      echo '</span>';
      echo '</p>';
    }



  echo '</div>';
  
}

function oai_collection_detail_original ( $data )

{

  if ( ! $data['collection'] )
    {
      return false;
    }

  echo '<div class="collection">';

  if ( user_admin ( user() ) || $data['collection']['user_id'] == user('user_id' ) )
    {

      if ( user_admin() )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_TRASH ) );
          echo '<input type="submit" class="confirm" value="DELETE" />';
          echo '</form>';
          echo '</div>';
        }

      if ( $data['collection']['status'] != STATUS_LIVE )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_LIVE ) );
          echo '<input type="submit" value="Publish" />';
          echo '</form>';
          echo '</div>';
        }

      if ( $data['collection']['status'] != STATUS_ARCHIVE )
        {
          echo '<div class="formbox-tool">';
          echo core_form_start ( $data['__this'], array ( 'collection_id'=>$data['collection']['collection_id'], 'status'=>STATUS_ARCHIVE ) );
          echo '<input type="submit" value="Unpublish" />';
          echo '</form>';
          echo '</div>';
        }

      echo '<div class="formbox-tool">';
      echo core_form_start ( 'collection_edit', array ( 'collection_id'=> $data['collection']['collection_id'] ) );
      echo '<input type="submit" value="Edit Collection" />';
      echo '</form>';
      echo '</div>';
    }


  echo '<h2>';
  echo '<a href="/' . $data['__this'] . '/' . $data['user']['username'] . '/' . $data['collection']['collection_id'] . '">';
  echo $data['collection']['name_primary'];
  echo '</a>';
  echo '</h2>';

  $collection = $data['collection'];

  echo "\n";
  echo '<ul class="names">';

  if ( $collection['name_alternate'] != '' )
    {
      echo "\n";
      echo '<li class="alternate">';
      echo $collection['name_alternate'];
      echo '</li>';
    }

  if ( $collection['name_abbreviated'] != '' )
    {
      echo "\n";
      echo '<li class="abbreviated">';
      echo $collection['name_abbreviated'];
      echo '</li>';
    }
  echo '</ul>';


  echo '<p class="description">';
  echo '<strong>';
  echo $collection['description_brief'];
  echo '</strong>';
  echo '</p>';

  echo '<p class="description">';
  echo $collection['description_full'];
  echo '</p>';

  echo "\n";
  echo '<p class="description significance">';
  echo $collection['description_significance'];
  echo '</p>';

  echo "\n";
  echo '<p class="description rights">';
  echo $collection['description_rights'];
  echo '</p>';

  echo "\n";
  echo '<p class="description access">';
  echo $collection['description_access'];
  echo '</p>';

  echo "\n";
  echo '<p class="description note">';
  echo $collection['description_note'];
  echo '</p>';
		  
  echo '<div class="meta">';

  // echo '<h3>';
  // echo 'Metadata';
  // echo '</h3>';

  echo '<ul>';

  if ( $collection['date_accessioned'] )
    {
      echo "\n";
      echo '<li>';
      echo 'Accessioned';
      echo '<span>';
      echo $collection['date_accessioned'];
      echo '</span>';
      echo '</li>';
    }

  if ( $collection['date_modified'] )
    {
      echo "\n";
      echo '<li>';
      echo 'Modified';
      echo '<span>';
      echo $collection['date_modified'];
      echo '</span>';
      echo '</li>';
    }

  if ( $collection['id_purl'] )
    {
      echo "\n";
      echo '<li>';
      echo 'Permalink';
      echo '<span>';
      echo $collection['id_purl'];
      echo '</span>';
      echo '</li>';
    }

  if ( $collection['id_uri'] )
    {
      echo "\n";
      echo '<li>';
      echo 'URI';
      echo '<span>';
      echo '<a href="' . $collection['id_uri'] . '" target="_new">';
      echo $collection['id_uri'];
      echo '</a>';
      echo '</span>';
      echo '</li>';
    }

  if ( $collection['id_local'] )
    {
      echo "\n";
      echo '<li>';
      echo 'Local ID: ';
      echo '<span>';
      echo $collection['id_local'];
      echo '</span>';
      echo '</li>';
    }

  echo '</ul>';


  if ( is_array ( $collection['subjects'] ) )
    {

      echo '<h3>';
      echo 'Subjects';
      echo '</h3>';

      foreach ( $collection['subjects'] as $type => $subjects )
        {

          echo '<h4 class="subjects">';
          echo $type;
          echo '</h4>';

          echo '<ul class="subjects">';
          foreach ( $subjects as $subject )
            {
              echo "\n";
              echo '<li type="' . $type . '">';
              echo $subject;
              echo '</li>';
            }
          echo '</ul>';
          echo '<div class="clear"></div>';
        }
    }

  // if ( is_array ( $collection['coverage'] ) )
  //   {
  //     // echo '<h3>';
  //     // echo 'Coverage';
  //     // echo '</h3>';

  //     foreach ( $collection['coverage'] as $mode => $coverage )
  //       {
  //         echo '<h3 class="coverage">';
  //         echo ucfirst( $mode ) . ' Coverage';
  //         echo '</h3>';
  //         echo '<ul class="coverage">';
  //         foreach ( $coverage as $type => $values )
  //           {
  //             foreach ( $values as $value )
  //               {
  //                 echo "\n";
  //                 if ( $mode == 'temporal' )
  //                   {
  //                     echo '<li>';
  //                     echo '<span title="' . $type . '">';
  //                     echo $value;
  //                     echo '</span>';
  //                     echo '</li>';
  //                   }
  //                 if ( $mode == 'spatial' )
  //                   {
  //                     echo '<li>';
  //                     echo '<span title="' . $type . '">';
  //                     echo $value;
  //                     echo '</span>';
  //                     echo '</li>';
  //                   }
  //               }
  //           }
  //         echo "\n";
  //         echo '</ul>';
  //       }

  //   }


  if ( is_array ( $collection['related'] ) )
    {
      echo '<h3>';
      echo 'Related Objects';
      echo '</h3>';

      echo '<ul>';

      foreach ( $collection['related'] as $object )
        {
          echo '<li>';
          echo $object['type'] . '';
          echo '<span>';
          echo '<a href="' . $object['key'] . '">';
          echo $object['description'];
          echo '</a>';
          echo '</span>';
          echo '</li>';

        }
      echo '</ul>';
    }

  echo "\n";
  echo '</div>';


  echo '</div>';

}

function collection_render_image_mv ( $id )
{

  $rows = 2;
  $cols = 11;
  $limit = $rows * $cols;

  $url = 'http://museumvictoria.com.au/collections/api/v1/themes/getitems?&size=' . ( 2 * $limit ) . '&format=json&id=' . $id;
  $s = file_get_contents ( $url );
  $s = json_decode ( $s, true );
  $result = false;

  if ( $s['status'] == 'ok' )
    {
      $items = $s['result']['items']['pagedItems'];
    }

  if ( ! $items )
    {
      return false;
    }

  $images = false;
  
  foreach ( $items as $item )
    {
      if ( ! isset ( $item['image'] ) )
        {
          continue;
        }
      $item['image']['item_url'] = $item['url'];
      $images[] = $item['image'];
      if ( sizeof ( $images ) >= $limit )
        {
          break;
        }
    }


  echo '<ul id="phm_image_grid">';
  //$style = "width: %spx; height: %spx";

  foreach ( $images as $image )
    {
      $iid = str_pad ( $image['id'], 6, '0', STR_PAD_LEFT );
      $image_url = 'http://museumvictoria.com.au/collections/itemimages/' . substr ( $iid, 0, 3 ) . '/' . substr ( $iid, 3, 3 ) . '/' . $image['id'] . '_largethumb.jpg';
        
      // Create Image tag
      echo '<li style="width: 66px; height: 66px;">';
      echo '<a href="' . $image['item_url'] . '" target="_blank" style="height: 60px;">';
      echo '<img id="' . $image['id'] . '" src="' . $image_url . '" title="' . $image['title'] . '" width="60" height="60" border="0" />';
      echo '</a>';
      echo '<div id="caption" style="width: %spx;">' . $image['title'] . '</div><div id="summary">' . $image['description'] . '</div>';
      echo '</li>';
    }
  
  echo '</ul>';
    
}

function oai_user_contact_form ( &$data ) 
{

  echo "\n";
  echo '<div class="formbox" id="contact-form">';
  
  echo "\n";
  echo '<h2>';
  echo 'Send email to ' . $data['user']['title'];
  echo '</h2>';
  

  if ( $data['collection'] )
    {
      echo '<p>Please indicate your interest in the attached collection. If this enquiry relates to a research project a brief out outline would be appreciated</p>';
    }

  echo "\n";
  echo core_form_start ( $data['__this'], array ( 'view'=>'contact_process', 'collection_id'=>$data['collection']['collection_id'], 'user_id'=>$data['user']['user_id'] ) );

  echo "\n";
  echo "\n";
  echo '<p>';
  echo '<label for="name">';
  if ( $data['form']['__error']['name'] )
    {
      echo '<span class="alert">';
      echo 'Your Name';
      echo '</span>';
    }
  else
    {
      echo 'Your Name';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo "\n";
  echo '<input type="text" id="name" name="formName" value="'. htmlentities ( $data['form']['name'] ).'" ' . ( ( $data['form']['__error']['name'] ) ? ' class="invalid"' : '' ) . ' />';
  echo '</p>';

  
  echo "\n";
  echo "\n";
  echo '<p>';
  echo "\n";
  echo '<label for="email">';
  if ( $data['form']['__error']['email'] )
    {
      echo '<span class="alert">';
      echo 'Your Email Address';
      echo '</span>';
    }
  else
    {
      echo 'Your Email Address';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo "\n";
  echo '<input type="text" id="email" name="formEmail" value="'. htmlentities ( $data['form']['email'] ).'" ' . ( ( $data['form']['__error']['email'] ) ? ' class="invalid"' : '' ) . ' />';
  echo '</p>';

  echo "\n";
  echo "\n";
  echo '<p>';
  echo "\n";
  echo '<label for="message">';
  if ( $data['form']['__error']['message'] )
    {
      echo '<span class="alert">';
      echo 'Enter Message';
      echo '</span>';
    }
  else
    {
      echo 'Enter Message';
      echo '<span class="alert">*</span>';
    }
  echo '</label>';
  echo "\n";
  echo '<textarea id="message" name="formMessage"  style="height: 100px;" cols="45" rows="4" ' . ( ( $data['form']['__error']['message'] ) ? ' class="invalid"' : '' ) . '>' . htmlentities ( $data['form']['message'] ) . '</textarea>';
  echo '</p>';

  echo "\n";
  echo "\n";
  echo '<p>';
  echo '<label>';
  echo '&nbsp;';
  echo '</label>';
  echo '<input type="submit" id="contact-submit" value="Send Mail" />';
  echo '</p>';

  echo '</form>';


  echo "\n";
  echo "\n";
  echo '<p>';
  echo '<label>';
  echo '&nbsp;';
  echo '</label>';
  echo '<span class="alert">*</span>';
  echo 'Please complete all fields in the form.';
  echo '</p>';

  echo '</div>'; 

}

function oai_user_contact_processed ( $data ) 
{

  echo '<div class="formbox" id="contact-form">';

  echo '<h2>';
  echo 'Thank you for your message';
  echo '</h2>';

  echo "\n";
  echo '<p>';
  echo 'Your message has been sent to <strong>' . $data['user']['title'] . '</strong> and they will respond to you as soon as possible.';
  echo '</p>';

  echo '</div>';

}

?>
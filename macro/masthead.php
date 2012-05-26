<?php

function macro_masthead ( &$data ) 
{

}


function render_masthead ( $data ) 
{

  echo '<div id="masthead">';

  echo '<h1>';
  echo '<a href="/oai">';
  echo 'Museum Metadata Exchange';
  echo '</a>';
  echo '</h1>';

  // ? >
// <div id="search">
// <div id="cse" style="width: 100%;">Loading</div>
// <script src="http://www.google.com/jsapi" type="text/javascript"></script>
// <script type="text/javascript">
//   google.load('search', '1', {language : 'en'});
//   google.setOnLoadCallback(function() {
//     var customSearchControl = new google.search.CustomSearchControl('006093268510207167827:xodwldld84s');
//     customSearchControl.setResultSetSize(google.search.Search.SMALL_RESULTSET);
//     customSearchControl.draw('cse');
//   }, true);
// </script>
// <link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" />
// </div>
// < ? php

  echo '<div id="user">';
  if ( user () )
    {
      echo '<p>';

      if ( user_admin() )
        {
          echo '<a href="/user_edit">';
          echo 'Add New User';
          echo '</a>';
          echo ' - ';
        }
      echo '<b>';
      echo '<a href="/oai/' . user('username') . '">';
      echo user('title');
      echo '</a>';
      echo '</b> ';
      echo ' - ';
      echo '<a href="/account">';
      echo 'Account';
      echo '</a>';	
      echo ' - ';
      echo '<a href="/logout">';
      echo 'Logout';
      echo '</a>';
      echo '</p>';
    }
  else
    {
      echo '<p>';
      echo '<a href="/login">';
      echo 'Login';
      echo '</a>';
      echo '</p>';
    }

  echo '</div>';
     
  echo '<div class="clear"></div>';
  echo '</div>';

}

?>
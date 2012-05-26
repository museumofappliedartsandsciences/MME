<?php

/**
 * perform a one-off query, taking care of creating db object and
 * fetching data returns an array of associative arrays of results:
 * $data[][field_name] = 'column value' false if nothing found
 */
function db_exec ( $sql, $key = false, $column = false ) 
{

  $db = new db_sql;
  $db->query( $sql );
  $i = 0;
  if ( $db->num_rows() > 0 )
    {
      while ( $db->next_record() ) 
        {
          if ( $key && $column )
            {
              // for extracting a single column, keyed by another
              // column, e.g.

              // $sql = "SELECT COUNT(*) AS count, ticket_type FROM
              // dinner GROUP BY ticket_type

              // $data['count'] = db_exec ( $sql, 'ticket_type',
              // 'count' );

              if ( isset ( $db->record[$key] ) )
                {
                  $data[$db->record[$key]] = $db->record[$column];
                }
            }
          elseif ( $key )
            {
              $data[$db->record[$key]] = $db->record;
            }
          else
            {
              $data[++$i] = $db->record ; // something wierd going on $data[] = $db->record doesn't work???
            }
        }

      return ( is_array( $data ) ) ? $data : false;
    }

}

// return single row of query

function db_exec_one ( $sql, $key='' ) 
{

  $db = new db_sql;
  $db->query( $sql );

  if ( $key )
    {
      return ( $db->next_record() )
        ? $db->record[$key]
        : false;
    }
  else
    {
      return ( $db->next_record() )
        ? $db->record
        : false;
    }
}


/**
 * return array of single field values
 *
 *  example usage:
 *   $sql = "SELECT DISTINCT(LEFT(datetime_published,4)) AS year FROM node WHERE parent=" . $root_id;
 *   $years = db_exec_keys ( $sql, 'year' );
 *   // $years = array( '2009' => '2009' );
 */

function db_exec_keys ( $sql, $key = false ) 
{


  if ( ! $key )
    {
      return false;
    }

  $db = new db_sql;
  $db->query( $sql );

  if ( ! $db->num_rows() )
    {
      return false;
    }

  while ( $db->next_record() ) 
    {
      $r = $db->record[$key];
      $data[$r] = $r;
    }

  return ( is_array( $data ) ) 
    ? $data 
    : false;

}

/**
 * creates a unique id to be used as a primary key in a database table
 *
 * if the counter name doesn't exist, creates one and returns id for new row
 * else increments existing counter by one and returns id for new row
 */
function db_unique_id ( $field = 'general' ) 
{

  // oh how nice it would be to have some transactions going on

  $db = new db_sql;

  $sql = "SELECT * FROM unique_id WHERE name = '$field'";

  $db->query( $sql );

  if ( $db->next_record() )
    {
      $r = $db->record['id_value'];
      $r ++;
      $sql = "UPDATE unique_id SET id_value = " . $r . " WHERE name = '$field'";
      $db->query( $sql );
    }
  else
    {
      // create a new row for a new name
      db_exec ("INSERT INTO unique_id ( id_value, name ) VALUES ( 1000, '$field' )");
      $r = 1000;
    }

  return $r ;

}

class db_sql 
{

  public $link_id = 0;
  public $query_id = 0;
  public $record = 0;
  public $row = 0;

  public $errno = 0;
  public $error = '';

  function halt( $msg ) 
  {
    core_die ( 'Database Error', $msg );
  }

  function connect() 
  {
    if ( ! $this->link_id ) 
      {

        if ( DATABASE_NAME == '__db_name__' )
          {
			  
            // holding value for database setting - site needs to be
            // configured

            if ( isset ( $_SERVER['argv'] ) )
              {
                // not set up and running on shell, fail silently so
                // notificaiton emails aren't generated
                exit;
              }

            core_die ( SITE_ROOT . ' - Site is not set up' );

          }

        $this->link_id = @mysql_connect ( DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD );

        if ( ! $this->link_id )
          {

            if ( isset ( $_SERVER['argv'] ) )
              {
                // not set up and running on shell, fail silently so
                // notificaiton emails aren't generated
                exit;
              }

            // http://dev.mysql.com/doc/refman/5.0/en/error-messages-client.html

            $errno = mysql_errno();

            echo '<p>';
			  
            if ( $errno == 2002 )
              {
                // can't connect to socket -- probably because mysql is overloaded
                echo SITE_NAME . ' is currently experiencing a high level of demand. Please try again shortly.';
              }
            else
              {
                echo SITE_NAME . ' is experiencing technical issues. Support has been notified and normal service will be resumed as soon as possible.</p>';
              }

            echo '</p>';
            exit;

          }

        if ( ! mysql_select_db ( DATABASE_NAME, $this->link_id ) )
          {
            $this->halt( 'Cannot use database ' . DATABASE_NAME );
          }
      }

  }

  function query( $query ) 
  {
    $this->connect();
    $this->query_id = mysql_query( $query, $this->link_id );
    $this->row = 0;
    $this->errno = mysql_errno();
    $this->error = mysql_error();

    if ( ! $this->query_id )
      {
        $s = '';
        $s .= 'Invalid SQL: ' . $this->error . "\n\n";
        $s .= $query;
        $this->halt( $s );
        exit;
      }
    return $this->query_id;
  }

  function num_rows() 
  {
    return @mysql_num_rows( $this->query_id );
  }

  function insert_id() 
  {
    return @mysql_insert_id( $this->link_id );
  }

  function first_record() 
  {
    if ( $this->next_record() )
      {
        $record = $this->record;
      }
    else
      {
        $record = "";
      }
    return $record;
  }

  function next_record() 
  {
    $this->record = mysql_fetch_assoc( $this->query_id );
    $this->row += 1;
    $this->errno = mysql_errno();
    $this->error = mysql_error();

    $stat = is_array( $this->record );
    if ( !$stat ) 
      {
        mysql_free_result( $this->query_id );
        $this->query_id = 0;
      }
    return $stat;
  }
	
  function seek( $pos ) 
  {
    $status = mysql_data_seek( $this->query_id, $pos );
    if ( $status )
      {
        $this->row = $pos;
      }
    return;
  }
	
}
?>
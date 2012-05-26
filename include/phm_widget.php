<?php

require_once realpath(dirname(__FILE__)) . '/phm_client.php';

class GridSize{
  public $cols;
  public $rows;
  public $v_space;
  public $h_space;

  public function __construct($cols = null, $rows = null, $v_space = null, $h_space = null){
    $this->cols 	= (is_null($cols))?4:$cols;
    $this->rows 	= (is_null($rows))?4:$rows;
    $this->v_space  = (is_null($v_space))?1:$v_space;
    $this->h_space  = (is_null($h_space))?1:$h_space;
  }

  public function get_total(){
    return $this->cols * $this->rows;
  }
}


class GridWidget{
  private $client;
  private $arguments 		= array();
  private $size      		= array();
  private $fields	  	 	= array();
  private $link_target 	= '_blank';
  private $grid_size		= null;
  private $is_random		= False;


  public function __construct($api_key, GridSize $grid_size = null, $random=False, $link_target = '_blank') {
    $this->client = new PowerhouseAPI($api_key, null);
    $this->link_target	= $link_target;

    // Default fields
    $this->set_fields(array('title', 'permanent_url', 'thumbnail', 'summary'));

    // Default filters
    // Just retrive items that has an image
    $this->set_filter('num_multimedia_gt', 0);
    //$this->set_filter('title_isblank', 0);

    // Define number of image to retrieve
    $this->grid_size = (is_null($grid_size))?new GridSize():$grid_size;
    $this->set_filter('limit', $this->grid_size->get_total());

    //Random
    if (gettype($random) == 'string')
      if (strtolower($random) == 'true')
        $random = True;
      else
        $random = False;

    $this->is_random = $random;

    // If random
    if ($random){
      $this->set_filter('order_by', '?');
    }

  }

  public function enable_cache($cache_path=Null){
    $this->client->enable_cache($cache_path);
  }


  public function set_size($width, $height){
    $this->size['width']  = $width;
    $this->size['height'] = $height;
  }

  public function set_fields($fields = array()){
    $this->fields = array_merge($fields);
    $this->set_filter('fields',implode(',', $this->fields));
  }


  public function set_filter($name, $value){
    $this->arguments[$name] = $value;
  }


  public function render( $collection_id ){
    $html_output = '';
    $html_images = '';
    $html_template_container 				= '<ul id="phm_image_grid" style="%s">%s</ul>';
    $html_template_container_image 			= '<li style="%s"><a href="%s" target="%s" style="height: %spx;">%s</a>%s</li>';
    $html_template_image_info   			= '<div id="caption" style="width: %spx;">%s</div><div id="summary">%s</div>';
    $html_image_template     				= '<img id="%s" src="%s" title="%s" width="%s" height="%s" border="0" style="%s"/>';
    $style = "width: %spx; height: %spx";

    $expire_cache = ($this->is_random)?60:Null;

    //$result = $this->client->do_request('item', $this->arguments, $expire_cache);
    $result = $this->client->do_request('collection/' . $collection_id . '/items', $this->arguments, $expire_cache);

    if (@$result->total == 0){
      return 'Not found images.';
    }

    $items = @$result->items;
    // Silence errors. Maybe not a good Idea
    $items = (!is_null($items))?$items: array();

    $thumb_width  = @$this->size['width'];
    $thumb_height = @$this->size['height'];

    // I don't like this idea but I need to pass the exact dimension to the main container
    $list_width  = ($thumb_width  + $this->grid_size->v_space) * $this->grid_size->cols;
    $list_height = ($thumb_height + $this->grid_size->h_space) * $this->grid_size->rows;
    $container_list_style = sprintf($style, $list_width, $list_height);

    $total_items = count($items);
    $count_group_images = 0;

    foreach ($items as $item ){
      // This test shouldn't be necessary but the data is bit weird
      // but although we are requesting images with thumbnail
      $width  = ($thumb_width)?$thumb_width:$item->thumbnail->width;
      $height = ($thumb_height)?$thumb_height:$item->thumbnail->height;

      // I don't like this idea but I need to pass the exact dimension to the containers
      $container_image_style = sprintf($style, $width + $this->grid_size->h_space, $height + $this->grid_size->v_space);
      $image_style = sprintf($style, $width, $height);

      $image_url = $item->thumbnail->url;
      $image_domain = 'images';
      $image_domain = ($count_group_images > 0)?$image_domain . $count_group_images:$image_domain;
      $image_url = str_replace('images.', $image_domain . '.', $image_url);

      $has_title  = ($item->title && $item->thumbnail != '' );
      $title 		= ($has_title)?$item->title:$item->summary;
      $summary 	= ($has_title)?$item->summary:'';
								
      if (property_exists($item->thumbnail, 'url')){
        // Create Image tag
        $img = sprintf($html_image_template,
                       $item->id,
                       $image_url,
                       $title,
                       $width,
                       $height,
                       $image_style
                       );

        // Create Image information tags
        $img_info		= sprintf($html_template_image_info,
                              $thumb_width - 10,
                              $title,
                              $summary
                              );

        // Create Image container
        $img_container = sprintf($html_template_container_image,
                                 $container_image_style,
                                 $item->permanent_url,
                                 $this->link_target,
                                 $thumb_height,
                                 $img,
                                 $img_info
                                 );

        // Concatenate images;
        $html_images .= $img_container;

        $count_group_images++;
        $count_group_images = ($count_group_images > 5)?0:$count_group_images;
      }

    }

    $html_output = sprintf($html_template_container, $container_list_style, $html_images);

    return $html_output;

  }


}


?>
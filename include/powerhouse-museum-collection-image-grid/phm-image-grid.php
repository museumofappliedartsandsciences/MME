<?php
/*
Plugin Name: Powerhouse Museum Collection Image Grid
Plugin URI: http://api.powerhousemuseum.com/wordpress
Description: Plugin to allow embedding of object thumbnails and descriptions from the Powerhouse Museum collection of technology, design and social history.
Version: 0.9.1.1
Author: Powerhouse Museum
Author URI: http://www.powerhousemuseum.com/
*/

define('PHM_IMAGE_GRID_DIR', SITE_ROOT . 'include/powerhouse-museum-collection-image-grid/');
define('PHM_IMAGE_GRID_URL',  '/phm_image_grid/'. basename(dirname(__FILE__)));
define('PHM_IMAGE_GRID_CACHE_DIR', PHM_IMAGE_GRID_DIR. 'cache/');

require_once PHM_IMAGE_GRID_DIR . 'phm-widget.php';
require_once PHM_IMAGE_GRID_DIR . 'phm-options.php';
require_once PHM_IMAGE_GRID_DIR . 'phm-utils.php';



//error_reporting(E_ALL);

$widget = new PHMCollectionGridWidget();

class PHMCollectionGridWidget {
	private $widget_name = 'Powerhouse Collection Image Grid';
	private $field_prefix = 'phm-image-grid-';
	private $options;
	private $errors;

	public function __construct(){
		$this->options = new PHMWidgetOptions('phm-image-grid');
		$this->errors  = new WP_Error();

		add_action("widgets_init",    array(&$this, 'register_widget'));
		add_action('wp_print_styles', array(&$this, 'add_my_stylesheet'));
		add_action('wp_print_scripts', array(&$this, 'add_my_script'));
		add_action( 'admin_menu',     array(&$this, 'admin_menu' ));

		add_shortcode('phm-grid', array($this, 'shortcode'));

		register_activation_hook( __FILE__, 	array(&$this, 'activate_hook'));
		register_deactivation_hook( __FILE__,   array(&$this, 'deactivate_hook'));

		//Allow the shortcode of this widget within a widgettext
		if (!is_admin())
  			add_filter('widget_text', 'do_shortcode', SHORTCODE_PRIORITY);

	}

	// WIDGET
	public function activate_hook(){
		$this->options->create_options();
	}

	public function deactivate_hook(){
		$this->options->delete_options();
	}


	function control(){
		$field_prefix = $this->field_prefix;

		if (isset($_POST[$field_prefix . 'api_key'])){
			$options = $this->options->get_options();

			//Store options
			foreach ($options as $name => $settings){
				$value = @$_POST[$field_prefix . $name];

				switch(@$settins['type']){
					case PHMWidgetOptions::$TYPE_CHECK:
						$value = (is_null($value))?false:true;
						break;
				}
				$this->options->set_value_option($name, $value);
			}

			//Recreate them
			$this->options->clean_search_filters();

			$filter_count = (int)$_POST[$field_prefix . 'filters_count'];

			for($index =0; $index<$filter_count; $index++){
				$field  = @$_POST[$field_prefix . 'field-' . $index ];
				$filter = @$_POST[$field_prefix . 'filter-' . $index ];
				$value  = @$_POST[$field_prefix . 'value-' . $index ];
				if ($field && $filter && $value){
					$this->options->add_search_filter($field, $filter, $value);
				}

			}

			$this->options->save();
		}

		$count_filters = count($this->options->get_search_filters());
		$count_filters = ($count_filters==0)?1:$count_filters;

	    $variables = array();
	    $variables['controls'] = $this->render_control();
	    $variables['filters']  = $this->render_search_fields();
	    $variables['count_filters'] = $count_filters;
	 	echo $this->render_template('templates/_snippet_controls.html', $variables);
	}

	public function render_control($default_values = False){
		$output = '';

		$field_prefix = $this->field_prefix;

		$input_hidden_tag   = '<input name="%s" type="hidden" value="%s" />';
		$input_text_tag     = '<p><label for="%s">%s:<br/></label><input name="%s" type="text" value="%s" class="widefat" /></p>';
		$input_check_tag    = '<p><label for="%s">%s:<br/></label><input name="%s" type="checkbox" %s /></p>';
		$options = $this->options->get_options();

		foreach($options as $name => $settings){
			$input	= Null;
			$input_name = $field_prefix . $name;
			$value = (!$default_values)?$settings['value']:$settings['default'];
			$type  = $settings['type'];

			switch($type){
				case PHMWidgetOptions::$TYPE_TEXT:
					$input =  sprintf($input_text_tag,   $input_name,
												    $settings['label'],
													$input_name,
													$value
													);
					break;

				case PHMWidgetOptions::$TYPE_HIDDEN:
					$input =  sprintf($input_hidden_tag,   $input_name,
													$value
													);
					break;


				case PHMWidgetOptions::$TYPE_CHECK:
					$value = ($value)?'checked="checked"':'';
					$input =  sprintf($input_check_tag,   $input_name,
												    $settings['label'],
													$input_name,
													$value
													);
					break;


			}

			if (!is_null($input)){
				$output .= $input;
			}
		}

		return $output;
	}


	public function render_search_fields($read_db=true){
		$output = '';
		$search_filters = $this->options->get_search_filters();
		if (count($search_filters) > 0 && $read_db){
			$inx = 0;
			foreach ($search_filters as $filter ){
				$output .= $this->create_search_filter($inx, @$filter[0], @$filter[1], @$filter[2]);
				$inx ++;
			}

		}else{
			$output = $this->create_search_filter();
		}

		return $output;
	}

	private function create_search_filter($id=Null, $field=null, $filter=null, $value = null){
		$variables = array();
		$variables['input_name'] = $field;
		$variables['id'] 		 = (is_null($id))?0:$id;
		$variables['fields']  = $this->create_html_select_options($this->options->get_list_fields(),  $field);
		$variables['filters'] = $this->create_html_select_options($this->options->get_list_filters(), $filter);
		$variables['value']	  = $value;

		$html =  $this->render_template('templates/_snippet_search_filters.html', $variables);
		return $html;
	}

	private function create_html_select_options($options, $selected = null){
		$option_tag = '<option %s>%s</option>';
		$output = sprintf($option_tag, '', '');
		foreach($options as $value){
			$selected_option = ($selected == $value)?'selected':'';
			$output .= sprintf($option_tag, $selected_option, $value);
		}
		return $output;
	}

	public function widget($args){
		echo $args['before_widget'];
		//echo $args['before_title'] . 'Powerhouse Collection' . $args['after_title'];

		$widget = $this->createImageGrid();

		echo $widget->render();
		echo $args['after_widget'];
	}

	public function register_widget(){
		register_sidebar_widget($this->widget_name, array($this, 'widget'));
		register_widget_control($this->widget_name, array($this, 'control'), 470, 500);
	}

	#SHORTCODE
    public function shortcode($atts, $content=null) {
    	$atts = shortcode_atts(array(
			'thumb_width' 	=> null,
			'thumb_height' 	=> null,
			'cols' 			=> null,
			'rows'			=> null,
			'v_space'		=> null,
			'h_space'		=> null,
    		'random'		=> null,
    		'parameters'	=> null
		), $atts);

		$widget = $this->createImageGrid($atts);
		return $widget->render();

		if (empty($clip_id) || !is_numeric($clip_id)) return '<p>fail</p>';

    }

	function media_buttons() {
		$variables = array();
		$variables['title'] = $this->widget_name;
		$variables['pluging_directory'] =  PHM_IMAGE_GRID_URL;

		echo $this->render_template('templates/_snippet_media_button.html', $variables);
	}


    #PAGE
	public function admin_menu(){
		if (is_null($this->get_value_option('api_key')) && @$_GET['page'] != 'phm-image-grid-key'){
			add_action( 'admin_notices', array(&$this, 'admin_notice_key') );
		}

	 	if ( !is_dir(PHM_IMAGE_GRID_CACHE_DIR) OR !is_writable(PHM_IMAGE_GRID_CACHE_DIR)){
	       add_action( 'admin_notices', array(&$this, 'admin_notice_cache') );
	    }

		add_object_page($this->widget_name, 'PHM Image Grid', 'edit_posts', 'phm-image-grid-key', array( &$this, 'phm_control_page' ),  PHM_IMAGE_GRID_URL . "/images/phm_icon.gif" );

		#register media_button
		add_action( 'media_buttons', array( &$this, 'media_buttons' ) );
	}



    public function phm_control_page() {

    	$field_api_key = $this->field_prefix . 'api_key';
		if (isset($_POST[$field_api_key ])){
			$value = @$_POST[$field_api_key];
			$this->options->set_value_option('api_key', $value);
			$this->options->save();
		}

		$variables = array();
		$variables['api_key'] = $this->get_value_option('api_key');
    	echo $this->render_template('templates/admin_key.html', $variables);
    }


    public function admin_notice_key(){
		echo $this->render_template('templates/admin_notice.html', array(message => 'You need to <a href="admin.php?page=phm-image-grid-key">input your Powerhouse Museum API Key</a>.'));
    }

    public function admin_notice_cache(){
    	$message  = 'Currently the PHM Image Grid Widget is not available to cache any query to the Powerhouse Museum API. ';
    	$message .= 'Please assing write permissions to the following folder.<br/<br/><em>' . PHM_IMAGE_GRID_CACHE_DIR .'</em>';
		echo $this->render_template('templates/admin_notice.html', array(message => $message));
    }

    public function render_template($template_path, $variables = null){
    	$template = new Template(PHM_IMAGE_GRID_DIR . $template_path);
    	return $template->render($variables);
    }


    /**
     * This funtion helps to get the value of a settings
     * for shortcode and widget
     */
    private function get_value_option($option_name, $shortcode_args = null){
    	$value = null;
    	$shortcode_args = (is_null($shortcode_args))?array():$shortcode_args;
    	if (array_key_exists($option_name, $shortcode_args)){
			$value = $shortcode_args[$option_name];
    	}else{
    		$value = $this->options->get_value_option($option_name);
    	}
		return $value;

    }

    // RENDER
	private function createImageGrid($shortcode_args = null){
		$api_key 		= $this->get_value_option('api_key', $shortcode_args);
		$thumb_width    = $this->get_value_option('thumb_width', $shortcode_args);
		$thumb_height	= $this->get_value_option('thumb_height', $shortcode_args);
		$cols 			= $this->get_value_option('cols', $shortcode_args);
		$rows 			= $this->get_value_option('rows', $shortcode_args);
		$v_space		= $this->get_value_option('v_space', $shortcode_args);
		$h_space		= $this->get_value_option('h_space', $shortcode_args);
		$random 		= $this->get_value_option('random', $shortcode_args);
		$parameters		= $this->get_value_option('parameters', $shortcode_args);



		$gridSize   = new GridSize($cols, $rows, $v_space, $h_space);
		$widget     = new GridWidget($api_key, $gridSize, $random);
		$widget->set_size($thumb_width, $thumb_height);
		$widget->enable_cache(PHM_IMAGE_GRID_CACHE_DIR);


		//Search
		if (gettype($parameters) == 'string'){
			$parameters = $this->convert_to_array_parameters($parameters);

			if($parameters){
				foreach ($parameters as $field => $value){
					$widget->set_filter($field, $value);
				}
			}
		}else{
			foreach ($parameters as $value){
				$widget->set_filter($value[3], $value[2]);
			}
		}

		return $widget;
	}

	private function convert_to_array_parameters($parameters){
		$tmp_param = explode('|', $parameters);
		$parameters = array();
		foreach($tmp_param as $pair){
			$pair = explode(':', $pair);
			$parameters[@$pair[0]] = @$pair[1];
		}
		return $parameters;
	}


}

?>
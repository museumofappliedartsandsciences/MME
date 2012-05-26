<?php

class PHMWidgetOptions{
	public static $TYPE_OPTION = 'option';
	public static $TYPE_TEXT   = 'text';
	public static $TYPE_CHECK  = 'check';
	public static $TYPE_HIDDEN = 'hidden';

	private $opts 		= array();
	private $filters 	= array();
	private $fields 	= array();
	private $prefix 	= '';

	public function __construct($prefix){
		$this->prefix = $prefix;

		//Set default values
		$this->init_option('api_key', null, 'API Key', self::$TYPE_HIDDEN);
		$this->init_option('cols', 4, 'Number colums');
		$this->init_option('rows', 4, 'Number rows');
		$this->init_option('v_space', 1, 'Vertical space between thumbnails');
		$this->init_option('h_space', 1, 'Horizontal space between thumbnails');
		$this->init_option('thumb_width',  140, 'Width image');
		$this->init_option('thumb_height', 140, 'Height image');
		$this->init_option('rows', 4, 'Number rows');
		$this->init_option('random', false, 'Random', self::$TYPE_CHECK);

		$this->init_search_filters();
	}

	public function init_option($name, $default = null, $label=null,  $type = null){
		# Check if this value exist in wordpress
		$value = null;
		$wp_option = get_option($this->get_wp_option_name($name));
		if ($wp_option){
			$value = @$wp_option['value'];
		}

		$option = array();
		$option['type']     		= (is_null($type))?self::$TYPE_TEXT:$type;
		$option['label']			= (is_null($label))?$name:$label;
		$option['default']			= $default;
		$option['value']			= ($value)?$value:$default;
		$this->opts[$name] 			= $option;
	}

	private function init_search_filters(){
		$this->filters = array('lte', 'lt', 'gt', 'gte', 'containts', 'exact', 'isblank', 'begins', 'ends', 'in');
		$this->fields  = array('id', 'registration_number', 'title', 'summary', 'description',
                   'production_notes', 'history_notes', 'significance_statement',
                   'acquisition_credit_line', 'marks', 'administrative_history',
                   'display_location', 'display_location_building', 'height',
                   'width', 'diameter', 'weight', 'length_units', 'weight_units',
                   'production_date_earliest', 'production_date_latest',
                   'num_multimedia', 'num_tags', 'num_subjects', 'num_provenance');

		# Check if this value exist in wordpress
		$value = null;
		$wp_option = get_option($this->get_wp_option_name('parameters'));
		if ($wp_option===False){
			$wp_option = array();
		}
		$this->opts['parameters'] = $wp_option;
	}

	public function get_option($name){
		return @$this->opts[$name];
	}

	public function set_value_option($name, $value){
		@$this->opts[$name]['value'] = $value;
	}

	public function get_value_option($name){
		$option = @$this->opts[$name];
		if(array_key_exists('value', $option)){
			$value = $option['value'];
		}else{
			$value = $option;
		}
		return $value;
	}

	//Fields and filter options
	public function get_list_filters(){
		return $this->filters;
	}

	//Fields and filter options
	public function get_list_fields(){
		return $this->fields;
	}

	//Search
	public function add_search_filter($field, $filter, $value){
		$parameter = ($filter != 'containts')?$field . '_'. $filter:$field;
		$this->opts['parameters'][] = array($field, $filter, $value, $parameter);
	}

	public function clean_search_filters(){
		$this->opts['parameters'] = array();
	}

	public function get_search_filters(){
		$filter_search = $this->opts['parameters'];
		return $filter_search;
	}

	public function get_rest_search_parameters(){
		$result = array();
		$filter_search = $this->get_search_filters();
		foreach ($filter_search as $filter){
			$param = $filter[0] . '_' . $filter[1];
			$result[$param] = $filter[2];
		}

		return http_build_query($result);
	}

	public function  get_options(){
		return $this->opts;
	}


	public function save(){
		foreach ($this->opts as $name => $value){
			$name = $this->get_wp_option_name($name);
			if (get_option($name) === false){
		    	add_option($name , $value);
		    } else {
			 	update_option($name , $value);
		    }
		}

	}

	public function create_options(){
		$this->save();
	}

	public function delete_options(){
		foreach ($this->opts as $name => $value){
			$name = $this->get_wp_option_name($name);
 			delete_option($name);
		}

	}

	private function get_wp_option_name($name){
		return $this->prefix . '-' . $name;
	}


}

?>
<?php
/**
 * REALLY SIMPLE TEMPLATE SYSTEM
 */
class Template{

	private $content_template = '';

	public function __construct($path_template){

		$this->content_template = $this->read_template($path_template);
	}

	public function render($variables){
		$output = $this->content_template;
		if (!is_null($variables)){
			foreach($variables as $key => $value){
				$output = preg_replace('/{%(|\s*)'. $key .'(|\s*)%}/', $value, $output);
			}
		}
		// Clean output of unused variables
		$output = preg_replace('/{%(|\s*)\w*(|\s*)%}/', '', $output);

		return $output;
	}

	private function read_template($filename){
		$fobj = fopen ($filename , "r");
		$content = fread ($fobj, filesize ($filename));
		fclose($fobj);
		return $content;
	}



}
?>
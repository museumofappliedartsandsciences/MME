<?php
define('WP_ADMIN', true);
require_once('../../../wp-load.php');
require_once realpath(dirname(__FILE__)) . '/phm-image-grid.php';

wp_enqueue_script( 'common' );
wp_enqueue_script( 'jquery-color' );
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
wp_enqueue_style( 'media' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Powerhouse Museum Image Grid</title>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
?>
</head>
<body>

	<form action="" method="post">
		 <?php
		    $variables = array();
		    $variables['controls'] = $widget->render_control(true);
		    $variables['filters']  = $widget->render_search_fields(false);
		    $variables['count_filters'] = '1';
		 	echo $widget->render_template('templates/_snippet_controls.html', $variables);
		 ?>
		<input id="phm_image_grid_send_to_editor" type="submit" value="Send to editor" class="button-primary" name="save">
	</form>

</body>
</html>

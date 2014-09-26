<?php

function gp_flickr_widget_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function gp_flickr_widget($args) {

		// "$args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys." - These are set up by the theme
		extract($args);

		// These are our own options
		$settings_options = get_option('gp-flickr-widget-settings');
		$api_key = $settings_options['api_key'];
		$secret = $settings_options['secret'];
		$user_id = $settings_options['user_id'];
		
		$widget_options = get_option('gp-flickr-widget-options');
		$title = $widget_options['title'];
		$tags = $widget_options['tags'];
		$num_photos = $widget_options['num_photos'];
	
        // Output

		// start
		require('phpFlickr/phpFlickr.php');

        $phpFlickr = new phpFlickr($api_key, $secret);
        
        $search_array = array("user_id"=>$user_id, "tags"=>$tags, "tag_mode"=>"all", "per_page" => $num_photos);
        $photos_array = $phpFlickr->photos_search($search_array);
        

        echo "<h3>".$title."</h3>";
        echo "<div id='gp-flickr-widget'>";
        $photos = $photos_array['photo'];
        foreach ( $photos as $photo ) {

            $photo_url = flickr_photo_to_image_url($photo);
            $link_url = flickr_photo_to_link_url($photo, $user_id);
            echo "<a href=$link_url target='_blank'>";
            echo "<img src=$photo_url />";
            echo "</a>";
        }
        echo "</div>";

	}
	
	function flickr_photo_to_image_url($photo) {
	    $size = "n";
	    $photo_url = "https://farm" . $photo['farm'] . ".staticflickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . "_" . $size . ".jpg";
	    return $photo_url;
	}
	
	function flickr_photo_to_link_url($photo, $user_id) {
	    $link_url = "https://www.flickr.com/photos/" . $user_id . "/" . $photo['id'];
	    return $link_url;
	}

	// Settings form
	function gp_flickr_widget_control() {

		// Get options
		$widget_options = get_option('gp-flickr-widget-options');

        // form posted?
		if ( isset($_POST['gp-flickr-widget-submit']) && $_POST['gp-flickr-widget-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$widget_options['tags'] = $_POST['gp-flickr-widget-options-tags'];
			$widget_options['num_photos'] = $_POST['gp-flickr-widget-options-num_photos'];
			$widget_options['title'] = $_POST['gp-flickr-widget-options-title'];
			update_option('gp-flickr-widget-options', $widget_options);
		}

		// Get options for form fields to show
		$title = $widget_options['title'];
		$tags = $widget_options['tags'];
		$num_photos = $widget_options['num_photos'];

		// The form fields

        echo '<p style="text-align:right;">
				<label for="gp-flickr-widget-options-title">' . __('Title:') . '
				<input style="width: 200px;" id="gcal-flickr-widget-options-title" name="gp-flickr-widget-options-title" type="text" value="'.$title.'" />
				</label></p>';
        
		echo '<p style="text-align:right;">
				<label for="gp-flickr-widget-options-collection">' . __('Tags:') . '
				<input style="width: 200px;" id="gcal-flickr-widget-options-tags" name="gp-flickr-widget-options-tags" type="text" value="'.$tags.'" />
				</label></p>';
				
		echo '<p style="text-align:right;">
        		<label for="gp-flickr-widget-options-num_photos">' . __('Number of Photos:') . '
        		<input style="width: 200px;" id="gcal-flickr-widget-options-num_photos" name="gp-flickr-widget-options-num_photos" type="text" value="'.$num_photos.'" />
        		</label></p>';
				
		echo '<input type="hidden" id="gp-flickr-widget-submit" name="gp-flickr-widget-submit" value="1" />';
	}


	// Register widget for use

	wp_register_sidebar_widget(
		'gp_flickr_widget',         // your unique widget id
		'Gracepoint Flickr Widget', // widget name
		'gp_flickr_widget',         // callback function
		array(                      // options
			'description' => 'Gracepoint Calendar widget'
        )
	); 
	
	wp_register_widget_control(
		'gp_flickr_widget',         // your unique widget id
		'Gracepoint Flickr Widget', // widget name
		'gp_flickr_widget_control', // Callback function
		300,200
	);
	wp_register_style('gp_flickr_widget', WP_PLUGIN_URL . '/gp-flickr-widget/gp-flickr-widget.css');
	wp_enqueue_style( 'gp_flickr_widget' );

}
// Run code and init
add_action('widgets_init', 'gp_flickr_widget_init');

?>

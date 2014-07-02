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
		$user_id = $settings_options['user_id'];
		
		$widget_options = get_option('gp-flickr-widget-options');
		$collection = $widget_options['collection'];
		$show_months = $widget_options['show_months'];
		$num_photos = $widget_options['num_photos'];
	
        // Output

		// start
		require('phpFlickr/phpFlickr.php');

        $phpFlickr = new phpFlickr($api_key);
        
        $show_months = -1 * $show_months;
        $searchDate = new DateTime( "now" );       
        $searchDate->modify( $show_months . ' months' );
        $searchDate = $searchDate->format('U');
        
        $collections = $phpFlickr->collections_getTree($collection, $user_id);
        $sets = $collections['collections']['collection'][0]['set'];
        $numSets = count( $sets );
        
        $count = 0;
        $myPhotos = array();
        while ( $count < $num_photos ) {
            $set_number = rand( 1, $numSets );
            $set_number--;
            $set = $sets[$set_number];
            $setID = $set['id'];
            
            $setInfo = $phpFlickr->photosets_getInfo( $set['id'] );

            if ( $setInfo['date_create'] < $searchDate ) {
               continue;
            }
            
            $num_photos_in_set = $setInfo['count_photos'];
            $photo_id = rand( 0, $num_photos_in_set - 1 );

            $photos = $phpFlickr->photosets_getPhotos( $setID, "url_sq,owner_name" );
            $photos = $photos['photoset']['photo'];

            array_push( $myPhotos, $photos[$photo_id] );
            $count++;
        }

        echo "<h3>Photos</h3>";
        echo "<div id='gp-flickr-widget'>";
        $photo_num = 0;
        foreach ( $myPhotos as $photo ) {
            $link_url = "http://www.flickr.com/photos/" . $photo['ownername'] . "/" . $photo['id'] . "/";
            $photo_url = $photo['url_sq'];
            echo "<a href=$link_url target='_blank'>";
            if ($photo_num % 2 == 0) {
                echo "<img class='odd' src=$photo_url />";
            } else {
                echo "<img src=$photo_url />";
            }
            echo "</a>";
            $photo_num++;
        }
        echo "</div>";

	}

	// Settings form
	function gp_flickr_widget_control() {

		// Get options
		$widget_options = get_option('gp-flickr-widget-options');

        // form posted?
		if ( isset($_POST['gp-flickr-widget-submit']) && $_POST['gp-flickr-widget-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$widget_options['collection'] = $_POST['gp-flickr-widget-options-collection'];
			$widget_options['show_months'] = $_POST['gp-flickr-widget-options-show_months'];
			$widget_options['num_photos'] = $_POST['gp-flickr-widget-options-num_photos'];
			update_option('gp-flickr-widget-options', $widget_options);
		}

		// Get options for form fields to show
		$collection = $widget_options['collection'];
		$show_months = $widget_options['show_months'];
		$num_photos = $widget_options['num_photos'];

		// The form fields

		echo '<p style="text-align:right;">
				<label for="gp-flickr-widget-options-collection">' . __('Collection:') . '
				<input style="width: 200px;" id="gcal-flickr-widget-options-collection" name="gp-flickr-widget-options-collection" type="text" value="'.$collection.'" />
				</label></p>';
		
		echo '<p style="text-align:right;">
				<label for="gp-flickr-widget-options-show_months">' . __('Number of Months:') . '
				<input style="width: 200px;" id="gcal-flickr-widget-options-show_months" name="gp-flickr-widget-options-show_months" type="text" value="'.$show_months.'" />
				</label></p>';
				
		echo '<p style="text-align:right;">
        		<label for="gp-flickr-widget-options-num_photos">' . __('Number of Photos:') . '
        		<input style="width: 200px;" id="gcal-flickr-widget-options-num_photos" name="gp-flickr-widget-options-num_photos" type="text" value="'.$num_photos.'" />
        		</label></p>';
				
		echo '<input type="hidden" id="gp-flickr-widget-submit" name="gp-flickr-widget-submit" value="1" />';
	}


	// Register widget for use

	wp_register_sidebar_widget(
		'gp_flickr_widget',        // your unique widget id
		'Gracepoint Flickr Widget', // widget name
		'gp_flickr_widget',  // callback function
		array(                  // options
			'description' => 'Gracepoint Calendar widget'
		     )
	); 
	
	wp_register_widget_control(
		'gp_flickr_widget', // your unique widget id
		'Gracepoint Flickr Widget', // widget name
		'gp_flickr_widget_control',// Callback function
		300,200
	);
	wp_register_style('gp_flickr_widget', WP_PLUGIN_URL . '/gp-flickr-widget/gp-flickr-widget.css');
	wp_enqueue_style( 'gp_flickr_widget' );

}
// Run code and init
add_action('widgets_init', 'gp_flickr_widget_init');

?>

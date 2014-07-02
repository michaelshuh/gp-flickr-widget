<?php
/*
Plugin Name: Gracepoint Flickr Widget

/* License

    Gracepoint Flickr Widget

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
*/
include_once dirname( __FILE__ ) . '/gp_flickr_widget.php';

function gp_flickr_add_settings_page() {
	add_options_page('GP Flickr Widget', 'GP Flickr Widget', 'manage_options', 'gp-flickr.php', 'gp_flickr_settings_page'); 
}

function gp_flickr_settings_page() {
    $gp_flickr_widget_options = get_option( "gp-flickr-widget-settings" );    
	
	if (isset($_POST['info_update'])) {  
		$gp_flickr_widget_api_key = $_POST['gp-flickr-widget-api_key'];
		$gp_flickr_widget_user_id = $_POST['gp-flickr-widget-user_id'];

        $gp_flickr_widget_options['api_key'] = $gp_flickr_widget_api_key;
        $gp_flickr_widget_options['user_id'] = $gp_flickr_widget_user_id;
        
		update_option( 'gp-flickr-widget-settings', $gp_flickr_widget_options );
	} 



	?>
		<div class="wrap">
            <h2>GP Flickr Widget</h2>
			<form method="post" action="options-general.php?page=gp-flickr.php" id="gp-flickr-widget-settings">

			<h3>Gracepoint Flickr Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Flickr API Key</th>
					<td><input type="text" name="gp-flickr-widget-api_key" value="<?php echo $gp_flickr_widget_options['api_key'];?>"></td>
				</tr>
				
				<tr valign="top">
    				<th scope="row">Flickr User ID</th>
    				<td><input type="text" name="gp-flickr-widget-user_id" value="<?php echo $gp_flickr_widget_options['user_id'];?>"></td>
    			</tr>
			</table>
			<p class="submit">
				<input type="submit" name="info_update" class="button-primary" value="Save" />
			</p>
			</form>
		</div>
	<?php
}

function gp_flickr_register_mysettings() {
	register_setting( 'gp-flickr-widget-settings-group', 'gp-flickr-widget-api_key' );
	register_setting( 'gp-flickr-widget-settings-group', 'gp-flickr-widget-user_id' );
}

function gp_flickr_widget_settings_link($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="options-general.php?page=gp-flickr.php">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
 
    return $links;
}
// Run code and init
add_filter('plugin_action_links', 'gp_flickr_widget_settings_link', 10, 2);
add_action('admin_menu', 'gp_flickr_add_settings_page');
add_action( 'admin_init', 'gp_flickr_register_mysettings' );

?>

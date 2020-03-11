<?php
/*
Plugin Name: Disha Pages for WordPress
Plugin URI: https://disha.ng/pages
Description: Simple plugin to show your Disha page on Wordpress
Version: 1.0
Author: Disha
Author URI: https://disha.ng


Disha Pages WP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Disha Pages WP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Disha Pages WP. If not, see http://www.gnu.org/licenses/gpl.html.

*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
  exit;
}


function wpdisha_pages_activation_redirect( ) {
    exit( wp_redirect( admin_url( 'admin.php?page=wpdisha' )  ) );
}
add_action( 'activated_plugin', 'wpdisha_pages_activation_redirect' );


add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wpdisha_pages_add_plugin_page_settings_link');
function wpdisha_pages_add_plugin_page_settings_link( $links ) {
    $links = array_merge( array(
        '<a href="' . admin_url( 'admin.php?page=wpdisha' ) . '">' . __('Settings') . '</a>'
        ), $links );

    return $links;
}


function wpdisha_pages_register_settings() {
    add_option( 'wpdisha_pages', '');
    register_setting( 'wpdisha_pages_options_group', 'wpdisha_pages', 'wpdisha_pages_callback' );
}
add_action( 'admin_init', 'wpdisha_pages_register_settings' );



function wpdisha_pages_register_options_page() {
    add_menu_page("Disha Pages", "Disha Pages", "manage_options", "wpdisha", "wpdisha_pages_options_page", plugins_url( 'disha.svg',  __FILE__ ), 99);
}
add_action('admin_menu', 'wpdisha_pages_register_options_page');


function wpdisha_pages_options_page() {
  ?>
    <div class="wrap">
    <?php screen_icon(); ?>
    <h1>Disha Pages</h1>
    <?php
      $wpdisha_pages_options = get_option( 'wpdisha_pages' );
      if (isset($_GET['settings-updated'])) : ?>
      <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
      <p><strong><?php _e('Disha Pages settings saved! '); if ($wpdisha_pages_options['page_id']) { ?>

        <a href="<?php echo esc_url( get_page_link( $wpdisha_pages_options['page_id'] ) ); ?>" target="_blank">
            <?php esc_html_e( 'Visit Page', 'textdomain' ); ?>
         </a>
       <?php } ?>

        </strong></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
      </div>
    <?php endif; ?>

    <form method="post" action="options.php">
    <?php settings_fields( 'wpdisha_pages_options_group' ); ?>
    <table class="form-table" role="presentation">
    	<tr>
    	     <th scope="row"><label for="wpdisha_pages_url">Disha Pages Link</label></th>
    	<td>
        <input type="url" id="wpdisha_pages_url" name="wpdisha_pages[url]" value="<?php echo @$wpdisha_pages_options['url']; ?>" placeholder="Enter your Disha pages URL" class="regular-text"/>
        <p class="description" id="tagline-description">E.g <kbd>https://ba.disha.page</kbd> or your custom domain</p>
      </td>
    </tr>
    <tr>
    	<th scope="row"><label for="wpdisha_pages_select">Select Page</label></th>
    	<td>
    		<select name="wpdisha_pages[page_id]" id="wpdisha_pages_select">
                  <?php
  	                if( $pages = get_pages() ){
  	                    foreach( $pages as $page ){
  	                        echo '<option value="' . $page->ID . '" ' . selected( $page->ID, $wpdisha_pages_options['page_id'] ) . '>' . $page->post_title . '</option>';
  	                    }
  	                }
                  ?>           
          </select>
          <p class="description" id="tagline-description">The content of the selected page will be overwritten.</p>
      	</td>
    	</tr>
    	<tr>
    		<th scope="row"><label for="wpdisha_pages_header">Disable Header</label></th>
    		<td>
          <input type="checkbox" id="wpdisha_pages_header" name="wpdisha_pages[disable_header]" value="1" <?php if (@$wpdisha_pages_options['disable_header'] == 1) { echo "checked"; } ?>/> Check to hide the header on the page
        </td>
    	</tr>
    	<tr>
    		<th scope="row"><label for="wpdisha_pages_footer">Disable Footer</label></th>
    		<td>
          <input type="checkbox" id="wpdisha_pages_footer" name="wpdisha_pages[disable_footer]" value="1" <?php if (@$wpdisha_pages_options['disable_footer'] == 1) { echo "checked"; } ?>/> Check to hide the footer on the page
        </td>
    	</tr>
    </table>
    <?php submit_button(); ?>
    </form>
    </div>
  <?php
} 


add_filter( 'the_content', 'wpdisha_pages_the_content' );
function wpdisha_pages_the_content( $content ) {

    $wpdisha_pages_options = get_option( 'wpdisha_pages' );

    $url = parse_url($wpdisha_pages_options['url']);
    $dns_result = dns_get_record($url['host']);

    if ( is_page($wpdisha_pages_options['page_id']) ) {

        if ( (strpos($url['host'], 'disha.page') !== false) || ($dns_result[0]['ip'] == "178.128.163.165") || (strpos($dns_result[0]['target'], 'disha.page') !== false) ) {
            $content = '<iframe style="border: 0; width: 100%; max-width: 100%; min-height:800px; height:100vh;" src="' . $wpdisha_pages_options['url'] .'" allowtransparency="true"></iframe>';
        } else {
          $content = "Check the Disha URL you added and try again";
        }

    }

    return $content;

}

//use disha blank template
add_filter( 'template_include', 'wpdisha_pages_template', 99 );
function wpdisha_pages_template( $template ) {

    $wpdisha_pages_options = get_option( 'wpdisha_pages' ); 

    if ( is_page($wpdisha_pages_options['page_id']) ) { 
        $template = dirname( __FILE__ ) . '/custom-template.php';
    }

    return $template;
}

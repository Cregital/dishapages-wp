<?php
/*
Plugin Name: Disha Pages WP
Plugin URI: https://disha.ng/pages
Description: Simple plugin to show your Disha page on Wordpress
Version: 1.0
Author: Disha
Author URI: https://disha.ng
*/


function wpdisha_activation_redirect( ) {
    exit( wp_redirect( admin_url( 'admin.php?page=wpdisha' )  ) );
}
add_action( 'activated_plugin', 'wpdisha_activation_redirect' );


add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'disha_add_plugin_page_settings_link');
function disha_add_plugin_page_settings_link( $links ) {
  $links = array_merge( array(
    '<a href="' . admin_url( 'admin.php?page=wpdisha' ) . '">' . __('Settings') . '</a>'
  ), $links );

  return $links;
}


function wpdisha_register_settings() {
   add_option( 'wpdisha_pages', '');
   register_setting( 'wpdisha_options_group', 'wpdisha_pages', 'wpdisha_callback' );
}
add_action( 'admin_init', 'wpdisha_register_settings' );



function wpdisha_register_options_page() {
  add_menu_page("Disha Pages", "Disha Pages", "manage_options", "wpdisha", "wpdisha_options_page", plugins_url( 'dishapages-wp/disha.svg' ), 99);
  //add_options_page('Disha Page Settings', 'Disha Pages', 'manage_options', 'wpdisha', 'wpdisha_options_page');
}
add_action('admin_menu', 'wpdisha_register_options_page');


function wpdisha_options_page() {
  ?>
    <div class="wrap">
    <?php screen_icon(); ?>
    <h1>Disha Pages</h1>
    <?php
      $options = get_option( 'wpdisha_pages' );
      if (isset($_GET['settings-updated'])) : ?>
      <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
      <p><strong><?php _e('Disha Pages settings saved! '); if ($options['page_id']) { ?>

        <a href="<?php echo esc_url( get_page_link( $options['page_id'] ) ); ?>" target="_blank">
            <?php esc_html_e( 'Visit Page', 'textdomain' ); ?>
         </a>
       <?php } ?>

        </strong></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
      </div>
    <?php endif; ?>
    <form method="post" action="options.php">
    <?php settings_fields( 'wpdisha_options_group' ); ?>
    <table class="form-table" role="presentation">
    	<tr>
    	<th scope="row"><label for="wpdisha_pages_url">Disha Pages URL</label></th>
    	<td>
        <input type="url" id="wpdisha_pages_url" name="wpdisha_pages[url]" value="<?php echo @$options['url']; ?>" placeholder="Enter your Disha pages URL" class="regular-text"/>
        <p class="description" id="tagline-description">E.g <kbd>https://ba.disha.page</kbd> or your custom domain</p>
      </td>
    </tr>
    <tr>
    	<th scope="row"><label for="wpdisha_select">Display Page</label></th>
    	<td>
    		<select name="wpdisha_pages[page_id]" id="wpdisha_select">
                  <?php
  	                if( $pages = get_pages() ){
  	                    foreach( $pages as $page ){
  	                        echo '<option value="' . $page->ID . '" ' . selected( $page->ID, $options['page_id'] ) . '>' . $page->post_title . '</option>';
  	                    }
  	                }
                  ?>           
          </select>
          <p class="description" id="tagline-description">The content of the selected page will be overwritten.</p>
      	</td>
    	</tr>
    	<tr>
    		<th scope="row"><label for="wpdisha_header">Disable Header</label></th>
    		<td>
          <input type="checkbox" id="wpdisha_header" name="wpdisha_pages[disable_header]" value="1" <?php if (@$options['disable_header'] == 1) { echo "checked"; } ?>/> Check to hide the header on the page
        </td>
    	</tr>
    	<tr>
    		<th scope="row"><label for="wpdisha_footer">Disable Footer</label></th>
    		<td>
          <input type="checkbox" id="wpdisha_footer" name="wpdisha_pages[disable_footer]" value="1" <?php if (@$options['disable_footer'] == 1) { echo "checked"; } ?>/> Check to hide the footer on the page
        </td>
    	</tr>
    </table>
    <?php  submit_button(); ?>
    </form>
    </div>
  <?php
} 



add_filter( 'the_content', 'wpse6034_the_content' );
function wpse6034_the_content( $content ) {
  
	$options = get_option( 'wpdisha_pages' );

  $url = parse_url($options['url']);
  $dns_result = dns_get_record($url['host']);

  if ( is_page($options['page_id']) ) {

    if ( (strpos($url['host'], 'disha.page') !== false) || ($dns_result[0]['ip'] == "178.128.163.165") || (strpos($dns_result[0]['target'], 'disha.page') !== false) ) {
        $content = '<iframe style="border: 0; width: 100%; max-width: 100%; min-height:800px; height:100vh;" src="' . $options['url'] .'" allowtransparency="true"></iframe>';
    } else {
      $content = "Check the Disha URL you added and try again";
    }

  }

  return $content;

}


//override the page template to hide header/footer
    // using CSS
/*
add_action('wp_head', 'disha_pages_header_styles', 100);

function disha_pages_header_styles() {

	$options = get_option( 'wpdisha_pages' );

	if ( is_page($options['page_id']) ) {

	?>
	    <style type="text/css"> 
	    	<?php   
	    		if ($options['disable_header'] == 1) { echo "body header {display:none;}"; } 
	    		if ($options['disable_footer'] == 1) { echo "body footer {display:none;}"; }
	    	?>
		</style>
	<?php
	}
}
*/

    //using template

add_filter( 'template_include', 'disha_page_template', 99 );

function disha_page_template( $template ) {

  $options = get_option( 'wpdisha_pages' );

    if ( is_page($options['page_id']) ) { //use plugin's template as a fallback

        $template = dirname( __FILE__ ) . '/custom-template.php';

    }

    return $template;
}
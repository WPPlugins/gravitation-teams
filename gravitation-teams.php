<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Gravitation Teams
Plugin URI: https://github.com/UlisesFreitas/gravitation-teams
Description: Gravitation teams, is a plugin to display teams on your site, with shortcodes
Author: Ulises Freitas
Version: 1.4
Text Domain: gravitation_teams
Author URI: https://disenialia.com/
License: GPLv2
*/
/*-----------------------------------------------------------------------------*/
/*
	Gravitation Teams
    Copyright (C) 2015 Gravitation teams

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
    USA


	Disenialia©, hereby disclaims all copyright interest in the
	library Gravitation teams (a library for display teams on Wordpress) 
	written by Ulises Freitas.
	
	Disenialia©, 21 December 2015
	CEO Ulises Freitas.
*/
/*-----------------------------------------------------------------------------*/
 
add_action('load_textdomain', 'load_gravitation_teams_language_files', 10, 2);

function load_gravitation_teams_language_files($domain, $mofile)
{
    // Note: the plugin directory check is needed to prevent endless function nesting
    // since the new load_textdomain() call will apply the same hooks again.
    if ('gravitation_teams' === $domain && plugin_dir_path($mofile) === WP_PLUGIN_DIR.'/gravitation-teams/languages/')
    {
        load_textdomain('gravitation_teams', WP_LANG_DIR.'/gravitation-teams/'.$domain.'-'.get_locale().'.mo');
    }
}

add_action('plugins_loaded', 'gravitation_teams_load_textdomain');
function gravitation_teams_load_textdomain() {
	load_plugin_textdomain( 'gravitation_teams', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

function gravitation_teams_install() {
 
    // Trigger our function that registers the custom post type
    gravitation_teams_create_post_type();
 
    // Clear the permalinks after the post type has been registered
    flush_rewrite_rules();
 
}
register_activation_hook( __FILE__, 'gravitation_teams_install' );

function gravitation_teams_deactivation() {
 
    // Our post type will be automatically removed, so no need to unregister it
 
    // Clear the permalinks to remove our post type's rules
    flush_rewrite_rules();
 
}
register_deactivation_hook( __FILE__, 'gravitation_teams_deactivation' );


global $pagenow;
if ( 'plugins.php' === $pagenow )
{
    // Better update message
    $file   = basename( __FILE__ );
    $folder = basename( dirname( __FILE__ ) );
    $hook = "in_plugin_update_message-{$folder}/{$file}";
    add_action( $hook, 'gravitation_teams_update_message', 20, 2 );
}
/**
 * Displays an update message for plugin list screens.
 * Shows only the version updates from the current until the newest version
 * 
 * @param (array) $plugin_data
 * @param (object) $r
 * @return (string) $output
 */
function gravitation_teams_update_message( $plugin_data, $r )
{
    // readme contents
    $data = file_get_contents( 'http://plugins.trac.wordpress.org/browser/gravitation-teams/trunk/readme.txt?format=txt' );

    // assuming you've got a Changelog section
    // @example == Changelog ==
    $changelog  = stristr( $data, '== Changelog ==' );

    // assuming you've got a Screenshots section
    // @example == Screenshots ==
    $changelog  = stristr( $changelog, '== Screenshots ==', true );

    // only return for the current & later versions
    $curr_ver   = get_plugin_data('Version');

    // assuming you use "= v" to prepend your version numbers
    // @example = v0.2.1 =
    $changelog  = stristr( $changelog, "= v{$curr_ver}" );

    // uncomment the next line to var_export $var contents for dev:
    # echo '<pre>'.var_export( $plugin_data, false ).'<br />'.var_export( $r, false ).'</pre>';

    // echo stuff....
    $output = '<p>Please read Changelog before update</p>';
    return print $output;
}

add_filter('plugin_action_links', 'gravitation_teams_action_links', 10, 2);

function gravitation_teams_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        //http://multisite/wp-admin/edit.php?post_type=gv_teams
        $settings_link = '<a href="' . get_admin_url() . 'edit.php?post_type=gv_teams">' . __('Settings','gravitation_teams') . '</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function gravitation_teams_stylesheet() {
	
	$check_gravitation_teams_bootstrap = get_option('gravitation_teams_bootstrap');
	    if($check_gravitation_teams_bootstrap == 1):
			$handle = 'bootstrap.min.css';
			if (wp_style_is( $handle, $list = 'enqueued, to_do, registered' )) {
				return;
	     	} else {
		 		wp_register_style( 'gravitation_gravitations_bootstrap', plugins_url( 'bootstrap/css/bootstrap.min.css', __FILE__ ), array(), '3.3.5', true  );
		 		wp_enqueue_style('gravitation_gravitations_bootstrap');
	     	}
	 	endif;
	
	wp_enqueue_style( 'gravitation_teams_owl_css', plugins_url( '/owl-carousel/css/owl.carousel.css', __FILE__ ) );
	wp_enqueue_style( 'gravitation_teams_owl_theme_css', plugins_url( '/owl-carousel/css/owl.theme.default.min.css', __FILE__ ) );
	wp_enqueue_style( 'gravitation_teams_style', plugins_url( '/css/style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'gravitation_teams_stylesheet' , 20);

function gravitation_teams_scripts(){
	    
	     $check_gravitation_teams_bootstrap = get_option('gravitation_teams_bootstrap');
	    if($check_gravitation_teams_bootstrap == 1):
		    $handle = 'bootstrap.min.js';
			$list = 'enqueued';
			if (wp_script_is( $handle, $list )) {
				return;
	     	} else {
		 		 wp_register_script('gravitation_teams_bootstrap_js', plugin_dir_url( __FILE__ ).'bootstrap/js/bootstrap.min.js', array('jquery'), '3.3.5', true );
		 		 wp_enqueue_script('gravitation_teams_bootstrap_js' );
	     	}
     	endif;
	   wp_register_script('gravitation-teams-owl-js', plugins_url('/owl-carousel/js/owl.carousel.js'  , __FILE__ ),'','1.0',true);
	   wp_register_script('gravitation-teams-teams-js', plugins_url('/js/gravitation-teams.js'  , __FILE__ ),'','1.0',true);
	   wp_enqueue_script('gravitation-teams-owl-js', array(''));
	   wp_enqueue_script('gravitation-teams-teams-js', array(''));	    
}
add_action('wp_enqueue_scripts','gravitation_teams_scripts', 20);


add_filter('widget_text', 'do_shortcode');

add_filter( 'manage_gv_teams_posts_columns', 'gravitation_set_custom_edit_teams_columns' );
add_action( 'manage_gv_teams_posts_custom_column' , 'gravitation_custom_teams_column', 10, 2 );

function gravitation_set_custom_edit_teams_columns($columns) {
    unset( $columns['author'] );
    unset( $columns['date'] );
    
    $columns['teams_image'] = __( 'Image', 'gravitation_teams' );
    $columns['gravitation_teams_twitter'] = __( 'Twitter', 'gravitation_teams' );
    $columns['gravitation_teams_facebook'] = __( 'Facebook', 'gravitation_teams' );
    $columns['gravitation_teams_google_plus'] = __( 'Goole Plus', 'gravitation_teams' );
    
    $columns['gravitation_teams_shortcode'] = __( 'Shortcode', 'gravitation_teams' );

    return $columns;
}
function gravitation_custom_teams_column( $column, $post_id ) {
    switch ( $column ) {

        case 'teams_image' :
            $team_image_thumbnail = get_the_post_thumbnail( $post_id, array(150,150) );
            
            if ( is_string( $team_image_thumbnail ) && !empty( $team_image_thumbnail ) )
                echo $team_image_thumbnail;
            else
                
                echo '<img src="'.plugin_dir_url( __FILE__ ). '/images/team.png'.'" alt="Teams"/>';
            break;
        case 'gravitation_teams_twitter':
        	$meta_twitter = get_post_meta( get_the_ID(), '_teams_post_twitter', true );
         	echo '<a href="' . $meta_twitter . '" tsrget="_blank" rel="nofollow">' . $meta_twitter . '</a>';

        break;
        
        case 'gravitation_teams_facebook':
	        $meta_facebook = get_post_meta( get_the_ID(), '_teams_post_facebook', true );
	        echo '<a href="' . $meta_facebook . '" tsrget="_blank" rel="nofollow">' . $meta_facebook . '</a>';
        break;
        
        case 'gravitation_teams_google_plus':
	        $meta_google_plus = get_post_meta( get_the_ID(), '_teams_post_google_plus', true );
	        echo '<a href="' . $meta_google_plus . '" tsrget="_blank" rel="nofollow">' . $meta_google_plus . '</a>';
        break;

        case 'gravitation_teams_shortcode' :
        	echo '[gravitation_teams ids="' . $post_id . '"]';
            break;

    }
}
function gravitation_teams_shortcode($atts, $content=null){
   
    extract(shortcode_atts(array(
	    'ids' => '',
	    'category' => '',
		'count' => '',
		'order' => 'DESC',
		'orderby' => 'menu_order',
        
    ), $atts)); 
	
	$args = array();
	
	//All teams [gravitation_teams]
	if(!$ids && !$count && !$category){
		$args=array(
			
			'post_type' => 'gv_teams',
			'order' => $order,
			'orderby' => $orderby,
			
		);
	}

		
	$query = new WP_Query($args);
	
	if(!$count){
		$count = $query->post_count;
	}

	$html = '';
	
    if ($query->have_posts()){ 
		
		$html .= '<div id="teams-carousel" class="owl-carousel owl-theme">';
    	
        while($query->have_posts()){	
	        		
			$query->the_post();
		    $team_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),'large');
	        $team_imgURL = $team_imgArray[0];
	        
	        $meta_twitter = get_post_meta( get_the_ID(), '_teams_post_twitter', true );
			$meta_facebook = get_post_meta( get_the_ID(), '_teams_post_facebook', true );
			$meta_google_plus = get_post_meta( get_the_ID(), '_teams_post_google_plus', true );
			
			$html .= '<div class="item item-team">';
			if(!empty($team_imgURL)){  
				$html .='<img alt="'. get_the_title() .'" class="attachment-large wp-post-image" src="' .$team_imgURL.'" title="'. get_the_title() .'" />';
			}else{ 
				$html .='<img alt="'. get_the_title() .'" class="attachment-large wp-post-image" src="' .plugins_url(). '/gravitation-teams/images/team.png'. '"title="'. get_the_title() .'" />';
			}
			$html .= '<h3 class="team-title">' . get_the_title().'</h3>';
			$html .= '<p class="team-description">' . get_the_content() . '</p>';
			$html .= '<ul class="social-nav list-inline list-unstyled">';
						if( $meta_twitter ): 
						$html .='<li><a href="' . $meta_twitter . '" target="_blank" class="twitter-btn" title="Twitter link"></a></li>';
						endif;
						if( $meta_facebook ):
						$html .='<li><a href="' . $meta_facebook . '" class="facebook-btn" title="Facebook link" target="_blank"></a></li>';
						endif;
						if( $meta_google_plus ):
						$html .= '<li><a href="' . $meta_google_plus . '" class="g-plus-btn" title="Google+ link" target="_blank"></a></li>';
						endif;
			$html .= '</ul>';
			
			
			
			$html .= '</div>';
			
        }
		$html .= '</div>';
	}
   
	wp_reset_query();
	
	return $html;

    
}
add_shortcode('gravitation_teams', 'gravitation_teams_shortcode');    		

add_action('admin_menu' , 'gravitation_teams_help_admin_menu'); 
function gravitation_teams_help_admin_menu() {
    add_submenu_page('edit.php?post_type=gv_teams', __('Help', 'gravitation_teams'), __('Help', 'gravitation_teams'), 'administrator', basename(__FILE__), 'gravitation_teams_help_page');	
}
		
function gravitation_teams_help_page() { 
	
	
	if(isset($_REQUEST['update_gravitation_teams_settings'])){ 
				if ( !isset($_POST['gravitation_teams_nonce']) || !wp_verify_nonce($_POST['gravitation_teams_nonce'],'gravitation_teams_settings') ){
				    _e('Sorry, your nonce did not verify.', 'gravitation_teams');
				   exit;
				}else{
					
				  	update_option('gravitation_teams_bootstrap',$_POST['gravitation_teams_bootstrap']);
				  	
				  	
				    
				}
			}
		?>


		<div id="custom-branding-general" class="wrap">
				
				<h2><?php esc_html_e('Help GV. teams','gravitation_teams'); ?></h2>
				
			<div class="metabox-holder">
				<div class="postbox">
				<div class="inside">
					
					<form method="post" action="edit.php?post_type=gv_teams&page=gravitation-teams.php">
					<?php settings_fields( 'gravitation-teams-settings-group' ); ?>
					<?php do_settings_sections( 'gravitation-teams-settings-group' ); ?>
				    <table class="form-table">
				        <tr valign="top">
				        <th scope="row"><?php _e('Include Bootstrap?','gravitation_teams'); ?>
					        <div class="sidebar-description">
				<p class="description"><?php _e('If your theme already includes bootstrap set this option to NO.','gravitation_teams'); ?></p>
			</div></th>
				        
				        <td>
				        <select name="gravitation_teams_bootstrap">
							<?php
								$check_gravitation_teams_bootstrap = get_option('gravitation_teams_bootstrap');
								for($i=0;$i<2;$i++){
									if($i == 0){
										$yes_no = __('No','gravitation_teams');
									}else{
										$yes_no = __('Yes','gravitation_teams');
									}
									echo '<option value="'.$i.'"'.selected($check_gravitation_teams_bootstrap, $i, false).'>'.$yes_no.'</option>';	 
								}		 
							?>										
						</select>
				        </td>
				        </tr>
				        
				    </table>
    
					<?php wp_nonce_field( 'gravitation_teams_settings', 'gravitation_teams_nonce' ); ?>
				    <p class="submit">
				        <input class="button-primary" type="submit" name="update_gravitation_teams_settings" value="<?php _e( 'Save Settings', 'gravitation_teams' ) ?>" />
				    </p> 

					</form>
					
					<hr>
					<p><?php _e('Type of shortcodes:','gravitation_teams'); ?></p>
					<p><?php _e('Pages, Posts and Widgets','gravitation_teams'); ?></p>
					
					<p><?php _e('Show all teams: <strong>[gravitation_teams]</strong>','gravitation_teams'); ?></p>
					
					
					<ol>
						<li><strong>[gravitation_teams]</strong> Display All teams</li>
					</ol>
						
            
    			</div>
  			</div>
		</div>
		</div>
<?php 
}
	
if( ! function_exists( 'gravitation_teams_create_post_type' ) ) :
	function gravitation_teams_create_post_type() {
		
		$labels = array(
		'name'                => _x( 'GV. teams', 'Post Type General Name', 'gravitation_teams' ),
		'singular_name'       => _x( 'teams', 'Post Type Singular Name', 'gravitation_teams' ),
		'menu_name'           => __( 'GV. teams', 'gravitation_teams' ),
		'name_admin_bar'      => __( 'GV. teams', 'gravitation_teams' ),
		'parent_item_colon'   => __( 'Parent team:', 'gravitation_teams' ),
		'all_items'           => __( 'All teams', 'gravitation_teams' ),
		'add_new_item'        => __( 'Add team', 'gravitation_teams' ),
		'add_new'             => __( 'Add New', 'gravitation_teams' ),
		'new_item'            => __( 'New team', 'gravitation_teams' ),
		'edit_item'           => __( 'Edit team', 'gravitation_teams' ),
		'update_item'         => __( 'Update team', 'gravitation_teams' ),
		'view_item'           => __( 'View team', 'gravitation_teams' ),
		'search_items'        => __( 'Search team', 'gravitation_teams' ),
		'not_found'           => __( 'teams Not found', 'gravitation_teams' ),
		'not_found_in_trash'  => __( 'teams Not found in Trash', 'gravitation_teams' ),
	);
	
	$args = array(
		'label'               => __( 'Gv. teams', 'gravitation_teams' ),
		'description'         => __( 'Gv. teams Creator simple responsive teams items', 'gravitation_teams' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-sticky',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'rewrite'             => true,
		'has_archive'         => false, //TODO
		'exclude_from_search' => true, //true show on query search
		'publicly_queryable'  => true,
		'query_var' => true,
		'capability_type'     => 'post',
		'register_meta_box_cb' => 'gravitation_teams_add_post_type_metabox'
	);

		register_post_type( 'gv_teams', $args );
		//flush_rewrite_rules();
 		
	}
	
	
	add_action( 'init', 'gravitation_teams_create_post_type' );
 
 
	function gravitation_teams_add_post_type_metabox() { // add the meta box
		add_meta_box( 'gravitation_teams_metabox', 'Additional information about this team', 'gravitation_teams_metabox', 'gv_teams', 'normal' );
	}
 
	function gravitation_teams_metabox() {
		global $post;

		echo '<input type="hidden" name="teams_post_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
 
		$teams_post_twitter = get_post_meta($post->ID, '_teams_post_twitter', true);
		$teams_post_facebook = get_post_meta($post->ID, '_teams_post_facebook', true);
		$teams_post_google_plus = get_post_meta($post->ID, '_teams_post_google_plus', true);
		
		echo '<table class="form-table">
			<tr>
				<th>';
				?>
					<label><?php  _e('Twitter','gravitation_teams'); ?></label>
				<?php
				echo '</th>
				<td>
					<input type="text" name="teams_post_twitter" class="regular-text" value="' . $teams_post_twitter . '"> 
				</td>
			</tr>
			<tr>
				<th>';
				?>
					<label><?php _e('Facebook','gravitation_teams'); ?></label>
				<?php
				echo '</th>
				<td>
					<input type="text" name="teams_post_facebook" class="regular-text" value="' . $teams_post_facebook . '"> 
				</td>
			</tr>
			<tr>
				<th>';
				?>
					<label><?php _e('Google plus','gravitation_teams'); ?></label>
				<?php
				echo '</th>
				<td>
					<input type="text" name="teams_post_google_plus" class="regular-text" value="' . $teams_post_google_plus . '"> 
				</td>
			</tr>
			
		</table>';
	
	}
 
function gravitation_teams_post_save_meta( $post_id, $post ) { // save the data

		 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return;
 
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
 
		if ( ! isset( $_POST['teams_post_noncename'] ) ) { // Check if our nonce is set.
			return;
		}
 
		if( !wp_verify_nonce( $_POST['teams_post_noncename'], plugin_basename(__FILE__) ) ) { // Verify that the nonce is valid.
			return $post->ID;
		}
 
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( !wp_verify_nonce( $_POST['teams_post_noncename'], plugin_basename(__FILE__) ) ) {
			return $post->ID;
		}
 
		// is the user allowed to edit the post or page?
		if( ! current_user_can( 'edit_post', $post->ID )){
			return $post->ID;
		}
		// ok, we're authenticated: we need to find and save the data
		// we'll put it into an array to make it easier to loop though
 
		$teams_post_meta['_teams_post_twitter'] = $_POST['teams_post_twitter'];
		$teams_post_meta['_teams_post_facebook'] = $_POST['teams_post_facebook'];
		$teams_post_meta['_teams_post_google_plus'] = $_POST['teams_post_google_plus'];
 
		// add values as custom fields
		foreach( $teams_post_meta as $key => $value ) { // cycle through the $teams_post_meta array

			$value = implode(',', (array)$value); // if $value is an array, make it a CSV (unlikely)
			if( get_post_meta( $post->ID, $key, FALSE ) ) { // if the custom field already has a value
				update_post_meta($post->ID, $key, $value);
			} else { // if the custom field doesn't have a value
				add_post_meta( $post->ID, $key, $value );
			}
			if( !$value ) { // delete if blank
				delete_post_meta( $post->ID, $key );
			}
		}
	}
	add_action( 'save_post', 'gravitation_teams_post_save_meta', 1, 2 ); // save the custom fields

endif; // end of function_exists()
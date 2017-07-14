<?php
	
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 
$team_home_active = 'teams_home_active';


 
delete_option( $team_home_active );

 
// For site options in Multisite
//delete_site_option( $option_name );  
 
// Drop a custom db table
//global $wpdb;
//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mytable" );

?>
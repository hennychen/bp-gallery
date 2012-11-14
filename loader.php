<?php
/*
Plugin Name: BP Gallery
Plugin URI: http://www.amkd.com.au/wordpress/bp-gallery-plugin/98
Description: Based on the orginal BP Photos+tags by Jesse Lareaux. This plugin enables users on a BuddyPress site to create multiple albums. Albums can be given the usual privacy restrictions, with the addition of giving Album access to members of a group they have created.
Version: 1.1.1
Revision Date: November 12, 2012
Requires at least: 3.1
Tested up to: WP 3.4.2, BP 1.6.1
Author: Caevan Sachinwalla
Author URI: http://www.amkd.com.au
Network: true
*/
// JLL_MOD - changed plugin header
define('BP_PLUGIN_PATH', WP_PLUGIN_DIR.'/BPGallery/');

/**
 * Attaches BuddyPress Album to Buddypress.
 *
 * This function is REQUIRED to prevent WordPress from white-screening if BuddyPress Album is activated on a
 * system that does not have an active copy of BuddyPress.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bpa_init() {
	
	require( dirname( __FILE__ ) . '/includes/bpa.core.php' );
	
	do_action('bpa_init');
	
}
add_action( 'bp_include', 'bpa_init' );

/**
 * bp_album_install()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_install(){
	global $bp,$wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

    $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_album (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            owner_type varchar(10) NOT NULL,
	            owner_id bigint(20) NOT NULL,
	            date_uploaded datetime NOT NULL,
	            title varchar(250) NOT NULL,
	            description longtext NOT NULL,
	            privacy tinyint(2) NOT NULL default '0',
	            pic_org_url varchar(250) NOT NULL,
	            pic_org_path varchar(250) NOT NULL,
	            pic_mid_url varchar(250) NOT NULL,
	            pic_mid_path varchar(250) NOT NULL,
	            pic_thumb_url varchar(250) NOT NULL,
	            pic_thumb_path varchar(250) NOT NULL,
							album_id bigint(20) NOT NULL,
  						like_count bigint(20) default '0',
  						feature_image tinyint(1) default NULL,
	  					group_id bigint(20) NOT NULL default '0',
            	KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY album_id (album_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";
	
	$sqlalbums[] = "CREATE TABLE {$wpdb->base_prefix}bp_albums (
  						id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  						owner_type varchar(10) character set utf8 NOT NULL,
  						owner_id bigint(20) NOT NULL,
  						date_created datetime NOT NULL,
 							date_updated datetime NOT NULL,
 							title varchar(250) character set utf8 NOT NULL,
  						description longtext character set utf8 NOT NULL,
  						privacy tinyint(2) NOT NULL default '0',
  						album_org_url varchar(250) character set utf8 NOT NULL,
  						feature_image_path varchar(250) character set utf8 NOT NULL,
  						feature_image bigint(20) NOT NULL,
  						like_count bigint(20) NOT NULL default '0',
  						group_id bigint(20) NOT NULL,
  						spare2 varchar(250) character set utf8 NOT NULL,
  						spare3 varchar(250) character set utf8 NOT NULL,
  						spare4 varchar(250) character set utf8 NOT NULL,
	            KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";
// JLL_MOD - add a table for face-tagging

    $sqltag[] = "CREATE TABLE {$wpdb->base_prefix}bp_album_tags (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            photo_id bigint(20) NOT NULL,
	            tagged_id bigint(20),
	            tagged_name varchar(250) NOT NULL,
	            height bigint(20) NOT NULL,
	            width bigint(20) NOT NULL,
	            top_pos bigint(20) NOT NULL,
	            left_pos bigint(20) NOT NULL,
	            KEY photo_id (photo_id),
	            KEY tagged_id (tagged_id)
	            ) {$charset_collate};";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

// JLL_MOD - add a table for face-tagging
	dbDelta($sql);
bp_logdebug('bp_album_install : creating album database : '.print_r($sql,true));
	dbDelta($sqlalbums);
bp_logdebug('bp_album_install : creating albums database : '.print_r($sqlalbums,true));
	dbDelta($sqltag);

	update_site_option( 'bp-phototag-db-version', BP_ALBUM_DB_VERSION  );

        if (!get_site_option( 'bp_album_slug' ))
            update_site_option( 'bp_album_slug', 'album');
	
        if ( !get_site_option( 'bp_album_max_upload_size' ))
            update_site_option( 'bp_album_max_upload_size', 6 ); // 6mb

        if (!get_site_option( 'bp_album_max_pictures' ))
            update_site_option( 'bp_album_max_pictures', false);

        if (!get_site_option( 'bp_album_max_priv0_pictures' ))
            update_site_option( 'bp_album_max_priv0_pictures', false);

        if (!get_site_option( 'bp_album_max_priv2_pictures' ))
            update_site_option( 'bp_album_max_priv2_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv3_pictures' ))
            update_site_option( 'bp_album_max_priv3_pictures', false);
            
        if (!get_site_option( 'bp_album_max_priv4_pictures' ))
            update_site_option( 'bp_album_max_priv4_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv6_pictures' ))
            update_site_option( 'bp_album_max_priv6_pictures', false);

        if(!get_site_option( 'bp_album_keep_original' ))
            update_site_option( 'bp_album_keep_original', true);
        
        if(!get_site_option( 'bp_album_require_description' ))
            update_site_option( 'bp_album_require_description', false);

        if(!get_site_option( 'bp_album_enable_comments' ))
            update_site_option( 'bp_album_enable_comments', true);

        if(!get_site_option( 'bp_album_enable_wire' ))
            update_site_option( 'bp_album_enable_wire', true);

        if(!get_site_option( 'bp_album_middle_size' ))
            update_site_option( 'bp_album_middle_size', 600);

        if(!get_site_option( 'bp_album_thumb_size' ))
            update_site_option( 'bp_album_thumb_size', 150);
        
        if(!get_site_option( 'bp_album_per_page' ))
            update_site_option( 'bp_album_per_page', 20 );

        if(!get_site_option( 'bp_album_url_remap' ))
	    update_site_option( 'bp_album_url_remap', false);

        if(true) {
		$path = bp_get_root_domain() . '/wp-content/uploads/album';
		update_site_option( 'bp_album_base_url', $path );
	}

}
register_activation_hook( __FILE__, 'bp_album_install' );

/**
 * bp_album_check_installed()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('install_plugins') )
		return;

	if (!defined('BP_VERSION') || version_compare(BP_VERSION, '1.2','<')){
		add_action('admin_notices', 'bp_album_compatibility_notices' );
		return;
	}

	if ( get_site_option( 'bp-phototag-db-version' ) < BP_ALBUM_DB_VERSION )
	{
	bp_logdebug('bp_album_check_installed : b4 ALBUM INSTALL');
		bp_album_install();
	}
}
add_action( 'admin_menu', 'bp_album_check_installed' );

/**
 * bp_album_compatibility_notices() 
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_compatibility_notices() {

	if (!defined('BP_VERSION')){    
		$message .= ' BP Gallery needs BuddyPress 1.2 or later to work. Please install Buddypress';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
		
	}elseif(version_compare(BP_VERSION, '1.2','<') ){
		$message .= 'BP Gallery needs BuddyPress 1.2 or later to work. Your current version is '.BP_VERSION.' please upgrade.';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
	}
}

/**
 * bp_album_activate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_activate() {
bp_logdebug('bp_album_activate : start');
	bp_album_check_installed();
		AddDonationProfileField();
	do_action( 'bp_album_activate' );
}
register_activation_hook( __FILE__, 'bp_album_activate' );

/**
 * bp_album_deactivate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_deactivate() {
	do_action( 'bp_album_deactivate' );
}
register_deactivation_hook( __FILE__, 'bp_album_deactivate' );

	function bp_logdebug($debugStr)
	{
				if(!is_dir(BP_PLUGIN_PATH.'debug'))
				{
					mkdir(BP_PLUGIN_PATH.'debug');
				}
				global $wp_query;
		   	$BP_DEBUG_DIR = BP_PLUGIN_PATH.'debug/bpdebug'.date('dmY').'.log'; 
		
	    	$date = date('d.m.Y H:i:s'); 
    		$log = $date." : [BP] ".$debugStr."\n"; 
    		error_log($log, 3, $BP_DEBUG_DIR); 
	
	}
function AddDonationProfileField()
{
	$group_args = array(
  	   'name' => 'BP Gallery'
    	 );
	$group_id = xprofile_insert_field_group( $group_args ); // group's ID}
	xprofile_insert_field(
    array (
           field_group_id  => $group_id,
           name            => 'Donation Link',
           can_delete      => false, // Doesn't work *
           field_order  => 1,
           is_required     => false,
           description		=> 'If you want people to be able to make a financial donation to support your galleries, enter your Paypal donation link',
           type            => 'textbox'
    )
	);
}
?>
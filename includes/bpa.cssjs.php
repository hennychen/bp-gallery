<?php

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * Javascript and CSS files.
 */

/**
 * bp_album_add_js()
 *
 * This function will enqueue the components Javascript file, so that you can make
 * use of any Javascript you bundle with your component within your interface screens.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_add_js() {
    
	global $bp;

	if ( $bp->current_component == $bp->album->slug )
	{
		wp_enqueue_script( 'bp-gallery-js', WP_PLUGIN_URL .'/BPGallery/includes/js/general.js' );
		wp_localize_script( 'bp-gallery-js', 'BPAAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),   
																						 'BPADeleteAlbum' => wp_create_nonce( 'BPADeleteAlbum' ),		
																						 'BPAFeatureImage' => wp_create_nonce( 'BPAFeatureImage' ),		
																						 'BPAAlbumPrivacy' => wp_create_nonce( 'BPAAlbumPrivacy' ),		
																						 'BPADeleteImage' => wp_create_nonce( 'BPADeleteImage' )) );		
	}
}
 add_action( 'template_redirect', 'bp_album_add_js', 1 );

/**
 * bp_album_add_css()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_add_css() {
    
	global $bp;

		wp_enqueue_style( 'bp-gallery-css', WP_PLUGIN_URL .'/BPGallery/includes/css/general.css' );
//		wp_enqueue_script( 'bp-phototag-js', WP_PLUGIN_URL .'/bp-phototag/includes/js/general.js' );
		wp_print_styles();	
		wp_enqueue_script( 'bp-gallery-js', WP_PLUGIN_URL .'/BPGallery/includes/js/general.js' );
		wp_localize_script( 'bp-gallery-js', 'BPAAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),   
																						 'BPADeleteAlbum' => wp_create_nonce( 'BPADeleteAlbum' ),		
																						 'BPAFeatureImage' => wp_create_nonce( 'BPAFeatureImage' ),		
																						 'BPAAlbumPrivacy' => wp_create_nonce( 'BPAAlbumPrivacy' ),		
																						 'BPADeleteImage' => wp_create_nonce( 'BPADeleteImage' )) );		
}
function admin_css()
{
		wp_enqueue_style( 'bp-gallery-css', WP_PLUGIN_URL .'/BPGallery/includes/css/general.css' );
}
add_action( 'wp_head', 'admin_css' );
add_action( 'admin_head', 'bp_album_add_css' );
?>
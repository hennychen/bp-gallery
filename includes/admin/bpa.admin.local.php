<?php

/***
 * This file is used to add site administration menus to the single user admin backend.
 *
 * If you need to provide configuration options for your component that can only
 * be modified by a site administrator, this is the best place to do it.
 *
 * However, if your component has settings that need to be configured on a user
 * by user basis - it's best to hook into the front end "Settings" menu.
 */

/**
 * bp_album_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_admin() {
    
	global $bp;

	// If the form has been submitted and the admin referrer checks out, save the settings
	if ( isset( $_POST['submit'] )  ) {

		check_admin_referer('bpa-settings');

		if( current_user_can('install_plugins') ) {

			update_site_option( 'bp_album_slug', $_POST['bp_album_slug'] );
			update_site_option( 'bp_album_max_pictures', $_POST['bp_album_max_pictures']=='' ? false : intval($_POST['bp_album_max_pictures']) );

			foreach(array(0,2,4,6) as $i){
				$option_name = "bp_album_max_priv{$i}_pictures";
				$option_value = $_POST[$option_name]=='' ? false : intval($_POST[$option_name]);
				update_site_option($option_name , $option_value);
			}
			
			update_site_option( 'bp_album_max_upload_size', $_POST['bp_album_max_upload_size'] );
			update_site_option( 'bp_album_keep_original', $_POST['bp_album_keep_original'] );
			update_site_option( 'bp_album_require_description', $_POST['bp_album_require_description'] );
			update_site_option( 'bp_album_enable_comments', $_POST['bp_album_enable_comments'] );
			update_site_option( 'bp_album_disable_public_access', $_POST['bp_album_disable_public_access'] );
			update_site_option( 'bp_album_enable_wire', $_POST['bp_album_enable_wire'] );
			update_site_option( 'bp_album_middle_size', $_POST['bp_album_middle_size'] );
			update_site_option( 'bp_album_thumb_size', $_POST['bp_album_thumb_size'] );
			update_site_option( 'bp_album_per_page', $_POST['bp_album_per_page'] );
			update_site_option( 'bp_album_url_remap', $_POST['bp_album_url_remap'] );
			update_site_option( 'bp_album_base_url', $_POST['bp_album_base_url'] );

			$updated = true;

			if($_POST['bp_album_rebuild_activity'] && !$_POST['bp_album_undo_rebuild_activity']){
			    bp_album_rebuild_activity();
			}

			if( !$_POST['bp_album_rebuild_activity'] && $_POST['bp_album_undo_rebuild_activity']){
			    bp_album_undo_rebuild_activity();
			}
		}
		else {
			die("You do not have the required permissions to view this page");
		}
	}

        $bp_album_slug = get_site_option( 'bp_album_slug' );
        $bp_album_max_pictures = get_site_option( 'bp_album_max_pictures' );
        $bp_album_max_upload_size = get_site_option( 'bp_album_max_upload_size' );
        $bp_album_max_priv0_pictures = get_site_option( 'bp_album_max_priv0_pictures' );
        $bp_album_max_priv2_pictures = get_site_option( 'bp_album_max_priv2_pictures' );
        $bp_album_max_priv3_pictures = get_site_option( 'bp_album_max_priv3_pictures' );
        $bp_album_max_priv4_pictures = get_site_option( 'bp_album_max_priv4_pictures' );
        $bp_album_max_priv6_pictures = get_site_option( 'bp_album_max_priv6_pictures' );
        $bp_album_keep_original = get_site_option( 'bp_album_keep_original' );
        $bp_album_require_description = get_site_option( 'bp_album_require_description' );
        $bp_album_enable_comments = get_site_option( 'bp_album_enable_comments' );
        $bp_album_disable_public_access = get_site_option('bp_album_disable_public_access');
        $bp_album_enable_wire = get_site_option( 'bp_album_enable_wire' );
        $bp_album_middle_size = get_site_option( 'bp_album_middle_size' );
        $bp_album_thumb_size = get_site_option( 'bp_album_thumb_size' );
        $bp_album_per_page = get_site_option( 'bp_album_per_page' );
	$bp_album_url_remap = get_site_option( 'bp_album_url_remap' );
	$bp_album_base_url = get_site_option( 'bp_album_base_url' );
	$bp_album_rebuild_activity = false;
	$bp_album_undo_rebuild_activity = false;



	?>
	<div class="wrap">
	    
		<h2><?php _e('BP Galleries - ', 'bp-phototag' ) ?> 1.1<?php _e(' - [Single Site Mode]', 'bp-phototag' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __('Settings Updated.', 'bp-phototag' ) . "</p></div>" ?><?php endif; ?>

		<p>
		    <br>
		</p>
		<?php bpg_info_box(); ?>
		<?php // The address in this line of code determines where the form will be sent to // ?>
		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-phototag-settings' ?>" name="example-settings-form" id="example-settings-form" method="post">

                    <h3><?php _e('Slug Name', 'bp-phototag' ) ?></h3>

		    <p>
		    <?php 
			_e("Bad slug names will disable the plugin. No Spaces. No Punctuation. No Special Characters. No Accents.", 'bp-phototag' );
			echo " <br> ";
			_e("{ abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_- } ONLY.", 'bp-phototag' )
		    ?>
		    </p>
		    
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Name of BP Photos+tags slug', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_slug" type="text" id="bp_album_slug" value="<?php echo esc_attr($bp_album_slug ); ?>" size="10" />
					</td>
				</tr>

			</table>

                    <h3><?php _e('General', 'bp-phototag' ) ?></h3>

			<table class="form-table">  
                                <tr>
					<th scope="row"><?php _e('Force members to enter a description for each image', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_require_description" type="text" id="bp_album_require_description"<?php if ($bp_album_require_description == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_require_description" type="text" id="bp_album_require_description"<?php if ($bp_album_require_description == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Allow site members to post comments on album images', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_enable_comments" type="text" id="bp_album_enable_comments"<?php if ($bp_album_enable_comments == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_enable_comments" type="text" id="bp_album_enable_comments"<?php if ($bp_album_enable_comments == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Disable public access to member galleries', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_disable_public_access" type="text" id="bp_album_disable_public_access"<?php if ($bp_album_disable_public_access == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_disable_public_access" type="text" id="bp_album_disable_public_access"<?php if ($bp_album_disable_public_access == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Post image thumbnails to members activity stream', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_enable_wire" type="text" id="bp_album_enable_wire"<?php if ($bp_album_enable_wire == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_enable_wire" type="text" id="bp_album_enable_wire"<?php if ($bp_album_enable_wire == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>

			</table>

                    <h3><?php _e( 'Album Size Limits', 'bp-phototag' ) ?></h3>

                    <p>
		    <?php _e( "<b>Accepted values:</b> EMPTY (no limit), NUMBER (value you set), 0 (disabled). The first option does not accept 0. The last option only accepts a number.", 'bp-phototag' ) ?>
		    </p>
		    
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Max total images allowed in a members album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_pictures" type="text" id="example-setting-one" value="<?php echo esc_attr( $bp_album_max_pictures ); ?>" size="10" />
					</td>
				</tr>
	              <tr>		
					<th scope="row"><label for="target_uri"><?php _e('Max images visible to public allowed in a members album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv0_pictures" type="text" id="bp_album_max_priv0_pictures" value="<?php echo esc_attr( $bp_album_max_priv0_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible only to members in a members album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv2_pictures" type="text" id="bp_album_max_priv2_pictures" value="<?php echo esc_attr( $bp_album_max_priv2_pictures ); ?>" size="10" />
					</td>
				</tr>
                                 <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible only to group members in a group album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv3_pictures" type="text" id="bp_album_max_priv3_pictures" value="<?php echo esc_attr( $bp_album_max_priv3_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible only to friends in a members album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv4_pictures" type="text" id="bp_album_max_priv4_pictures" value="<?php echo esc_attr( $bp_album_max_priv4_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max private images in a members album', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv6_pictures" type="text" id="bp_album_max_priv6_pictures" value="<?php echo esc_attr( $bp_album_max_priv6_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Images per album page', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_per_page" type="text" id="bp_album_per_page" value="<?php echo esc_attr( $bp_album_per_page ); ?>" size="10" />
					</td>
				</tr>
			</table>

			<h3><?php _e('Image Size Limits', 'bp-phototag' ) ?></h3>

			<p>
			<?php _e( "Uploaded images will be re-sized to the values you set here. Values are for both X and Y size in pixels. We <i>strongly</i> suggest you keep the original image files so you can re-render your images during the upgrade process.", 'bp-phototag' ) ?>
			</p>
			
			<table class="form-table">
			    <tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Maximum file size (mb) that can be uploaded', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_max_upload_size" type="text" id="bp_album_max_upload_size" value="<?php echo esc_attr( $bp_album_max_upload_size ); ?>" size="10" />
					</td> 
				</tr>
	              <tr>
					<th scope="row"><label for="target_uri"><?php _e('Album Image Size', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_middle_size" type="text" id="bp_album_middle_size" value="<?php echo esc_attr( $bp_album_middle_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Thumbnail Image Size', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_thumb_size" type="text" id="bp_album_thumb_size" value="<?php echo esc_attr( $bp_album_thumb_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Keep original image files', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_keep_original" type="text" id="bp_album_keep_original"<?php if ( $bp_album_keep_original == true ) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_keep_original" type="text" id="bp_album_keep_original"<?php if ($bp_album_keep_original == false) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>

			</table>

			<h3><?php _e('Image URL Mapping', 'bp-phototag' ) ?></h3>

			<p>
			<?php
			    _e( "If you get broken links when viewing images in bp-phototag, it means your server is sending the wrong base URL to the plugin. You can use the image URL re-mapping function to fix this.",'bp-phototag' );
			    echo "<a href='http://code.google.com/p/buddypress-media/wiki/UsingTheURLRemapper'> ";
			    _e("DOCUMENTATION",'bp-phototag' );
			    echo "</a>";
			?>
			</p>

			<table class="form-table">
                                <tr>
					<th scope="row"><?php _e('Use image URL re-mapping', 'bp-phototag' ) ?></th>
					<td>
						<input type="radio" name="bp_album_url_remap" type="text" id="bp_album_url_remap"<?php if ($bp_album_url_remap == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-phototag' ) ?> &nbsp;
						<input type="radio" name="bp_album_url_remap" type="text" id="bp_album_url_remap"<?php if ($bp_album_url_remap == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-phototag' ) ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Base URL', 'bp-phototag' ) ?></label></th>
					<td>
						<input name="bp_album_base_url" type="text" id="bp_album_base_url" value="<?php echo esc_attr( $bp_album_base_url ); ?>" size="70" />
						/userID/filename.xxx
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-phototag' ) ?>"/>
			</p>

			<?php
			// This is very important, don't leave it out.
			wp_nonce_field( 'bpa-settings' );
			?>
		</form>
	</div>
<?php
}
function bpg_info_box() {
?>
	<div id="fb-info">
		<h3>Info</h3>
		<ul>
			<li><a href="http://www.amkd.com.au/wordpress/bp-gallery-plugin/98">BP Gallery Home</a></li>
			<!--  li><a href="http://wordpress.org/tags/fotobook?forum_id=10">Support Forum</a></li -->
			<li><a href="http://www.fatcow.com/join/index.bml?AffID=642780">Host your Web site with FatCow!</a></li>
			<li><a href="http://www.amkd.com.au/">Need someone to build your web site?</a></li>
		</ul>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="YSM3KMT3B5AQE">
			<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
<?php
}

?>
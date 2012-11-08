<?php 

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * bp_album_screen_picture()
 *
 * Single picture
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_album_screen_single() {

	global $bp,$pictures_template;

	if ( $bp->current_component == $bp->album->slug && $bp->album->single_edit_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  ) {
	
		do_action( 'bp_album_screen_edit' );

		add_action( 'bp_template_title', 'bp_album_screen_edit_title' );
		add_action( 'bp_template_content', 'bp_album_screen_edit_content' );
	
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		
		return;
	}
	
	do_action( 'bp_album_screen_single' );

	bp_album_query_pictures();
	bp_core_load_template( apply_filters( 'bp_album_template_screen_single', 'album/single' ) );
}
/**
 * bp_album_show_albums()
 *
 * Single picture
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_album_show_albums() {

	global $bp,$albums_template;
	if(!isset($albums_template))
	{
		bp_album_query_albums();
	}		

	if ( $bp->current_component == $bp->album->slug && $bp->album->album_slug == $bp->current_action && $albums_template->album_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  ) {
	
		do_action( 'bp_album_screen_edit' );

		add_action( 'bp_template_content', 'bp_album_screen_edit_album_content' );
//		add_action( 'bp_template_content', 'bp_album_screen_edit_content' );
	
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		
		return;
	}
	
	do_action( 'bp_album_show_albums' );

	bp_album_query_albums();
	bp_core_load_template( apply_filters( 'bp_album_template_album', 'album/albums' ) );
}

/**
 * bp_album_screen_edit_title()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_album_screen_edit_title() {
	_e( 'Edit Photo', 'bp-phototag' );
}

/**
 * bp_album_screen_edit_content()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_album_screen_edit_content() {

	global $bp,$pictures_template;

	if (bp_album_has_pictures() ) :  
		if(isset($bp->action_variables[0]))
		{
	   bp_album_the_picture($bp->action_variables[0]);
	  }
	  else
		{
	   bp_album_the_picture();
	  }
	  $album_id = $bp->action_variables[2];
	$limit_info = bp_album_limits_info();
	if($bp->bp_album_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-phototag'),
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	?>
	<h4><?php _e( 'Edit Photo', 'bp-phototag' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-phototag-edit-form" id="bp-phototag-edit-form" class="standard-form">

    <img id="picture-edit-thumb" src='<?php bp_album_picture_middle_url() ?>' />
		<?php // JLL_MOD - adds face-tagging
        global $wpdb, $bp;
        bp_core_delete_notifications_for_user_by_item_id( $bp->loggedin_user->id, bp_album_get_picture_id(), album, 'user_tagged' );
            //populate autofill
            $table_name = $wpdb->prefix . "bp_friends";
            $currentuserid = $bp->loggedin_user->id;
            $currentusername = $bp->loggedin_user->fullname;
            $friends = $wpdb->get_results( "SELECT friend_user_id, initiator_user_id FROM " . $table_name. " WHERE is_confirmed=1 AND (friend_user_id=" . $currentuserid  . " OR initiator_user_id=" . $currentuserid . ")", ARRAY_A );
            for($i=0; $i<count($friends); $i++)
            {
                $singlefriend = $friends[$i];
                if ( $currentuserid == $singlefriend[initiator_user_id] ) {
                    $friendids[] = $singlefriend[friend_user_id];
                    $friendnames[] = bp_core_get_user_displayname( $singlefriend[friend_user_id] );
                } else {
                    $friendids[] = $singlefriend[initiator_user_id];
                    $friendnames[] = bp_core_get_user_displayname( $singlefriend[initiator_user_id] );
                }		
            }
            // populate default tags
            $thepic = bp_album_get_picture_id();
            $table_tags = $wpdb->prefix . "bp_album_tags";
            $default_tags = $wpdb->get_results( "SELECT * FROM " . $table_tags. " WHERE photo_id=" . $thepic, ARRAY_A );
            for($i=0; $i<count($default_tags); $i++)
            {
                $singletag = $default_tags[$i];
                $d_tags_id[] = $singletag[id];
                $d_tags_tagged_id[] = $singletag[tagged_id];
                $d_tags_tagged_name[] = $singletag[tagged_name];
                $d_tags_height[] = $singletag[height];
                $d_tags_width[] = $singletag[width];
                $d_tags_top[] = $singletag[top_pos];
                $d_tags_left[] = $singletag[left_pos];
            }
        ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#picture-edit-thumb").tag({
                    autoComplete: [ <?php // set autofill
                        echo '{ value: "' . $currentusername . '", label: "' . $currentusername . '", fid: "' . $currentuserid  . '", plink: "urlurlurlurl" },';
                        for ( $i = 0; $i < count( $friends ); $i++ ) {
                        echo '{ value: "' . $friendnames[$i] . '", label: "' . $friendnames[$i] . '", fid: "' . $friendids[$i] . '", plink: "urlurlurlurl" }, ';
                        } ?>
                        ],
                    defaultTags: [ <?php // set default tags
                        for ( $i = 0; $i < count( $default_tags ); $i++ ) {
                        echo '{ id: "' . $d_tags_id[$i] . '", label: "' . $d_tags_tagged_name[$i] . '", fid: "' . $d_tags_tagged_id[$i] . '", height: "' . $d_tags_height[$i] . '", width: "' . $d_tags_width[$i] . '", top: "' . $d_tags_top[$i] . '", left: "' . $d_tags_left[$i] . '", plink: "' . bp_core_get_user_domain($d_tags_tagged_id[$i]) . '" }, ';
                        } ?>	  
                        ],
                    save : function( width,height,top_pos,left,label,the_tag,fid ){
                                    var pid = "<?php bp_album_picture_id(); ?>";
                                    var fid = $("#fid").html(); 
                                    var oid = "<?php echo $bp->displayed_user->id; ?>";
                                    if (window.XMLHttpRequest)
                                      {// code for IE7+, Firefox, Chrome, Opera, Safari
                                      xmlhttp=new XMLHttpRequest();
                                      }
                                    else
                                      {// code for IE6, IE5
                                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                                      }
                                    xmlhttp.onreadystatechange=function()
                                      {
                                      if (xmlhttp.readyState==4 && xmlhttp.status==200)
                                        {
                                            var tagsetid = xmlhttp.responseText;
                                            the_tag.setId(tagsetid); // set tag id from db
                                            $("#fid").html("");
                                            $("#enable").html("Tag this photo");
                                            $("#enable").attr('class','tag-button');
                                            $("div.picture-middle").find('a').attr('id','');
                                        }
                                      }
                                    xmlhttp.open("GET","<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/bp-phototag/photo-tagging/phototag-gethint.php?wid="+width+"&hei="+height+"&top="+top_pos+"&left="+left+"&lab="+label+"&pid="+pid+"&fid="+fid+"&oid="+oid,true);
                                    xmlhttp.send();
                                },
                    remove: function(id){
                                    //alert('Here I can do some ajax to delete tag #'+id+' in my db');
                                    if (window.XMLHttpRequest)
                                      {// code for IE7+, Firefox, Chrome, Opera, Safari
                                      xmlhttp=new XMLHttpRequest();
                                      }
                                    else
                                      {// code for IE6, IE5
                                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                                      }
                                    xmlhttp.open("GET","<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/bp-phototag/photo-tagging/phototag-gethint.php?d="+id+"&pid=<?php bp_album_picture_id(); ?>",true);
                                    xmlhttp.send();
                                },
                });
                $("#enable").click(function(){
                            $("#picture-edit-thumb").parent().mousedown(function(e){
                                $("#picture-edit-thumb").showDrag(e);
                                $("#picture-edit-thumb").parent().unbind('mousedown');
                            });
                            $("#enable").html("Click on the photo to add a tag...");
                            $("#enable").attr('class','');
                });
            });
        </script>
        <div id="fid" style="display:none;"></div><div id="uid" style="display:none;"><?php echo $bp->loggedin_user->id; ?></div><div id="did" style="display:none;"><?php echo $bp->displayed_user->id; ?></div><div id="pid" style="display:none;"><?php bp_album_picture_id(); ?></div>
        
     <?php if ( $bp->loggedin_user->id ) { ?>
     <div id="enable" class="tag-button">Tag this photo</div> 
     <?php } ?>




    <p>
  <input type="hidden" name="picture_id" value="<?php bp_album_picture_id() ?>" />
  <input type="hidden" name="album_id" value="<?php echo $album_id ?>" />

	<label><?php _e('Photo Title *', 'bp-phototag' ) ?><br />
	<input type="text" name="title" id="picture-title" size="100" value="<?php
		echo (empty($_POST['title'])) ? bp_album_get_picture_title() : wp_filter_kses($_POST['title']);
	?>"/></label>
    </p>
    <p>
	<label><?php _e('Photo Description', 'bp-phototag' ) ?><br />
	<textarea name="description" id="picture-description" rows="15"cols="40" ><?php
		echo (empty($_POST['description'])) ? bp_album_get_picture_desc() : wp_filter_kses($_POST['description']);
	?></textarea></label>
    </p>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-phototag' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-phototag-edit' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( "Either this url is not valid or you can't edit this picture.", 'bp-phototag' ) ?></p>
	<?php endif;
}

/**
 * bp_album_screen_pictures()
 *
 * An album page
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_screen_pictures() {

	do_action( 'bp_album_screen_pictures' );
	if(isset($bp->action_variables[0]))
	{
		$locAlbumID = $bp->action_variables[0];
		$album_args = array(
								'album_id' => $locAlbumID);
		bp_album_query_pictures($album_args);
//		bp_album_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_album_template_screen_pictures', 'album/albumcontent' ) );
	}		
	else if(isset($_GET['album_id']))
	{
		$locAlbumID = $_GET['album_id'];
		$album_args = array(
								'album_id' => $locAlbumID);
		bp_album_query_pictures($album_args);
		bp_album_query_albums();
		bp_album_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_album_template_screen_pictures', 'album/albumcontent' ) );
	}
	else
	{
		bp_album_query_pictures();
		bp_core_load_template( apply_filters( 'bp_album_template_screen_pictures', 'album/pictures' ) );
	}
}
function bp_album_screen_album() {

	do_action( 'bp_album_screen_album' );

	bp_album_query_albums();
	bp_core_load_template( apply_filters( 'bp_album_template_screen_albums', 'album/pictures' ) );
}

/**
 * bp_album_screen_upload()
 *
 * Sets up and displays the screen output for the sub nav item "example/screen-two"
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_screen_upload() {
    
	global $bp;

	do_action( 'bp_album_screen_upload' );

	add_action( 'bp_template_content', 'bp_album_screen_upload_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * bp_album_screen_upload_title()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_screen_upload_title() {
	_e( 'Upload new photos', 'bp-phototag' );
}

/**
 * bp_album_screen_upload_content()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_screen_upload_content() {

	global $bp;

	$limit_info = bp_album_limits_info();

	if($bp->bp_album_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-phototag'),
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	$owner_id = $bp->loggedin_user->id;
	$loc_album = new BP_Album_Album();
	$loc_album->owner_id = $owner_id; 
	$loc_album->owner_type = 'user';

	$albums = $loc_album->query_album_names();
	if( ($limit_info['all']['enabled'] == true) && ($limit_info['all']['remaining'] > 0) ):?>

	<h4><?php _e( 'Upload new photos', 'bp-phototag' ) ?></h4>
	<!-- form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
		<a style="position: relative; top: 8px;" href="javascript:$('#file_upload').uploadifive('upload')">Upload Files</a>
	</form -->

	<form method="post" enctype="multipart/form-data" name="bp-phototag-upload-form" id="bp-phototag-upload-form" class="standard-form">

    <input type="hidden" name="upload" value="<?php echo $bp->album->bp_album_max_upload_size; ?>" />
    <input type="hidden" name="action" value="picture_upload" />

 	<?php 
 	if($albums)
	{
		echo "\n<p><label>";
					_e('Select Gallery', 'bp-phototag' );
					echo "\n".'	<SELECT NAME="selected_album" id="selected_album">'."\n";
					echo "<OPTION>\n";
					foreach( $albums as $album)
					{
						echo '<OPTION VALUE="'.$album->id.'">'.$album->title."\n";
					}
					echo "</SELECT>\n<br />";
					_e('Or', 'bp-phototag' );
				echo "</label></p>\n";
	}?>

   <p>
	<label><?php _e('Enter Gallery Name', 'bp-phototag' ) ?><br />
	<input type="text" name="album_name" id="album_name"/></label>
    </p>

    <p>
	<label><?php _e('Select Picture to Upload *', 'bp-phototag' ) ?><br />
	<input type="file" value="" name="upload[]" multiple id="file"/></label>
    </p>
    <p>
	<label><?php _e('Visibility','bp-phototag') ?></label>

			<?php $checked=false;
				$groups_array = BP_Groups_Member::get_is_admin_of($owner_id);
				$group_count = $groups_array['total'];
				foreach($priv_str as $k => $str){
					if($limit_info[$k]['enabled']) {?>
				
			<label><input type="radio" id="priv_<?php echo $k ?>"name="privacy" value="<?php echo $k ?>" <?php
				if(!$checked){
					 echo 'checked="checked" ';
					 $checked = true;
				}
				if($k == 3)
				{
					if ($group_count == 0) // Only add group privacy if member has groups
					{
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-phototag' );
					}											
					else
					{
						echo '/>'.$str;
						echo "\n<label>";
						_e('Select Group', 'bp-phototag' );
						echo "\n".'	<SELECT NAME="selected_group" id="selected_group">'."\n";
					foreach( $groups_array['groups'] as $group)
						{
							echo '<OPTION VALUE="'.$group->id.'">'.$group->name."\n";
						}
						echo "</SELECT>\n<br />";
						echo "</label>\n";

					}
				}
				else
				{
					if (!$limit_info[$k]['current'] && !$limit_info[$k]['remaining'])
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-phototag' );
					else
						echo '/>'.$str;
				}
			?></label>

			<?php }} ?>
    </p>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Upload picture', 'bp-phototag' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-phototag-upload' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( 'You have reached the upload limit, delete some pictures if you want to upload more', 'bp-phototag' ) ?></p>
	<?php endif;
}
 
 
/********************************************************************************
 * Action Functions
 *
 * Action functions are exactly the same as screen functions, however they do not
 * have a template screen associated with them. Usually they will send the user
 * back to the default screen after execution.
 */

/**
 * bp_album_action_upload()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_action_upload() {
    
	global $bp, $wpdb;
	
	$notify_activity = false;


	if ( $bp->current_component == $bp->album->slug && $bp->album->upload_slug == $bp->current_action && isset( $_POST['submit'] )) {
	
		check_admin_referer('bp-phototag-upload');
		
		$error_flag = false;
		$feedback_message = array();

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-phototag' );

		} else {

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }
			$test = bp_album_get_picture_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			// For group privacy get the group_id
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
					$feedback_message[] = __( 'Please select a group for this album.', 'bp-phototag' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

			if( $pic_limit === null){
				$error_flag = true;
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-phototag' );	
			}			
			elseif( $pic_limit !== false && ( $pic_limit === 0  || $pic_limit <= bp_album_get_picture_count(array('privacy'=>$priv_lvl)) ) ) {

				$error_flag = true;
				
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public pictures.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to community members.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to friends.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private pictures.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
				}
			}
		}
		
		$uploadErrors = array(
			0 => __("There was no error, the file uploaded with success", 'bp-phototag'),
			1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
			2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
			3 => __("The uploaded file was only partially uploaded", 'bp-phototag'),
			4 => __("No file was uploaded", 'bp-phototag'),
			6 => __("Missing a temporary folder", 'bp-phototag')
		);
			$owner_type = 'user';
			$owner_id = $bp->loggedin_user->id;
			$date_uploaded =  gmdate( "Y-m-d H:i:s" );
		if(isset($_POST['selected_album']) && ($_POST['selected_album']) ){
				$album_id = $_POST['selected_album'];
			// Update the album's privacy level as well as the privacy level of the pictures within it	
		}
		else
		{
			if( !isset($_POST['album_name']) ){
					$album_name = 'default';
			}
			else {
					$album_name = $_POST['album_name'];
			}
			$album_id = bp_album_add_album($owner_type,$owner_id,$album_name,$album_name,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$group_id,$spare2,$spare3,$spare4);

		}
		if($album_id)
		{
			bp_album_update_privacy($album_id,$priv_lvl,$group_id);
			$album = new BP_Album_Album( $album_id );
			// Set image privacy level to album privacy level
			$files=array();
			if ( isset($_FILES['upload']) )
			{
				$fdata=$_FILES['upload'];
				if(is_array($fdata['name']))
				{
					for($i=0;$i<count($fdata['name']);++$i){
	        	$files[]=array(
				    	'name'    =>$fdata['name'][$i],
    					'type'  => $fdata['type'][$i],
    					'tmp_name'=>$fdata['tmp_name'][$i],
    					'error' => $fdata['error'][$i], 
    					'size'  => $fdata['size'][$i]  
    				);
    			}
				}
				else
				{
			 		$files[]=$fdata;
				}
				if ($album->feature_image == 0)
				{
					$firstImage = true;
				}
				foreach($files as $file)
				{
					if ( $file['error'] ) {
						
						$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'bp-phototag' ), $uploadErrors[$file['error']] );
						$error_flag = true;
						continue;
					}		
					elseif ( ($file['size'] / (1024 * 1024)) > $bp->album->bp_album_max_upload_size ) 
					{
						$feedback_message[] = sprintf(__( 'The picture you tried to upload was too big. Please upload a file less than ' . $bp->album->bp_album_max_upload_size . 'MB', 'bp-phototag'));
						$error_flag = true;
						continue;				
					}
					// Check the file has the correct extension type. Copied from bp_core_check_avatar_type() and modified with /i so that the
					// regex patterns are case insensitive (otherwise .JPG .GIF and .PNG would trigger an error)
					elseif ( (!empty( $file['type'] ) && !preg_match('/(jpe?g|gif|png)$/i', $file['type'] ) ) || !preg_match( '/(jpe?g|gif|png)$/i', $file['name'] ) ) {
						
						$feedback_message[] = __( 'Please upload only JPG, GIF or PNG image files.', 'bp-phototag' );
						$error_flag = true;
						continue;
					}

					add_filter( 'upload_dir', 'bp_album_upload_dir', 10, 0 );

					$pic_org = wp_handle_upload( $file,array('test_form' => false,'action'=>'picture_upload') );


					if ( !empty( $pic_org['error'] ) ) {
						$feedback_message[] = sprintf( __('Your upload failed, please try again. Error was: %s', 'bp-phototag' ), $pic_org['error'] );
						$error_flag = true;
						continue;
					}
		
					if( !is_multisite() )
					{

			    	// Some site owners with single-blog installs of WordPress change the path of
						// their upload directory by setting the constant 'BLOGUPLOADDIR'. Handle this
						// for compatibility with legacy sites.

						if( defined( 'BLOGUPLOADDIR' ) )
						{
							$abs_path_to_files = str_replace('/files/','/',BLOGUPLOADDIR);
						}
						else 
						{
							$abs_path_to_files = ABSPATH;
						}

					}
					else
					{
						// If the install is running in multisite mode, 'BLOGUPLOADDIR' is automatically set by
						// WordPress to something like "C:\xampp\htdocs/wp-content/blogs.dir/1/" even though the
						// actual file is in "C:\xampp\htdocs/wp-content/uploads/", so we need to use ABSPATH

						$abs_path_to_files = ABSPATH;
					}
					$pic_org_path = $pic_org['file'];
					$pic_org_url = str_replace($abs_path_to_files,'/',$pic_org_path);
			
					$pic_org_size = getimagesize( $pic_org_path );
					$pic_org_size = ($pic_org_size[0]>$pic_org_size[1])?$pic_org_size[0]:$pic_org_size[1];
			
					if($pic_org_size <= $bp->album->bp_album_middle_size)
					{
						$pic_mid_path = $pic_org_path;
						$pic_mid_url = $pic_org_url;
					} 
					else
					{
						$pic_mid = wp_create_thumbnail( $pic_org_path, $bp->album->bp_album_middle_size );
						$pic_mid_path = str_replace( '//', '/', $pic_mid );
						$pic_mid_url = str_replace($abs_path_to_files,'/',$pic_mid_path);

						if (!$bp->album->bp_album_keep_original)
						{

							unlink($pic_org_path);
							$pic_org_url=$pic_mid_url;
							$pic_org_path=$pic_mid_path;
						}
					}

					if($pic_org_size <= $bp->album->bp_album_thumb_size){
						$pic_thumb_path = $pic_org_path;
						$pic_thumb_url = $pic_org_url;
					} 
					else
					{
						$pic_thumb = image_resize( $pic_mid_path, $bp->album->bp_album_thumb_size, $bp->album->bp_album_thumb_size, true);
						$pic_thumb_path = str_replace( '//', '/', $pic_thumb );
						$pic_thumb_url = str_replace($abs_path_to_files,'/',$pic_thumb);
					}

					$owner_type = 'user';
					$owner_id = $bp->loggedin_user->id;
					$date_uploaded =  gmdate( "Y-m-d H:i:s" );
					$title = $_FILES['file']['name'];
					$description = ' ';
					$privacy = $priv_lvl;

					$id=bp_album_add_picture($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path,$album_id,$group_id);
				    if($id)
				    {
				    	//We want to save the feature image if the album does not have one
				    	if($firstImage)
				    	{
								$album->date_updated = gmdate( "Y-m-d H:i:s" );
				    		$album->feature_image = $id;
				    		$album->feature_image_path = $pic_thumb_url;
				    		$album->save();
				    		$firstImage = false;
			    			$notify_activity = true;

				    	}
					    $feedback_message[] = __('Picture uploaded. Now you can edit the pictures details.', 'bp-phototag');
					  }
				    else {
					    $error_flag = true;
					    $feedback_message[] = __('There were problems saving the pictures details.', 'bp-phototag');
						}
				}
			}
			else
			{
				$error_flag = true;
				$feedback_message[] = __('There were problems creating the album.', 'bp-phototag');
			}
		}
		else 
		{
			$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'bp-phototag' ), $uploadErrors[4] );
			$error_flag = true;
		
		}

		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} 
		else {
			if ($notify_activity)
			{
				bp_album_record_album_activity($album);
			}
			else
			{
				bp_album_record_album_activity($album,true);
			}
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/?album_id='. $album->id);
//			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/?album_id='. $album->id);
//			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
			die;
		}
		
	}
	
}
add_action('bp_actions','bp_album_action_upload',3);
add_action('wp','bp_album_action_upload',3);

/**
 * bp_album_upload_dir() 
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_upload_dir() {
    
	global $bp;

	$user_id = $bp->loggedin_user->id;
	
	$dir = BP_ALBUM_UPLOAD_PATH;

	$siteurl = trailingslashit( get_blog_option( 1, 'siteurl' ) );
	$url = str_replace(ABSPATH,$siteurl,$dir);
	
	$bdir = $dir;
	$burl = $url;
	
	$subdir = '/' . $user_id;
	
	$dir .= $subdir;
	$url .= $subdir;

	if ( !file_exists( $dir ) )
		@wp_mkdir_p( $dir );

	return apply_filters( 'bp_album_upload_dir', array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir, 'basedir' => $bdir, 'baseurl' => $burl, 'error' => false ) );

}

/**
 * bp_album_action_edit()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_action_edit() {
    
	global $bp,$pictures_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_edit_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1] &&  isset( $_POST['submit'] )) {
	
		check_admin_referer('bp-phototag-edit');
		
		$error_flag = false;
		$feedback_message = array();
		
		if(isset($_POST['picture_id']))
		{
			$id = $_POST['picture_id'];
		}
		else
		{
			$error_flag = true;
			$feedback_message[] = __( 'Unable to save image.', 'bp-phototag' );
		}
		$album_id = 0;
		if(isset($_POST['album_id']))
		{
			$album_id = $_POST['album_id'];
		}
		
		if(empty($_POST['title'])){
//			$error_flag = true;
//			$feedback_message[] = __( 'Picture Title can not be blank.', 'bp-phototag' );
		}

		if( $bp->album->bp_album_require_description && empty($_POST['description'])){
//			$error_flag = true;
//			$feedback_message[] = __( 'Picture Description can not be blank.', 'bp-phototag' );
		}
		
		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-phototag' );	
		}
		else {
			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }


			if($priv_lvl == 10 )
				$pic_limit = is_super_admin() ? false : null;
			if( $pic_limit === null){
				$error_flag = true;
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-phototag' );	
			}
			elseif( $pic_limit !== false && $priv_lvl !== $pictures_template->pictures[0]->privacy && ( $pic_limit === 0|| $pic_limit <= bp_album_get_picture_count(array('privacy'=>$priv_lvl)) ) ){
				$error_flag = true;
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public pictures.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to community members.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for pictures visible to friends.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private pictures.', 'bp-phototag' ).' '.__( 'Please select another privacy option.', 'bp-phototag' );
						break;
				}
			}
		}

		if(bp_is_active('activity') && $bp->album->bp_album_enable_comments)
			if(!isset($_POST['enable_comments']) || ($_POST['enable_comments']!= 0 && $_POST['enable_comments']!= 1)){
				$error_flag = true;
				$feedback_message[] = __( 'Comments option is not correct.', 'bp-phototag' );
			}
		else
			$_POST['enable_comments']==0;

		if( !$error_flag ){

			// WordPress adds an escape character "\" to some special values in INPUT FIELDS (test's becomes test\'s), so we have to strip
			// the escape characters, and then run the data through *proper* filters to prevent SQL injection, XSS, and various other attacks.

			if( bp_album_edit_picture($id,stripslashes($_POST['title']),stripslashes($_POST['description']),$priv_lvl,$_POST['enable_comments']) ){
				$feedback_message[] = __('Picture details saved.', 'bp-phototag');
			}else{
				$error_flag = true;
				$feedback_message[] = __('There were problems saving picture details.', 'bp-phototag');
			}
		}
		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} 
		else {
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->album_slug.'/'.$album_id.'/');
			die;
		}
		
	}
	
}
add_action('bp_actions','bp_album_action_edit',3);
add_action('wp','bp_album_action_edit',3);

/**
 * bp_album_action_delete()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_action_delete() {

	global $bp,$pictures_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_slug == $bp->current_action && $pictures_template->picture_count && isset($bp->action_variables[1]) && $bp->album->delete_slug == $bp->action_variables[1] ) {
		check_admin_referer('bp-phototag-delete-pic');
		
				
		if(!$pictures_template->picture_count){
			bp_core_add_message( __( 'This url is not valid.', 'bp-phototag' ), 'error' );
			return;
		}
		else{
			
			if ( !bp_is_my_profile() && !current_user_can(level_10) ) {
				bp_core_add_message( __( 'You don\'t have permission to delete this picture', 'bp-phototag' ), 'error' );
			}
			elseif (bp_album_delete_picture($pictures_template->pictures[0]->id)){
				bp_core_add_message( __( 'Picture deleted.', 'bp-phototag' ), 'success' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->pictures_slug .'/');
				die;
			}
			else{
				bp_core_add_message( __( 'There were problems deleting the picture.', 'bp-phototag' ), 'error' );
			}
		}
		bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->single_slug .'/'.$pictures_template->pictures[0]->id. '/');
		die;
	}
}
add_action('bp_actions','bp_album_action_delete',3);
add_action('wp','bp_album_action_delete',3);

/**
 * bp_album_screen_all_images()
 * 
 * Displays sitewide featured content block
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_screen_all_images() {

        global $bp;

        bp_album_query_pictures();
	bp_album_load_subtemplate( apply_filters( 'bp_album_screen_all_images', 'album/all-images' ), false );
}
add_action('bp_album_all_images','bp_album_screen_all_images',3);

function bp_album_screen_add_album() {
    
	global $bp;

	do_action( 'bp_album_screen_add_album' );

	add_action( 'bp_template_content', 'bp_album_screen_add_album_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_album_screen_add_album_content() {

	global $bp;

	$limit_info = bp_album_limits_info();

	if($bp->bp_album_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-phototag'),
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}

	?>

	<h4><?php _e( 'Add New Gallery', 'bp-phototag' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-phototag-add-album-form" id="bp-phototag-add-album-form" class="standard-form">

    <!-- input type="hidden" name="action" value="add_album" /-->
    <input type="hidden" name="action" value="album_upload" />

    <p>
			<label><?php _e('Enter Gallery Name', 'bp-phototag' ) ?><br />
			<input type="text" name="album_name" id="album_name"/></label>
    </p>
   	<p>
			<label><?php _e('Enter Gallery Description', 'bp-phototag' ) ?><br />
				<textarea cols="40" rows="10" name="album_desc" id="album_desc">
				</textarea>
			</label>
    </p>
    <p>
	    <?php	bp_album_screen_visibility(); ?>
    </p>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Add Album', 'bp-phototag' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-phototag-add-album' );
		?>
	</form>
	<?php 
}

/**
 * bp_album_action_add_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_action_add_album() {
    
	global $bp;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->albums_add_slug == $bp->current_action && isset( $_POST['submit'] )) 
	{
	
		check_admin_referer('bp-phototag-add-album');
		
		$error_flag = false;
		$feedback_message = array();

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-phototag' );

		} else 
		{

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }
			$test = bp_album_get_picture_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
					$feedback_message[] = __( 'Please select a group for this gallery.', 'bp-phototag' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

		
			$uploadErrors = array(
				0 => __("There was no error, the file uploaded with success", 'bp-phototag'),
				1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
				2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
				3 => __("The uploaded file was only partially uploaded", 'bp-phototag'),
				4 => __("No file was uploaded", 'bp-phototag'),
				6 => __("Missing a temporary folder", 'bp-phototag')
			);
		}
		if(!$error_flag)
		{  


			if( !isset($_POST['album_name']) ){
		    $error_flag = true;
		    $feedback_message[] = __('You need to specify a gallery name', 'bp-phototag');
			}
			else {
				$album_name = $_POST['album_name'];
			}
			if(!$error_flag)
			{
				if( !isset($_POST['album_desc']) ){
					$album_desc = $album_name;
				}
				else {
					$album_desc = $_POST['album_desc'];
				}
				$owner_type = 'user';
				$owner_id = $bp->loggedin_user->id;
				$date_uploaded =  gmdate( "Y-m-d H:i:s" );
			
				$album_id = bp_album_add_album($owner_type,$owner_id,$album_name,$album_name,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$group_id,$spare2,$spare3,$spare4);
				if($album_id)
				{
	
			    $feedback_message[] = __('Gallery '.$album_name.' created successfully, you can now add images to it', 'bp-phototag');
				}
				else
				{
					$error_flag = true;
					$feedback_message[] = __('There were problems creating the album '.$album_name.'.', 'bp-phototag');
				}
			}
			if ($error_flag){
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
			} 
			else {
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/');
//				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
				die;
			}
		
		}
	}
}
add_action('bp_actions','bp_album_action_add_album',3);
add_action('wp','bp_album_action_add_album',3);
function bp_album_screen_edit_album() {
    
	global $bp;

	do_action( 'bp_album_screen_edit_album' );

	add_action( 'bp_template_content', 'bp_album_screen_edit_album_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_album_screen_visibility($group_id = 0){
	global $bp;

	$limit_info = bp_album_limits_info();

	if($bp->bp_album_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-phototag'),
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	$owner_id = $bp->loggedin_user->id;

   echo "<p>\n<label>";
	_e('Visibility','bp-phototag');
	echo"</label>\n";

	$checked=false;
	$groups_array = BP_Groups_Member::get_is_admin_of($owner_id);
	$group_count = $groups_array['total'];
	foreach($priv_str as $k => $str){
		echo "\n".'<label><input type="radio" id="priv_'.$k.'" name="privacy" value="'.$k.'"';
		if($k == bp_album_get_album_priv()) {
			if(!$checked){
				echo 'checked="checked" ';
				$checked = true;
			}
		}
		if($k == 3)
		{
			if ($group_count == 0) // Only add group privacy if member has groups
			{
				echo 'disabled="disabled" />'.$str;
			}											
			else
			{
				echo '/>'.$str;
				echo "\n<label>";
				_e('Select Group', 'bp-phototag' );
				echo "\n".'	<SELECT NAME="selected_group" id="selected_group">'."\n";
				foreach( $groups_array['groups'] as $group)
				{
					if(($group_id > 0) && ($group->id == $group_id))
					{
						echo '<OPTION selected="selected" VALUE="'.$group->id.'">'.$group->name."\n";
					}
					else
					{
						echo '<OPTION VALUE="'.$group->id.'">'.$group->name."\n";
					}
				}
				echo "</SELECT>\n<br />";
				echo "</label>\n";

			}
		}
		else
		{
			echo '/>'.$str;
		}
		echo "\n</label></p>\n";

	}
}

function bp_album_screen_edit_album_content() {

	global $bp,$albums_template;

	if (bp_album_has_albums()) :
		if(isset($bp->action_variables[0]))
		{
	   bp_album_the_album($bp->action_variables[0]);
	  }
	  else
		{
	   bp_album_the_album();
	  }
	$limit_info = bp_album_limits_info();

	if($bp->bp_album_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-phototag'),
			2 => __('Registered members','bp-phototag'),
			3 => __('Group Members','bp-phototag'),
			4 => __('Only friends','bp-phototag'),
			6 => __('Private','bp-phototag'),
			10 => __('Hidden (admin only)','bp-phototag')
		);
	}

	?>

	<h4><?php _e( 'Edit Gallery', 'bp-phototag' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-phototag-edit-album-form" id="bp-phototag-edit-album-form" class="standard-form">

    <!-- input type="hidden" name="action" value="add_album" /-->
    <input type="hidden" name="action" value="album_edit" />
    <input type="hidden" name="album_id" value="<?php bp_album_album_id() ?>" />

   <p>
		<label><?php _e('Gallery Name *', 'bp-phototag' ) ?><br />
		<input type="text" name="title" id="album-title" size="100" value="<?php
		echo (empty($_POST['title'])) ? bp_album_get_album_title() : wp_filter_kses($_POST['title']);
	?>"/></label>
    </p>
    <p>
	<label><?php _e('Gallery Description', 'bp-phototag' ) ?><br />
	<textarea name="description" id="album-description" rows="15"cols="40" ><?php
		echo (empty($_POST['description'])) ? bp_album_get_album_desc() : wp_filter_kses($_POST['description']);
	?></textarea></label>
    </p>
		<?php bp_album_screen_visibility(bp_album_get_group_id()); ?>
    
    <?php if(bp_is_active('activity') && $bp->album->bp_album_enable_comments ): ?>
    <p>
	<label><?php _e('Picture activity and comments','bp-phototag') ?></label>
			<label><input type="radio" name="enable_comments" value="1" checked="checked" /><?php _e('Enable','bp-phototag') ?></label>
			<label><input type="radio" name="enable_comments" value="0" /><?php _e('Disable','bp-phototag') ?></label>
			<?php _e('If picture already has comments this will delete them','bp-phototag') ?>
    </p>
    <?php endif; ?>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-phototag' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-phototag-edit-album' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( "Either this url is not valid or you can't edit this picture.", 'bp-phototag' ) ?></p>
	<?php endif;
}

/**
 * bp_album_action_edit_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_action_edit_album() {
    
	global $bp;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->album_slug == $bp->current_action && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  && isset( $_POST['submit'] )) 
	{
	
		check_admin_referer('bp-phototag-edit-album');
		
		$error_flag = false;
		$feedback_message = array();

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-phototag' );

		} else 
		{

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }
			$test = bp_album_get_picture_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			// For group privacy get the group_id
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
					$feedback_message[] = __( 'Please select a group for this gallery.', 'bp-phototag' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

		
			$uploadErrors = array(
				0 => __("There was no error, the file uploaded with success", 'bp-phototag'),
				1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
				2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_album_max_upload_size . "MB"),
				3 => __("The uploaded file was only partially uploaded", 'bp-phototag'),
				4 => __("No file was uploaded", 'bp-phototag'),
				6 => __("Missing a temporary folder", 'bp-phototag')
			);
		}
		if(bp_is_active('activity') && $bp->album->bp_album_enable_comments)
			if(!isset($_POST['enable_comments']) || ($_POST['enable_comments']!= 0 && $_POST['enable_comments']!= 1)){
				$error_flag = true;
				$feedback_message[] = __( 'Comments option is not correct.', 'bp-phototag' );
			}
		else
			$_POST['enable_comments']==0;
		if(!$error_flag)
		{  


			if( !isset($_POST['title']) ){
		    $error_flag = true;
		    $feedback_message[] = __('You need to specify an gallery name', 'bp-phototag');
			}
			else {
				$album_name = $_POST['title'];
			}
			if(!$error_flag)
			{
				if( !isset($_POST['description']) ){
					$album_desc = $album_name;
				}
				else {
					$album_desc = $_POST['description'];
				}
				if( !isset($_POST['album_id']) ){
			    $error_flag = true;
		    	$feedback_message[] = __('Unable to save album', 'bp-phototag');
				}
				else {
					$album_id = $_POST['album_id'];
				}
				if(!$error_flag)
				{
					$date_updated =  gmdate( "Y-m-d H:i:s" );
					$edit_result = bp_album_edit_album($album_id,$album_name,$album_desc,$priv_lvl,$date_updated,$_POST['enable_comments'],$group_id);
					if($edit_result)
					{
	
				    $feedback_message[] = __('Gallery '.$album_name.' updated successfully', 'bp-phototag');
					}
					else
					{
						$error_flag = true;
						$feedback_message[] = __('There were problems updating the gallery '.$album_name.'.', 'bp-phototag');
					}
				}
			}
			if ($error_flag){
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
			} 
			else {
					bp_album_record_album_activity($album,true);
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/'. $album_id.'/');
//				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
				die;
			}
		
		}
	}
}
add_action('bp_actions','bp_album_action_edit_album',3);
add_action('wp','bp_album_action_edit_album',3);


?>
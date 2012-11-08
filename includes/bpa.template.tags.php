<?php

/**
 * Example use in the template file:
 *
 * 	<?php if ( bp_album_has_pictures() ) : ?>
 *
 *		<?php while ( bp_album_has_pictures() ) : bp_album_the_picture(); ?>
 *
 *			<a href="<?php bp_album_picture_url() ?>">
 *				<img src='<?php bp_album_picture_thumb_url() ?>' />
 *			</a>
 *
 *		<?php endwhile; ?>
 *
 *	<?php else : ?>
 *
 *		<p class="error">No Pics!</p>
 *
 *	<?php endif; ?>
 */
class BP_Album_Template {
    
	var $current_picture = -1;
	var $picture_count = 0;
	var $pictures;
	var $picture;

	var $in_the_loop;

	var $pag_page;
	var $pag_per_page;
	var $pag_links;
	var $pag_links_global;
	var $album_id;
	
	function BP_Album_Template( $args = '' ) {
		$this->__construct( $args);
	}
	
	function __construct( $args = '' ) {
		global $bp;

		
		$defaults = bp_album_default_query_args();
		$r = apply_filters('bp_album_template_args',wp_parse_args( $args, $defaults ));

		extract( $r , EXTR_SKIP);

		$this->pag_page = $page;
		$this->pag_per_page = $per_page;
		$this->owner_id = $owner_id;
		$this->privacy= $privacy;
		$this->album_id = $album_id;

		$total = bp_album_get_picture_count($r);
		$this->pictures = bp_album_get_pictures($r);

		if ( !$max || $max >= $total )
			$this->total_picture_count = $total;
		else
			$this->total_picture_count = $max;

		if ( !$max || $max >= count($this->pictures))
			$this->picture_count = count($this->pictures);
		else
			$this->picture_count = $max;
		
		
		$this->pag_links_global = paginate_links( array(
			'base' => get_permalink() . '%#%',
			'format' => '?page=%#%',
			'total' => ceil( (int) $this->total_picture_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		    
		));
						
		$this->pag_links = paginate_links( array(
			'base' => $bp->displayed_user->domain . $bp->album->slug .'/'. $bp->album->pictures_slug .'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_picture_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
		
		if ($this->picture_count)
			$this->picture = $this->pictures[0];
		
	}
	
	function has_pictures() {
		if ( $this->current_picture + 1 < $this->picture_count ) {
			return true;
		} elseif ( $this->current_picture + 1 == $this->picture_count && $this->picture_count > 0) {
			do_action('bp_album_loop_end');

			$this->rewind_pictures();
		}

		$this->in_the_loop = false;
		return false;
	}

	function next_picture() {
		$this->current_picture++;
		$this->picture = $this->pictures[$this->current_picture];

		return $this->picture;
	}

	function rewind_pictures() {
		$this->current_picture = -1;
		if ( $this->picture_count > 0 ) {
			$this->picture = $this->pictures[0];
		}
	}

	function the_picture($id=0) {
		global $picture, $bp;

		if($id != 0) 
		{
			for($i=0; $i< $this->picture_count; $i++)
			{
				if($this->pictures[$i]->id == $id)
				{
					$this->current_picture = $i;
					$this->picture = $this->pictures[$i];
					break;
				}
			}
		}
		else
		{
	$this->in_the_loop = true;
		$this->picture = $this->next_picture();

		if ( 0 == $this->current_picture )
			do_action('bp_album_loop_start');
		}
	}
	
	function has_next_pic(){
		if (!isset($this->picture->next_pic))
		{
			$pic_args = array(
			'id' => $this->picture->id);

			$this->picture->next_pic = bp_album_get_next_picture($pic_args);
		}
		if (isset($this->picture->next_pic) && $this->picture->next_pic !== false)
			return true;
		if (isset($this->picture->next_pic) && $this->picture->next_pic === false)
			return false;
		
	}
	function has_prev_pic(){
	if (!isset($this->picture->prev_pic))
	{
					$pic_args = array('id' => $this->picture->id);
			$this->picture->prev_pic = bp_album_get_prev_picture($pic_args);
	}
		if (isset($this->picture->prev_pic) && $this->picture->prev_pic !== false)
			return true;
		if (isset($this->picture->prev_pic) && $this->picture->prev_pic === false)
			return false;
	}
} //** End class BP_Album_Template

function bp_album_query_pictures( $args = '' ) {
    
	global $pictures_template;

	$pictures_template = new BP_Album_Template( $args );

	return $pictures_template->has_pictures();
}

function bp_album_the_picture($id = 0) {
    
	global $pictures_template;
	return $pictures_template->the_picture($id);
}

/**
 * bp_album_has_pictures()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_pictures() {
    
	global $pictures_template;
	return $pictures_template->has_pictures();
}

/**
 * bp_album_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_title() {
	echo bp_album_get_picture_title();
}
	function bp_album_get_picture_title() {
	    
		global $pictures_template;
		return apply_filters( 'bp_album_get_picture_title', $pictures_template->picture->title);
	}

/**
 * bp_album_picture_title_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_title_truncate($length = 11) {
	echo bp_album_get_picture_title_truncate($length);
}	
	function bp_album_get_picture_title_truncate($length) {

		global $pictures_template;

		$title = $pictures_template->picture->title;

		$title = apply_filters( 'bp_album_get_picture_title_truncate', $title);

		$r = wp_specialchars_decode($title, ENT_QUOTES);


		if ( function_exists('mb_strlen') && strlen($r) > mb_strlen($r) ) {

			$length = round($length / 2);
		}

		if ( function_exists( 'mb_substr' ) ) {


			$r = mb_substr($r, 0, $length);
		}
		else {
			$r = substr($r, 0, $length);
		}

		$result = _wp_specialchars($r) . '&#8230;';

		return $result;
		
	}
/**
 * bp_album_picture_picture_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_album_title() {
	echo bp_album_get_picture_album_title();
}
	function bp_album_get_picture_album_title() {
	    
		global $bp, $wpdb, $pictures_template;
		$sql = $wpdb->prepare( "SELECT title FROM {$bp->album->albums_table_name} WHERE id = %d", $pictures_template->picture->album_id );
		$title = $wpdb->get_var( $sql );

		return apply_filters( 'bp_album_get_picture_title', $title);
	}
/**
 * bp_album_picture_picture_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_album_desc() {
	echo bp_album_get_picture_album_desc();
}
	function bp_album_get_picture_album_desc() {
	    
		global $bp, $wpdb, $pictures_template;
		$sql = $wpdb->prepare( "SELECT description FROM {$bp->album->albums_table_name} WHERE id = %d", $pictures_template->picture->album_id );
		$title = $wpdb->get_var( $sql );

		return apply_filters( 'bp_album_get_picture_title', $title);
	}

/**
 * bp_album_picture_desc()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_desc() {
	echo bp_album_get_picture_desc();
}
	function bp_album_get_picture_desc() {
	    
		global $pictures_template;
		
		return apply_filters( 'bp_album_get_picture_desc', $pictures_template->picture->description );
	}
	
/**
 * bp_album_picture_desc_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_desc_truncate($words=55) {
	echo bp_album_get_picture_desc_truncate($words);
}
	function bp_album_get_picture_desc_truncate($words=55) {
	    
		global $pictures_template;
		
		$exc = bp_create_excerpt($pictures_template->picture->description, $words, true) ;
		
		return apply_filters( 'bp_album_get_picture_desc_truncate', $exc, $pictures_template->picture->description, $words );
	}

/**
 * bp_album_picture_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_id() {
	echo bp_album_get_picture_id();
}
	function bp_album_get_picture_id() {
	    
		global $pictures_template;
		
		return apply_filters( 'bp_album_get_picture_id', $pictures_template->picture->id );
	}
/**
 * bp_album_picture_picture_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_album_id() {
	echo bp_album_get_picture_album_id();
}
	function bp_album_get_picture_album_id() {
	    
		global $bp, $wpdb, $pictures_template;

		return apply_filters( 'bp_album_get_picture_id', $pictures_template->picture->album_id);
	}

/**
 * bp_album_picture_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_url() {
	echo bp_album_get_picture_url();
}
	function bp_album_get_picture_url() {
	    
		global $bp,$pictures_template;

		$owner_domain = bp_core_get_user_domain($pictures_template->picture->owner_id);
//		return apply_filters( 'bp_album_get_picture_url', $owner_domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pictures_template->picture->id  . '/');
	  return apply_filters( 'bp_album_get_picture_thumb_url', bp_get_root_domain().$pictures_template->picture->pic_org_url );
	}

/**
 * bp_album_picture_edit_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_edit_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_album_get_picture_edit_url().'" class="picture-edit">'.__('Edit picture','bp-phototag').'</a>';
}

/**
 * bp_album_picture_edit_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_edit_url() {
	echo bp_album_get_picture_edit_url();
}
	function bp_album_get_picture_edit_url() {
	    
		global $bp,$pictures_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_album_get_picture_edit_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$pictures_template->picture->id.'/'.$bp->album->edit_slug),'bp-phototag-edit-pic');
	}
/**
 * bp_album_picture_edit_url_stub()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_edit_url_stub() {
	echo bp_album_get_picture_edit_url_stub();
}
	function bp_album_get_picture_edit_url_stub() {
	    
		global $bp,$pictures_template;
		
		if (bp_is_my_profile() || is_super_admin())
		{
//			return wp_nonce_url(apply_filters( 'bp_album_get_picture_edit_url_stub', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$pictures_template->picture->id.'/'.$bp->album->edit_slug),'bp-phototag-edit-pic');
			return wp_nonce_url(apply_filters( 'bp_album_get_picture_edit_url_stub', $bp->album->single_edit_slug.'/'.$pictures_template->picture->id.'/'.$bp->album->edit_slug).'/'.$pictures_template->picture->album_id,'bp-phototag-edit-pic');
//				return wp_nonce_url(apply_filters( 'bp_album_get_album_edit_url_stub',$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-phototag-edit-album');
		}
}
/**
 * bp_album_album_priv()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_priv() {
	echo bp_album_get_picture_priv();
}
	function bp_album_get_picture_priv() {
	    
		global $pictures_template;
		
		return apply_filters( 'bp_album_get_album_priv', $pictures_template->picture->privacy );
	}

/**
 * bp_album_picture_delete_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_delete_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_album_get_picture_delete_url().'" class="picture-delete">'.__('Delete picture','bp-phototag').'</a>';
}

/**
 * bp_album_picture_delete_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_delete_url() {
	echo bp_album_get_picture_delete_url();
}
	function bp_album_get_picture_delete_url() {
	    
		global $bp,$pictures_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_album_get_picture_delete_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$pictures_template->picture->id.'/'.$bp->album->delete_slug ),'bp-phototag-delete-pic');
	}

/**
 * bp_album_picture_original_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_original_url() {
	echo bp_album_get_picture_original_url();
}
	function bp_album_get_picture_original_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $pictures_template->picture->pic_org_url, strrpos($pictures_template->picture->pic_org_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_original_url', bp_get_root_domain().$pictures_template->picture->pic_org_url );
		}
		
	}

/**
 * bp_album_picture_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_middle_url() {
	echo bp_album_get_picture_middle_url();
}
	function bp_album_get_picture_middle_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $pictures_template->picture->pic_mid_url, strrpos($pictures_template->picture->pic_mid_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_middle_url', bp_get_root_domain().$pictures_template->picture->pic_mid_url );
		}
	}

/**
 * bp_album_picture_thumb_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_thumb_url() {
	echo bp_album_get_picture_thumb_url();
}
	function bp_album_get_picture_thumb_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){
		    $filename = substr( $pictures_template->picture->pic_thumb_url, strrpos($pictures_template->picture->pic_thumb_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_thumb_url', bp_get_root_domain().$pictures_template->picture->pic_thumb_url );
		}
	}

/**
 * bp_album_total_picture_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_total_picture_count() {
	echo bp_album_get_total_picture_count();
}
	function bp_album_get_total_picture_count() {
	    
		global $pictures_template;
		
		return apply_filters( 'bp_album_get_total_picture_count', $pictures_template->total_picture_count );
	}

/**
 * bp_album_picture_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_pagination($always_show = false) {
	echo bp_album_get_picture_pagination($always_show);
}
	function bp_album_get_picture_pagination($always_show = false) {
	    
		global $pictures_template;
		
		if ($always_show || $pictures_template->total_picture_count > $pictures_template->pag_per_page)
		return apply_filters( 'bp_album_get_picture_pagination', $pictures_template->pag_links );
	}

/**
 * bp_album_picture_pagination_global()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_picture_pagination_global($always_show = false) {
	echo bp_album_get_picture_pagination_global($always_show);
}
	function bp_album_get_picture_pagination_global($always_show = false) {
	    
		global $pictures_template;
		
		if ($always_show || $pictures_template->total_picture_count > $pictures_template->pag_per_page)
		return apply_filters( 'bp_album_get_picture_pagination_global', $pictures_template->pag_links_global );
	}

/**
 * bp_album_adjacent_links()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_album_adjacent_links() {
	echo bp_album_get_adjacent_links();
}
	function bp_album_get_adjacent_links() {
	    
		global $pictures_template;
		
		if ($pictures_template->has_prev_pic() || $pictures_template->has_next_pic())
			return bp_album_get_prev_picture_or_album_link().' '.bp_album_get_next_picture_or_album_link();
		else
			return '<a href="'.bp_album_get_pictures_url().'" class="picture-album-link picture-no-adjacent-link"><span>'.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_album_next_picture_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_picture_link($text = ' &raquo;', $title = true) {
	echo bp_album_get_next_picture_link($text, $title);
}
	function bp_album_get_next_picture_link($text = ' &raquo;', $title = true) {
	    
		global $pictures_template;
		
		if ($pictures_template->has_next_pic()){
			$text = ( ($title)?bp_album_get_next_picture_title():'' ).$text;
			return '<a href="'.bp_album_get_next_picture_url().'" class="picture-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return null;
	}

/**
 * bp_album_next_picture_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_picture_or_album_link($text = ' &raquo;', $title = true) {
	echo bp_album_get_next_picture_or_album_link($text, $title);
}
	function bp_album_get_next_picture_or_album_link($text = ' &raquo;', $title = true) {
	    
		global $pictures_template;
		
		if ($pictures_template->has_next_pic()){
			$text = ( ($title)?bp_album_get_next_picture_title():'' ).$text;
			return '<a href="'.bp_album_get_next_picture_url().'" class="picture-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return '<a href="'.bp_album_get_pictures_url().'" class="picture-album-link picture-next-link"> <span> '.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span></a>';
	}

/**
 * bp_album_next_picture_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_picture_url() {
	echo bp_album_get_next_picture_url();
}
	function bp_album_get_next_picture_url() {
	    
		global $bp,$pictures_template;
		
		if ($pictures_template->has_next_pic())
			return apply_filters( 'bp_album_get_next_picture_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_edit_slug.'/'.$pictures_template->picture->next_pic->id  . '/');
	}

/**
 * bp_album_next_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_picture_title() {
	echo bp_album_get_next_picture_title();
}
	function bp_album_get_next_picture_title() {
	    
		global $pictures_template;
		
		if ($pictures_template->has_next_pic())
			return apply_filters( 'bp_album_get_picture_title', $pictures_template->picture->next_pic->title );
	}
	
/**
 * bp_album_has_next_picture()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_next_picture() {
    
	global $bp,$pictures_template;
	
	return $pictures_template->has_next_pic();
}

/**
 * bp_album_prev_picture_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_picture_link($text = '&laquo; ', $title = true) {
	echo bp_album_get_prev_picture_link($text, $title);
}
	function bp_album_get_prev_picture_link($text = '&laquo; ', $title = true) {
	    
		global $pictures_template;
		
		if ($pictures_template->has_prev_pic()){
			$text .= ($title)?bp_album_get_prev_picture_title():'';
			return '<a href="'.bp_album_get_prev_picture_url().'" class="picture-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return null;
	}

/**
 * bp_album_prev_picture_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_picture_or_album_link($text = '&laquo; ', $title = true) {
	echo bp_album_get_prev_picture_or_album_link($text, $title);
}
	function bp_album_get_prev_picture_or_album_link($text = '&laquo; ', $title = true) {
	    
		global $pictures_template;
		if ($pictures_template->has_prev_pic()){
			$text .= ($title)?bp_album_get_prev_picture_title():'';
			return '<a href="'.bp_album_get_prev_picture_url().'" class="picture-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return '<a href="'.bp_album_get_pictures_url().'" class="picture-album-link picture-prev-link"><span> '.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_album_prev_picture_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_picture_url() {
	echo bp_album_get_prev_picture_url();
}
	function bp_album_get_prev_picture_url() {
	    
		global $bp,$pictures_template;
		
		if ($pictures_template->has_prev_pic())
			return apply_filters( 'bp_album_get_prev_picture_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_edit_slug.'/'.$pictures_template->picture->prev_pic->id . '/');
	}

/**
 * bp_album_prev_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_picture_title() {
	echo bp_album_get_prev_picture_title();
}
	function bp_album_get_prev_picture_title() {
	    
		global $pictures_template;
		
		if ($pictures_template->has_prev_pic())
			return apply_filters( 'bp_album_get_picture_title', $pictures_template->picture->prev_pic->title );
	}
	
/**
 * bp_album_has_prev_picture()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_prev_picture() {
    
	global $pictures_template;
	
	return $pictures_template->has_prev_pic();
}

/**
 * bp_album_pictures_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_pictures_url() {
    
	echo bp_album_get_pictures_url();
	
}
	function bp_album_get_pictures_url() {
	    
		global $bp;
			return apply_filters( 'bp_album_get_pictures_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->pictures_slug . '/');
	}

/**
 * bp_album_picture_has_activity()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_album_picture_has_activity(){

	global $bp,$pictures_template;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}

	return bp_has_activities( array('object'=> $bp->album->id,'primary_id'=>$pictures_template->picture->id , 'show_hidden' => true) );
}

/**
 * bp_album_comments_enabled()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_comments_enabled() {
    
        global $bp;

        return $bp->album->bp_album_enable_comments;
	}

class BP_Album_Album_Template {
    
	var $current_album = -1;
	var $album_count = 0;
	var $albums;
	var $album;

	var $in_the_loop;

	var $pag_page;
	var $pag_per_page;
	var $pag_links;
	var $pag_links_global;

	
	function BP_Album_Album_Template( $args = '' ) {
		$this->__construct( $args);
	}
	
	function __construct( $args = '' ) {
		global $bp;

	
		$defaults = bp_album_default_query_args();
		
		$r = apply_filters('bp_album_template_args',wp_parse_args( $args, $defaults ));
		extract( $r , EXTR_SKIP);

		$this->pag_page = $page;
		$this->pag_per_page = $per_page;
		if((isset($all_albums)) && ($all_albums))
		{
			$this->owner_id = false;
		}
		else
		{
			$this->owner_id = $owner_id;
		} 
		$this->privacy= $privacy;

		$total = bp_album_get_album_count($r);
		$this->albums = bp_album_get_albums($r);

		if ( !$max || $max >= $total )
			$this->total_album_count = $total;
		else
			$this->total_album_count = $max;

		if ( !$max || $max >= count($this->albums))
			$this->album_count = count($this->albums);
		else
			$this->album_count = $max;
		
		
		$this->pag_links_global = paginate_links( array(
			'base' => get_permalink() . '%#%',
			'format' => '?page=%#%',
			'total' => ceil( (int) $this->total_album_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		    
		));
						
		$this->pag_links = paginate_links( array(
			'base' => $bp->displayed_user->domain . $bp->album->slug .'/'. $bp->album->album_slug .'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_album_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
		
		if ($this->album_count)
			$this->album = $this->albums[0];
		
	}
	
	function has_albums() {
		if ( $this->current_album + 1 < $this->album_count ) {
			return true;
		} elseif ( $this->current_album + 1 == $this->album_count && $this->album_count > 0) {
			do_action('bp_album_loop_end');

			$this->rewind_albums();
		}

		$this->in_the_loop = false;
		return false;
	}

	function next_album() {
		$this->current_album++;
		$this->album = $this->albums[$this->current_album];

		return $this->album;
	}

	function rewind_albums() {
		$this->current_album = -1;
		if ( $this->album_count > 0 ) {
			$this->album = $this->albums[0];
		}
	}

	function the_album($id=0) {
		global $picture, $bp;
		if($id != 0) 
		{
			$this->current_album = $id;
			for($i=0; $i< $this->album_count; $i++)
			{
				if($this->albums[$i]->id == $id)
				{
					$this->album = $this->albums[$i];
					break;
				}
			}
		}
		else
		{
			$this->in_the_loop = true;
			$this->album = $this->next_album();
			if ( 0 == $this->current_album )
				do_action('bp_album_loop_start');
		}
	}
	
	function has_next_album(){
		if (!isset($this->album->next_album))
			$this->album->next_album = bp_album_get_next_album();
		if (isset($this->album->next_album) && $this->album->next_album !== false)
			return true;
		if (isset($this->album->next_album) && $this->album->next_album === false)
			return false;
		
	}
	function has_prev_album(){
		if (!isset($this->album->prev_album))
			$this->picture->prev_album = bp_album_get_prev_album();
		if (isset($this->album->prev_album) && $this->album->prev_album !== false)
			return true;
		if (isset($this->album->prev_album) && $this->album->prev_album === false)
			return false;
	}
} // END class BP_Album_Album_Template

function bp_album_query_albums( $args = '' ) {
    
	global $albums_template;

	$albums_template = new BP_Album_Album_Template( $args );

	return $albums_template->has_albums();
}

function bp_album_the_album($id = 0) {
    
	global $albums_template;
	return $albums_template->the_album($id);
}

/**
 * bp_album_has_albums()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_albums() {
    
	global $albums_template;
	return $albums_template->has_albums();
}

/**
 * bp_album_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_title() {
	echo bp_album_get_album_title();
}
	function bp_album_get_album_title() {
	    
		global $albums_template;
		return apply_filters( 'bp_album_get_picture_title', $albums_template->album->title);
	}

/**
 * bp_album_album_owner_user_name()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_owner_user_name() {
	echo bp_album_album_get_owner_user_name();
}
function bp_album_album_get_owner_user_name($user_id)
{
	global $bp,$albums_template;
		return apply_filters( 'bp_album_get_picture_title', bp_core_get_username( $albums_template->album->owner_id ));
}

 function bp_album_album_get_owner_profile_link() {
       echo bp_album_album_owner_profile_link();
 }
function bp_album_album_owner_profile_link() {
   global $bp, $albums_template;
   
   return apply_filters( 'bp_get_member_permalink', bp_core_get_userlink( $albums_template->album->owner_id, false, false, true ) );
}

/**
 * bp_album_picture_title_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_title_truncate($length = 11) {
	echo bp_album_get_album_title_truncate($length);
}	
	function bp_album_get_album_title_truncate($length) {

		global $albums_template;

		$title = $albums_template->album->title;

		$title = apply_filters( 'bp_album_get_picture_title_truncate', $title);

		$r = wp_specialchars_decode($title, ENT_QUOTES);


		if ( function_exists('mb_strlen') && strlen($r) > mb_strlen($r) ) {

			$length = round($length / 2);
		}

		if ( function_exists( 'mb_substr' ) ) {


			$r = mb_substr($r, 0, $length);
		}
		else {
			$r = substr($r, 0, $length);
		}

		$result = _wp_specialchars($r) . '&#8230;';

		return $result;
		
	}

/**
 * bp_album_picture_desc()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_desc() {
	echo bp_album_get_album_desc();
}
	function bp_album_get_album_desc() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_album_get_album_desc', $albums_template->album->description );
	}
	
/**
 * bp_album_picture_desc_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_desc_truncate($words=55) {
	echo bp_album_get_album_desc_truncate($words);
}
	function bp_album_get_album_desc_truncate($words=55) {
	    
		global $albums_template;
		
		$exc = bp_create_excerpt($pictures_template->album->description, $words, true) ;
		
		return apply_filters( 'bp_album_get_picture_desc_truncate', $exc, $albums_template->album->description, $words );
	}

/**
 * bp_album_album_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_id() {
	echo bp_album_get_album_id();
}
	function bp_album_get_album_id() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_album_get_album_id', $albums_template->album->id );
	}

/**
 * bp_album_album_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_url() {
	echo bp_album_get_album_url();
}
	function bp_album_get_album_url() {
	    
		global $bp,$albums_template;

		$owner_domain = bp_core_get_user_domain($albums_template->album->owner_id);
//		return apply_filters( 'bp_album_get_picture_url', $owner_domain . $bp->album->slug . '/'.$bp->album->album_slug.'/'.$albums_template->album->id  . '/');
		return apply_filters( 'bp_album_get_picture_url', $owner_domain . $bp->album->slug . '/?album_id='.$albums_template->album->id);
	}

/**
 * bp_album_album_edit_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_edit_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_album_get_album_edit_url().'" class="album-edit">'.__('Edit album','bp-phototag').'</a>';
}

/**
 * bp_album_picture_edit_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_edit_url() {
	echo bp_album_get_album_edit_url();
}
	function bp_album_get_album_edit_url() {
	    
		global $bp,$albums_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_album_get_album_edit_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->album_slug.'/'.$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-phototag-edit-album');
	}
function bp_album_album_edit_url_stub() {
	echo bp_album_get_album_edit_url_stub();
}
	function bp_album_get_album_edit_url_stub() {
	    
		global $bp,$albums_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_album_get_album_edit_url_stub',$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-phototag-edit-album');
	}
/**
 * bp_album_album_priv()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_priv() {
	echo bp_album_get_album_priv();
}
	function bp_album_get_album_priv() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_album_get_album_priv', $albums_template->album->privacy );
	}
/**
 * bp_album_album_priv_info()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_priv_info() {
	echo bp_album_get_album_priv_info();
}
	function bp_album_get_album_priv_info() {
	    
		global $albums_template;
	
		$loc_priv = $albums_template->album->privacy;
		switch($loc_priv)
		{
			case 0: $loc_priv_txt = 'Public'; break;
			case 2: $loc_priv_txt = 'Registered members'; break;
			case 3: $loc_priv_txt = 'Group Members';
/*					$groups_array = BP_Groups_Member::get_is_admin_of($albums_template->album->owner_id);
					$group_count = $groups_array['total'];
 					foreach( $groups_array['groups'] as $group)
					{
						if(( $albums_template->album->group_id > 0) && ($group->id ==  $albums_template->album->group_id))
						{
							$loc_priv_txt = 'Members of Group "'.$group->name.'"';
						}
					} */
					break;
			case 4: $loc_priv_txt = 'Only friends'; break;
			case 6: $loc_priv_txt = 'Private'; break;
			default : $loc_priv_txt = $loc_priv;break;
		}
		return apply_filters( 'bp_album_get_album_priv', $loc_priv_txt );
	}
	
/**
 * bp_album_album_group_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_group_id() {
	echo bp_album_get_group_id();
}
	function bp_album_get_group_id() {
	    
		global $albums_template;
		return apply_filters( 'bp_album_get_group_id', $albums_template->album->group_id );
	}


/**
 * bp_album_album_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
//function bp_album_album_id() {
//	echo bp_album_get_album_id();
//}
//	function bp_album_get_album_id() {
	    
//		global $albums_template;
		
//		return apply_filters( 'bp_album_get_album_priv', $albums_template->album->id );
//	}


/**
 * bp_album_picture_delete_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*function bp_album_picture_delete_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_album_get_picture_delete_url().'" class="picture-delete">'.__('Delete picture','bp-phototag').'</a>';
}
*/
/**
 * bp_album_picture_delete_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
 /*
function bp_album_picture_delete_url() {
	echo bp_album_get_picture_delete_url();
}
	function bp_album_get_picture_delete_url() {
	    
		global $bp,$pictures_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_album_get_picture_delete_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$pictures_template->picture->id.'/'.$bp->album->delete_slug ),'bp-phototag-delete-pic');
	}
*/
/**
 * bp_album_picture_original_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*function bp_album_picture_original_url() {
	echo bp_album_get_picture_original_url();
}
	function bp_album_get_picture_original_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $pictures_template->picture->pic_org_url, strrpos($pictures_template->picture->pic_org_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_original_url', bp_get_root_domain().$pictures_template->picture->pic_org_url );
		}
		
	}
*/
/**
 * bp_album_picture_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*
function bp_album_picture_middle_url() {
	echo bp_album_get_picture_middle_url();
}
	function bp_album_get_picture_middle_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $pictures_template->picture->pic_mid_url, strrpos($pictures_template->picture->pic_mid_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_middle_url', bp_get_root_domain().$pictures_template->picture->pic_mid_url );
		}
	}
*/
/**
 * bp_album_picture_thumb_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
 /*
function bp_album_picture_thumb_url() {
	echo bp_album_get_picture_thumb_url();
}
	function bp_album_get_picture_thumb_url() {

		global $bp, $pictures_template;

		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $pictures_template->picture->pic_thumb_url, strrpos($pictures_template->picture->pic_thumb_url, '/') + 1 );
		    $owner_id = $pictures_template->picture->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_picture_thumb_url', bp_get_root_domain().$pictures_template->picture->pic_thumb_url );
		}
	}
*/
/**
 * bp_album_picture_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */

function bp_album_album_feature_url() {
	echo bp_album_get_album_feature_url();
}
	function bp_album_get_album_feature_url() {

		global $bp, $albums_template;


		if( $albums_template->album->feature_image == 0)
		{
			// No feature image has been set;
			return plugins_url( 'includes/images/No-Image-Available.gif' , dirname(__FILE__) );
		}
		if($bp->album->bp_album_url_remap == true){

		    $filename = substr( $albums_template->album->feature_image_path, strrpos($albums_template->album->feature_image_path, '/') + 1 );
		    $owner_id = $albums_template->album->owner_id;
		    $result = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_album_get_album_feature_url', bp_get_root_domain().$albums_template->album->feature_image_path );
		}
	}
/**
 * bp_album_total_album_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_total_album_count() {
	echo bp_album_get_total_album_count();
}
	function bp_album_get_album_album_count() {
	    
		global $albums_template;
			
		return apply_filters( 'bp_album_get_total_picture_count', $albums_template->total_album_count );
	}

/**
 * bp_album_album_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_pagination($always_show = false) {
	echo bp_album_get_album_pagination($always_show);
}
	function bp_album_get_album_pagination($always_show = false) {
	    
		global $albums_template;
		
		if ($always_show || $albums_template->total_picture_count > $albums_template->pag_per_page)
		return apply_filters( 'bp_album_get_picture_pagination', $albums_template->pag_links );
	}

/**
 * bp_album_picture_pagination_global()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_pagination_global($always_show = false) {
	echo bp_album_get_album_pagination_global($always_show);
}
	function bp_album_get_album_pagination_global($always_show = false) {
	    
		global $albums_template;
		
		if ($always_show || $albums_template->total_picture_count > $albums_template->pag_per_page)
		return apply_filters( 'bp_album_get_picture_pagination_global', $albums_template->pag_links_global );
	}

/**
 * bp_album_adjacent_links()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_album_album_adjacent_links() {
	echo bp_album_album_get_adjacent_links();
}
	function bp_album_album_get_adjacent_links() {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album() || $albums_template->has_next_album())
			return bp_album_get_prev_album_or_album_link().' '.bp_album_get_next_album_or_album_link();
		else
			return '<a href="'.bp_album_get_album_url().'" class="picture-album-link picture-no-adjacent-link"><span>'.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_album_next_picture_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_album_link($text = ' &raquo;', $title = true) {
	echo bp_album_get_next_album_link($text, $title);
}
	function bp_album_get_next_album_link($text = ' &raquo;', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_next_album()){
			$text = ( ($title)?bp_album_get_next_album_title():'' ).$text;
			return '<a href="'.bp_album_get_next_album_url().'" class="picture-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return null;
	}

/**
 * bp_album_next_picture_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_album_or_album_link($text = ' &raquo;', $title = true) {
	echo bp_album_get_next_album_or_album_link($text, $title);
}
	function bp_album_get_next_album_or_album_link($text = ' &raquo;', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_next_pic()){
			$text = ( ($title)?bp_album_get_next_album_title():'' ).$text;
			return '<a href="'.bp_album_get_next_album_url().'" class="picture-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return '<a href="'.bp_album_get_album_url().'" class="picture-album-link picture-next-link"> <span> '.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span></a>';
	}

/**
 * bp_album_next_picture_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_album_url() {
	echo bp_album_get_next_album_url();
}
	function bp_album_get_next_album_url() {
	    
		global $bp,$albums_template;
		
		if ($albums_template->has_next_album())
			return apply_filters( 'bp_album_get_next_picture_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$albums_template->album->next_album->id  . '/');
	}

/**
 * bp_album_next_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_next_album_title() {
	echo bp_album_get_next_album_title();
}
	function bp_album_get_next_album_title() {
	    
		global $albums_template;
		
		if ($albums_template->has_next_album())
			return apply_filters( 'bp_album_get_picture_title', $albums_template->album->next_album->title );
	}
	
/**
 * bp_album_has_next_album()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_next_album() {
    
	global $bp,$albums_template;
	
	return $albums_template->has_next_album();
}

/**
 * bp_album_prev_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_album_link($text = '&laquo; ', $title = true) {
	echo bp_album_get_prev_album_link($text, $title);
}
	function bp_album_get_prev_album_link($text = '&laquo; ', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album()){
			$text .= ($title)?bp_album_get_prev_album_title():'';
			return '<a href="'.bp_album_get_prev_album_url().'" class="picture-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return null;
	}

/**
 * bp_album_prev_album_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_album_or_album_link($text = '&laquo; ', $title = true) {
	echo bp_album_get_prev_album_or_album_link($text, $title);
}
	function bp_album_get_prev_album_or_album_link($text = '&laquo; ', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album()){
			$text .= ($title)?bp_album_get_prev_album_title():'';
			return '<a href="'.bp_album_get_prev_album_url().'" class="picture-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return '<a href="'.bp_album_get_album_url().'" class="picture-album-link picture-prev-link"><span> '.bp_word_or_name( __( "Return to your album", 'bp-phototag' ), __( "Return to %s album", 'bp-phototag' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_album_prev_picture_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_album_url() {
	echo bp_album_get_prev_album_url();
}
	function bp_album_get_prev_album_url() {
	    
		global $bp,$albums_template;
		
		if ($albums_template->has_prev_pic())
			return apply_filters( 'bp_album_get_prev_picture_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$albums_template->album->prev_album->id . '/');
	}

/**
 * bp_album_prev_picture_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_prev_album_title() {
	echo bp_album_get_prev_album_title();
}
	function bp_album_get_prev_album_title() {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album())
			return apply_filters( 'bp_album_get_picture_title', $albums_template->album->prev_album->title );
	}
	
/**
 * bp_album_has_prev_picture()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_has_prev_album() {
    
	global $albums_template;
	
	return $albums_template->has_prev_album();
}

/**
 * bp_album_pictures_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
//function bp_album_album_url() {
    
//	echo bp_album_get_album_url();
	
//}
//	function bp_album_get_album_url() {
	    
//		global $bp;
//			return apply_filters( 'bp_album_get_pictures_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->album_slug . '/');
//	}

/**
 * bp_album_picture_has_activity()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_album_album_has_activity(){

	global $bp,$albums_template;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}
	$returnValue = bp_has_activities( array('object'=> $bp->album->id,'primary_id'=>$albums_template->album->id , 'show_hidden' => true) );
	return $returnValue;
}

/**
 * bp_album_album_comments_enabled()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_album_comments_enabled() {
    
        global $bp;

        return $bp->album->bp_album_enable_comments;
	
}
function bp_album_album_time() {
	echo bp_album_get_album_time();
}
	function bp_album_get_album_time() {
	    
		global $albums_template;
		
		if($albums_template->album->date_updated > $albums_template->album->date_created)
		{
			return apply_filters( 'bp_album_get_picture_title','Updated '.bp_core_time_since( strtotime( $albums_template->album->date_updated ) ));
		}
		else
		{
			return apply_filters( 'bp_album_get_picture_title','Created '.bp_core_time_since( strtotime( $albums_template->album->date_created ) ));
		}
	}
function time_elapsed_string($ptime) {
    $etime = time() - $ptime;
    
    if ($etime < 1) {
        return '0 seconds';
    }
    
    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );
    
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '');
        }
    }
}
function bp_album_album_created_time() {
	echo bp_album_get_album_created_time();
}
	function bp_album_get_album_created_time() {
	    
		global $albums_template;
		
			return apply_filters( 'bp_album_get_picture_title',time_elapsed_string( strtotime( $albums_template->album->date_created )).' ago');
//			return apply_filters( 'bp_album_get_picture_title',bp_core_time_since( strtotime( $albums_template->album->date_created ) ));
	}
function bp_album_album_updated_time() {
	echo bp_album_get_album_updated_time();
}
	function bp_album_get_album_updated_time() {
	    
		global $albums_template;
		
			return apply_filters( 'bp_album_get_picture_title',bp_core_time_since( strtotime( $albums_template->album->date_updated ) ));
	}
/**
 * bp_album_total_album_image_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_total_album_image_count() {
	echo bp_album_get_total_album_image_count();
}
	function bp_album_get_total_album_image_count() {
	    
		global $albums_template, $bp, $wpdb;
		
				$sql =  $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->album->table_name} WHERE album_id= %d",$albums_template->album->id) ;
				$result = $wpdb->get_var( $sql );
				return apply_filters( 'bp_album_total_album_image_count', $result );
	}
?>
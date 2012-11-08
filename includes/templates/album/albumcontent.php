<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">
				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>

					<?php if ( bp_album_has_pictures() ) : ?>
					
				<div class="picture-pagination">
					<?php bp_album_picture_pagination(); ?>	
				</div>			
				<table "width=100%">
					<tr>
						<td width="70%">
							<h1><?php bp_album_picture_album_title()?></h1>
						</td>
						<td width="30%">
							<?php 	bp_album_like_button( bp_album_get_album_id(), 'album' ); ?> Like this gallery
						</td>
					</tr>
				</table>
								
				<p><?php bp_album_picture_album_desc()?></p>
					
				<div class="picture-gallery">												
						<?php while ( bp_album_has_pictures() ) : bp_album_the_picture(); ?>

				<div class="picture-thumb-box">
	
	                <a href="<?php bp_album_picture_url() ?>" class="thickbox"><img src='<?php bp_album_picture_thumb_url() ?>' /></a>
	                <!-- a href="<?php bp_album_picture_url() ?>"  class="picture-title"><?php bp_album_picture_title_truncate() ?></a -->	
	                <?php if (bp_is_my_profile() || is_super_admin()) : ?>
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; image actions &raquo;
												</option>
												<option value="window.location = '<?php bp_album_picture_edit_url_stub()?>'">
													Edit Image
												</option>
												<option value="BPAFeatureImage(<?php bp_album_album_id() ?>,'<?php bp_album_album_title()?>',<?php bp_album_picture_id() ?>)">
													Feature Image
												</option>
												<option value="BPADeleteImage(<?php bp_album_picture_id() ?>)">
													Delete Image
												</option>
											</select>
										</div>

									<?php endif; ?>		
				</div>
					
						<?php endwhile; ?>
				</div>					
					<?php else : ?>
					
				<div id="message" class="info">
					<p><?php echo bp_word_or_name( __('No pics here, show something to the community!', 'bp-phototag' ), __( "Either %s hasn't uploaded any picture yet or they have restricted access", 'bp-phototag' )  ,false,false) ?></p>
				</div>
				
				<?php endif; ?>

			</div><!-- #item-body -->
				<?php bp_album_load_subtemplate( apply_filters( 'bp_album_template_screen_comments', 'album/albumcomments' ) ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>

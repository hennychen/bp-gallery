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

					<?php if ( bp_gallplus_has_images() ) : ?>
					
				<div class="image-pagination">
					<?php bp_gallplus_image_pagination(); ?>	
				</div>			
					
				<div class="image-gallery">												
						<?php while ( bp_gallplus_has_images() ) : bp_gallplus_the_image(); ?>

				<div class="image-thumb-box">
<!-- In Image.php -->	
	                <a href="<?php bp_gallplus_image_url() ?>" class="thickbox"><img src='<?php bp_gallplus_image_thumb_url() ?>' /></a>
	                <!-- a href="<?php bp_gallplus_image_url() ?>"  class="image-title"><?php bp_gallplus_image_title_truncate() ?></a -->	
				</div>
					
						<?php endwhile; ?>
				</div>					
					<?php else : ?>
					
				<div id="message" class="info">
					<p><?php echo bp_word_or_name( __('No pics here, show something to the community!', 'bp-galleries-plus' ), __( "Either %s hasn't uploaded any image yet or they have restricted access", 'bp-galleries-plus' )  ,false,false) ?></p>
				</div>
				
				<?php endif; ?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
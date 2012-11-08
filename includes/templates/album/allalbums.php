

			<div id="item-body">
				<h1> Latest Galleries </h1>
<?php 		$album_args = array(
								'all_albums' => true,
								'order_key' => 'date_created');
					bp_album_query_albums($album_args);?>

  				<?php if ( bp_album_has_albums() ) : ?>
						<div class="picture-pagination">
							<?php bp_album_album_pagination(); ?>	
						</div>			
					
						<div class="picture-gallery">	
							<?php $count = 0; ?>											
							<?php while ( bp_album_has_albums() ) : bp_album_the_album(); ?>
								<div class="picture-thumb-box">
	                <a href="<?php bp_album_album_url() ?>" class="picture-thumb"><img src='<?php echo bp_album_get_album_feature_url() ?>' /></a>
	                <a href="<?php bp_album_album_url() ?>"  class="picture-title"><?php bp_album_album_title() ?></a>	
	                <?php if (is_super_admin()) : ?>
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; album actions &raquo;
												</option>
												<option value="window.location = '<?php bp_album_album_edit_url_stub()?>'">
													Edit Gallery
												</option>
												<option value="BPADeleteAlbum(<?php bp_album_album_id() ?>,'<?php bp_album_album_title()?>')">
													Delete Gallery
												</option>
											</select>
											<p>
											<?php bp_album_album_time() ?>
										</p>
											<div class="bpa-album-meta">                       
												<a href="#" class="like" id="bpa-like-album-<?php bp_album_album_id() ?>" title="Like this album"><img src="<?php echo get_stylesheet_directory_uri() ?>/img/thumbsupsm.png" /></a>
												<div class="clear"></div>
							        </div>
										</div>
									<?php endif; ?>		
								</div>
					
							<?php endwhile; ?>
						</div>					
					
					<?php else : ?>
					
						<div id="message" class="info">
							<p><?php echo bp_word_or_name( __('No albums here, show something to the community!', 'bp-phototag' ), __( "Either %s hasn't uploaded any picture yet or they have restricted access", 'bp-phototag' )  ,false,false) ?></p>
						</div>
				
					<?php endif; ?>

                  

			</div><!-- #item-body -->


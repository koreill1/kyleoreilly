<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>
<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); //hook ?>
<article itemscope itemtype="http://schema.org/Article" id="post-<?php the_ID(); ?>" <?php post_class("post clearfix portfolio " . $themify->get_categories_as_classes(get_the_ID())); ?>>
	<?php themify_post_start(); //hook ?>

<?php if($themify->hide_image != "yes"): ?>
	<?php themify_before_post_image(); // Hook ?>
	<?php
	if(themify_check('gallery_shortcode')){

		wp_enqueue_script('themify-carousel-js');

		// Get images from [gallery]
		$sc_gallery = preg_replace('#\[gallery(.*)ids="([0-9|,]*)"(.*)\]#i', '$2', themify_get('gallery_shortcode'));
		$image_ids = explode(',', str_replace(' ', '', $sc_gallery));
		$gallery_images = get_posts(array(
			'post__in' => $image_ids,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'orderby' => 'post__in',
			'order' => 'ASC'
		));

		$autoplay = themify_check('setting-portfolio_slider_autoplay')?
						themify_get('setting-portfolio_slider_autoplay'): '4000';

		$effect = themify_check('setting-portfolio_slider_effect')?
				themify_get('setting-portfolio_slider_effect'):	'scroll';

		$speed = themify_check('setting-portfolio_slider_transition_speed')?
				themify_get('setting-portfolio_slider_transition_speed'): '500';

		?>
		<div class="post-image">
			<div id="portfolio-slider-<?php the_ID(); ?>" class="slideshow-wrap">
				<ul class="slideshow" data-id="portfolio-slider-<?php the_ID(); ?>" data-autoplay="<?php echo $autoplay; ?>" data-effect="<?php echo $effect; ?>" data-speed="<?php echo $speed; ?>">
					<?php
					foreach ( $gallery_images as $gallery_image ) { ?>
						<li>
							<?php
							$getimage = $themify->theme->portfolio_image($gallery_image->ID, $themify->width, $themify->height);
							if(is_singular('portfolio')){
								?>
								<a href="<?php echo themify_get_featured_image_link(); ?>">
									<?php echo $getimage; ?>
								</a>
								<?php
							} else { ?>
								<a href="<?php echo themify_get_featured_image_link(); ?>">
									<?php echo $getimage; ?>
								</a>
								<?php
							}
							if('' != $img_caption = $gallery_image->post_excerpt) {	?>
								<div class=slider-image-caption><?php echo $img_caption; ?></div>
								<?php
							}
							?>
						</li>
						<?php
					}
					?>
				</ul>
			</div> <!-- .slideshow-wrap -->
			<?php echo do_shortcode(themify_get('gallery_shortcode')); ?>
		</div> <!-- .post-image -->
	<?php } else {

		//otherwise display the featured image
		if( $post_image = themify_get_image('ignore=true&'.$themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ){ ?>
			<figure class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
				<?php endif; ?>
			</figure>
		<?php } // end if post image
	} ?>
	<?php themify_after_post_image(); // Hook ?>
<?php endif; //post image ?>

	<div class="post-content">

		<?php if($themify->hide_meta != 'yes'): ?>
			<p class="post-meta entry-meta">
						<?php echo ' '. get_the_term_list( get_the_ID(), get_post_type().'-category', '<span class="post-category">', ', ', ' </span>' ) ?>
			</p>
		<?php endif; //post meta ?>

		<?php if($themify->hide_title != "yes"): ?>
			<?php themify_before_post_title(); // Hook ?>
			<?php if($themify->unlink_title == "yes"): ?>
				<h1 class="post-title entry-title" itemprop="name"><?php the_title(); ?></h1>
			<?php else: ?>
				<h1 class="post-title entry-title" itemprop="name"><a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php endif; //unlink post title ?>
			<?php themify_after_post_title(); // Hook ?>
		<?php endif; //post title ?>

		<?php if($themify->hide_date != "yes"): ?>
			<time datetime="<?php the_time('o-m-d') ?>" class="post-date entry-date updated" itemprop="datePublished"><?php the_time(apply_filters('themify_loop_date', 'M j, Y')) ?></time>
		<?php endif; //post date ?>

		<div class="entry-content" itemprop="articleBody">

		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>

			<?php the_excerpt(); ?>

		<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>

		<?php else: ?>

			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

		<?php endif; //display content ?>

		</div><!-- /.entry-content -->

		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

	</div>
	<!-- /.post-content -->

	<?php themify_post_end(); //hook ?>
</article>
<!-- /.post -->
<?php themify_post_after(); //hook ?>
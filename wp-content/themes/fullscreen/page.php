<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>


<?php if(is_front_page() && !is_paged()) : ?>
<?php get_template_part( 'includes/welcome-message'); ?>
<?php else : ?>

<!-- layout-container -->
<div id="layout" class="pagewidth clearfix">
	
	<div id="contentwrap">
    
    	<?php themify_content_before(); //hook ?>
		<!-- content -->
		<div id="content" class="clearfix">
        	<?php themify_content_start(); //hook ?>
			
			<?php 
			/////////////////////////////////////////////
			// 404							
			/////////////////////////////////////////////
			?>
			<?php if(is_404()): ?>
				<h1 class="page-title" itemprop="name"><?php _e('404','themify'); ?></h1>	
				<p><?php _e( 'Page not found.', 'themify' ); ?></p>	
			<?php endif; ?>
	
			<?php 
			/////////////////////////////////////////////
			// Page							
			/////////////////////////////////////////////
			?>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="page-<?php the_ID(); ?>" class="type-page" itemscope itemtype="http://schema.org/Article">
				
				<!-- page-title -->
				<?php if($themify->page_title != "yes"): ?> 
					<h1 class="page-title" itemprop="name"><?php the_title(); ?></h1>
				<?php endif; ?>	
				<!-- /page-title -->
				
				<div class="page-content entry-content" itemprop="articleBody">
				
					<?php the_content(); ?>
					
					<?php wp_link_pages(array('before' => '<p class="post-pagination"><strong>'.__('Pages:','themify').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					
					<?php edit_post_link(__('Edit','themify'), '[', ']'); ?>
					
					<!-- comments -->
					<?php if(!themify_check('setting-comments_pages') && $themify->query_category == ""): ?>
						<?php comments_template(); ?>
					<?php endif; ?>
					<!-- /comments -->
				
				</div>
				<!-- /.post-content -->
			
				</div><!-- /.type-page -->
		<?php endwhile; endif; ?>
			
			<?php 
			/////////////////////////////////////////////
			// Query Category							
			/////////////////////////////////////////////
			?>
	
			<?php 
			
			///////////////////////////////////////////
			// Setting image width, height
			///////////////////////////////////////////
			
			if('' != $themify->query_category): ?>

				<?php
				// Categories for Query Posts or Portfolios
				$categories = '0' == $themify->query_category? $themify->theme->get_all_terms_ids($themify->query_taxonomy) : explode(',', str_replace(' ', '', $themify->query_category));
				$qpargs = array(
					'post_type' => $themify->query_post_type,
					'tax_query' => array(
						array(
							'taxonomy' => $themify->query_taxonomy,
							'field' => 'id',
							'terms' => $categories
						)
					),
					'posts_per_page' => $themify->posts_per_page,
					'paged' => $themify->paged,
					'order' => $themify->order,
					'orderby' => $themify->orderby
				);
				?>
						
				<?php if(themify_get('section_categories') != 'yes'): ?>
				
					<?php query_posts(apply_filters('themify_query_posts_page_args', $qpargs)); ?>
					
						<?php if(have_posts()): ?>
							
							<!-- loops-wrapper -->
							<div id="loops-wrapper" class="loops-wrapper <?php echo $themify->layout . ' ' . $themify->post_layout; ?>">
	
								<?php while(have_posts()) : the_post(); ?>
									
									<?php get_template_part('includes/loop', $themify->query_post_type); ?>
							
								<?php endwhile; ?>
													
							</div>
							<!-- /loops-wrapper -->
	
							<?php if ($themify->page_navigation != "yes"): ?>
								<?php get_template_part( 'includes/pagination'); ?>
							<?php endif; ?>
									
						<?php else : ?>	
						
						<?php endif; ?>
	
				<?php else: ?>
					
					<?php $categories = explode(",",str_replace(" ","",$themify->query_category)); ?>
					
					<?php foreach($categories as $category): ?>
					
					<?php $category = get_term_by(is_numeric($category)? 'id': 'slug', $category, 'category');
					$cats = get_categories( array( 'include' => isset( $category ) && isset( $category->term_id )? $category->term_id : 0, 'orderby' => 'id' ) ); ?>
					
					<?php foreach($cats as $cat): ?>
						
						<?php query_posts( apply_filters( 'themify_query_posts_page_args', 'cat='.$cat->cat_ID.'&posts_per_page='.$themify->posts_per_page.'&paged='.$themify->paged.'&order='.$themify->order.'&orderby='.$themify->orderby ) );	?>
				
						<?php if(have_posts()): ?>
							
							<!-- category-section -->
							<div class="category-section clearfix <?php echo $cat->slug; ?>-category">
	
								<h3 class="category-section-title"><a href="<?php echo esc_url( get_category_link($cat->cat_ID) ); ?>" title="<?php _e('View more posts', 'themify'); ?>"><?php echo $cat->cat_name; ?></a></h3>
	
								<!-- loops-wrapper -->
								<div id="loops-wrapper" class="loops-wrapper <?php echo $themify->layout . ' ' . $themify->post_layout; ?>">
								<?php while(have_posts()) : the_post(); ?>
									
									<?php get_template_part('includes/loop', 'query'); ?>
							
								<?php endwhile; ?>
								</div>
								<!-- /loops-wrapper -->
	
								<?php if ($themify->page_navigation != "yes"): ?>
									<?php get_template_part( 'includes/pagination'); ?>
								<?php endif; ?>
	
							</div>
							<!-- /category-section -->
									
						<?php else : ?>	
						
						<?php endif; ?>
					
					<?php endforeach; ?>
					
					<?php endforeach; ?>
				
				<?php endif; ?>
	
			<?php endif; ?>
			<?php wp_reset_query(); ?>
            
            <?php themify_content_end(); //hook ?>
		</div>
		<!-- /content -->
        <?php themify_content_after() //hook; ?>

	</div>
	<!-- /contentwrap -->
	
	<?php 
	/////////////////////////////////////////////
	// Sidebar							
	/////////////////////////////////////////////
	if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>
		
</div>
<!-- /layout-container -->

<?php endif; // Homepage layout ?>
	
<?php get_footer(); ?>

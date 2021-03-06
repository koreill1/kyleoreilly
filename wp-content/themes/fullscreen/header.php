<!doctype html>
<html <?php echo themify_get_html_schema(); ?> <?php language_attributes(); ?>>
<head>
<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />

<!-- reset viewport for mobile -->

<title itemprop="name"><?php wp_title(); ?></title>

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<!-- wp_header -->
<?php wp_head(); ?>

</head>

<body <?php body_class();?>>

<?php themify_body_start(); //hook ?>
<div id="pagewrap" class="hfeed site">

	<div id="headerwrap">
	
    	<?php themify_header_before(); //hook ?>
		<header id="header" class="pagewidth">
        	<?php themify_header_start(); //hook ?>
			
			<hgroup>
				<?php echo themify_logo_image('site_logo'); ?>
	
				<?php if ( $site_desc = get_bloginfo( 'description' ) ) : ?>
					<?php global $themify_customizer; ?>
					<div id="site-description" class="site-description"><?php echo class_exists( 'Themify_Customizer' ) ? $themify_customizer->site_description( $site_desc ) : $site_desc; ?></div>
				<?php endif; ?>
			</hgroup>
			
			<div id="main-nav-wrap">
				<div id="menu-button"><?php _e('Menu', 'themify'); ?></div>
                <nav>
                    <?php
					if ( function_exists( 'themify_custom_menu_nav' ) ) {
						themify_custom_menu_nav();
					} else {
						wp_nav_menu( array(
							'theme_location' => 'main-nav',
							'fallback_cb'    => 'themify_default_main_nav',
							'container'      => '',
							'menu_id'        => 'main-nav',
							'menu_class'     => 'main-nav'
						));
					}
					?>
                </nav>
				<!-- /#main-nav -->		
			</div>
            <!-- /#main-nav-wrap --> 

			<div class="social-widget">
				<?php dynamic_sidebar('social-widget'); ?>
	
				<?php if(!themify_check('setting-exclude_rss')): ?>
					<div class="rss"><a href="<?php if(themify_get('setting-custom_feed_url') != ""){ echo themify_get('setting-custom_feed_url'); } else { echo bloginfo('rss2_url'); } ?>">RSS</a></div>
				<?php endif ?>
			</div>
			<!-- /.social-widget -->
			<div class="clearfix"></div>
			<?php if( !is_front_page() ): //hide header widget on home ?> 
			<div class="header-widget">
				<?php dynamic_sidebar('header-widget'); ?>
			</div>
			<!--/header widget -->
			<?php endif; ?>

			<?php themify_header_end(); //hook ?>
		</header>
		<!-- /#header -->
        <?php themify_header_after(); //hook ?>
				
	</div>
	<!-- /#headerwrap -->
	
	<div id="body" class="clearfix">
    <?php themify_layout_before(); //hook ?>

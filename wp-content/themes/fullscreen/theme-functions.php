<?php	

/*
To add custom PHP functions to the theme, create a new 'custom-functions.php' file in the theme folder. 
They will be added to the theme automatically.
*/

/**
 * Changes condition to filter post layout class
 * @param $condition
 * @return bool
 */
function themify_theme_default_post_layout_condition($condition) {
	return $condition || is_tax('portfolio-category');
}

/**
 * Returns modified post layout class
 * @return string
 */
function themify_theme_default_post_layout() {
	global $themify;
	// get default layout
	$class = $themify->post_layout;
	if('portfolio' == $themify->query_post_type) {
		$class = themify_check('portfolio_layout') ? themify_get('portfolio_layout') : themify_get('setting-default_post_layout');
	} elseif (is_tax('portfolio-category')) {
		$class = themify_check('setting-default_portfolio_index_post_layout')? themify_get('setting-default_portfolio_index_post_layout') : 'list-post';
	}
	return $class;
}

/**
 * Changes condition to filter sidebar layout class
 * @param bool $condition
 * @return bool
 */
function themify_theme_default_layout_condition($condition) {
	global $themify;
	// if layout is not set or is the home page and front page displays is set to latest posts
	return $condition || (is_home() && 'posts' == get_option('show_on_front')) || '' != $themify->query_category || is_tax('portfolio-category') || is_singular('portfolio');
}
/**
 * Returns modified sidebar layout class
 * @param string $class Original body class
 * @return string
 */
function themify_theme_default_layout($class) {
	global $themify;
	// get default layout
	$class = $themify->layout;
	if (is_tax('portfolio-category')) {
		$class = themify_check('setting-default_portfolio_index_layout')? themify_get('setting-default_portfolio_index_layout') : 'sidebar-none';
	}
	return $class;
}

add_filter('themify_default_post_layout_condition', 'themify_theme_default_post_layout_condition', 12);
add_filter('themify_default_post_layout', 'themify_theme_default_post_layout', 12);
add_filter('themify_default_layout_condition', 'themify_theme_default_layout_condition', 12);
add_filter('themify_default_layout', 'themify_theme_default_layout', 12);


/* 	Enqueue Stylesheets and Scripts
/***************************************************************************/
add_action( 'wp_enqueue_scripts', 'themify_theme_enqueue_scripts', 11 );
function themify_theme_enqueue_scripts(){
	
	///////////////////
	//Enqueue styles
	///////////////////

	//Themify base styling
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), wp_get_theme()->display('Version'));
	
	//Themify Media Queries CSS
	wp_enqueue_style( 'themify-media-queries', THEME_URI . '/media-queries.css');
	

	//Google Web Fonts embedding
	wp_enqueue_style( 'google-fonts', themify_https_esc('http://fonts.googleapis.com/css'). '?family=Open+Sans+Condensed:700|Open+Sans&subset=latin,latin-ext');
	
	///////////////////
	//Enqueue scripts
	///////////////////
	
	//Fullscreen background script
	wp_enqueue_script( 'fullscreen', THEME_URI . '/js/fullscreen.js', array('jquery'), false, true );
	
	//Touch script
	wp_enqueue_script( 'touch', THEME_URI . '/js/touch.js', array('jquery'), false, true );
	//Carousel script
	wp_enqueue_script('themify-carousel-js');
	
	//Resize Stop event
	wp_enqueue_script( 'resize-stop', THEME_URI . '/js/jquery.resizeStop.js', array('jquery'), false, true );

	//Themify internal scripts
	wp_enqueue_script( 'theme-script',	THEME_URI . apply_filters('themify_main_script', '/js/themify.script.js'), array('jquery'), false, true );

	//Themify Gallery
	wp_enqueue_script( 'themify-gallery', THEMIFY_URI . '/js/themify.gallery.js', array('jquery'), false, true );
	
	//Inject variable values in gallery script
	wp_localize_script( 'theme-script', 'themifyScript', array(
		'lightbox' => themify_lightbox_vars_init(),
		'lightboxContext' => apply_filters('themify_lightbox_context', '#pagewrap'),
		'isTouch' => themify_is_touch()? 'true': 'false',
		'backgroundMode' => themify_check('setting-background_mode')? themify_get('setting-background_mode') : 'cover'
	));
	
	//Themify gallery script
	wp_enqueue_script( 'gallery-script',	THEME_URI . '/js/themify.gallery.js', array('jquery'), false, true );
	
	//Inject variable values in gallery script
	wp_localize_script( 'gallery-script', 'themifyVars', array(
			'play'		=> (!themify_get('setting-footer_slider_auto'))? 'yes' : themify_get('setting-footer_slider_auto'),
			'autoplay'	=> (!themify_get('setting-footer_slider_autotimeout'))? 5 : themify_get('setting-footer_slider_autotimeout'),
			'speed'		=> (!themify_get('setting-footer_slider_speed'))? 500 : themify_get('setting-footer_slider_speed')
		)
	);
	
	//WordPress internal script to move the comment box to the right place when replying to a user
	if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' );
		
}

/**
 * Add JavaScript files if IE version is lower than 9
 * @package themify
 */
function themify_ie_enhancements(){
	echo '
	<!-- media-queries.js -->
	<!--[if lt IE 9]>
		<script src="' . THEME_URI . '/js/respond.js"></script>
	<![endif]-->
	
	<!-- html5.js -->
	<!--[if lt IE 9]>
		<script src="'.themify_https_esc('http://html5shim.googlecode.com/svn/trunk/html5.js').'"></script>
	<![endif]-->
	';
}
add_action( 'wp_head', 'themify_ie_enhancements' );

/**
 * Add viewport tag for responsive layouts
 * @package themify
 */
function themify_viewport_tag(){
	echo "\n".'<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">'."\n";
}
add_action( 'wp_head', 'themify_viewport_tag' );

/**
 * Custom Post Type Background Gallery
/***************************************************************************/
class Themify_Background_Gallery{
	
	/**
	 * Custom post type key
	 * @var String
	 */
	static $cpt = 'background-gallery';

	function __construct(){
		add_action( 'init',  	 array(&$this, 'register_background_gallery') );
		add_action( 'wp_footer', array(&$this, 'create_controller') );
		add_action( 'wp_footer', array(&$this, 'background_pattern') );
		add_action( 'wp_footer', array(&$this, 'fullscreen_button') );
		if( is_admin() ){
			add_action('admin_head-media-upload-popup', array(&$this, 'hide_gallery_settings') );
			add_action('add_meta_boxes',  array(&$this, 'backgrounds_meta_box' ) );
			add_filter('media_upload_tabs', array(&$this, 'remove_from_url_tab') );
		}
	}

	function create_controller(){
		global $post;
		
		/** ID of default background gallery
		 * @var String|Number */
		$bggallery_id = $this->get_bggallery_id();
		
		// If we still don't have a background gallery ID, do nothing.
		if( !$bggallery_id || 'default' == $bggallery_id ) return;

		$images = get_posts( array(
			'post_type' => 'attachment',
			'post_parent' => $bggallery_id,
			'numberposts' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_mime_type' => 'image'
		));

		if($images){
			echo '
			<div id="gallery-controller">
				<div id="footer-slider" class="pagewidth slider">
					
						<ul class="slides clearfix">';
			foreach( $images as $image ){
				// Get large size for background
				$image_size = (themify_get('setting-footer_slider_image_size') != 'blank') ? themify_get('setting-footer_slider_image_size') : 'large';
				$image_data = wp_get_attachment_image_src( $image->ID, $image_size );
				// Get thumbnail for carousel
				$image_thumb = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
				echo '<li data-bg="',$image_data[0],'">
						<img src="',$image_thumb[0],'" width="40" alt="',$image->post_title,'" />';

				if( 'none' != themify_get('setting-show_caption') || '' != themify_get('setting-show_caption') ){
					if( 'excerpt' == themify_get('setting-show_caption') ){
						$image_caption = $image->post_excerpt;
					} elseif( 'title' == themify_get('setting-show_caption') ) {
						$image_caption = $image->post_title;
					}
					if( $image_caption ){
						echo '
							<div class="fullscreen-caption">
								<div class="fullscreen-caption-inner">
									',$image_caption,'
								</div>
							</div>';
					}
				}
						
				echo '</li>';
			}
			echo '		</ul>
						<div class="carousel-nav-wrap">
							<a href="#" class="carousel-prev" style="display: block; ">&lsaquo;</a>
							<a href="#" class="carousel-playback">' . __('Pause', 'themify') . '</a>
							<a href="#" class="carousel-next" style="display: block; ">&rsaquo;</a>
						</div>
					
				</div>
			</div>
			<!-- /gallery-controller -->';
		}
	}

	function fullscreen_button(){		
		// Check if we have a background gallery id
		if(  'default' == $this->get_bggallery_id() ) return;
		
		echo '	<!-- go fullscreen button -->
				<a id="fullscreen-button" href="#" class=""></a>';
	}
	
	function background_pattern(){
		$class = '';
		if( 'default' != $this->get_bggallery_id() ) {
			$class = 'loading';
		}
		echo '	<!-- background pattern -->
				<div id="pattern" class="'. $class .'"></div>';
	}
	
	/**
	 * Hides gallery settings from media upload
	 */
	function hide_gallery_settings(){
		if( self::$cpt == get_post_type( $_GET['post_id'] ) )
			echo '<style>#gallery-settings{ display: none !important; }</style>';
	}
	
	/**
	 * Displays a list of current background galleries
	 * @return array of name/value arrays
	 */
	function get_backgrounds(){
		$bgs = get_posts( array(
			'post_type' => self::$cpt,
			'orderby' => 'title',
			'order' => 'ASC',
			'numberposts' => -1
		));
		$backgrounds = array();
		$backgrounds[] = array( 'name' => '', 'value' => 'default');
		foreach($bgs as $index => $background){
			$backgrounds[] = array(
				'name' => $background->post_title,
				'value' => $background->ID
			);
		}
		return $backgrounds;
	}
	
	/**
	 * Return the background gallery ID if one of the following is found:
	 * - bg gallery defined in theme settings
	 * - bg gallery defined in Themify custom panel, either in post or page
	 * @return String|Mixed Background Gallery ID or 'default'
	 */
	function get_bggallery_id(){
		$bggallery_id = themify_get('setting-default_bg_gallery');
		
		// If it's a page or post, check if a gallery was specified in custom field
		if( is_singular() ){
			$bggallery_id = themify_get('background_gallery');
			if( 'default' == $bggallery_id ){
				$bggallery_id = themify_get('setting-default_bg_gallery');
			}
		}
		if( is_attachment() ){
			$bggallery_id = themify_get('setting-default_bg_gallery');
		}
		
		return $bggallery_id;
	}

	function register_background_gallery() {
		register_post_type( self::$cpt, array(
			'labels' => array(
				'name' => __( 'BG Galleries', 'themify' ),
				'singular_name' => __( 'BG Gallery', 'themify' ),
				'add_new' => __( 'Add New', 'themify' ),
				'add_new_item' => __( 'Add New BG Gallery', 'themify' ),
				'edit_item' => __( 'Edit BG Gallery', 'themify' ),
				'new_item' => __( 'New BG Gallery', 'themify' ),
				'view_item' => __( 'View BG Gallery', 'themify' ),
				'search_items' => __( 'Search BG Galleries', 'themify' ),
				'not_found' => __( 'No BG galleries found', 'themify' ),
				'not_found_in_trash' => __( 'No BG galleries found in Trash', 'themify' ),
				'menu_name' => __( 'BG Gallery', 'themify' )
			),
			'hierarchical' => false,
			'supports' => array( 'title' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'has_archive' => false,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post'
			)
		);
	}
	
	/**
	 * Removes 'From URL' tab
	 * @param Array
	 * @return Array
	 */
	function remove_from_url_tab($tabs) {
		if (isset($_REQUEST['post_id'])) {
			$post_type = get_post_type($_REQUEST['post_id']);
			if ( self::$cpt == $post_type){
				unset($tabs['type_url']);
				unset($tabs['library']);
			}
		}
		return $tabs;
	}
	
	/**
	 * Includes this custom post to array of cpts managed by Themify
	 * @param Array
	 * @return Array
	 */
	function extend_post_types($types){
		$types[] = self::$cpt;
		return $types;
	}
	
	function backgrounds_meta_box(){
		add_meta_box( "themify-backgrounds-meta-box", __('Background Images', 'themify' ), array( &$this, 'do_backgrounds_meta_box'), self::$cpt, 'normal', 'high' );
	}
	
	function do_backgrounds_meta_box(){
		global $post_ID, $temp_ID;
		
		/**
		 * Entry ID
		 * @var Number
		 */
		$id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		
		// Security nonce is not needed here since we won't be saving values from this meta box

		// Show gallery tab if there are some images on the post, otherwise the first tab to upload
		$media_library_url = admin_url('media-upload.php?post_id='.$id.'&#038;type=image&#038;tab=gallery');
		
		// Load media library for this background gallery
		if ($id > 0)
	      echo '<style type="text/css">
	      			#edit-slug-box, #minor-publishing-actions #preview-action { display: none; }
	      			#poststuff h2{ margin-top: 0; }
	      			#themify-backgrounds-meta-box, #themify-backgrounds-meta-box iframe {
						min-height: 385px;
					}
					#poststuff iframe{
						width: 100%;
						height: 100%;
						display: block;
					}
				</style>
				<i'.'frame'.' frameborder="0" name="fast_insert" id="fast_insert" src="'.$media_library_url.'" hspace="0"
				> </i'.'frame'.'>';
	    else
		  _e('Entry ID could not be obtained. Click "Save Draft" to get an ID after saving the draft.', 'themify');
	}
}
// Start Background Gallery
global $themify_bg_gallery;
$themify_bg_gallery = new Themify_Background_Gallery();


/**
 * Make IE behave like a standards-compliant browser 
 */
function themify_ie_standards_compliant() {
	echo '
	<!--[if lt IE 9]>
	<script src="'.themify_https_esc('http://s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js').'"></script>
	<script type="text/javascript" src="'.themify_https_esc('http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js').'"></script> 
	<![endif]-->
	';
}
add_action('wp_head', 'themify_ie_standards_compliant');

/* Custom Write Panels
/***************************************************************************/

/** Definition for tri-state hide meta buttons
 *  @var array */
$states = array(
	array(
		'name' => __('Hide', 'themify'),
		'value' => 'yes',
		'icon' => THEMIFY_URI . '/img/ddbtn-check.png',
		'title' => __('Hide this meta', 'themify')
	),
	array(
		'name' => __('Do not hide', 'themify'),
		'value' => 'no',
		'icon' => THEMIFY_URI . '/img/ddbtn-cross.png',
		'title' => __('Show this meta', 'themify')
	),
	array(
		'name' => __('Theme default', 'themify'),
		'value' => '',
		'icon' => THEMIFY_URI . '/img/ddbtn-blank.png',
		'title' => __('Use theme settings', 'themify'),
		'default' => true
	)
);

///////////////////////////////////////
// Setup Write Panel Options
///////////////////////////////////////

// Post Meta Box Options
$post_meta_box_options = array(
	array(
	  "name" 		=> "layout",	
	  "title" 		=> __('Sidebar Option', 'themify'), 	
	  "description" => "", 				
	  "type" 		=> "layout",			
	'show_title' => true,
	  "meta"		=> array(
	  						array("value" => "default", "img" => "images/layout-icons/default.png", "selected" => true, 'title' => __('Default', 'themify')),
							array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
							array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
							array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
						)
	),
		// Content Width
		array(
			'name'=> 'content_width',
			'title' => __('Content Width', 'themify'),
			'description' => '',
			'type' => 'layout',
			'show_title' => true,
			'meta' => array(
				array(
					'value' => 'default_width',
					'img' => 'themify/img/default.png',
					'selected' => true,
					'title' => __( 'Default', 'themify' )
				),
				array(
					'value' => 'full_width',
					'img' => 'themify/img/fullwidth.png',
					'title' => __( 'Fullwidth', 'themify' )
				)
			)
		),
   	// Post Image
	array(
		  "name" 		=> "post_image",
		  "title" 		=> __('Featured Image', 'themify'),
		  "description" => '',
		  "type" 		=> "image",
		  "meta"		=> array()
		),
	// Featured Image Size
	array(
		  'name'	=>	'feature_size',
		  'title'	=>	__('Image Size', 'themify'),
		  'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="admin.php?page=themify_regenerate-thumbnails">Regenerated</a>', 'themify'),
		  'type'		 =>	'featimgdropdown'
		),
	// Image Width
	array(
		  "name" 		=> "image_width",	
		  "title" 		=> __('Image Width', 'themify'), 
		  "description" => "", 				
		  "type" 		=> "textbox",			
		  "meta"		=> array("size"=>"small")			
		),
	// Image Height
	array(
		  "name" 		=> "image_height",	
		  "title" 		=> __('Image Height', 'themify'), 
		  "description" => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'), 				
		  "type" 		=> "textbox",			
		  "meta"		=> array("size"=>"small")			
		),
	// Select Background Gallery
	array(
		  "name" 		=> "background_gallery",
		  "title"		=> __('Background Gallery', 'themify'),
		  "description"	=> __('Select gallery to show on background', 'themify'),
		  "type" 		=> "dropdown",			
		  "meta"		=> $themify_bg_gallery->get_backgrounds()
	),
	// Hide Post Title
	array(
		  "name" 		=> "hide_post_title",	
		  "title" 		=> __('Hide Post Title', 'themify'), 	
		  "description" => "", 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)			
		),
	// Unlink Post Title
	array(
		  "name" 		=> "unlink_post_title",	
		  "title" 		=> __('Unlink Post Title', 'themify'), 	
		  "description" => __('Unlink post title (it will display the post title without link)', 'themify'), 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
							)
		),

	// Hide Post Meta
	array(
		  "name" 		=> "hide_post_meta",	
		  "title" 		=> __('Hide Post Meta', 'themify'), 	
		  "description" => "", 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
							)
		),
	// Hide Post Date
	array(
		  "name" 		=> "hide_post_date",	
		  "title" 		=> __('Hide Post Date', 'themify'), 	
		  "description" => "", 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
							)
		),
	// Hide Post Image
	array(
		  "name" 		=> "hide_post_image",	
		  "title" 		=> __('Hide Featured Image', 'themify'), 	
		  "description" => "", 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
							)
		),
		// Unlink Post Image
	array(
		  "name" 		=> "unlink_post_image",	
		  "title" 		=> __('Unlink Featured Image', 'themify'), 	
		  "description" => __('Display the Featured Image without link', 'themify'), 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
							)
		),
	// Video URL
	array(
		"name" 		=> 'video_url',
		"title" 	=> __('Video URL', 'themify'),
		"description" => __('Video embed URL such as YouTube or Vimeo video url (<a href="http://themify.me/docs/video-embeds">details</a>).', 'themify'),
		"type" 		=> 'textbox',
		"meta"		=> array()
	),
	// External Link
	array(
		  "name" 		=> "external_link",	
		  "title" 		=> __('External Link', 'themify'), 	
		  "description" => __('Link Featured Image to external URL', 'themify'), 				
		  "type" 		=> "textbox",			
		  "meta"		=> array()			
		),
	// Lightbox Link + Zoom icon
	themify_lightbox_link_field()
);
							

// Page Meta Box Options
$page_meta_box_options = array(
  	// Page Layout
	array(
	  "name" 		=> "page_layout",
	  "title"		=> __('Sidebar Option', 'themify'),
	  "description"	=> "",
	  "type"		=> "layout",
		'show_title' => true,
	  "meta"		=> array(
			array("value" => "default", "img" => "images/layout-icons/default.png", "selected" => true, 'title' => __('Default', 'themify')),
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
		)
	),
	// Content Width
	array(
		'name'=> 'content_width',
		'title' => __('Content Width', 'themify'),
		'description' => '',
		'type' => 'layout',
		'show_title' => true,
		'meta' => array(
			array(
				'value' => 'default_width',
				'img' => 'themify/img/default.png',
				'selected' => true,
				'title' => __( 'Default', 'themify' )
			),
			array(
				'value' => 'full_width',
				'img' => 'themify/img/fullwidth.png',
				'title' => __( 'Fullwidth', 'themify' )
			)
		)
	),
	// Select Background Gallery
	array(
		  "name" 		=> "background_gallery",
		  "title"		=> __('Background Gallery', 'themify'),
		  "description"	=> __('Select gallery to show on background', 'themify'),
		  "type" 		=> "dropdown",			
		  "meta"		=> $themify_bg_gallery->get_backgrounds()
	),
	// Hide page title
	array(
		"name" 		=> "hide_page_title",
		"title"		=> __('Hide Page Title', 'themify'),
		"description"	=> "",
		"type" 		=> "dropdown",			
		"meta"		=> array(
			array("value" => "default", "name" => "", "selected" => true),
			array("value" => "yes", 'name' => __('Yes', 'themify')),
			array("value" => "no",	'name' => __('No', 'themify'))
		)
	),
		// Custom menu for page
        array(
            'name' 		=> 'custom_menu',
            'title'		=> __( 'Custom Menu', 'themify' ),
            'description'	=> '',
            'type'		=> 'dropdown',
            'meta'		=> themify_get_available_menus(),
        ),
);

$query_post_meta_box = array(
	// Post Category
	array(
		'name' 		=> 'query_category',
		'title'		=> __('Post Category', 'themify'),
		'description'	=> __('Select a category or enter multiple category IDs (eg. 2,5,6). Enter 0 to display all categories.', 'themify'),
		'type'		=> 'query_category',
		'meta'		=> array()
	),
	// Descending or Ascending Order for Posts
	array(
		'name' 		=> 'order',
		'title'		=> __('Order', 'themify'),
		'description'	=> '',
		'type'		=> 'dropdown',
		'meta'		=> array(
			array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
			array('name' => __('Ascending', 'themify'), 'value' => 'asc')
		)
	),
	// Criteria to Order By
	array(
		'name' 		=> 'orderby',
		'title'		=> __('Order By', 'themify'),
		'description'	=> '',
		'type'		=> 'dropdown',
		'meta'		=> array(
			array('name' => __('Date', 'themify'), 'value' => 'content', 'selected' => true),
			array('name' => __('Random', 'themify'), 'value' => 'rand'),
			array('name' => __('Author', 'themify'), 'value' => 'author'),
			array('name' => __('Post Title', 'themify'), 'value' => 'title'),
			array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
			array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
			array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
			array('name' => __('Post ID', 'themify'), 'value' => 'ID')
		)
	),
	// Section Categories
	array(
		  "name" 		=> "section_categories",	
		  "title" 		=> __('Section Categories', 'themify'), 	
		  "description" => __('Display multiple query categories separately', 'themify'), 				
		  "type" 		=> "dropdown",			
		  "meta"		=> array(
		  						array("value" => "default", "name" => "", "selected" => true),
								array("value" => "yes", 'name' => __('Yes', 'themify')),
								array("value" => "no",	'name' => __('No', 'themify'))
				)
	),
	// Post Layout
	array(
		"name" 		=> "layout",
		"title"		=> __('Post Layout', 'themify'),
		"description"	=> "",
		"type"		=> "layout",
		'show_title' => true,
		"meta"		=> array(
			array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'selected' => true, 'title' => __('List Post', 'themify')),
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify')),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
			array('value' => 'list-large-image', 'img' => 'images/layout-icons/list-large-image.png', 'title' => __('List Large Image', 'themify')),
			array('value' => 'list-thumb-image', 'img' => 'images/layout-icons/list-thumb-image.png', 'title' => __('List Thumb Image', 'themify')),
			array('value' => 'grid2-thumb', 'img' => 'images/layout-icons/grid2-thumb.png', 'title' => __('Grid 2 Thumb', 'themify'))
		)
	),
	// Posts Per Page
	array(
		  'name' 		=> 'posts_per_page',
		  'title'		=> __('Posts per page', 'themify'),
		  'description'	=> '',
		  'type'		=> 'textbox',
		  'meta'		=> array('size' => 'small')
		),
	
	// Display Content
	array(
		'name' 		=> 'display_content',
		'title'		=> __('Display Content', 'themify'),
		'description'	=> '',
		'type'		=> 'dropdown',
		'meta'		=> array(
			array('name' => __('Full Content', 'themify'),'value'=>'content','selected'=>true),
			array('name' => __('Excerpt', 'themify'),'value'=>'excerpt'),
			array('name' => __('None', 'themify'),'value'=>'none')
		)
	),
	// Featured Image Size
	array(
		'name'	=>	'feature_size_page',
		'title'	=>	__('Image Size', 'themify'),
		'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="admin.php?page=themify_regenerate-thumbnails">Regenerated</a>', 'themify'),
		'type'		 =>	'featimgdropdown'
		),
	// Image Width
	array(
		  'name' 		=> 'image_width',	
		  'title' 		=> __('Image Width', 'themify'), 
		  'description' => '', 				
		  'type' 		=> 'textbox',			
		  'meta'		=> array('size'=>'small')			
		),
	// Image Height
	array(
		  'name' 		=> 'image_height',	
		  'title' 		=> __('Image Height', 'themify'), 
		  'description' => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'), 				
		  'type' 		=> 'textbox',			
		  'meta'		=> array('size'=>'small')			
		),
	// Hide Title
	array(
		  'name' 		=> 'hide_title',
		  'title'		=> __('Hide Post Title', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)
		),
	// Unlink Post Title
	array(
		  'name' 		=> 'unlink_title',	
		  'title' 		=> __('Unlink Post Title', 'themify'), 	
		  'description' => __('Unlink post title (it will display the post title without link)', 'themify'), 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)			
		),
	// Hide Post Date
	array(
		  'name' 		=> 'hide_date',
		  'title'		=> __('Hide Post Date', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)
		),
	// Hide Post Meta
	array(
		"name" 		=> "hide_post_meta",
		"title"		=> __('Hide Post Meta', 'themify'),
		"description"	=> "",
		"type" 		=> "dropdown",
		"meta"		=> array(
			array("value" => "default", "name" => "", "selected" => true),
			array("value" => "yes", 'name' => __('Yes', 'themify')),
			array("value" => "no",	'name' => __('No', 'themify'))
		)
	),
	// Hide Post Image
	array(
		  'name' 		=> 'hide_image',	
		  'title' 		=> __('Hide Featured Image', 'themify'), 	
		  'description' => '', 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)
	),
	// Unlink Post Image
	array(
		  'name' 		=> 'unlink_image',	
		  'title' 		=> __('Unlink Featured Image', 'themify'), 	
		  'description' => __('Display the Featured Image without link', 'themify'), 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)			
	),
	// Page Navigation Visibility
	array(
		  'name' 		=> 'hide_navigation',
		  'title'		=> __('Hide Page Navigation', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
			)
		)
);

/** 
 * Page Meta Box Options
 * @var array */
$query_portfolio_meta_box = array(
	// Notice
	array(
		'name' => '_query_posts_notice',
		'title' => '',
		'description' => '',
		'type' => 'separator',
		'meta' => array(
			'html' => '<div class="themify-info-link">' . sprintf( __( '<a href="%s">Query Posts</a> allows you to query WordPress posts from any category on the page. To use it, select a Query Category.', 'themify' ), 'http://themify.me/docs/query-posts' ) . '</div>'
		),
	),
	// Query Category
	array(
		'name' 		=> 'portfolio_query_category',
		'title'		=> __('Portfolio Category', 'themify'),
		'description'	=> __('Select a portfolio category or enter multiple portfolio category IDs (eg. 2,5,6). Enter 0 to display all portfolio categories.', 'themify'),
		'type'		=> 'query_category',
		'meta'		=> array('taxonomy' => 'portfolio-category')
	),
	// Descending or Ascending Order for Portfolios
	array(
		'name' 		=> 'portfolio_order',
		'title'		=> __('Order', 'themify'),
		'description'	=> '',
		'type'		=> 'dropdown',
		'meta'		=> array(
			array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
			array('name' => __('Ascending', 'themify'), 'value' => 'asc')
		)
	),
	// Criteria to Order By
	array(
		'name' 		=> 'portfolio_orderby',
		'title'		=> __('Order By', 'themify'),
		'description'	=> '',
		'type'		=> 'dropdown',
		'meta'		=> array(
			array('name' => __('Date', 'themify'), 'value' => 'content', 'selected' => true),
			array('name' => __('Random', 'themify'), 'value' => 'rand'),
			array('name' => __('Author', 'themify'), 'value' => 'author'),
			array('name' => __('Post Title', 'themify'), 'value' => 'title'),
			array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
			array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
			array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
			array('name' => __('Post ID', 'themify'), 'value' => 'ID')
		)
	),
	// Post Layout
	array(
		  'name' 		=> 'portfolio_layout',
		  'title'		=> __('Portfolio Layout', 'themify'),
		  'description'	=> '',
		  'type'		=> 'layout',
		  'show_title' => true,
		  'meta'		=> array(
				array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'selected' => true),
				array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify')),
				array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
				array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
			)
		),
	// Posts Per Page
	array(
		  'name' 		=> 'portfolio_posts_per_page',
		  'title'		=> __('Portfolios per page', 'themify'),
		  'description'	=> '',
		  'type'		=> 'textbox',
		  'meta'		=> array('size' => 'small')
		),
	
	// Display Content
	array(
		  'name' 		=> 'portfolio_display_content',
		  'title'		=> __('Display Content', 'themify'),
		  'description'	=> '',
		  'type'		=> 'dropdown',
		  'meta'		=> array(
								array('name' => __('Full Content', 'themify'),'value'=>'content','selected'=>true),
		  						array('name' => __('Excerpt', 'themify'),'value'=>'excerpt'),
								array('name' => __('None', 'themify'),'value'=>'none')
							)
		),
	// Featured Image Size
	array(
		'name'	=>	'portfolio_feature_size_page',
		'title'	=>	__('Image Size', 'themify'),
		'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="admin.php?page=themify_regenerate-thumbnails">Regenerated</a>', 'themify'),
		'type'		 =>	'featimgdropdown'
		),
	// Image Width
	array(
		  'name' 		=> 'portfolio_image_width',	
		  'title' 		=> __('Image Width', 'themify'), 
		  'description' => '', 				
		  'type' 		=> 'textbox',			
		  'meta'		=> array('size'=>'small')			
		),
	// Image Height
	array(
		  'name' 		=> 'portfolio_image_height',	
		  'title' 		=> __('Image Height', 'themify'), 
		  'description' => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'), 				
		  'type' 		=> 'textbox',			
		  'meta'		=> array('size'=>'small')			
		),
	// Hide Title
	array(
		  'name' 		=> 'portfolio_hide_title',
		  'title'		=> __('Hide Portfolio Title', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)
		),
	// Unlink Post Title
	array(
		  'name' 		=> 'portfolio_unlink_title',	
		  'title' 		=> __('Unlink Portfolio Title', 'themify'), 	
		  'description' => __('Unlink portfolio title (it will display the post title without link)', 'themify'), 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)			
		),
	// Hide Post Date
	array(
		  'name' 		=> 'portfolio_hide_date',
		  'title'		=> __('Hide Portfolio Date', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
		  						array('value' => 'default', 'name' => '', 'selected' => true),
								array('value' => 'yes', 'name' => __('Yes', 'themify')),
								array('value' => 'no',	'name' => __('No', 'themify'))
							)
		),
	// Hide Post Meta
	array(
		'name' 		=> 'portfolio_hide_meta_all',
		'title' 	=> __('Hide Portfolio Meta', 'themify'), 	
		'description' => '', 				
		'type' 		=> 'dropdown',			
		'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)
	),
	// Hide Post Image
	array(
		  'name' 		=> 'portfolio_hide_image',	
		  'title' 		=> __('Hide Featured Image', 'themify'), 	
		  'description' => '', 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)
	),
	// Unlink Post Image
	array(
		  'name' 		=> 'portfolio_unlink_image',	
		  'title' 		=> __('Unlink Featured Image', 'themify'), 	
		  'description' => __('Display the Featured Image without link', 'themify'), 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)			
	),
	// Page Navigation Visibility
	array(
		  'name' 		=> 'portfolio_hide_navigation',
		  'title'		=> __('Hide Page Navigation', 'themify'),
		  'description'	=> '',
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
			)
		)
);
	
	/** Portfolio Meta Box Options */
$portfolio_meta_box = array(
	// Content Width
	array(
		'name'=> 'content_width',
		'title' => __('Content Width', 'themify'),
		'description' => '',
		'type' => 'layout',
		'show_title' => true,
		'meta' => array(
			array(
				'value' => 'default_width',
				'img' => 'themify/img/default.png',
				'selected' => true,
				'title' => __( 'Default', 'themify' )
			),
			array(
				'value' => 'full_width',
				'img' => 'themify/img/fullwidth.png',
				'title' => __( 'Fullwidth', 'themify' )
			)
		)
	),
	// Post Image
	array(
		'name' 		=> "post_image",
		'title' 		=> __('Featured Image', 'themify'),
		'description' => '',
		"type" 		=> "image",
		'meta'		=> array(),
		'toggle'	=> array('media-image-toggle')
	),
	// Featured Image Size
	array(
		'name'	=>	'feature_size',
		'title'	=>	__('Image Size', 'themify'),
		'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="admin.php?page=themify_regenerate-thumbnails">Regenerated</a>', 'themify'),
		'type'		 =>	'featimgdropdown',
		'toggle'	=> array('media-image-toggle')
		),
	// Multi field: Image Dimension
	array(
		'type' => 'multi',
		'name' => 'image_dimensions',
		'title' => __('Image Dimension', 'themify'),
		'meta' => array(
			'fields' => array(
				// Image Width
				array(
					'name' => 'image_width',	
					'label' => __('width', 'themify'), 
					'description' => '',
					'type' => 'textbox',			
					'meta' => array('size'=>'small'),
					'before' => '',
					'after' => '',
				),
				// Image Height
				array(
					'name' => 'image_height',
					'label' => __('height', 'themify'),
					'type' => 'textbox',						
					'meta' => array('size'=>'small'),
					'before' => '',
					'after' => '',
				),
			),
			'description' => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'), 	
			'before' => '',
			'after' => '',
			'separator' => ''
		),
		'toggle'	=> array('media-image-toggle')
	),
	// Gallery Shortcode
	array(
		'name' 		=> 'gallery_shortcode',
		'title' 	=> __('Gallery', 'themify'),
		'description' => '',			
		'type' 		=> 'gallery_shortcode',			
		'toggle'	=> 'media-gallery-toggle'
	),
	// Select Background Gallery
	array(
		  "name" 		=> "background_gallery",
		  "title"		=> __('Background Gallery', 'themify'),
		  "description"	=> __('Select gallery to show on background', 'themify'),
		  "type" 		=> "dropdown",
		  "meta"		=> $themify_bg_gallery->get_backgrounds()
	),
	// Hide Post Title
	array(
		  'name' 		=> "hide_post_title",	
		  'title' 		=> __('Hide Portfolio Title', 'themify'), 	
		  'description' => '',		
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			)			
		),
	// Unlink Post Title
	array(
		  'name' 		=> "unlink_post_title",	
		  'title' 		=> __('Unlink Portfolio Title', 'themify'), 	
		  'description' => __('Display the portfolio title without link', 'themify'), 				
		  'type' 		=> 'dropdown',			
		  'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)
	),
	// Hide Post Meta
	array(
		"name" 		=> "hide_post_meta",
		"title"		=> __('Hide Post Meta', 'themify'),
		"description"	=> "",
		"type" 		=> "dropdown",
		"meta"		=> array(
			array("value" => "default", "name" => "", "selected" => true),
			array("value" => "yes", 'name' => __('Yes', 'themify')),
			array("value" => "no",	'name' => __('No', 'themify'))
		)
	),
	// Hide Post Date
	array(
		'name' 		=> "hide_post_date",	
		'title' 	=> __('Hide Portfolio Date', 'themify'), 	
		'description' => '', 				
		'type' 		=> 'dropdown',			
		'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		)
	),
	// Hide Post Image
	array(
		'name' 		=> "hide_post_image",	
		'title' 	=> __('Hide Featured Image', 'themify'), 	
		'description' => '', 				
		'type' 		=> 'dropdown',			
		'meta'		=> array(
			array('value' => 'default', 'name' => '', 'selected' => true),
			array('value' => 'yes', 'name' => __('Yes', 'themify')),
			array('value' => 'no',	'name' => __('No', 'themify'))
		),
		'toggle'	=> array('media-image-toggle')
	),
	// Unlink Post Image
	array(
		'name' 		=> "unlink_post_image",	
		'title' 		=> __('Unlink Featured Image', 'themify'), 	
		'description' => __('Display the Featured Image without link', 'themify'), 				
		'type' 		=> 'dropdown',			
		'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
		),
		'toggle'	=> array('media-image-toggle')
	),
	// External Link
	array(
		'name' 		=> 'external_link',	
		'title' 		=> __('External Link', 'themify'), 	
		'description' => __('Link Featured Image and Post Title to external URL', 'themify'), 				
		'type' 		=> 'textbox',			
		'meta'		=> array()
	),
	// Lightbox Link + Zoom icon
	array(
		'name' 	=> '_multi_lightbox_link',	
		'title' => __('Lightbox Link', 'themify'), 	
		'description' => '', 				
		'type' 	=> 'multi',			
		'meta'	=> array(
			'fields' => array(
		  		// Lightbox link field
		  		array(
					'name' 	=> 'lightbox_link',
					'label' => '',
					'description' => __('Link Featured Image and Post Title to lightbox image, video or iframe URL <br/>(<a href="http://themify.me/docs/lightbox">learn more</a>)', 'themify'),
					'type' 	=> 'textbox',
					'meta'	=> array(),
					'before' => '',
					'after' => '',
				)
			),
			'description' => '',
			'before' => '',
			'after' => '',
			'separator' => ''
			)
		),
);
								 
///////////////////////////////////////
// Build Write Panels
///////////////////////////////////////
themify_build_write_panels(array(
	array(
		 "name"		=> __('Post Options', 'themify'), // Name displayed in box
			'id' => 'post-options',
		 "options"	=> $post_meta_box_options, 	// Field options
		 "pages"	=> "post"					// Pages to show write panel
		 ),
	array(
		 "name"		=> __('Page Options', 'themify'),	
			'id' => 'page-options',
		 "options"	=> $page_meta_box_options, 		
		 "pages"	=> "page"
		 ),
	array(
		'name'		=> __('Query Posts', 'themify'),	
			'id' => 'query-posts',
		'options'	=> $query_post_meta_box, 		
		'pages'	=> 'page'
	),
	array(
		'name'		=> __('Query Portfolios', 'themify'),	
			'id' => 'query-portfolio',
		'options'	=> $query_portfolio_meta_box, 		
		'pages'	=> 'page'
	),
	array(
		'name'		=> __('Portfolio Options', 'themify'),			// Name displayed in box
			'id' => 'portfolio-options',
		'options'	=> $portfolio_meta_box, 	// Field options
		'pages'	=> 'portfolio'					// Pages to show write panel
	)
));

/* 	Custom Functions
/***************************************************************************/
	
	///////////////////////////////////////
	// Enable WordPress feature image
	///////////////////////////////////////
	add_theme_support( 'post-thumbnails' );
	remove_post_type_support( 'page', 'thumbnail' );
	
	// Register Custom Menu Function
	function themify_register_custom_nav() {
		if (function_exists('register_nav_menus')) {
			register_nav_menus( array(
				'main-nav' => __( 'Main Navigation', 'themify' )
			) );
		}
	}
	
	// Register Custom Menu Function - Action
	add_action('init', 'themify_register_custom_nav');
	
	// Default Main Nav Function
	function themify_default_main_nav() {
		echo '<ul id="main-nav" class="clearfix">';
		wp_list_pages('title_li=');
		echo '</ul>';
	}

	// Register Sidebars
	if ( function_exists('register_sidebar') ) {
		register_sidebar(array(
			'name' => __('Sidebar', 'themify'),
			'id' => 'sidebar-main',
			'before_widget' => '<div class="widgetwrap"><div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div></div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		register_sidebar(array(
			'name' => __('Header Widget', 'themify'),
			'id' => 'header-widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		register_sidebar(array(
			'name' => __('Social Widget', 'themify'),
			'id' => 'social-widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<strong class="widgettitle">',
			'after_title' => '</strong>',
		));
	}

	// Footer Sidebars
	themify_register_grouped_widgets();

	/**
	 * Generates opening content wrapper specific for this theme
	 * @since 1.2.0
	 */
	function themify_theme_wc_compatibility_sidebar_before() {
		if( !themify_is_woocommerce_active() || !is_woocommerce() ) return;
		?>
		<!-- contentwrap -->
		<div id="contentwrap">
		<?php
	}
	/**
	 * Generates closing content wrapper specific for this theme
	 * @since 1.2.0
	 */
	function themify_theme_wc_compatibility_sidebar_after() {
		if( !themify_is_woocommerce_active() || !is_woocommerce() ) return;
		?>
		</div>
		<!-- /contentwrap -->
		<?php
		global $themify;
		themify_wc_compatibility_sidebar();
	}
	remove_action( 'themify_content_after', 'themify_wc_compatibility_sidebar', 10);
	add_action( 'themify_content_before', 'themify_theme_wc_compatibility_sidebar_before', 10);
	add_action( 'themify_content_after', 'themify_theme_wc_compatibility_sidebar_after', 10);

if( ! function_exists('themify_theme_comment') ) {
	/**
	 * Custom Theme Comment
	 * @param object $comment Current comment.
	 * @param array $args Parameters for comment reply link.
	 * @param int $depth Maximum comment nesting depth.
	 * @since 1.0.0
	 */
	function themify_theme_comment($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment; 

	?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<p class="comment-author">
				<?php echo get_avatar($comment, $size = '36'); ?>
				<?php printf('<cite>%s</cite>', get_comment_author_link()) ?><br />
			</p>
			<div class="commententry">
				<?php if ($comment->comment_approved == '0') : ?>
				<p><em><?php _e('Your comment is awaiting moderation.', 'themify') ?></em></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			
			<div class="comment-meta-wrap">
				<div class="line"></div>
				<div class="comment-meta">
					<small class="comment-time"><?php echo apply_filters('themify_comment_date', sprintf( __('%s ago', 'themify'), human_time_diff(get_comment_time('U'), current_time('timestamp')))); ?></small>
					 &sdot; 
					<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment', 'depth' => $depth, 'reply_text' => __( 'Reply', 'themify' ), 'max_depth' => $args['max_depth']))) ?>
					<?php edit_comment_link( __('Edit', 'themify'),' &sdot; [',']') ?>
				</div>
			</div>
	<?php
	}
}
	?>
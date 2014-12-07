<?php
/**
 * Theme Class
 *
 * Classes that provides special functions for the theme front end and admin.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('Themify_Portfolio')){
/**
 * Themify_Portfolio
 * Class for filtering and sorting portfolios in admin.
 *
 * @class 		Themify_Portfolio
 * @author 		Themify
 */
class Themify_Portfolio {

	public $post_type = '';
	public $taxonomies;
	static $run_once = true;
	
	function __construct($args = array()) {
		$defaults = array(
			'prefix' => '',
			'post_type' => ''
		);
		$args = wp_parse_args($args, $defaults);
		$this->post_type = $args['post_type'];
		$this->manage_and_filter();
	}

	/**
	 * Trigger at the end of __construct of this shortcode
	 */
	function manage_and_filter(){
		if(is_admin()){

			add_action( 'load-edit.php', array(&$this, "{$this->post_type}_load") );
		}
	}

	/**
	 * Add columns when filtering posts in edit.php
	 */
	public function add_columns( $taxonomies ) {
		return array_merge( $taxonomies, $this->taxonomies );
	}

	/**
	 * Filter request to sort
	 */
	function portfolio_load() {
		add_action( current_filter(), array( $this, 'setup_vars' ), 20 );
		add_action( 'restrict_manage_posts', array( $this, 'get_select' ) );
		add_filter( "manage_taxonomies_for_{$this->post_type}_columns", array( $this, 'add_columns' ) );
	}

	/**
	 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
	 * @param $category
	 * @param $post_type
	 * @return array
	 */
	function parse_category_args($category, $post_type) {
		if( 'all' != $category){
			$tax_query_terms = explode(',', $category);
			if(preg_match('#[a-z]#', $category)){
				return array( array(
					'taxonomy' => $post_type . '-category',
					'field' => 'slug',
					'terms' => $tax_query_terms
				));
			} else {
				return array( array(
					'taxonomy' => $post_type . '-category',
					'field' => 'id',
					'terms' => $tax_query_terms
				));
			}
		}
	}
	
	/**
	* Select form element to filter the post list
	* @return string HTML
	*/
	public function get_select() {
		if(!self::$run_once){
			return;
		}
		self::$run_once = false;
		$html = '';
		foreach ( $this->taxonomies as $tax ) {
			$options = sprintf('<option value="">%s %s</option>',
            	__( 'View All', 'themify' ),
				get_taxonomy($tax)->label
			);
			$class = is_taxonomy_hierarchical( $tax ) ? ' class="level-0"' : '';
			foreach ( get_terms( $tax ) as $taxon ) {
				$options .= sprintf( '<option %s%s value="%s">%s%s</option>',
					isset( $_GET[$tax] ) ? selected( $taxon->slug, $_GET[$tax], false ) : '',
					'0' !== $taxon->parent ? ' class="level-1"' : $class,
					$taxon->slug,
					'0' !== $taxon->parent ? str_repeat( '&nbsp;', 3 ) : '',
					"{$taxon->name} ({$taxon->count})"
				);
			}
			$html .= sprintf('<select name="%s" id="%s" class="postform">%s</select>', $tax, $tax, $options);
		}
        return print $html;
    }
	/**
	 * Setup vars when filtering posts in edit.php
	 */
	public function setup_vars() {
		$this->post_type  = get_current_screen()->post_type;
		$this->taxonomies = array_diff(
			get_object_taxonomies( $this->post_type ),
			get_taxonomies( array( 'show_admin_column' => 'false' ) )
		);
	}
}// class end
}// end if class exists

if(!class_exists('Themify_ThemeClass')) {
/**
 * Themify_ThemeClass
 * Class for theme front end
 *
 * @class 		Themify_ThemeClass
 * @author 		Themify
 */
class Themify_ThemeClass {
	
	// Custom Post Types
	static $portfolio = 'portfolio';
	
	function __construct() {
			
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'themify_post_types', array( $this, 'extend_post_types' ) );

		$class_name = apply_filters( 'themify_theme_class_type', 'Themify_' . ucwords( self::$portfolio ) );
		$new_class = new $class_name
		( array( 'post_type' => self::$portfolio ) );
	}
	
	function register() {
		/**
		 * @var array Custom Post Types to create with its plural and singular forms
		 */
		$cpts = array(
			self::$portfolio => array(
				'plural' => __('Portfolios', 'themify'),
				'singular' => __('Portfolio', 'themify'),
				'rewrite' => themify_check('themify_portfolio_slug')? themify_get('themify_portfolio_slug') : apply_filters('themify_portfolio_rewrite', 'project')
			)
		);
		$position = 52;
		foreach( $cpts as $key => $cpt ){
			register_post_type( $key, array(
				'labels' => array(
					'name' => $cpt['plural'],
					'singular_name' => $cpt['singular'],
					'add_new' => __( 'Add New', 'themify' ),
					'add_new_item' => sprintf(__( 'Add New %s', 'themify' ), $cpt['singular']),
					'edit_item' => sprintf(__( 'Edit %s', 'themify' ), $cpt['singular']),
					'new_item' => sprintf(__( 'New %s', 'themify' ), $cpt['singular']),
					'view_item' => sprintf(__( 'View %s', 'themify' ), $cpt['singular']),
					'search_items' => sprintf(__( 'Search %s', 'themify' ), $cpt['plural']),
					'not_found' => sprintf(__( 'No %s found', 'themify' ), $cpt['plural']),
					'not_found_in_trash' => sprintf(__( 'No %s found in Trash', 'themify' ), $cpt['plural']),
					'menu_name' => $cpt['plural']
				),
				'supports' => isset($cpt['supports'])? $cpt['supports'] : array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
				'menu_position' => $position++,
				'hierarchical' => false,
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'rewrite' => array( 'slug' => isset($cpt['rewrite'])? $cpt['rewrite']: strtolower($cpt['singular']) ),
				'query_var' => true,
				'can_export' => true,
				'capability_type' => 'post'
			));
			register_taxonomy( $key.'-category', array($key), array(
				'labels' => array(
					'name' => sprintf(__( '%s Categories', 'themify' ), $cpt['singular']),
					'singular_name' => sprintf(__( '%s Category', 'themify' ), $cpt['singular']),
					'search_items' => sprintf(__( 'Search %s Categories', 'themify' ), $cpt['singular']),
					'popular_items' => sprintf(__( 'Popular %s Categories', 'themify' ), $cpt['singular']),
					'all_items' => sprintf(__( 'All Categories', 'themify' ), $cpt['singular']),
					'parent_item' => sprintf(__( 'Parent %s Category', 'themify' ), $cpt['singular']),
					'parent_item_colon' => sprintf(__( 'Parent %s Category:', 'themify' ), $cpt['singular']),
					'edit_item' => sprintf(__( 'Edit %s Category', 'themify' ), $cpt['singular']),
					'update_item' => sprintf(__( 'Update %s Category', 'themify' ), $cpt['singular']),
					'add_new_item' => sprintf(__( 'Add New %s Category', 'themify' ), $cpt['singular']),
					'new_item_name' => sprintf(__( 'New %s Category', 'themify' ), $cpt['singular']),
					'separate_items_with_commas' => sprintf(__( 'Separate %s Category with commas', 'themify' ), $cpt['singular']),
					'add_or_remove_items' => sprintf(__( 'Add or remove %s Category', 'themify' ), $cpt['singular']),
					'choose_from_most_used' => sprintf(__( 'Choose from the most used %s Category', 'themify' ), $cpt['singular']),
					'menu_name' => sprintf(__( '%s Category', 'themify' ), $cpt['singular']),
				),
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => true,
				'query_var' => true
			));
			add_filter('manage_edit-'.$key.'-category_columns', array(&$this, 'taxonomy_header'), 10, 2);
			add_filter('manage_'.$key.'-category_custom_column', array(&$this, 'taxonomy_column_id'), 10, 3);
		}
	}

	/**
	 * Includes this custom post to array of cpts managed by Themify
	 * @param Array
	 * @return Array
	 */
	function extend_post_types($types){
		return array_merge($types, array(self::$portfolio));
	}
	
	/**
	 * Display an additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_header($cat_columns){
	    $cat_columns['cat_id'] = 'ID';
	    return $cat_columns;
	}
	/**
	 * Display ID in additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_column_id($null, $column, $termid){
		return $termid;
	}

	/**
	 * Returns post category IDs concatenated in a string
	 * @param number Post ID
	 * @return string Category IDs
	 */
	public function get_categories_as_classes($post_id) {
		$categories = wp_get_post_categories($post_id);
		$class = '';
		foreach($categories as $cat)
			$class .= ' cat-'.$cat;
		return $class;
	}
	 
	/**
	 * Returns category description
	 * @return string
	 */
	function get_category_description() {
	 	$category_description = category_description();
		if ( !empty( $category_description ) ){
			return '<div class="category-description">' . $category_description . '</div>';
		}
	}
	
	/**
	 * Returns all IDs from the given taxonomy
	 * @param string $tax Taxonomy to retrieve terms from.
	 * @return array $term_ids Array of all taxonomy terms
	 */
	function get_all_terms_ids($tax = 'category') {
		if ( ! $term_ids = wp_cache_get( 'all_'.$tax.'_ids', $tax ) ) {
			$term_ids = get_terms( $tax, array('fields' => 'ids', 'get' => 'all') );
			wp_cache_add( 'all_'.$tax.'_ids', $term_ids, $tax );
		}
		return $term_ids;
	}

	/**
	 * Returns the image for the portfolio slider
	 * @param int $attachment_id Image attachment ID
	 * @param int $width Width of the returned image
	 * @param int $height Height of the returned image
	 * @param string $size Size of the returned image
	 * @return string
	 * @since 1.1.8
	 */
	function portfolio_image($attachment_id, $width, $height, $size = 'large') {
		$size = apply_filters( 'themify_portfolio_image_size', $size );
		if ( themify_check( 'setting-img_settings_use' ) ) {
			// Image Script is disabled, use WP image
			$html = wp_get_attachment_image( $attachment_id, $size );
		} else {
			// Image Script is enabled, use it to process image
			$img = wp_get_attachment_image_src($attachment_id, $size);
			$html = themify_get_image('ignore=true&src='.$img[0].'&w='.$width.'&h='.$height);
		}
		return apply_filters( 'themify_portfolio_image_html', $html, $attachment_id, $width, $height, $size );
	}
		
}// class end
}// end if class exists

?>
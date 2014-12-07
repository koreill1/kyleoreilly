<?php

/* 	Custom Modules
/***************************************************************************/
	
	///////////////////////////////////////////
	// Footer Slider Function
	///////////////////////////////////////////
	function themify_overlay_gallery($data=array()){
		$data = themify_get_data();

		$display_options = array(
			__('Fullscreen Swipe', 'themify') => 'photoswipe',
			__('Popup Lightbox', 'themify') => 'lightbox'
		);
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Gallery mode', 'themify') . ' </span>
						<select name="setting-overlay_gallery">';
						
						foreach($display_options as $option => $value){
							if($value == $data['setting-overlay_gallery']){
								$output .= '<option value="'.$value.'" selected="selected">'.$option.'</option>';
							} else {
								$output .= '<option value="'.$value.'">'.$option.'</option>';
							}
						}
								
		$output .= '	</select>
					</p>';
		return $output;
	}
	
	///////////////////////////////////////////
	// Footer Slider Function
	///////////////////////////////////////////
	function themify_footer_slider($data=array()){
		$data = themify_get_data();
		
		global $themify_bg_gallery;
		$galleries = $themify_bg_gallery->get_backgrounds();
		
		$auto_options = array(__('Yes', 'themify') => 'yes', __('No', 'themify') => 'no');
		$autotimeout_options = apply_filters('themify_footer_slider_autotimeout', array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
		if ( !$data['setting-footer_slider_auto'] ) $data['setting-footer_slider_auto'] = 'yes';
		if ( !$data['setting-footer_slider_autotimeout'] ) $data['setting-footer_slider_autotimeout'] = 5;
		$display_options = array(
			__('Image caption', 'themify') => 'excerpt',
			__('Image title', 'themify') => 'title',
			__('None', 'themify') => 'none'
		);
		$speed_options = apply_filters( 'themify_footer_slider_speed', array(
			__('Normal', 'themify') => 500,
			__('Fast', 'themify') => 300,
			__('Slow', 'themify') => 1000
		) );
		
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Default BG Gallery', 'themify') . ' </span>
						<select name="setting-default_bg_gallery">';
						foreach($galleries as $gallery){
							if($gallery['value'] == $data['setting-default_bg_gallery']){
								$output .= '<option value="'.$gallery['value'].'" selected="selected">'.$gallery['name'].'</option>';
							} else {
								$output .= '<option value="'.$gallery['value'].'">'.$gallery['name'].'</option>';
							}
						}
		$output .= '	</select>
					</p>';
		
		$output .= '<p>
						<span class="label">' . __('Show Caption', 'themify') . ' </span>
						<select name="setting-show_caption">';
						
						foreach($display_options as $option => $value){
							if($value == $data['setting-show_caption']){
								$output .= '<option value="'.$value.'" selected="selected">'.$option.'</option>';
							} else {
								$output .= '<option value="'.$value.'">'.$option.'</option>';
							}
						}
								
		$output .= '	</select>
						<br/>
						<span class="pushlabel"><small>' . __('Image caption can be inserted in WP Media lightbox panel', 'themify') . '</small></span>
					</p>';
					
		$bgg_image_sizes = themify_get_image_sizes_list();
		$output .= '<hr />
					<p>
						<span class="label">' . __('BG Gallery Image Size', 'themify') . '</span>
						<select name="setting-footer_slider_image_size">';
						foreach($bgg_image_sizes as $option){
							if($option['value'] == themify_get('setting-footer_slider_image_size')){
								$output .= '<option value="'.$option['value'].'" selected="selected">';
									$output .= $option['name'];
								$output .= '</option>';
							} else {
								$output .= '<option value="'.$option['value'].'">'.$option['name'].'</option>';
							}
						}
		$output .= '	</select>
						<br/>
						<span class="pushlabel"><small>' . __('Set the size of the images used by the BG Gallery', 'themify') . '</small></span>
					</p>';
					
		$output .= '<hr />
					<p>
						<span class="label">' . __('Autoplay', 'themify') . '</span>
						<select name="setting-footer_slider_auto">';
						foreach($auto_options as $option){
							if ( isset( $data['setting-footer_slider_auto'] ) && ( $option == $data['setting-footer_slider_auto'] ) ) {
								$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
							} else {
								$output .= '<option value="'.$option.'">'.$option.'</option>';
							}
						}		
		$output .= '	</select>
					</p>';
		
		$output .= '<p>
						<span class="label">' . __('Autoplay Timeout', 'themify') . '</span>
						<select name="setting-footer_slider_autotimeout">';
						foreach($autotimeout_options as $option){
							if($option == $data['setting-footer_slider_autotimeout']){
								$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
							} else {
								$output .= '<option value="'.$option.'">'.$option.'</option>';
							}
						}		
		$output .= '	</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Speed', 'themify') . '</span> 
						<select name="setting-footer_slider_speed">';
						foreach($speed_options as $name => $val){
							if ( isset( $data['setting-footer_slider_speed'] ) && ( $data['setting-footer_slider_speed'] == $val ) ) {
								$output .= '<option value="'.$val.'" selected="selected">'.$name.'</option>';	
							} else {
								$output .= '<option value="'.$val.'">'.$name.'</option>';	
							}	
						}
		$output .= '	</select>
					</p>';


		$key = 'setting-background_mode';

		$output .= '<p>';

			$output .= '<span class="label">' . __('Background Mode', 'themify') . '</span>';

				// Full Cover
				$output .= '<input ' . checked(isset($data[$key])? $data[$key] : 'cover', 'cover', false) . ' type="radio" id="'.$key.'_cover" name="'.$key.'" value="cover" /> ';
				$output .= '<label for="'.$key.'_cover">' . __('Full Cover', 'themify') . '</label>';

			$output .= '<span class="pushlabel">';

				// Best Fit
				$output .= '<input ' . checked( $data[$key], 'best-fit', false) . ' type="radio" id="'.$key.'_best-fit" name="'.$key.'" value="best-fit" /> ';
				$output .= '<label for="'.$key.'_best-fit">' . __('Best Fit', 'themify') . '</label>';

			$output .= '</span>';

		$output .= '</p>';

		return $output;
	}

	///////////////////////////////////////////
	// Default Page Layout Module - Action
	///////////////////////////////////////////
	function themify_default_page_layout($data=array()){
		$data = themify_get_data();
		
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify'), "selected" => true),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
		);
		
		$default_options = array(
								array('name'=>'','value'=>''),
								array('name'=>__('Yes', 'themify'),'value'=>'yes'),
								array('name'=>__('No', 'themify'),'value'=>'no')
								);
							 
		$val = isset( $data['setting-default_page_layout'] ) ? $data['setting-default_page_layout'] : '';

		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Page Sidebar Option', 'themify') . '</span>';
		foreach ( $options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		$output .= '<input type="hidden" name="setting-default_page_layout" class="val" value="'.$val.'" /></p>';
		$output .= '<p>
						<span class="label">' . __('Hide Title in All Pages', 'themify') . '</span>
						
						<select name="setting-hide_page_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-hide_page_title'] ) && ( $title_option['value'] == $data['setting-hide_page_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
						
						
		$output .=	'</select>
					</p>';
		if ( isset( $data['setting-comments_pages'] ) && $data['setting-comments_pages'] ) {
			$pages_checked = "checked='checked'";	
		}
		$output .= '<p><span class="label">' . __('Page Comments', 'themify') . '</span><label for="setting-comments_pages"><input type="checkbox" id="setting-comments_pages" name="setting-comments_pages" '.checked( themify_get( 'setting-comments_pages' ), 'on', false ).' /> ' . __('Disable comments in all Pages', 'themify') . '</label></p>';	
		
		return $output;													 
	}
	
	///////////////////////////////////////////
	// Default Index Layout Module - Action
	///////////////////////////////////////////
	function themify_default_layout($data=array()){
		$data = themify_get_data();
		
		if ( ! isset( $data['setting-default_more_text'] ) || '' == $data['setting-default_more_text'] ) {
			$more_text = __('More', 'themify');
		} else {
			$more_text = $data['setting-default_more_text'];
		}
		
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		$default_layout_options = array(
			array('name' => __('Full Content', 'themify'),'value'=>'content'),
			array('name' => __('Excerpt', 'themify'),'value'=>'excerpt'),
			array('name' => __('None', 'themify'),'value'=>'none')
		);
		$default_post_layout_options = array(
			array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'title' => __('List Post', 'themify'), "selected" => true),
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify')),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
			array('value' => 'list-large-image', 'img' => 'images/layout-icons/list-large-image.png', 'title' => __('List Large Image', 'themify')),
			array('value' => 'list-thumb-image', 'img' => 'images/layout-icons/list-thumb-image.png', 'title' => __('List Thumb Image', 'themify')),
			array('value' => 'grid2-thumb', 'img' => 'images/layout-icons/grid2-thumb.png', 'title' => __('Grid 2 Thumb', 'themify')),
		);
																 	 
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify'), "selected" => true),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
		);
						 
		$val = isset( $data['setting-default_layout'] ) ? $data['setting-default_layout'] : '';
		
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<div class="themify-info-link">' . __( 'Here you can set the <a href="http://themify.me/docs/default-layouts">Default Layouts</a> for WordPress archive post layout (category, search, archive, tag pages, etc.), single post layout (single post page), and the static Page layout. The default single post and page layout can be override individually on the post/page > edit > Themify Custom Panel.', 'themify' ) . '</div>';

		$output .= '<p>
						<span class="label">' . __('Index Sidebar Option', 'themify') . '</span>';
		foreach ( $options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_layout" class="val" value="'.$val.'" />';
		$output .= '</p>';
		$output .= '<p>
						<span class="label">' . __('Post Layout', 'themify') . '</span>';
						
		$val = isset( $data['setting-default_post_layout'] ) ? $data['setting-default_post_layout'] : '';
		
		foreach ( $default_post_layout_options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_post_layout" class="val" value="'.$val.'" />
					</p>
					<p>
						<span class="label">' . __('Content Display', 'themify') . '</span>  
						<select name="setting-default_layout_display">';
						foreach ( $default_layout_options as $layout_option ) {
							if ( isset( $data['setting-default_layout_display'] ) && ( $layout_option['value'] == $data['setting-default_layout_display'] ) ) {
								$output .= '<option selected="selected" value="'.$layout_option['value'].'">'.$layout_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$layout_option['value'].'">'.$layout_option['name'].'</option>';
							}
						}
		$output .=	'	</select>
					</p>
					
					<p>
						<span class="label">' . __('More Text', 'themify') . '</span>
						<input type="text" name="setting-default_more_text" value="'.$more_text.'">
<span class="pushlabel vertical-grouped"><label for="setting-excerpt_more"><input type="checkbox" value="1" id="setting-excerpt_more" name="setting-excerpt_more" '.checked( themify_get( 'setting-excerpt_more' ), 1, false ).'/> ' . __('Display more link button in excerpt mode as well.', 'themify') . '</label></span><br />
						<span class="pushlabel"><small>' . __('Note: more text is only available if display=content and the post has the <a href="http://themify.me/docs/more-tag" target="_blank">more tag</a>', 'themify') . '</small></span>
					</p>';
					
		/**
		 * Order & OrderBy Options
		 */
		if( function_exists( 'themify_post_sorting_options' ) ) 
			$output .= themify_post_sorting_options('setting-index_order', $data);
		
		/**
		 * Hide Post Title
		 */
		$output .=	'<p>
						<span class="label">' . __('Hide Post Title', 'themify') . '</span>
						
						<select name="setting-default_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_title'] ) && ( $title_option['value'] == $data['setting-default_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Unlink Post Title', 'themify') . '</span>
						
						<select name="setting-default_unlink_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_unlink_post_title'] ) && ( $title_option['value'] == $data['setting-default_unlink_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Hide Post Meta', 'themify') . '</span>

						<select name="setting-default_post_meta">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_meta'] ) && ( $title_option['value'] == $data['setting-default_post_meta'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Hide Post Date', 'themify') . '</span>
						
						<select name="setting-default_post_date">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_date'] ) && ( $title_option['value'] == $data['setting-default_post_date'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Auto Featured Image', 'themify') . '</span>

						<label for="setting-auto_featured_image"><input type="checkbox" value="1" id="setting-auto_featured_image" name="setting-auto_featured_image" '.checked( themify_get( 'setting-auto_featured_image' ), 1, false ).'/> ' . __('If no featured image is specified, display first image in content.', 'themify') . '</label>
					</p>

					<p>
						<span class="label">' . __('Hide Featured Image', 'themify') . '</span>

						<select name="setting-default_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_image'] ) && ( $title_option['value'] == $data['setting-default_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Unlink Featured Image', 'themify') . '</span>
						
						<select name="setting-default_unlink_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_unlink_post_image'] ) && ( $title_option['value'] == $data['setting-default_unlink_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>';
		
		$output .= themify_feature_image_sizes_select('image_post_feature_size');
		$data = themify_get_data();
		$options = array( 'left', 'right' );

		$output .= '<p>
						<span class="label">' . __('Image Size', 'themify') . '</span>  
						<input type="text" class="width2" name="setting-image_post_width" value="' . themify_get( 'setting-image_post_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>  
						<input type="text" class="width2" name="setting-image_post_height" value="' . themify_get( 'setting-image_post_height' ) . '" /> ' . __('height', 'themify') . ' <small>(px)</small>
						<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
					</p>
					<p>
						<span class="label">' . __('Featured Image Alignment', 'themify') . '</span>
						<select name="setting-image_post_align">
							<option></option>';
		foreach ( $options as $option ) {
			if ( isset( $data['setting-image_post_align'] ) && ( $option == $data['setting-image_post_align'] ) ) {
				$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
			} else {
				$output .= '<option value="'.$option.'">'.$option.'</option>';
			}
		}
		$output .=	'</select>
					</p>
					';
		return $output;
	}
	
	///////////////////////////////////////////
	// Default ' . __('Post Layout', 'themify') . '
	///////////////////////////////////////////
	function themify_default_post_layout($data=array()){
		
		$data = themify_get_data();
		
		$default_options = array(
								array('name'=>'','value'=>''),
								array('name'=>__('Yes', 'themify'),'value'=>'yes'),
								array('name'=>__('No', 'themify'),'value'=>'no')
								);
		
		$val = isset( $data['setting-default_page_post_layout'] ) ? $data['setting-default_page_post_layout'] : '';

		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Post Sidebar Option', 'themify') . '</span>';
						
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify'), "selected" => true),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
		);
										
		foreach ( $options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_page_post_layout" class="val" value="'.$val.'" />';
		$output .= '</p>
					<p>
						<span class="label">' . __('Hide Post Title', 'themify') . '</span>
						
						<select name="setting-default_page_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_title'] ) && ( $title_option['value'] == $data['setting-default_page_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Unlink Post Title', 'themify') . '</span>
						
						<select name="setting-default_page_unlink_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_unlink_post_title'] ) && ( $title_option['value'] == $data['setting-default_page_unlink_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Hide Post Meta', 'themify') . '</span>
						
						<select name="setting-default_page_post_meta">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_meta'] ) && ( $title_option['value'] == $data['setting-default_page_post_meta'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Hide Post Date', 'themify') . '</span>
						
						<select name="setting-default_page_post_date">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_date'] ) && ( $title_option['value'] == $data['setting-default_page_post_date'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Hide Featured Image', 'themify') . '</span>
						
						<select name="setting-default_page_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_image'] ) && ( $title_option['value'] == $data['setting-default_page_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p><p>
						<span class="label">' . __('Unlink Featured Image', 'themify') . '</span>
						
						<select name="setting-default_page_unlink_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_unlink_post_image'] ) && ( $title_option['value'] == $data['setting-default_page_unlink_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .= '</select></p>';
		$output .= themify_feature_image_sizes_select('image_post_single_feature_size');
		$output .= '<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
						<input type="text" class="width2" name="setting-image_post_single_width" value="' . themify_get( 'setting-image_post_single_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>  
						<input type="text" class="width2" name="setting-image_post_single_height" value="' . themify_get( 'setting-image_post_single_height' ) . '" /> ' . __('height', 'themify') . ' <small>(px)</small>
						<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
					</p>
					<p>
						<span class="label">' . __('Featured Image Alignment', 'themify') . '</span>
						<select name="setting-image_post_single_align">
							<option></option>';
		$options = array( 'left', 'right' );
		foreach ( $options as $option ) {
			if ( isset( $data['setting-image_post_single_align'] ) && ( $option == $data['setting-image_post_single_align'] ) ) {
				$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
			} else {
				$output .= '<option value="'.$option.'">'.$option.'</option>';
			}
		}
		$output .=	'</select>
					</p>';
		if ( themify_check( 'setting-comments_posts' ) ) {
			$comments_posts_checked = "checked='checked'";	
		}
		$output .= '<p><span class="label">' . __('Post Comments', 'themify') . '</span><label for="setting-comments_posts"><input type="checkbox" id="setting-comments_posts" name="setting-comments_posts" '.checked( themify_get( 'setting-comments_posts' ), 'on', false ).' /> ' . __('Disable comments in all Posts', 'themify') . '</label></p>';	
		
		if ( themify_check( 'setting-post_author_box' ) ) {
			$author_box_checked = "checked='checked'";	
		}
		$output .= '<p><span class="label">' . __('Show Author Box', 'themify') . '</span><label for="setting-post_author_box"><input type="checkbox" id="setting-post_author_box" name="setting-post_author_box" '.checked( themify_get( 'setting-post_author_box' ), 'on', false ).' /> ' . __('Show author box in all Posts', 'themify') . '</label></p>';

		// Post Navigation
		$pre = 'setting-post_nav_';
		$output .= '
		<p>
			<span class="label">' . __('Post Navigation', 'themify') . '</span>
			<label for="'.$pre.'disable">
				<input type="checkbox" id="'.$pre.'disable" name="'.$pre.'disable" '. checked( themify_get( $pre.'disable' ), 'on', false ) .'/> ' . __('Remove Post Navigation', 'themify') . '
				</label>
		<span class="pushlabel vertical-grouped">
				<label for="'.$pre.'same_cat">
					<input type="checkbox" id="'.$pre.'same_cat" name="'.$pre.'same_cat" '. checked( themify_get( $pre.'same_cat' ), 'on', false ) .'/> ' . __('Show only posts in the same category', 'themify') . '
				</label>
			</span>
		</p>';	

		return $output;
	}

if(!function_exists('themify_default_portfolio_single_layout')){
	/**
	 * Default Single Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_single_layout($data=array()){
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();
		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_single_';
		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		/**
		 * Sidebar Layout
		 * @var string
		 */
		$layout = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
		/**
		 * Sidebar Layout Options
		 * @var array
		 */
		$sidebar_options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'selected' => true, 'title' => __('No Sidebar', 'themify'))
		);
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Portfolio Sidebar Option', 'themify') . '</span>';
						foreach($sidebar_options as $option){
							if ( ( '' == $layout || !$layout || ! isset( $layout ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
								$layout = $option['value'];
							}
							if($layout == $option['value']){ 
								$class = 'selected';
							} else {
								$class = '';
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
						}
						$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$layout.'" />
					</p>';
		$output .= '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category', true) . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date') . '
						</select>
					</p>';
		/**
		 * Image Dimensions
		 */
		$output .= '
			<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
				<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.$data[$prefix.'image_post_width'].'" /> ' . __('width', 'themify') . ' <small>(px)</small>
				<input type="text" class="width2" name="'.$prefix.'image_post_height" value="'.$data[$prefix.'image_post_height'].'" /> ' . __('height', 'themify') . ' <small>(px)</small>
			</p>';

		return $output;
	}
}

if(!function_exists('themify_default_portfolio_index_layout')){
	/**
	 * Default Index Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_index_layout($data=array()){
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();
		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_index_';
		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		/**
		 * Sidebar Layout
		 * @var string
		 */
		$layout = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
		/**
		 * Sidebar Layout Options
		 * @var array
		 */
		$sidebar_options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'selected' => true, 'title' => __('No Sidebar', 'themify')),
		);
		/**
		 * Post Layout Options
		 * @var array
		 */
		$post_layout_options = array(
			array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'title' => __('List Post', 'themify'), "selected" => true),
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify')),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
		);
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Portfolio Sidebar Option', 'themify') . '</span>';
						foreach($sidebar_options as $option){
							if ( ( '' == $layout || !$layout || ! isset( $layout ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$layout = $option['value'];
							}
							if($layout == $option['value']){
								$class = 'selected';
							} else {
								$class = '';
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}
						$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$layout.'" />';
		$output .= '</p>';
		/**
		 * Post Layout
		 */
		$output .= '<p>
						<span class="label">' . __('Portfolio Layout', 'themify') . '</span>';

						$val = isset( $data[$prefix.'post_layout'] ) ? $data[$prefix.'post_layout'] : '';

						foreach($post_layout_options as $option){
							if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$val = $option['value'];
							}
							if ( $val == $option['value'] ) {
								$class = "selected";
							} else {
								$class = "";
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}

		$output .= '	<input type="hidden" name="'.$prefix.'post_layout" class="val" value="'.$val.'" />
					</p>';
		/**
		 * Display Content
		 */
		$output .= '<p>
						<span class="label">' . __('Display Content', 'themify') . '</span>
						<select name="'.$prefix.'display">'.
							themify_options_module(array(
								array('name' => __('None', 'themify'),'value'=>'none'),
								array('name' => __('Full Content', 'themify'),'value'=>'content'),
								array('name' => __('Excerpt', 'themify'),'value'=>'excerpt')
							), $prefix.'display').'
						</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category', true) . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date', true) . '
						</select>
					</p>';
		/**
		 * Image Dimensions
		 */
		$output .= '<p>
						<span class="label">' . __('Image Size', 'themify') . '</span>
						<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.$data[$prefix.'image_post_width'].'" /> ' . __('width', 'themify') . ' <small>(px)</small>
						<input type="text" class="width2" name="'.$prefix.'image_post_height" value="'.$data[$prefix.'image_post_height'].'" /> ' . __('height', 'themify') . ' <small>(px)</small>
					</p>';
		return $output;
	}
}

if(!function_exists('themify_portfolio_slug')){
	/**
	 * Portfolio Slug
	 * @param array $data
	 * @return string
	 */
	function themify_portfolio_slug($data=array()){
		$data = themify_get_data();
		$portfolio_slug = isset($data['themify_portfolio_slug'])? $data['themify_portfolio_slug']: apply_filters('themify_portfolio_rewrite', 'project');
		return '
			<p>
				<span class="label">' . __('Portfolio Base Slug', 'themify') . '</span>  
				<input type="text" name="themify_portfolio_slug" value="'.$portfolio_slug.'" class="slug-rewrite">
				<br />
				<span class="pushlabel"><small>' . __('Use only lowercase letters, numbers, underscores and dashes.', 'themify') . '</small></span>
				<br />
				<span class="pushlabel"><small>' . sprintf(__('After changing this, go to <a href="%s">permalinks</a> and click "Save changes" to refresh them.', 'themify'), admin_url('options-permalink.php')) . '</small></span><br />
			</p>';
	}
}

function themify_portfolio_slider(){
	/**
	 * Associative array containing theme settings
	 * @var array
	 */
	$data = themify_get_data();
	/**
	 * Variable prefix key
	 * @var string
	 */
	$prefix = 'setting-portfolio_slider_';
	/**
	 * Basic default options '', 'yes', 'no'
	 * @var array
	 */
	$default_options = array(
		array('name'=>__('Yes', 'themify'), 'value'=>'yes'),
		array('name'=>__('No', 'themify'), 'value'=>'no')
	);
	$auto_options = array(
		__('4 Secs (default)', 'themify') => 4000,
		__('Off', 'themify') => 'off',
		__('1 Sec', 'themify') => 1000,
		__('2 Secs', 'themify') => 2000,
		__('3 Secs', 'themify') => 3000,
		__('4 Secs', 'themify') => 4000,
		__('5 Secs', 'themify') => 5000,
		__('6 Secs', 'themify') => 6000,
		__('7 Secs', 'themify') => 7000,
		__('8 Secs', 'themify') => 8000,
		__('9 Secs', 'themify') => 9000,
		__('10 Secs', 'themify')=> 10000
	);
	$speed_options = array(
		__('Fast', 'themify') => 500,
		__('Normal', 'themify') => 1000,
		__('Slow', 'themify') => 1500
	);
	$effect_options = array(
		array('name' => __('Slide', 'themify'), 'value' => 'slide'),
		array('name' => __('Fade', 'themify'), 'value' =>'fade')
	);
	
	/**
	 * HTML for settings panel
	 * @var string
	 */
	$output = '<p>
					<span class="label">' . __('Auto Play', 'themify') . '</span>
					<select name="'.$prefix.'autoplay">';
					foreach($auto_options as $name => $val){
						$output .= '<option value="'.$val.'" ' . selected($data[$prefix.'autoplay'], $data[$prefix.'autoplay']? $val: 4000, false) . '>'.$name.'</option>';
					}
	$output .= '	</select>
				</p>';
	$output .= '<p>
					<span class="label">' . __('Effect', 'themify') . '</span>
					<select name="'.$prefix.'effect">'.
					themify_options_module($effect_options, $prefix.'effect') . '
					</select>
				</p>';
	$output .= '<p>
					<span class="label">' . __('Transition Speed', 'themify') . '</span>
					<select name="'.$prefix.'transition_speed">';
					foreach($speed_options as $name => $val){
						$output .= '<option value="'.$val.'" ' . selected($data[$prefix.'transition_speed'], $data[$prefix.'transition_speed']? $val: 500, false) . '>'.$name.'</option>';
					}
	$output .= '	</select>
				</p>';

	return $output;
}
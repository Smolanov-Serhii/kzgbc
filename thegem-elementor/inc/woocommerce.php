<?php

function thegem_woocommerce_scripts() {
	if(thegem_is_plugin_active('woocommerce/woocommerce.php')) {
		wp_enqueue_style('thegem-woocommerce-minicart', get_template_directory_uri() . '/css/thegem-woocommerce-minicart.css');

		wp_register_style('thegem-woocommerce', get_template_directory_uri() . '/css/thegem-woocommerce.css');
		wp_register_style('thegem-woocommerce1', get_template_directory_uri() . '/css/thegem-woocommerce1.css');
		wp_register_style('thegem-woocommerce-temp', get_template_directory_uri() . '/css/thegem-woocommerce-temp.css');
		wp_register_style('thegem-woocommerce-custom', get_template_directory_uri() . '/css/thegem-woocommerce-custom.css');

		if (thegem_is_woocommerce_page() || thegem_is_wishlist_page()) {
			thegem_enqueue_woocommerce_styles();
		}
		if ( thegem_is_wishlist_page()) {
			wp_enqueue_style('yith-wcwl-user-main');
		}

		wp_register_script('thegem-checkout', get_template_directory_uri() . '/js/thegem-checkout.js', array('jquery'));
		wp_register_script('thegem-product-legacy-gallery', get_template_directory_uri() . '/js/thegem-product-legacy-gallery.js', array('jquery', 'jquery-carouFredSel'), '', true);
		wp_register_script('thegem-woocommerce', get_template_directory_uri() . '/js/thegem-woocommerce.js', array('jquery'), '', true);

		wp_register_script('thegem-product-gallery', get_template_directory_uri() . '/js/thegem-product-gallery.js', array('jquery', 'owl', 'owl-zoom'), false, true);
		wp_register_style('thegem-product-gallery', get_template_directory_uri() . '/css/thegem-product-gallery.css', array('owl'));

		$galleryScripts = array(
			'jquery', 'thegem-woocommerce', thegem_get_option('product_gallery') != 'legacy' ? 'thegem-product-gallery' : 'thegem-product-legacy-gallery',
		);

		wp_register_script('thegem-product-quick-view', get_template_directory_uri() . '/js/thegem-product-quick-view.js', $galleryScripts, '', true);

		wp_localize_script('thegem-woocommerce', 'thegem_woo_data', array(
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce' => wp_create_nonce('product_quick_view_ajax_security'),
		));
		if(is_woocommerce()) {
			wp_enqueue_script('thegem-woocommerce');
			if (thegem_get_option('products_pagination', 'normal') == 'scroll') {
				wp_enqueue_script('thegem-scroll-monitor');
			}

			if (in_array(thegem_get_option('products_pagination', 'normal'), array('more', 'scroll'))) {
				wp_enqueue_style('thegem-animations');
				wp_enqueue_script('thegem-items-animations');
			}

		}

		wp_register_style('thegem-accordion', THEGEM_ELEMENTOR_URL . '/widgets/accordion/assets/css/thegem-accordion.css', array(), null);
		wp_register_script('thegem-tta', THEGEM_ELEMENTOR_URL . '/widgets/accordion/assets/js/thegem-tta.js', array('jquery'), null, true);
		wp_register_script('thegem-accordion', THEGEM_ELEMENTOR_URL . '/widgets/accordion/assets/js/thegem-accordion.js', array('thegem-tta'), null, true);
		wp_register_style('thegem-tabs', THEGEM_ELEMENTOR_URL . '/widgets/tabs-tours/assets/css/thegem-tabs.css', array('thegem-accordion'), null);
		wp_register_script('thegem-tabs', THEGEM_ELEMENTOR_URL . '/widgets/tabs-tours/assets/js/thegem-tabs.js', array('thegem-accordion'), null, true);


	}
}
add_action('wp_enqueue_scripts', 'thegem_woocommerce_scripts');

function thegem_enqueue_woocommerce_styles() {
	wp_enqueue_style('thegem-woocommerce');
	wp_enqueue_style('thegem-woocommerce1');
	wp_enqueue_style('thegem-woocommerce-temp');
	wp_enqueue_style('thegem-woocommerce-custom');
}

function thegem_is_woocommerce_page() {
	return function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_product_category());
}

function thegem_get_cart_count() {
	return empty(WC()->cart) ? 0 : (thegem_get_option('cart_label_count') ? WC()->cart->cart_contents_count : sizeof(WC()->cart->get_cart()));
}

function thegem_is_wishlist_page() {
	return (function_exists('yith_wcwl_is_wishlist') && yith_wcwl_is_wishlist()) || (function_exists('yith_wcwl_is_wishlist_page') && yith_wcwl_is_wishlist_page());
}

add_action('add_meta_boxes', 'thegem_add_product_settings_boxes');
function thegem_add_product_settings_boxes() {
	add_meta_box('thegem_product_description_meta_box', esc_html__('Product Description', 'thegem'), 'thegem_product_description_settings_box', 'product', 'normal', 'high');
	add_meta_box('thegem_product_video_meta_box', esc_html__('Product Video', 'thegem'), 'thegem_product_video_settings_box', 'product', 'side', 'low');
}

function thegem_product_description_settings_box($post) {
	wp_nonce_field('thegem_product_description_settings_box', 'thegem_product_description_settings_box_nonce');
	$product_description = get_post_meta($post->ID, 'thegem_product_description', true);
?>
<div class="inside">
	<?php wp_editor(htmlspecialchars_decode($product_description), 'thegem_product_description', array(
			'textarea_name' => 'thegem_product_description',
			'quicktags' => array('buttons' => 'em,strong,link'),
			'tinymce' => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		)); ?>
</div>
<?php
}

function thegem_product_hover_settings_box($post) {
	wp_nonce_field('thegem_product_hover_settings_box', 'thegem_product_hover_settings_box_nonce');
	$product_hover = get_post_meta($post->ID, 'thegem_product_disable_hover', true);
?>
<div class="inside">
	<input name="thegem_product_disable_hover" type="checkbox" id="thegem_product_disable_hover" value="1" <?php checked($product_hover, 1); ?> />
	<label for="thegem_product_disable_hover"><?php esc_html_e('Disable hover with alternative product image', 'thegem'); ?></label>
</div>
<?php
}

function thegem_product_video_settings_box($post) {
	wp_nonce_field('thegem_product_video_settings_box', 'thegem_product_video_settings_box_nonce');

	$product_video_data = get_post_meta($post->ID, 'thegem_product_video', true);
	$product_video = thegem_get_sanitize_product_video_data($product_video_data);
	$video_background_types = array('' => __('None', 'thegem'), 'youtube' => __('YouTube', 'thegem'), 'vimeo' => __('Vimeo', 'thegem'), 'self' => __('Self-Hosted Video', 'thegem'));
	?>
	<div class="thegem-product-video">
		<div class="product-video-box visible">
			<label for="thegem_product_video_type"><?php esc_html_e('Video Type', 'thegem'); ?>:</label>
			<?php thegem_print_select_input($video_background_types, esc_attr($product_video['product_video_type']), 'thegem_product_video_type', 'thegem_product_video_type'); ?>
		</div>
		<div id="product-video-self" class="product-video-box">
			<label for="thegem_product_video_link"><?php esc_html_e('Link to video', 'thegem'); ?>:</label>
			<input type="text" name="thegem_product_video_link" id="thegem_product_video_link" value="<?php echo esc_attr($product_video['product_video_link']); ?>" class="video-select"/>
		</div>
		<div id="product-video-id" class="product-video-box">
			<label for="thegem_product_video_id"><?php esc_html_e('Video URL (for YouTube or Vimeo)', 'thegem'); ?>:</label>
			<input type="text" name="thegem_product_video_id" id="thegem_product_video_id" value="<?php echo esc_attr($product_video['product_video_id']); ?>"/>
		</div>
		<div id="product-video-thumb" class="product-video-box">
			<label for="thegem_product_video_thumb"><?php esc_html_e('Video Poster', 'thegem'); ?>:</label>
			<input type="text" name="thegem_product_video_thumb" id="thegem_product_video_thumb" value="<?php echo esc_attr($product_video['product_video_thumb']); ?>" class="picture-select"/>
		</div>
		<div class="product-video-box visible"><a href="#" id="remove-product-video">Remove product video</a></div>
	</div>
	<?php
}

function thegem_save_product_data($post_id) {
	if(!isset($_POST['thegem_product_description_settings_box_nonce'])) {
		return;
	}
	if(!wp_verify_nonce($_POST['thegem_product_description_settings_box_nonce'], 'thegem_product_description_settings_box')) {
		return;
	}

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if(isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
		if(!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {
		if(!current_user_can('edit_post', $post_id)) {
			return;
		}
	}
	if(isset($_POST['thegem_product_description'])) {
		update_post_meta($post_id, 'thegem_product_description', $_POST['thegem_product_description']);
	}

	if(!wp_verify_nonce($_POST['thegem_product_video_settings_box_nonce'], 'thegem_product_video_settings_box')) {
		return;
	}
	$product_video = thegem_get_sanitize_product_video_data( array(
		'product_video_type' => $_POST['thegem_product_video_type'],
		'product_video_link' => $_POST['thegem_product_video_link'],
		'product_video_id' => $_POST['thegem_product_video_id'],
		'product_video_thumb' => $_POST['thegem_product_video_thumb'],
	));
	update_post_meta($post_id, 'thegem_product_video', $product_video);

	//update_post_meta($post_id, 'thegem_product_disable_hover', isset($_POST['thegem_product_disable_hover']));
}
add_action('save_post', 'thegem_save_product_data');

add_filter('woocommerce_enqueue_styles', '__return_false');

function thegem_loop_shop_columns($count) {
	$item_data = array(
		'sidebar_position' => '',
	);
	$item_data = thegem_get_post_data($item_data, 'page', wc_get_page_id('shop'));
	$sidebar_position = thegem_check_array_value(array('', 'left', 'right'), $item_data['sidebar_position'], '');
	if(is_active_sidebar('shop-sidebar') && $sidebar_position) {
		return 3;
	}
	return 4;
}
add_filter('loop_shop_columns', 'thegem_loop_shop_columns');

function thegem_woocommerce_single_product_gallery() {
	global $post, $product;
	$product_gallery_data = thegem_get_output_product_gallery_data($product->get_id());
	if($product_gallery_data['product_gallery'] !== 'legacy') return ;
	wp_enqueue_style('thegem-hovers');
	wp_enqueue_script('thegem-product-legacy-gallery');
	$attachments_ids = array();
	if(has_post_thumbnail()) {
		$attachments_ids = array(get_post_thumbnail_id());
	}
	$attachments_ids = array_merge($attachments_ids, $product->get_gallery_image_ids());
	if('variable' === $product->get_type()) {
		foreach($product->get_available_variations() as $variation) {
			if(has_post_thumbnail($variation['variation_id'])) {
				$thumbnail_id = get_post_thumbnail_id($variation['variation_id']);
				if(!in_array($thumbnail_id, $attachments_ids)) {
					$attachments_ids[] = $thumbnail_id;
				}
			}
		}
	}
	if(empty($attachments_ids)) return ;
	$gallery_uid = uniqid();
	$preview_class = '';
	if (thegem_is_plugin_active('elementor/elementor.php') && \Elementor\Plugin::$instance->preview->is_preview_mode()) {
		$preview_class = ' gem-gallery-elementor-preview';
	}
	echo '<div class="preloader"><div class="preloader-spin"></div></div>';
	echo '<div class="gem-gallery gem-gallery-hover-default'.$preview_class.'">';
	foreach($attachments_ids as $attachments_id) {
		if(thegem_get_option('woocommerce_activate_images_sizes')) {
			$thumb_image_url = thegem_get_thumbnail_src($attachments_id, 'thegem-product-thumbnail');
			$preview_image_url = thegem_get_thumbnail_src($attachments_id, 'thegem-product-single');
		} else {
			$thumb_image_url = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail'));
			$preview_image_url = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_large_thumbnail_size', 'shop_single'));
		}
		$full_image_url = wp_get_attachment_image_src($attachments_id, 'full');
		?>
<div class="gem-gallery-item" data-image-id="<?php echo esc_attr($attachments_id); ?>">
	<div class="gem-gallery-item-image">
		<a href="<?php echo esc_url($preview_image_url[0]); ?>" data-fancybox-group="product-gallery-<?php echo esc_attr($gallery_uid); ?>" data-full-image-url="<?php echo esc_url($full_image_url[0]); ?>" data-elementor-open-lightbox="no">
			<svg width="20" height="10"><path d="M 0,10 Q 9,9 10,0 Q 11,9 20,10" /></svg>
			<img src="<?php echo esc_url($thumb_image_url[0]); ?>" alt="" class="img-responsive">
		</a>
	</div>
</div>
<?php
	}
	echo '</div>';
}

function thegem_woocommerce_single_product_gallery_alternative() {
	global $post, $product;
	$product_gallery_data = thegem_get_output_product_gallery_data($product->get_id());
	if($product_gallery_data['product_gallery'] !== 'enabled') return ;

	wp_enqueue_style( 'thegem-product-gallery' );
	wp_enqueue_script( 'thegem-product-gallery' );

	$attachments_ids = array();
	if ( has_post_thumbnail() ) {
		$attachments_ids = array( get_post_thumbnail_id() );
	}
	$attachments_ids = array_merge( $attachments_ids, $product->get_gallery_image_ids() );
	if ( 'variable' === $product->get_type() ) {
		foreach ( $product->get_available_variations() as $variation ) {
			if ( has_post_thumbnail( $variation['variation_id'] ) ) {
				$thumbnail_id = get_post_thumbnail_id( $variation['variation_id'] );
				if ( ! in_array( $thumbnail_id, $attachments_ids ) ) {
					$attachments_ids[] = $thumbnail_id;
				}
			}
		}
	}
	if ( empty( $attachments_ids ) ) {
		return;
	}
	$gallery_uid = uniqid();

	$product_gallery_data = thegem_get_output_product_gallery_data($product->get_id());

	echo '<div class="preloader"><div class="preloader-spin"></div></div>';
	echo '<div class="product-gallery" data-type="'.$product_gallery_data['product_gallery_show_image'].'" data-thumb="'.$product_gallery_data['product_gallery_type'].'" data-fancy="'.$product_gallery_data['product_gallery_lightbox'].'" data-zoom="'.$product_gallery_data['product_gallery_zoom'].'" data-colors="'.$product_gallery_data['product_gallery_elements_color'].'" data-auto-height="'.$product_gallery_data['product_gallery_auto_height'].'">';

	//Labels
	if ( $product_gallery_data['product_gallery_labels'] ) {
		echo '<div class="product-gallery-labels">';
		echo '<div class="product-labels">';
		echo $product_gallery_data['product_gallery_label_sale'] ? woocommerce_show_product_loop_sale_flash() : null;
		echo $product_gallery_data['product_gallery_label_new'] ? thegem_woocommerce_show_product_loop_featured_flash() : null;
		echo $product_gallery_data['product_gallery_label_out_stock'] ? thegem_woocommerce_show_product_loop_out_of_stock_flash() : null;
		echo '</div></div>';
	}

	//Zoom icon
	if ( $product_gallery_data['product_gallery_lightbox']) {
		echo '<div class="product-gallery-fancy"></div>';
	}

	//Images
	foreach ( $attachments_ids as $attachments_id ) {
		if ( thegem_get_option( 'woocommerce_activate_images_sizes' ) ) {
			$thumb_image_url			 = thegem_get_thumbnail_src( $attachments_id, 'thegem-product-thumbnail' );
			$thumb_image_url_2x		  = thegem_get_thumbnail_src( $attachments_id, 'thegem-product-thumbnail-2x' );
			$thumb_vertical_image_url	= thegem_get_thumbnail_src( $attachments_id, 'thegem-product-thumbnail-vertical' );
			$thumb_vertical_image_url_2x = thegem_get_thumbnail_src( $attachments_id, 'thegem-product-thumbnail-vertical-2x' );
		} else {
			$thumb_image_url			 = wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$thumb_image_url_2x		  = wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$thumb_vertical_image_url	= wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$thumb_vertical_image_url_2x = wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		}
		$full_image_url = wp_get_attachment_image_src( $attachments_id, 'full' );
		?>
		<div class="product-gallery-slider-item" data-image-id="<?= esc_attr( $attachments_id ); ?>">
			<div class="product-gallery-image <?=$product_gallery_data['product_gallery_zoom'] ? 'init-zoom' : null?>">
				<?php if ($product_gallery_data['product_gallery_lightbox']): ?>
					<a href="<?= esc_url( $full_image_url[0] ); ?>" class="fancy-product-gallery"
					   data-fancybox-group="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
					   data-fancybox="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
					   data-full-image-url="<?= esc_url( $full_image_url[0] ); ?>">
						<img src="<?= esc_url( $full_image_url[0] ); ?>" alt="" class="img-responsive">
					</a>
				<?php else: ?>
					<img src="<?= esc_url( $full_image_url[0] ); ?>" alt="" class="img-responsive">
				<?php endif; ?>
			</div>
		</div>

		<?php if ($product_gallery_data['product_gallery_type'] == 'horizontal' || $product_gallery_data['product_gallery_type'] == 'vertical'): ?>
			<div class="product-gallery-thumb-item" data-image-id="<?= esc_attr( $attachments_id ); ?>">
				<div class="product-gallery-image">
					<img
						<?php if ( $product_gallery_data['product_gallery_type'] == 'vertical' ): ?>
							src="<?php echo esc_url( $thumb_vertical_image_url[0] ); ?>"
							<?php if ( thegem_get_option( 'product_gallery_retina_ready' ) ): ?>
								srcset="<?php echo esc_url( $thumb_vertical_image_url_2x[0] ); ?> 2x"
							<?php endif; ?>
							data-ww="<?php echo esc_url( $thumb_vertical_image_url[0] ); ?>"
						<?php else: ?>
							src="<?php echo esc_url( $thumb_image_url[0] ); ?>"
							<?php if ( thegem_get_option( 'product_gallery_retina_ready' ) ): ?>
								srcset="<?php echo esc_url( $thumb_image_url_2x[0] ); ?> 2x"
							<?php endif; ?>
						<?php endif; ?>
							alt="" class="img-responsive"
					>
				</div>
			</div>
		<?php endif; ?>

		<?php
	}
	?>

	<!--Video-->
	<?php
	$product_video_data = get_post_meta( $post->ID, 'thegem_product_video', true );
	$product_video = thegem_get_sanitize_product_video_data($product_video_data);
	$video_type = $product_video['product_video_type'];
	$video = $product_video['product_video_id'];
	$video_self = $product_video['product_video_link'];
	$poster = $product_video['product_video_thumb'];
	$poster_id = attachment_url_to_postid($poster);

	if (!empty($video) && $video_type == 'youtube'){
		$youtube_id = thegem_parcing_youtube_url($video);
	}

	if (!empty($video) && $video_type == 'vimeo') {
		$vimeo_id = thegem_parcing_vimeo_url($video);
	}

	if ( thegem_get_option( 'woocommerce_activate_images_sizes' ) ) {
		$thumb_video_url			 = thegem_get_thumbnail_src( $poster_id, 'thegem-product-thumbnail' );
		$thumb_video_url_2x		  = thegem_get_thumbnail_src( $poster_id, 'thegem-product-thumbnail-2x' );
		$thumb_vertical_video_url	= thegem_get_thumbnail_src( $poster_id, 'thegem-product-thumbnail-vertical' );
		$thumb_vertical_video_url_2x = thegem_get_thumbnail_src( $poster_id, 'thegem-product-thumbnail-vertical-2x' );
	} else {
		$thumb_video_url			 = wp_get_attachment_image_src( $poster_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
		$thumb_video_url_2x		  = wp_get_attachment_image_src( $poster_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
		$thumb_vertical_video_url	= wp_get_attachment_image_src( $poster_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		$thumb_vertical_video_url_2x = wp_get_attachment_image_src( $poster_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
	}

	$link = '';
	if($video_type == 'youtube' || $video_type == 'vimeo') {
		if($video_type == 'youtube' && $youtube_id) {
			$link = '//www.youtube.com/embed/'.$youtube_id.'?playlist='.$youtube_id.'&autoplay=1&mute=1&controls=1&loop=1&showinfo=0&autohide=1&iv_load_policy=3&rel=0&disablekb=1&wmode=transparent';

			if ($poster){
				$video_block = '<iframe src="'.esc_url($link).'" frameborder="0" muted="muted" allowfullscreen></iframe>';
			} else {
				$video_block = '<div id="productYoutubeVideo" data-yt-id="'.$youtube_id.'"></div>';
			}
		}
		if($video_type == 'vimeo' && $vimeo_id) {
			$link = '//player.vimeo.com/video/'.$vimeo_id.'?autoplay=1&muted=1&controls=1&loop=1&title=0&badge=0&byline=0&autopause=0&autohide=1';

			if ($poster){
				$video_block = '<iframe src="'.esc_url($link).'" frameborder="0" muted="muted" allowfullscreen></iframe>';
			} else {
				$video_block = '<div id="productVimeoVideo" data-vm-id="'.$vimeo_id.'"></div>';
			}
		}
	} else if ($video_type == 'self') {
		$link = $video_self;
		$video_block = '<video id="productSelfVideo" class="fancybox-video" controls disablePictureInPicture controlsList="nodownload" loop="loop" src="'.$video_self.'" muted="muted"'.($poster ? ' poster="'.esc_url($poster).'"' : '').'></video>';
	}

	if ( isset($video_block) ) { ?>
		<div class="product-gallery-slider-item <?php if (!$poster || $video_type == 'self'): ?>video-block<?php endif; ?>" data-video-type="<?=$video_type?>">
			<?php if ($product_gallery_data['product_gallery_lightbox']): ?>
				<a href="<?= $link ?>"
				   class="fancy-product-gallery"
				   data-fancybox-group="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
				   data-fancybox="product-gallery-<?= esc_attr( $gallery_uid ); ?>">
					<?php if ( $poster && $video_type != 'self'): ?>
						<img src="<?php echo esc_url( $poster ); ?>" alt="" class="img-responsive">
						<i class="icon-play <?= $video_type ?>"></i>
					<?php else: ?>
						<?= $video_block ?>
					<?php endif; ?>
				</a>
			<?php else: ?>
				<?= $video_block ?>
			<?php endif; ?>
		</div>

		<?php if ($product_gallery_data['product_gallery_type'] == 'horizontal' || $product_gallery_data['product_gallery_type'] == 'vertical'): ?>
			<div class="product-gallery-thumb-item">
				<div class="product-gallery-image">
					<?php if ( $poster ): ?>
						<img
							<?php if ( $product_gallery_data['product_gallery_type'] == 'vertical' ): ?>
								src="<?php echo esc_url( $thumb_vertical_video_url[0] ); ?>"
								<?php if ( thegem_get_option( 'product_gallery_retina_ready' ) ): ?>
									srcset="<?php echo esc_url( $thumb_vertical_video_url_2x[0] ); ?> 2x"
								<?php endif; ?>
								data-ww="<?php echo esc_url( $thumb_vertical_video_url[0] ); ?>"
							<?php else: ?>
								src="<?php echo esc_url( $thumb_video_url[0] ); ?>"
								<?php if ( thegem_get_option( 'product_gallery_retina_ready' ) ): ?>
									srcset="<?php echo esc_url( $thumb_video_url_2x[0] ); ?> 2x"
								<?php endif; ?>
							<?php endif; ?>
								alt="" class="img-responsive"
						>
					<?php else: ?>
						<img src="<?=get_stylesheet_directory_uri()?>/images/dummy/dummy.png" alt="" class="img-responsive">
					<?php endif; ?>
					<i class="icon-play <?=$video_type?>" style="color: <?=$poster ? '#ffffff' : '#dfe5e8'?>"></i>
				</div>
			</div>
		<?php endif; ?>
	<?php }
	?>

	<?php
	echo '</div>';
}

function thegem_woocommerce_single_product_quick_view_gallery() {
	global $post, $product;

	$attachments_ids = array();
	if ( has_post_thumbnail() ) {
		$attachments_ids = array( get_post_thumbnail_id() );
	}
	$attachments_ids = array_merge( $attachments_ids, $product->get_gallery_image_ids() );
	if ( 'variable' === $product->get_type() ) {
		foreach ( $product->get_available_variations() as $variation ) {
			if ( has_post_thumbnail( $variation['variation_id'] ) ) {
				$thumbnail_id = get_post_thumbnail_id( $variation['variation_id'] );
				if ( ! in_array( $thumbnail_id, $attachments_ids ) ) {
					$attachments_ids[] = $thumbnail_id;
				}
			}
		}
	}
	if ( empty( $attachments_ids ) ) {
		return;
	}
	$gallery_uid = uniqid();
	echo '<div class="preloader"><div class="preloader-spin"></div></div>';

	if ( thegem_get_option( 'product_gallery' ) != 'legacy' ) {
		echo '<div class="product-gallery gem-quick-view-gallery" data-thumb="dots" data-loop="0" data-auto-height="0" data-colors="#00bcd4">';
		foreach ( $attachments_ids as $attachments_id ) {
			$preview_image_url = wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			?>
			<div class="product-gallery-slider-item" data-image-id="<?= esc_attr( $attachments_id ); ?>">
				<div class="product-gallery-image">
					<img src="<?= esc_url( $preview_image_url[0] ); ?>" alt="" class="img-responsive">
				</div>
			</div>
			<?php
		}

		$product_video_data = get_post_meta( $post->ID, 'thegem_product_video', true );
		$product_video	  = thegem_get_sanitize_product_video_data( $product_video_data );
		$video_type		 = $product_video['product_video_type'];
		$video			  = $product_video['product_video_id'];
		$video_self		 = $product_video['product_video_link'];
		$poster			 = $product_video['product_video_thumb'];

		if (!empty($video) && $video_type == 'youtube'){
			$youtube_id = thegem_parcing_youtube_url($video);
		}

		if (!empty($video) && $video_type == 'vimeo') {
			$vimeo_id = thegem_parcing_vimeo_url($video);
		}

		if ( $video_type == 'youtube' || $video_type == 'vimeo' ) {
			if ( $video_type == 'youtube' && $youtube_id ) {
				$video_block = '<div id="productYoutubeVideo" data-yt-id="'.$youtube_id.'"></div>';
			}
			if ( $video_type == 'vimeo' && $vimeo_id ) {
				$video_block = '<div id="productVimeoVideo" data-vm-id="'.$vimeo_id.'"></div>';
			}
		} else if ( $video_type == 'self' ) {
			$video_block = '<video id="productSelfVideo" class="fancybox-video" controls disablePictureInPicture controlsList="nodownload" loop="loop" src="' . $video_self . '" muted="muted"' . ( $poster ? ' poster="' . esc_url( $poster ) . '"' : '' ) . '></video>';
		}

		if ( isset( $video_block ) ) {
			?>
			<div class="product-gallery-slider-item video-block" data-video-type="<?= $video_type ?>"><?= $video_block ?></div>
			<?php
		}

	} else {
		echo '<div class="gem-simple-gallery gem-quick-view-gallery gem-gallery-hover-default responsive">';
		foreach ( $attachments_ids as $attachments_id ) {
			$preview_image_url = wp_get_attachment_image_src( $attachments_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			?>
			<div class="gem-gallery-item">
				<div class="gem-gallery-item-image">
					<img src="<?php echo esc_url( $preview_image_url[0] ); ?>" alt="" class="img-responsive">
				</div>
			</div>
			<?php
		}
	}
	echo '</div>';
}

function thegem_woocommerce_single_product_page_content() {
	if(get_the_content() || (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode())) {
?>
<div class="product-content entry-content"><?php the_content(); ?></div>
<?php
	}
}

function thegem_woocommerce_output_related_products_args($args) {
	$args['posts_per_page'] = 6;
	$args['columns'] = 6;
	return $args;
}
add_filter('woocommerce_output_related_products_args', 'thegem_woocommerce_output_related_products_args');

function thegem_loop_shop_per_page() {
	$pc = !empty($_REQUEST['product_count']) && intval($_REQUEST['product_count']) > 0 ? intval($_REQUEST['product_count']) : 12;
	return $pc;
}
add_filter('loop_shop_per_page', 'thegem_loop_shop_per_page', 15);

function thegem_woocommerce_product_per_page_select() {
	$products_per_page_items = array(12,24,48);
	$pc = !empty($_REQUEST['product_count']) && intval($_REQUEST['product_count']) > 0 ? intval($_REQUEST['product_count']) : 12;
?>
<div class="woocommerce-select-count">
	<select id="products-per-page" name="products_per_page" class="gem-combobox" onchange="window.location.href=jQuery(this).val();">
		<?php foreach($products_per_page_items as $products_per_page_item) : ?>
			<option value="<?php echo esc_url(add_query_arg('product_count', $products_per_page_item)); ?>" <?php selected($pc, $products_per_page_item); ?>><?php printf(esc_html__('Show %d On Page', 'thegem'), $products_per_page_item); ?></option>
		<?php endforeach; ?>
	</select>
</div>
<?php
}

function thegem_woocommerce_before_shop_content() {
	echo '<div class="products-list">';
}
function thegem_woocommerce_after_shop_content() {
	echo '</div><!-- .products-list -->';
}

function thegem_woocommerce_before_shop_loop_start() {
	echo '<div class="before-products-list rounded-corners clearfix">';
}
function thegem_woocommerce_before_shop_loop_end() {
	echo '</div>';
}
function thegem_woocommerce_single_product_navigation() {
?>
<div class="block-navigation">
	<?php if($post = get_previous_post()) : ?>
		<div class="block-product-navigation-prev">
			<?php thegem_button(array(
				'text' => __('Prev', 'thegem'),
				'href' => get_permalink($post->ID),
				'style' => 'outline',
				'size' => 'tiny',
				'position' => 'left',
				'icon' => 'prev',
				'border_color' => thegem_get_option('button_background_basic_color'),
				'text_color' => thegem_get_option('button_background_basic_color'),
				'hover_background_color' => thegem_get_option('button_background_basic_color'),
				'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
			), 1); ?>
		</div>
	<?php endif; ?>
	<?php if($post = get_next_post()) : ?>
		<div class="block-product-navigation-next">
			<?php thegem_button(array(
				'text' => __('Next', 'thegem'),
				'href' => get_permalink($post->ID),
				'style' => 'outline',
				'size' => 'tiny',
				'position' => 'right',
				'icon' => 'next',
				'icon_position' => 'right',
				'border_color' => thegem_get_option('button_background_basic_color'),
				'text_color' => thegem_get_option('button_background_basic_color'),
				'hover_background_color' => thegem_get_option('button_background_basic_color'),
				'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
			), 1); ?>
		</div>
	<?php endif; ?>
</div><!-- .block-product-navigation-prev -->
<?php
}

function thegem_product_quick_view_navigation() {
?>
<div class="product-quick-view-navigation">
	<?php if($post = get_previous_post()) : ?>
		<?php thegem_button(array(
			'style' => 'outline',
			'size' => 'tiny',
			'icon' => 'prev',
			'border_color' => thegem_get_option('button_background_basic_color'),
			'text_color' => thegem_get_option('button_background_basic_color'),
			'hover_background_color' => thegem_get_option('button_background_basic_color'),
			'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
			'attributes' => array(
				'data-product-id' => $post->ID
			)
		), 1); ?>
	<?php endif; ?>
	<?php if($post = get_next_post()) : ?>
		<?php thegem_button(array(
			'style' => 'outline',
			'size' => 'tiny',
			'icon' => 'next',
			'icon_position' => 'right',
			'border_color' => thegem_get_option('button_background_basic_color'),
			'text_color' => thegem_get_option('button_background_basic_color'),
			'hover_background_color' => thegem_get_option('button_background_basic_color'),
			'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
			'attributes' => array(
				'data-product-id' => $post->ID
			)
		), 1); ?>
	<?php endif; ?>
</div>
<?php
}

function thegem_woocommerce_show_product_loop_featured_flash() {
	global $post, $product;
	if($product->is_featured()) {
		echo apply_filters('woocommerce_featured_flash', '<span class="new-label title-h6">' . esc_html__( 'New', 'thegem' ) . '</span>', $post, $product);
	}
}

function thegem_woocommerce_show_product_loop_out_of_stock_flash() {
	global $post, $product;
	if(!$product->is_in_stock()) {
		echo apply_filters('woocommerce_out_of_stock_flash', '<span class="out-of-stock-label title-h6">' . wp_kses(__('Out <span class="small">of stock</span>', 'thegem'), array('span' => array('class' => array()))) . '</span>', $post, $product);
	}
}

function thegem_woocommerce_after_shop_loop_item_link() {
	global $post, $product;
	echo '<a href="'.esc_url(get_the_permalink()).'" class="bottom-product-link"></a>';
}

function thegem_woocommerce_after_shop_loop_item_wishlist() {
	global $post, $product;
	if(function_exists('thegem_is_plugin_active') && !thegem_get_option('catalog_view') && defined( 'YITH_WCWL' )) {
		echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
	}
}
function thegem_woocommerce_after_shop_loop_item_linebreak() {
	echo '<div class="product-bottom-linebreak"></div>';
}

function thegem_woocommerce_back_to_shop_button() {
	thegem_button(array(
		'href' => get_permalink(wc_get_page_id('shop')),
		'style' => 'outline',
		'size' => 'tiny',
		'position' => 'right',
		'icon' => 'prev',
		'border_color' => thegem_get_option('button_background_basic_color'),
		'text_color' => thegem_get_option('button_background_basic_color'),
		'hover_background_color' => thegem_get_option('button_background_basic_color'),
		'hover_text_color' => thegem_get_option('button_outline_text_hover_color'),
		'extra_class' => 'back-to-shop-button'
	), 1);
}

function thegem_woocommerce_rating_separator() {
	echo '<div class="rating-divider"></div>';
}

function thegem_woocommerce_size_guide() {
	global $product;
	$product_size_guide_data = thegem_get_sanitize_product_size_guide_data($product->get_id());
	$size_guide_image = thegem_get_option('size_guide_image');
	if($product_size_guide_data['size_guide'] == 'disabled') {
		$size_guide_image = '';
	} elseif($product_size_guide_data['size_guide'] == 'custom' && $product_size_guide_data['custom_image']) {
		$size_guide_image = $product_size_guide_data['custom_image'];
	}
?>
	<?php if($size_guide_image) : ?>
		<div class="size-guide"><a href="<?php echo esc_url($size_guide_image); ?>" class="fancybox"><?php esc_html_e('Size guide', 'thegem'); ?></a></div>
	<?php endif; ?>
<?php
}

function thegem_yith_wcwl_add_to_wishlist_params($additional_params, $atts) {
	if(isset($atts['thegem_product_page']) && $atts['thegem_product_page']) {
		$additional_params['thegem_product_page'] = 1;
	}
	if(isset($atts['thegem_products_grid']) && $atts['thegem_products_grid']) {
		$additional_params['thegem_products_grid'] = 1;
		$additional_params['container_classes'] .= ' icon';
	}
	return $additional_params;
}
add_filter('yith_wcwl_add_to_wishlist_params', 'thegem_yith_wcwl_add_to_wishlist_params', 10, 2);

function thegem_yith_wcwl_add_to_wishlist_button() {
	if(function_exists('thegem_is_plugin_active') && thegem_is_plugin_active('yith-woocommerce-wishlist/init.php')) {
		echo do_shortcode( '[yith_wcwl_add_to_wishlist thegem_product_page="1"]' );
	}
}

add_filter('yith_wcwl_is_wishlist_responsive', function() { return false; });

function thegem_woocommerce_template_loop_product_hover_thumbnail() {
	global $post, $product;
	$gallery = $product->get_gallery_image_ids();
	$product_hover = get_post_meta($post->ID, 'thegem_product_disable_hover', true);
	$output = '<span class="woo-product-overlay"></span>';
	if(isset($gallery[0]) && !$product_hover) {
		if(thegem_get_option('woocommerce_activate_images_sizes')) {
			$image = thegem_get_thumbnail_image($gallery[0], 'thegem-product-catalog', false, array( 'class' => "attachment woo-product-hover"));
		} else {
			$image = wp_get_attachment_image($gallery[0], 'shop_catalog', false, array( 'class' => "attachment woo-product-hover"));
		}
		if(!empty($image)) $output = $image;
	}
	echo $output;
}

function thegem_woocommerce_template_loop_product_quick_view() {
	global $post, $product;
	if(thegem_get_option('product_quick_view')) {
		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		wp_enqueue_script('thegem-product-quick-view');
		if (thegem_get_option('product_gallery') != 'legacy') {
			wp_enqueue_style('thegem-product-gallery');
		} else {
			wp_enqueue_style('thegem-hovers');
		}
		echo '<span class="quick-view-button title-h6" data-product-id="'.$post->ID.'">'.esc_html__('Quick View', 'thegem').'</span>';
	}
}

function thegem_woocommerce_template_loop_category_title($category) {
	echo '<div class="category-overlay">';
	echo '<h6 class="category-title">'.$category->name.'</h6>';
	echo '<div class="category-overlay-separator"></div>';
	echo '<div class="category-count">'.sprintf(esc_html(_n('%s item', '%s items', $category->count, 'thegem')), $category->count).'</div>';
	echo '</div>';
}

function thegem_woocommerce_dropdown_variation_attribute_options_args($args) {
	$args['class'] = 'gem-combobox';
	return $args;
}

function thegem_woocommerce_review_gravatar_size($size) {
	return '70';
}

function thegem_woocommerce_product_review_comment_form_args($args) {
	if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
		$consent = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

		$cookies = sprintf(
			'<p class="comment-form-cookies-consent col-md-12 col-xs-12">%s %s</p>',
			sprintf(
				'<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s class="gem-checkbox" />',
				$consent
			),
			sprintf(
				'<label for="wp-comment-cookies-consent">%s</label>',
				__( 'Save my name, email, and website in this browser for the next time I comment.' )
			)
		);

		// Ensure that the passed fields include cookies consent.
		if ( isset( $args['fields'] ) && ! isset( $args['fields']['cookies'] ) ) {
			$args['fields']['cookies'] = $cookies;
		}
	}
	return $args;
}

remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
add_action( 'woocommerce_after_shop_loop', 'thegem_woocommerce_after_shop_content', 15);
add_action( 'woocommerce_after_shop_loop', 'woocommerce_taxonomy_archive_description', 15 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_product_archive_description', 15 );

remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 5 );

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
add_action('woocommerce_before_shop_loop', 'thegem_woocommerce_before_shop_content', 4);
add_action('woocommerce_before_shop_loop', 'thegem_woocommerce_before_shop_loop_start', 11);
add_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 15);
add_action('woocommerce_before_shop_loop', 'woocommerce_breadcrumb', 20);
add_action('woocommerce_before_shop_loop', 'thegem_woocommerce_product_per_page_select', 30);
add_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 40);
add_action('woocommerce_before_shop_loop', 'thegem_woocommerce_before_shop_loop_end', 45);

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action('woocommerce_shop_loop_item_labels', 'woocommerce_show_product_loop_sale_flash', 5);
add_action('woocommerce_shop_loop_item_labels', 'thegem_woocommerce_show_product_loop_featured_flash', 10);
add_action('woocommerce_shop_loop_item_labels', 'thegem_woocommerce_show_product_loop_out_of_stock_flash', 10);
add_action('woocommerce_shop_loop_item_image', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_shop_loop_item_image', 'thegem_woocommerce_template_loop_product_hover_thumbnail', 15);
add_action('woocommerce_shop_loop_item_image', 'thegem_woocommerce_template_loop_product_quick_view', 40);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

add_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_link', 15);
add_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_wishlist', 20);
//add_action('woocommerce_after_shop_loop_item', 'thegem_woocommerce_after_shop_loop_item_linebreak', 17);

add_action('thegem_woocommerce_single_product_left', 'thegem_woocommerce_single_product_gallery', 5);
add_action('thegem_woocommerce_single_product_left', 'thegem_woocommerce_single_product_gallery_alternative', 5);
if (!thegem_get_option('product_hide_social_sharing')){
	add_action('thegem_woocommerce_single_product_left', 'thegem_socials_sharing', 10);
	add_action('woocommerce_before_single_product_summary', 'thegem_socials_sharing',30);
}
add_action('thegem_woocommerce_single_product_left', 'woocommerce_template_single_meta', 15);
add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_meta', 35);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

add_action('thegem_woocommerce_single_product_quick_view_left', 'thegem_woocommerce_single_product_quick_view_gallery', 5);

add_action('thegem_woocommerce_single_product_right', 'thegem_woocommerce_back_to_shop_button', 5);
add_action('woocommerce_single_product_summary', 'thegem_woocommerce_back_to_shop_button', 4);
add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_title', 10);
add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_rating', 20);
add_action('thegem_woocommerce_single_product_right', 'thegem_woocommerce_rating_separator', 25);
add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_price', 30);
add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_excerpt', 35);
add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_add_to_cart', 45);
add_action('thegem_woocommerce_single_product_right', 'thegem_woocommerce_size_guide', 50);
add_action('woocommerce_single_product_summary', 'thegem_woocommerce_size_guide', 35);

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'thegem_woocommerce_dropdown_variation_attribute_options_args', 20 );
add_filter( 'woocommerce_product_description_heading', '__return_false', 20 );
add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 20 );

add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_title', 10);
add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_rating', 20);
add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_price', 30);
add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_excerpt', 35);
add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_add_to_cart', 45);
add_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_meta', 55);
add_action('thegem_woocommerce_single_product_quick_view_bottom', 'thegem_product_quick_view_navigation', 10);

add_action('thegem_woocommerce_after_add_to_cart_button', 'thegem_yith_wcwl_add_to_wishlist_button');
add_filter('yith_wcwl_show_add_to_wishlist', '__return_false', 20);

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('thegem_woocommerce_single_product_bottom', 'woocommerce_output_product_data_tabs', 5);
add_action('thegem_woocommerce_single_product_bottom', 'thegem_woocommerce_single_product_navigation', 10);
add_action('thegem_woocommerce_single_product_bottom', 'thegem_woocommerce_single_product_page_content', 15);

add_action('thegem_woocommerce_after_single_product', 'woocommerce_output_related_products', 5);

remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
add_action( 'woocommerce_shop_loop_subcategory_title', 'thegem_woocommerce_template_loop_category_title', 10 );

add_filter( 'woocommerce_review_gravatar_size', 'thegem_woocommerce_review_gravatar_size', 20 );
add_filter( 'woocommerce_product_review_comment_form_args', 'thegem_woocommerce_product_review_comment_form_args', 20 );

function thegem_cart_menu($items, $args) {
	if(thegem_is_plugin_active('woocommerce/woocommerce.php') && $args->menu_id == 'primary-menu' && !thegem_get_option('hide_card_icon')) {
		$count = thegem_get_cart_count();
		ob_start();
		woocommerce_mini_cart();
		$minicart = ob_get_clean();
		$items .= '<li class="menu-item menu-item-cart not-dlmenu"><a href="'.esc_url(get_permalink(wc_get_page_id('cart'))).'" class="minicart-menu-link ' . ($count == 0 ? 'empty' : '') . '">' . '<span class="minicart-item-count">' . $count . '</span>' . '</a><div class="minicart"><div class="widget_shopping_cart_content">'.$minicart.'</div></div></li>';
	}
	return $items;
}
add_filter('wp_nav_menu_items', 'thegem_cart_menu', 11, 2);

function thegem_woocommerce_placeholder_img($val, $size, $dimensions) {
	return '<span class="product-dummy-wrapper" style="max-width: '.$dimensions['width'].'px;"><span class="product-dummy" style="padding-bottom: '.($dimensions['height']*100/$dimensions['width']).'%;"></span></span>';
}
add_filter('woocommerce_placeholder_img', 'thegem_woocommerce_placeholder_img', 10, 3);

function thegem_cart_short_info() {
	echo '<div class="cart-short-info">'.sprintf(wp_kses(__('You Have <span class="items-count">%d Items</span> In Your Cart', 'thegem'), array('span' => array('class' => array()))), WC()->cart->cart_contents_count).'</div>';
}
add_action('woocommerce_before_cart', 'thegem_cart_short_info', 15);
add_action('woocommerce_before_cart', 'woocommerce_breadcrumb', 10);

function thegem_cart_items_html_output() {
	thegem_cart_short_info();
	die(-1);
}
add_action('wp_ajax_thegem_cart_items_html', 'thegem_cart_items_html_output');
add_action('wp_ajax_nopriv_thegem_cart_items_html', 'thegem_cart_items_html_output');

function thegem_wc_add_to_cart_message($message, $products) {
	$titles = array();
	$count  = 0;

	$show_qty = true;

	if ( ! is_array( $products ) ) {
		$products = array( $products => 1 );
		$show_qty = false;
	}

	if ( ! $show_qty ) {
		$products = array_fill_keys( array_keys( $products ), 1 );
	}

	foreach ( $products as $product_id => $qty ) {
		$titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) );
		$count += $qty;
	}

	$titles = array_filter( $titles );

	$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'woocommerce' ), wc_format_list_of_items( $titles ) );

	// Output success messages
	if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
		$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );

		$message = sprintf('<div class="cart-added"><div class="cart-added-text">%s</div><div class="cart-added-button"><a href="%s" class="gem-button button wc-forward">%s</a></div></div>', $added_text, esc_url($return_to), esc_html__('Continue shopping', 'woocommerce'));

	} else {

		$message = sprintf('<div class="cart-added"><div class="cart-added-text">%s</div><div class="cart-added-button"><a href="%s" class="gem-button button wc-forward">%s</a></div></div>', $added_text, esc_url(wc_get_page_permalink( 'cart' )), esc_html__('View cart', 'woocommerce'));

	}

	return $message;
}
add_filter('wc_add_to_cart_message_html', 'thegem_wc_add_to_cart_message', 10, 2);

function thegem_product_add_page_settings_boxes() {
	add_meta_box('thegem_page_title', esc_html__('Page Title', 'thegem'), 'thegem_page_title_settings_box', 'product', 'normal', 'high');
	add_meta_box('thegem_page_sidebar', esc_html__('Page Sidebar', 'thegem'), 'thegem_page_sidebar_settings_box', 'product', 'normal', 'high');
}
if ($thegem_use_old_page_options) {
	add_action('add_meta_boxes', 'thegem_product_add_page_settings_boxes');
}

function thegem_save_product_page_data($post_id) {
	if(
		!isset($_POST['thegem_page_title_settings_box_nonce']) ||
		!isset($_POST['thegem_page_sidebar_settings_box_nonce'])
	) {
		return;
	}
	if(
		!wp_verify_nonce($_POST['thegem_page_title_settings_box_nonce'], 'thegem_page_title_settings_box') ||
		!wp_verify_nonce($_POST['thegem_page_sidebar_settings_box_nonce'], 'thegem_page_sidebar_settings_box')
	) {
		return;
	}

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if(isset($_POST['post_type']) && in_array($_POST['post_type'], array('product'))) {
		if(!current_user_can('edit_page', $post_id)) {
			return;
		}
	} else {
		if(!current_user_can('edit_post', $post_id)) {
			return;
		}
	}

	if(!isset($_POST['thegem_page_data']) || !is_array($_POST['thegem_page_data'])) {
		return;
	}

	$page_data = array_merge(
		thegem_get_sanitize_page_title_data(0, $_POST['thegem_page_data']),
		thegem_get_sanitize_page_sidebar_data(0, $_POST['thegem_page_data'])
	);
	update_post_meta($post_id, 'thegem_page_data', $page_data);
}
if ($thegem_use_old_page_options) {
	add_action('save_post', 'thegem_save_product_page_data');
}


function thegem_product_tabs($tabs = array()) {
		global $product, $post;
		// Description tab - shows product content
		if (get_post_meta($post->ID, 'thegem_product_description', true)) {
			$tabs['description'] = array(
				'title'	=> esc_html__( 'Description', 'woocommerce' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab'
			);
		} elseif(isset($tabs['description'])) {
			unset($tabs['description']);
		}

		return $tabs;
}
add_filter('woocommerce_product_tabs', 'thegem_product_tabs', 11);

function thegem_woocommerce_subcategory_thumbnail( $category ) {
	$small_thumbnail_size = apply_filters( 'single_category_small_thumbnail_size', 'shop_catalog' );
	$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true);
	$image = '';

	if ( $thumbnail_id ) {
		if(thegem_get_option('woocommerce_activate_images_sizes')) {
			$image = thegem_get_thumbnail_src( $thumbnail_id, 'thegem-product-catalog' );
		} else {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
		}
		global $thegem_product_categories_images;
		if($thegem_product_categories_images) {
			$image = wp_get_attachment_image_src( $thumbnail_id, 'thegem-custom-product-categories');
		}
	}

	if ( $image ) {
		$image[0] = str_replace( ' ', '%20', $image[0] );
		echo '<img src="' . esc_url( $image[0] ) . '" width="'.esc_attr($image[1]).'" height="'.esc_attr($image[2]).'" alt="' . esc_attr( $category->name ) . '" class="img-responsive" />';
	} else {
		if(thegem_get_option('woocommerce_activate_images_sizes')) {
			echo wc_placeholder_img(array(thegem_get_option('woocommerce_catalog_image_width'), thegem_get_option('woocommerce_catalog_image_height'), 1));
		} else {
			echo wc_placeholder_img($small_thumbnail_size);
		}
	}
}
remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10);
add_action('woocommerce_before_subcategory_title', 'thegem_woocommerce_subcategory_thumbnail', 10);

add_filter('woocommerce_add_to_cart_fragments', 'gem_woocommerce_header_dropdown_cart_fragment');

function gem_woocommerce_header_dropdown_cart_fragment( $fragments ) {
	$count = thegem_get_cart_count();
	$fragments['a.minicart-menu-link'] = '<a href="'.esc_url(get_permalink(wc_get_page_id('cart'))).'" class="minicart-menu-link ' . ($count == 0 ? 'empty' : '') . '"><span class="minicart-item-count">'.$count.'</span></a>';
	return $fragments;
}

function thegem_single_product_small_thumbnail_size($size) {
	global $thegem_product_categories_images;
	if($thegem_product_categories_images) {
		return 'thegem-custom-product-categories';
	}
	return $size;
}
add_filter( 'single_category_small_thumbnail_size', 'thegem_single_product_small_thumbnail_size' );

function thegem_woocommerce_get_image_size_categories($size) {
	$size = array(
		'width'  => '1170',
		'height' => '1117',
		'crop'   => 1
	);
	return $size;
}
add_filter( 'woocommerce_get_image_size_thegem-custom-product-categories', 'thegem_woocommerce_get_image_size_categories' );

function thegem_woocommerce_account_menu_item_classes($classes, $endpoint) {
	if(in_array('is-active', $classes)) {
		$classes[] = 'current-menu-ancestor';
	}
	return $classes;
}
add_filter('woocommerce_account_menu_item_classes', 'thegem_woocommerce_account_menu_item_classes', 10, 2);

function thegem_product_quick_view_output() {
	$nonce = empty($_REQUEST['ajax_nonce']) ? '' : $_REQUEST['ajax_nonce'];
	$product_id = empty($_REQUEST['product_id']) ? '' : $_REQUEST['product_id'];

	if(!wp_verify_nonce($nonce, 'product_quick_view_ajax_security' )) {
		die(-1);
	}

	$args = array(
		'posts_per_page'	  => 1,
		'post_type'		   => 'product',
		'post_status'		 => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'	   => 1,
	);

	if ( isset( $product_id ) ) {
		$args['p'] = absint( $product_id );
	}

	$single_product = new WP_Query( $args );

	$preselected_id = '0';

	ob_start();

	while ( $single_product->have_posts() ) :
		$single_product->the_post();
		?>

		<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>" data-quick-view="<?=thegem_get_option('product_gallery')?>">

			<?php wc_get_template_part( 'content', 'single-product-quick-view' ); ?>

		</div>

	<?php endwhile; // end of the loop.

	wp_reset_postdata();
$time2= time();
	echo '<div class="woocommerce">' . ob_get_clean() . '</div>';

	die(-1);
}
add_action('wp_ajax_thegem_product_quick_view', 'thegem_product_quick_view_output');
add_action('wp_ajax_nopriv_thegem_product_quick_view', 'thegem_product_quick_view_output');

function thegem_catalog_view() {
	if(thegem_get_option('catalog_view')) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_price', 30);
		remove_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_add_to_cart', 45);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
		remove_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_price', 30);
		remove_action('thegem_woocommerce_single_product_quick_view_right', 'woocommerce_template_single_add_to_cart', 45);
		remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

		remove_action('thegem_woocommerce_single_product_left', 'thegem_socials_sharing', 10);
		remove_action('thegem_woocommerce_single_product_left', 'woocommerce_template_single_meta', 15);

		if (!thegem_get_option('product_hide_social_sharing')){
			add_action('thegem_woocommerce_single_product_right', 'thegem_socials_sharing', 65);
		}

		add_action('thegem_woocommerce_single_product_right', 'woocommerce_template_single_meta', 70);

		remove_action('wp_nav_menu_items', 'thegem_cart_menu', 11);

	}
}
add_action('init', 'thegem_catalog_view');
add_action('wp', 'thegem_catalog_view');

function thegem_woocommerce_form_field_args_callback($args, $key, $value) {
	if (stripos($key, 'shipping_') === 0) {
		$args['autofocus'] = false;
	}
	return $args;
}
add_filter('woocommerce_form_field_args', 'thegem_woocommerce_form_field_args_callback', 10, 3);

function thegem_woocommerce_loop_add_to_cart_link($link, $product) {
	if (strripos($link, 'add_to_cart_button') === false) {
		return '';
	}
	return $link;
}
add_filter('woocommerce_loop_add_to_cart_link', 'thegem_woocommerce_loop_add_to_cart_link', 10, 2);

function thegem_woocommerce_structured_data() {
	if(isset($GLOBALS['woocommerce']) && isset($GLOBALS['woocommerce']->structured_data)) {
		add_action('thegem_woocommerce_single_product_right', array($GLOBALS['woocommerce']->structured_data, 'generate_product_data'), 60);
	}
}
add_action('init', 'thegem_woocommerce_structured_data');

function thegem_single_product_archive_thumbnail_size($size) {
	if(thegem_get_option('woocommerce_activate_images_sizes')) {
		return 'thegem-product-catalog';
	}
	return $size;
}
add_filter( 'single_product_archive_thumbnail_size', 'thegem_single_product_archive_thumbnail_size' );
add_filter( 'subcategory_archive_thumbnail_size', 'thegem_single_product_archive_thumbnail_size' );

function thegem_woocommerce_get_image_size_thumbnail($size) {
	if(thegem_get_option('woocommerce_activate_images_sizes')) {
		return array(
			'width' => thegem_get_option('woocommerce_thumbnail_image_width'),
			'height' => thegem_get_option('woocommerce_thumbnail_image_height'),
			'crop' => 1,
		);
	}
	return $size;
}
add_filter( 'woocommerce_get_image_size_thumbnail', 'thegem_woocommerce_get_image_size_thumbnail' );

function thegem_woocommerce_product_get_image( $image, $product, $size, $attr, $placeholder, $image_o) {
	if(thegem_get_option('woocommerce_activate_images_sizes') && $size == 'thegem-product-catalog') {
		if ( $product->get_image_id() ) {
			$image = thegem_get_thumbnail_image($product->get_image_id(), $size, false, $attr);
		} elseif ( $product->get_parent_id() ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			$image = $parent_product->get_image( $size, $attr, $placeholder );
		} elseif ( $placeholder ) {
			$image = wc_placeholder_img( $size );
		} else {
			$image = '';
		}
	}
	return $image;
}
add_filter( 'woocommerce_product_get_image', 'thegem_woocommerce_product_get_image', 10, 6);

function thegem_mobile_cart_toggle() {
	if(thegem_is_plugin_active('woocommerce/woocommerce.php') && thegem_get_option('mobile_cart_position', 'top') == 'top' && !thegem_get_option('hide_card_icon') && !thegem_get_option('catalog_view')) {
		$count = thegem_get_cart_count();
		echo '<div class="mobile-cart"><a href="'.esc_url(get_permalink(wc_get_page_id('cart'))).'" class="minicart-menu-link temp ' . ($count == 0 ? 'empty' : '') . '">' . '<span class="minicart-item-count">' . $count . '</span>' . '</a></div><div class="mobile-minicart-overlay"></div>';
	}
}
add_action('thegem_header_menu_opposite', 'thegem_mobile_cart_toggle');

function thegem_mobile_cart_before_mini_cart() {
	if(thegem_get_option('mobile_cart_position', 'top') == 'top' && !thegem_get_option('hide_card_icon') && !thegem_get_option('catalog_view')) {
		echo '<div class="mobile-cart-header">';
		echo '<div class="mobile-cart-header-title title-h6">'.esc_html__('Cart', 'woocommerce').'</div>';
		echo '<a class="mobile-cart-header-close" href="#"><span class="cart-close-line-1"></span><span class="cart-close-line-2"></span></a>';
		echo '</div>';
	}
}
add_action('woocommerce_before_mini_cart', 'thegem_mobile_cart_before_mini_cart');

function thegem_mobile_cart_toggle_body_class($classes) {
	if(thegem_is_plugin_active('woocommerce/woocommerce.php') && thegem_get_option('mobile_cart_position', 'top') == 'top' && !thegem_get_option('hide_card_icon') && !thegem_get_option('catalog_view') && empty($_REQUEST['thegem_header_test'])) {
		$classes[] = 'mobile-cart-position-top';
	}
	return $classes;
}
add_filter('body_class', 'thegem_mobile_cart_toggle_body_class');

add_action( 'elementor/widget/render_content', function( $content, $widget ) {
	if ( 'woocommerce-product-images' === $widget->get_name() ) {
		$content = thegem_woocommerce_single_product_gallery();
	}
	return $content;
}, 10, 2 );

add_action( 'elementor/element/woocommerce-product-images/section_product_gallery_style/after_section_end', function( $element, $args ) {
	$element->remove_control('wc_style_warning');
	$element->remove_control('sale_flash');
	$element->remove_control('image_border_border');
	$element->remove_control('image_border_width');
	$element->remove_control('image_border_color');
	$element->remove_control('image_border_radius');
	$element->remove_control('spacing');
	$element->remove_control('heading_thumbs_style');
	$element->remove_control('thumbs_border_border');
	$element->remove_control('thumbs_border_width');
	$element->remove_control('thumbs_border_color');
	$element->remove_control('thumbs_border_radius');
	$element->remove_control('spacing_thumbs');
}, 10, 3 );

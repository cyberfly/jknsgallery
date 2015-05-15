<?php
add_action( 'after_setup_theme', 'et_setup_theme' );
if ( ! function_exists( 'et_setup_theme' ) ){
	function et_setup_theme(){
		global $themename, $shortname, $default_colorscheme, $et_jplayer_instances_on_page, $et_bg_texture_urls, $et_google_fonts, $epanel_texture_urls;

		$themename = "Notebook";
		$shortname = "notebook";
		$default_colorscheme = "Default";

		$et_bg_texture_urls = array(esc_html__('Thin Vertical Lines',$themename), esc_html__('Small Squares',$themename), esc_html__('Thick Diagonal Lines',$themename), esc_html__('Thin Diagonal Lines',$themename), esc_html__('Diamonds',$themename), esc_html__('Small Circles',$themename), esc_html__('Thick Vertical Lines',$themename), esc_html__('Thin Flourish',$themename), esc_html__('Thick Flourish',$themename), esc_html__('Pocodot',$themename), esc_html__('Checkerboard',$themename), esc_html__('Squares',$themename), esc_html__('Noise',$themename), esc_html__('Wooden',$themename), esc_html__('Stone',$themename), esc_html__('Canvas',$themename));

		$et_google_fonts = apply_filters( 'et_google_fonts', array('Kreon','Droid Sans','Droid Serif','Lobster','Yanone Kaffeesatz','Nobile','Crimson Text','Arvo','Tangerine','Cuprum','Cantarell','Philosopher','Josefin Sans','Dancing Script','Raleway','Bentham','Goudy Bookletter 1911','Quattrocento','Ubuntu', 'PT Sans') );
		sort($et_google_fonts);

		$epanel_texture_urls = $et_bg_texture_urls;
		array_unshift( $epanel_texture_urls, 'Default' );

		$template_dir = get_template_directory();

		$et_jplayer_instances_on_page = 0;

		require_once($template_dir . '/epanel/custom_functions.php');

		require_once($template_dir . '/includes/functions/comments.php');

		require_once($template_dir . '/includes/functions/sidebars.php');

		load_theme_textdomain( $themename, $template_dir . '/lang' );

		require_once($template_dir . '/epanel/core_functions.php');

		require_once($template_dir . '/epanel/post_thumbnails_notebook.php');

		require_once($template_dir . '/includes/additional_functions.php');

		include($template_dir . '/includes/widgets.php');

		add_theme_support( 'post-formats', array( 'image', 'audio', 'video' ) );

		add_action( 'pre_get_posts', 'et_home_posts_query' );
	}
}

function register_main_menus() {
	register_nav_menus(
		array(
			'primary-menu' => __( 'Primary Menu', 'Notebook' )
		)
	);
}
if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

/**
 * Filters the main query on homepage
 */
function et_home_posts_query( $query = false ) {
	/* Don't proceed if it's not homepage or the main query */
	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;

	/* Set the amount of posts per page on homepage */
	$query->set( 'posts_per_page', (int) et_get_option( 'notebook_homepage_posts', '6' ) );

	/* Exclude categories set in ePanel */
	$exclude_categories = et_get_option( 'notebook_exlcats_recent', false );
	if ( $exclude_categories ) $query->set( 'category__not_in', array_map( 'intval', et_generate_wpml_ids( $exclude_categories, 'category' ) ) );
}

if ( ! function_exists( 'et_get_the_author_posts_link' ) ){
	function et_get_the_author_posts_link(){
		global $authordata, $themename;
		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
}

if ( ! function_exists( 'et_postinfo_meta' ) ){
	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) ){
			$postinfo_meta .= ' ' . esc_html__('By',$themename) . ' ' . et_get_the_author_posts_link();
		}

		if ( in_array( 'date', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('on',$themename) . ' ' . get_the_time( $date_format );

		if ( in_array( 'categories', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('in',$themename) . ' ' . get_the_category_list(', ');

		if ( in_array( 'comments', $postinfo ) )
			$postinfo_meta .= ' | ' . et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		echo $postinfo_meta;
	}
}

if ( ! function_exists( 'et_get_comments_popup_link' ) ){
	function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
		global $themename;

		$id = get_the_ID();
		$number = get_comments_number( $id );

		if ( 0 == $number && !comments_open() && !pings_open() ) return;

		if ( $number > 1 )
			$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
		elseif ( $number == 0 )
			$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
		else // must be one
			$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

		return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
	}
}

if ( ! function_exists( 'et_list_pings' ) ){
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
}

add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );
function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}

// add Home link to the custom menu WP-Admin page
function et_add_home_link( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

add_action( 'wp_enqueue_scripts', 'et_load_notebook_scripts' );
function et_load_notebook_scripts(){
	global $shortname;

	$et_prefix = ! et_options_stored_in_one_row() ? "{$shortname}_" : '';
	$heading_font_option_name = "{$et_prefix}heading_font";
	$body_font_option_name = "{$et_prefix}body_font";

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( $heading_font_option_name, 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( $body_font_option_name, 'none' ) );

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;

	if ( ! empty( $et_gf_enqueue_fonts ) ) et_gf_enqueue_fonts( $et_gf_enqueue_fonts );

	if ( ! is_admin() ){
		$template_dir = get_template_directory_uri();

		wp_enqueue_script('jplayer', $template_dir . '/js/jquery.jplayer.min.js', array('jquery'), '2.0.0', false);
		wp_enqueue_script('masonry', $template_dir . '/js/jquery.masonry.min.js', array('jquery'), '2.0.111015', true);
		wp_enqueue_script('custom', $template_dir . '/js/custom.js', array('jquery'), '1.0', true);

		$admin_access = apply_filters( 'et_showcontrol_panel', current_user_can('switch_themes') );
		if ( $admin_access && get_option('notebook_show_control_panel') == 'on' ) {
			wp_enqueue_script('et_colorpicker', $template_dir . '/epanel/js/colorpicker.js', array('jquery'), '1.0', true);
			wp_enqueue_script('et_eye', $template_dir . '/epanel/js/eye.js', array('jquery'), '1.0', true);
			wp_enqueue_script('et_cookie', $template_dir . '/js/jquery.cookie.js', array('jquery'), '1.0', true);
			wp_enqueue_script('et_control_panel', get_bloginfo('template_directory') . '/js/et_control_panel.js', array('jquery'), '1.0', true);
			wp_localize_script( 'et_control_panel', 'notebook_cp', apply_filters( 'notebook_cp_settings', array( 'theme_folder' => $template_dir ) ) );
		}
	}
	if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
}

if ( ! function_exists( 'et_show_audio_interface' ) ){
	function et_show_audio_interface(){
		global $post, $et_jplayer_instances_on_page;

		$et_audiolink_mp3 = get_post_meta( $post->ID, '_et_notebook_audio_mp3_url', true );
		$et_audiolink_ogg = get_post_meta( $post->ID, '_et_notebook_audio_ogg_url', true );
		$themepath = get_template_directory_uri();

		++$et_jplayer_instances_on_page; ?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($){
				$("<?php echo esc_js( "#jquery_jplayer_{$et_jplayer_instances_on_page}" ); ?>").jPlayer({
					ready: function () {
						$(this).jPlayer("setMedia", {
							<?php
								if ( '' != $et_audiolink_mp3 ) echo 'mp3:"' . esc_js( $et_audiolink_mp3 ) . '"';
								if ( '' != $et_audiolink_ogg ) echo ', oga:"' . esc_js( $et_audiolink_ogg ) . '"';
							?>
						});
					},
					swfPath: "<?php echo esc_js( $themepath . '/js'); ?>",
					supplied: "<?php if ( '' != $et_audiolink_ogg ) { echo 'oga, '; } echo 'mp3'; ?>",
					cssSelectorAncestor: "<?php echo esc_attr( "#jp_container_{$et_jplayer_instances_on_page}" ); ?>",
					wmode: "window"
				});
			});
			//]]>
		</script>

		<div id="<?php echo esc_attr( "jquery_jplayer_{$et_jplayer_instances_on_page}" ); ?>" class="jp-jplayer"></div>

		<div id="<?php echo esc_attr( "jp_container_{$et_jplayer_instances_on_page}" ); ?>" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play audio_play" tabindex="1"><?php esc_html_e('Play','Notebook'); ?></a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1"><?php esc_html_e('Pause','Notebook'); ?></a></li>
					</ul>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
				</div>
				<div class="jp-no-solution">
					<span><?php esc_html_e('Update Required','Notebook'); ?></span>
					<?php printf( __('To play the media you will need to either update your browser to a recent version or update your %s.','Notebook'), '<a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>' ); ?>
				</div>
			</div>
		</div>
<?php
	}
}

if ( ! function_exists( 'et_display_single_post' ) ) {
	function et_display_single_post(){
		global $post;
		$format = get_post_format();
		if ( false === $format ) $format = 'standard';
		$et_post_id = (int) get_the_ID();
		$add_permalink = 'on' == get_option('notebook_blog_style') && ! is_single();
	?>
		<article id="<?php echo esc_attr( "post-{$et_post_id}" ); ?>" <?php post_class( 'single_view' ); ?>>
			<?php if (get_option('notebook_integration_single_top') <> '' && get_option('notebook_integrate_singletop_enable') == 'on') echo(get_option('notebook_integration_single_top')); ?>

			<h1 class="main_title"><?php if ( $add_permalink ) echo '<a href="' . esc_url( get_permalink() ) . '">'; ?><?php the_title(); ?><?php if ( $add_permalink ) echo '</a>';?></h1>
			<?php get_template_part('includes/postinfo','single'); ?>

			<div id="post_content">
				<?php
					if ( in_array( $format, array( 'standard', 'image' ) ) ) {
						$thumb = '';
						$width = apply_filters( 'notebook_single_image_width', 220 );
						$height = apply_filters( 'notebook_single_image_height', 220 );

						if ( 'image' == $format ){
							$width = apply_filters( 'notebook_single_imageformat_width', 592 );
							$height = apply_filters( 'notebook_single_imageformat_height', 389 );
						}

						$classtext = '';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Single');
						$thumb = $thumbnail["thumb"]; ?>
						<?php if ( '' <> $thumb && 'on' == get_option( 'notebook_thumbnails' ) ) { ?>
							<div class="single-thumbnail">
								<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
								<span class="post-overlay"></span>
							</div> 	<!-- end .post-thumbnail -->
						<?php }
					} elseif ( 'video' == $format ) { ?>
						<div class="video_box">
							<?php
								global $wp_embed;
								$video_width = apply_filters( 'notebook_single_video_width', 592 );
								$video_height = apply_filters( 'notebook_single_video_height', 389 );

								$video = get_post_meta($post->ID, '_et_notebook_video_url', true);
								$video = apply_filters( 'the_content', $wp_embed->shortcode( '', $video ) );

								$video = preg_replace('/<embed /','<embed wmode="transparent" ',$video);
								$video = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video);

								$video = preg_replace("/width=\"[0-9]*\"/", "width={$video_width}", $video);
								$video = preg_replace("/height=\"[0-9]*\"/", "height={$video_height}", $video);

								echo $video;
							?>
						</div> <!-- end .video_box -->
					<?php } elseif ( 'audio' == $format ) { ?>
						<?php
							$thumb = '';
							$width = apply_filters( 'et_audio_format_image_width', 176 );
							$height = apply_filters( 'et_audio_format_image_height', 176 );
							$classtext = 'audio-image';
							$titletext = strip_tags( get_the_title() );
							$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Audio');
							$thumb = $thumbnail["thumb"];
						?>
						<div class="entry audio">
							<?php if( '' != $thumb ) print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>

							<div class="content">
								<?php et_show_audio_interface(); ?>
							</div> <!-- end .content -->
						</div>
					<?php } ?>
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Notebook').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php edit_post_link(esc_attr__('Edit this page','Notebook')); ?>
			</div> <!-- end #post_content -->

			<div class="post_bottom_bg"></div>
		</article> <!-- end .single_view -->
<?php }
}

add_action('et_header_top','et_notebook_control_panel');
function et_notebook_control_panel(){
	global $themename;

	$admin_access = apply_filters( 'et_showcontrol_panel', current_user_can('switch_themes') );
	if ( !$admin_access ) return;
	if ( get_option('notebook_show_control_panel') <> 'on' ) return;
	global $et_bg_texture_urls, $et_google_fonts; ?>
	<div id="et-control-panel">
		<div id="control-panel-main">
			<a id="et-control-close" href="#"></a>
			<div id="et-control-inner">
				<h3 class="control_title"><?php esc_html_e('Example Colors',$themename); ?></h3>
				<a href="#" class="et-control-colorpicker" id="et-control-background"></a>

				<div class="clear"></div>

				<?php
					$sample_colors = array( '6a8e94', '8da49c', 'b0b083', '859a7c', 'c6bea6', 'b08383', 'a4869d', 'f5f5f5', '4e4e4e', '556f6a', '6f5555', '6f6755' );
					for ( $i=1; $i<=12; $i++ ) { ?>
						<a class="et-sample-setting" id="et-sample-color<?php echo $i; ?>" href="#" rel="<?php echo esc_attr($sample_colors[$i-1]); ?>" title="#<?php echo esc_attr($sample_colors[$i-1]); ?>"><span class="et-sample-overlay"></span></a>
				<?php } ?>
				<p><?php esc_html_e('or define your own in ePanel',$themename); ?></p>

				<h3 class="control_title"><?php esc_html_e('Texture Overlays',$themename); ?></h3>
				<div class="clear"></div>

				<?php
					$sample_textures = $et_bg_texture_urls;
					for ( $i=1; $i<=count($et_bg_texture_urls); $i++ ) { ?>
						<a title="<?php echo esc_attr($sample_textures[$i-1]); ?>" class="et-sample-setting et-texture" id="et-sample-texture<?php echo $i; ?>" href="#" rel="bg<?php echo $i+1; ?>"><span class="et-sample-overlay"></span></a>
				<?php } ?>

				<p><?php esc_html_e('or define your own in ePanel',$themename); ?></p>

				<?php
					$google_fonts = $et_google_fonts;
					$font_setting = 'Lobster';
					$body_font_setting = 'Droid+Sans';
					if ( isset( $_COOKIE['et_notebook_header_font'] ) ) $font_setting = $_COOKIE['et_notebook_header_font'];
					if ( isset( $_COOKIE['et_notebook_body_font'] ) ) $body_font_setting = $_COOKIE['et_notebook_body_font'];
				?>

				<h3 class="control_title"><?php esc_html_e('Fonts',$themename); ?></h3>
				<div class="clear"></div>

				<label for="et_control_header_font"><?php esc_html_e('Header',$themename); ?>
					<select name="et_control_header_font" id="et_control_header_font">
						<?php foreach( $google_fonts as $google_font ) { ?>
							<?php $encoded_value = urlencode($google_font); ?>
							<option value="<?php echo esc_attr($encoded_value); ?>" <?php selected( $font_setting, $encoded_value ); ?>><?php echo esc_html($google_font); ?></option>
						<?php } ?>
					</select>
				</label>
				<a href="#" class="et-control-colorpicker et-font-control" id="et-control-headerfont_bg"></a>
				<div class="clear"></div>

				<label for="et_control_body_font"><?php esc_html_e('Body',$themename); ?>
					<select name="et_control_body_font" id="et_control_body_font">
						<?php foreach( $google_fonts as $google_font ) { ?>
							<?php $encoded_value = urlencode($google_font); ?>
							<option value="<?php echo esc_attr($encoded_value); ?>" <?php selected( $body_font_setting, $encoded_value ); ?>><?php echo esc_html($google_font); ?></option>
						<?php } ?>
					</select>
				</label>
				<a href="#" class="et-control-colorpicker et-font-control" id="et-control-bodyfont_bg"></a>
				<div class="clear"></div>

			</div> <!-- end #et-control-inner -->
		</div> <!-- end #control-panel-main -->
	</div> <!-- end #et-control-panel -->
<?php
}

add_action( 'wp_head', 'et_set_bg_properties' );
function et_set_bg_properties(){
	global $et_bg_texture_urls;

	$bgcolor = '';
	$bgcolor = ( isset( $_COOKIE['et_notebook_bgcolor'] ) && get_option('notebook_show_control_panel') == 'on' ) ? $_COOKIE['et_notebook_bgcolor'] : get_option('notebook_bgcolor');

	$bgtexture_url = '';
	$bgimage_url = '';
	if ( get_option('notebook_bgimage') == '' ) {
		if ( isset( $_COOKIE['et_notebook_texture_url'] ) && get_option('notebook_show_control_panel') == 'on' ) $bgtexture_url =  $_COOKIE['et_notebook_texture_url'];
		else {
			$bgtexture_url = get_option('notebook_bgtexture_url');
			if ( $bgtexture_url == 'Default' ) $bgtexture_url = '';
			else $bgtexture_url = get_bloginfo('template_directory') . '/images/control_panel/body-bg' . ( array_search( $bgtexture_url, $et_bg_texture_urls )+2 ) . '.png';
		}
	} else {
		$bgimage_url = get_option('notebook_bgimage');
	}

	$style = '';
	$style .= '<style type="text/css">';
	if ( $bgcolor <> '' ) $style .= 'body { background-color: #' . esc_attr($bgcolor) . '; }';
	if ( $bgtexture_url <> '' ) $style .= 'body { background-image: url(' . esc_attr($bgtexture_url) . '); }';
	if ( $bgimage_url <> '' ) $style .= 'body { background-image: url(' . esc_attr($bgimage_url) . '); background-position: top center; background-repeat: no-repeat; }';
	$style .= '</style>';

	if ( $bgcolor <> '' || $bgtexture_url <> '' || $bgimage_url <> '' ) echo $style;
}

add_action( 'wp_head', 'et_set_font_properties' );
function et_set_font_properties(){
	$font_style = '';
	$font_color = '';
	$font_family = '';
	$font_color_string = '';

	if ( isset( $_COOKIE['et_notebook_header_font'] ) && get_option('notebook_show_control_panel') == 'on' ) $et_header_font =  $_COOKIE['et_notebook_header_font'];
	else {
		$et_header_font = get_option('notebook_header_font');
		if ( $et_header_font == 'Lobster' ) $et_header_font = '';
	}

	if ( isset( $_COOKIE['et_notebook_header_font_color'] ) && get_option('notebook_show_control_panel') == 'on' )
		$et_header_font_color =  $_COOKIE['et_notebook_header_font_color'];
	else
		$et_header_font_color = get_option('notebook_header_font_color');

	if ( $et_header_font <> '' || $et_header_font_color <> '' ) {
		$et_header_font_id = strtolower( str_replace( '+', '_', $et_header_font ) );
		$et_header_font_id = str_replace( ' ', '_', $et_header_font_id );

		if ( $et_header_font <> '' ) {
			$font_style .= "<link id='" . esc_attr($et_header_font_id) . "' href='" . esc_url( "http://fonts.googleapis.com/css?family=" . str_replace( ' ', '+', $et_header_font )  . ( 'Raleway' == $et_header_font ? ':100' : '' ) ) . "' rel='stylesheet' type='text/css' />";
			$font_family = "font-family: '" . esc_html(str_replace( '+', ' ', $et_header_font )) . "', Arial, sans-serif !important; ";
		}

		if ( $et_header_font_color <> '' ) {
			$font_color_string = "color: #" . esc_html($et_header_font_color) . "; ";
		}

		$font_style .= "<style type='text/css'>h1,h2,h3,h4,h5,h6 { ". $font_family .  " }</style>";
		$font_style .= "<style type='text/css'>h1,h2,h3,h4,h5,h6 { ". esc_html($font_color_string) .  " }
		</style>";

		echo $font_style;
	}

	$font_style = '';
	$font_color = '';
	$font_family = '';
	$font_color_string = '';

	if ( isset( $_COOKIE['et_notebook_body_font'] ) && get_option('notebook_show_control_panel') == 'on' ) $et_body_font =  $_COOKIE['et_notebook_body_font'];
	else {
		$et_body_font = get_option('notebook_body_font');
		if ( $et_body_font == 'Droid+Sans' ) $et_body_font = '';
	}

	if ( isset( $_COOKIE['et_notebook_body_font_color'] ) && get_option('notebook_show_control_panel') == 'on' )
		$et_body_font_color =  $_COOKIE['et_notebook_body_font_color'];
	else
		$et_body_font_color = get_option('notebook_body_font_color');

	if ( $et_body_font <> '' || $et_body_font_color <> '' ) {
		$et_body_font_id = strtolower( str_replace( '+', '_', $et_body_font ) );
		$et_body_font_id = str_replace( ' ', '_', $et_body_font_id );

		if ( $et_body_font <> '' ) {
			$font_style .= "<link id='" . esc_attr($et_body_font_id) . "' href='" . esc_url( "http://fonts.googleapis.com/css?family=" . str_replace( ' ', '+', $et_body_font ) . ( 'Raleway' == $et_body_font ? ':100' : '' ) ) . "' rel='stylesheet' type='text/css' />";
			$font_family = "font-family: '" . esc_html(str_replace( '+', ' ', $et_body_font )) . "', Arial, sans-serif !important; ";
		}

		if ( $et_body_font_color <> '' ) {
			$font_color_string = "color: #" . esc_html($et_body_font_color) . "; ";
		}

		$font_style .= "<style type='text/css'>body, .entry.normal-post .content p { ". $font_family .  " !important }</style>";
		$font_style .= "<style type='text/css'>body { ". esc_html($font_color_string) .  " }</style>";

		echo $font_style;
	}
}

function et_epanel_custom_colors_css(){
	global $shortname; ?>

	<style type="text/css">
		body { color: #<?php echo esc_html(get_option($shortname.'_color_mainfont')); ?>; }
		#content-area a { color: #<?php echo esc_html(get_option($shortname.'_color_mainlink')); ?>; }
		ul.nav li a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink')); ?> !important; }
		ul.nav > li.current_page_item > a, ul#top-menu > li:hover > a, ul.nav > li.current-cat > a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink_active')); ?>; }
		h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: #<?php echo esc_html(get_option($shortname.'_color_headings')); ?>; }

		#sidebar a { color:#<?php echo esc_html(get_option($shortname.'_color_sidebar_links')); ?>; }
		.footer-widget { color:#<?php echo esc_html(get_option($shortname.'_footer_text')); ?> }
		#footer a, ul#bottom-menu li a { color:#<?php echo esc_html(get_option($shortname.'_color_footerlinks')); ?> }
	</style>

<?php }

if ( function_exists( 'get_custom_header' ) ) {
	// compatibility with versions of WordPress prior to 3.4

	add_action( 'customize_register', 'et_notebook_customize_register' );
	function et_notebook_customize_register( $wp_customize ) {
		$google_fonts = et_get_google_fonts();

		$font_choices = array();
		$font_choices['none'] = 'Default Theme Font';
		foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
			$font_choices[ $google_font_name ] = $google_font_name;
		}

		$wp_customize->remove_section( 'title_tagline' );
		$wp_customize->remove_section( 'background_image' );
		$wp_customize->remove_section( 'colors' );

		$wp_customize->add_section( 'et_google_fonts' , array(
			'title'		=> __( 'Fonts', 'Notebook' ),
			'priority'	=> 50,
		) );

		$wp_customize->add_setting( 'notebook_heading_font', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'notebook_heading_font', array(
			'label'		=> __( 'Header Font', 'Notebook' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'notebook_heading_font',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );

		$wp_customize->add_setting( 'notebook_body_font', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'notebook_body_font', array(
			'label'		=> __( 'Body Font', 'Notebook' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'notebook_body_font',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );
	}

	add_action( 'wp_head', 'et_notebook_add_customizer_css' );
	add_action( 'customize_controls_print_styles', 'et_notebook_add_customizer_css' );
	function et_notebook_add_customizer_css(){ ?>
		<style type="text/css">
		<?php
			global $shortname;

			$et_prefix = ! et_options_stored_in_one_row() ? "{$shortname}_" : '';
			$heading_font_option_name = "{$et_prefix}heading_font";
			$body_font_option_name = "{$et_prefix}body_font";

			$et_gf_heading_font = sanitize_text_field( et_get_option( $heading_font_option_name, 'none' ) );
			$et_gf_body_font = sanitize_text_field( et_get_option( $body_font_option_name, 'none' ) );

			if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font ) :

				if ( 'none' != $et_gf_heading_font )
					et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6' );

				if ( 'none' != $et_gf_body_font )
					et_gf_attach_font( $et_gf_body_font, 'body' );

			endif;
		?>
		</style>
	<?php }

	add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );
	function et_load_google_fonts_scripts() {
		wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), '1.0', true );
		wp_localize_script( 'et_google_fonts', 'et_google_fonts', array(
			'options_in_one_row' => ( et_options_stored_in_one_row() ? 1 : 0 )
		) );
	}

	add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );
	function et_load_google_fonts_styles() {
		wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), null );
	}
}
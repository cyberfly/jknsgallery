<?php
	global $wp_embed;
	$et_video_id = 'et_video_post_' . get_the_ID();
	$et_videolink = get_post_meta( get_the_ID(), '_et_notebook_video_url', true );
	$et_videos_output = '';
	if ( '' != $et_videolink ) $et_videos_output = '<div id="'. esc_attr( $et_video_id ) .'">' . apply_filters( 'the_content', $wp_embed->shortcode( '', $et_videolink ) ) . '</div>';
	$et_class = 'video';
	if ( 'false' == get_option('notebook_blog_style') ) $et_class .= ' entry';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $et_class ); ?>>
	<?php
		$thumb = '';
		$width = apply_filters( 'et_video_format_image_width', 280 );
		$height = apply_filters( 'et_video_format_image_height', 187 );
		$classtext = 'video-image';
		$titletext = sanitize_text_field( get_the_title() );
		$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Photo');
		$thumb = $thumbnail["thumb"];
	?>
	<?php if( '' != $thumb && 'on' == get_option('notebook_thumbnails_index') ) { ?>
		<div class="img">
			<a href="<?php echo esc_url( '#' . $et_video_id ); ?>" class="fancybox" title="<?php echo esc_attr( $titletext ); ?>">
				<span class="video_box">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
				</span>
				<span class="overlay"></span>
				<span class="play"></span>
			</a>
		</div> 	<!-- end .img -->
	<?php } ?>

	<div class="content">
		<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<?php get_template_part('includes/postinfo'); ?>
	</div>
</article> <!-- end .entry-->
<?php if ( '' != $et_videos_output ) echo '<div class="et_embedded_videos">' . $et_videos_output . '</div>'; ?>
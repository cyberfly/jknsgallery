<?php
	$et_post_id = (int) get_the_ID();
	$et_class = 'audio';
	if ( 'false' == get_option('notebook_blog_style') ) $et_class .= ' entry';
?>
<article id="<?php echo esc_attr( "post-{$et_post_id}" ); ?>" <?php post_class( $et_class ); ?>>
	<?php
		$thumb = '';
		$width = apply_filters( 'et_audio_format_image_width', 176 );
		$height = apply_filters( 'et_audio_format_image_height', 176 );
		$classtext = 'audio-image';
		$titletext = strip_tags( get_the_title() );
		$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Audio');
		$thumb = $thumbnail["thumb"];
	?>
	<?php if( '' != $thumb && 'on' == get_option('notebook_thumbnails_index') ) { ?>
		<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
	<?php } ?>

	<div class="content">
		<div class="audio_info">
			<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php get_template_part('includes/postinfo'); ?>
		</div> <!-- end .audio_info -->

		<?php et_show_audio_interface(); ?>

	</div> <!-- end .content -->
</article> <!-- end .entry -->
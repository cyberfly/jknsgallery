<?php
	$et_class = 'image';
	if ( 'false' == get_option('notebook_blog_style') ) $et_class .= ' entry';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $et_class ); ?>>
	<?php
		$thumb = '';
		$width = apply_filters( 'et_image_format_image_width', 280 );
		$height = apply_filters( 'et_image_format_image_height', 187 );
		$classtext = 'entry-image';
		$titletext = strip_tags( get_the_title() );
		$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,true,'Photo');
		$thumb = $thumbnail["thumb"];
	?>
	<?php if( '' != $thumb && 'on' == get_option('notebook_thumbnails_index') ) { ?>
		<div class="photo">
			<a href="<?php echo esc_attr( $thumbnail["fullpath"] ); ?>" class="fancybox" title="<?php echo esc_attr( $titletext ); ?>">
				<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
				<span class="overlay"></span>
				<span class="zoom"></span>
			</a>
			<span class="tape"></span>
		</div> 	<!-- end .photo -->
	<?php } ?>

	<div class="content">
		<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php get_template_part('includes/postinfo'); ?>
	</div> <!-- end .content-->
</article> <!-- end .entry-->
<?php
	$et_class = 'normal-post';
	if ( 'false' == get_option('notebook_blog_style') ) $et_class .= ' entry';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $et_class ); ?>>
	<div class="main_content">
		<?php
			$thumb = '';
			$width = apply_filters( 'et_normal_format_image_width', 280 );
			$height = apply_filters( 'et_normal_format_image_height', 117 );
			$classtext = 'n-post-image';
			$titletext = strip_tags( get_the_title() );
			$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Entry');
			$thumb = $thumbnail["thumb"];
		?>
		<?php if( '' != $thumb && 'on' == get_option('notebook_thumbnails_index') ) { ?>
			<div class="thumb">
				<a href="<?php the_permalink(); ?>">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
					<span class="overlay"></span>
				</a>
			</div> 	<!-- end .thumb -->
		<?php } ?>

		<div class="content">
			<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

			<?php get_template_part('includes/postinfo'); ?>

			<p><?php truncate_post(75); ?></p>
		</div> <!-- end .content-->
	</div> <!-- end .main_content-->

	<div class="center_layer"></div>
	<div class="bottom_layer"></div>
</article> <!-- end .entry-->
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article class="single_view">
		<h1 class="main_title"><?php the_title(); ?></h1>

		<div id="post_content">
			<?php if (get_option('notebook_page_thumbnails') == 'on') { ?>
				<?php
					$thumb = '';
					$width = apply_filters( 'notebook_single_image_width', 220 );
					$height = apply_filters( 'notebook_single_image_height', 220 );
					$classtext = '';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Single');
					$thumb = $thumbnail["thumb"];
				?>

				<?php if($thumb <> '') { ?>
					<div class="single-thumbnail">
						<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
						<span class="post-overlay"></span>
					</div> 	<!-- end .post-thumbnail -->
				<?php } ?>
			<?php } ?>

			<?php the_content(); ?>
			<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Notebook').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			<?php edit_post_link(esc_attr__('Edit this page','Notebook')); ?>
		</div>
	</article> <!-- end .single_view -->
<?php endwhile; // end of the loop. ?>
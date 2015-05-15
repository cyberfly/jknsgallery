<?php $notebook_blog_style = get_option('notebook_blog_style'); ?>

<?php
	if ( 'on' == $notebook_blog_style ) echo '<div id="regular_content">';
	else echo '<div id="et_posts">';
?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php
			if ( 'false' == $notebook_blog_style ) get_template_part( 'content', get_post_format() );
			else et_display_single_post();
		?>
	<?php endwhile; ?>
<?php if ( 'false' == $notebook_blog_style ) echo '</div>';?>
	<?php if ( function_exists('wp_pagenavi') ) { wp_pagenavi(); }
		else { ?>
			 <?php get_template_part('includes/navigation'); ?>
		<?php } ?>
	<?php else : ?>
		<?php get_template_part('includes/no-results'); ?>
	<?php endif; ?>
<?php if ( 'on' == $notebook_blog_style ) echo '</div>';?>
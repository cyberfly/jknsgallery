<?php get_header(); ?>

<div id="regular_content">
	<?php get_template_part('includes/breadcrumbs','index'); ?>

	<?php get_template_part('loop','single'); ?>

	<?php if ( 'on' == get_option('notebook_show_postcomments') ) comments_template('', true); ?>
</div> <!-- end #regular_content -->

<?php get_footer(); ?>
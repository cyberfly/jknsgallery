<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php et_display_single_post(); ?>

	<?php if (get_option('notebook_integration_single_bottom') <> '' && get_option('notebook_integrate_singlebottom_enable') == 'on') echo(get_option('notebook_integration_single_bottom')); ?>

	<?php
		if ( get_option('notebook_468_enable') == 'on' ){
			if ( get_option('notebook_468_adsense') <> '' ) echo( get_option('notebook_468_adsense') );
			else { ?>
			   <a href="<?php echo esc_url(get_option('notebook_468_url')); ?>"><img src="<?php echo esc_attr(get_option('notebook_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
	<?php 	}
		}
	?>
<?php endwhile; // end of the loop. ?>
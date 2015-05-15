<?php
	$post_id = get_the_ID();
	$single_postinfo = et_get_option( 'origin_postinfo2' );

	$et_settings = array();
	$et_settings = maybe_unserialize( get_post_meta( $post_id, '_et_origin_settings', true ) );

	$big_thumbnail = isset( $et_settings['thumbnail'] ) ? $et_settings['thumbnail'] : '';

	if ( '' != $big_thumbnail ) echo '<div style="background-image: url(' . esc_url( $big_thumbnail ) . ');" id="big_thumbnail"></div>';
?>

<div id="main-content"<?php if ( '' == $big_thumbnail ) echo ' class="et-no-big-image"'; ?>>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php if (et_get_option('origin_integration_single_top') <> '' && et_get_option('origin_integrate_singletop_enable') == 'on') echo (et_get_option('origin_integration_single_top')); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content clearfix' ); ?>>
		<div class="main-title">
			<h1><?php the_title(); ?></h1>
		<?php
			if ( $single_postinfo ){
				echo '<p class="meta-info">';
				et_postinfo_meta( $single_postinfo, et_get_option('origin_date_format'), esc_html__('0 comments','Origin'), esc_html__('1 comment','Origin'), '% ' . esc_html__('comments','Origin') );
				echo '</p>';
			}
		?>
		</div> <!-- .main-title -->

	<?php if ( ( has_post_thumbnail( $post_id ) || '' != get_post_meta( $post_id, 'Thumbnail', true ) ) && 'on' == et_get_option( 'origin_thumbnails' ) ) { ?>
		<div class="post-thumbnail">
		<?php
			if ( has_post_thumbnail( $post_id ) ) the_post_thumbnail( 'full' );
			else printf( '<img src="%1$s" alt="%2$s" />', esc_attr( get_post_meta( $post_id, 'Thumbnail', true ) ), the_title_attribute( array( 'echo' => 0 ) ) );
		?>
		</div> 	<!-- end .post-thumbnail -->
	<?php } ?>

		<?php the_content(); ?>

		<!-- show EXIF -->

		<h3>EXIF</h3>
		<ul>

		<?php

		yapb_exif(
		'li-exif', // CSS Class for the LIs
		': ', // Separator between EXIF token name and value
		'<strong>', // HTML before EXIF token name
		'</strong>', // HTML after EXIF token name
		'<i>', // HTML before EXIF token value
		'</i>' // HTML after EXIF token value
		)

		?>

		<?php

		//get the full URL of the post's YAPB image
		$exifimg = site_url() . $post->image->uri;

		//use the function we created to get the EXIF data
		$camera = cameraUsed( $exifimg );

		//display the EXIF data using PHP and a whole lot of "echo"
		//note that quotes must be escaped by a backslash, see first line below

		//start an unordered list
		echo "<ul class=\"ul-exif\">";

		//generate the list items, if they exist
		if (!empty($camera['make'])) {
		  echo "<li class=\"li-exif\">Camera Make: <i>" . $camera['make'] . "</i></li>";
		}
		if (!empty($camera['model'])) {
		  echo "<li class=\"li-exif\">Camera Model: <i>" . $camera['model'] . "</i></li>";
		}
		if (!empty($camera['lensmake'])) {
		  echo "<li class=\"li-exif\">Lens Make: <i>" . $camera['lensmake'] . "</i></li>";
		}
		if (!empty($camera['lens'])) {
		  echo "<li class=\"li-exif\">Lens Model: <i>" . $camera['lens'] . "</i></li>";
		}
		if (!empty($camera['exposure'])) {
		  echo "<li class=\"li-exif\">Shutter Speed: <i>" . $camera['exposure'] . "</i></li>";
		}
		if (!empty($camera['aperture'])) {
		  echo "<li class=\"li-exif\">Aperture: <i>" . $camera['aperture'] . "</i></li>";
		}
		if (!empty($camera['iso'])) {
		  echo "<li class=\"li-exif\">ISO Value: <i>" . $camera['iso'] . "</i></li>";
		}
		if (!empty($camera['focallength'])) {
		  echo "<li class=\"li-exif\">Focal Length: <i>" . $camera['focallength'] . "</i></li>";
		}
		if (!empty($camera['focallength35'])) {
		  echo "<li class=\"li-exif\">35mm-equiv.: <i>" . $camera['focallength35'] . "</i></li>";
		}
		if (!empty($camera['flashdata'])) {
		  echo "<li class=\"li-exif\">Flash: <i>" . $camera['flashdata'] . "</i></li>";
		}
		if (!empty($camera['distance'])) {
		  echo "<li class=\"li-exif\">Focus Distance: <i>" . $camera['distance'] . "</i></li>";
		}
		//close the unordered list
		echo "</ul>";
		?>


		<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Origin').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		<?php edit_post_link(esc_attr__('Edit this page','Origin')); ?>

	</article> <!-- end .entry-content -->

	<?php if (et_get_option('origin_integration_single_bottom') <> '' && et_get_option('origin_integrate_singlebottom_enable') == 'on') echo(et_get_option('origin_integration_single_bottom')); ?>

	<?php
		if ( et_get_option('origin_468_enable') == 'on' ){
			if ( et_get_option('origin_468_adsense') <> '' ) echo( et_get_option('origin_468_adsense') );
			else { ?>
			   <a href="<?php echo esc_url(et_get_option('origin_468_url')); ?>"><img src="<?php echo esc_attr(et_get_option('origin_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
	<?php 	}
		}
	?>

	<?php
		if ( 'on' == et_get_option('origin_show_postcomments') ) comments_template('', true);
	?>
<?php endwhile; // end of the loop. ?>

</div> <!-- #main-content -->
<?php if ( ! is_single() && 'false' == get_option('notebook_blog_style') && ( $index_postinfo = get_option('notebook_postinfo1') ) && $index_postinfo ) { ?>
	<p class="meta">
		<?php et_postinfo_meta( $index_postinfo, get_option('notebook_date_format'), esc_html__('0 comments','Notebook'), esc_html__('1 comment','Notebook'), '% ' . esc_html__('comments','Notebook') ); ?>
	</p>
<?php } elseif ( ( is_single() && get_option('notebook_postinfo2') ) || 'on' == get_option('notebook_blog_style') ) { ?>
	<p class="meta-info">
		<?php global $query_string;
		$new_query = new WP_Query($query_string);
		while ($new_query->have_posts()) $new_query->the_post(); ?>
			<?php et_postinfo_meta( get_option('notebook_postinfo2'), get_option('notebook_date_format'), esc_html__('0 comments','Notebook'), esc_html__('1 comment','Notebook'), '% ' . esc_html__('comments','Notebook') ); ?>
		<?php wp_reset_postdata() ?>
	</p>
<?php }; ?>
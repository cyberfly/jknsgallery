<?php
add_action( 'admin_enqueue_scripts', 'import_epanel_javascript' );
function import_epanel_javascript( $hook_suffix ) {
	if ( 'admin.php' == $hook_suffix && isset( $_GET['import'] ) && isset( $_GET['step'] ) && 'wordpress' == $_GET['import'] && '1' == $_GET['step'] )
		add_action( 'admin_head', 'admin_headhook' );
}

function admin_headhook(){
	global $themename; ?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$("p.submit").before("<p><input type='checkbox' id='importepanel' name='importepanel' value='1' style='margin-right: 5px;'><label for='importepanel'><?php esc_html_e('Import epanel settings', $themename ); ?></label></p>");
		});
	</script>
<?php }

add_action('import_end','importend');
function importend(){
	global $wpdb, $shortname;

	#make custom fields image paths point to sampledata/sample_images folder
	$sample_images_postmeta = $wpdb->get_results(
		$wpdb->prepare( "SELECT meta_id, meta_value FROM $wpdb->postmeta WHERE meta_value REGEXP %s", 'http://et_sample_images.com' )
	);
	if ( $sample_images_postmeta ) {
		foreach ( $sample_images_postmeta as $postmeta ){
			$template_dir = get_template_directory_uri();
			if ( is_multisite() ){
				switch_to_blog(1);
				$main_siteurl = site_url();
				restore_current_blog();

				$template_dir = $main_siteurl . '/wp-content/themes/' . get_template();
			}
			preg_match( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $postmeta->meta_value, $matches );
			$image_path = $matches[1];

			$local_image = preg_replace( '/http:\/\/et_sample_images.com\/([^.]+).jpg/', $template_dir . '/sampledata/sample_images/$1.jpg', $postmeta->meta_value );

			$local_image = preg_replace( '/s:55:/', 's:' . strlen( $template_dir . '/sampledata/sample_images/' . $image_path . '.jpg' ) . ':', $local_image );

			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => esc_url_raw( $local_image ) ), array( 'meta_id' => $postmeta->meta_id ), array( '%s' ) );
		}
	}

	if ( !isset($_POST['importepanel']) )
		return;

	$importedOptions = array(
		$shortname . '_logo' => '',
		$shortname . '_favicon' => '',
		$shortname . '_bgcolor' => '',
		$shortname . '_bgtexture_url' => 'Default',
		$shortname . '_bgimage' => '',
		$shortname . '_header_font' => 'Kreon',
		$shortname . '_header_font_color' => '',
		$shortname . '_body_font' => 'Droid Sans',
		$shortname . '_body_font_color' => '',
		$shortname . '_catnum_posts' => '6',
		$shortname . '_archivenum_posts' => '5',
		$shortname . '_searchnum_posts' => '5',
		$shortname . '_tagnum_posts' => '5',
		$shortname . '_date_format' => 'M j, Y',
		$shortname . '_show_control_panel' => 'on',
		$shortname . '_homepage_posts' => '7',
		$shortname . '_menupages' => array( 724 ),
		$shortname . '_enable_dropdowns' => 'on',
		$shortname . '_home_link' => 'on',
		$shortname . '_sort_pages' => 'post_title',
		$shortname . '_order_page' => 'asc',
		$shortname . '_tiers_shown_pages' => '3',
		$shortname . '_menucats' => array( 14, 15, 1 ),
		$shortname . '_enable_dropdowns_categories' => 'on',
		$shortname . '_categories_empty' => 'on',
		$shortname . '_tiers_shown_categories' => '3',
		$shortname . '_sort_cat' => 'name',
		$shortname . '_order_cat' => 'asc',
		$shortname . '_postinfo2' => array( 'author', 'date', 'categories', 'comments' ),
		$shortname . '_thumbnails' => 'on',
		$shortname . '_show_postcomments' => 'on',
		$shortname . '_postinfo1' => array( 'author', 'date', 'categories', 'comments' ),
		$shortname . '_thumbnails_index' => 'on',
		$shortname . '_child_cssurl' => '',
		$shortname . '_color_mainfont' => '',
		$shortname . '_color_mainlink' => '',
		$shortname . '_color_pagelink' => '',
		$shortname . '_color_pagelink_active' => '',
		$shortname . '_color_headings' => '',
		$shortname . '_color_sidebar_links' => '',
		$shortname . '_footer_text' => '',
		$shortname . '_color_footerlinks' => '',
		$shortname . '_seo_home_titletext' => '',
		$shortname . '_seo_home_descriptiontext' => '',
		$shortname . '_seo_home_keywordstext' => '',
		$shortname . '_seo_home_type' => 'BlogName | Blog description',
		$shortname . '_seo_home_separate' => ' | ',
		$shortname . '_seo_single_field_title' => 'seo_title',
		$shortname . '_seo_single_field_description' => 'seo_description',
		$shortname . '_seo_single_field_keywords' => 'seo_keywords',
		$shortname . '_seo_single_type' => 'Post title | BlogName',
		$shortname . '_seo_single_separate' => ' | ',
		$shortname . '_seo_index_type' => 'Category name | BlogName',
		$shortname . '_seo_index_separate' => ' | ',
		$shortname . '_integrate_header_enable' => 'on',
		$shortname . '_integrate_body_enable' => 'on',
		$shortname . '_integrate_singletop_enable' => 'on',
		$shortname . '_integrate_singlebottom_enable' => 'on',
		$shortname . '_integration_head' => '',
		$shortname . '_integration_body' => '',
		$shortname . '_integration_single_top' => '',
		$shortname . '_integration_single_bottom' => '',
		$shortname . '_468_image' => '',
		$shortname . '_468_url' => '',
		$shortname . '_468_adsense' => ''
	);

	foreach ( $importedOptions as $key => $value ) {
		if ( $value != '' ) update_option( $key, $value );
	}

	update_option( $shortname . '_use_pages', 'false' );
} ?>
<?php

/* Meta boxes */

function notebook_settings(){
	add_meta_box("et_post_meta", "ET Settings", "notebook_display_options", "post", "normal", "high");
}
add_action("admin_init", "notebook_settings");

function notebook_display_options($callback_args) {
	global $post, $themename;

	$et_fs_video = get_post_meta( $post->ID, '_et_notebook_video_url', true );
	$et_fs_audio_mp3 = get_post_meta( $post->ID, '_et_notebook_audio_mp3_url', true );
	$et_fs_audio_ogg = get_post_meta( $post->ID, '_et_notebook_audio_ogg_url', true );
?>

	<?php wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' ); ?>

	<div id="et_custom_settings" style="margin: 13px 0 17px 4px;">

		<h4 style="font-size: 13px;"><?php esc_html_e('Video Post Format Settings: ',$themename); ?></h4>

		<div class="et_fs_setting" style="margin: 13px 0 26px 4px;">
			<label for="et_fs_video" style="color: #000; font-weight: bold;"> <?php esc_html_e('Video url:',$themename); ?> </label>
			<input type="text" style="width: 30em;" value="<?php echo esc_url($et_fs_video); ?>" id="et_fs_video" name="et_fs_video" size="67" />
			<br />
			<small style="position: relative; top: 8px;"><?php esc_html_e('You can put Youtube or Vimeo link here.', $themename); ?> ex: <code><?php echo htmlspecialchars("http://www.youtube.com/watch?v=WkuHbkaieZ4");?></code></small>
		</div>

		<h4 style="font-size: 13px; margin-top: 45px;"><?php esc_html_e('Audio Post Format Settings: ',$themename); ?></h4>

		<div class="et_fs_setting" style="margin: 13px 0 26px 4px;">
			<label for="et_fs_audio_mp3" style="color: #000; font-weight: bold;"> <?php esc_html_e('Audio Mp3 Url:',$themename); ?> </label>
			<input type="text" style="width: 30em;" value="<?php echo esc_url($et_fs_audio_mp3); ?>" id="et_fs_audio_mp3" name="et_fs_audio_mp3" size="67" />
			<br />
			<small style="position: relative; top: 8px;">ex: <code><?php echo htmlspecialchars("http://www.jplayer.org/audio/m4a/Miaow-07-Bubble.m4a");?></code></small>
		</div>

		<div class="et_fs_setting" style="margin: 13px 0 26px 4px;">
			<label for="et_fs_audio_ogg" style="color: #000; font-weight: bold;"> <?php esc_html_e('Audio Ogg Url:',$themename); ?> </label>
			<input type="text" style="width: 30em;" value="<?php echo esc_url($et_fs_audio_ogg); ?>" id="et_fs_audio_ogg" name="et_fs_audio_ogg" size="67" />
			<br />
			<small style="position: relative; top: 8px;">ex: <code><?php echo htmlspecialchars("http://www.jplayer.org/audio/ogg/Miaow-07-Bubble.ogg");?></code></small>
		</div>

	</div> <!-- #et_custom_settings -->

	<?php
}

add_action( 'save_post', 'notebook_save_details', 10, 2 );
function notebook_save_details( $post_id, $post ){
	global $pagenow;
	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || !wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	if ( isset($_POST["et_fs_video"]) ) update_post_meta( $post_id, "_et_notebook_video_url", esc_url_raw( $_POST["et_fs_video"] ) );
	if ( isset($_POST["et_fs_audio_mp3"]) ) update_post_meta( $post_id, "_et_notebook_audio_mp3_url", esc_url_raw( $_POST["et_fs_audio_mp3"] ) );
	if ( isset($_POST["et_fs_audio_ogg"]) ) update_post_meta( $post_id, "_et_notebook_audio_ogg_url", esc_url_raw( $_POST["et_fs_audio_ogg"] ) );
}
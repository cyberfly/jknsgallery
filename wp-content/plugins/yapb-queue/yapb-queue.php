<?php
/*
	Plugin Name: yapb-queue
	Plugin URI:
	Description: Schedule YAPB images from a directory with an interval of time.<br /> Read iptcs set the title, body and tags.<br />Perfect for photoblogging.
	Version: 1.0.7
	Author: Fran Sim&oacute;
	Author URI: http://justpictures.es/
*/

add_action('admin_menu', 'yapbq_add');
add_action('activate_yapb-queue/yapb-queue.php','yapbq_install');

function yapbq_add(){
	add_posts_page('FTP Import Image','FTP Import Image', 1, basename(__FILE__), 'yapbq_page');
}

function yapq_form($start_date) {
	$uploads=wp_upload_dir();
	$yapbq_input_dir=$uploads['basedir'].'/yapbq_input';
?> <br/>
	<form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post">
	Image from directory <strong><?php echo $yapbq_input_dir; ?></strong> will be queued.<br/><br/>
	First image will be posted at start date: <input name="start_date" type="text" size="30" value="<?php echo $start_date; ?>" /><br />
	and the next image will be posted  <input name="hours_period" type="text" size="5" value="24" /> hours after.<br /><br />
	<input name="save" type="submit" value="Process Queue" tabindex="24" accesskey="S" />
	<input name="action" type="hidden" value="proccess" />
	</form>
<?php }


function yapbq_page() {
	echo '<div id="wpbody-content">';
	if (!class_exists("Yapb")) {
		echo '<strong>YAPB queue</strong> needs <a href="http://johannes.jarolim.com/yapb">YAPB plugin</a>';
	} else {
		if ( $_GET['page'] == basename(__FILE__) ) {
			if ( 'proccess' == $_REQUEST['action'] ) {
				$start_date=$_REQUEST['start_date'];
				$hours_period=$_REQUEST['hours_period'];
				if ((strtotime($start_date)>0) && ($hours_period>0)) {
					yapbq_proc_queue($start_date, $hours_period);
				} else {
					echo 'Invalid date';
				}
			} else {
				$start_date=yapbq_start_date();
				yapq_form($start_date);
			}
		}
	}
	echo '</div>';
}


function yapbq_start_date() {
	//get the most future schuedle post
	$wp_posts_p = array();
	$wp_posts_p['post_type']='post';
	$wp_posts_p['post_status']='future';
	$wp_posts_p['numberposts']=1;
	$wp_posts=get_posts($wp_posts_p);
	//print_r($wp_posts);
	if (!empty($wp_posts)) {
		$t=new stdClass;
		$t=$wp_posts[0];
		$start_date=$t->post_date;
	} else {
		$start_date=current_time('mysql');
	}
	return $start_date;
}

function yapbq_proc_queue($start_date='',$hours_period=24) {
	$uploads=wp_upload_dir();
	$yapbq_input_dir=$uploads['basedir'].'/yapbq_input';
	$yapbq_output_dir=$uploads['path'];

	$yapbq_output_final_uri_dir=$uploads['url'];
	$yapbq_output_final_uri_dir=substr($yapbq_output_final_uri_dir, strlen('http://'.$_SERVER['SERVER_NAME']));

	/*
	echo "d:".$directory_install."<br />";
	echo "f:".$yapbq_output_final_uri_dir."<br />";
	print_r($uploads);
	echo "<br />";
	$yapbq_directory_install=substr(get_option('siteurl'), strlen('http://'.$_SERVER['SERVER_NAME']));
	die();
	*/

	// Get the number of YAPB images to name the post numerically.
	$ypmb= new YapbMaintainance;
	$yapb_count=$ypmb->getImagefileCount();


	echo "Start sequence: $yapb_count<br />";


	//Get the list of files into the queue directory.
	yapbq_log("yapbq_input_dir: $yapbq_input_dir");
	$dirlist = list_files($yapbq_input_dir);
	sort($dirlist); //just in case

	echo "Start date: $start_date<br />"; yapbq_log("Start date: $start_date");
	echo "Hours between post: $hours_period<br /><br />"; yapbq_log("Hours between post: $hours_period");

	$qcount=1;
	foreach ($dirlist as $file) {
		$file_base = substr(sanitize_file_name(basename($file)), -150);
		if (yapbq_check_extension($file_base)) {

			$file_dest=$yapbq_output_dir.'/'.$file_base;
			$yapbq_output_final_uri = $yapbq_output_final_uri_dir .'/'.$file_base;
			$f=1;

			while (is_file($file_dest)) {
				$file_base = $f.'_'.substr(sanitize_file_name(basename($file)), -150);
				$file_dest=$yapbq_output_dir.'/'.$file_base;
				$yapbq_output_final_uri = $yapbq_output_final_uri_dir .'/'.$file_base;
				$f++;
			}
			rename($file,$file_dest);
			echo $file_base."<br />";

			// $datep=gmdate( 'Y-m-d H:i:s', ( strtotime ($start_date) + ( ($qcount-1) * 3600 * $hours_period ) ) );
			$datep=gmdate( 'Y-m-d H:i:s', ( strtotime ($start_date)  ) );
			echo $datep.'<br /><br />';

			yapbq_proc_image($yapbq_output_final_uri, $yapb_count+$qcount, $datep);
			$qcount++;
		}
	}

}

function yapbq_proc_image($uri,$yapb_count,$date) {

	//reuse form post of WP. This will kick-off YAPB as well

	$my_post = array();
	$my_post['post_title'] = '#'.$yapb_count;
	$my_post['post_status'] = 'publish';
	$my_post['post_date'] = $date;


	$postid = wp_insert_post($my_post);

	yapbq_log("insert post result: ".print_r($postid, true));

	/* COPIADO DE YapbClass::_on_edit_publish_save_post */

	$image = new YapbImage(null, $postid, $uri);

	// We persist the image to the database

	$image->persist();

	// Hook requested by Joost
	// Since: 1.9.18

	do_action('yapb_image_upload', $image);

	/* COPIADO DE YapbClass::_on_edit_publish_save_post */

	$image = YapbImage::getInstanceFromDb($postid);
	//get iptc info
	if(!empty($image) && true) {
		yapbq_log("image path: ".$image->systemFilePath());

		getimagesize($image->systemFilePath(), $data);
		if(isset($data['APP13'])){
			$iptc = iptcparse($data['APP13']);
			if($iptc){
				$updates = array('ID' => $image->post_id);

				//get keywords
				if($iptc['2#025'])
					//$updates['tags_input'] = implode(", ",$iptc['2#025']);
					$updates['tags_input'] = yapb_convert_charset(implode(", ",$iptc['2#025']));

				//get title
				if($iptc["2#005"][0]) //title
				{
					$updates['post_title'] = $iptc["2#005"][0]; //set post title
					$updates['post_name'] = $iptc["2#005"][0]; //set post slug
				}
				else if($iptc["2#105"][0]) //headline
				{
					$updates['post_title'] = $iptc["2#105"][0]; //post title
					$updates['post_name'] = $iptc["2#105"][0]; //post slug
				}

				//get description
				if($iptc["2#120"][0])
					$updates['post_content'] = yapb_convert_charset($iptc["2#120"][0]);

				$updates['post_name']=stripslashes($updates['post_title'] );

				yapbq_log("iptc update: ".print_r($updates, true));

				wp_update_post(add_magic_quotes($updates));
				yapbq_log("ok.fin");
			}
		}

		// set the featured image

		// set featured image

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// use wp sideload

		$filename = $image->systemFilePath();

		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
		    'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
		    'post_mime_type' => $filetype['type'],
		    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
		);

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename, $postid );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		// require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $postid, $attach_id );

		// end of set featured image

	}

	if(empty($postid) || $postid==0 || is_wp_error($postid))
		die("Could not insert post");
}


function yapbq_log($message) {
	$date = date("Y-m-d H:i:s");
	file_put_contents(dirname(__FILE__)."/log.txt", "$date : $message\n", FILE_APPEND);
}

function yapbq_check_extension($file){
	$allowed_ext=array('.jpg', '.jpeg', '.png');
	$matches=0;
	foreach($allowed_ext as $extension) {
		if(substr(strtolower($file),-strlen($extension)) == $extension) {
			$matches++;
		}
	}
	return ($matches>0);
}

function yapbq_install() {
	$uploads=wp_upload_dir();
	$yapbq_input_dir=$uploads['basedir'].'/yapbq_input';
	if (!is_dir($yapbq_input_dir)) {
		mkdir($yapbq_input_dir);
	}
	//yapbq_cron_activation();
}

function yapb_convert_charset($in_str) {
	return utf8_encode($in_str);
}


/* CRON functions *-/

require_once( ABSPATH . 'wp-admin/includes/file.php' );

function yapbq_cron_do_this_daily() {
	yapbq_cron_activation();
}

function yapbq_cron_do_this() {
	yapbq_proc_queue(yapbq_start_date(),24);
}

/* Configure and activate the CRON *-/

add_action('yapbq_cron_event', 'yapbq_cron_do_this');
add_action('yapbq_cron_event_daily', 'yapbq_cron_do_this_daily');

add_filter('cron_schedules', 'yapbq_cron_add_minute' );

function yapbq_cron_activation() {
	$ok=false;

	if ( !wp_next_scheduled( 'yapbq_cron_event' ) ) {
		yapbq_log("No hay evento wp_next_scheduled");
		$result=wp_schedule_event( time(), 'minute', 'yapbq_cron_event');
		if ( $result == false ) {
			yapbq_log("Ha fallado wp_schedule_event hourly");
		} else {
			yapbq_log("Ha ido bien  wp_schedule_event hourly");
			$ok=true;
		}
	}
	if ( !wp_next_scheduled( 'yapbq_cron_event_daily' ) ) {
		yapbq_log("No hay evento wp_next_scheduled");
		$result=wp_schedule_event( time(), 'daily', 'yapbq_cron_event_daily');
	}
}
//add_action('wp', 'yapbq_cron_activation');

function yapbq_cron_add_minute( $schedules ) {
 	// Adds once every minute
 	$schedules['minute'] = array(
 		'interval' => 60,
 		'display' => __( 'Once every minute' )
 	);
 	return $schedules;
 }

*/
?>

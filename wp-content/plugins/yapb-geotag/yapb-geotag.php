<?php
/*
	Plugin Name: yapb-geotag
	Plugin URI: 
	Description: Extract GPS data from <a href="http://wordpress.org/extend/plugins/yet-another-photoblog/">Yet another photoblog plugin</a> images and creates meta data for <a href="http://wordpress.org/extend/plugins/geotag/">Geotag plugin</a>
	Version: 1.0.1
	Author: Fran Sim&oacute;
	Author URI: http://justpictures.es/
*/

add_action('save_post', 'yapb_geotag_save'); 

function yapb_geotag_save($post_id) {
	if (wp_is_post_revision($post_id)) $post_id=wp_is_post_revision($post_id);
	
	if (class_exists('YapbImage')) {
		if (!is_null($image = YapbImage::getInstanceFromDb($post_id))) {
			$exifData = ExifUtils::getExifData($image, true);
	   		if (array_key_exists('GPSLatitude', $exifData)) {
				if ($exifData['GPSLatitude']["Minutes"]<60 && $exifData['GPSLongitude']["Minutes"]<60) {
					$gpsLat=$exifData['GPSLatitude']["Degrees"]  + $exifData['GPSLatitude']["Minutes"]/60  + $exifData['GPSLatitude']["Seconds"]/3600;
					if (substr($exifData['GPSLatitudeRef'],0,1)=='S') $gpsLat=$gpsLat*-1;

					$gpsLon=$exifData['GPSLongitude']["Degrees"] + $exifData['GPSLongitude']["Minutes"]/60 + $exifData['GPSLongitude']["Seconds"]/3600;
					if (substr($exifData['GPSLongitudeRef'],0,1)=='W') $gpsLon=$gpsLon*-1;
					
					add_post_meta($post_id, "_geotag_lat", $gpsLat, true) or update_post_meta($post_id, "_geotag_lat", $gpsLat);
					add_post_meta($post_id, "_geotag_lon", $gpsLon, true) or update_post_meta($post_id, "_geotag_lon", $gpsLon);					
				}
			}
		}
	}
}

?>

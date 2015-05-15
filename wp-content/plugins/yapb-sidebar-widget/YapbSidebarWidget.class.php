<?php

	/*	Copyright 2008 J.P.Jarolim (email : yapb@johannes.jarolim.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	*/

	/**
	 * Class YapbSidebarWidget
	 *
	 **/

	class YapbSidebarWidget {

		/**
		 * Constructor
		 **/
		function YapbSidebarWidget() {

			add_filter('yapb_options', array(&$this, 'filterYapbOptions'));
			add_action('plugins_loaded', array(&$this, 'onPluginsLoaded'));

		}

		/**
		 * This method registers the widget right after 
		 * all plugins have loaded
		 **/
		function onPluginsLoaded() {

			// Register the widget-method that displays images from
			// the local YAPB installation

			register_sidebar_widget('YAPB Sidebar Widget', array(&$this, 'onDrawLocal'));
			register_widget_control('YAPB Sidebar Widget', array(&$this, 'onControlLocal'));

		}

		/**
		 * This method draws the controls for the widget.
		 * In this case, we only have a small informational text.
		 **/
		function onControlLocal() {
			echo '<p>Please have a look onto the YAPB Options Page to configure this widget.</p>';
		}

		function onDrawLocal($args) {

			$order = '';
			switch (get_option('yapb_sidebarwidget_order')) {
				case 'random':
					$order = ' ORDER BY RAND()';
					break;
				case 'latest':
				default:
					$order = ' ORDER BY p.post_date DESC';
					break;
			}

			global $wpdb;

			$sql = 'SELECT p.ID, p.post_title FROM ' . $wpdb->posts . ' p LEFT JOIN ' . YAPB_TABLE_NAME . ' yi ON p.ID = yi.post_id WHERE p.post_type = \'post\' AND yi.URI IS NOT NULL AND p.post_status = \'publish\'' . $order . ' LIMIT 0,' . get_option('yapb_sidebarwidget_imagecount');
			$posts = $wpdb->get_results($sql);

			// Now we cylce through all posts, instance the according
			// YapbImage Instance and return all the needed data

			$data = array();

			// Let's define the thumbnail configuration parameters
			// needed for the YapbImage getThumbnailXXX methods

			$thumb = array();
			$thumb[] = 'q=100';
			$restrict = get_option('yapb_sidebarwidget_restrict');
			switch ($restrict) {
				case 'h':
					$thumb[] = 'w=' . get_option('yapb_sidebarwidget_maxsize'); 
					break;
				case 'v': 
					$thumb[] = 'h=' . get_option('yapb_sidebarwidget_maxsize'); 
					break;
				case 'b':
					$thumb[] = 'w=' . get_option('yapb_sidebarwidget_maxsize');
					$thumb[] = 'h=' . get_option('yapb_sidebarwidget_maxsize');
					$thumb[] = 'zc=1';
					break;
			}

			if (!empty($posts)) {
				foreach ($posts as $post) {
					
					$item = array();
					$item['post.id'] = $post->ID;
					$item['post.title'] = $post->post_title;
					$item['post.url'] = get_permalink($post->ID);

					$yapbImage = YapbImage::getInstanceFromDb($post->ID);
					$item['img.url'] = $yapbImage->getThumbnailHref($thumb);
					$item['img.width'] = $yapbImage->getThumbnailWidth($thumb);
					$item['img.height'] = $yapbImage->getThumbnailHeight($thumb);

					$data[] = $item;

				}
			}

			$this->draw($args, $data);

		}


		/**
		 * This method finally gets things done:
		 * It draws the sidebar widget when called by WP
		 **/
		function draw($args, $data) {

			extract($args);

			$imagecount = get_option('yapb_sidebarwidget_imagecount');
			$maxsize = get_option('yapb_sidebarwidget_maxsize');
			$restrict = get_option('yapb_sidebarwidget_restrict');
			$title = get_option('yapb_sidebarwidget_title');
			
			switch(get_option('yapb_sidebarwidget_displayas')) {
				
				case 'ul' :
					
					$beforeBlock = '<ul class="yapb-latest-images">';
					$beforeItem = '<li>';
					$afterItem = '</li>';
					$afterBlock = '</ul>';
					break;
				
				case 'div' :
				default:

					$beforeBlock = '<div class="yapb-latest-images">';
					$beforeItem = '';
					$afterItem = '';
					$afterBlock = '</div>';
					break;
					
			}
			
			echo $before_widget;

			// Output the title

			if (trim($title) != '') {
				echo $before_title . $title . $after_title;
			}

			// Let's define the thumbnail configuration parameters
			// needed for the request

			$thumb = array();
			$thumb[] = 'q=100';
			$restrict = get_option('yapb_sidebarwidget_restrict');
			switch ($restrict) {
				case 'h':
					$thumb[] = 'w=' . get_option('yapb_sidebarwidget_maxsize'); 
					break;
				case 'v': 
					$thumb[] = 'h=' . get_option('yapb_sidebarwidget_maxsize'); 
					break;
				case 'b':
					$thumb[] = 'w=' . get_option('yapb_sidebarwidget_maxsize');
					$thumb[] = 'h=' . get_option('yapb_sidebarwidget_maxsize');
					$thumb[] = 'zc=1';
					break;
			}
			
			if (!empty($data)) {

				echo $beforeBlock;

				// The default loop direction

				$loop_start = 0;
				$loop_end = count($data);
				$loop_inc = 1;

				if (get_option('yapb_sidebarwidget_reverse')) {

					// User wants the direction reversed

					$loop_start = count($data)-1;
					$loop_end = -1;
					$loop_inc = -1;

				}

				// The image loop

				for($i=$loop_start; $i!=$loop_end; $i+=$loop_inc) {
					$item = $data[$i];
					echo $beforeItem . '<a title="' . $item['post.title'] . '" style="border:0;padding:0;margin:0;" href="' . $item['post.url'] . '"><img border="0" style="padding-right:2px;padding-bottom:2px;" src="' . $item['img.url'] . '" width="' . $item['img.width'] . '" height="' . $item['img.height'] . '" alt="' . $item['post.title'] . '" /></a>' . $afterItem;
				}

				echo $afterBlock;

				if (get_option('yapb_sidebarwidget_link_activate')) {
					echo '<a class="yapb-latest-images-link" href="' . get_option('yapb_sidebarwidget_link_mosaic') . '">' . get_option('yapb_sidebarwidget_link_mosaic_title') . '</a>';
				}

			} else {

				// Sorry, no images
				echo '<p class="yapb-no-latest-images">Sorry nothing yet</p>';

			}

			echo $after_widget;

		}


		function filterYapbOptions($options) {

			// Build the options array

			$additionalOptions = new YapbOptionGroup(
				'YAPB Sidebar Widget',
				'',
				array(
					
					new YapbOptionGroup(
						__('Widget Configuration', 'yapb'), 
						'',
						array(
							new YapbInputOption('yapb_sidebarwidget_title', __('Widget Title: #20 Leave empty if you don\'t want to display a title.', 'yapb'), 'Latest photography'),
							new YapbSelectOption('yapb_sidebarwidget_displayas', __('#', 'yapb'), array('Display thumbnails as bunch of linked images in a div container' => 'div','Display thumbnails as bunch of list items in an unordered list' => 'ul'), 'div'),
							new YapbSelectOption('yapb_sidebarwidget_order', __('#'), array('The widget displays a list of your latest images'=>'latest', 'The widget displays some random images'=>'random'), 'latest'),
							new YapbCheckboxOption('yapb_sidebarwidget_reverse', __('Display images in reverse order', 'yapb'), false),
							new YapbInputOption('yapb_sidebarwidget_imagecount', __('Show #10 images.', 'yapb'), '5'),

							new YapbInputOption('yapb_sidebarwidget_maxsize', __('Maximal thumbnail size: #10 px.', 'yapb'), ''),
							new YapbSelectOption('yapb_sidebarwidget_restrict', __('Restrict thumbnail size #.', 'yapb'), array('horizontally'=>'h','vertically'=>'v', 'both'=>'b'), 'vertically'),

						)
					),

					new YapbOptionGroup(
						__('Mosaic link', 'yapb'), 
						'',
						array(
							new YapbCheckboxOption('yapb_sidebarwidget_link_activate', __('Show Link to mosaic page', 'yapb'), false),
							new YapbInputOption('yapb_sidebarwidget_link_mosaic', __('Link to mosaic page: #40 (optional)', 'yapb'), ''),
							new YapbInputOption('yapb_sidebarwidget_link_mosaic_title', __('Title of mosaic page link: #20', 'yapb'), 'All images')
						)
					)

				)
			);

			$additionalOptions->isPlugin = true;	// These are Plugin Options
			$additionalOptions->setLevel(1);		// These are suboptions that we attach to the YAPB Main Options
			$additionalOptions->initialize();		// Initialize the options
			$options->add($additionalOptions);

			return $options;

		}


	}

?>

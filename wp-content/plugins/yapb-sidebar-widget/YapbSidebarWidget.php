<?php
	
	/*
	Plugin Name: YAPB Sidebar Widget
	Plugin URI: http://johannes.jarolim.com/yapb/sidebar-widget
	Description: The YAPB Sidebar Widget displays some of your latest images or some random images posted with <a href="http://johannes.jarolim.com/yapb">YAPB</a>.
	Author: J.P.Jarolim
	License: GPL
	Version: 2.2
	Author URI: http://johannes.jarolim.com
	*/

	/**
	 *
	 * The YAPB Sidebar Widget displays either some of your latest or some random images from
	 * your PhotoBlog. To display your images, you either have to have an active YAPB Installation
	 * on this blog, OR an active YAPB + YAPB XMLRPC Server installation on another blog.
	 *
	 * (1) Just activate the plugin 
	 * (2) go the YAPB options page and configure the widget
	 * (3) go to the Design/Widgets Page and add the widget to your Sidebar.
	 *
	 **/

	/* Short and sweet */

	require_once 'YapbSidebarWidget.class.php';
	$yapbSidebarWidget = new YapbSidebarWidget();

?>
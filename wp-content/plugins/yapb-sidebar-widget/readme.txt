=== YAPB Sidebar Widget ===

Contributors: jaroat
Donate link: http://johannes.jarolim.com/yapb/donate
Tags: yapb, yet another photoblog, sidebar, widget, sidebar widget
Requires at least: 2.5
Tested up to: 3.2.1
Stable tag: 2.3

The YAPB Sidebar Widget displays some of your latest images or some random images posted with <a href="http://johannes.jarolim.com/yapb">YAPB</a>.





== Description ==

The YAPB Sidebar Widget displays some of your latest images or some random images posted with <a href="http://johannes.jarolim.com/yapb">YAPB</a>.

If you need to display images from another blog with installed <a href="http://johannes.jarolim.com/yapb">YAPB</a>, please refer to the <a href="http://wordpress.org/extend/plugins/yapb-xmlrpc-sidebar-widget">YAPB XMLRPC Sidebar Widget</a> Plugin.




== Installation ==

= Upload the files =

1. Unzip the content of the zip-file into an empty directory 
2. On your server, create a directory "yapb-sidebar-widget" below your "wp-content/plugins" folder
2. Upload the unzipped files directly into the new folder

= Activate and Configure =

1. Go to your Admin Panel / Plugins
2. Activate the "YAPB Sidebar Widget"
3. Go to your YAPB Options Page and configure the widget
4. Finally go to Design / Widgets Page and add the widget to your Sidebar.
5. Done!

= Enjoy and share your photography = 

Really: do and share some serious photography so everybody may discover your view and interpretation of the world.




== Changelog ==

= 2011-11-19, Release 2.3 =

* PHP 5.3 compatibility patch 

= 2008-09-22, Release 2.2 =

* Adapted to new YAPB Plugin Infrastructure: Plugins may add their options via the yapb_options filter now

= 2008-09-03, Release 2.1 =

* XMLRPC functionality outsourced to <a href="http://wordpress.org/extend/plugins/yapb-xmlrpc-sidebar-widget">its own plugin</a>
* Plugin workflow rethought and reworked: Options should now show up on YAPB Options Page even if YAPB gets loaded after the widget
* Code simplified

= 2008-07-24, Release 2.0.1 =

Bugfix: 

* Little typo preventing rendering img height of thumbnails thus breaking ie7 rendering (Thanks to John775 for the recherche and reporting)

= 2008-06-23, Release 2.0 =

Much more features:

* Additional XMLRPC functionality: Access a remote blog with YAPB + YAPB XMLRPC Server
* Additional Output Options

= 2008-06-12, Release 1.0 =

Initial release of the YAPB Sidebar Widget for YAPB 1.9+
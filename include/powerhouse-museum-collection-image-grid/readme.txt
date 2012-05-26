=== Powerhouse Museum Collection Image Grid ===
Contributors: powerhousemuseum
Tags: powerhouse museum, Australia, science, technology, objects, history
Plugin URI: http://www.powerhousemuseum.com
Requires at least: 3.0.0
Tested up to: 3.0.0
Stable tag: trunk

Plugin to allow embedding of object thumbnails and descriptions from the Powerhouse Museum collection of technology, design and social history.
 
== Description ==

Plugin to make it easy to embed thumbnails and popup descriptions of thousands of objects from the Powerhouse Museum collection of technology, design and social history in your Wordpress blog. The plugin supports both in-post and sidebar widget implementations and a variety of filtering and display methods.

The Powerhouse Museum's online collection is full of objects that you may want to incorporate into your blog. This plugin uses the [Powerhouse API](http://api.powerhousemuseum.com/ "http://api.powerhousemuseum.com/") and a jQuery popup for displaying the objects in summary view.

Using the plugin you can display a grid of objects in a post or as a sidebar widget. The size and number of objects as well as the grid layout can be easily customised as well as the search variables.

== Installation ==

= GENERAL STEPS =
1. Download and extract the zip file, upload the phm-image-grid.X.X.zip to your wp-content/mu-plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Sign up for an account [here.](http://api.powerhousemuseum.com/register/ "Powerhouse Museum API") Next create an [API Key](http://api.powerhousemuseum.com/register/ ""). 
4. Enter this key value in the 'API key' field of the Powerhouse Museum Image Grid admin page accessible from the left menu. 

= Widget =
1. In Appearance/Widgets, drag the widget to a sidebar. 
2. Change configuration and add filters. 
3. Save

= Shortcode =
Place the shortcode [phm-grid ] anywhere in any post or page. The basic parameters are 'cols', 'rows', 'thumb_width', 'thumb_height', and 'parameters'.
You can use as many parameters you need. For further information please go to the [Powerhouse Museum API documentation](http://api.powerhousemuseum.com/api/v1/documentation/ "")
eg. [phm-grid cols=4 rows=4 v_space=1 h_space=1 thumb_width=120 thumb_height=120 random=true parameters="title:chair|description:New South Wales"]


== Frequently Asked Questions ==

= How can I get an API Key? =

Please sign up for an account [here](http://api.powerhousemuseum.com/register/ "Powerhouse Museum API") and then create an API Key for you application.


== Screenshots ==

1. Widget sidebar configuration UI: the options in this menu are the same as the shortcode.  
2. Use of the widget in the sidebar, pulling in four objects from the Powerhouse Museum collection.
3. You need to get a free API key to use this plugin. 
4. You can use the shortcode editor to insert a grid of Powerhouse Museum objects within a post.
5. Example of shortcode use.
6. Grid of images pulled from the Powerhouse Museum Collection.
7. Extra information when you rollover each object.

== Changelog ==

= 0.9 =
* First implementation

= 0.9.1 =
* Fix wrong CSS and JS paths

= 0.9.1.1 =
* Fix missing objects when title was null or empty

== Upgrade Notice ==


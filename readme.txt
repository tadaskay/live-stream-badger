=== Live Stream Badger ===
Contributors: tkrivickas
Tags: live stream, twitch, widget, menu
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display status of Twitch.tv live streams

== Description ==

Live Stream Badger comes with a simple Widget to display status of a Live Stream (Twitch.tv) on your website.

If you have a good idea or there is some issue, feel free to open a discussion in 'Support'.

== Installation ==

1. Download and unzip the plugin into your WordPress plugins directory (usually `/wp-content/plugins/`).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Using WordPress Appearance > Menus, create a menu with Custom Links to Live Stream channels (e.g. `http://www.twitch.tv/tobiwandota` as URL and `My favourite stream!` as a label).
4. Place the widget on your sidebar through the 'Widgets' menu in your WordPress Admin.
5. When configuring the widget, select a menu that contains your precious Live Stream links.

== Frequently Asked Questions ==

= Which live stream websites are supported? =

Currently, only [Twitch.tv](http://www.twitch.tv) is supported. 
Other platforms will be supported in the future versions.

= How do I add Live Stream links? =

Using 'Appearance' > 'Menus' in WordPress Admin:
 
1. Create a menu (you can name it 'Streams'). 
2. Add Custom Links to this menu

= It doesn't work! =

1. Double check if your widget points to the correct menu.
2. Check if menu items have valid Live Stream channel URLs (e.g. `http://www.twitch.tv/tobiwandota`) and names (e.g. `My live stream!`)

= Status is not displayed next to each Live Stream! =

Hold on a second. Live stream status is scheduled to be updated every 5 minutes to save your web server resources. If this is your first run, give it a few minutes after activation.

== Screenshots ==

1. Live Stream Badger widget is Live! *Note: Flags are provided by another plugin, by using a shortcode in menu item's label*

== Changelog ==

= 1.0 =
* Initial version
=== Live Stream Badger ===
Contributors: tkrivickas
Tags: live stream, twitch, widget, menu
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display status of Twitch.tv livestreams

== Description ==

All-in-one livestream (Twitch.TV) integration for your WordPress website.

= Features =
* Widget to display livestream status (Online/Offline)
* Shortcode to embed a livestream: `[livestream url="http://www.twitch.tv/mychannel"]`

= Planned features (to do) =
* TinyMCE extension for user-friendly embedding of a livestream via shortcode
* Thumbnails instead of simple links in the Widget
* Top livestreams listed by category as a Widget
* Support multiple livestream providers (e.g. Hashd.TV, Justin.TV, UStream.TV)

= Have something to say? =
[Suggest ideas, report issues or join development](http://wordpress.org/support/plugin/live-stream-badger)!

== Installation ==

1. Upload the `live-stream-badger` folder to the `/wp-content/plugins/` directory
2. Activate the Live Stream Badger through the 'Plugins' menu in WordPress
3. Done! Follow [Quickstart](http://wordpress.org/extend/plugins/live-stream-badger/quickstart/)

== Quickstart ==

= Configure 'Stream status' widget =

1. Go to WordPress Appearance > Menus
2. Create a new menu
3. Create a custom link, add it to the menu and save. Link should point to the channel, e.g. `http://www.twitch.tv/tobiwandota` as URL and `My favourite stream!` as a label
4. Go to Wordpress Appearance > Widgets
5. Place 'LSB Stream Status' widget on the sidebar
6. In widget configuration, select the menu you created in Step (3) and save
7. Go to your website and you should see the livestream link in a widget
8. Wait for about 5 minutes (stream status is updating) and refresh the page
9. You should see a livestream link and its status (how many viewers are watching or 'Offline')

= Embed a stream using the shortcode =

1. Create new or edit a post
2. Type in the following: `[livestream url="http://www.twitch.tv/tobiwandota"]`
3. Save and view the post
4. You should see an embedded livestream in the post

= [livestream] shortcode reference =

Sample usage: `[livestream url="http://www.twitch.tv/tobiwandota" chat="true"]`

Parameters:

1. url - URL of the livestream channel (string, default: '')
2. width - width of livestream embed (int, default: 620)
3. height - height of livestream embed (int, default: 378)
4. stream - show stream? (boolean, default: true)
5. chat_width - width of livestream chat (int, default: 620)
6. chat_height - height of livestream chat (int, default: 400)
7. chat - show chat? (boolean, default: false)

== Frequently Asked Questions ==

No questions yet. [Ask one!](http://wordpress.org/support/plugin/live-stream-badger)

== Screenshots ==

1. Live Stream Badger widget is Live! *Note: Flags are provided by another plugin, by using a shortcode in menu item's label*

== Changelog ==

= 1.1.1 =
* Update of readme
= 1.1 =
* Added livestream shortcode!
* Changed display from a table to a list
* Several bugfixes reported in forum
* Implemented pluggable API to support other providers than Twitch in the future
* Major refactoring
= 1.0.1 =
* Fixed shortcode support in Widget (link names)
* Fixed channel status sometimes not updating because of non-standard URL
* Switched to WP HTTP API
= 1.0 =
* Initial version

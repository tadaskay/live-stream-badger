# Live Stream Badger #
**Contributors:** tkrivickas  
**Tags:** twitch, live stream, stream status, widget, menu  
**Requires at least:** 3.7  
**Tested up to:** 3.8  
**Stable tag:** 1.4.3  
**License:** GPLv3  
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html  

Display status of Twitch.tv livestreams

## Description ##

All-in-one livestream (Twitch.TV) integration for your WordPress website.

### Features ###
* Widget to display livestream status.
* Streams can be displayed as Images, Screen captures or as a text.
* Shortcode to embed a livestream: `[livestream url="http://www.twitch.tv/mychannel"]`
* Easy customization using CSS, WordPress hooks and filters

Works out-of-the-box, but you can customize it.

### Planned features (to do) ###
* TinyMCE extension for user-friendly embedding of a livestream via shortcode
* Top livestreams listed by category as a Widget
* Support multiple livestream providers (e.g. Hashd.TV, Justin.TV, UStream.TV)

### Requirements ###
* PHP 5.3+
* WordPress 3.7+

### Have something to say? ###
[Suggest ideas, report issues or join development](http://wordpress.org/support/plugin/live-stream-badger)!

## Installation ##

1. Upload the `live-stream-badger` folder to the `/wp-content/plugins/` directory
2. Activate the Live Stream Badger through the 'Plugins' menu in WordPress
3. Done! Follow steps below to get started.

### Configure 'Stream status' widget ###

1. Go to WordPress Appearance > Menus
2. Create a new menu
3. Create a custom link, add it to the menu and save. Link should point to the channel, e.g. `http://www.twitch.tv/tobiwandota` as URL and `My favourite stream!` as a label
4. Go to WordPress Appearance > Widgets
5. Place 'LSB Stream Status' widget on the sidebar
6. In widget configuration, select the menu you created in Step (3) and save
7. Go to your website and you should see the livestream link in a widget

### 'Stream status' widget CSS classes ###
Use these in your own stylesheet to customize display of the widget.

* `lsb-status-widget-holder` main container
* `lsb-status-widget-list-item` list item for the stream list
* `lsb-status-widget-indicator` stream status indicator
* `lsb-on` online status
* `lsb-off` offline status

Classes `lsb-on` and `lsb-off` are added to both `lsb-status-widget-list-item` and `lsb-status-widget-indicator`. Selector example: `.lsb-status-widget-indicator.lsb-on`.

### Embed a stream using the shortcode ###

1. Create new or edit a post
2. Type in the following: `[livestream url="http://www.twitch.tv/tobiwandota"]`
3. Save and view the post
4. You should see an embedded livestream in the post

### [livestream] shortcode reference ###

Sample usage: `[livestream url="http://www.twitch.tv/tobiwandota" chat="true"]`

Parameters:

1. url - URL of the livestream channel (string, default: '')
2. width - width of livestream embed (int, default: 620)
3. height - height of livestream embed (int, default: 378)
4. stream - show stream? (boolean, default: true)
5. chat_width - width of livestream chat (int, default: 620)
6. chat_height - height of livestream chat (int, default: 400)
7. chat - show chat? (boolean, default: false)
8. autoplay - automatically play embedded content (boolean, default: true)

## Frequently Asked Questions ##

No questions yet. [Ask one!](http://wordpress.org/support/plugin/live-stream-badger)

## Screenshots ##

![1. Live Stream Badger widget is Live!](http://s-plugins.wordpress.org/live-stream-badger/assets/screenshot-1.png)

## Changelog ##

### 1.4.2-1.4.3 ###

* Minor bugfixes  

### 1.4.1 ###

* Bugfix: Embedded stream throwing fatal error  
* Improvement: Tweaked health check upon activation (should show compatibility issues if any)  

### 1.4 ###

* New Feature: Added configurable plugin's settings  
* Bugfix: Fixed stream list not updating in certain configurations (WP Cron has been replaced by Transients API)  
* Improvement: Moved development to [GitHub](https://github.com/tkrivickas/live-stream-badger), everyone is welcome to contribute  
* Improvement: Switched from Justin.tv to Twitch API (version 3, bleeding edge)  
* Improvement: Upgraded to PHP version 5.3+ (cleaner code thanks to namespaces, autoloader)  
* Improvement: Updated minimum requirements for WordPress (3.7+, though 3.8 is highly recommended) as well as for PHP (5.3+)  
* Improvement: Major code refactoring (hopefully, for the good)
* Improvement: Moved templates and filters for extensions to a separate folder `extend`  

### 1.3 ###

* Improvement: Added shortcode parameter to disable autoplay  
* New feature: Added templates for customizing widget output. Filters added: `lsb_status_widget_format`, `lsb_status_widget_item_format`, `lsb_status_widget_item_with_image_format`, `lsb_status_widget_no_content_format`. See `extend\class-templates.php` for more details.  

### 1.2.2 ###

* Bugfix: shortcode embed not working  

### 1.2 ###

* New Feature: Sort streams  
* New Feature: Display screen capture or channel image  
* Improvement: Plugin now uses transient storage (should work fast with caching plugins)  
* Improvement: Added CSS classes for online/offline indicators in the widget  
* Improvement: Added health check for HTTP transport (plugin will not activate if technical requirements are not met)  
* Improvement: Changed required minimum WP version to 3.5  
* Improvement: Major refactoring  

### 1.1.1-1.1.2 ###

* Update of readme and usage guide under 'Installation'  

### 1.1 ###

* Added livestream shortcode!  
* Changed display from a table to a list  
* Several bugfixes reported in forum  
* Implemented pluggable API to support other providers than Twitch in the future  
* Major refactoring  

### 1.0.1 ###

* Fixed shortcode support in Widget (link names)  
* Fixed channel status sometimes not updating because of non-standard URL  
* Switched to WP HTTP API  

### 1.0 ###

* Initial version  

## Upgrade Notice ##

Please read the changelog before upgrading.

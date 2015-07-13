=== SuperSlider-Login ===
Contributors: Daiv Mowbray
Donate link: http://wp-superslider.com/support-me/donate/
Plugin URI: http://wp-superslider.com/superslider/superslider-login
Tags: animated, login, mootools, slidein, panel
Requires at least: 2.5
Tested up to: 3
Stable tag: 1.0

Slidein login panel, uses mootools 1.2

== Description ==

 A slide in login panel. Theme based, animated, automatic user detection, uses mootools 1.2 java script. This plugin is part of the SuperSlider series. Get more supersliders at [supersliders](http://wordpress.org/extend/plugins/superslider/ "SuperSliders")


**Features**

* complete global control from options page
* Control transition time, type.

**Demos**

This plugin can be seen in use here:

* [Demo 1](http://wp-superslider.com/2009/superslider-login-demo "Demo")


== Screenshots ==

1. ![login sample](screenshot-1.png "login sample horizontal")
2. ![login sample](screenshot-2.png "login sample horizontal")
3. ![login sample](screenshot-1.png "login sample vertical")
4. ![SuperSlider-login options screen](screenshot-1.png "SuperSlider-login options screen")

**Support**

If you have any problems or suggestions regarding this plugin [please speak up](http://support.wp-superslider.com/forum/superslider-show "support forum")

**Plugins**
Download These Plugins here:

* [SuperSlider-Show](http://wordpress.org/extend/plugins/superslider-show/ "SuperSlider-Show")
* [SuperSlider-Menu](http://wordpress.org/extend/plugins/superslider-menu/ "SuperSlider-Menu")
* [Superslider-PostsinCat](http://wordpress.org/extend/plugins/superslider-postsincat/ "Superslider-PostsinCat")
* [SuperSlider-MooFlow](http://wordpress.org/extend/plugins/superslider-mooflow/ "SuperSlider-MooFlow")

**NOTICE**

* The downloaded folder's name should be superslider-login
* Also available for [download from here](http://wp-superslider.com/downloadsuperslider/superslider-login-download "superslider-login plugin home page").
* Probably not compatible with plugins which use jquery. (not tested)

== Installation ==

* Unpack contents to wp-content/plugins/ into a **superslider-login** directory
* Activate the plugin,
* Configure global settings for plugin under > settings > SuperSlider-login
* (optional) move SuperSlider-login plugin sub folder plugin-data to your wp-content folder,
	under  > settings > SuperSlider-login > option group, File Storage - Loading Options
	select "Load css from plugin-data folder, see side note. (Recommended)". This will
	prevent plugin uploads from over writing any css changes you may have made.

== Upgrade Notice ==

You may need to re-save your settings/ options when upgrading

== USAGE ==

If you are not sure how this plugin works you may want to read the following.

* First ensure that you have uploaded all of the plugin files into wp-content/plugins/superslider-login folder.
* Go to your WordPress admin panel and stop in to the plugins control page. Activate the SuperSlider-login plugin.
* Go to the SuperSlider-login settings page and set your preferred options.

You should be able to view your new login slidein panel in your site.
You can adjust how the slidein login panel looks and works by making adjustments in the plugin settings page.

== OPTIONS AND CONFIGURATIONS ==

Available under > settings > SuperSlider-login

* theme css files to use
* transition type
* transition speed
* Overlay opacity
* transition time
* to load or not Mootools.js
* css files storage loaction

----------


== Themes ==

Create your own graphic and animation theme based on one of these provided.

**Available themes**

* default
* blue
* black
* custom

== To Do ==



== Frequently Asked Questions ==	

=  Why isn't my login working? =

>*You first need to check that your web site page isn't loading more than 1 copy of mootools javascript into the head of your file.
>*While reading the source code of your website files header look to see if another plugin is using jquery. This may cause a javascript conflict. Jquery and mootools are not always compatible.

=  How do I change the style of the login? =
  
>I recommend that you move the folder plugin-data to your wp-content folder if you already have a plugin-data folder there, just move the superslider folder. Remember to change the css location option in the settings page for this plugin. Or edit directly: **wp-content/plugins/superslider-show/plugin-data/superslider/ssLogin/custom/custom.css** Alternatively, you can copy those rules into your WordPress themes, style file. Then remember to change the css location option in the settings page for this plugin.
  

= The stylesheet doesn't seem to be having any effect? =
 
>Check this url in your browser:
>http://yourblogaddress/wp-content/plugins/superslider-show/plugin-data/superslider/ssLogin/default/default.css
>If you don't see a plaintext file with css style rules, there may be something wrong with your .htaccess file (mod_rewrite). If you don't know how to fix this, you can copy the style rules there into your themes style file.

= How do I use different graphics and symbols for the tab? =

>You can upload your own images to
>http://yourblogaddress/wp-content/plugins/superslider-login/plugin-data/superslider/ssLogin/custom/images


== CAVEAT ==

Currently this plugin relies on Javascript to create the popover.
If a user's browser doesn't support javascript the image will display normally.

== Changelog ==

* 1.0 (2010/06/02)

  * fixed link to settings page
  * added save options upon deactivation option

* 0.9 (2010/04/20)

    * fixed salutation at tab top when not logged in.
    * fixed css issue with IE.

* 0.8 (2010/02/14)

  * logged in user name link repaired, now works.
  * updated for WP 2.9
  * added as submenu for superslider
  
* 0.7 (2009/11/20)

  * fixed IE css png with gifs

* 0.6 (2009/10/24)

    * changed js var names to avoid conflicts

* 0.6 (2009/10/24)

    * fixed theme independence from ss-base.
    * adjustments to the css and tab graphics.

* 0.5.5 (2009/9/27)

    * made xhtml compliant.

* 0.5.4 (2009/9/27)

    * fixed css bugs, tab overflow, display block.


* 0.5.3 (2009/9/25)

    * fixed graphics for the panel bottom on vertical slider.

* 0.5.2 (2009/9/21)

    * changed theme graphics to css sprites.
    * changed css loader to use wp_enqueue_style

* 0.5 (2009/7/27)

    * changed the logout redirect to work properly on home page
    * added option to define any custom link to open the slidein panel
    * updated java script to be compatable with jquery
    * updated mootools to 1.2.3
    * added slide to page top when using custom link

* 0.4 (2009/6/30)

    * changed the hidden tab text to css controlled.
    * changed tab class to logintab

* 0.3 (2009/6/26)

    * issue with the css failed on safari

* 0.2 (2009/6/19)

    * admin tabs fixed

* 0.1 (2009/6/15)

    * first public launch

---------------------------------------------------------------------------
=== Anti-AdBlock ===
Tags: adblock, advertise, monetize, donate, seo, backlink, link juice
Contributors: madeinthayaland
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=thaya.kareeson@gmail.com&currency_code=USD&amount=&return=&item_name=Donate+a+cup+of+coffee+or+two+to+support+Anti-AdBlock+plugin.
Requires at least: 2.7
Tested up to: 2.9.2
Stable Tag: 0.2.1

This plugin displays a notification message to visitors with AdBlock on, humbly
asking them to turn it off.

== Description ==
This plugin detects if a regular visitor to your blog (10 visits by default)
has AdBlock software enabled. If so, it will display a floating notification
message on the 11th visit to your visitor. You can set this message to humbly
ask your loyal visitor to support your blog by turning off their AdBlock
software, by making a small donation, or by writing about your blog (be
creative!). By default, this message box will never show up again for that
visitor.

== Features ==

* Ability to make the notification message closeable or not.
* Ability to NOT display message to visitor until X visits (default 10).
* Ability to set the number of times your visitor gets nagged by the
  notification. (default 1)
* Customizable notification message. You can even Rick Roll your visitos who
  have AdBlock enabled!
* Optional "message bar" mode to be less visitor intrusive.

== Installation ==

1. Upload the plugin to your plugins folder: 'wp-content/plugins/'
2. Activate the 'Anti-AdBlock' plugin from the Plugins admin panel.
3. (optional) Go to the Options -> Anti-AdBlock admin panel to make
   any customizations.
4. Test this out by turning AdBlock on and visiting your blog.

== Frequently Asked Questions ==

= The default message displayed is ugly!  Can I change the way it looks? =
Yes!  You can modify the notification CSS in the plugin settings page.

= I can't get it to work! I'm frustrated! =
Please take a look at some documentation on the 
[plugin page](http://omninoggin.com/projects/wordpress-plugins/anti-adblock-wordpress-plugin/)
to see if any of them can help you.  If not, feel free to post your issues
on the appropriate [plugin support forum](http://omninoggin.com/forum).
I will try my best to help you resolve any issues that you are having.

== Changelog ==

* 0.2.1
  1. Accounted for new AdBlock Plus's feature that will not trigger the message.
* 0.2
  1. Lets you specify ###visit_count### in message to display the number of
     times the user has visited your site.
  1. Accounted for AdBlock Plus's feature that will not trigger the message.
* 0.1.9
  1. Perfomance improvement by moving bait banner to the footer.
* 0.1.8
  1. Fixed 'wpgb' not found bug.
* 0.1.7
  1. Added "message bar" mode.
  1. Valid XHTML fix.
  1. Modified preset text to have more support options.
* 0.1.6
  1. Fixed critical bug that prints JS into rss feeds.
* 0.1.5
  1. Added the ability to NOT display message to visitor until X visits
     (default 10).
* 0.1.4
  1. Fixed default message spelling typo.
* 0.1.3
  1. Forgot a single quote.
* 0.1.2
  1. Load the bait banner from /images/ads/<random>.gif so AdBlock users
     cannot add it to the whitelist without whitelisting other ads.
* 0.1.1
  1. Made the notification message id randomly generated to prevent AdBlock
     from globally blocking the plugin on all sites.
* 0.1
  1. Initial release

== Screenshots ==

1. AdBlock notification message
2. Message bar mode

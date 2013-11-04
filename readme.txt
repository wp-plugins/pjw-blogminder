=== PJW Blogminder  ===
Tags: reminder
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=paypal%40ftwr%2eco%2euk&item_name=Peter%20Westwood%20WordPress%20Plugins&no_shipping=1&cn=Donation%20Notes&tax=0&currency_code=GBP&bn=PP%2dDonationsBF&charset=UTF%2d8
Contributors: westi
Requires at least: 3.0.5
Tested up to: 3.7.1
Stable tag: 0.92

== Description ==
This plugin allows your users to configure a reminder to be sent to them if they haven't posted in the last n days.

You configure the reminder on your Profile page (or the profile of another user if you have than capability)

It is in the Personal Options section and called Blogminder Threshold 

== Changelog ==

= 0.92 =
* Fixed a glaring bug which meant it sent an email every day with WordPress 3.0 or newer.

= 0.91 = 
* Added filter 'pjw_blogminder_maximum_threshold' to allow site customisation plugin to filter maximum threshold value.

= 0.90 =
* Initial Release

== Installation ==

1. Upload to your plugins folder, usually `wp-content/plugins/`
2. Activate the plugin on the plugin screen.
3. Configure your reminder setting on your profile page.

== Frequently Asked Questions ==

= How often does the plugin check =

The plugin checks all users on a twice daily basis using the WordPress cron system.

= How often does the plugin send reminders =

At the moment the plugin will send a reminder every 12 hours once you have gone over your configured reminder level.

= How can I customise the plugin for my site = 

To customise the plugin for your site then you can filter some of the settings by creating another plugin.
If you want to change the maximum threshold value to allow shorter or longer reminder periods then use the 'pjw_blogminder_maximum_threshold' filter.

== Screenshots ==

1. This shows the user option as it appears on the profile page.

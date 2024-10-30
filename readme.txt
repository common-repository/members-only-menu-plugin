=== Members Only Menu Plugin ===
Contributors: brandon.wamboldt
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y398QNA6FM9TA
Tags: members, only, plugin, restricted, access, menus, 3.0, wp_nav_menu
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 2.0

This plugin allows you to mark a page as "Members Only" and still add it to menus (Only visible if logged in)

== Description ==

**This plugin is now deprecated, please use WordPress Access Control for access to new features, such as restrict to members or non-members, restrict to certain roles and specify a redirect URL. This name didn't suite the version 2.0.**

http://wordpress.org/extend/plugins/wordpress-access-control/

------

This plugin allows you to mark a page as "Members Only". Once this is done, the user must login to view the page. However, you can still add the page to the menu, and it will then ONLY show up if the user is authenticated. That way you can have more items for logged in members on your website.

This plugin is designed for people using WordPress as a CMS. It's extremely easy to use. You install the plugin, and activate it. Whenever you create or edit a page there is a checkbox on the sidebar that allows you to mark that page as members only.

This plugin now fully supports wp_nav_menu and wp_page_menu for older sites, so it should be compatible with older versions of WordPress, as well as wp_list_pages for certain themes.

It automatically sets the walker class to the one we custom made so theme developers don't need to do anything

== Installation ==

Installation is very simple:

1. Upload `members-only-menu-plugin/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can now go to a page and mark it as members only.

== Frequently Asked Questions ==

**I get a PHP error when I try to activate your plugin**

This was caused by PHP4, which is now supported in version 1.4

== Screenshots ==

1. The meta box added by this plugin

== Changelog ==

= 1.6.4 =
* Fixed a problem with certain themes that use wp_list_pages as my plugin didn't affect that function. It does now, as we hook into get_pages. Also updated some of the code to better reflect WordPress coding standards

= 1.6.3 =
* Fixed a problem in pre WordPress 3 instances where a PHP error is generated due to lack of the Walker_Nav_Menu class

= 1.6.2 = 
* Fixed (X)HTML validation errors caused by an empty ul which could occur if all items in a submenu were members only but the parent element was not.

= 1.6 =
* Fixed a bug where third level menu items with members only attributes would break the HTML/menu

= 1.5 =
* Fixed an error where submenus would still be generated if the parent was marked as members only. This has been fixed.

= 1.4 = 
* Added support for PHP4

= 1.3 =
* Added support for wp_page_menu

= 1.2 =
* Added a filter which catches a fallback to wp_page_menu and removes our walker class from the arguments list

= 1.1 =
* Added a filter which removed the need to change the wp_nav_menu commands

= 1.0 =
* Initial Version

== Upgrade Notice ==

= 1.6.4 = 
* Fixed several bugs including adding support for wp_list_pages

= 1.6 =
* Fixed a bug with third level menu items

= 1.5 =
* Fixed a bug with submenus and the parent being members only

= 1.4 =
Now supports PHP 4

= 1.3 =
Full support for wp_page_menu!

= 1.2 =
Fixes the code so it no longer degrades terribly when no nav menu is available

= 1.1 =
Increases compatibility and ease of use by removing the need to change theme file

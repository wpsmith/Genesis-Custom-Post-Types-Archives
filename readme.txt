=== Genesis Custom Post Types Archives ===
Contributors: wpsmith
Plugin URI: http://www.wpsmith.net/genesis-custom-post-types-archives
Donate link: http://www.wpsmith.net/donation
Tags: custom post types, cpts, archives, genesis, genesiswp
Requires at least: 3.0
Tested up to: 3.3.1

Allows you to customize Genesis Custom Post Type archive pages for solid SEO.

== Description ==

Genesis Custom Post Types Archives extends the builtin Genesis SEO functionality to Genesis Custom Post Types Archives with the added ability to add custom content before the archives loop.

Genesis Custom Post Types Archives is for sites that use **Custom Post Types**. If you do not use CPTs, then this plugin will prove useless for you.

IMPORTANT: 
**You must have [Genesis](http://wpsmith.net/get-genesis "Learn more about Genesis") installed. Click [here](http://wpsmith.net/get-genesis "Learn more about Genesis") to learn more about [Genesis](http://wpsmith.net/get-genesis "Learn more about Genesis")**


== Installation ==

1. Upload the entire `genesis-cpt-archives` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin via all the Custom Post Type Subpage Menus

== Frequently Asked Questions ==

= Why doesn't my Custom Post Type have a menu for CPT Archives? =
The plugin checks the custom post type registration to determine whether the plugin is public, has a UI, has an archive, compatibility type of post only, and is not builtin. If you want to extend or change, you can change the pt_args via the builtin filter (gcpta_pt_args).

= I get 404: Not Found on my archives pages? =
Go to Settings > Permalinks and save your permalinks again. You need to flush your .htaccess rules every time you configure a custom post type. Otherwise you will get a 404.

= Do you have future plans for this plugin? =
Not at this time.
* If you have suggestions, please feel free to [contact me](http://wpsmith.net/contact/ "Contact Travis")


== Screenshots ==

None.

== Changelog ==

= 0.6.5-0.6.6 =
* Removed duplicate sidebars from simple sidebars support.
* Fixed simple sidebars support, admin error.

= 0.6.1 =
* Removed test class props GaryJ.
* Added priority (5) to gcpta_do_post_image to come back into alignment with genesis_do_image. props GaryJ.

= 0.6.0 =
* Added Simple Sidebars support
* Added excerpts support to Genesis Grid

= 0.5.1 =
* Minor Debug Notice fix

= 0.5 =
* Initial Release

= 0.1-0.4 =
* Private Beta

== Special Thanks ==
I owe a huge debt of gratitude to all the folks at [StudioPress](http://wpsmith.net/get-genesis/ "StudioPress"), their [themes](http://wpsmith.net/get-genesis/ "StudioPress Themes") make life easier.

And thanks to the various individuals who helped me through the beta testing.
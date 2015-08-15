<?php

/*
Plugin Name: Genesis Custom Post Types Archives
Plugin URI: http://www.wpsmith.net/genesis-custom-post-types-archives
Description: Allows you to customize Genesis Custom Post Type archive pages for solid SEO.
Version: 0.6.6
Author: Travis Smith
Author URI: http://www.wpsmith.net/
License: GPLv2

    Copyright 2012  Travis Smith  (email : http://wpsmith.net/contact)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'GCPTA_DOMAIN' , 'genesis-featured-images' );
define( 'GCPTA_PLUGIN_DIR', dirname( __FILE__ ) );
define( "GCPTA_URL" , WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "" , plugin_basename( __FILE__ ) ) );

/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    wp_die( __( "Sorry, you are not allowed to access this page directly.", GCPTA_DOMAIN ) );
}

register_activation_hook( __FILE__, 'gcpta_activation_check' );

/**
 * Checks for minimum Genesis Theme version before allowing plugin to activate
 *
 * @author Nathan Rice
 * @uses gfi_truncate()
 * @since 0.1
 * @version 0.2
 */
function gcpta_activation_check() {

    $latest = '1.8';

    $theme_info = get_theme_data( TEMPLATEPATH . '/style.css' );

    if ( basename( TEMPLATEPATH ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed and actived %1$sGenesis%2$s or a %3$sGenesis Child Theme%2$s', 'GFI' ), '<a href="http://wpsmith.net/go/genesis">', '</a>', '<a href="http://wpsmith.net/go/spthemes">' ) );
    }

    $version = gcpta_truncate( $theme_info['Version'], 3 );

    if ( version_compare( $version, $latest, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate without %1$sGenesis %2$s%3$s or greater', 'GFI' ), '<a href="http://wpsmith.net/go/genesis">', $latest, '</a>' ) );
    }
}

/**
 *
 * Used to cutoff a string to a set length if it exceeds the specified length
 *
 * @author Nick Croft
 * @since 0.1
 * @version 0.2
 * @param string $str Any string that might need to be shortened
 * @param string $length Any whole integer
 * @return string
 */
function gcpta_truncate( $str, $length=10 ) {

    if ( strlen( $str ) > $length ) {
        return substr( $str, 0, $length );
    } else {
        $res = $str;
    }

    return $res;
}

add_action( 'genesis_init', 'gcpta_init', 15 );
/**
 *
 * Loads required files when needed
 * @author Travis Smith
 * @since 0.1
 *
 */
function gcpta_init() {
	if ( is_admin() )
		require_once( GCPTA_PLUGIN_DIR . '/lib/gcpta-settings.php');
	else {
		require_once( GCPTA_PLUGIN_DIR . '/lib/functions.php' );
		require_once( GCPTA_PLUGIN_DIR . '/lib/seo-functions.php' );
		require_once( GCPTA_PLUGIN_DIR . '/lib/grid_loop.php' );
	}
}

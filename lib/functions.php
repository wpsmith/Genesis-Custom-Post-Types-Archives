<?php

/**
 * Genesis Custom Post Types Archives Functions
 *
 * This file performs a check on the page to add and/or remove
 * functions based on user selections. If cpt archive page, this
 * file filters post info and meta, optionally removes post title,
 * adds scripts to header/footer, adds custom content & headline
 * to top of archive page, and replaces genesis_* functions.
 *
 * @package      WPS_GCPTA
 * @author       Travis Smith <travis@wpsmith.net>
 * @copyright    Copyright (c) 2012, Travis Smith
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        0.1
 * @alter        3.1.2012
 *
 */

add_action( 'get_header' , 'gcpta_post_check' , 5 );
/**
 *
 * Checks the page at the earliest possible time to make adjustments to the page
 *  -removes genesis_post_info
 *  -removes genesis_post_ meta
 *  -removes genesis_do_post_title
 *  -removes genesis_do_post_image
 *
 * @author Travis Smith
 * @since 0.1
 * @uses gcpta_remove_function() removes a specific function
 * @uses gcpta_replace_functions() replaces a couple functions
 * @uses genesis_get_option() Get plugin setting value
 * @global stdClass $post Post object
 */
function gcpta_post_check() {
	global $post;
	
	if ( ! is_post_type_archive() )
		return;

	if ( genesis_get_option( 'gcpta_remove_post_info_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		gcpta_remove_function( 'genesis_post_info' );
	elseif ( genesis_get_option( 'gcpta_post_info_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		add_filter( 'genesis_post_info' , 'gcpta_post_info_filter' );
	
	if ( genesis_get_option( 'gcpta_remove_post_meta_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		gcpta_remove_function( 'genesis_post_meta' );
	elseif ( genesis_get_option( 'gcpta_post_meta_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		add_filter( 'genesis_post_meta' , 'gcpta_post_meta_filter' );
	
	if ( genesis_get_option( 'gcpta_post_title_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		gcpta_remove_function( 'genesis_do_post_title' );
	
	gcpta_replace_functions();
	gcpta_remove_function( 'genesis_do_post_image' );
		
	if ( is_post_type_archive() && ! gcpta_is_doing_grid_loop() )
		add_action( 'genesis_post_content', 'gcpta_do_post_image', 5 );
}

/**
 * Removes a function hooked into somewhere
 *
 * @author Travis Smith
 * @param string function name
 * @global array $wp_filter
 */
function gcpta_remove_function( $function ) {
    global $wp_filter;
 
    // Loop through all hooks (yes, stored under the $wp_filter global)
    foreach ( $wp_filter as $hook => $priority)  {
 
		// has_action returns int for the priority
		if ( $priority = has_action( $hook, $function ) ) {

			// If there's a function hooked in, remove the genesis_* function
			// from whichever hook we're looping through at the time.
			remove_action( $hook, $function, $priority );
		}
    }
}

/**
 * Replace some genesis_* functions hooked into somewhere for some gcpta_* functions
 * of the same suffix, at the same hook and priority
 *
 * @author Gary Jones
 *
 * @global array $wp_filter
 */
function gcpta_replace_functions() {
    global $wp_filter;
 
    // List of genesis_* functions to be replaced with gcpta_* functions.
    $functions = array(
        'do_post_content',
        'posts_nav',
        'do_sidebar',
        'do_sidebar_alt',
    );
 
    // Loop through all hooks (yes, stored under the $wp_filter global)
    foreach ( $wp_filter as $hook => $priority)  {
 
        // Loop through our array of functions for each hook
        foreach( $functions as $function) {
 
            // has_action returns int for the priority
            if ( $priority = has_action( $hook, 'genesis_' . $function ) ) {
 
                // If there's a function hooked in, remove the genesis_* function
                // from whichever hook we're looping through at the time.
                remove_action( $hook, 'genesis_' . $function, $priority );
 
                // Add a replacement function in at the same time.
                add_action( $hook, 'gcpta_' . $function, $priority );
            }
        }
    }
 
}

/**
 * Display secondary sidebar.
 *
 * Display custom sidebar if one exists, else display default secondary sidebar.
 *
 */
function gcpta_do_sidebar() {
	if ( ! gcpta_sidebar() )
		genesis_do_sidebar();
}

/**
 * Display primary sidebar.
 *
 * Display custom sidebar if one exists, else display default primary sidebar.
 *
 */
function gcpta_do_sidebar_alt() {
	if ( ! gcpta_sidebar( 'gcpta_ss_sidebar_alt_' ) )
		genesis_do_sidebar_alt();
}

add_action( 'get_header', 'replace_ss_support', 99 );
/**
 * Remove simple sidebars support
 */
function replace_ss_support() {
	gcpta_remove_function( 'genesis_do_sidebar' );
	gcpta_remove_function( 'ss_do_sidebar' );
	gcpta_remove_function( 'genesis_do_sidebar_alt' );
	gcpta_remove_function( 'ss_do_sidebar_alt' );
	add_action( 'genesis_sidebar', 'gcpta_do_sidebar' );
	add_action( 'genesis_sidebar_alt', 'gcpta_do_sidebar_alt' );
}

/**
 * Sidebar Helper Function
 *
 * Checks to see if on post type archive page, adds correct sidebar
 *
 */
function gcpta_sidebar( $key = 'gcpta_ss_sidebar_' ) {
	global $post;
	if ( is_post_type_archive() && $sidebar_key = genesis_get_option( $key . $post->post_type , 'gcpta-settings-' . $post->post_type, 'gcpta-settings' ) ) {
		if ( dynamic_sidebar( $sidebar_key ) ) return true;
	}
}

/**
 * Filters post meta
 *
 * @author Travis Smith
 *
 * @global stdClass $post Post object
 */
function gcpta_post_meta_filter( $post_meta ) {
	global $post;
	return genesis_get_option( 'gcpta_post_meta_' . $post->post_type , 'gcpta-settings-' . $post->post_type );
}

/**
 * Filters post info
 *
 * @author Travis Smith
 *
 * @global stdClass $post Post object
 */
function gcpta_post_info_filter( $post_info ) {
	global $post;
	return genesis_get_option( 'gcpta_post_info_' . $post->post_type , 'gcpta-settings-' . $post->post_type );
}

add_action( 'genesis_before_loop' , 'gcpta_intro' );
/**
 * Adds intro content to the beginning of the custom post type archives page
 *
 * @author Travis Smith
 *
 * @global stdClass $post Post object
 * @uses is_post_type_archive() Checks if on cpt archive page
 * @uses genesis_get_option() Get plugin setting value
 * @uses get_post_type_object() Gets post type obj for use
 */
function gcpta_intro() {
	global $post;
	
	if ( ! is_post_type_archive() || ! genesis_get_option( 'gcpta_intro_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) ) {
		return;
	}
	
	$pt = get_post_type_object( $post->post_type );
	
	$headline   = genesis_get_option( 'gcpta_headline_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_headline_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) : $pt->label . __( 'Archives' , GCPTA_DOMAIN );
	$headline   = sprintf( '<h1>%s</h1>', esc_html( $headline ) );
	$archives_intro = apply_filters( 'the_content' , genesis_get_option( 'gcpta_intro_content_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) );
	
	if ( $headline || $archives_intro )
		printf( '<div class="archives-intro">%s</div>', $headline . $archives_intro );
	
}

add_filter( 'gcpta_header_scripts', 'do_shortcode' );
add_action( 'wp_head', 'gcpta_header_scripts' );
/**
 * Echo header scripts in to wp_head().
 *
 * Allows shortcodes.
 *
 * Applies genesis_header_scripts on value stored in header_scripts setting.
 *
 * Also echoes scripts from the post's custom field.
 *
 * @since 0.1
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses genesis_get_option() Get plugin setting value
 * @uses genesis_get_custom_field() Echo custom field value
 */
function gcpta_header_scripts() {
	global $post;
	echo apply_filters( 'gcpta_header_scripts' , genesis_get_option( 'gcpta_keywords_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) );

}

add_filter( 'gcpta_footer_scripts', 'do_shortcode' );
add_action( 'wp_footer', 'gcpta_footer_scripts' );
/**
 * Echo the footer scripts, defined in Theme Settings.
 *
 * Applies the 'genesis_footer_scripts filter to the value returns from the
 * footer_scripts option.
 *
 * @since 0.1
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses genesis_get_option()
 */
function gcpta_footer_scripts() {
	global $post;
	echo apply_filters( 'gcpta_footer_scripts' , genesis_get_option( 'gcpta_keywords_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) );

}

add_filter( 'genesis_site_layout' , 'gcpta_layout' );
/**
 * Returns cpt archive layout
 *
 * @since 0.1
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses genesis_get_option() Get plugin setting value
 */
function gcpta_layout( $layout ) {
	global $post;
	if ( is_post_type_archive() && ( ! is_tag() || ! is_category() ) && ( genesis_get_option( 'gcpta_layout_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ) )
		return genesis_get_option( 'gcpta_layout_' . $post->post_type, 'gcpta-settings-' . $post->post_type );
	else
		return $layout;
}

add_filter( 'wp_title', 'gcpta_title', 15, 3 );
/**
 * Return filtered post title.
 *
 * This function does 3 things:
 * 1. Pulls the values for $sep and $seplocation, uses defaults if necessary
 * 2. Determines if the site title should be appended
 * 3. Allows the user to set a custom title on a per-page/post basis
 *
 * @since 0.1
 * @author Travis Smith
 * @uses genesis_get_seo_option() Get SEO setting value
 * @uses genesis_get_custom_field() Get custom field value
 * @uses genesis_get_option() Get plugin setting value
 *
 * @global stdClass $post Post object
 * @param string $title Existing page title
 * @param string $sep Separator character(s). Default is '-' if not set
 * @param string $seplocation Separator location - "left" or "right". Default is "right" if not set
 * @return string Page title
 */
function gcpta_title( $title, $sep, $seplocation ) {
	global $post;

	if ( ! is_post_type_archive() ) return $title;
	
	$sep = genesis_get_seo_option( 'doctitle_sep' ) ? genesis_get_seo_option( 'doctitle_sep' ) : '–';
	$seplocation = genesis_get_seo_option( 'doctitle_seplocation' ) ? genesis_get_seo_option( 'doctitle_seplocation' ) : 'right';
	$post_type = get_post_type_object( $post->post_type );
	
	/** Determine the doctitle */
	$title = genesis_get_option( 'gcpta_doctitle_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_doctitle_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) : $post_type->label;
	
	/** Append site description, if necessary */
	$title = genesis_get_option( 'gcpta_description_' . $post->post_type ) ? $title . " $sep " . get_bloginfo( 'description' ) : $title;

	/**	If we don't want site name appended */
	if ( ! genesis_get_seo_option( 'append_site_title' ) )
		return esc_html( trim( $title ) );

	/** Else append the site name */
	$title = 'right' == $seplocation ? $title . " $sep " . get_bloginfo( 'name' ) : get_bloginfo( 'name' ) . " $sep " . $title;
	return esc_html( trim( $title ) );

}

/**
 * Conditionally echoes post navigation in a format dependent on chosen setting.
 *
 * @since 0.2.3
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses genesis_get_option() Get plugin setting value
 * @uses is_post_type_archive() Checks for cpt archive page to customize pagination
 * @uses genesis_prev_next_posts_nav() Displays prev-next post nav
 * @uses genesis_numeric_posts_nav() Displays numeric post nav
 * @uses genesis_older_newer_posts_nav() Displays older-newer post nav
 */
function gcpta_posts_nav() {
	global $post;
	
	if ( ! is_post_type_archive() ) 
		$nav = genesis_get_option( 'posts_nav' );
	else
		$nav = genesis_get_option( 'gcpta_posts_nav_' . $post->post_type, 'gcpta-settings-' . $post->post_type );

	if( 'prev-next' == $nav )
		genesis_prev_next_posts_nav();
	elseif( 'numeric' == $nav )
		genesis_numeric_posts_nav();
	else
		genesis_older_newer_posts_nav();

}

/**
 * Echo the post image on archive pages.
 *
 * If this an archive page and the option is set to show thumbnail, then it
 * gets the image size as per the theme setting, wraps it in the post  permalink
 * and echoes it.
 *
 * @since 0.1
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses is_post_type_archive() Checks for cpt archive page 
 * @uses genesis_get_option() Get plugin setting value
 * @uses genesis_get_image() Return an image pulled from the media gallery
 *
 */
function gcpta_do_post_image() {
	global $post;
	
	if ( is_post_type_archive() && genesis_get_option( 'gcpta_content_archive_thumbnail_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) && 'grid' != genesis_get_option( 'gcpta_loop_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ) {
		$img = genesis_get_image( array( 'format' => 'html', 'size' => genesis_get_option( 'gcpta_image_size_' . $post->post_type, 'gcpta-settings-' . $post->post_type ), 'attr' => array( 'class' => genesis_get_option( 'gcpta_image_size_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) . ' post-image' ) ) );
		if ( $img )
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $img );
	}
}

/**
 * Echo the post content.
 *
 * On single posts or pages it echoes the full content, and optionally the
 * trackback string if they are enabled. On single pages, also adds the edit
 * link after the content.
 *
 * Elsewhere it displays either the excerpt, limited content, or full content.
 *
 * Pagination links are included at the end, if needed.
 *
 * @since 0.1
 * @author Travis Smith
 * @global stdClass $post Post object
 * @uses genesis_get_option() Get plugin setting value
 * @uses the_content_limit() Limited content
 */
function gcpta_do_post_content() {
	global $post;
	if ( 'grid' == genesis_get_option( 'gcpta_loop_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) && 2 != genesis_get_option( 'gcpta_grid_content_limit_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		return;
	
	$readmore = genesis_get_option( 'gcpta_grid_read_more_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_read_more_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) : __( '[Read more...]', 'genesis' );
	
	if ( 'excerpts' == genesis_get_option( 'gcpta_content_archive_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) || 2 == genesis_get_option( 'gcpta_grid_content_limit_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) ) {
		the_excerpt();
		echo apply_filters( 'gcpta_excerpt_read_more', sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), $readmore ), $readmore );
	}
	else {
		if ( genesis_get_option( 'gcpta_content_archive_limit_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) )
			the_content_limit( (int) genesis_get_option( 'gcpta_content_archive_limit_' . $post->post_type, 'gcpta-settings-' . $post->post_type ),$readmore );
		else
			the_content( $readmore );
	}

	wp_link_pages( array( 'before' => '<p class="pages">' . __( 'Pages:', 'genesis' ), 'after' => '</p>' ) );

}

//add_action( 'genesis_before', 'wps_filters2' );
function wps_filters2() {
	global $wp_filter;
	print_r($wp_filter);
}
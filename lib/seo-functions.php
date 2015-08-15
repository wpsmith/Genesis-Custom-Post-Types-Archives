<?php

/**
 * Genesis Custom Post Types Archives SEO Functions
 *
 * This file controls all of the custom post type archive SEO options extending
 * genesis_* SEO functions.
 *
 * @package      WPS_GCPTA
 * @author       Travis Smith <travis@wpsmith.net>
 * @copyright    Copyright (c) 2012, Travis Smith
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        0.1
 * @alter        3.1.2012
 *
 */

/**
 * Checks to see if the Genesis SEO has been disabled or if there is
 * another SEO plugin activated
 *
 * @since 0.1
 * @uses genesis_seo_disabled()
 * @uses genesis_detect_seo_plugins()
 * @uses is_post_type_archive()
 * return boolean true if on cpt archive page, no seo plugins, & genesis
 *        seo has not been disabled.
 */
function gcpta_seo_check() {
	if ( genesis_seo_disabled() || genesis_detect_seo_plugins() || ! is_post_type_archive() )
		return true;
	else
		return false;
}

add_action( 'genesis_meta' , 'gcpta_seo_description' );
/**
 * Generates the meta description for cpt archives
 *
 * Outputs nothing if description isn't present.
 *
 * @since 0.1
 *
 * @uses genesis_get_seo_option() Get SEO setting value
 * @uses genesis_get_custom_field() Get custom field value
 * @uses gcpta_seo_check() Checks for SEO options
 *
 * @global stdClass $post Post object
 */
function gcpta_seo_description() {
	global $post;
	
	if ( gcpta_seo_check() )
		return;
	
	$description = genesis_get_option( 'gcpta_description_' . $post->post_type, 'gcpta-settings-' . $post->post_type );
	
	/** Add the description if one exists */
	if ( $description )
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";

}

add_action( 'genesis_meta' , 'gcpta_seo_keywords' );
/**
 * This function generates the meta keywords for cpt archives
 *
 * Outputs nothing if keywords aren't present.
 *
 * @since 0.1
 *
 * @uses genesis_get_seo_option() Get SEO setting value
 * @uses genesis_get_custom_field() Get custom field value
 * @uses gcpta_seo_check() Checks for SEO options
 *
 * @global stdClass $post Post object
 */
function gcpta_seo_keywords() {
	global $post;
	
	if ( gcpta_seo_check() )
		return;
	
	$keywords = genesis_get_option( 'gcpta_keywords_' . $post->post_type, 'gcpta-settings-' . $post->post_type );
	
	/** Add the keywords if they exist */
	if ( $keywords )
		echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '" />' . "\n";
}

add_action( 'genesis_meta' , 'gcpta_seo_robots' );
/**
 * This function generates the index / follow / noodp / noydir / noarchive code
 * in the document <head> for cpt archives page.
 *
 * @since 0.1
 *
 * @uses genesis_get_seo_option() Get SEO setting value
 * @uses genesis_get_custom_field() Get custom field value
 * @uses gcpta_seo_check() Checks for SEO options
 *
 * @global stdClass $post Post object
 * @return null Returns early if blog is not public
 */
function gcpta_seo_robots() {
	
	if ( gcpta_seo_check() )
		return;

	global $post;
	//$post_type = get_query_var( 'post_type' );

	/** Defaults */
	$meta = array(
		'noindex'   => genesis_get_option( 'gcpta_noarchive_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? 'noindex' : '',
		'nofollow'  => genesis_get_option( 'gcpta_nofollow_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? 'nofollow' : '',
		'noarchive' => genesis_get_option( 'gcpta_noarchive_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) ? 'noarchive' : '',
		'noodp'     => genesis_get_seo_option( 'noodp' ) ? 'noodp' : '',
		'noydir'    => genesis_get_seo_option( 'noydir' ) ? 'noydir' : '',
	);
	
	/**	noindex paged archives, if canonical archives is off */
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$meta['noindex'] = $paged > 1 && ! genesis_get_seo_option( 'canonical_archives' ) ? 'noindex' : $meta['noindex'];
	
	/** Strip empty array items */
	$meta = array_filter( $meta );

	/** Add meta if any exist */
	if ( $meta )
		printf( '<meta name="robots" content="%s" />' . "\n", implode( ',', $meta ) );
	
}

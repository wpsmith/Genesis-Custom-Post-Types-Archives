<?php

/**
 * Genesis Custom Post Types Archives Grid Loop Functions
 *
 * This file controls all of the grid loop options and functionality.
 *
 * @package      WPS_GCPTA
 * @author       Travis Smith <travis@wpsmith.net>
 * @copyright    Copyright (c) 2012, Travis Smith
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @link		 http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * @since        0.1
 *
 */

/**
 * Possibly amend the loop.
 * 
 * Specify the conditions under which the grid loop should be used.
 *
 * @author Travis Smith
 * @author Bill Erickson
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * @global stdClass $post Post object
 * @return boolean Return true of doing the grid loop, false if not. 
 */
function gcpta_is_doing_grid_loop() {
	global $post;
	// Amend this conditional to pick where this grid looping occurs.
	// This says to use the grid loop everywhere except single posts,
	// single pages and single attachments.
	if ( is_post_type_archive() && 'grid' == genesis_get_option( 'gcpta_loop_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) )
		return true;
	else
		return false;

}

/**
 * Grid Loop Arguments
 * 
 * Specify all the desired grid loop and query arguments
 *
 * @author Travis Smith
 * @author Bill Erickson
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * @global stdClass $post Post object
 * @return array $arguments
 */
function gcpta_grid_loop_arguments() {
	global $post;
	$post_type = $post->post_type;
	
	// Features Settings
	$features               = genesis_get_option( 'gcpta_features_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_features_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 0;
	$features_content_limit = genesis_get_option( 'gcpta_features_content_limit_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_features_content_limit_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 0;
	
	if ( 0 == $features )
		$feature_image_size = '';
	else
		$feature_image_size = genesis_get_option( 'gcpta_features_image_size_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_features_image_size_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 'grid-thumbnail';
	
	$feature_image_class = genesis_get_option( 'gcpta_features_image_class_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? 'alignleft post-image ' . genesis_get_option( 'gcpta_features_image_class_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 'alignleft post-image';
	
	// Grid Settings
	$grid_posts         = genesis_get_option( 'gcpta_grid_posts_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_posts_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 2;
	$grid_content_limit = genesis_get_option( 'gcpta_grid_content_limit_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_content_limit_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 250;
	$grid_image_size    = genesis_get_option( 'gcpta_grid_image_size_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_image_size_' . $post_type , 'gcpta-settings-' . $post->post_type ) : '';
	$grid_image_class   = genesis_get_option( 'gcpta_grid_image_class_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? 'alignleft post-image ' . genesis_get_option( 'gcpta_grid_image_class_' . $post_type , 'gcpta-settings-' . $post->post_type ) : 'alignleft post-image';
	$read_more          = genesis_get_option( 'gcpta_grid_read_more_' . $post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_read_more_' . $post_type , 'gcpta-settings-' . $post->post_type ) : '';
	
	// Grid Args
	$grid_args = apply_filters( 'gcpta_grid_args' , array(
		'features'              => $features,
		'feature_content_limit' => $features_content_limit,
		'feature_image_size'    => $feature_image_size,
		'feature_image_class'   => $feature_image_class,
		'grid_content_limit' 	=> $grid_content_limit,
		'grid_image_size'		=> $grid_image_size,
		'grid_image_class'      => $grid_image_class,
		'post_type'				=> $post_type,
		'more' 					=> $read_more,
	) );

	$query_args = array(
		'posts_per_page'        => $grid_posts,
	);

	return array(
		'grid_args'  => $grid_args,
		'query_args' => $query_args,
	);
}

add_action( 'genesis_before_loop', 'gcpta_prepare_grid_loop' );
/**
 * Prepare Grid Loop.
 * 
 * Swap out the standard loop with the grid and apply classes.
 *
 * @author Travis Smith
 * @author Bill Erickson
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * @global stdClass $post Post object
 */
function gcpta_prepare_grid_loop() {
	global $post;
	if ( gcpta_is_doing_grid_loop() ) {
		// Remove the standard loop
		remove_action( 'genesis_loop', 'genesis_do_loop' );
	
		// Use the prepared grid loop
		add_action( 'genesis_loop', 'gcpta_do_grid_loop' );
		add_action( 'genesis_before_post_content', 'gcpta_grid_check' );
	
		// Add some extra post classes to the grid loop so we can style the columns
		add_filter( 'genesis_grid_loop_post_class', 'gcpta_grid_loop_post_class' );
	}
	
}

/**
 * Grid check to remove content based on options
 * 
 * @author Travis Smith
 * @global stdClass $post Post object
 */
function gcpta_grid_check() {
	global $post;
	if ( 'grid' != genesis_get_option( 'gcpta_loop_' . $post->post_type, 'gcpta-settings-' . $post->post_type ) || 
	     2 == genesis_get_option( 'gcpta_grid_content_limit_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) ||
		 1 == genesis_get_option( 'gcpta_grid_content_limit_' . $post->post_type , 'gcpta-settings-' . $post->post_type ))
		remove_action( 'genesis_post_content', 'genesis_grid_loop_content' );
}

add_action( 'pre_get_posts', 'gcpta_grid_query' );
/**
 * Grid query to get the posts that will appear in the grid.
 * 
 * Any changes to the actual query (posts per page, category…) should be here.
 *
 * @author Travis Smith
 * @author Bill Erickson
 * @author Gary Jones
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * @global stdClass $post Post object
 * @param WP_Query $query
 */
function gcpta_grid_query( $query ) {
	global $post;
	if ( ! isset( $post ) ) return $query;
	if ( 'grid' != genesis_get_option( 'gcpta_loop_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) )
		return $query;
	
	// Only apply to main query, if this matches our grid query conditional, and if it isn't in the back-end
	//if ( $query->is_main_query() && gcpta_is_doing_grid_loop() && ! is_admin() ) {
	if ( gcpta_is_doing_grid_loop() && ! is_admin() ) {
		
		// Get all arguments
		$args = gcpta_grid_loop_arguments();
		
		// Don't edit below, this does the logic to figure out how many posts on each page
		$posts_per_page = $args['query_args']['posts_per_page'];
		$features = $args['grid_args']['features'];
		$offset = 0;
		$paged = $query->query_vars['paged'];
		if ( $posts_per_page >= 0 ) {
			if ( 0 == $paged )
				// If first page, add number of features to grid posts, so balance is maintained
				$posts_per_page += $features;
			else
				// Keep the offset maintained from our page 1 adjustment
				$offset = ( $paged - 1 ) * $posts_per_page + $features;
				
			$query->set( 'offset', $offset );
		}
		else
			$query->set( 'nopaging', true ); 

		$query->set( 'posts_per_page', $posts_per_page );
		$query->set( 'posts_per_archive_page', $posts_per_page );
	}
	
}

/**
 * Prepare the grid loop.
 * 
 * Only use grid-specific arguments. All query args should be done in the
 * gcpta_grid_query() function.
 *  
 * @author Gary Jones
 * @author Bill Erickson
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 *
 * @uses genesis_grid_loop() Requires Genesis 1.5
 *
 * @global WP_Query $wp_query Post query object.
 */
function gcpta_do_grid_loop() {
	global $wp_query;

	// Grid specific arguments
	$all_args = gcpta_grid_loop_arguments();
	$grid_args = $all_args['grid_args'];

	// Combine with original query
	$args = array_merge( $wp_query->query_vars, $grid_args );

	// Create the Grid Loop
	genesis_grid_loop( $args );

}

/**
 * Add some extra body classes to grid posts.
 * 
 * Change the $columns value to alter how many columns wide the grid uses.
 *
 * @author Travis Smith
 * @author Gary Jones
 * @author Bill Erickson
 * @link   http://code.garyjones.co.uk/genesis-grid-loop-advanced/
 * 
 * @global array   $_genesis_loop_args
 * @global integer $loop_counter
 * @global stdClass $post Post object
 *
 * @param array $grid_classes 
 */
function gcpta_grid_loop_post_class( $grid_classes ) {

	global $_genesis_loop_args, $loop_counter, $post;

	// Alter this number to change the number of columns - used to add class names
	$columns = genesis_get_option( 'gcpta_grid_columns_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) ? genesis_get_option( 'gcpta_grid_columns_' . $post->post_type , 'gcpta-settings-' . $post->post_type ) : 2;
	
	// Be able to convert the number of columns to the class name in Genesis
	$fractions = array( '', 'half', 'third', 'fourth', 'fifth', 'sixth' );

	// Only want extra classes on grid posts, not feature posts
	if ( $loop_counter >= $_genesis_loop_args['features'] ) {
		// Make a note of which column we're in
		$column_number = ( ( $loop_counter - $_genesis_loop_args['features'] ) % $columns ) + 1;
		
		// Add genesis-grid-column-? class to know how many columns across we are
		$grid_classes[] = sprintf( 'genesis-grid-column-%d', $column_number );

		// Add one-* class to make it correct width
		$grid_classes[] = sprintf( 'one-' . $fractions[$columns - 1], $columns );
		
		// Add a class to the first column, so we're sure of starting a new row with no padding-left
		if ( 1 == $column_number )
			$grid_classes[] = 'first';
	}

	return $grid_classes;

}

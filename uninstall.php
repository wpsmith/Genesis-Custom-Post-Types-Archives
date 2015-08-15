<?php

//If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

$settings = delete_option( 'gcpta-settings' );

$pt_args = apply_filters( 'gcpta_pt_args' , array( 'public' => true, 'capability_type' => 'post', '_builtin' => false, 'has_archive' => true, 'show_ui' => true ) );
$pts = get_post_types( $pt_args , 'names', 'and' );

foreach ( $pts  as $pt ) {
	$settings = delete_option( 'gcpta-settings-' . $pt );
}

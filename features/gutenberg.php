<?php

namespace jn;

add_action( 'jurassic_ninja_init', function() {
	$defaults = [
		'gutenberg' => false,
	];
	// Declare that this feature will be off by default when launching a site with the /create endpoint.
	add_filter( 'jurassic_ninja_rest_feature_defaults', function( $defaults ) {
		return array_merge( $defaults, [
			'gutenberg' => false
		] );
	} );

	// Declare that this feature can be requested or disabled from the /create endpoint.
	add_filter( 'jurassic_ninja_rest_create_request_features', function( $features, $json_params ) {
		if ( isset( $json_params['gutenberg'] ) ) {
			$features['gutenberg'] = $json_params['gutenberg'];
		}
		return $features;
	}, 10, 2 );

	//Hook the feature before adding autologin to the site.
	add_action( 'jurassic_ninja_add_features_before_auto_login', function( &$app, $features, $domain ) use ( $defaults ) {
		$features = array_merge( $defaults, $features );
		if ( $features['gutenberg'] ) {
			debug( '%s: Adding Gutenberg', $domain );
			add_gutenberg_plugin();
		}
	}, 10, 3 );

} );

/**
 * Installs and activates Gutenberg Plugin on the site.
 */
function add_gutenberg_plugin() {
	$cmd = 'wp plugin install gutenberg --activate';
	add_filter( 'jurassic_ninja_feature_command', function ( $s ) use ( $cmd ) {
		return "$s && $cmd";
	} );
}

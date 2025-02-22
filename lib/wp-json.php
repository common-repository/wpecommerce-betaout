<?php
/**
 * JSON API support for WordPress
 *
 * @package WordPress
 */

/**
 * Whether this is a XMLRPC Request
 *
 * @var bool
 * @todo Remove me in favour of JSON_REQUEST
 */
define( 'XMLRPC_REQUEST', true );

/**
 * Whether this is a JSON Request
 *
 * @var bool
 */
define( 'JSON_REQUEST', true );

/** Include the bootstrap for setting up WordPress environment */
include( './wp-load.php' );

include_once( ABSPATH . 'wp-admin/includes/admin.php' );
include_once( ABSPATH . WPINC . '/class-wp-xmlrpc-server.php' );
include_once( ABSPATH . WPINC . '/class-wp-json-datetime.php' );
include_once( ABSPATH . WPINC . '/class-wp-json-server.php' );

// Allow for a plugin to insert a different class to handle requests.
$wp_betaout_server_class = apply_filters( 'wp_betaout_server_class', 'WP_BETAOUT_Server' );

$wp_betaout_server = new $wp_betaout_server_class;

// Fire off the request
$wp_betaout_server->serve_request();

exit;

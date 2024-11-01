<?php
/*
  Plugin Name: WP eCommerce Betaout
  Plugin URI: http://www.betaout.com
  Description: Manage all your Wordpress sites and Editorial team from a single interface
  Version: 1.0
  Author: BetaOut (support@betaout.com)
  Author URI: http://www.betaout.com
  License: GPLv2 or later
 */
ini_set("display_errors",0);

try{
    
function betaout_admin_notice1() {
    echo "Please deactivate Woocommerce betaout plugin";
    return false;
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
include_once 'includes/amplify.php';
include_once 'includes/amplifylogin.php';


include_once( dirname( __FILE__ ) . '/lib/class-jsonserializable.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-datetime.php' );

include_once( dirname( __FILE__ ) . '/lib/class-wp-json-responsehandler.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-server.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-responseinterface.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-response.php' );

include_once( dirname( __FILE__ ) . '/lib/class-wp-json-wpecorder.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-wpecproduct.php' );
include_once( dirname( __FILE__ ) . '/lib/class-wp-json-wpecuser.php' );

//------------------------------------------------------------------------------
//the plugin will work function if cURL and add_function exist and the appropriate version of PHP is available.
$adminErrorMessage = "";

if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    $adminErrorMessage .= "PHP 5.2 or newer not found!<br/>";
}

if (!function_exists("curl_init")) {
    $adminErrorMessage .= "cURL library was not found!<br/>";
}

if (!function_exists("session_start")) {
    $adminErrorMessage .= "Sessions are not enabled!<br/>";
}

if (!function_exists("json_decode")) {
    $adminErrorMessage .= "JSON was not enabled!<br/>";
}

if(!empty($adminErrorMessage)){
    add_action( 'admin_notices', '$adminErrorMessage' );
    exit;
}


add_action('init', array('AmplifyLoginNew','amplifyinit'));

function betaout_api_register_rewrites() {
	add_rewrite_rule( '^' . betaout_get_url_prefix() . '/?$','index.php?json_route=/','top' );
	add_rewrite_rule( '^' . betaout_get_url_prefix() . '(.*)?','index.php?json_route=$matches[1]','top' );
  }
  
  function betaout_api_init() {
	betaout_api_register_rewrites();

	global $wp;
	$wp->add_query_var( 'json_route' );
   }
add_action( 'init', 'betaout_api_init' );

function betaout_api_maybe_flush_rewrites() {
	flush_rewrite_rules();
}
add_action( 'init', 'betaout_api_maybe_flush_rewrites', 999 );


function betaout_api_default_filters( $server ) {
    
       global $wp_json_wpecuser,$wp_json_product,$wp_json_wpecorder;
      //user
        $wp_json_wpecuser = new WP_BETAOUT_WpecUsers( $server );
        
        add_filter('json_endpoints', array( $wp_json_wpecuser, 'register_routes' ), 0);
        
        //order
       $wp_json_wpecorder = new WP_BETAOUT_WpecOrder( $server );
       add_filter('json_endpoints', array( $wp_json_wpecorder, 'register_routes' ), 0);
//        //wpec product
        $wp_json_product = new WP_BETAOUT_WpecProduct( $server );
	add_filter('json_endpoints', array( $wp_json_product, 'register_routes' ), 0 );
 
    }
    
add_action( 'wp_betaout_server_before_serve', 'betaout_api_default_filters', 10, 1 );

function betaout_api_loaded() {
    
	if ( empty( $GLOBALS['wp']->query_vars['json_route'] ) )
		return;

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

	global $wp_betaout_server;

	// Allow for a plugin to insert a different class to handle requests.
	$wp_betaout_server_class = apply_filters( 'wp_betaout_server_class', 'WP_BETAOUT_Server' );
	$wp_betaout_server = new $wp_betaout_server_class;

	/**
	 * Prepare to serve an API request.
	 *
	 * Endpoint objects should be created and register their hooks on this
	 * action rather than another action to ensure they're only loaded when
	 * needed.
	 *
	 * @param WP_JSON_ResponseHandler $wp_betaout_server Response handler object
	 */
	do_action( 'wp_betaout_server_before_serve', $wp_betaout_server );

	// Fire off the request
	$wp_betaout_server->serve_request( $GLOBALS['wp']->query_vars['json_route'] );

	// Finish off our request
	die();
}
add_action( 'template_redirect', 'betaout_api_loaded', -100 );



function betaout_register_post_type( $post_type, $args ) {
	global $wp_post_types;

	$type = &$wp_post_types[ $post_type ];

	// Exception for pages
	if ( $post_type === 'page' ) {
		$type->show_in_json = true;
	}

	// Exception for revisions
	if ( $post_type === 'revision' ) {
		$type->show_in_json = true;
	}

	if ( ! isset( $type->show_in_json ) ) {
		$type->show_in_json = $type->publicly_queryable;
	}
}
add_action( 'registered_post_type', 'betaout_register_post_type', 10, 2 );
  
function betaout_get_url_prefix() {
	return apply_filters( 'betaout_url_prefix', 'betaout-json' );
}

function betaout_ensure_response( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( $response instanceof WP_BETAOUT_ResponseInterface ) {
		return $response;
	}

	return new WP_BETAOUT_Response( $response );
}

/**
 * Parse an RFC3339 timestamp into a DateTime
 *
 * @param string $date RFC3339 timestamp
 * @param boolean $force_utc Force UTC timezone instead of using the timestamp's TZ?
 * @return DateTime
 */
function betaout_parse_date( $date, $force_utc = false ) {
	// Default timezone to the server's current one
	$timezone = betaout_get_timezone();

	if ( $force_utc ) {
		$date = preg_replace( '/[+-]\d+:?\d+$/', '+00:00', $date );
		$timezone = new DateTimeZone( 'UTC' );
	}

	// Strip millisecond precision (a full stop followed by one or more digits)
	if ( strpos( $date, '.' ) !== false ) {
		$date = preg_replace( '/\.\d+/', '', $date );
	}
	$datetime = WP_BETAOUT_DateTime::createFromFormat( DateTime::RFC3339, $date, $timezone );

	return $datetime;
}

function betaout_get_timezone() {
	static $zone = null;

	if ( $zone !== null ) {
		return $zone;
	}

	$tzstring = get_option( 'timezone_string' );

	if ( ! $tzstring ) {
		// Create a UTC+- zone if no timezone string exists
		$current_offset = get_option( 'gmt_offset' );
		if ( 0 == $current_offset ) {
			$tzstring = 'UTC';
		} elseif ( $current_offset < 0 ) {
			$tzstring = 'Etc/GMT' . $current_offset;
		} else {
			$tzstring = 'Etc/GMT+' . $current_offset;
		}
	}
	$zone = new DateTimeZone( $tzstring );

	return $zone;
}


function betaout_cookie_check_errors( $result ) {
    
	if ( ! empty( $result ) ) {
		return $result;
	}

	global $wp_json_auth_cookie;

	// Are we using cookie authentication?
	// (If we get an auth error, but we're still logged in, another
	// authentication must have been used.)
	if ( $wp_json_auth_cookie !== true && is_user_logged_in() ) {
		return $result;
	}

	// Do we have a nonce?
	$nonce = null;
	if ( isset( $_REQUEST['_wp_json_nonce'] ) ) {
		$nonce = $_REQUEST['_wp_json_nonce'];
	} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
		$nonce = $_SERVER['HTTP_X_WP_NONCE'];
	}

	if ( $nonce === null ) {
		// No nonce at all, so act as if it's an unauthenticated request
		wp_set_current_user( 0 );
		return true;
	}

	// Check the nonce
	$result = wp_verify_nonce( $nonce, 'wp_json' );
	if ( ! $result ) {
		return new WP_Error( 'json_cookie_invalid_nonce', __( 'Cookie nonce is invalid' ), array( 'status' => 403 ) );
	}

	return true;
}
add_filter( 'betaout_authentication_errors', 'betaout_cookie_check_errors', 100 );


function betaout_url( $path = '', $scheme = 'json' ) {
	return get_betaout_url( null, $path, $scheme );
}
add_action('wp_ajax_verify_key', 'verify_key_callback');
if(!function_exists('verify_key_callback')){
function verify_key_callback() {
    try{
          $amplifyApiKey = $_POST['amplifyApiKey'];
          $amplifyProjectId = $_POST['amplifyProjectId'];

               update_option("_AMPLIFY_API_KEY",$amplifyApiKey);
               update_option("_AMPLIFY_PROJECT_ID",$amplifyProjectId);
               $curlResponse['responseCode']="200";
              echo json_encode($curlResponse);

	die(); // this is required to return a proper result
    }catch(Exception $e){

    }
}
}
}catch(Exception $e){

}
if($_POST['start_date'] || $_POST['end_date'])
{
    $export = new AmplifyLoginNew;
    $data = $export->exportdata($_POST['start_date'] , $_POST['end_date']);
}

function wpeactivation_check() {
   
	if(is_plugin_active('woocommerce-betaout/index.php')){
            
               add_action( 'admin_notices', 'betaout_admin_notice1');
               
               deactivate_plugins( plugin_basename( __FILE__ ) );
	       wp_die( 'Please deactive Woocommerce betaout Plugin' );
            
              }
              
       if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
		$mu_blogs = wp_get_sites();

		foreach ( $mu_blogs as $mu_blog ) {
			switch_to_blog( $mu_blog['blog_id'] );

			betaout_api_register_rewrites();
			//update_option( 'json_api_plugin_version', null );
		}

		restore_current_blog();
	} else {
		betaout_api_register_rewrites();
		//update_option( 'json_api_plugin_version', null );
	}
}

register_activation_hook( __FILE__,'wpeactivation_check');



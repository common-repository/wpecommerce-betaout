<?php

/**
 * Compatibility shim for PHP <5.4
 *
 * @link http://php.net/jsonserializable
 *
 * @package WordPress
 * @subpackage JSON API
 */

if ( ! interface_exists( 'BetaoutSerializable' ) ) {
	define( 'WP_BETAOUT_SERIALIZE_COMPATIBLE', true );
	interface BetaoutSerializable {
		public function betaoutSerialize();
	}
}

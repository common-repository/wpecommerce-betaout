<?php

class WP_BETAOUT_WpecUsers {
	/**
	 * Server object
	 *
	 * @var WP_JSON_ResponseHandler
	 */
	protected $server;

	/**
	 * Constructor
	 *
	 * @param WP_JSON_ResponseHandler $server Server object
	 */
	public function __construct( WP_BETAOUT_ResponseHandler $server ) {
		$this->server = $server;
	}

	/**
	 * Register the user-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$user_routes = array(
			// User endpoints
			'/wpecusers' => array(
				array( array( $this, 'get_users' ),WP_BETAOUT_Server::READABLE ),
				
			),
			
		);
		return array_merge( $routes, $user_routes );
	}

	/**
	 * Retrieve users.
	 *
	 * @param array $filter Extra query parameters for {@see WP_User_Query}
	 * @param string $context optional
	 * @param int $page Page number (1-indexed)
	 * @return array contains a collection of User entities.
	 */
	public function get_users( $filter = array(), $context = 'view', $page = 1 ) {
		$args = array(
			'orderby' => 'user_login',
			'order'   => 'ASC'
		);
		$args = array_merge( $args, $filter );

		$args = apply_filters( 'json_user_query', $args, $filter, $context, $page );

		// Pagination
		$args['number'] = empty( $args['number'] ) ? 10 : absint( $args['number'] );
		$page           = absint( $page );
		$args['offset'] = ( $page - 1 ) * $args['number'];

		$user_query = new WP_User_Query( $args );

		if ( empty( $user_query->results ) ) {
			return array();
		}

		$struct = array();

		foreach ( $user_query->results as $user ) {
			$struct[] = $this->prepare_user( $user, $context );
		}

		return $struct;
	}

	
	
	protected function prepare_user( $user, $context = 'view' ) {
           
		$user_fields = array(
			'ID'          => $user->ID,
			'username'    => $user->user_login,
                        'email'       => $user->user_email,
			'name'        => $user->display_name,
			'first_name'  => $user->first_name,
			'last_name'   => $user->last_name,
			'nickname'    => $user->nickname,
			'slug'        => $user->user_nicename,
			'URL'         => $user->user_url,
			'avatar'      => "",//json_get_avatar_url( $user->user_email ),
			'description' => $user->description,
		);

		

//		$user_fields['meta'] = array(
//			'links' => array(
//				'self' => json_url( '/users/' . $user->ID ),
//				'archives' => json_url( '/users/' . $user->ID . '/posts' ),
//			),
//		);

		return apply_filters( 'json_prepare_user', $user_fields, $user, $context );
	}

	

	
	
	
	
}

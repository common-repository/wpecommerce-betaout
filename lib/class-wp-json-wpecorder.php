<?php

class WP_BETAOUT_WpecOrder {
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
	public function __construct(WP_BETAOUT_ResponseHandler $server) {
		$this->server = $server;
	}

	/**
	 * Register the post-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$post_routes = array(
			// Post endpoints
			'/wpecorder' => array(
				array( array( $this, 'get_posts' ),WP_BETAOUT_Server::READABLE ),
			),
                   );
		return array_merge( $routes, $post_routes );
	}

	
	/**
	 * Retrieve posts.
	 *
	 * @since 3.4.0
	 *
	 * The optional $filter parameter modifies the query used to retrieve posts.
	 * Accepted keys are 'post_type', 'post_status', 'number', 'offset',
	 * 'orderby', and 'order'.
	 *
	 * The optional $fields parameter specifies what fields will be included
	 * in the response array.
	 *
	 * @uses wp_get_recent_posts()
	 * @see WP_JSON_Posts::get_post() for more on $fields
	 * @see get_posts() for more on $filter values
	 *
	 * @param array $filter Parameters to pass through to `WP_Query`
	 * @param string $context
	 * @param string|array $type Post type slug, or array of slugs
	 * @param int $page Page number (1-indexed)
	 * @return stdClass[] Collection of Post entities
	 */
	public function get_posts( $filter = array(), $context = 'view', $type = 'post', $page = 1 ) {
	
          global $wpdb;
         
        $perpage=isset($filter['per_page'])?$filter['per_page']:0;
        $reultdata=array();
        $query="SELECT * FROM " . WPSC_TABLE_PURCHASE_LOGS." ORDER BY id ASC  ";
        if($perpage){
        $offset = ( $page - 1 ) * $perpage;
	$limit = "LIMIT " . absint( $perpage ) . " OFFSET " . absint( $offset );
        }
        $query=$query.$limit;
     
        $orderObj=$wpdb->get_results($wpdb->prepare($query ));
       $i=0;
       foreach($orderObj as $order){
        $reultdata[$i]['order']=$order;
        $id=$order->id;
        $orderdata=new WPSC_Purchase_Log($id);
        $data=$orderdata->get_cart_contents();
        $reultdata[$i]['items']=$data;
        $i++;
        }
       $response   = new WP_BETAOUT_Response();
       $response->set_data($reultdata);
       
        return $response;
	}
}

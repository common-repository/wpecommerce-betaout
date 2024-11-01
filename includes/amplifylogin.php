<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set("display_errors",1);
if (!class_exists('AmplifyLoginNew')) {
class AmplifyLoginNew{

    public $amplifyObj;

    public static function addFiles() {

        wp_localize_script('amplify_magic', 'personaL10n', array(
            'plugin_url' => plugins_url('wpecommerce-betaout'),
            'ajax_url' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
        ));
    }

    public static function adminStyle() {
        $src = plugins_url('css/common.css', dirname(__FILE__));
        wp_register_style('custom_wp_admin_css', $src);
        wp_enqueue_style('custom_wp_admin_css');
    }

    
   public function wp_amplify_footer() {
       
 $user = wp_get_current_user();
//  print_r($user);
            $userEmail = "";
            $userId='';
            if (is_user_logged_in() && $user) {
                $userEmail = $user->user_email;
                $userId=$user->ID;
            }
//            echo "<script type='text/javascript'>
//                _bout.push(['identify', {
//                 'email':'$userEmail',
//                 'customer_id': '$userId'
//                }])
//        </script>";

    }

    public function wp_amplify_header() {
$user = wp_get_current_user();

            $userEmail = "";
            $userId='';
            if (is_user_logged_in() && $user) {
                $userEmail = $user->user_email;
                $userId=$user->ID;
            }
        
        
 echo '<script type="text/javascript">
  var _bout = _bout || [];
  var _boutAKEY = "'.get_option('_AMPLIFY_API_KEY').'", _boutPID = "'.get_option('_AMPLIFY_PROJECT_ID').'"; 
 
</script>';

                
        $user = wp_get_current_user();
        $u_email=$u_id=$u_phone="";
        if (is_user_logged_in() && $user) 
        {
          $u_email=$user->user_email;
          $u_id= $user->ID;
           
            $get_user_meta=get_user_meta($user->ID);
            $visitor = $get_user_meta['_wpsc_visitor_id'];
            $visitor_data = wpsc_get_visitor_meta($visitor[0]);

            if($visitor_data['billingphone'][0] != '')
            {
              $u_phone=$visitor_data['billingphone'][0];
//                $user_data = array_merge($user_data,array('phone' => $visitor_data['billingphone'][0]));
            }
            
        }
         $user_data = array(
                'email' => $u_email,
                'customer_id' => $u_id,
                'phone' => $u_phone,
                'betaoutAPI' => get_option('_AMPLIFY_API_KEY'),
                'betaoutPID' => get_option('_AMPLIFY_PROJECT_ID')
            );
            
        wp_register_script('amplify_head', plugins_url('wpecommerce-betaout/js/amplify.js'), array('jquery'));
        wp_enqueue_script('amplify_head');
        wp_localize_script( 'amplify_head', 'user_data', $user_data );
        
        global $wpsc_cart;
        $cartob = $wpsc_cart->cart_items;
        
        $pid = array();
        if(!empty($cartob))
        {
            foreach ($cartob as $value)
            {
                array_push($pid,$value->product_id);
            }
        }
        setcookie('pid',serialize($pid));
    }

    public function amplifyinit() {
     try{
         session_start();
          $currentPage=$_SERVER['REQUEST_URI']; 
         $_SESSION['currentPage'];
         if(!isset($_SESSION['currentPage'])){
          $_SESSION['currentPage'] = $currentPage;
          $_SESSION['sendviewed'] = true;
         }else if($_SESSION['currentPage'] != $currentPage){
           $_SESSION['currentPage'] = $currentPage;
           $_SESSION['sendviewed'] = true;
         }else{
           $_SESSION['currentPage']=false  ;
           $_SESSION['sendviewed'] = false;
         }
        $amplifyKey = get_option('_AMPLIFY_API_KEY', false);
        
        add_action('wp_enqueue_scripts', array('AmplifyLoginNew', 'addFiles'));
        add_action('admin_menu', array('AmplifyLoginNew', 'amplifyMenu'));
        add_action('wp_footer', array('AmplifyLoginNew', 'wp_amplify_footer'));
        add_action('wp_head', array('AmplifyLoginNew', 'wp_amplify_header'), 1);
        add_action('login_enqueue_scripts', array('AmplifyLoginNew', 'wp_amplify_header'), 1);

        if ( class_exists( 'WP_eCommerce' ) ) {
         
          if(!empty($amplifyKey)){
              
            add_action('init', array('AmplifyLoginNew', 'amplify_track_from_post'), 25);
            
            add_action('wp_login', array('AmplifyLoginNew', 'amplify_signed_in'));
            add_action('user_register', array('AmplifyLoginNew', 'amplify_signed_up'));
            add_action('wpsc_product_addon_after_descr', array('AmplifyLoginNew', 'amplify_viewed_product'));
            add_action('wpsc_add_item', array('AmplifyLoginNew', 'amplify_added_to_cart'));
            add_action('wpsc_edit_item',array('AmplifyLoginNew', 'amplify_update_cart'));
            add_action('wpsc_remove_item', array('AmplifyLoginNew', 'amplify_remove_from_cart'));
            add_action('wpsc_submit_checkout', array('AmplifyLoginNew', 'amplify_completed_purchase'), 10, 1);

        }
      }
        
     }catch(Exception $e){

     }
    }

    public static function amplifyMenu() {
       add_menu_page('Betaout Wp-e-commerce', 'Betaout Wp-e-commerce', 'manage_options', 'wpecommerce-betaout', 'AmplifyLoginNew::amplify', plugins_url('images/icon.png', dirname(__FILE__)));
       add_submenu_page ( 'wpecommerce-betaout', 'Betaout Export CSV', 'Betaout Export CSV', 'manage_options', 'betaout-export','AmplifyLoginNew::exportcsv' );
       
    }
    public function amplify_signed_up($user_id) {
        try{
            $amplifyObj = new Amplify();
            $data = get_userdata( $user_id );
            $user['email'] = $data->user_email;
            $user['customer_id'] = $user_id;
            
            $result = $amplifyObj->identify($user);
        }catch(Exception $e){

        }
    }

    public function amplify_signed_in($user_login) {
        try{
            
        $amplifyObj = new Amplify();
        $data = get_userdatabylogin($user_login);
        
        $user['email'] = $data->user_email;
        $user['customer_id'] = $data->ID;
        $get_user_meta=get_user_meta($data->ID);
        $visitor = $get_user_meta['_wpsc_visitor_id'];
        
        if(!empty($visitor))
        {
            $visitor_data = wpsc_get_visitor_meta($visitor[0]);
            
            if($visitor_data['billingphone'][0] != '' && !empty($visitor_data['billingphone'][0]))
            {
                $user['phone'] = $visitor_data['billingphone'][0];
            }
        }
        
        try {
             $result = $amplifyObj->identify($user);

           } catch (Exception $ex) {

           }
        }catch(Exception $e){
            
        }
         
    }

    public function amplify_viewed_product($product_id) {
        try{
        
        if ($product_id != null) {
            $ampObj=new AmplifyLoginNew();
           
            $product = new WPSC_Product($product_id);
            
            $productarray=array();
            $productarray['name'] = $product->post->post_title;
            $productarray['id'] = $product_id;
            $productarray['image_url'] = wpsc_the_product_image('','',$product_id);
            $productarray['price'] = $product->sale_price ? $product->sale_price : $product->price;
            $productarray['sku'] = wpsc_product_sku($product_id) ? wpsc_product_sku($product_id) : $product_id;
            $productarray['categories'] = $ampObj->wcbetaout_categories($product_id);
            
            $parray = array(
                'products' => array($productarray),
                'activity_type' => 'view',
            );
            //print_r($parray);die();
            $amplifyObj = new Amplify();
            $result = $amplifyObj->customer_action($parray);
       }
        }catch(Exception $e){
           
        }
        $cat=$ampObj->wcbetaout_categories($product_id);
        
        $user = wp_get_current_user();
        $u_email=$u_id=$u_phone=$cat_id=$p_id="";
        if (is_user_logged_in() && $user) 
        {
          $u_email=$user->user_email;
          $u_id= $user->ID;
          $p_id= $product_id;
          $cat_id= $cat[0]['cat_id'];
            $get_user_meta=get_user_meta($user->ID);
            $visitor = $get_user_meta['_wpsc_visitor_id'];
            $visitor_data = wpsc_get_visitor_meta($visitor[0]);

            if($visitor_data['billingphone'][0] != '')
            {
              $u_phone=$visitor_data['billingphone'][0];
//                $user_data = array_merge($user_data,array('phone' => $visitor_data['billingphone'][0]));
            }
            
        }
         $user_data = array(
                'email' => $u_email,
                'customer_id' => $u_id,
                'phone' => $u_phone,
                'p_id' =>$p_id,
                'cat_id' =>$cat_id,
                'betaoutAPI' => get_option('_AMPLIFY_API_KEY'),
                'betaoutPID' => get_option('_AMPLIFY_PROJECT_ID')
            );
            
        wp_register_script('amplify_head_view', plugins_url('wpecommerce-betaout/js/amplify.js'), array('jquery'));
        wp_enqueue_script('amplify_head_view');
        wp_localize_script( 'amplify_head_view', 'user_data', $user_data );
    }

    public function amplify_added_to_cart($product_id) {
       try{
            
            $ampObj=new AmplifyLoginNew();
            $product = new WPSC_Product($product_id);

            $productarray = array();
            $productarray['name'] = $product->post->post_title;
            $productarray['id'] = $product_id;
            $productarray['quantity'] = $_REQUEST['wpsc_quantity_update'];
            $productarray['image_url'] = wpsc_the_product_image('','',$product_id);
            $productarray['price'] = $product->sale_price ? $product->sale_price : $product->price;
            $productarray['sku'] = wpsc_product_sku($product_id) ? wpsc_product_sku($product_id) : $product_id;
            $productarray['categories'] = $ampObj->wcbetaout_categories($product_id);
            
            global $wpsc_cart;
            foreach($wpsc_cart->cart_items as $cart_item){
                $subtotal += $cart_item->total_price;
            }
            $cart_info = array();
            $cart_info['total'] = wpsc_cart_total(false);
            $cart_info['revenue'] = $subtotal;
            $cart_info['currency'] = wpsc_get_currency_code();
            
            $parray = array(
                'products' => array($productarray),
                'cart_info' => $cart_info,
                'activity_type' => 'add_to_cart',
            );
            //print_r($wpsc_cart->cart_items);die();
            $amplifyObj = new Amplify();
            $result = $amplifyObj->customer_action($parray);
       }catch(Exception $e){

       }
    }

    public function amplify_update_cart($product_id){
       try{
            $ampObj=new AmplifyLoginNew();
            global $wpsc_cart;
            foreach($wpsc_cart->cart_items as $cart_item){
                $subtotal += $cart_item->total_price;
            }
            $cartob = $wpsc_cart->cart_items;
            $productarray = array();
            foreach ($cartob as $value)
            {
                $i = $value->product_id;
                $product = new WPSC_Product($i);
                $productarray[$i]['name'] = $value->product_name;
                $productarray[$i]['id'] = $i;
                $productarray[$i]['image_url'] = wpsc_the_product_image('','',$i);
                $productarray[$i]['quantity'] = $value->quantity;
                $productarray[$i]['price'] = $product->sale_price ? $product->sale_price : $product->price;
                $productarray[$i]['sku'] = wpsc_product_sku($i) ? wpsc_product_sku($i) : $i;
                $productarray[$i]['categories'] = $ampObj->wcbetaout_categories($i);
            }
            
            global $wpsc_cart;
            $cart_info = array();
            $cart_info['total'] = wpsc_cart_total(false);
            $cart_info['revenue'] = $subtotal;
            $cart_info['currency'] = wpsc_get_currency_code();
            
            $amplifyObj = new Amplify();

            $parray = array(
                'products' => $productarray,
                'cart_info' => $cart_info,
                'activity_type' => 'update_cart',
            );
            //print_r($parray);die();
            $result = $amplifyObj->customer_action($parray);

        }catch(Exception $e){

        }
    }

    public function amplify_remove_from_cart($cart_item) {
        try{
            $ampObj=new AmplifyLoginNew();
            $previous = unserialize(filter_input(INPUT_COOKIE, 'pid'));
            
            global $wpsc_cart;
            $cartob = $wpsc_cart->cart_items;
            $pid_new = array();
            foreach ($cartob as $value)
            {
                array_push($pid_new,$value->product_id);
            }

            $delpro = array_diff($previous, $pid_new);
            foreach ($delpro as $value)
            {
                $product = new WPSC_Product($value);

                $productarray = array();
                $productarray['name'] = $product->post->post_title;
                $productarray['id'] = $value;
                $productarray['image_url'] = wpsc_the_product_image('','',$value);
                $productarray['price'] = $product->sale_price ? $product->sale_price : $product->price;
                $productarray['sku'] = wpsc_product_sku($value) ? wpsc_product_sku($value) : $value;
                $productarray['categories'] = $ampObj->wcbetaout_categories($value);
            }
            
            $cart_info = array();
            $cart_info['total'] = $wpsc_cart->total_price;
            $cart_info['revenue'] = $wpsc_cart->subtotal;
            $cart_info['currency'] = wpsc_get_currency_code();
            
            $parray = array(
                'products' => array($productarray),
                'cart_info' => $cart_info,
                'activity_type' => 'remove_from_cart',
            );
            $amplifyObj = new Amplify();
            $result = $amplifyObj->customer_action($parray);
        
        }catch(Exception $e){

        }
        
    }
    
    public function amplify_completed_purchase($params)
    {
        session_destroy();
        $ampObj=new AmplifyLoginNew();
        $purchase_ob = new WPSC_Purchase_Log($params['purchase_log_id']);
        $gateway = $purchase_ob->get_meta();
        $processed = $purchase_ob->get_data();
        $p_id = $processed['processed'];
        
        global $wpsc_cart;
        
        $cartob = $wpsc_cart->cart_items;
        $checkout_ob = new WPSC_Checkout_Form_Data($params['purchase_log_id']);
        $checkout_data = $checkout_ob->get_data();
        
        $user = array(
            'email' => $checkout_data['billingemail'],
            'phone' => $checkout_data['billingphone'] ? $checkout_data['billingphone'] : '',
        );
        
        if($params['our_user_id'] != '0')
        {
            $user = array_merge($user,array('customer_id' => $params['our_user_id']));
        }
        //print_r($user);die();
        $_SESSION['ampUser'] = $user;
        
        $amplifyObj = new Amplify();
        $amplifyObj->identify($user);
        
        $productarray = array();
        foreach ($cartob as $value)
        {
            $product = new WPSC_Product($value->product_id);
            $i = $value->product_id;
            $productarray[$i]['name'] = $value->product_name;
            $productarray[$i]['id'] = $i;
            $productarray[$i]['image_url'] = wpsc_the_product_image('','',$i);
            $productarray[$i]['quantity'] = $value->quantity;
            $productarray[$i]['price'] = $product->sale_price ? $product->sale_price : $product->price;
            $productarray[$i]['sku'] = wpsc_product_sku($i) ? wpsc_product_sku($i) : $i;
            $productarray[$i]['categories'] = $ampObj->wcbetaout_categories($i);
        }

        $order_info = array();
        $order_info['total'] = $wpsc_cart->total_price;
        $order_info['revenue'] = $wpsc_cart->subtotal;
        $order_info['order_id'] = $params['purchase_log_id'];
        $order_info['status'] = wpsc_find_purchlog_status_name($p_id);
        $order_info['payment_method'] = $gateway['gateway_name'];
        $order_info['currency'] = wpsc_get_currency_code();
        
        $parray = array(
            'products' => $productarray,
            //'cart_info' => $cart_info,
            'activity_type' => 'purchase',
            'order_info' => $order_info
        );
        
        if($params['our_user_id'] == '0')
        {
            $event = $amplifyObj->event(array('events' => array(array('name' => 'purchased_as_guest_user'))));
        
        }
        $result = $amplifyObj->customer_action($parray);
    }
    
    public function wcbetaout_categories($product_id){
        
        $terms = get_the_terms($product_id, 'wpsc_product_category' );
        
        if ( $terms && ! is_wp_error( $terms ) ) : 
           $i=0;
            $cat_links = array();
            foreach ( $terms as $term ) {
                $cat_links[$i] = array(
                         "cat_name" => $term->name,
                         "parent_cat_id" => $term->parent,
                         "cat_id" => $term->term_id
                    );
                $i++;
           }
           
           return $cat_links;
        endif;
    }

    /**
     * Track events from $_POST
     */
    public function amplify_track_from_post() {
        try{
        // Applied Coupon
        if (!empty($_POST['coupon_num']) AND !empty($_POST['coupon_num'])) {
            $coupon = new wpsc_coupons($_POST['coupon_num']);
           
            if ($coupon->validate_coupon()) {
                $amplifyObj = new Amplify();
                $result = $amplifyObj->event(array('events' => array('name'=>'coupon_success')));
            }else{
                 
                $amplifyObj = new Amplify();
                $result = $amplifyObj->event(array('events' => array('name'=>'coupon_unsuccess')));
            }
             die();
        }
        if (isset($_GET['cancel_order']) AND isset($_GET['order']) AND isset($_GET['order_id'])) {
             $amplifyObj = new Amplify();
             $result = $amplifyObj->event(array('events' => array('name'=>'cancelled_order')));
        }
        
        }catch(Exception $e){

        }
        
    }

    public static function amplify() {
        try {

            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changekey') {
                require_once('html/configuration.php');
            } else {
                $amplifyApiKey = get_option("_AMPLIFY_API_KEY");
                $amplifyProjectId = get_option("_AMPLIFY_PROJECT_ID");
                
                $wordpressVersion = get_bloginfo('version');

                if (!empty($amplifyApiKey) && !empty($amplifyProjectId)) {
                    $parameters = array('wordpressVersion' => $wordpressVersion, 'wordpressBoPluginUrl' => $wordpressBoPluginUrl);
                    try {

                        $AMPLIFYSDKObj = new Amplify($amplifyApiKey, $amplifyProjectId, $debug);
                
                    } catch (Exception $ex) {
                        $curlResponse = '{ "error": "' . $ex->getMessage() . '", "responseCode": 500 }';
                        $curlResponse = json_decode($result);
                    }
                    $curlResponse = $curlResponse;
                }

                require_once('html/amplify.php');
            }
        } catch (Exception $ex) {

        }
    }

    public function exportcsv()
    {
        
        ?>
            <form method="post" action="">
            <input type="date" id="start_date" name="start_date">
            <input type="date" id="end_date" name="end_date">
            <input type="submit">
            </form>
        <?php
        
        
    }
    public function exportdata($start='', $end='')
    {
        
        $file = fopen('php://output', 'w');
        $fields = array(
            'orderId',
            'subtotalPrice',
            'totalPrice',
            'email',
            'phone',
            'customer_id',
            'product_id',
            'product_title',
            'product_quantity',
            'product_price'
        );
        
        fputcsv($file, $fields);
        
        global $wpdb, $wpsc_gateways;
        $o =new WPSC_Purchase_Log();
        print_r($o);die();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        exit();
    }
}
}
?>

<?php
namespace FrontEnd;

( defined( 'ABSPATH' ) ) || exit;

if ( ! class_exists( 'Overshield_Public' ) ) {
    class Overshield_Public{
        function __construct(){
            add_action('rest_api_init',[$this,'overshield_rest_api_init']);
            add_action('rest_api_init',[$this,'overshield_rest_api_customer_create']);
            add_action('rest_api_init',[$this,'overshield_rest_api_customer_purchased_plan']);
            add_action('rest_api_init',[$this,'overshield_rest_api_customer_used_plan_amount']);
        }
        function overshield_rest_api_customer_purchased_plan(){
            register_rest_route('os/v1','/customer-plan-details',[
                'methods'   =>  'POST',
                'callback'  =>  [$this,'os_customer_plan_details'],
            ]);
        }
        function overshield_rest_api_customer_create(){
            register_rest_route('os/v1','/customer-create',[
                'methods'   =>  'POST',
                'callback'  =>  [$this,'os_customer_create'],
            ]);
        }
        function overshield_rest_api_init(){
            register_rest_route('os/v1','/customers',[
                'methods'   =>  'POST',
                'callback'  =>  [$this,'os_plan_expiry_date'],
            ]);
        }
        function overshield_rest_api_customer_used_plan_amount(){
            register_rest_route('os/v1','/customer-purchased-amount',[
                'methods'   =>  'GET',
                'callback'  =>  [$this,'os_customer_used_plan_amount'],
            ]);
        }
        function os_customer_create(\WP_REST_Request $request){
            return 'customer-create working';
        }
        function os_plan_expiry_date( \WP_REST_Request $request ){
            $user_id = $request->get_param('user_id');
            // return $user_id;
            $memberships = wc_memberships_get_user_memberships($user_id);
            if ( $memberships ) {
                foreach( $memberships as $membership ) {
                  // Print the expiration date in mysql format.
                  $plan_id  =   $membership->plan_id;
                  $get_plan_products    =   get_post_meta($plan_id,'_product_ids',true);
                  $product = wc_get_product( $get_plan_products[0] );
                //   return $product->get_price();https://os-woo.masology.net/wp-json/os/v1/customer-create
                  return $membership->get_end_date();
                }
              }
            return 'membership expired';
        }
        function os_customer_plan_details( \WP_REST_Request $request ){
            $user_id = $request->get_param('user_id');
            // return $user_id;
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $user_id,
                'post_type'   => 'shop_order', // WC orders post type
                'post_status' => 'wc-completed' // Only orders with status "completed"
            ) );
            foreach ( $customer_orders as $customer_order ) {
                // Updated compatibility with WooCommerce 3+customer-create
                $order = wc_get_order( $customer_order );
                $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        
                // Iterating through each current customer products bought in the order
                foreach ($order->get_items() as $item) {
                    $product_id[] = $item->get_product_id();
                    $product = wc_get_product( $item->get_product_id() );
                    $product_price    =   $product->get_price();
                }
            }
            return $product_price;
        }
        function os_customer_used_plan_amount( \WP_REST_Request $request ){
            $user_id = $request->get_param('user_id');
            $pro_price = $request->get_param('pro_price');
            if( ! $user_id ){
                return 'user id is required!';
            }
            if( ! $pro_price ){
                return 'Product price is required!';
            }
            $memberships = wc_memberships_get_user_memberships($user_id);
            if ( $memberships ) {
                foreach( $memberships as $membership ) {
                  // Print the expiration date in mysql format.
                  $plan_id  =   $membership->plan_id;
                  $get_plan_previous_amount =   get_post_meta($plan_id,'plan_used_amount',true);
                  if($get_plan_previous_amount){
                      $plan_used_price  =   $get_plan_previous_amount + $pro_price;
                  }else{
                      $plan_used_price  =   $pro_price;
                  }
                  $udpate_plan_price    =   update_post_meta($plan_id,'plan_used_amount',$plan_used_price);
                  if( ! $udpate_plan_price ){
                    return 'plan amount could not be updated!';
                  }
                  $get_plan_products    =   get_post_meta($plan_id,'_product_ids',true);
                  $product = wc_get_product( $get_plan_products[0] );
                  return 'plan used price updated!';
                //   return $membership->get_end_date();
                }
            }
            return 'user has not purchased any plan';
            // return $user_id;
            // $customer_orders = get_posts( array(
            //     'numberposts' => -1,
            //     'meta_key'    => '_customer_user',
            //     'meta_value'  => $user_id,
            //     'post_type'   => 'shop_order', // WC orders post type
            //     'post_status' => 'wc-completed' // Only orders with status "completed"
            // ) );
            // foreach ( $customer_orders as $customer_order ) {
            //     // Updated compatibility with WooCommerce 3+
            //     $order = wc_get_order( $customer_order );
            //     $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        
            //     // Iterating through each current customer products bought in the order
            //     foreach ($order->get_items() as $item) {
            //         $product_id[] = $item->get_product_id();
            //         $product = wc_get_product( $item->get_product_id() );
            //         $product_price    =   $product->get_price();
            //     }
            // }
            // return $product_price;
        }
        
    }
}
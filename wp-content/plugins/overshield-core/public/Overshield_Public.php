<?php
namespace FrontEnd;

( defined( 'ABSPATH' ) ) || exit;

if ( ! class_exists( 'Overshield_Public' ) ) {
    class Overshield_Public{
        function __construct(){
            add_action('rest_api_init',[$this,'overshield_rest_api_init']);
        }
        function overshield_rest_api_init(){
            register_rest_route('os/v1','/customers',[
                'methods'   =>  'POST',
                'callback'  =>  [$this,'os_posts'],
            ]);
        }
        function os_posts(){
            return 'API working';
        }
    }
}
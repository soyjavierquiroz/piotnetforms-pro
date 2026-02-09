<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_ajax_piotnetforms_razorpay_get_plan', 'piotnetforms_razorpay_get_plan' );
add_action( 'wp_ajax_nopriv_piotnetforms_razorpay_get_plan', 'piotnetforms_razorpay_get_plan' );

function piotnetforms_razorpay_get_plan(){
    if(isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'piotnetforms_pro_editor_nonce' )){
        $key = get_option( 'piotnetforms-razorpay-key-id' );
        $secret = get_option('piotnetforms-razorpay-key-secret');
        $plan_url = 'https://api.razorpay.com/v1/plans';
        $args = [
            'method' => 'GET',
            'headers' => [
                'content-type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode( $key . ':' . $secret ),
            ],
        ];
        $response = wp_remote_get( $plan_url, $args );
        if ( !is_wp_error( $response ) && !empty($response['body']) ) {
            $body = json_decode( $response['body'], true );
            $plans = isset($body['items']) ? $body['items'] : [];
            $plan_html = '';
            if(!empty($plans)){
                $plan_html .= '<div class="pafe-razorpay-plans">';
                foreach ($plans as $plan) {
                    $plan_id = $plan['id'];
                    $plan_name = $plan['item']['name'];
                    $period = isset($plan['period']) ? '( ' . $plan['period'] . ' )' : '';
                    $plan_html .= '<div style="display:flex;align-items: center; margin-bottom: 5px;"><label style="width:90%;">'.$plan_name.' '.$period.'</label><input type="text" value="'.$plan_id.'" readonly/></div>';
                }
                $plan_html .= '</div>';
            }
            echo $plan_html;
        } else {
            echo '<div style="color:red;">An error occurred, please check again.</div>';
        }
    }else{
        echo "Nonce verification failed.";
    }
    wp_die();
}
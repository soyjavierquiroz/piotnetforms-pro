<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_ajax_piotnetforms_mailpoet_get_custom_fields', 'piotnetforms_mailpoet_get_custom_fields' );
add_action( 'wp_ajax_nopriv_piotnetforms_mailpoet_get_custom_fields', 'piotnetforms_mailpoet_get_custom_fields' );
function piotnetforms_mailpoet_get_custom_fields() {
    if(isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'piotnetforms_pro_preview_nonce' )){
        if ( class_exists( \MailPoet\API\API::class ) ) {
            $mailpoet_api = \MailPoet\API\API::MP( 'v1' );
            $fields = $mailpoet_api->getSubscriberFields();
            $html = '';
            foreach ( $fields as $field ) {
                $html .= '<div class="piotnet-mailpoet-custom-field__inner"><label>'.$field['name'].'</label><div class="piotnet-mailpoet-custom-field__id"><input type="text" value="'.$field['id'].'" readonly></div></div>';
            }
            echo $html;
        } else {
            echo 'You have not installed the Mailpoet plugin';
        }
    }else{
        echo "Nonce verification failed.";
    }
	wp_die();
}

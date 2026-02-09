<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_ajax_piotnetforms_twilio_sendgrid_get_field', 'piotnetforms_twilio_sendgrid_get_field' );
add_action( 'wp_ajax_nopriv_piotnetforms_twilio_sendgrid_get_field', 'piotnetforms_twilio_sendgrid_get_field' );

function piotnetforms_twilio_sendgrid_get_field() {
    if(isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'piotnetforms_pro_editor_nonce' )){
        $url = 'https://api.sendgrid.com/v3/marketing/field_definitions';
        $api_key = $_REQUEST['api'];
        $args = [
            'method' => 'GET',
            'sslverify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
        ];
        $response = wp_remote_request($url, $args);
        $res_data = json_decode(wp_remote_retrieve_body($response));
        $custom_fields = $res_data->custom_fields;
        $reserved_fields = $res_data->reserved_fields;
        $html = '<div class="piotnetforms-sendgrid-fields">';
        if(!empty($reserved_fields)){
            $html .= '<hr><div class="piotnetforms-sendgrid-reserved-field"><div class="piotnetforms-sendgrid-reserved-field__title">Reserved Fields</div>';
            foreach($reserved_fields as $field){
                $html .= '<div class="piotnetforms-sendgrid-reserved-field__item"><label class="piotnetforms-sendgrid-reserved-field__label">'.ucfirst(str_replace('_', ' ', strtolower($field->name))).'</label><div class="piotnetforms-sendgrid-reserved-field__name"><input type="text" value="'.$field->name.'" readonly></div></div>';
            }
            $html .= '</div>';
        }
        if(!empty($custom_fields)){
            $html .= '<hr><div class="piotnetforms-sendgrid-reserved-field"><div class="piotnetforms-sendgrid-reserved-field__title">Custom Fields</div>';
            foreach($custom_fields as $field){
                $html .= '<div class="piotnetforms-sendgrid-reserved-field__item"><label class="piotnetforms-sendgrid-reserved-field__label">'.ucfirst(str_replace('_', ' ', strtolower($field->name))).'</label><div class="piotnetforms-sendgrid-reserved-field__name"><input type="text" value="'.$field->name.'" readonly></div></div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';
        echo $html;
    }else{
        echo "Nonce verification failed.";
    }
    wp_die();
}

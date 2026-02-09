<?php
    if ( ! defined( 'ABSPATH' ) ) { exit; }
    
	add_action( 'wp_ajax_piotnetforms_delete_post', 'piotnetforms_delete_post' );
	add_action( 'wp_ajax_nopriv_piotnetforms_delete_post', 'piotnetforms_delete_post' );
	function piotnetforms_delete_post() {
        if(isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'piotnetforms_pro_advanced2_nonce' )){
            global $wpdb;
            if ( !empty( $_POST['id'] ) ) {
                $id = intval( $_POST['id'] );
                if (current_user_can( 'edit_others_posts', $_POST['id'] )) {
                    $force_delete = intval( $_POST['force_delete'] );

                    if ( $force_delete == 0 ) {
                        $force_delete = false;
                    }

                    if ( $force_delete == 1 ) {
                        $force_delete = true;
                    }

                    $delete_post = wp_delete_post( $id, $force_delete );
                    if ( $delete_post != false ) {
                        echo 1;
                    }
                }else{
                    die('You do not have permission to delete the post.');
                }
            }
        }else{
            echo "Nonce verification failed.";
        }
		wp_die();
	}

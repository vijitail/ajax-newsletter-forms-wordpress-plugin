<?php

/**
 * @package ANFPlugin
*/

include 'AdminNotice.php';

if(!class_exists('Serializer')) {

    class Serializer 
    {
        function __construct() {

            $this->db_update = new DBUpdate();
            $this->admin_notice = new AdminNotice();

            add_action( 'admin_post_nopriv_save_anf', array( $this, 'save_anf' ) );
            add_action( 'admin_post_save_anf', array( $this, 'save_anf') );

        }

        public function save_anf() {

            if ( ! ( $this->has_valid_nonce() && current_user_can( 'manage_options' ) ) ) {
                // TODO: Display an error message.
                echo "Error";
            }

            if ( null != wp_unslash( $_POST['formName'] ) ) {
                $value = sanitize_text_field( $_POST['formName'] );
                if($this->db_update->store_ANF($value))
                   $success = "success"; 
            }

            $this->redirect($success);

        }

        private function has_valid_nonce() {
 
            if ( ! isset( $_POST['anf-custom-message'] ) ) {
                return false;
            }
     
            $field  = wp_unslash( $_POST['anf-custom-message'] );
            $action = 'anf-form-save';
     
            return wp_verify_nonce( $field, $action );
     
        }

        private function redirect($notice_type="") {
 
            if ( ! isset( $_POST['_wp_http_referer'] ) ) { 
                $_POST['_wp_http_referer'] = wp_login_url();
            }
     
            $url = sanitize_text_field(
                    wp_unslash( $_POST['_wp_http_referer']."&updated=$notice_type" ) 
            );
     
            wp_safe_redirect( urldecode( $url ) );
            exit;
     
        }
    }

}
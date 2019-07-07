<?php

/**
 * @package ANFPlugin
*/

if(!class_exists('Serializer')) {

    class Serializer 
    {
        function __construct() {

            $this->db_update = new DBUpdate();

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
                $this->db_update->store_ANF($value);
            }

            $this->redirect();

        }

        private function has_valid_nonce() {
 
            if ( ! isset( $_POST['anf-custom-message'] ) ) {
                return false;
            }
     
            $field  = wp_unslash( $_POST['anf-custom-message'] );
            $action = 'anf-form-save';
     
            return wp_verify_nonce( $field, $action );
     
        }

        private function redirect() {
 
            if ( ! isset( $_POST['_wp_http_referer'] ) ) { 
                $_POST['_wp_http_referer'] = wp_login_url();
            }
     
            $url = sanitize_text_field(
                    wp_unslash( $_POST['_wp_http_referer'] ) 
            );
     
            wp_safe_redirect( urldecode( $url ) );
            exit;
     
        }
    }

}
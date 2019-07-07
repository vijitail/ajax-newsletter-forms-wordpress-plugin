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

            add_action( 'admin_post_nopriv_edit_anf', array( $this, 'edit_anf' ) );
            add_action( 'admin_post_edit_anf', array( $this, 'edit_anf') );
        }

        public function save_anf() {

            if ( ! ( $this->has_valid_nonce() && current_user_can( 'manage_options' ) ) ) {
                // TODO: Display an error message.
                wp_die("Error");
            }

            if ( null != wp_unslash( $_POST['formName'] && null != wp_unslash( $_POST['listNumber'] )) ) {
                $values = array(
                    'name' => sanitize_text_field($_POST['formName']),
                    'list_num' => sanitize_text_field($_POST['listNumber']),
                    'has_name_field' => isset($_POST['hasName']) && $_POST['hasName'] == 'on',
                    'onsuccess_jquery' => isset($_POST['onsuccessJQuery']) ? htmlspecialchars($_POST['onsuccessJQuery']) : '',
                    'onerror_jquery' => isset($_POST['onerrorJQuery']) ? htmlspecialchars($_POST['onerrorJQuery']) : '',
                );
                if($this->db_update->store_anf($values))
                   $success = "success"; 
            }

            $this->redirect($success, "save");

        }

        public function edit_anf() {

            if ( ! ( $this->has_valid_nonce() && current_user_can( 'manage_options' ) ) ) {
                // TODO: Display an error message.
                wp_die('Error');
            }

            if ( null != wp_unslash( $_POST['formName'] ) && null != wp_unslash( $_POST['formId'] ) ) {
                $values = array(
                    'name' => sanitize_text_field($_POST['formName']),
                    'list_num' => sanitize_text_field($_POST['listNumber']),
                    'has_name_field' => isset($_POST['hasName']) && $_POST['hasName'] == 'on',
                    'onsuccess_jquery' => isset($_POST['onsuccessJQuery']) ? htmlspecialchars($_POST['onsuccessJQuery']) : '',
                    'onerror_jquery' => isset($_POST['onerrorJQuery']) ? htmlspecialchars($_POST['onerrorJQuery']) : '',
                );
                $id = sanitize_text_field( $_POST['formId'] );
                if($this->db_update->update_anf($id, $values))
                   $success = "success"; 
            }

            $this->redirect($success, "edit");

        }

        private function has_valid_nonce() {
 
            if ( ! isset( $_POST['anf-custom-message'] ) ) {
                return false;
            }
     
            $field  = wp_unslash( $_POST['anf-custom-message'] );
            $action = 'anf-form-save';
     
            return wp_verify_nonce( $field, $action );
     
        }

        public function redirect($notice_type="", $action="") {
 
            if ( ! isset( $_POST['_wp_http_referer'] ) ) { 
                $_POST['_wp_http_referer'] = wp_login_url();
            }
     
            // $url = sanitize_text_field(
            //         wp_unslash( $_POST['_wp_http_referer']."&updated=$notice_type" ) 
            // );

            $url = admin_url("/admin.php?page=ajax_newsletter_forms&updated=$notice_type&action=$action");
     
            wp_safe_redirect( urldecode( $url ) );
            exit;
     
        }
    }

}
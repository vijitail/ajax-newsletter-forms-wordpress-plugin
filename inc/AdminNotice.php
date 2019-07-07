<?php

/**
 * @package ANFPlugin
*/

if(!class_exists('AdminNotice')) {

    class AdminNotice
    {
        function __construct() {
            add_action( 'admin_notices', array($this, 'display_form_save_message') );;
        }

        function display_form_save_message() {
            if(isset($_GET['updated'])) {
        ?>
            <div class="notice notice-success">
                <p><?php _e( 'Form saved successfully.', 'ajax-newsletter-forms' ); ?></p>
            </div>
        <?php
            }
        }
    }

}
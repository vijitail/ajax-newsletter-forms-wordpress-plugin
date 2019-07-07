<?php

/**
 * @package ANFPlugin
*/

if(!class_exists('AdminNotice')) {

    class AdminNotice
    {
        function __construct() {
            add_action( 'admin_notices', array($this, 'display_form_save_message') );
            add_action( 'admin_notices', array($this, 'display_form_delete_message') );
            add_action( 'admin_notices', array($this, 'display_form_bulk_delete_message') );
            add_action( 'admin_notices', array($this, 'display_form_edit_message') );
        }

        function display_form_save_message() {
            if(null !== $_GET['updated'] && null !== $_GET['action'] && $_GET['action'] == 'save') {
        ?>
            <div class="notice notice-success">
                <p><?php _e( 'Form saved successfully.', 'ajax-newsletter-forms' ); ?></p>
            </div>
        <?php
            }
        }

        function display_form_delete_message() {

        }

        function display_form_edit_message() {
            if(null !== $_GET['updated'] && null !== $_GET['action'] && $_GET['action'] == 'edit') {
        ?>
            <div class="notice notice-success">
                <p><?php _e( 'Form edited successfully.', 'ajax-newsletter-forms' ); ?></p>
            </div>
        <?php
            }

        }

        function display_form_bulk_delete_message() {
            if(null !== $_GET['updated'] && null !== $_GET['action'] && $_GET['action'] == 'delete') {
        ?>
            <div class="notice notice-success">
                <p><?php _e( 'Forms deleted successfully.', 'ajax-newsletter-forms' ); ?></p>
            </div>
        <?php
            }
        }
    }

}
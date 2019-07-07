<?php

/**
 * @package ANFPlugin
*/

include 'Serializer.php';

if(!class_exists('AdminController')) {

    class AdminController 
    {
        function register($plugin_name) {

            add_action( 'admin_menu', function() {
                $this->add_admin_menu();
            } );

            add_filter( "plugin_action_links_$plugin_name", function($links) {
                return $this->add_settings_link($links);
            } );

            add_action( 'admin_enqueue_scripts', function($hook) {
                $this->add_admin_assets($hook);
            } );

            $serializer = new Serializer();

        }

        private function add_admin_menu() {
            add_menu_page( 
                'Ajax Newsletter Form', 
                'Ajax NF', 
                'manage_options', 
                'ajax_newsletter_forms', 
                function() {
                    $this->admin_index();
                }, 
                'dashicons-feedback', 5 
            );
            add_submenu_page( 
                'ajax_newsletter_forms', 
                'All Forms', 
                'All Forms', 
                'manage_options', 
                'ajax_newsletter_forms');
            add_submenu_page( 
                'ajax_newsletter_forms', 
                'Create A New Ajax Form', 
                'New Form', 
                'manage_options', 
                'new_ajax_newsletter_form', 
                function() {
                    $this->admin_new_ajax_form(); 
                }
            );
        }

        private function add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=ajax_newsletter_forms">Settings</a>';
            array_push($links, $settings_link);
            return $links;
        }

        private function admin_index() {
            require_once plugin_dir_path( __FILE__ ).'../templates/admin.php';
            
        }

        private function admin_new_ajax_form() {
            require_once plugin_dir_path( __FILE__ ).'../templates/create-form.php';
        }

        private function add_admin_assets($hook) {
            if($hook != "ajax-nf_page_new_ajax_newsletter_form") {
                return;
            }
            $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/javascript'));
            wp_localize_script('jquery', 'cm_settings', $cm_settings);
            wp_enqueue_script('wp-theme-plugin-editor');
            wp_enqueue_style('wp-codemirror');
            wp_enqueue_style( 'anf_admin_css', plugins_url('../assets/admin/style.css', __FILE__) );
            wp_enqueue_script( 'anf_admin_script', plugins_url('../assets/admin/script.js', __FILE__) );
        }
    }

}
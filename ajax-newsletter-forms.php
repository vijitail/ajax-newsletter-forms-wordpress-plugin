<?php

/**
 * @package ANFPlugin
*/

/**
 * Plugin Name: Ajax Newsletter Forms
 * Plugin URI: https://vijitail.github.io/anf-plugin
 * Description: Ajax Form builder for Newsletter plugin
 * Version: 1.0.0 
 * Author: Vijit Ail
 * Author URI: https://vijitail.github.io
 * License: GPL2
 * Text Domain: ajax-newsletter-forms 
 * 
 * {Plugin Name} is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
 * {Plugin Name} is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with {Plugin Name}. If not, see {License URI}.
*/

if(!defined('ABSPATH')) {
    exit;
}

include 'inc/DBUpdate.php';

if(!class_exists('AjaxNewsletterForms')) {

    class AjaxNewsletterForms 
    {
        public $plugin_name;

        function __construct() {
            $this->plugin_name = plugin_basename( __FILE__ );
            $this->db_update = new DBUpdate();
        }

        function register() {
            require_once plugin_dir_path( __FILE__ ).'/inc/AdminController.php';
            if(class_exists('AdminController')) {
                $admin_controller = new AdminController();
                $admin_controller->register($this->plugin_name);
            }
            require_once plugin_dir_path( __FILE__ ).'/inc/FrontendController.php';
            if(class_exists('FrontendController')) {
                $frontend_controller = new FrontendController();
                $frontend_controller->register();
            }
        }

        function activate() {
            $this->db_update->create_ANF_Table();
            flush_rewrite_rules();
        }

        function deactivate() {
            $this->db_update->delete_ANF_Table();
            flush_rewrite_rules();
        }
        
    }

    if(class_exists('AjaxNewsletterForms')) {
        $ajaxNewsletterForms = new AjaxNewsletterForms();
        $ajaxNewsletterForms->register();
    }

    register_activation_hook( __FILE__, array( $ajaxNewsletterForms, 'activate' ) );

    register_deactivation_hook( __FILE__, array( $ajaxNewsletterForms, 'deactivate' ) );

}
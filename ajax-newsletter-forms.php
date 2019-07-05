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

class AjaxNewsletterForms 
{
    function __constructor() {

    }

    function activate() {

    }

    function deactivate() {

    }

    function uninstall() {

    }
}

if(class_exists('AjaxNewsletterForms')) {
    $ajaxNewsletterForms = new AjaxNewsletterForms();
}

register_activation_hook( __FILE__, array( $ajaxNewsletterForms, 'activate' ) );

register_deactivation_hook( __FILE__, array( $ajaxNewsletterForms, 'deactivate' ) );
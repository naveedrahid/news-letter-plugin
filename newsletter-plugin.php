<?php
/*
Plugin Name: Newsletter Plugin
Description: Plugin to manage newsletter subscribers use this shortcode [newsletter_form].
Version: 1.0
Author: Naveed
*/

if (!defined('ABSPATH')) {
    exit;
}

include 'includes/database.php';
include 'includes/admin/subscribers-list.php';
include 'includes/admin/setting.php';
include 'includes/form.php';

register_activation_hook(__FILE__, 'create_subscribers_table');
register_deactivation_hook(__FILE__, 'delete_subscribers_table');
<?php
if (!defined('ABSPATH')) {
    exit;
}

function subscribers_menu() {
    add_menu_page(
        'Subscribers List',
        'Subscribers',
        'manage_options',
        'subscribers-list',
        'display_subscribers_list'
    );

    add_submenu_page(
        'subscribers-list', // parent slug
        'Subscribers Settings', // page title
        'Settings', // menu title
        'manage_options', // capability
        'subscribers-settings', // menu slug
        'display_subscribers_settings' // callback function
    );
}
add_action('admin_menu', 'subscribers_menu');

function display_subscribers_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<h2>Subscribers List</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Name</th><th>Email</th></tr>';
    foreach ($subscribers as $subscriber) {
        echo "<tr><td>{$subscriber->id}</td><td>{$subscriber->name}</td><td>{$subscriber->email}</td></tr>";
    }
    echo '</table>';
}

function display_subscribers_settings() {
    ?>
    <div class="wrap">
        <h2>Subscribers Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('subscribers_settings_group');
            do_settings_sections('subscribers_settings_section');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

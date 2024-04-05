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

function export_all_subscribers_csv() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name");

    $csv_output = fopen('php://output', 'w');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    
    fputcsv($csv_output, array('ID', 'Email'));

    foreach ($subscribers as $subscriber) {
        fputcsv($csv_output, array($subscriber->id, $subscriber->email));
    }

    fclose($csv_output);
    exit;
}

add_action('admin_post_export_subscribers_csv', 'export_all_subscribers_csv');


function display_subscribers_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap">';
    echo '<a href="' . admin_url('admin-post.php?action=export_subscribers_csv') . '" class="button button-primary">Export CSV</a>';
    echo '</div>';
    echo '<div class="wrap">';
    echo '<h2>Subscribers List</h2>';
    echo '<table class="wp-list-table widefat striped">';
    echo '<thead><tr><th>ID</th><th>Email</th><th>Date</th></tr></thead>';
    echo '<tbody>';
    foreach ($subscribers as $subscriber) {
    $registration_date = date('d-m-Y', strtotime($subscriber->created_at));
    echo '<tr>';
    echo "<td>#{$subscriber->ID}</td>";
    echo "<td>{$subscriber->email}</td>";
    echo "<td>{$registration_date}</td>"; // Display registration date
    echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
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

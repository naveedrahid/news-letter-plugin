<?php
if (!defined('ABSPATH')) {
    exit;
}

function create_subscribers_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE `{$wpdb->base_prefix}subscribers` (
        ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        name varchar(50) NOT NULL,
        email varchar(100) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function delete_subscribers_table() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}subscribers");
}
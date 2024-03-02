<?php

function plugin_admin_init() {
    // Register a new setting for the submenu page
    register_setting('subscribers_settings_group', 'subscribers_settings', 'subscribers_settings_sanitize');

    // Add a section to the submenu page
    add_settings_section(
        'subscribers_section_id', // section_id
        'Subscribers Settings', // title
        'subscribers_section_callback', // callback
        'subscribers_settings_section' // parent slug
    );

    // Add fields to the section
    add_settings_field(
        'to_email', // field_id
        'To Email', // field title
        'to_email_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'email_host', // field_id
        'Email Host', // field title
        'subscribers_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'subs_username', // field_id
        'User Name', // field title
        'sub_username_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'subs_password', // field_id
        'Password', // field title
        'sub_password_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'subs_port', // field_id
        'Port', // field title
        'sub_port_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'subs_from_email', // field_id
        'From Email Address', // field title
        'subs_from_email_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );

    add_settings_field(
        'subs_subject', // field_id
        'Subject', // field title
        'subs_subject_field_callback', // field_callback
        'subscribers_settings_section', // parent slug
        'subscribers_section_id' 
    );
}
add_action('admin_init', 'plugin_admin_init');

function subscribers_settings_sanitize($settings) {
    $sanitized_settings = array();
    foreach ($settings as $key => $value) {
        // Sanitize each setting individually
        $sanitized_settings[$key] = sanitize_text_field($value);
    }
    return $sanitized_settings;
}

function subscribers_section_callback() {
    echo '<p>Configure settings for your subscribers plugin:</p>';
}

function to_email_field_callback() {
    $options = get_option('subscribers_settings');
    $to_email = isset($options['to_email']) ? $options['to_email'] : '';
    echo '<input type="email" name="subscribers_settings[to_email]" value="' . esc_attr($to_email) . '" />';
    echo '<p>Add admin email</p>';
}

function subscribers_field_callback() {
    $options = get_option('subscribers_settings');
    $email_host = isset($options['email_host']) ? $options['email_host'] : '';
    echo '<input type="text" name="subscribers_settings[email_host]" value="' . esc_attr($email_host) . '" />';
    echo '<p>The SMTP server which will be to send email.</p>';
}

function sub_username_field_callback() {
    $options = get_option('subscribers_settings');
    $sub_username = isset($options['subs_username']) ? $options['subs_username'] : '';
    echo '<input type="text" name="subscribers_settings[subs_username]" value="' . esc_attr($sub_username) . '" />';
    echo '<p>Your SMTP Username.</p>';
}

function sub_password_field_callback(){
    $options = get_option('subscribers_settings');
    $subs_password = isset($options['subs_password']) ? $options['subs_password'] : '';
    echo '<input type="password" name="subscribers_settings[subs_password]" value="'.esc_attr($subs_password).'" />';
    echo '<p>Your SMTP Password.</p>';
}

function sub_port_field_callback(){
    $options = get_option('subscribers_settings');
    $subs_port = isset($options['subs_port']) ? $options['subs_port'] : '';
    echo '<input type="number" name="subscribers_settings[subs_port]" value="'.esc_attr($subs_port).'" />';
    echo '<p>The Port which will be used sending an email (587/465/25).</p>';
}

function subs_from_email_field_callback(){
    $options = get_option('subscribers_settings');
    $subs_from_email = isset($options['subs_from_email']) ? $options['subs_from_email'] : '';
    echo '<input type="email" name="subscribers_settings[subs_from_email]" value="'.esc_attr($subs_from_email).'" />';
    echo '<p>The email address which will be used as the From Address if it is not supplied to the email function.</p>';
}

function subs_subject_field_callback(){
    $options = get_option('subscribers_settings');
    $subs_subject = isset($options['subs_subject']) ? $options['subs_subject'] : '';
    echo '<input type="text" name="subscribers_settings[subs_subject]" value="'.esc_attr($subs_subject).'" />';
    echo '<p>Add Email Subject.</p>';
}















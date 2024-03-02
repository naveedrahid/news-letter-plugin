<?php

function newsletter_form_html() {
    ob_start(); ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="newsletter-form">
        <input type="hidden" name="action" value="handle_newsletter_form">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>
        <input type="submit" value="Subscribe" onclick="return validateForm()">
    </form>
    <div id="validation-message" style="display: none;"></div>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value;
            let email = document.getElementById('email').value;
            let errorMessage = '';

            if (name.trim() === '') {
                errorMessage += 'Name is required.\n';
            }
            if (email.trim() === '') {
                errorMessage += 'Email is required.\n';
            } else if (!validateEmail(email)) {
                errorMessage += 'Invalid email address.\n';
            }

            if (errorMessage !== '') {
                document.getElementById('validation-message').innerHTML = errorMessage;
                document.getElementById('validation-message').style.display = 'block';
                return false;
            } else {
                return true;
            }
        }

        function validateEmail(email) {
            let re = /\S+@\S+\.\S+/;
            return re.test(email);
        }
    </script>
    <?php
    return ob_get_clean();
}

function handle_newsletter_form() {
    if (isset($_POST['name']) && isset($_POST['email'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscribers';
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);

        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email
            )
        );
        $_POST['name'] = '';
        $_POST['email'] = '';

        add_action('phpmailer_init', function ($phpmailer) use ($email, $name) {
            $options = get_option('subscribers_settings');

            $phpmailer->isSMTP();
            $to = $options['to_email'];
            $phpmailer->Host = $options['email_host'];
            $phpmailer->Port = $options['subs_port'];
            $phpmailer->SMTPAuth = true;
            $phpmailer->SMTPSecure = 'ssl';
            $phpmailer->Username = $options['subs_username'];
            $phpmailer->Password = $options['subs_password'];
            $phpmailer->setFrom($options['subs_from_email'], $options['subs_subject']);
            $phpmailer->addAddress($to, $name);
            $phpmailer->Subject = 'Thank you for subscribing!';
            $phpmailer->Body = 'Dear ' . $name . ',<br><br>Thank you for subscribing to our.' . $options['subs_subject'];
            $phpmailer->isHTML(true);
        });

        $to_customer = $email;
        $subject_customer = 'Thank you for subscribing!';
        $message_customer = 'Dear ' . $name . ',<br><br>Thank you for subscribing to our.' . $options['subs_subject'];
        $headers_customer[] = 'Content-Type: text/html; charset=UTF-8';
        wp_mail($to_customer, $subject_customer, $message_customer, $headers_customer);

        $subject = 'Thank you for subscribing!';
        $message = 'Dear ' . $name . ',<br><br>Thank you for subscribing to our newsletter.';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        wp_mail($to, $subject, $message, $headers);
        
        // Add success message
        $success_message = 'Thank you for subscribing!';
        set_transient('newsletter_success_message', $success_message, 5);

        wp_redirect('/');
        exit();
    }
}

add_action('admin_post_nopriv_handle_newsletter_form', 'handle_newsletter_form');
add_action('admin_post_handle_newsletter_form', 'handle_newsletter_form');

function newsletter_form_shortcode() {
    return newsletter_form_html();
}
add_shortcode('newsletter_form', 'newsletter_form_shortcode');
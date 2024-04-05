<?php
function newsletter_form_html() {
    
    ob_start(); 
    
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="newsletter-form">
                <input type="hidden" name="action" value="handle_newsletter_form">
        <div class="newsletterWrapper">
            <div class="newsletterEmail">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="newsletterBtn">
                <input type="submit" value="Subscribe" onclick="return validateForm()">
            </div>
        </div>
    </form>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'subscribers';
    $subscribers = $wpdb->get_results("SELECT * FROM $table_name");
    $emailExist = [];
    foreach($subscribers as $subscriber){
       $emailExist[] =  $subscriber->email;
    }
    
    ?>
    <div id="validation-message" style="display: none;"></div>
    <script>
        function validateForm() {
            let email = document.getElementById('email').value;
            let errorMessage = '';
            if (email.trim() === '') {
                errorMessage += 'Email is required.\n';
            } else if (!validateEmail(email)) {
                errorMessage += 'Invalid email address.\n';
            } else if (<?php echo json_encode($emailExist); ?>.includes(email)) {
                errorMessage += 'Email already exists.\n';
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

function enqueue_newsletter_ajax_script() {
    $options = get_option('subscribers_settings');
    wp_enqueue_script('newsletter-ajax-script', plugin_dir_url(__FILE__) . 'js/newsletter-ajax.js', array('jquery'), '1.0', true);
    wp_localize_script('newsletter-ajax-script', 'newsletter_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'options' => $options // Pass options to JavaScript
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_newsletter_ajax_script');

// Ajax handler function
function handle_newsletter_form_ajax() {

    if (isset($_POST['email'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'subscribers';
        $email = sanitize_email($_POST['email']);

        // Retrieve options from the database
        $options = get_option('subscribers_settings');

        $existing_subscriber = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE email = %s", $email));

        if ($existing_subscriber > 0) {
            wp_send_json_error('Email already exists.');
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'email' => $email
                )
            );

            $options = get_option('subscribers_settings');

            add_action('phpmailer_init', function ($phpmailer) use ($email, $options) {
                $phpmailer->isSMTP();
                $to = $options['to_email'];
                $phpmailer->Host = $options['email_host'];
                $phpmailer->Port = $options['subs_port'];
                $phpmailer->SMTPAuth = true;
                $phpmailer->SMTPSecure = 'ssl';
                $phpmailer->Username = $options['email'];
                $phpmailer->Username = $options['subs_username'];
                $phpmailer->Password = $options['subs_password'];
                $phpmailer->setFrom($options['subs_from_email'], $options['subs_subject']);
                $phpmailer->addAddress($to);
                $phpmailer->Subject = 'Thank you for subscribing!';
                $phpmailer->Body = 'Dear Subscriber,<br><br>Thank you for subscribing to our.' . $options['subs_subject'];
                $phpmailer->isHTML(true);
            });

            // $to_customer = $email;
            $subject_customer = 'Thank you for subscribing!';
            $message_customer = 'Dear Subscriber ,<br><br>Thank you for subscribing to our.' . $options['subs_subject'];
            $headers_customer[] = 'Content-Type: text/html; charset=UTF-8';
            wp_mail($email, $subject_customer, $message_customer, $headers_customer);

            // $subject = 'Subscribe New User';
            // $message = 'Dear Admin ,<br><br>Thank you for subscribing to our newsletter.';
            // $headers[] = 'Content-Type: text/html; charset=UTF-8';
            // wp_mail($to, $subject, $message, $headers);
            
            $success_message = 'Thank you for subscribing!';
            set_transient('newsletter_success_message', $success_message, 5);

            wp_send_json_success('Thank you for subscribing!');
        }
    }
    wp_die();
}
add_action('wp_ajax_handle_newsletter_form', 'handle_newsletter_form_ajax');
add_action('wp_ajax_nopriv_handle_newsletter_form', 'handle_newsletter_form_ajax');


function newsletter_form_shortcode() {
    return newsletter_form_html();
}
add_shortcode('newsletter_form', 'newsletter_form_shortcode');

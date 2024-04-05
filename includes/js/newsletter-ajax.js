jQuery(document).ready(function($) {
    $('#newsletter-form').on('submit', function(event) {
        event.preventDefault();
        let email = $('#email').val();
        let options = newsletter_ajax_object.options;
        $.ajax({
            type: 'POST',
            url: newsletter_ajax_object.ajax_url,
            data: {
                'action': 'handle_newsletter_form',
                'email': email,
            },
            success: function(response) {
                if (response.success) {
                    // Handle success message
                    $('#validation-message').html(response.data).addClass('success').fadeIn();
                } else {
                    // Handle error message
                    $('#validation-message').html(response.data).addClass('error').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

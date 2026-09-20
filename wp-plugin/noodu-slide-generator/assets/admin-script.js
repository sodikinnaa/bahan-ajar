/* Noodu Admin Scripts */

jQuery(document).ready(function($) {
    // Validate API connection
    validateApiConnection();
});

function validateApiConnection() {
    var baseUrl = jQuery('[name="noodu_openai_base_url"]').val();
    var apiKey = jQuery('[name="noodu_openai_api_key"]').val();

    if (baseUrl && apiKey) {
        // API is configured
        document.getElementById('apiStatus').textContent = 'Connected';
    }
}

function showNotice(message, type) {
    type = type || 'success';
    var notice = jQuery('<div class="notice notice-' + type + '"><p>' + message + '</p></div>');
    jQuery('.wrap').prepend(notice);

    setTimeout(function() {
        notice.fadeOut(function() {
            jQuery(this).remove();
        });
    }, 5000);
}

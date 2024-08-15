jQuery(document).ready(function($) {
    $('#athlete-check-in-button').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: athleteDashboardProfile.ajaxurl,
            type: 'POST',
            data: {
                action: 'athlete_dashboard_check_in',
                nonce: athleteDashboardProfile.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Check-in successful!');
                    updateCheckInHistory();
                } else {
                    alert('Check-in failed. Please try again.');
                }
            }
        });
    });

    function updateCheckInHistory() {
        $.ajax({
            url: athleteDashboardProfile.ajaxurl,
            type: 'GET',
            data: {
                action: 'athlete_dashboard_get_check_in_history',
                nonce: athleteDashboardProfile.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#check-in-history').html(response.data);
                }
            }
        });
    }

    updateCheckInHistory();
});
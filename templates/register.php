<?php
/**
 * Template Name: Athlete Dashboard Registration
 *
 * @package AthleteDashboard
 */

get_header();

// Check if user is already logged in
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/dashboard' ) );
    exit;
}

?>

<div class="athlete-dashboard-register">
    <h2><?php esc_html_e( 'Register for Athlete Dashboard', 'athlete-dashboard' ); ?></h2>

    <?php
    // Display error messages
    if (isset($_GET['register_errors'])) {
        $errors = explode('|', urldecode($_GET['register_errors']));
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p class="error-message">' . esc_html($error) . '</p>';
        }
        echo '</div>';
    }

    // Show any error messages (legacy method, should be replaced by the above)
    if ( isset( $_GET['register'] ) && 'failed' == $_GET['register'] ) {
        echo '<div class="error-message">' . esc_html__( 'Registration failed. Please try again.', 'athlete-dashboard' ) . '</div>';
    }
    ?>

    <form id="athlete-dashboard-register-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="athlete_dashboard_register">
        <?php wp_nonce_field( 'athlete_dashboard_register_nonce', 'athlete_dashboard_register_nonce_field' ); ?>

        <p>
            <label for="username"><?php esc_html_e( 'Username', 'athlete-dashboard' ); ?></label>
            <input type="text" name="username" id="username" required>
        </p>

        <p>
            <label for="email"><?php esc_html_e( 'Email', 'athlete-dashboard' ); ?></label>
            <input type="email" name="email" id="email" required>
        </p>

        <p>
            <label for="password"><?php esc_html_e( 'Password', 'athlete-dashboard' ); ?></label>
            <input type="password" name="password" id="password" required>
        </p>

        <p>
            <label for="confirm_password"><?php esc_html_e( 'Confirm Password', 'athlete-dashboard' ); ?></label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </p>

        <p>
            <input type="checkbox" name="gdpr_consent" id="gdpr_consent" required>
            <label for="gdpr_consent"><?php esc_html_e( 'I consent to my personal data being processed according to the Privacy Policy', 'athlete-dashboard' ); ?></label>
        </p>

        <p>
            <input type="submit" value="<?php esc_attr_e( 'Register', 'athlete-dashboard' ); ?>">
        </p>
    </form>

    <p><?php esc_html_e( 'Already have an account?', 'athlete-dashboard' ); ?> <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Log in', 'athlete-dashboard' ); ?></a></p>
</div>

<?php
get_footer();

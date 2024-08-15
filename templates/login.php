<?php
/**
 * Template Name: Athlete Dashboard Login
 *
 * @package AthleteDashboard
 */

get_header();

// Check if user is already logged in
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/athlete-dashboard/' ) );
    exit;
}

?>

<div class="athlete-dashboard-login">
    <h2><?php esc_html_e( 'Login to Athlete Dashboard', 'athlete-dashboard' ); ?></h2>

    <?php
    // Display error messages
    if (isset($_GET['login_errors'])) {
        $errors = explode('|', urldecode($_GET['login_errors']));
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p class="error-message">' . esc_html($error) . '</p>';
        }
        echo '</div>';
    }
    ?>

    <form id="athlete-dashboard-login-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <input type="hidden" name="action" value="athlete_dashboard_login">
        <?php wp_nonce_field( 'athlete_dashboard_login_nonce', 'athlete_dashboard_login_nonce_field' ); ?>

        <p>
            <label for="user_login"><?php esc_html_e( 'Username or Email', 'athlete-dashboard' ); ?></label>
            <input type="text" name="user_login" id="user_login" required>
        </p>

        <p>
            <label for="user_pass"><?php esc_html_e( 'Password', 'athlete-dashboard' ); ?></label>
            <input type="password" name="user_pass" id="user_pass" required>
        </p>

        <p>
            <input type="checkbox" name="rememberme" id="rememberme" value="forever">
            <label for="rememberme"><?php esc_html_e( 'Remember Me', 'athlete-dashboard' ); ?></label>
        </p>

        <p>
            <input type="checkbox" name="gdpr_consent" id="gdpr_consent" required>
            <label for="gdpr_consent"><?php esc_html_e( 'I consent to my login data being stored as per the Privacy Policy', 'athlete-dashboard' ); ?></label>
        </p>

        <p>
            <input type="submit" value="<?php esc_attr_e( 'Log In', 'athlete-dashboard' ); ?>">
        </p>
    </form>

    <p><?php esc_html_e( "Don't have an account?", 'athlete-dashboard' ); ?> <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Register', 'athlete-dashboard' ); ?></a></p>
</div>

<?php
get_footer();
?>
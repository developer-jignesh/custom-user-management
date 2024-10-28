<?php
if (!defined('ABSPATH')) {
    exit;
}
// Handle form submission at the top to process data and prevent header errors
if (isset($_POST['submit_edit_user'])) {
    $user_id = intval($_POST['user_id']); // Get the user_id from the form
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit_user_' . $user_id)) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $role = sanitize_text_field($_POST['role']);
        
        // Update user details
        $user_data = [
            'ID' => $user_id,
            'display_name' => $name,
            'user_email' => $email,
        ];
        $update_result = wp_update_user($user_data);

        // Check for errors
        if (is_wp_error($update_result)) {
            $error_message = $update_result->get_error_message();
        } else {
            // Update role if it has been changed
            $user = new WP_User($user_id);
            $user->set_role($role);

            $url = admin_url('admin.php?page=custom-user-management&updated=1');
            // Redirect back to the user list with a success message
            wp_safe_redirect($url);
            exit; // Always exit after a redirect to prevent further code execution
        }
    } else {
        $error_message = "Nonce verification failed.";
    }
}
// Ensure no more header modifications happen after output
?>
<div class="wrap">
    <h1>Edit User</h1>

    <?php
    // Check if user_id is passed as a query parameter
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    // Fetch user data based on the user_id
    $user = get_userdata($user_id);

    // If the user doesn't exist or does not have the 'fk_user' role, stop execution
    if (!$user || !in_array('fk_user', $user->roles)) {
        wp_die('Invalid user or user does not have the required role.');
    }

    // Display error message if one exists
    if (isset($error_message)) {
        echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
    }
    ?>

    <form method="POST" action="<?php admin_url( 'admin.php?page=custom-user-management&action=edit&user_id='.$user_id ) ?>">
        <?php wp_nonce_field('edit_user_' . $user_id); ?>
        <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
        <table class="form-table">
            <tr>
                <th><label for="name">Name</label></th>
                <td><input type="text" name="name" id="name" value="<?php echo esc_attr($user->display_name); ?>" required></td>
            </tr>
            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="email" name="email" id="email" value="<?php echo esc_attr($user->user_email); ?>" required></td>
            </tr>
            <tr>
                <th><label for="role">Role</label></th>
                <td>
                    <select name="role" id="role">
                        <option value="fk_user" <?php selected($user->roles[0], 'fk_user'); ?>>FK User</option>
                        <option value="subscriber" <?php selected($user->roles[0], 'subscriber'); ?>>Subscriber</option>
                        <option value="contributor" <?php selected($user->roles[0], 'contributor'); ?>>Contributor</option>
                        <option value="author" <?php selected($user->roles[0], 'author'); ?>>Author</option>
                        <option value="editor" <?php selected($user->roles[0], 'editor'); ?>>Editor</option>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit_edit_user" class="button button-primary" value="Update User">
        </p>
    </form>
</div>
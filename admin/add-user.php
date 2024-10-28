<?php
// admin/add-user.php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Add New User</h1>
    <form method="POST" action="">
        <?php wp_nonce_field('add_user_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="name">Name</label></th>
                <td><input type="text" name="name" id="name" required></td>
            </tr>
            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="email" name="email" id="email" required></td>
            </tr>
            <tr>
                <th><label for="role">Role</label></th>
                <td>
                    <select name="role" id="role">
                        <option value="fk_user">FK User</option>
                        <option value="subscriber">Subscriber</option>
                        <option value="contributor">Contributor</option>
                        <option value="author">Author</option>
                        <option value="editor">Editor</option>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit_add_user" class="button button-primary" value="Add User">
        </p>
    </form>
</div>
<?php

// Handle form submission
if (isset($_POST['submit_add_user'])) {
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'add_user_nonce')) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $role = sanitize_text_field($_POST['role']);

        if (!email_exists($email)) {
            $user_id = wp_insert_user([
                'user_login' => $email,
                'user_email' => $email,
                'user_pass' => wp_generate_password(),
                'display_name' => $name,
                'role' => $role,
            ]);

            if (is_wp_error($user_id)) {
                $error_message = $user_id->get_error_message();
            } else {
                $success_message = "Successfully added user with ID {$user_id}.";
                wp_new_user_notification($user_id, null, 'user');
            }
        } else {
            $error_message = "User email {$email} already exists.";
        }
    } else {
        $error_message = "Nonce verification failed.";
    }
}
// Display messages
if (isset($error_message)) {
    echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
}
if (isset($success_message)) {
    echo '<div class="updated"><p>' . esc_html($success_message) . '</p></div>';
}
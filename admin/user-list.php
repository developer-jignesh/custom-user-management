<?php 
// admin/user-list.php
if (!defined('ABSPATH')) {
    exit;
}
// Get all users with role 'fk_user'
$users = get_users(['role' => 'fk_user']);
// Display success message if a user was deleted
if (isset($_GET['deleted'])) {
    echo '<div class="updated"><p>User deleted successfully.</p></div>';
}
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Check if the user has permission to delete users
    if (current_user_can('delete_users')) {
        
        // Verify nonce before deleting
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_user_' . $user_id)) {
            
            // Delete the user
            wp_delete_user($user_id);
            
            // Redirect after successful deletion
            wp_safe_redirect(admin_url('admin.php?page=custom-user-management&deleted=1'));
            exit;
        } else {
            echo '<div class="error"><p>Invalid request. Nonce verification failed.</p></div>';
        }
    } else {
        echo '<div class="error"><p>You do not have permission to delete users.</p></div>';
    }
}
?>
<div class="wrap">
    <h1>Custom User List</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo esc_html($user->display_name); ?></td>
                    <td><?php echo esc_html($user->user_email); ?></td>
                    <td><?php echo esc_html($user->roles[0]); ?></td>
                    <td style="display:flex; gap:10px">
                        <a href="<?php echo admin_url('admin.php?page=custom-user-management&action=edit&user_id=' . $user->ID); ?>">Edit</a>
                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-user-management&action=delete&user_id=' . $user->ID), 'delete_user_' . $user->ID); ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
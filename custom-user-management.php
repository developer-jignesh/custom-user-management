<?php
/**
 * Plugin Name: Custom User Management
 * Version: 1.0.0
 * Author: jignesh-sharma
 * Description: To Get more control over the custom user
 */
// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}
// ob_start();
// Add custom role

add_action('plugins_loaded', function():void {
    add_role('fk_user', 'FK User', [
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
    ]);
});
// Add admin menu
function custom_user_management_menu() {
    add_menu_page(
        'Custom Users',
        'Custom Users',
        'manage_options',
        'custom-user-management',
        'custom_user_management_page',
        'dashicons-admin-users',
        71
    );
    add_submenu_page(
        'custom-user-management',
        'Add User',
        'Add User',
        'manage_options',
        'custom-user-add',
        'custom_user_add_page'
    );
}
add_action('admin_menu', 'custom_user_management_menu');
// Include admin pages
function custom_user_management_page() {
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['user_id'])) {
        require_once( plugin_dir_path(__FILE__) . 'admin/edit-user.php' );
    } else {
        require_once( plugin_dir_path(__FILE__) . 'admin/user-list.php' );
    }
}
function custom_user_add_page() {
    include plugin_dir_path(__FILE__) . 'admin/add-user.php';
}
function custom_user_edit_page() {
    
}

// add_action('shutdown', function() {
//     ob_end_flush();
// });
<?php

class MJ_CRUD {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'load_assets']);
        add_action('admin_post_mj_save_data', [$this, 'save_data']);
        add_action('admin_post_mj_delete_data', [$this, 'delete_data']);
        add_shortcode('mj_crud_list', [$this, 'frontend_list_view']);
    }

    public function add_admin_page() {
        add_menu_page(
            'MJ CRUD',
            'MJ CRUD',
            'manage_options',
            'mj-crud',
            [$this, 'render_admin_page'],
            'dashicons-database',
            6
        );
    }

    public function load_assets($hook) {
        if ($hook !== 'toplevel_page_mj-crud') return;

        wp_enqueue_style('mj-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
    }

    public function render_admin_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'mj_data';

        // Create table if not exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

        // Get data
        $results = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
        
        include plugin_dir_path(__FILE__) . 'view-admin.php';
    }

    public function save_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'mj_data';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);

        if ($id) {
            $wpdb->update($table, ['name' => $name, 'email' => $email], ['id' => $id]);
        } else {
            $wpdb->insert($table, ['name' => $name, 'email' => $email]);
        }

        wp_redirect(admin_url('admin.php?page=mj-crud'));
        exit;
    }

    public function delete_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'mj_data';
        $id = intval($_GET['id'] ?? 0);

        if ($id) {
            $wpdb->delete($table, ['id' => $id]);
        }

        wp_redirect(admin_url('admin.php?page=mj-crud'));
        exit;
    }

    public function frontend_list_view() {
        global $wpdb;
        $table = $wpdb->prefix . 'mj_data';
        $results = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);

        ob_start(); ?>

        <div class="mj-frontend-list">
            <h3>MJ Data List</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= esc_html($row['id']) ?></td>
                            <td><?= esc_html($row['name']) ?></td>
                            <td><?= esc_html($row['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <style>
            .mj-frontend-list table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .mj-frontend-list th, .mj-frontend-list td {
                border: 1px solid #ddd;
                padding: 10px;
            }
            .mj-frontend-list th {
                background: #f4f4f4;
            }
        </style>

        <?php
        return ob_get_clean();
    }

}

new MJ_CRUD();
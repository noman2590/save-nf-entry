<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SNFEMainController
{
    public function __construct()
    {
        register_activation_hook( SNFE_PLUGIN_BASENAME, array( $this, 'snfe_activation_hook' ));
        add_action('admin_menu', array( $this, 'snfe_admin_menu' ));
        add_action('admin_menu', array( $this, 'snfe_register_custom_admin_page' ));
        add_action('ninja_forms_after_submission', array($this, 'snfe_save_nf_entry'));
        add_action('admin_enqueue_scripts', array( $this, 'snfe_enqueue_admin_scripts'));
    }

    public function snfe_admin_menu() {
        add_menu_page(
            __('NF Entries', 'nf-entries'),
            __('NF Entries', 'nf-entries'),
            'manage_options',
            'manage-nf-entries',
            'SNFEContactController::index',
            'dashicons-database',
            6
        );
    }

    function snfe_register_custom_admin_page() {
        add_submenu_page(
            'nf-entries', // hidden submenu
            __('Form Entries Listing', 'ninja-form-entries'),
            __('Form Entries Listing', 'ninja-form-entries'),
            'manage_options',
            'ninja-form-entries',
            'SNFEContactController::form_entry_details'
        );
    }

    public static function snfe_query_var_custom( $args )
    {
        global $wp_query;
        $wp_query->set("data", $args);

    }

    public function snfe_activation_hook() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'nf_entries';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            $sql = "CREATE TABLE  $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
                `form_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            dbDelta($sql);
        }

        $table_name = $wpdb->prefix . 'nf_entry_meta';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            $sql = "CREATE TABLE  $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
                `nf_entry_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `meta_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            dbDelta($sql);
        }
    }

    function snfe_save_nf_entry( $form_data ) {

        global $wpdb;

        $form_id = $form_data['form_id'];
        $fields = $form_data['fields'];
        
        $table = $wpdb->prefix . 'nf_entries';
        $entry_data = array(
            'form_id' => $form_id,
        );

        $wpdb->insert($table, $entry_data);
        $entry_id = $wpdb->insert_id;

        if( $entry_id ) {
            $table = $wpdb->prefix . 'nf_entry_meta';
            foreach ($fields as $field) {
                $key = preg_replace('/_\d+$/', '', $field['key']);
                if ($key != 'submit') {
                    $entry_meta = array( 'nf_entry_id' => $entry_id, 'meta_key' => $key, 'meta_value' => $field['value'] );
                    $wpdb->insert($table, $entry_meta);
                }
            }
        }
    }

    function snfe_enqueue_admin_scripts () {
        global $pagenow;
        if ($pagenow === 'admin.php' && $_GET['page'] === 'ninja-form-entries') {
            wp_enqueue_script('data_tables', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), '1.10.25', true);
            wp_enqueue_style('data_tables_style', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
        }
        wp_enqueue_style('plugin-style', SNFE_PLUGIN_URL . '/lib/assets/style.css');
    }
      

}
<?php

/**
 * Register a export menu page by class.
 */
class EvntMgmt_Export_Events_Menu {

    /**
     * Menu initialization.
     */
    public function __construct() {

        add_action('admin_menu', array($this, 'register_export_menu_page'));
        add_action('admin_init', array($this, 'export_report'));
    }

    /**
     * Register Menu.
     */
    public function register_export_menu_page() {
        add_menu_page(
                __('Export Events', EVENT_TEXT_DOMAIN),
                'Export Events',
                'manage_options',
                'export-events',
                array($this, 'export_menu_page'),
                'dashicons-table-col-before');
    }

    /**
     * Render export function.
     */
    public function export_menu_page() {

        echo '<h1>Export Events</h1>';
        echo '<a href="' . wp_nonce_url(admin_url('admin.php?page=export-events&export=true'), 'event_export_action', 'event_export_nonce') . '" class="button button-primary button-large" />' . __('Export Events', EVENT_TEXT_DOMAIN) . '</a>';
    }

    /*
     * Export Sheet
     */

    public function export_report() {

        if (isset($_GET['export']) && $_GET['export'] == 'true') {

            if (isset($_GET['event_export_nonce']) || wp_verify_nonce($_GET['event_export_nonce'], 'event_export_action')) {

                $delimiter = ",";
                // Create a file pointer 
                $f = fopen('php://memory', 'w');

                // Column names 
                $fields = array('Title', 'Description', 'Event Date', 'Venue', 'Location');
                fputcsv($f, $fields, $delimiter);

                // Fetch records from database 
                $events_args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'events-management',
                    'orderby' => 'ID',
                    'order' => 'DESC',
                    'post_status' => 'publish'
                );

                $event_listing = get_posts($events_args);

                if ($event_listing) {

                    foreach ($event_listing as $event_val) {

                        $title = html_entity_decode(get_the_title($event_val->ID), ENT_QUOTES, 'UTF-8');
                        $content = $event_val->post_content;
                        $get_event_date = get_post_meta($event_val->ID, 'evntmgmt_date', true);
                        $event_date = !empty($get_event_date) ? date('d-m-Y', strtotime($get_event_date)) : '';
                        $get_venue = get_post_meta($event_val->ID, 'evntmgmt_venue', true);
                        $get_location = get_post_meta($event_val->ID, 'evntmgmt_location', true);
                        $lineData = array($title, $content, $event_date, $get_venue, $get_location);
                        fputcsv($f, $lineData, $delimiter);
                    }
                } else {
                    fputcsv($f, array('No events found.'), $delimiter);
                }

                // Headers for download 
                // Move back to beginning of file 
                fseek($f, 0);

                // Set headers to download file rather than displayed 
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="event_export.csv";');

                //output all remaining data on a file pointer
                fpassthru($f);
                exit;
            } else {

                wp_die(__('Sorry,nonce did not verify.', EVENT_TEXT_DOMAIN));
            }
        }
    }

}

// Initialize class object
new EvntMgmt_Export_Events_Menu();

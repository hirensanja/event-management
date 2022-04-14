<?php

/**
 * Register a meta box using a class.
 */
class EvntMgmt_Create_Custom_Fields {

    /**
     * Meta box initialization.
     */
    public function __construct() {

        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('save_post', array($this, 'save_metabox'), 10, 2);
    }

    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
                'events-meta-box',
                __('Events Management Fields', EVENT_TEXT_DOMAIN),
                array($this, 'render_metabox'),
                'events-management',
                'advanced',
                'default'
        );
    }

    /**
     * Renders the meta box.
     */
    public function render_metabox($post) {

        $event_date = get_post_meta($post->ID, 'evntmgmt_date', true);
        $venue = get_post_meta($post->ID, 'evntmgmt_venue', true);
        $location = get_post_meta($post->ID, 'evntmgmt_location', true);
        // Include js & css
        wp_enqueue_style( 'evntmgmt-jQuery-ui-css' );
        wp_enqueue_style( 'evntmgmt-custom-css' );
        wp_enqueue_script('evntmgmt-jQuery-ui');
        wp_enqueue_script('evntmgmt-custom-js');
        ?>
        <div>
            <label for="evntmgmt_date"><strong><?php echo __('Event Date:', EVENT_TEXT_DOMAIN); ?></strong></label><br/>
            <input readonly="readonly" type="text" id="evntmgmt_date" value="<?php echo !empty($event_date) ? date('d-m-Y', strtotime($event_date)) : ''; ?>" name="evntmgmt_date" />
        </div><br/>
        <div>
            <label for="evntmgmt_venue"><strong><?php echo __('Event Venue:', EVENT_TEXT_DOMAIN); ?></strong></label><br/>
            <textarea id="evntmgmt_venue" name="evntmgmt_venue" rows="4" cols="50"><?php echo $venue; ?></textarea>
        </div><br/>
        <div>
            <label for="evntmgmt_location"><strong><?php echo __('Event Location:', EVENT_TEXT_DOMAIN); ?></strong></label><br/>
            <textarea id="evntmgmt_location" name="evntmgmt_location" rows="4" cols="50"><?php echo $location; ?></textarea>
        </div>
        <?php
        // Add nonce for security and authentication.
        wp_nonce_field('evntmgmt_nonce_action', 'evntmgmt_nonce');
    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox($post_id, $post) {
        // Add nonce for security and authentication.
        $nonce_name = isset($_POST['evntmgmt_nonce']) ? $_POST['evntmgmt_nonce'] : '';
        $nonce_action = 'evntmgmt_nonce_action';

        // Check if nonce is valid.
        if (!wp_verify_nonce($nonce_name, $nonce_action)) {
            return;
        }

        // Check if user has permissions to save data.
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if not an autosave.
        if (wp_is_post_autosave($post_id)) {
            return;
        }

        // Check if not a revision.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Check post type
        if (get_post_type(get_the_ID()) != 'events-management') {
            return;
        }
        
        // Save event date
        if (isset($_POST['evntmgmt_date'])) {
            $get_event_date = sanitize_text_field($_POST['evntmgmt_date']);
            $convert_date = date('Y-m-d', strtotime($get_event_date));
            update_post_meta($post_id, 'evntmgmt_date', $convert_date);
        }
        
        // Save event venue
        if (isset($_POST['evntmgmt_venue'])) {
            $get_event_vanue = stripslashes(sanitize_textarea_field($_POST['evntmgmt_venue']));
            update_post_meta($post_id, 'evntmgmt_venue', $get_event_vanue);
        }
        
        // Save event location
        if (isset($_POST['evntmgmt_location'])) {
            $get_event_location = stripslashes(sanitize_textarea_field($_POST['evntmgmt_location']));
            update_post_meta($post_id, 'evntmgmt_location', $get_event_location);
        }
    }

}

// Initialize class object
new EvntMgmt_Create_Custom_Fields();

<?php

/**
 * Register js/css libraries for events
 */
class EvntMgmt_Register_Libraries {

    /**
     * Libraries initialization.
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'add_libraries'));
        add_action('admin_enqueue_scripts', array($this, 'add_libraries'));
    }

    /**
     * Add the libraries.
     */
    public function add_libraries() {

        // Js
        wp_register_script('evntmgmt-jQuery-ui',
                EVENT_PLUGIN_URL . 'assets/libraries/js/jquery-ui.js',
                array('jquery'), //depends on these, however, they are registered by core already, so no need to enqueue them.
                false, false);

        wp_register_script('evntmgmt-custom-js',
                EVENT_PLUGIN_URL . 'assets/libraries/js/custom-libraries.js',
                array('jquery'), //depends on these, however, they are registered by core already, so no need to enqueue them.
                false, false);
        
        wp_localize_script('evntmgmt-custom-js', 'evntmgmt_ajax_object',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'datepicker_img' => EVENT_PLUGIN_URL.'assets/libraries/css/images/calendar-interface-symbol-tool.png',
                    'nonce' => wp_create_nonce( 'evntmgmt-filters' ),
                )
        );
        // Cs
        wp_register_style('evntmgmt-jQuery-ui-css', EVENT_PLUGIN_URL . 'assets/libraries/css/jquery-ui.css');
        wp_register_style('evntmgmt-custom-css', EVENT_PLUGIN_URL . 'assets/libraries/css/custom-style.css');
    }

}

// Initialize class object
new EvntMgmt_Register_Libraries();

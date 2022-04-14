<?php

/**
 * Register a custom post type using a class.
 */
class EvntMgmt_Create_Post_Type {

    /**
     * Custom post type initialization.
     */
    public function __construct() {

        add_action('init', array($this, 'create_events_post_type'));
    }

    /**
     * Create Event post type .
     */
    public function create_events_post_type() {
        $labels = array(
            'name' => _x('Events', 'Post type general name', EVENT_TEXT_DOMAIN),
            'singular_name' => _x('Events', 'Post type singular name', EVENT_TEXT_DOMAIN),
            'menu_name' => _x('Events', 'Admin Menu text', EVENT_TEXT_DOMAIN),
            'name_admin_bar' => _x('Events', 'Add New on Toolbar', EVENT_TEXT_DOMAIN),
            'add_new' => __('Add New', EVENT_TEXT_DOMAIN),
            'add_new_item' => __('Add New Events', EVENT_TEXT_DOMAIN),
            'new_item' => __('New Events', EVENT_TEXT_DOMAIN),
            'edit_item' => __('Edit Events', EVENT_TEXT_DOMAIN),
            'view_item' => __('View Events', EVENT_TEXT_DOMAIN),
            'all_items' => __('All Events', EVENT_TEXT_DOMAIN),
            'search_items' => __('Search Events', EVENT_TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Events:', EVENT_TEXT_DOMAIN),
            'not_found' => __('No events found.', EVENT_TEXT_DOMAIN),
            'not_found_in_trash' => __('No events found in Trash.', EVENT_TEXT_DOMAIN),
            'archives' => _x('Events archives', 'The post type archive label used in nav menus. Default "Post Archives".', EVENT_TEXT_DOMAIN),
            'insert_into_item' => _x('Insert into events', 'Overrides the "Insert into post/Insert into page" phrase (used when inserting media into a post).', EVENT_TEXT_DOMAIN),
            'uploaded_to_this_item' => _x('Uploaded to this events', 'Overrides the "Uploaded to this post/Uploaded to this page" phrase (used when viewing media attached to a post).', EVENT_TEXT_DOMAIN),
            'filter_items_list' => _x('Filter events list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list/Filter pages list".', EVENT_TEXT_DOMAIN),
            'items_list_navigation' => _x('Events list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation/Pages list navigation".', EVENT_TEXT_DOMAIN),
            'items_list' => _x('Events list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list/Pages list".', EVENT_TEXT_DOMAIN),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'events-management'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail'),
        );

        register_post_type('events-management', $args);

        // Add new taxonomy, make it hierarchical (like categories)
        $taxonomy_labels = array(
            'name' => _x('Events Types', 'taxonomy general name', EVENT_TEXT_DOMAIN),
            'singular_name' => _x('Events Types', 'taxonomy singular name', EVENT_TEXT_DOMAIN),
            'search_items' => __('Search Events Types', EVENT_TEXT_DOMAIN),
            'popular_items' => __('Popular Events Types', EVENT_TEXT_DOMAIN),
            'all_items' => __('All Events Types', EVENT_TEXT_DOMAIN),
            'parent_item' => __('Parent Events Types', EVENT_TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Events Types:', EVENT_TEXT_DOMAIN),
            'edit_item' => __('Edit Events Types', EVENT_TEXT_DOMAIN),
            'update_item' => __('Update Events Types', EVENT_TEXT_DOMAIN),
            'add_new_item' => __('Add New Events Types', EVENT_TEXT_DOMAIN),
            'new_item_name' => __('New Events Types Name', EVENT_TEXT_DOMAIN),
            'separate_items_with_commas' => __('Separate events types with commas', EVENT_TEXT_DOMAIN),
            'add_or_remove_items' => __('Add or remove events types', EVENT_TEXT_DOMAIN),
            'choose_from_most_used' => __('Choose from the most used events types', EVENT_TEXT_DOMAIN),
            'not_found' => __('No events types found.', EVENT_TEXT_DOMAIN),
            'menu_name' => __('Events Types', EVENT_TEXT_DOMAIN),
            'menu_name' => __('Events Types', EVENT_TEXT_DOMAIN),
        );

        $taxonomy_args = array(
            'hierarchical' => true,
            'labels' => $taxonomy_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'events-types'),
        );

        register_taxonomy('events-types', array('events-management'), $taxonomy_args);
    }

}

// Initialize class object
new EvntMgmt_Create_Post_Type();

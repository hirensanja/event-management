<?php

/**
 * Register a shortcode for event listing using a class.
 */
class EvntMgmt_Create_Shortcode {

    /**
     * Shortcode initialization.
     */
    public function __construct() {

        add_shortcode('events_listing', array($this, 'get_events_listing'));
        add_action('wp_ajax_nopriv_get_events_listing_by_filters', array($this, 'get_events_listing_by_filters'));
        add_action('wp_ajax_get_events_listing_by_filters', array($this, 'get_events_listing_by_filters'));
    }

    /**
     * Renders the shortcode.
     */
    public function get_events_listing() {

        // Include js & css
        wp_enqueue_style('evntmgmt-jQuery-ui-css');
        wp_enqueue_style('evntmgmt-custom-css');
        wp_enqueue_script('evntmgmt-jQuery-ui');
        wp_enqueue_script('evntmgmt-custom-js');

        $html = '';
        $html .= '<div id="evntmgmt_listing">';
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $args = array('post_type' => 'events-management',
            'posts_per_page' => 10,
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'Desc',
            'paged' => $paged);

        $loop = new WP_Query($args);

        if ($loop->have_posts()) :
            $html .= '<div class="evntmgmt_filter">';
            $taxonomies = get_terms(array(
                'taxonomy' => 'events-types',
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            if (!empty($taxonomies)) :
                $html .= '<div class="evntmgmt_cat_filter">';
                $html .= '<lable for="evntmgmt_type">' . __('Events Categories:', EVENT_TEXT_DOMAIN) . '</lable><br/>';
                $html .= '<select name="evntmgmt_type" id="evntmgmt_type">';
                $html .= '<option value="">' . __('Please select one', EVENT_TEXT_DOMAIN) . '</option>';
                foreach ($taxonomies as $get_event_type) {
                    $html .= '<option value="' . esc_attr($get_event_type->term_id) . '">' . esc_html($get_event_type->name) . '</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
            endif;
            $html .= '<div class="evntmgmt_start_dt_filter">';
            $html .= '<lable for="evntmgmt_start_date">' . __('Start Date:', EVENT_TEXT_DOMAIN) . '</lable><br/>';
            $html .= '<input placeholder="dd-mm-YYYY" type="text" name="evntmgmt_start_date" id="evntmgmt_start_date" readonly="readonly" />';
            $html .= '</div>';
            $html .= '<div class="evntmgmt_end_dt_filter">';
            $html .= '<lable for="evntmgmt_end_date">' . __('End Date:', EVENT_TEXT_DOMAIN) . '</lable><br/>';
            $html .= '<input placeholder="dd-mm-YYYY" type="text" name="evntmgmt_end_date" id="evntmgmt_end_date" readonly="readonly" />';
            $html .= '</div>';
            $html .= '<div class="evntmgmt_filter_btn">';
            $html .= '<button type="button" id="evntmgmt_filters">' . __('Filters', EVENT_TEXT_DOMAIN) . '</button>';
            $html .= '<img src="' . EVENT_PLUGIN_URL . 'assets/libraries/css/images/loader.gif" style="display:none" class="evntmgmt_loader" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="evntmgmt_details">';
            while ($loop->have_posts()) : $loop->the_post();
                $feat_image_url = wp_get_attachment_url(get_post_thumbnail_id());
                $title = get_the_title();
                $desc = get_the_content();
                $get_event_date = get_post_meta(get_the_ID(), 'evntmgmt_date', true);
                $event_date = !empty($get_event_date) ? date('d-m-Y', strtotime($get_event_date)) : '-';
                $get_venue = get_post_meta(get_the_ID(), 'evntmgmt_venue', true);
                $venue = !empty($get_venue) ? $get_venue : '-';
                $get_location = get_post_meta(get_the_ID(), 'evntmgmt_location', true);
                $location = !empty($get_location) ? $get_location : '-';
                if (!empty($feat_image_url)) {
                    $html .= '<div class="evntmgmt_img">';
                    $html .= '<img src="' . $feat_image_url . '" />';
                    $html .= '</div>';
                }
                $html .= '<div class="evntmgmt_title"><h2>' . $title . '</h2></div>';
                $html .= '<div class="evntmgmt_date"><strong>Event Date:</strong> ' . $event_date . '</div>';
                $html .= '<div class="evntmgmt_venue"><strong>Venue:</strong> ' . $venue . '</div>';
                $html .= '<div class="evntmgmt_location"><strong>Location:</strong> ' . $location . '</div>';
                $html .= '<div class="evntmgmt_desc">' . wpautop($desc) . '</div>';

                $html .= '<hr/>';
            endwhile;
            $big = 999999999;
            $html .= '<div class="pagination">';
            $html .= paginate_links(array(
                'base' => str_replace($big, '%#%', get_pagenum_link($big)),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $loop->max_num_pages
            ));
            $html .= '</div>';
            $html .= '</div>';
        else:
            $html .= '<div class="evntmgmt_not_found">';
            $html .= __('No events found..', EVENT_TEXT_DOMAIN);
            $html .= '</div>';
        endif;
        $html .= '</div>';
        return $html;
    }

    /**
     * Ajax base filters.
     */
    public function get_events_listing_by_filters() {

        check_ajax_referer('evntmgmt-filters', 'security');
        $cat_filter_args = array();
        $dt_filter_args = array();
        $cat = !empty($_POST['cat']) ? sanitize_text_field($_POST['cat']) : '';
        $start_dt = !empty($_POST['start_dt']) ? date('Y-m-d', strtotime(sanitize_text_field($_POST['start_dt']))) : '';
        $end_dt = !empty($_POST['end_dt']) ? date('Y-m-d', strtotime(sanitize_text_field($_POST['end_dt']))) : '';
        $default_args = array('post_type' => 'events-management',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'ID',
            'order' => 'Desc');
        if (!empty($cat)) {
            $cat_filter_args = array('tax_query' => array(
                    array(
                        'taxonomy' => 'events-types', // taxonomy slug
                        'terms' => array($cat), // term ids
                        'field' => 'term_id', // Also support: slug, name, term_taxonomy_id
                        'operator' => 'IN', // Also support: slug, name, term_taxonomy_id
                        'include_children' => true,
                    ),
            ));
        }
        if (!empty($start_dt) && !empty($end_dt)) {

            $dt_filter_args = array('meta_query' => array(
                    array(
                        'key' => 'evntmgmt_date',
                        'value' => array($start_dt, $end_dt),
                        'compare' => 'BETWEEN',
                        'type' => 'DATE'
                    )
            ));
        }

        $filter_args = array_merge($default_args, $cat_filter_args,$dt_filter_args);
        $filter_loop = new WP_Query($filter_args);
        $filter_data = array();
        $no_records_found = array();
        if ($filter_loop->have_posts()) :

            while ($filter_loop->have_posts()) : $filter_loop->the_post();
                $feat_image_url = wp_get_attachment_url(get_post_thumbnail_id());
                $title = get_the_title();
                $desc = wpautop(get_the_content());
                $get_event_date = get_post_meta(get_the_ID(), 'evntmgmt_date', true);
                $event_date = !empty($get_event_date) ? date('d-m-Y', strtotime($get_event_date)) : '-';
                $get_venue = get_post_meta(get_the_ID(), 'evntmgmt_venue', true);
                $venue = !empty($get_venue) ? $get_venue : '-';
                $get_location = get_post_meta(get_the_ID(), 'evntmgmt_location', true);
                $location = !empty($get_location) ? $get_location : '-';

                $filter_data[] = array('img' => $feat_image_url,
                    'title' => $title,
                    'desc' => $desc,
                    'event_date' => $event_date,
                    'venue' => $venue,
                    'location' => $location);
            endwhile;
        else:
            $no_records_found = __('No events found..', EVENT_TEXT_DOMAIN);
        endif;

        echo json_encode(array('events_dtls' => $filter_data, 'no_events_found' => $no_records_found));
        die;
    }

}

// Initialize class object
new EvntMgmt_Create_Shortcode();

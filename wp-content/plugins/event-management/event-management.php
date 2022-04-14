<?php
/**
 * Plugin Name: Event Management
 * Description: Event Management
 * Version: 1.0.0
 * Author: Hiren Sanja
 * Author URI: https://github.com/Hiren1094
 * Text Domain: event-management
 * Requires PHP: 7.0
 *
 * @package Event Management
 */

defined( 'ABSPATH' ) || exit;

// Define Constance
define('EVENT_TEXT_DOMAIN','event-management');
define('EVENT_PLUGIN_URL',plugin_dir_url( __FILE__ ));
define('EVENT_PLUGIN_DIR',__DIR__);
// Load core packages and the autoloader.
require __DIR__ . '/src/create-events-post-type.php';
require __DIR__ . '/src/create-custom-fields-for-events.php';
require __DIR__ . '/src/register-libraries-for-events.php';
require __DIR__ . '/src/create-shortcode-listing.php';
require __DIR__ . '/src/export-events.php';
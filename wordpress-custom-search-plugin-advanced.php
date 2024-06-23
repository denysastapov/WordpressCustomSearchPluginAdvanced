<?php
/*
Plugin Name: WordPress Custom Search Plugin Advanced
Plugin URI: https://test.local
Description: Extends the functionality of post type pages by adding a custom search string.
Version: 1.0
Author: Denys Astapov
Author URI: https://test.local
*/

require_once plugin_dir_path(__FILE__) . 'shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';

function custom_search_plugin_add_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=wordpress-custom-search-plugin-advanced">' . __('Settings', 'your-textdomain') . '</a>';
    $links[] = $settings_link;
    return $links;
}
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'custom_search_plugin_add_settings_link');

function custom_search_plugin_scripts()
{
    wp_enqueue_style('custom-search-style', plugin_dir_url(__FILE__) . 'styles.css');
}
add_action('wp_enqueue_scripts', 'custom_search_plugin_scripts');

function my_custom_search_plugin_add_google_fonts()
{
    wp_enqueue_style('my-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap', false);
}
add_action('wp_enqueue_scripts', 'my_custom_search_plugin_add_google_fonts');

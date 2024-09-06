<?php

/**
 * Plugin Name: MG ANIME LIST
 * Plugin URI: https://www.wordpress.org/mv-translations
 * Description: Plugin generate posts with anime list
 * Version: 1.0
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Author: Mario Reyes
 * Author URI: https://www.linkedin.com/in/mager19/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mg-anime-list
 * Domain Path: /languages
 */
/*
MG ANIME LIST is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
MG ANIME LIST is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with MG ANIME LIST. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('MG_Anime_List')) {

    class MG_Anime_List
    {

        public function __construct()
        {

            $this->define_constants();
            add_action('admin_menu', array($this, 'add_menu'));
            require_once(MG_ANIME_LIST_PATH . 'functions/functions.php');
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_ajax_fetch_anime_data', array($this, 'fetch_anime_data'));

            require_once(MG_ANIME_LIST_PATH . 'post-types/mg-anime-list.cpt.php');
            $mg_anime_list_cpt = new MG_ANIME_LIST_CPT();
        }

        public function define_constants()
        {
            // Path/URL to root of this plugin, with trailing slash.
            define('MG_ANIME_LIST_PATH', plugin_dir_path(__FILE__));
            define('MG_ANIME_LIST_URL', plugin_dir_url(__FILE__));
            define('MG_ANIME_LIST_VERSION', '1.0.0');
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            update_option('rewrite_rules', '');
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            flush_rewrite_rules();
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall() {}

        public function add_menu()
        {
            add_menu_page(
                "MG Anime List Options",
                "Anime List Options",
                "manage_options",
                "mg-anime-list-admin",
                array($this, "mg_anime_list_settings_page"),
                "data:image/svg+xml;base64," .
                    base64_encode(
                        '
                        <svg fill="#000000" viewBox="0 0 24 24" role="img" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M2.933 13.467a10.55 10.55 0 1 1 21.067-.8V12c0-6.627-5.373-12-12-12S0 5.373 0 12s5.373 12 12 12h.8a10.617 10.617 0 0 1-9.867-10.533zM19.2 14a3.85 3.85 0 0 1-1.333-7.467A7.89 7.89 0 0 0 14 5.6a8.4 8.4 0 1 0 8.4 8.4 6.492 6.492 0 0 0-.133-1.6A3.415 3.415 0 0 1 19.2 14z"></path></g></svg>
                        '
                    ),
                21
            );
        }

        public function mg_anime_list_settings_page()
        { ?>
            <div class="wrap">
                <h1>MG Anime List</h1>
                <p>
                    This plugin will fetch 3 random animes from the Jikan API and create a post for each one.
                </p>
                <button id="fetch-anime" class="button button-primary">Fetch 3 Random Animes</button>
                <div id="anime-result"></div>
            </div>
<?php
        }

        // Enqueue the script for AJAX
        public function enqueue_scripts()
        {
            wp_enqueue_script(
                'mg-anime-list-script',
                MG_ANIME_LIST_URL . 'assets/js/anime-list.js',
                array('jquery'), // Include jQuery
                MG_ANIME_LIST_VERSION,
                true
            );

            // Pass AJAX URL to the script
            wp_localize_script(
                'mg-anime-list-script',
                'mg_anime_list_ajax',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'   => wp_create_nonce('wp_rest')
                )
            );
        }
    }
}

// Plugin Instantiation
if (class_exists('MG_Anime_List')) {

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('MG_Anime_List', 'activate'));
    register_deactivation_hook(__FILE__, array('MG_Anime_List', 'deactivate'));
    register_uninstall_hook(__FILE__, array('MG_Anime_List', 'uninstall'));

    // Instatiate the plugin class
    $mg_anime_list = new MG_Anime_List();
}

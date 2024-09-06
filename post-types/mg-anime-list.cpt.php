<?php
if (!class_exists("MG_ANIME_LIST_CPT")) {
    class MG_ANIME_LIST_CPT
    {
        function __construct()
        {
            add_action("init", array($this, "create_post_type"));
        }

        public function create_post_type()
        {
            register_post_type("mg-anime-list", [
                "label" => "Anime List",
                "description" => "Anime List Posts",
                "labels" => array(
                    "name" => "Anime List",
                    "singular_name" => "Show",
                ),
                "public" => true,
                "supports" => array("title", "editor", "thumbnail"),
                "hierarchical" => false,
                "show_ui" => true,
                "show_in_menu" => true,
                "menu_position" => 5,
                "show_in_admin_bar" => true,
                "show_in_nav_menus" => true,
                "can_export" => true,
                "has_archive" => false,
                "exclude_from_search" => false,
                "publicly_queryable" => true,
                "show_in_rest" => true,
                "menu_icon" => "dashicons-images-alt2",
            ]);
        }
    }
}

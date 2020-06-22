<?php

if (! function_exists('get_stylesheet_directory')) {
    function get_stylesheet_directory()
    {}
}

if (! function_exists('wp_insert_post')) {
    function wp_insert_post(...$args)
    {}
}

if (! function_exists('wp_upload_dir')) {
    function wp_upload_dir()
    {}
}

if (! function_exists('acf_add_options_page')) {
    function acf_add_options_page(...$args)
    {}
}

if (! function_exists('get_fields')) {
    function get_fields(...$args)
    {}
}

if (! function_exists('get_permalink')) {
    function get_permalink(...$args)
    {}
}

if (! function_exists('get_bloginfo')) {
    function get_bloginfo($param = null)
    {}
}

if (! function_exists('wp_get_attachment_image_srcset')) {
    function wp_get_attachment_image_srcset(...$params)
    {}
}

if (! function_exists('esc_url')) {
    function esc_url(...$params)
    {}
}

if (! function_exists('get_post_thumbnail_id')) {
    function get_post_thumbnail_id(...$args)
    {}
}

if (! function_exists('get_the_title')) {
    function get_the_title(...$args)
    {}
}

if (! function_exists('get_the_post_thumbnail_url')) {
    function get_the_post_thumbnail_url(...$args)
    {}
}

if (! function_exists('get_the_post_thumbnail')) {
    function get_the_post_thumbnail(...$args)
    {}
}

if (! function_exists('has_post_thumbnail')) {
    function has_post_thumbnail(...$args)
    {}
}

if (! function_exists('is_admin')) {
    function is_admin()
    {}
}

if (! function_exists('add_action')) {
    function add_action(...$args)
    {}
}

if (! function_exists('remove_action')) {
    function remove_action(...$args)
    {}
}

if (! function_exists('add_filter')) {
    function add_filter(...$args)
    {}
}

if (! function_exists('wp_scripts')) {
    function wp_scripts()
    {}
}

if (! function_exists('get_post_meta')) {
    function get_post_meta(...$args)
    {}
}

if (! function_exists('get_the_ID')) {
    function get_the_ID()
    {}
}

if (! function_exists('get_queried_object')) {
    function get_queried_object()
    {}
}

if (! function_exists('__')) {
    function __(...$args)
    {}
}

if (! function_exists('register_post_type')) {
    function register_post_type(...$args)
    {}
}

if (! function_exists('register_nav_menu')) {
    function register_nav_menu(...$args)
    {}
}

if (! function_exists('add_image_size')) {
    function add_image_size(...$args)
    {}
}

if (! function_exists('wp_nav_menu')) {
    function wp_nav_menu(...$args)
    {}
}

if (! function_exists('get_posts')) {
    function get_posts(...$args)
    {}
}

if (! function_exists('get_post')) {
    function get_post(...$args)
    {}
}

if (! function_exists('get_query_var')) {
    function get_query_var(...$args)
    {}
}

if (! function_exists('add_theme_support')) {
    function add_theme_support(...$args)
    {}
}

if (! function_exists('paginate_links')) {
    function paginate_links(...$args)
    {}
}

if (! function_exists('get_query_var')) {
    function get_query_var(...$args)
    {}
}

if (! function_exists('get_previous_posts_link')) {
    function get_previous_posts_link(...$args)
    {}
}

if (! function_exists('get_next_posts_link')) {
    function get_next_posts_link(...$args)
    {}
}

if (! class_exists('WP_Post_Type')) {
    class WP_Post_Type
    {
        public $name;
    }
}

if (! class_exists('WP_Term')) {
    class WP_Term
    {
        public $taxonomy;
    }
}

if (! class_exists('WP_Post')) {
    class WP_Post
    {
        public $post_type, $slug;
    }
}

if (! class_exists('WP_Query')) {
    class WP_Query
    {
        public $max_num_pages;

        function __construct(...$args)
        {}

        function get_posts()
        {}
    }
}

<?php

namespace Platon\ServiceProviders;

use Platon\Application;

class OptimisationsServiceProvider extends ServiceProvider
{
    public function boot(Application $app)
    {
        if (! config('app.optimize') || is_admin()) {
            return;
        }

        // Move scripts to footer
        add_action('after_setup_theme', function () {
            remove_action('wp_head', 'wp_print_scripts');
            remove_action('wp_head', 'wp_print_head_scripts', 9);
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
            add_action('wp_footer', 'wp_print_scripts', 5);
            add_action('wp_footer', 'wp_enqueue_scripts', 5);
            add_action('wp_footer', 'wp_print_head_scripts', 5);
        });

        // Remove src from images and replace with data-src
        add_filter('wp_get_attachment_image_attributes', function ($attributes) {
            $attributes['data-src'] = $attributes['src'];
            $attributes['data-srcset'] = $attributes['srcset'] ?? null;
            unset($attributes['src'], $attributes['srcset']);
            $attributes['class'] .= ' lazy';

            return $attributes;
        });

        // Remove src from images in content and replace with data-src
        add_filter('the_content', function ($content) {
            return str_replace([' src', ' srcset'], [' data-src', ' data-srcset'], $content);
        });

//        add_filter('style_loader_tag', function ($html) {
//            // Ie sucks and cannot handle this
//            if (preg_match("/MSIE\s(?P<v>\d+)/i", $_SERVER['HTTP_USER_AGENT'])) {
//                return $html;
//            }
//
//            // MS Edge sucks and cannot handle this
//            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
//                return $html;
//            }
//
//            return preg_replace('/rel\=\'(.*?)\'/', 'rel="preload" as="style" onload="this.rel=\'stylesheet\';"', $html);
//        });

        // Apply lazy load script to footer
        add_action('wp_footer', function () {
            echo '<script>window.lazyLoadOptions = {elements_selector: ".lazy,.content img,.content iframe", callback_reveal: function (e) {if (e.dataset.style) {e.style.cssText = e.dataset.style;}}};</script><script async src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@12.0.0/dist/lazyload.min.js"></script>';
        });

        // Add preload to styles tags
//        add_filter('style_loader_tag', function ($html) {
//            // If the browser is ie, we dont want to preload because
//            // IE sucks and cannot handle this technique
//            if (preg_match("/MSIE\s(?P<v>\d+)/i", $_SERVER['HTTP_USER_AGENT'])) {
//                return $html;
//            }
//
//            // If the browser is MS Edge, we dont want to preload because
//            // MS Edge sucks and cannot handle this technique
//            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
//                return $html;
//            }
//
//            return preg_replace('/rel\=\'(.*?)\'/', 'rel="preload" as="style" onload="this.rel=\'stylesheet\'"', $html);
//        });

        // Prepare the content for lazy loading images
        add_filter('the_content', function ($content) {
            return str_replace(['src', 'srcset'], ['data-src', 'data-srcset'], $content);
        });

        // Prepare images for lazy loading
        add_filter('wp_get_attachment_image_attributes', function ($attributes) {
            return $attributes;
            $attributes['data-src'] = $attributes['src'];
            $attributes['data-srcset'] = $attributes['srcset'];
            $attributes['src'] = '';
            $attributes['srcset'] = '';
            $attributes['class'] .= ' lazy';

            return $attributes;
        });

        // Move jquery to the footer
        add_action('wp_enqueue_scripts', function () {
            wp_scripts()->add_data('jquery', 'group', 1);
            wp_scripts()->add_data('jquery-core', 'group', 1);
            wp_scripts()->add_data('jquery-migrate', 'group', 1);
        });
    }
}

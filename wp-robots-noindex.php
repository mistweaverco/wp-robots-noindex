<?php
/*
Plugin Name: WP Robots NoIndex
Version:     1.0.0
Plugin URI:  https://apps.walialu.com/wp-robots-noindex/
Description: Ask search engines not to index individual pages by checking an option in the publish post box.
Author:      Marco Kellershoff
Author URI:  https://about.walialu.com/
License:     MIT License
*/

if( !defined( 'ABSPATH' ) ) exit;

const ROBOTSNOINDEX_NOINDEX = 1;
const ROBOTSNOINDEX_NOFOLLOW = 2;

function robotsnoindex_post_types() {
        return apply_filters('noindex-pages-post-types', array('post', 'page'));
}

function robotsnoindex_enqueue_css() {
        $screen = get_current_screen();
        if ($screen->base == 'post') {
                wp_enqueue_style('robotsnoindex', plugins_url('robotsnoindex-post.css', __FILE__));
        }
}

add_action('admin_enqueue_scripts', 'robotsnoindex_enqueue_css');

function robotsnoindex_checkedhelper($checked=false) {
        return ($checked) ? ' checked="checked" ' : '';
}

function robotsnoindex_create_checkbox($name, $label, $checked=false) {
        echo '<div class="misc-pub-section misc-robotsnoindex">'
        . '<input type="checkbox" name="'.$name.'" id="'.$name.'"'
        .  robotsnoindex_checkedhelper($checked) . '/> '
        . '<label for="'.$name.'">'.$label.'</label>'
        . '</div>';
}

function robotsnoindex_display_meta_checkboxes() {
        global $post;
        if (empty($post->ID))
                return;
        robotsnoindex_create_checkbox(
                'robotsnoindex_post',
                'Hide from search engines',
                get_post_meta($post->ID, 'robotsnoindex', true)
        );
        robotsnoindex_create_checkbox(
                'robotsnofollow_post',
                'Forbid search enginges to follow links',
                get_post_meta($post->ID, 'robotsnofollow', true)
        );
}

add_action('post_submitbox_misc_actions', 'robotsnoindex_display_meta_checkboxes', 3);

function robotsnoindex_save_meta( $post_id ) {
        if (in_array( get_post_type( $post_id ), robotsnoindex_post_types() )) {
                $robotsnoindex_post = empty($_REQUEST['robotsnoindex_post']) ? 0 : 1;
                $robotsnofollow_post = empty($_REQUEST['robotsnofollow_post']) ? 0 : 1;
                if ($robotsnoindex_post) {
                        update_post_meta($post_id, 'robotsnoindex', 1);
                } else{
                        delete_post_meta($post_id, 'robotsnoindex');
                }
                if ($robotsnofollow_post) {
                        update_post_meta($post_id, 'robotsnofollow', 1);
                } else{
                        delete_post_meta($post_id, 'robotsnofollow');
                }
        }
}

add_action('save_post', 'robotsnoindex_save_meta');

function robotsnoindex_get($t) {
        switch ($t) {
        case ROBOTSNOINDEX_NOINDEX:
                if (get_post_meta(get_the_ID(), 'robotsnoindex', true)) {
                        return true;
                } else {
                        return false;
                }
                break;
        case ROBOTSNOINDEX_NOFOLLOW:
                if (get_post_meta(get_the_ID(), 'robotsnofollow', true)) {
                        return true;
                } else {
                        return false;
                }
                break;
        default:
                return false;
                break;
        }
}

function robotsnoindex_is_in_post_types() {
        $post_type = get_post_type();
        return ($post_type && in_array($post_type, robotsnoindex_post_types()));
}

function robotsnoindex_display_meta_tag() {
        if (!is_singular())
                return;
        $noindex = false;
        $nofollow = false;
        if (robotsnoindex_is_in_post_types()) {
                $noindex = robotsnoindex_get(ROBOTSNOINDEX_NOINDEX);
                $nofollow = robotsnoindex_get(ROBOTSNOINDEX_NOFOLLOW);
        }
        $content = (($noindex) ? 'noindex' : 'index') . ',' .
                (($nofollow) ? 'nofollow' : 'follow');
        echo '<meta name="robots" content="'.$content.'"/>' . "\n";
}

add_action('wp_head', 'robotsnoindex_display_meta_tag');


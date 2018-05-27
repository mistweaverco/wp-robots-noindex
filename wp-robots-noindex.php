<?php
/*
Plugin Name: WP Robots NoIndex
Version:     3.0.1
Plugin URI:  https://apps.walialu.com/wp-robots-noindex/
Description: Ask search engines not to index individual pages by checking an option in the publish post box.
Author:      Marco Kellershoff
Author URI:  https://about.walialu.com/
License:     MIT License
*/

if (!defined('ABSPATH')) exit;

const ROBOTSNOINDEX_NOINDEX = 1;
const ROBOTSNOINDEX_NOFOLLOW = 2;

function robotsnoindex_post_types() {
        return apply_filters('noindex-pages-post-types', array('post', 'page'));
}

function robotsnoindex_add_action($where, $what, $pos=false) {
        if ($pos == false) {
                add_action($where, $what);
        } else {
                add_action($where, $what, $pos);
        }
        return array($where, $what, $pos);
}

function robotsnoindex_enqueue_css() {
        $screen = get_current_screen();
        if ($screen->base == 'post') {
                wp_enqueue_style('robotsnoindex', plugins_url('robotsnoindex-post.css', __FILE__));
                return true;
        }
        return false;
}

robotsnoindex_add_action('admin_enqueue_scripts', 'robotsnoindex_enqueue_css');

function robotsnoindex_checkedhelper($checked=false) {
        return ($checked) ? ' checked="checked" ' : '';
}

function robotsnoindex_create_checkbox($name, $label, $checked=false) {
        return '<div class="misc-pub-section misc-robotsnoindex">'
        . '<input type="checkbox" name="'.$name.'" id="'.$name.'"'
        .  robotsnoindex_checkedhelper($checked) . '/> '
        . '<label for="'.$name.'">'.$label.'</label>'
        . '</div>';
}

function robotsnoindex_display_meta_checkboxes() {
        global $post;
        if (empty($post->ID)) {
                return false;
        }
        echo robotsnoindex_create_checkbox(
                'robotsnoindex_post',
                'Robots: NoIndex',
                get_post_meta($post->ID, 'robotsnoindex', true)
        );
        echo robotsnoindex_create_checkbox(
                'robotsnofollow_post',
                'Robots: NoFollow',
                get_post_meta($post->ID, 'robotsnofollow', true)
        );
        return true;
}

robotsnoindex_add_action('post_submitbox_misc_actions', 'robotsnoindex_display_meta_checkboxes', 3);

function robotsnoindex_save_meta($post_id) {
        $retval = array();
        if (in_array(get_post_type($post_id), robotsnoindex_post_types())) {
                $robotsnoindex_post = empty($_REQUEST['robotsnoindex_post']) ? 0 : 1;
                $robotsnofollow_post = empty($_REQUEST['robotsnofollow_post']) ? 0 : 1;
                if ($robotsnoindex_post) {
                        update_post_meta($post_id, 'robotsnoindex', 1);
                        $retval[] = true;
                } else{
                        delete_post_meta($post_id, 'robotsnoindex');
                        $retval[] = false;
                }
                if ($robotsnofollow_post) {
                        update_post_meta($post_id, 'robotsnofollow', 1);
                        $retval[] = true;
                } else{
                        delete_post_meta($post_id, 'robotsnofollow');
                        $retval[] = false;
                }
        }
        return $retval;
}

robotsnoindex_add_action('save_post', 'robotsnoindex_save_meta');

function robotsnoindex_get($t, $id=false) {
        if ($id==false) {
                $id = get_the_ID();
        }
        switch ($t) {
        case ROBOTSNOINDEX_NOINDEX:
                if (get_post_meta($id, 'robotsnoindex', true)) {
                        return true;
                } else {
                        return false;
                }
                break;
        case ROBOTSNOINDEX_NOFOLLOW:
                if (get_post_meta($id, 'robotsnofollow', true)) {
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

function robotsnoindex_display_meta_tag($id = 0, $force = false) {
        $retval = array();
        if (!is_singular() && $force == false) {
                return null;
        }
        if ($id == 0) {
                $id = get_the_ID();
        }
        $noindex = false;
        $nofollow = false;
        if (robotsnoindex_is_in_post_types()) {
                $noindex = robotsnoindex_get(ROBOTSNOINDEX_NOINDEX, $id);
                if ($noindex)
                        $retval[] = 1;
                else
                        $retval[] = 0;
                $nofollow = robotsnoindex_get(ROBOTSNOINDEX_NOFOLLOW, $id);
                if ($nofollow)
                        $retval[] = 1;
                else
                        $retval[] = 0;
        }
        $content = (($noindex) ? 'noindex' : 'index') . ',' .
                (($nofollow) ? 'nofollow' : 'follow');
        echo '<meta name="robots" content="'.$content.'"/>' . "\n";
        return $retval;
}

robotsnoindex_add_action('wp_head', 'robotsnoindex_display_meta_tag');


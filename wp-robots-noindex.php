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

function robotsnoindex_display_meta_checkbox() {
        global $post;
        if (empty($post->ID))
                return;
        $robotsnoindex_enabled = get_post_meta($post->ID, 'robotsnoindex', true);
        ?>
<div class="misc-pub-section misc-robotsnoindex">
        <input type="checkbox" name="robotsnoindex_post" id="robotsnoindex_post" <?php checked($robotsnoindex_enabled); ?> />
        <label for="robotsnoindex_post">Hide from search engines</label>
</div>
        <?php
}

add_action('post_submitbox_misc_actions', 'robotsnoindex_display_meta_checkbox', 3);

function robotsnoindex_save_meta( $post_id ) {
        if (in_array( get_post_type( $post_id ), robotsnoindex_post_types() )) {
                $robotsnoindex_post = empty($_REQUEST['robotsnoindex_post']) ? 0 : 1;
                if ($robotsnoindex_post) {
                        update_post_meta($post_id, 'robotsnoindex', 1);
                } else{
                        delete_post_meta($post_id, 'robotsnoindex');
                }
        }
}

add_action('save_post', 'robotsnoindex_save_meta');

function robotsnoindex_display_meta_tag() {
        if (!is_singular())
                return;
        $post_type = get_post_type();
        $hasNoIndexFlag = false;
        if ($post_type && in_array($post_type, robotsnoindex_post_types())) {
                $noindex = get_post_meta(get_the_ID(), 'robotsnoindex', true);
                if ( (int) $noindex === 1 ) {
                        $hasNoIndexFlag = true;
                }
        }
        if ($hasNoIndexFlag) {
                        echo '<meta name="robots" content="noindex" />' . "\n";
        } else {
                        echo '<meta name="robots" content="index,follow"/>' . "\n";
        }
}

add_action( 'wp_head', 'robotsnoindex_display_meta_tag' );


<?php
use PHPUnit\Framework\TestCase;

define('ABSPATH', 1);
function wp_enqueue_style() {}
function update_post_meta() {}
function delete_post_meta() {}
function plugins_url() {}
$post = new stdClass();
$screenOverride = false;
function get_the_ID() {return 1;}

function is_singular() {return false;}

function get_current_screen() {
        global $screenOverride;
        $screen = new stdClass();
        $screen->base = 'post';
        if ($screenOverride) {
                $screen->base = $screenOverride;
        }
        return $screen;
}

function apply_filters($tag, $value, $var=null) {
        return $value;
}

function add_action($where, $what, $position=1) {
        return true;
}

function get_post_type($id=1) {
        if ($id == 2308)
                return 'foo';
        if ($id == 511)
                return 'baz';
        return 'post';
}

function get_post_meta($id, $key, $single=false) {
        if ($id == 2308)
                return false;
        if ($id == 511)
                return false;
        if ($id == 8784)
                return false;
        return true;
}

?>


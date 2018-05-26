<?php
use PHPUnit\Framework\TestCase;

// Mock the WordPress Stuff
include_once('./tests/_wordpressMocks.php');
include_once('./wp-robots-noindex.php');

final class WpRobotsNoIndexTest extends TestCase
{
        public function test_robotsnoindex_post_types(): void
        {
                $this->assertEquals(
                        array('post', 'page'),
                        \robotsnoindex_post_types()
                );
        }
        public function test_robotsnoindex_enqueue_css(): void
        {
                $this->assertEquals(
                        true,
                        \robotsnoindex_enqueue_css()
                );
                global $screenOverride;
                $screenOverride = 'foobaz';
                $this->assertEquals(
                        false,
                        \robotsnoindex_enqueue_css()
                );
        }
        public function test_robotsnoindex_checkedhelper(): void
        {
                $this->assertEquals(
                        ' checked="checked" ',
                        \robotsnoindex_checkedhelper(true)
                );
        }
        public function test_robotsnoindex_add_action(): void
        {
                $this->assertEquals(
                        array('here', 'this', false),
                        \robotsnoindex_add_action('here', 'this')
                        );
                $this->assertEquals(
                        array('here', 'this', 1),
                        \robotsnoindex_add_action('here', 'this', 1)
                );
        }
        public function test_robotsnoindex_create_checkbox(): void
        {
                $this->assertEquals(
                        '<div class="misc-pub-section misc-robotsnoindex">'
                        . '<input type="checkbox" name="foo" id="foo"'
                        . ' checked="checked" /> '
                        . '<label for="foo">baz</label>'
                        . '</div>',
                        \robotsnoindex_create_checkbox('foo', 'baz', true)
                );
                $this->assertEquals(
                        '<div class="misc-pub-section misc-robotsnoindex">'
                        . '<input type="checkbox" name="foo" id="foo"'
                        . '/> '
                        . '<label for="foo">baz</label>'
                        . '</div>',
                        \robotsnoindex_create_checkbox('foo', 'baz', false)
                );
        }
        public function test_robotsnoindex_display_meta_checkboxes(): void
        {
                global $post;
                $this->assertEquals(
                        false,
                        \robotsnoindex_display_meta_checkboxes()
                );
                $post->ID = 1;
                $this->assertEquals(
                        true,
                        \robotsnoindex_display_meta_checkboxes()
                );
        }
        public function test_robotsnoindex_save_meta(): void
        {
                // is not of type post or page
                $this->assertEquals(
                        array(),
                        \robotsnoindex_save_meta(2308)
                );

                // only noindex
                $_REQUEST['robotsnoindex_post'] = 1;
                $this->assertEquals(
                        array(1, 0),
                        \robotsnoindex_save_meta(1)
                );
                // only nofollow
                unset($_REQUEST['robotsnoindex_post']);
                $_REQUEST['robotsnofollow_post'] = 1;
                $this->assertEquals(
                        array(0, 1),
                        \robotsnoindex_save_meta(1)
                );
                // both noindex and nofollow
                $_REQUEST['robotsnoindex_post'] = 1;
                $this->assertEquals(
                        array(1, 1),
                        \robotsnoindex_save_meta(1)
                );
        }
        public function test_robotsnoindex_get(): void
        {
                $this->assertEquals(
                        true,
                        \robotsnoindex_get(ROBOTSNOINDEX_NOINDEX)
                );
                $this->assertEquals(
                        true,
                        \robotsnoindex_get(ROBOTSNOINDEX_NOFOLLOW)
                );
                $this->assertEquals(
                        false,
                        \robotsnoindex_get(ROBOTSNOINDEX_NOINDEX, 2308)
                );
                $this->assertEquals(
                        false,
                        \robotsnoindex_get(ROBOTSNOINDEX_NOFOLLOW, 511)
                );
                $this->assertEquals(
                        false,
                        \robotsnoindex_get(8784, 511)
                );
        }
        public function test_robotsnoindex_is_in_post_types(): void
        {
                $this->assertEquals(
                        true,
                        \robotsnoindex_is_in_post_types()
                );
        }
        public function test_robotsnoindex_display_meta_tag(): void
        {
                $this->assertEquals(
                        array(1, 1),
                        \robotsnoindex_display_meta_tag(1, true)
                );
                $this->assertEquals(
                        array(0, 0),
                        \robotsnoindex_display_meta_tag(8784, true)
                );
                $this->assertEquals(
                        array(1, 1),
                        \robotsnoindex_display_meta_tag(0, true)
                );
                // mock is singular
                $this->assertEquals(
                        null,
                        \robotsnoindex_display_meta_tag()
                );
        }

}

?>


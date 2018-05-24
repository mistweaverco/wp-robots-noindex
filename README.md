WP-Robots-NoIndex
=================

A WordPress-Plugin for flagging individual pages and/or posts,
so they will output a `<meta name="robots" content="noindex" />` tag.

This tells the search-engines not to index you page/post.

If you don't tick the checkbox (this is the default),
it'll output a `<meta name="robots" content="index,follow"/>` tag,
which tells the search-engines to index your post/page and follow all the
links.

If you happen to have a completely customized theme,
you can simply call this function

```php
<?php
        robotsnoindex_display_meta_tag();
        // echoes either
        // <meta name="robots" content="noindex" />
        // or
        // <meta name="robots" content="index,follow"/>
?>
```


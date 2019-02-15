<?php
/**
 * Full\hard\default version
 */

echo '<!-- Meta tags: begin -->'.PHP_EOL;

// Display title tag, $cfg['title_separator'] uses as separator for parts of title array
echo '<title>'.$title.'</title>'.PHP_EOL;

// Add slash at end of tag?
$slash_at_end = $slash_at_end ? '/' : '';

// Display meta tags
foreach ($tags as $attributes)
{
    echo '<meta'.HTML::attributes($attributes).$slash_at_end.'>'.PHP_EOL;
}

echo '<!-- Meta tags: end -->'.PHP_EOL;

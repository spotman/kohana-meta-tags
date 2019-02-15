<?php
/**
 * Easy\light\alternative version.
 */

echo '<!-- Meta tags: begin -->'.PHP_EOL;

// Display title tag, ' - ' uses as separator for parts of title array
echo '<title>'.$title.'</title>'.PHP_EOL;

// Display meta tags
foreach ($tags as $attributes) {
    echo '<meta'.HTML::attributes($attributes).'/>'.PHP_EOL;
}

echo '<!-- Meta tags: end -->'.PHP_EOL;

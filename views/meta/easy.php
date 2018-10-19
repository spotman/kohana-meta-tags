<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Easy\light\alternative version.
 */
// Sets cache lifetime in seconds
$cache_lifetime = Kohana::$caching ? 300 : 0;
// Check\load cache (work only in production version)

echo '<!-- Meta tags: begin -->'.PHP_EOL;
// Display title tag, ' - ' uses as separator for parts of title array
if (isset($tags['title'])) {
    echo '<title>'.HTML::chars(implode(' - ', (array)$tags['title'])).'</title>'.PHP_EOL;
    unset($tags['title']);
}
// Display meta tags
foreach ($tags as $attributes) {
    echo '<meta'.HTML::attributes($attributes).'/>'.PHP_EOL;
}
echo '<!-- Meta tags: end -->'.PHP_EOL;

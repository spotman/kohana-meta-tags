##Meta tags module for Kohana framework 3.х

###Installation and setup:
Unzip the archive and place the contents into a directory with modules(`DOCROOT/modules/`). 
If necessary, copy `config/meta.php` in `APPPATH/config/meta.php` and change it.

###Basic usage:
Instance class. Optional, you can set new config options.
~~~
$meta = Meta::instance($config);
~~~
Set tag:
~~~
$meta->set('description', 'description text');
$meta->content-language = I18n::$lang;
~~~
Sets title content as string or array.
~~~
$meta->set('title', array('Site name', 'Page name'));
$meta->set('title', 'Site name - Page name');
~~~
Set tags:
~~~
$meta->set(array('author' => 'WinterSilence', 'generator' => 'Kohana 3.1'));
~~~
Get tag:
~~~
$meta->get('description');
$tag_content = $meta->description;
~~~
Get all tag:
~~~
$all_tags = $meta->get();
~~~
Unset:
~~~
unset($meta->description);
~~~
Isset:
~~~
if (isset($meta->description))
{
	// ...
}
~~~
Render:
~~~
echo $meta->render();
echo $meta; // called __toString()
~~~

###Helpers:
Parces meta tags from document:
~~~
$tags = Meta::parce($url);
~~~
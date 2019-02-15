<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Class for work with HTML meta tags.
 * For get more info about meta tags visit [http://wikipedia.org/wiki/Meta_element](http://wikipedia.org/wiki/Meta_element).
 *
 * @package    Kohana/Meta
 * @category   Base
 * @version    1.4
 * @author     WinterSilence <info@handy-soft.ru>
 * @author     Samuel Demirdjian
 * @copyright  2013 © handy-soft.ru
 * @license    MIT
 * @link       http://github.com/WinterSilence/kohana-meta-tags
 */
class Meta
{
    /**
     * Uses in title method
     */
    public const TITLE_REPLACE = 0;
    public const TITLE_UNSHIFT = 1;
    public const TITLE_PREPEND = 1; // Same as unshift
    public const TITLE_PUSH    = 2;
    public const TITLE_APPEND  = 2; // Same as push

    /**
     * @var  array  Configuration options
     */
    private $cfg;

    /**
     * @var  string[][]  Meta tags
     */
    private $tags = [];

    private $title = [];

    /**
     * @var string[][]
     */
    private $links = [];

    /**
     * Load configuration and default tags
     *
     * @return void
     * @uses   Kohana
     * @uses   Config
     * @uses   Config_Group
     */
    public function __construct()
    {
        $this->cfg = (array)Kohana::$config->load('meta');
        $this->loadFromConfig($this->cfg['tags_config_groups']);
    }

    /**
     * Load tags from config
     *
     *     Meta::instance()->loadFromConfig('cms.meta_tags');
     *     Meta::instance()->loadFromConfig(array('meta_tags', 'blog.meta'));
     *
     * @param  string|array $group Config name or an array of them
     *
     * @return Meta
     * @uses   Kohana
     * @uses   Config
     * @uses   Config_Group
     */
    public function loadFromConfig(array $group): \Meta
    {
        $tags = [];
        // Merge configs data
        foreach ((array)$group as $name) {
            $config = Kohana::$config->load($name);

            if ($config instanceof Config_Group) {
                $config = $config->as_array();
            }

            $tags[] = (array)$config;
        }

        $tags = array_merge(...$tags);

        // Set tags
        foreach ($tags as $name => $value) {
            $this->set($name, $value);
        }

        // Return self
        return $this;
    }

    /**
     * Set tags
     *
     * @param  string $tag   Tag name
     * @param  string $value Content attribute
     *
     * @return void
     */
    public function set(string $tag, ?string $value): void
    {
        if (!$value) {
            return;
        }

        $tag = strtolower($tag);

        $group = $this->detectTagGroup($tag);

        // Add meta tag
        $this->tags[$tag] = [
            $group    => $tag,
            'content' => $value,
        ];
    }

    /**
     * Get tags
     *
     * @param  string $name
     *
     * @return mixed
     */
    public function get($name = null)
    {
        if ($name === null) {
            // Get all nonempty tags
            return array_filter($this->tags);
        }

        return $this->tags[$name] ?? null;
    }

    /**
     * Delete tags
     *
     * @param  string $name
     *
     * @return Meta
     */
    public function delete(string $name): \Meta
    {
        unset($this->tags[$name]);

        return $this;
    }

    /**
     * Wrapper for get\set title tag
     *
     * @param   string  $title  New title value
     * @param   integer $method Action type for title array
     *
     * @return  void
     */
    public function setTitle(string $title, int $method = null): void
    {
        $method = $method ?? self::TITLE_REPLACE;

        $newTitle = (array)$title;

        switch ($method) {
            case static::TITLE_UNSHIFT:
                // Merge, the new one will be prepended (like array_unshift)
                $this->title = array_merge($newTitle, $this->title);
                break;
            case static::TITLE_PUSH:
                // Merge, the new one will be appended (like array_push)
                $this->title = array_merge($this->title, $newTitle);
                break;
            default: // Case Meta::TITLE_REPLACE:
                // Replace
                $this->title = $newTitle;
        }
    }

    public function setDescription(string $text): void
    {
        $this->set('description', $text);
        $this->set('og:description', $text);
        $this->set('twitter:description', $text);
    }

    public function setImage(string $url): void
    {
        $this->set('og:image', $url);
        $this->set('twitter:image', $url);
    }

    public function setContentType(string $mimeType, string $encoding = null): void
    {
        if (!$encoding) {
            $encoding = Kohana::$charset;
        }

        $this->set('content-type', $mimeType.'; charset='.$encoding);
    }

    /**
     * Set <link rel="canonical"> value
     *
     * @param string    $href
     * @param bool|null $overwrite
     *
     * @return \Meta
     */
    public function setCanonical(string $href, ?bool $overwrite = null): self
    {
        // Prevent overwrite of `canonical` value by error pages and nested IFaces
        if (!$overwrite && $this->hasLink('canonical')) {
            return $this;
        }

        $this->set('og:url', $href);

        return $this->addLink('canonical', $href);
    }

    public function addLink(string $rel, string $href, ?array $attributes = null): self
    {
        $attributes = $attributes ?? [];

        $attributes['rel']  = $rel;
        $attributes['href'] = $href;

        $this->links[] = $attributes;

        return $this;
    }

    public function hasLink(string $rel): bool
    {
        foreach ($this->links as $attributes) {
            if ($attributes['rel'] === $rel) {
                return true;
            }
        }

        return false;
    }

    public function render(): string
    {
        return $this->renderTags()."\r\n".$this->renderLinks();
    }

    /**
     * Allows a class to decide how it will react when it is treated like a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    private function detectTagGroup(string $name): string
    {
        if (in_array($name, $this->cfg['http-equiv'], true)) {
            return 'http-equiv';
        }

        if (strpos($name, 'twitter:') === 0) {
            return 'name';
        }

        if (strpos($name, 'og:') === 0) {
            return 'property';
        }

        return 'name';
    }

    /**
     * Render template(View) with meta data.
     *
     * @return  string
     * @uses    View
     */
    private function renderTags(): string
    {
        $title = HTML::chars(implode($this->cfg['title_separator'], $this->title));

        return View::factory($this->cfg['template'])
            ->set('title', $title)
            ->set('tags', array_filter($this->tags))
            ->set('slash_at_end', $this->cfg['slash_at_end'])
            ->render();
    }

    private function renderLinks(): string
    {
        $output = [];

        foreach ($this->links as $item) {
            $output[] = '<link'.HTML::attributes($item).' />';
        }

        return implode("\r\n", $output);
    }
} // End Meta

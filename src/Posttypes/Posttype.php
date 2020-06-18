<?php

namespace Platon\Posttypes;

class Posttype
{

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $public = true;

    /**
     * @var int
     */
    protected $position = 25;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var bool
     */
    protected $exportable = true;

    /**
     * @var bool
     */
    protected $deleteWithUser = false;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var string
     */
    protected $capabilityType = 'post';

    /**
     * @var bool
     */
    protected $hasArchives = false;

    /**
     * @var bool
     */
    protected $queryVar = false;

    /**
     * @var array
     */
    protected $supports = [
        'title',
        'editor',
        'excerpt',
        'author',
        'thumbnail',
        'comments',
        'trackbacks',
        'custom-fields',
        'revisions',
        'page-attributes',
        'post-formats'
    ];

    /**
     * @var string
     */
    protected $singular;

    /**
     * @var string
     */
    protected $plural;

    /**
     * Posttype constructor.
     *
     * @param string $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @param array<string> ...$supports
     *
     * @return $this
     */
    public function supports(...$supports)
    {
        $this->supports = $supports;

        return $this;
    }

    /**
     * @param string $singular
     *
     * @return $this
     */
    public function singular($singular)
    {
        $this->singular = mb_strtolower($singular, 'utf-8');

        return $this;
    }

    /**
     * @param string $plural
     *
     * @return $this
     */
    public function plural($plural)
    {
        $this->plural = mb_strtolower($plural, 'utf-8');

        return $this;
    }

    /**
     * @return $this
     */
    public function isPrivate()
    {
        $this->public = false;

        return $this;
    }

    /**
     * @param int $positon
     *
     * @return $this
     */
    public function position($positon)
    {
        $this->position = $positon;

        return $this;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return $this
     */
    public function isExportable()
    {
        $this->exportable = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function isNotExportable()
    {
        $this->exportable = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function deleteWithUser()
    {
        $this->deleteWithUser = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function hierarchical()
    {
        $this->hierarchical = true;

        return $this;
    }

    /**
     * @param string $capability
     *
     * @return $this
     */
    public function capability($capability)
    {
        $this->capabilityType = $capability;

        return $this;
    }

    /**
     * @return string
     */
    protected function getPlural()
    {
        return $this->plural ?? $this->singular ?? $this->slug;
    }

    /**
     * @return string
     */
    protected function getSingular()
    {
        return $this->singular ?? $this->plural ?? $this->slug;
    }

    /**
     * @return void
     */
    public function register()
    {
        register_post_type(
            $this->slug, [
                'public' => $this->public,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'menu_position' => $this->position,
                'menu_icon' => $this->icon,
                'can_export' => $this->exportable,
                'delete_with_user' => $this->deleteWithUser,
                'hierarchical' => $this->hierarchical,
                'has_archive' => $this->hasArchives,
                'query_var' => $this->queryVar,
                'capability_type' => $this->capabilityType,
                'supports' => $this->supports,

                'labels' => [
                    'name' => __(ucfirst($this->getPlural()), config('app.slug')),
                    'singular_name' => __(ucfirst($this->getSingular()), config('app.slug')),
                    'menu_name' => __(ucfirst($this->getPlural()), config('app.slug')),
                    'name_admin_bar' => __(ucfirst($this->getPlural()), config('app.slug')),
                    'add_new' => __("Lägg till {$this->getSingular()}", config('app.slug')),
                    'add_new_item' => __("Lägg till {$this->getSingular()}", config('app.slug')),
                    'edit_item' => __( "Redigera {$this->getSingular()}", config('app.slug')),
                    'new_item' => __( "Ny {$this->getSingular()}", config('app.slug')),
                    'view_item' => __( "Visa {$this->getSingular()}", config('app.slug')),
                    'search_items' => __( "Sök {$this->getSingular()}", config('app.slug')),
                    'not_found' => __( "Inga {$this->getPlural()} hittades", config('app.slug')),
                    'not_found_in_trash' => __( "Inga {$this->getPlural()} hittades i papperskorgen", 'example-textdomain' ),
                    'all_items' => __( "Alla {$this->getPlural()}", config('app.slug')),
                    'parent_item'        => __("Parent {$this->getSingular()}", config('app.slug')),
                    'parent_item_colon'  => __("Parent {$this->getSingular()}:", config('app.slug')),
                    'archive_title'      => __(ucfirst($this->getPlural()), config('app.slug')),
                ]
            ]
        );
    }
}
<?php

namespace Platon\Posttypes;

use Platon\Media\ImageRegistrator;

class Posttype
{
    /**
     * @var string
     */
    protected $id;

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
     * @var boolean
     */
    protected $showInRest = false;

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
     * @var array
     */
    protected $taxonomies = [];

    /**
     * Posttype constructor.
     *
     * @param string $slug
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param \Platon\Posttypes\Taxonomy $taxonomy
     *
     * @return $this
     */
    public function taxonomy(Taxonomy $taxonomy)
    {
        $this->taxonomies[] = $taxonomy;

        return $this;
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
        return $this->plural ?? $this->singular ?? $this->id;
    }

    /**
     * @return string
     */
    protected function getSingular()
    {
        return $this->singular ?? $this->plural ?? $this->id;
    }

    /**
     * @return $this
     */
    public function hasArchives()
    {
        $this->hasArchives = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function hasIndexPage()
    {
        $this->hasArchives = true;

        return $this;
    }

    /**
     * @param $slug
     *
     * @return $this
     */
    public function slug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @param  boolean $value
     *
     * @return $this
     */
    public function useGutenberg($value = true)
    {
        $this->showInRest = $value;

        return $this;
    }

    /**
     * @return void
     */
    public function register()
    {
        register_post_type(
            $this->id, [
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
                'show_in_rest' => $this->showInRest,
                'supports' => $this->supports,
                'rewrite' => [
                    'slug' => $this->slug ?? $this->id
                ],

                'labels' => [
                    'name' => trans(ucfirst($this->getPlural())),
                    'singular_name' => trans(ucfirst($this->getSingular())),
                    'menu_name' => trans(ucfirst($this->getPlural())),
                    'name_admin_bar' => trans(ucfirst($this->getPlural())),
                    'add_new' => trans("Lägg till {$this->getSingular()}"),
                    'add_new_item' => trans("Lägg till {$this->getSingular()}"),
                    'edit_item' => trans( "Redigera {$this->getSingular()}"),
                    'new_item' => trans( "Ny {$this->getSingular()}"),
                    'view_item' => trans( "Visa {$this->getSingular()}"),
                    'search_items' => trans( "Sök {$this->getSingular()}"),
                    'not_found' => trans( "Inga {$this->getPlural()} hittades"),
                    'not_found_in_trash' => trans( "Inga {$this->getPlural()} hittades i papperskorgen", 'example-textdomain' ),
                    'all_items' => trans( "Alla {$this->getPlural()}"),
                    'parent_item'        => trans("Parent {$this->getSingular()}"),
                    'parent_item_colon'  => trans("Parent {$this->getSingular()}:"),
                    'archive_title'      => trans(ucfirst($this->getPlural())),
                ]
            ]
        );

        foreach ($this->taxonomies as $taxonomy)
        {
            $taxonomy->register($this->id);
        }

        if (in_array('thumbnail', $this->supports)) {
            app(ImageRegistrator::class)->support($this->id);
        }
    }
}

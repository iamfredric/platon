<?php

namespace Platon\Posttypes;

class Taxonomy
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $singular;

    /**
     * @var string
     */
    protected $plural;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $queryVar = false;

    /**
     * @var bool
     */
    protected $public = true;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var bool
     */
    protected $showUi = true;

    /**
     * @var bool
     */
    protected $showTagCloud = false;

    /**
     * @var null
     */
    protected $slug = null;

    /**
     * Taxonomy constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
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
     * @param string $description
     *
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param $queryVar
     *
     * @return $this
     */
    public function queryVar($queryVar)
    {
        $this->queryVar = $queryVar;

        return $this;
    }

    /**
     * @return $this
     */
    public function isPublic()
    {
        $this->public = true;

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
     * @return $this
     */
    public function multilevel()
    {
        $this->hierarchical = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function isHierarchical()
    {
        $this->hierarchical = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function flat()
    {
        $this->hierarchical = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function isNotHierarchical()
    {
        $this->hierarchical = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function showUi()
    {
        $this->showUi = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontShowUi()
    {
        $this->showUi = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function showTagCloud()
    {
        $this->showTagCloud = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontShowTagCloud()
    {
        $this->showTagCloud = false;

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function slug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @param $posttype
     */
    public function register($posttype)
    {
        register_taxonomy($this->id, $posttype, [
            'labels'        => [
                'name'                  => trans(ucfirst($this->plural)),
                'singular_name'         => trans(ucfirst($this->singular)),
                'search_items'          => trans($this->plural),
                'popular_items'         => trans("Populära {$this->plural}"),
                'all_items'             => trans("Alla {$this->plural}"),
                'parent_item'           => trans("Förälder{$this->singular}"),
                'parent_item_colon'     => trans("Förälder{$this->singular}"),
                'edit_item'             => trans("Redigera {$this->singular}"),
                'view_item'             => trans("Visa {$this->singular}"),
                'update_item'           => trans("Uppdatera {$this->singular}"),
                'add_new_item'          => trans("Lägg till {$this->singular}"),
                'new_item_name'         => trans("Nytt {$this->singular} namn"),
                'add_or_remove_items'   => trans("Lägg till/ta bort {$this->singular}"),
                'choose_from_most_used' => trans("Visa från mest använda {$this->plural}"),
                'not_found'             => trans("Ingen {$this->singular} hittades"),
                'no_terms'              => trans("Inga {$this->plural}"),
            ],
            'query_var'     => $this->queryVar,
            'description'   => $this->description,
            'public'        => $this->public,
            'hierarchical'  => $this->hierarchical,
            'show_ui'       => $this->showUi,
            'show_tagcloud' => $this->showTagCloud,
            'rewrite' => [
                'slug' => $this->slug ?: $this->id
            ]
        ]);
    }
}

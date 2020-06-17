<?php

namespace Platon\Menus;

class MenuRegistrator
{
    /**
     * @var string
     */
    private $themeId;

    /**
     * MenuRegistrator constructor.
     *
     * @param $themeId
     */
    public function __construct($themeId)
    {
        $this->themeId = $themeId;
    }

    /**
     * @param $slug
     * @param $label
     *
     * @return void
     */
    public function register($slug, $label)
    {
        register_nav_menu($slug, __($label, $this->themeId));
    }

    /**
     * @param $slug
     * @param array $args
     *
     * @return mixed
     */
    public function render($slug, $args = [])
    {
        return wp_nav_menu(array_merge([
            'theme_location' => $slug,
            'container' => null,
            'items_wrap' => '%3$s'
        ], $args));
    }
}

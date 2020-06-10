<?php

if (! function_exists('config')) {
    function config($key, $default = null)
    {
        $keys = explode('.', $key);
        $name = array_shift($keys);

        $filepath = theme_path("config/{$name}.php");

        if (! file_exists($filepath)) {
            return $default;
        }

        $value = require $filepath;

        foreach ($keys as $key) {
            if (! isset($value[$key])) {
                return $default;
            }

            $value = $value[$key];
        }

        return $value;
    }
}

if (! function_exists('theme_path')) {
    function theme_path($path = '')
    {
        return rtrim(get_stylesheet_directory(), '/') . '/' . trim($path, '/');
    }
}

if (! function_exists('uploads_path')) {
    /**
     * Basig helper for getting absoulte uploads path
     *
     * @param string $path
     *
     * @return string
     */
    function uploads_path($path = '')
    {
        $directory = wp_upload_dir();

        return rtrim($directory['basedir'], '/') . '/' . trim($path, '/');
    }
}

if (! function_exists('app'))
{
    /**
     * @param null $abstract
     *
     * @return \Platon\Application|mixed
     */
    function app($abstract = null)
    {
        $instance = \Platon\Application::getInstance();

        return $abstract ? $instance->make($abstract) : $instance;
    }
}
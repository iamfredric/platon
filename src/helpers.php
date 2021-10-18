<?php

if (! function_exists('config')) {
    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|null
     */
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
    /**
     * @param string $path
     *
     * @return string
     */
    function theme_path($path = '')
    {
        if (function_exists('get_stylesheet_directory')) {
            return rtrim(get_stylesheet_directory(), '/') . '/' . trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('uploads_path')) {
    /**
     * Basic helper for getting absoulte uploads path
     *
     * @param string $path
     *
     * @return string
     */
    function uploads_path($path = '')
    {
        if (function_exists('wp_upload_dir')) {
            $directory = wp_upload_dir();

            return rtrim($directory['basedir'], '/') . '/' . trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('app'))
{
    /**
     * @param string $abstract
     *
     * @return mixed|object|\Platon\Application
     * @throws \ReflectionException
     */
    function app($abstract = null)
    {
        $instance = \Platon\Application::getInstance();

        return $abstract ? $instance->make($abstract) : $instance;
    }
}

if (! function_exists('theme_url')) {
    /**
     * Basic helper for getting the theme url
     *
     * @param string $url optional
     *
     * @return string
     */
    function theme_url($url = '')
    {
        return (string) \Illuminate\Support\Str::of(get_bloginfo('stylesheet_directory'))
                                               ->append("/$url")
                                               ->rtrim('/');
    }
}

if (! function_exists('assets')) {
    /**
     * @param string $file
     *
     * @return string
     */
    function assets($file)
    {
        $file = ltrim($file, '/');

        return (string) \Illuminate\Support\Str::of($file)
                                               ->ltrim('/')
                                               ->replace('//', '/')
                                               ->prepend(theme_url(config('paths.assets')).'/');
    }
}

if (! function_exists('mix')) {
    /**
     * @param string $originalFilename
     *
     * @return string
     */
    function mix($originalFilename)
    {
        $filename = '/'.ltrim($originalFilename, '/');

        $manifestFile = theme_path(config('paths.assets') . '/mix-manifest.json');

        if (! file_exists($manifestFile)) {
            return assets($originalFilename);
        }

        $manifest = json_decode(file_get_contents($manifestFile));


        return isset($manifest->{$filename})
            ? assets($manifest->{$filename})
            : assets($originalFilename);
    }
}

if (! function_exists('view'))
{
    /**
     * @param string $name
     * @param array $args
     *
     * @return mixed|object|\Platon\Application
     */
    function view($name = null, $args = [])
    {
        $view = app('view');

        if (! $name) {
            return $view;
        }

        return $view->make($name, $args);
    }
}

if (! function_exists('trans'))
{
    /**
     * @param string $string
     */
    function trans($string)
    {
        do_action('platon_translate', $string, config('app.theme-slug'));

        return __($string, config('app.theme-slug')) ?: $string;
    }
}

if (! function_exists('csrf_token')) {
    function csrf_token()
    {
        if(session_id() == '') {
            session_start();
        }

        if (! $_SESSION['_token'] ?? null) {
            $_SESSION['_token'] = \Illuminate\Support\Str::random(64);
        }

        return $_SESSION['_token'];
    }
}

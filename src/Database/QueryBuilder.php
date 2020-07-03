<?php

namespace Platon\Database;

use Illuminate\Support\Collection;

class QueryBuilder
{
    /**
     * Query arguments
     *
     * @var array
     */
    protected $arguments = [
        'suppress_filters' => false
    ];

    /**
     * Meta query arguments
     *
     * @var array
     */
    protected $metaArguments = [];

    /**
     * @var \Platon\Database\Model|null
     */
    protected $model;

    /**
     * QueryBuilder constructor.
     *
     * @param \Platon\Database\Model|null $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model;

        if ($model) {
            $this->setArgument('post_type', $model->getType());
        }
    }

    /**
     * Fetches the first record from database
     *
     * @return \Platon\Database\Model|null
     */
    public function first()
    {
        $this->setArgument('posts_per_page', 1);

        if ($posts = get_posts($this->getArguments())) {
            return $this->buildItem($posts[0]);
        }

        return null;
    }

    /**
     * @param int $id
     * @param \Platon\Database\Model|null $model
     *
     * @return mixed
     */
    public static function find($id, $model = null)
    {
        $instance = new static($model);

        return $instance->buildItem(get_post($id));
    }

    /**
     * Fetches the items from the database based on given queries
     *
     * @return Collection
     */
    public function get()
    {
        $posts = new Collection();

        foreach ((array) get_posts($this->getArguments()) as $post) {
            $posts->push($this->buildItem($post));
        }

        return $posts;
    }

    /**
     * @param int|null $limit
     *
     * @return \Platon\Database\Paginaton
     */
    public function paginate($limit = null)
    {
        $posts = [];

        $this->setArgument('posts_per_page', $limit);
        $this->setArgument('paged', get_query_var('paged') ?: 1);
        $query = new \WP_Query($this->getArguments());

        foreach ((array) $query->get_posts() as $post) {
            $posts[] = $this->buildItem($post);
        }

        return new Paginaton($posts, $query);
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit($limit)
    {
        $this->setArgument('posts_per_page', $limit);

        return $this;
    }

    /**
     * @param mixed $post
     *
     * @return mixed
     */
    protected function buildItem($post)
    {
        if ($this->model) {
            $class = get_class($this->model);

            return $class::make($post);
        }

        return Model::make($post);
    }

    /**
     * Fetches all items from database
     *
     * @param mixed $model
     *
     * @return mixed
     */
    public static function all($model = null)
    {
        $instance = new static($model);

        $instance->setArgument('posts_per_page', -1);

        return $instance->get();
    }

    /**
     * Getter for arguments
     *
     * @return array
     */
    public function getArguments()
    {
        $args = $this->arguments;

        if (count($this->metaArguments)) {
            $args['meta_query'] = $this->metaArguments;
        }

        return $args;
    }

    /**
     * Setter for argument
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;
    }

    /**
     * Query builder
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    protected function buildWhere($key, $value)
    {
        $this->setArgument($key, $value);

        return $this;
    }

    /**
     * Metaquery builder
     *
     * @param array $args
     *
     * @return $this
     */
    protected function buildMetaWhere($args)
    {
        $key = $args[0];
        $compare = count($args) == 3 ? $args[1] : '=';
        $value = count($args) == 3 ? $args[2] : $args[1];

        $this->metaArguments[] = [
            'key' => $key,
            'value' => $value,
            'compare' => $compare
        ];

        return $this;
    }

    /**
     * Resolves what magic method is called
     *
     * @param string $method
     * @param mixed $args
     *
     * @return QueryBuilder
     */
    protected function resolveMethodCall($method, $args)
    {
        list($key, $value) = $this->resolveArgumentsFromMethodCall($method, $args);

        if ($key == 'meta') {
            return $this->buildMetaWhere($args);
        }

        if ($key == 'limit') {
            return $this->limit($args[0]);
        }

        return $this->buildWhere($key, $value);
    }

    /**
     * Resolves arguments from magic method call
     *
     * @param string $method
     * @param mixed $arguments
     * @param array $args
     *
     * @return array
     */
    public function resolveArgumentsFromMethodCall($method, $arguments, $args = [])
    {
        if ($method = str_replace('where', '', $method)) {
            $args[] = strtolower($method);
        }

        foreach ($arguments as $argument) {
            $args[] = $argument;
        }

        return $args;
    }

    /**
     * @param string $method
     * @param mixed $args
     *
     * @return QueryBuilder
     */
    public function __call($method, $args)
    {
        return $this->resolveMethodCall($method, $args);
    }

    /**
     * @param string $method
     * @param mixed $args
     *
     * @return QueryBuilder|Collection|\Platon\Database\Model
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static();

        return $instance->__call($method, $args);
    }
}
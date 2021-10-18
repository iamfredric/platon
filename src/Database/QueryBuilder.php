<?php

namespace Platon\Database;

use Illuminate\Support\Collection;

class QueryBuilder
{
    use Macroable;

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

        if ($post = get_post($id)) {
            return $instance->buildItem($post);
        }

        return null;
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
        $query = WpQuery::make($this->getArguments());

        foreach ((array) $query->get_posts() as $post) {
            $posts[] = $this->buildItem($post);
        }

        return new Paginaton($posts, $query);
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

    public function setMetaArgument($key, $compare, $value = null)
    {
        $this->metaArguments[] = [
            'key' => $key,
            'value' => $value === null ? $compare : $value,
            'compare' => $value === null ? '=' : $compare
        ];
    }

    /**
     * @param $key
     * @param $compare
     * @param string|null $value
     *
     * @return void
     */
    public function scopeWhereMeta($key, $compare, $value = null)
    {
        $this->setMetaArgument($key, $compare, $value);
    }

    /**
     * @param $orderBy
     * @param string $direction
     *
     * @return void
     */
    public function scopeOrderBy($orderBy, $direction = 'asc')
    {
        $this->setArgument('orderby', $orderBy);
        $this->setArgument('order', strtolower($direction) === 'asc' ? 'ASC' : 'DESC');
    }

    /**
     * @return void
     */
    public function scopeWhere($key, $value)
    {
        $this->setArgument($key, $value);
    }

    /**
     * @param $limit
     *
     * @return void
     */
    public function scopeLimit($limit)
    {
        $this->setArgument('posts_per_page', $limit);
    }

    /**
     * @param string $orderBy
     *
     * @return void
     */
    public function scopeLatest($orderBy = 'date')
    {
        $this->orderBy($orderBy, 'desc');
    }

    /**
     * @param string $orderBy
     *
     * @return void
     */
    public function scopeOldest($orderBy = 'date')
    {
        $this->orderBy($orderBy, 'asc');
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
        if ($this->hasMacro($method)) {
            $this->resolveMacro($method, $this, ...$args);
        } elseif (method_exists($this, $name = 'scope'. ucfirst($method))) {
            $this->{$name}(...$args);
        }

        return $this;
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

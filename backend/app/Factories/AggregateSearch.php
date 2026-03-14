<?php

namespace App\Factories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AggregateSearch
{
    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var array
     */
    protected $selects = [];

    /**
     * Create a new factory instance
     *
     * @return void
     */
    public function __construct(string $modelClass)
    {
        $this->model = new $modelClass;
        $this->registerQueries();
    }

    /**
     * Registers the initial queries for the given model
     *
     * @return void
     */
    protected function registerQueries()
    {
        $relations = static::relations($this->model);

        foreach ($relations as $relation) {
            $this->addRelation($this->model->{$relation}());
        }

        $keywords = static::keywords($this->model);

        $this
            ->addSelect($this->model->getQualifiedKeyName(), 'id')
            ->addSelect("CONCAT_WS(\" \", {$keywords})", 'keywords')
            ->addSelect('?', 'type', [$this->getClass()]);
    }

    /**
     * Create new instance of the factory
     */
    public static function create(string $modelClass)
    {
        return new static($modelClass);
    }

    /**
     * Add new relational query for the given relation instance
     *
     * @param  Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @return $this
     */
    public function addRelation($relation)
    {
        if (!is_subclass_of($relation, Relation::class)) {
            throw new InvalidArgumentException('Invalid relation object.');
        }

        $tableName = $relation->getRelated()->getTable();

        $this->relations[$tableName] = [
            $relation->getQualifiedOwnerKeyName(),
            '=',
            $relation->getQualifiedForeignKeyName(),
        ];

        return $this;
    }

    /**
     * Add new select query entry into the factory
     *
     * @return $this
     */
    public function addSelect(string $expression, string $alias, array $bindings = [])
    {
        $this->selects[] = [$expression, $alias, $bindings];

        return $this;
    }

    /**
     * Get the class name of the factory model
     *
     * @return string
     */
    public function getClass()
    {
        return get_class($this->model);
    }

    /**
     * Wrap the column name with the factory table
     *
     * @return string
     */
    public function wrapColumn(string $column)
    {
        return "{$this->model->getTable()}.{$column}";
    }

    /**
     * Create fresh query instance from registered sub queries
     *
     * @return App\Database\QueryBuilder
     */
    public function getQuery()
    {
        $newQuery = DB::query()->from($this->model->getTable());

        foreach ($this->selects as $select) {
            [$expression, $alias, $bindings] = $select;

            $newQuery->selectRaw("{$expression} as {$alias}", $bindings);
        }

        foreach ($this->relations as $table => $relation) {
            $newQuery->join($table, ...$relation);
        }

        return $newQuery;
    }

    /**
     * Get all searchable columns from the given model
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public static function columns(Model $model)
    {
        $columns = Arr::get($model->getSearchableColumns(), 'columns', []);

        return array_map(function ($column) use ($model) {
            return "{$model->getTable()}.{$column}";
        }, $columns);
    }

    /**
     * Get all relational columns from the given model
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    public static function relations(Model $model)
    {
        return Arr::get($model->getSearchableColumns(), 'relations', []);
    }

    /**
     * Create list of qualified columns for keyword matching
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    public static function keywords(Model $model)
    {
        $columns = static::columns($model);
        $relations = static::relations($model);

        $relationsKeyName = array_map(function ($value) use ($model) {
            $relationModel = $model->{$value}()->getModel();
            $relationColumns = static::columns($relationModel);

            return implode(', ', $relationColumns);
        }, $relations);

        return implode(', ', array_merge(
            $columns,
            $relationsKeyName,
        ));
    }
}

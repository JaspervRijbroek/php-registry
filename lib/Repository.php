<?php

declare(strict_types=1);

namespace Library;

class Repository
{
    private $items = [];
    private $removedItems = [];
    private $modelClass;

    public function __construct(string $class)
    {
        if (!is_subclass_of($class, \Library\Model::class)) {
            throw new \Error('Model repository expects a model class');
        }

        $this->modelClass = $class;
    }

    public function find(int $id): Model
    {
        $results = $this->findBy(['id' => $id]);

        return array_shift($results);
    }

    /**
     * This method will find items based on the specified items.
     * Also it directly watches the changes on all the found items in this repository instance.
     *
     * So if this method is called again, it will watch the previous and future instances and upload all the changes.
     *
     * @param string|array $column The column on which to get the data.
     * @param $value string The value on which to check
     * @return array The model instances found by the query.
     */
    public function findBy(array $filter = []): array
    {
        $results = DB::getInstance()->select(
            $this->getTableName(),
            '*',
            $filter
        );
        $results = array_map(
            function ($result) {
                return new $this->modelClass($result);
            },
            $results
        );

        // Merge the items with the results, because a repository can be reused.
        // We will watch all the items that we have selected.
        $this->items = array_merge($this->items, $results);

        return $results;
    }

    public function create()
    {
        $item = new $this->modelClass();
        $this->items[] = $item;

        return $item;
    }

    public function remove($id)
    {
        $this->removedItems = [];

        return $this;
    }

    private function getTableName()
    {
        $parts = explode('\\', $this->modelClass);

        return strtolower(end($parts));
    }

    public function flush(): Repository
    {
        // Get all the basic data and ids.
        // All changes are done in a transaction.
        $tableName = $this->getTableName();
        $db = DB::getInstance();
        $changedModels = array_filter(
            $this->items,
            function (Model $item) {
                return $item->isChanged();
            }
        );

        foreach ($changedModels as $model) {
            $method = $model->getId() ? 'update' : 'insert';

            $db->{$method}(
                $tableName,
                array_intersect_key($model->getData(), $model->getChanges()),
                [
                    'id' => $model->getId()
                ]
            );
        }

        foreach ($this->removedItems as $removedId) {
            $db->delete(
                $tableName,
                [
                    'id' => $removedId
                ]
            );
        }

        return $this;
    }
}
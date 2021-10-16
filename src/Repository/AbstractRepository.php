<?php

namespace EnoffSpb\EntityManager\Repository;

use EnoffSpb\EntityManager\EntityMetadata;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;

/**
 * @template T of object
 * @implements RepositoryInterface<T>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    protected EntityMetadata $metadata;
    protected DriverInterface $driver;

    /**
     * @var T[]
     */
    protected array $entitiesCache = [];

    public function __construct(EntityMetadata $metadata, DriverInterface $driver)
    {
        $this->metadata = $metadata;
        $this->driver = $driver;
    }

    /**
     * @param T $entity
     */
    public function attach(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);
        $this->entitiesCache[$id] = $entity;

        $this->storeValues($entity);
    }

    /**
     * @param T $entity
     */
    public function detach(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);

        if(isset($this->entitiesCache[$id])) {
            unset($this->entitiesCache[$id]);
        }

        $this->clearStoredValues($entity);
    }

    private array $storedValues = [];

    /**
     * @param T $entity
     */
    public function storeValues(object $entity): void
    {
        $columns = $this->metadata->getMapping();
        $id = $this->metadata->getPkValue($entity);

        $values = [];
        foreach($columns as $column) {
            if($column->getter !== null) {
                $values[$column->field] = $entity->{$column->getter}();
            } else {
                $values[$column->field] = $entity->{$column->attribute};
            }
        }
        $this->storedValues[$id] = $values;
    }

    /**
     * @param T $entity
     */
    public function getStoredValues(object $entity): ?array
    {
        $id = $this->metadata->getPkValue($entity);

        if(!isset($this->storedValues[$id])) {
            return null;
        }

        return $this->storedValues[$id];
    }

    /**
     * @param T $entity
     */
    public function clearStoredValues(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);

        if(isset($this->storedValues[$id])) {
            unset($this->storedValues[$id]);
        }
    }
}

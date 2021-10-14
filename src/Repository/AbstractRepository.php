<?php

namespace enoffspb\EntityManager\Repository;

use enoffspb\EntityManager\EntityMetadata;
use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    protected EntityMetadata $metadata;
    protected DriverInterface $driver;
    protected array $entitiesCache = [];

    public function __construct(EntityMetadata $metadata, DriverInterface $driver)
    {
        $this->metadata = $metadata;
        $this->driver = $driver;
    }

    public function attach(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);
        $this->entitiesCache[$id] = $entity;

        $this->storeValues($entity);
    }

    public function detach(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);

        if(isset($this->entitiesCache[$id])) {
            unset($this->entitiesCache[$id]);
        }

        $this->clearStoredValues($entity);
    }

    private array $storedValues = [];
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

    public function getStoredValues(object $entity): ?array
    {
        $id = $this->metadata->getPkValue($entity);

        if(!isset($this->storedValues[$id])) {
            return null;
        }

        return $this->storedValues[$id];
    }

    public function clearStoredValues(object $entity): void
    {
        $id = $this->metadata->getPkValue($entity);

        if(isset($this->storedValues[$id])) {
            unset($this->storedValues[$id]);
        }
    }
}

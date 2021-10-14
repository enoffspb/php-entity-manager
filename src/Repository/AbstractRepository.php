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
        $pk = $this->metadata->primaryKey;

        if(isset($this->entitiesCache[$entity->$pk])) {
            unset($this->entitiesCache[$entity->$pk]);
        }

        $this->clearStoredValues($entity);
    }

    private array $storedValues = [];
    public function storeValues(object $entity): void
    {
        $columns = $this->metadata->getMapping();
        $pkField = $this->metadata->primaryKey;

        $pk = $this->metadata->getPkValue($entity);

        $values = [];
        foreach($columns as $column) {
            $values[$column->attribute] = $entity->{$column->attribute};
        }
        $this->storedValues[$pk] = $values;
    }

    public function getStoredValues(object $entity): ?array
    {
        $pkField = $this->metadata->primaryKey;
        $pk = $entity->$pkField;

        if(!isset($this->storedValues[$pk])) {
            return null;
        }

        return $this->storedValues[$pk];
    }

    public function clearStoredValues(object $entity): void
    {
        $pkField = $this->metadata->primaryKey;
        $pk = $entity->$pkField;

        if(isset($this->storedValues[$pk])) {
            unset($this->storedValues[$pk]);
        }
    }
}

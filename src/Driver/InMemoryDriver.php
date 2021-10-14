<?php

namespace enoffspb\EntityManager\Driver;

use enoffspb\EntityManager\EntityMetadata;
use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;
use enoffspb\EntityManager\Repository\InMemoryGenericRepository;

/**
 * InMemoryDriver is using while a development process of components in action and with auto-testing
 */
class InMemoryDriver extends BaseDriver implements DriverInterface
{
    public array $storage = [];

    public function getGenericRepositoryClass(): string
    {
        return InMemoryGenericRepository::class;
    }

    public function save(object $entity): bool
    {
        $entityClass = get_class($entity);
        $metadata = $this->getMetadata($entityClass);

        $nextId = $this->getNextPk($entityClass);
        $metadata->setPkValue($entity, $nextId);

        $this->cacheEntity($entity);

        $repository = $this->getRepository(get_class($entity));
        $repository->attach($entity);

        return true;
    }

    public function update(object $entity): bool
    {
        $this->cacheEntity($entity);

        return true;
    }

    public function delete(object $entity): bool
    {
        $entityClass = get_class($entity);
        $metadata = $this->getMetadata($entityClass);

        $id = $metadata->getPkValue($entity);

        if(isset($this->storage[$entityClass]) && isset($this->storage[$entityClass][$id])) {
            unset($this->storage[$entityClass][$id]);

            return true;
        }

        return false;
    }

    /**
     * @return mixed A scalar value for next primary key
     */
    public function getNextPk(string $entityClass)
    {
        if(!isset($this->storage[$entityClass])) {
            $this->storage[$entityClass] = [];
        }

        $nextId = count($this->storage[$entityClass]) + 1;

        return $nextId;
    }

    public function cacheEntity(object $entity): void
    {
        $entityClass = get_class($entity);

        if(!isset($this->storage[$entityClass])) {
            $this->storage[$entityClass] = [];
        }

        $metadata = $this->getMetadata($entityClass);
        $id = $metadata->getPkValue($entity);

        $this->storage[$entityClass][$id] = $entity;
    }

    public function getEntity(string $entityClass, $id): ?object
    {
        if(!isset($this->storage[$entityClass]) || !isset($this->storage[$entityClass][$id])) {
            return null;
        }

        return $this->storage[$entityClass][$id];
    }
}

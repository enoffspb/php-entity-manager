<?php

namespace EnoffSpb\EntityManager\Driver\InMemory;

use EnoffSpb\EntityManager\Driver\BaseDriver;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Driver\InMemory\InMemoryGenericRepository;

/**
 * InMemoryDriver is using while a development process of components in action and with auto-testing
 */
class InMemoryDriver extends BaseDriver implements DriverInterface
{
    /**
     * @var array<string, array<object>>
     */
    public array $storage = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(?string $dsn = null, ?string $user = null, ?string $password = null, array $options = [])
    {
        if($dsn || $user || $password || $options) {
            // use variables for establishing a connection
        }
    }

    public function getGenericRepositoryClass(): string
    {
        return InMemoryGenericRepository::class;
    }

    public function save(object $entity): void
    {
        $entityClass = get_class($entity);
        $metadata = $this->getMetadata($entityClass);

        $nextId = $this->getNextPk($entityClass);
        $metadata->setPkValue($entity, $nextId);

        $this->cacheEntity($entity);

        $repository = $this->getRepository(get_class($entity));
        $repository->attach($entity);
    }

    public function update(object $entity): void
    {
        $this->cacheEntity($entity);
    }

    public function delete(object $entity): void
    {
        $entityClass = get_class($entity);
        $metadata = $this->getMetadata($entityClass);

        $id = $metadata->getPkValue($entity);

        if(isset($this->storage[$entityClass]) && isset($this->storage[$entityClass][$id])) {
            unset($this->storage[$entityClass][$id]);
        }
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

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @param mixed $id
     * @returns T|null
     */
    public function getEntity(string $entityClass, $id): ?object
    {
        if(!isset($this->storage[$entityClass]) || !isset($this->storage[$entityClass][$id])) {
            return null;
        }

        return $this->storage[$entityClass][$id];
    }
}

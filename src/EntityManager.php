<?php

namespace EnoffSpb\EntityManager;

use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Interfaces\EntityManagerInterface;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Repository\GenericRepository;

class EntityManager implements EntityManagerInterface
{
    protected DriverInterface $driver;

    /**
     * @var array<string, array>
     */
    protected array $entitiesConfig;

    /**
     * EntityManager constructor.
     *
     * @param DriverInterface $driver One of available drivers (InMemoryDriver, MysqlDriver, etc)
     * @param array<string, array> $entitiesConfig Format: [Entity::class => [* entity config, properties of EntityMetadata *]]
     */
    public function __construct(DriverInterface $driver, array $entitiesConfig = [])
    {
        $this->driver = $driver;
        $this->setEntitiesConfig($entitiesConfig);
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @param array<string, array> $entitiesConfig Format: [Entity::class => [* entity config, properties of EntityMetadata *]]
     */
    public function setEntitiesConfig(array $entitiesConfig): void
    {
        $this->entitiesConfig = $entitiesConfig;
        $this->driver->setEntitiesConfig($entitiesConfig);
    }

    /**
     * @return array<string, array>
     */
    public function getEntitiesConfig(): array
    {
        return $this->entitiesConfig;
    }

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return RepositoryInterface<T>
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        return $this->getDriver()->getRepository($entityClass);
    }

    public function save(object $entity): void
    {
        $this->driver->save($entity);
    }

    public function update(object $entity): void
    {
        $this->driver->update($entity);
    }

    public function delete(object $entity): void
    {
        $this->driver->delete($entity);
    }
}

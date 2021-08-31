<?php

namespace enoffspb\EntityManager;

use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;
use enoffspb\EntityManager\Repository\GenericRepository;

class EntityManager implements EntityManagerInterface
{
    protected DriverInterface $driver;
    protected array $entitiesConfig;

    /**
     * EntityManager constructor.
     *
     * @param DriverInterface $driver One of available drivers (InMemoryDriver, MysqlDriver, etc)
     * @param array $entitiesConfig Format: [Entity::class => [* entity config, properties of EntityMetadata *]]
     */
    public function __construct(DriverInterface $driver, array $entitiesConfig = [])
    {
        $this->driver = $driver;
        $this->entitiesConfig = $entitiesConfig;
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @param array $entitiesConfig Format: [Entity::class => [* entity config, properties of EntityMetadata *]]
     */
    public function setEntitiesConfig(array $entitiesConfig)
    {
        $this->entitiesConfig = $entitiesConfig;
    }

    public function getEntitiesConfig(): array
    {
        return $this->entitiesConfig;
    }

    private array $repositories = [];

    public function getRepository(string $entityClass): RepositoryInterface
    {
        if(isset($this->repositories[$entityClass])) {
            return $this->repositories[$entityClass];
        }

        $metadata = $this->driver->getMetadata($entityClass);
        $repositoryClass = null;

        if($metadata->repositoryClass !== null) {
            $repositoryClass = $metadata->repositoryClass;
        } else {
            $repositoryClass = $this->driver->getGenericRepositoryClass();
        }

        $repository = new $repositoryClass($metadata, $this->driver);

        $this->repositories[$entityClass] = $repository;

        return $repository;
    }

    public function save(object $entity): bool
    {
        return $this->driver->save($entity);
    }

    public function update(object $entity): bool
    {
        return $this->driver->update($entity);
    }

    public function delete(object $entity): bool
    {
        return $this->driver->delete($entity);
    }
}

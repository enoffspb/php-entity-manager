<?php

namespace EnoffSpb\EntityManager\Driver;

use EnoffSpb\EntityManager\EntityMetadata;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;

abstract class BaseDriver implements DriverInterface
{
    /**
     * @var EntityMetadata[]
     */
    protected array $metaData = [];

    /**
     * @var array<string, array>
     */
    protected array $entitiesConfig = [];

    /**
     * @var array<string, RepositoryInterface<object>>
     */
    protected array $repositories = [];

    /**
     * @param array<string, mixed> $entitiesConfig
     */
    public function setEntitiesConfig(array $entitiesConfig): void
    {
        $this->entitiesConfig = $entitiesConfig;
    }

    /**
     * @param class-string $entityClass
     * @param array<string, mixed> $entityConfig
     * @return EntityMetadata
     */
    public function createMetadata(string $entityClass, array $entityConfig): EntityMetadata
    {
        $metadata = new EntityMetadata($entityConfig);
        $metadata->entityClass = $entityClass;

        return $metadata;
    }

    /**
     * @param class-string $entityClass
     */
    public function getMetadata(string $entityClass): EntityMetadata
    {
        if(isset($this->metaData[$entityClass])) {
            return $this->metaData[$entityClass];
        }

        $entityConfig = $this->entitiesConfig[$entityClass] ?? null;

        $metadata = $this->createMetadata($entityClass, $entityConfig ?? []);

        $this->metaData[$entityClass] = $metadata;

        return $metadata;
    }

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return RepositoryInterface<T>
     */
    public function getRepository(string $entityClass): RepositoryInterface
    {
        if(isset($this->repositories[$entityClass])) {
            /**
             * @var RepositoryInterface<T>
             */
            $repository = $this->repositories[$entityClass];
        }

        $metadata = $this->getMetadata($entityClass);
        $repositoryClass = null;

        if($metadata->repositoryClass !== null) {
            $repositoryClass = $metadata->repositoryClass;
        } else {
            $repositoryClass = $this->getGenericRepositoryClass();
        }

        $repository = new $repositoryClass($metadata, $this);

        $this->repositories[$entityClass] = $repository;

        return $repository;
    }
}

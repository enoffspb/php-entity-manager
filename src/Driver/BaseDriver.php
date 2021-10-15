<?php

namespace enoffspb\EntityManager\Driver;

use enoffspb\EntityManager\EntityMetadata;
use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;

abstract class BaseDriver implements DriverInterface
{
    protected array $metaData = [];
    protected array $entitiesConfig = [];
    protected array $repositories = [];

    public function setEntitiesConfig(array $entitiesConfig): void
    {
        $this->entitiesConfig = $entitiesConfig;
    }

    public function createMetadata(string $entityClass, array $entityConfig): EntityMetadata
    {
        $metadata = new EntityMetadata($entityConfig);
        $metadata->entityClass = $entityClass;

        return $metadata;
    }

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

    public function getRepository(string $entityClass): RepositoryInterface
    {
        if(isset($this->repositories[$entityClass])) {
            return $this->repositories[$entityClass];
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

<?php

namespace enoffspb\EntityManager\Driver;

use enoffspb\EntityManager\EntityMetadata;
use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;

abstract class BaseDriver implements DriverInterface
{
    private EntityManagerInterface $entityManager;
    private array $metaDatas = [];
    private array $entitiesConfig = [];

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function setEntitiesConfig(array $entitiesConfig)
    {
        $this->entitiesConfig = $entitiesConfig;
    }

    public function getMetadata(string $entityClass): EntityMetadata
    {
        if(isset($this->metaDatas[$entityClass])) {
            return $this->metaDatas[$entityClass];
        }

        $entityConfig = $this->entitiesConfig[$entityClass] ?? null;

        $metadata = $this->createMetadata($entityClass, $entityConfig);

        $this->metaDatas[$entityClass] = $metadata;

        return $metadata;
    }
}

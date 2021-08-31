<?php

namespace enoffspb\EntityManager\Interfaces;

use enoffspb\EntityManager\EntityMetadata;

interface DriverInterface extends EntityManagerInterface
{
    public function getMetadata(string $entityClass): EntityMetadata;
    public function setEntityManager(EntityManagerInterface $entityManager): void;
    public function createMetadata($entityClass, $entityConfig): EntityMetadata;
}

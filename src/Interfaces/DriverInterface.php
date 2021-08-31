<?php

namespace enoffspb\EntityManager\Interfaces;

use enoffspb\EntityManager\EntityMetadata;

interface DriverInterface
{
    public function getMetadata(string $entityClass): EntityMetadata;
    public function setEntityManager(EntityManagerInterface $entityManager): void;
    public function createMetadata($entityClass, $entityConfig): EntityMetadata;
    public function getGenericRepositoryClass(): string;

    public function save(object $entity): bool;
    public function update(object $entity): bool;
    public function delete(object $entity): bool;
}

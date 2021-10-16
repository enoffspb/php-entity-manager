<?php

namespace EnoffSpb\EntityManager\Interfaces;

use EnoffSpb\EntityManager\EntityMetadata;

interface DriverInterface
{
    public function getMetadata(string $entityClass): EntityMetadata;
    public function createMetadata(string $entityClass, array $entityConfig): EntityMetadata;
    public function setEntitiesConfig(array $entitiesConfig): void;

    public function save(object $entity): bool;
    public function update(object $entity): bool;
    public function delete(object $entity): bool;

    public function getGenericRepositoryClass(): string;

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return RepositoryInterface<T>
     */
    public function getRepository(string $entityClass): RepositoryInterface;
}

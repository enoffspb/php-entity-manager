<?php

namespace EnoffSpb\EntityManager\Interfaces;

use EnoffSpb\EntityManager\EntityMetadata;

interface DriverInterface
{
    /**
     * @param array<string,mixed> $options
     */
    public function __construct(?string $dsn = null, ?string $user = null, ?string $password = null, array $options = []);

    public function getMetadata(string $entityClass): EntityMetadata;

    /**
     * @param array<string,mixed> $entityConfig
     */
    public function createMetadata(string $entityClass, array $entityConfig): EntityMetadata;

    /**
     * @param array<string,array> $entitiesConfig
     */
    public function setEntitiesConfig(array $entitiesConfig): void;

    public function save(object $entity): void;
    public function update(object $entity): void;
    public function delete(object $entity): void;

    public function getGenericRepositoryClass(): string;

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return RepositoryInterface<T>
     */
    public function getRepository(string $entityClass): RepositoryInterface;
}

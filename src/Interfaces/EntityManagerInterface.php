<?php

namespace EnoffSpb\EntityManager\Interfaces;

interface EntityManagerInterface
{
    public function save(object $entity): void;
    public function update(object $entity): void;
    public function delete(object $entity): void;

    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @return RepositoryInterface<T>
     */
    public function getRepository(string $entityClass): RepositoryInterface;

    public function getDriver(): DriverInterface;
}

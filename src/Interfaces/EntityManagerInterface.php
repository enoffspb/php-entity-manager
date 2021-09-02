<?php

namespace enoffspb\EntityManager\Interfaces;

interface EntityManagerInterface
{
    public function save(object $entity): bool;
    public function update(object $entity): bool;
    public function delete(object $entity): bool;

    public function getRepository(string $entityClass): RepositoryInterface;

    public function getDriver(): DriverInterface;
}

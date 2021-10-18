<?php

namespace EnoffSpb\EntityManager\Interfaces;

interface EntityManagerInterface
{
    public function save(object $entity): void;
    public function update(object $entity): void;
    public function delete(object $entity): void;

    public function getRepository(string $entityClass): RepositoryInterface;

    public function getDriver(): DriverInterface;
}

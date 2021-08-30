<?php

namespace enoffspb\EntityManager;

use enoffspb\EntityManager\Interfaces\EntityManagerInterface;

class InMemoryEntityManager implements EntityManagerInterface
{
    private array $entities = [];

    public function getRepository(string $entityClass)
    {
        // TODO: Implement getRepository() method.
    }

    public function save(object $entity): bool
    {
        // TODO: Implement save() method.
    }

    public function update(object $entity): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(object $entity): bool
    {
        // TODO: Implement delete() method.
    }
}

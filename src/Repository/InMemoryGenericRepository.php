<?php

namespace enoffspb\EntityManager\Repository;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;

/**
 * @property InMemoryDriver $driver
 */
class InMemoryGenericRepository extends AbstractRepository implements RepositoryInterface
{
    public function getByPk($primaryKey): ?object
    {
        return $this->driver->getEntity($this->metadata->entityClass, $primaryKey);
    }

    public function getList($criteria): array
    {
        throw new \Exception('@TODO: Implement getList() method.');
    }
}

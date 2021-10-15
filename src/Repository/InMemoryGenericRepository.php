<?php

namespace enoffspb\EntityManager\Repository;

use enoffspb\EntityManager\Driver\InMemoryDriver;
use enoffspb\EntityManager\Interfaces\RepositoryInterface;

/**
 * @property InMemoryDriver $driver
 */
class InMemoryGenericRepository extends AbstractRepository implements RepositoryInterface
{
    public function getById($id): ?object
    {
        return $this->driver->getEntity($this->metadata->entityClass, $id);
    }

    public function getList($criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $entityClass = $this->metadata->entityClass;
        if(!isset($this->driver->storage[$entityClass])) {
            return [];
        }

        $result = [];
        foreach($this->driver->storage[$entityClass] as $k => $entity) {
            if($this->isMatched($entity, $criteria)) {
                $result[] = $entity;
            }
        }

        return $result;
    }

    private function isMatched(object $entity, $criteria): bool
    {
        $matched = true;

        foreach($criteria as $field => $filterValue) {
            $value = $this->metadata->getFieldValue($entity, $field);
            $matched = $matched && ($value === $filterValue);
        }

        return $matched;
    }
}

<?php

namespace EnoffSpb\EntityManager\Driver\InMemory;

use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Repository\AbstractRepository;

/**
 * @template T of object
 * @implements RepositoryInterface<T>
 *
 * @property InMemoryDriver $driver
 */
class InMemoryGenericRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * @return T|null
     */
    public function getById($id): ?object
    {
        /**
         * @var T|null $entity
         */
        $entity = $this->driver->getEntity($this->metadata->entityClass, $id);
        return $entity;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return T[]
     */
    public function getList(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
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

        if($orderBy !== null) {
            usort($result, function($a, $b) use($orderBy) {
                $aValues = $this->metadata->getValues($a);
                $bValues = $this->metadata->getValues($b);

                foreach($orderBy as $field => $direction) {
                    $desc = $direction === SORT_DESC || strtoupper($direction) === 'DESC';

                    $aValue = $aValues[$field];
                    $bValue = $bValues[$field];

                    if($aValue === $bValue) {
                        continue;
                    }

                    if($desc) {
                        return $aValue < $bValue ? -1 : 1;
                    } else {
                        return $aValue > $bValue ? -1 : 1;
                    }
                }

                return 0;
            });
        }

        if($limit !== null) {
            $result = array_slice($result, $offset ?? null, $limit);
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

<?php

namespace enoffspb\EntityManager\Interfaces;

/**
 * @template T of object
 */
interface RepositoryInterface
{
    /**
     * @param mixed $id
     * @return T|null
     */
    public function getById($id): ?object;

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return T[]
     */
    public function getList(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param T $entity
     */
    public function attach(object $entity): void;

    /**
     * @param T $entity
     */
    public function detach(object $entity): void;
}

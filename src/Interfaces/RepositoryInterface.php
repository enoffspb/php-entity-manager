<?php

namespace enoffspb\EntityManager\Interfaces;

use enoffspb\EntityManager\EntityMetadata;

/**
 * @template T
 */
interface RepositoryInterface
{
    public function __construct(EntityMetadata $metadata);

    /**
     * @param mixed $primaryKey
     * @return T|null
     */
    public function getByPk($primaryKey): ?object;

    /**
     * @param array $criteria
     * @return T[]
     */
    public function getList($criteria): array;

    /**
     * @param T $entity
     */
    public function attach(object $entity): void;

    /**
     * @param T $entity
     */
    public function detach(object $entity): void;
}

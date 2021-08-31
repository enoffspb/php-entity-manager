<?php

namespace enoffspb\EntityManager;

use enoffspb\EntityManager\Interfaces\RepositoryInterface;

class GenericRepository implements RepositoryInterface
{
    protected EntityMetadata $metadata;

    public function __construct(EntityMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getByPk($primaryKey): ?object
    {
        // TODO: Implement getByPk() method.
    }

    public function getList($criteria): array
    {
        // TODO: Implement getList() method.
    }

    public function attach(object $entity): void
    {
        // TODO: Implement attach() method.
    }

    public function detach(object $entity): void
    {
        // TODO: Implement detach() method.
    }
}

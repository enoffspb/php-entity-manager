<?php

namespace enoffspb\EntityManager\Driver;

use enoffspb\EntityManager\EntityMetadata;
use enoffspb\EntityManager\Interfaces\DriverInterface;
use enoffspb\EntityManager\Interfaces\EntityManagerInterface;

class InMemoryDriver extends BaseDriver implements DriverInterface
{
    public function getRepository(string $entityClass)
    {
        // TODO: Implement getRepository() method.
    }

    public function createMetadata($entityClass, $entityConfig): EntityMetadata
    {
        $metadata = new EntityMetadata();
        $metadata->entityClass = $entityClass;

        if($entityConfig) {
            foreach($entityConfig as $k => $v) {
                $metadata->$k = $v;
            }
        }

        return $metadata;
    }

    private array $storage = [];

    public function save(object $entity): bool
    {
        $entityClass = get_class($entity);
        $metadata = $this->getMetadata($entityClass);
        $pk = $metadata->primaryKey;

        if(!isset($this->storage[$entityClass])) {
            $this->storage[$entityClass] = [];
        }

        $nextId = count($this->storage[$entityClass]) + 1;
        $entity->$pk = $nextId;

        $this->storage[$entityClass][$nextId] = $entity;

//        $repository = $this->getRepository(get_class($entity));
//        $repository->attach($entity);

        return true;
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

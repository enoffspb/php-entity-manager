<?php

namespace EnoffSpb\EntityManager\Driver\MySql;

use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;
use EnoffSpb\EntityManager\Repository\AbstractRepository;

class MySqlGenericRepository extends AbstractRepository implements RepositoryInterface
{

    public function getById($id): ?object
    {
        throw new \Exception('@TODO: Implement ' . __METHOD__ . ' method.');
    }

    public function getList(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        throw new \Exception('@TODO: Implement ' . __METHOD__ . ' method.');
    }
}
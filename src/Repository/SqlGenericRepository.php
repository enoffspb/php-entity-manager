<?php

namespace EnoffSpb\EntityManager\Repository;

use EnoffSpb\EntityManager\Driver\SqlAbstractDriver;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;

use PDO;
use PDOStatement;

/**
 * @template T of object
 *
 * @property SqlAbstractDriver $driver
 */
class SqlGenericRepository extends AbstractRepository implements RepositoryInterface
{
    private ?PDOStatement $getByIdStmt = null;

    /**
     * @return T|null
     */
    public function getById($id): ?object
    {
        if(isset($this->entitiesCache[$id])) {
            return $this->entitiesCache[$id];
        }

        $pdo = $this->driver->getPdo();

        if($this->getByIdStmt === null) {

            $q = $this->driver->identifierQuote;
            $tableName = $q . $this->metadata->tableName . $q;

            $pkColumn = $this->metadata->getMapping()[$this->metadata->primaryKey];
            $pkColumnName = $q . $pkColumn->field . $q;

            $query = "SELECT $tableName.* FROM $tableName WHERE $pkColumnName=? LIMIT 1";
            $this->getByIdStmt = $pdo->prepare($query);
        }

        $r = $this->getByIdStmt->execute([$id]);
        if(!$r) {
            $errInfo = $pdo->errorInfo();
            throw new \Exception('Cannot execute a SQL query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $data = $this->getByIdStmt->fetch(PDO::FETCH_ASSOC);
        if($data === false) {
            return null;
        }
        
        // An issue: we cannot pass params to an entity constructor.
        // @todo Move creation of an entity to a separate method, further it allows to overload a creation process.

        $entity = new $this->metadata->entityClass();
        $this->metadata->setValues($entity, $data);

        $this->attach($entity);

        return $entity;
    }

    /**
     * @return T[]
     */
    public function getList(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        throw new \Exception('@TODO: Implement ' . __METHOD__ . ' method.');
    }
}

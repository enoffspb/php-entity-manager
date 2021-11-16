<?php

namespace EnoffSpb\EntityManager\Repository;

use EnoffSpb\EntityManager\Driver\SqlBaseDriver;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;

use PDO;
use PDOStatement;

/**
 * @template T of object
 *
 * @extends AbstractRepository<T>
 * @implements RepositoryInterface<T>
 *
 * @property SqlBaseDriver $driver
 */
class SqlGenericRepository extends AbstractRepository implements RepositoryInterface
{
    private ?PDOStatement $getByIdStmt = null;

    /**
     * @returns T|null
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
        // @todo Do it for getList() also.

        /**
         * @var T
         */
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
        $q = $this->driver->identifierQuote;
        $tableName = $q . $this->metadata->tableName . $q;

        $query = "SELECT $tableName.* FROM $tableName";

        $whereExpressions = [];
        $params = [];
        foreach($criteria as $field => $value) {
            $whereExpressions[] = $q . $field . $q . '=?';
            $params[] = $value;
        }

        if(!empty($whereExpressions)) {
            $query .= ' WHERE ' . implode(' AND ', $whereExpressions);
        }

        if(!empty($orderBy)) {
            $orderParts = [];
            foreach($orderBy as $field => $direction) {
                $desc = $direction == SORT_DESC || strtoupper((string) $direction) == 'DESC';
                $orderParts[] = $q . $field . $q . ' ' . ($desc ? 'DESC' : 'ASC');
            }
            $query .= ' ORDER BY ' . implode(', ', $orderParts);
        }

        if($limit !== null || $offset !== null) {
            $this->applyLimitAndOffsetToSql($query, $limit, $offset);
        }

        $pdo = $this->driver->getPdo();
        $stmt = $pdo->prepare($query);
        if($stmt == false) {
            $errInfo = $pdo->errorInfo();
            throw new \Exception('Cannot prepare a statement for a select query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $res = $stmt->execute($params);
        if($res === false) {
            $errInfo = $pdo->errorInfo();
            throw new \Exception('Cannot execute a SQL query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            /**
             * @var T
             */
            $entity = new $this->metadata->entityClass;

            $this->metadata->setValues($entity, $row);
            $id = $this->metadata->getPkValue($entity);
            if(!isset($this->entitiesCache[$id])) {
                $this->attach($entity);
            } else {
                // @todo Close an issue about a merge strategy
                // There are three states of entity values:
                // - stored values
                // - object values
                // - new values from db
                //
                // @todo Create mergeEntities() method

                // If an entity has already attached to the repository, we need to return the instance from cache

                $cachedEntity = $this->entitiesCache[$id];

                unset($entity);
                $entity = $cachedEntity;
            }

            $result[] = $entity;
        }

        return $result;
    }

    protected function applyLimitAndOffsetToSql(string &$query, ?int $limit = null, ?int $offset = null): void
    {
        if($limit !== null) {
            $query .= ' LIMIT ' . $limit;
        }
        if($offset !== null) {
            $query .= ' OFFSET ' . $offset;
        }
    }
}

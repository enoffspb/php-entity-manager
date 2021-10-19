<?php

namespace EnoffSpb\EntityManager\Repository;

use EnoffSpb\EntityManager\Driver\SqlBaseDriver;
use EnoffSpb\EntityManager\Interfaces\RepositoryInterface;

use PDO;
use PDOStatement;

/**
 * @template T of object
 *
 * @property SqlBaseDriver $driver
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
        // @todo Do it for getList() also.

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
                $desc = $direction === SORT_DESC || strtoupper($direction) === 'DESC';
                $orderParts[] = $q . $field . $q . ' ' . ($desc ? 'DESC' : 'ASC');
            }
            $query .= ' ORDER BY ' . implode(', ', $orderParts);
        }

        if($limit !== null || $offset !== null) {
            $this->applyLimitAndOffsetToSql($query, $limit, $offset);
        }

        $pdo = $this->driver->getPdo();
        $stmt = $pdo->prepare($query);
        if($stmt === false) {
            $errInfo = $pdo->errorInfo();
            throw new \Exception('Cannot prepare a statement for a select query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $res = $stmt->execute($params);
        if($res === false) {
            $errInfo = $pdo->errorInfo();
            throw new \Exception('Cannot execute a SQL query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        /**
         * @todo Implement a merge strategy. If an entity has already attached - what do we need to do?
         * @todo Should we update fields of an exists entity? It seems as only one possible case.
         */
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entity = new $this->metadata->entityClass;
            $this->metadata->setValues($entity, $row);

            $this->attach($entity);

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

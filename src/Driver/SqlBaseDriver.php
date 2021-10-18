<?php

namespace EnoffSpb\EntityManager\Driver;

use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Repository\AbstractRepository;
use EnoffSpb\EntityManager\Repository\SqlGenericRepository;

use PDO;
use PDOStatement;

/**
 * @todo Review and do refactoring all the exceptions in the class
 */
class SqlBaseDriver extends BaseDriver implements DriverInterface
{
    public string $identifierQuote = '"';
    public string $valueQuote = "'";

    private PDO $pdo;

    private ?PDOStatement $insertStmt = null;
    private ?PDOStatement $deleteStmt = null;

    public function __construct(?string $dsn = null, ?string $user = null, ?string $password = null, array $options = [])
    {
        if($dsn === null) {
            throw new \Exception('Parameter $dsn of ' . get_class($this) . ' cannot be null.');
        }

        $this->pdo = new PDO($dsn, $user, $password);
    }

    public function getGenericRepositoryClass(): string
    {
        return SqlGenericRepository::class;
    }

    public function save(object $entity): void
    {
        $metadata = $this->getMetadata(get_class($entity));

        $fields = $metadata->getValues($entity);

        $queryColumns = [];
        $queryValues = [];
        $placeholders = [];
        foreach($fields as $columnName => $columnValue) {
            if($columnName === $metadata->primaryKey && $columnValue === null) {
                continue;
            }
            $queryColumns[] = $this->identifierQuote . $columnName . $this->identifierQuote;
            $queryValues[] = $columnValue;
            $placeholders[] = '?';
        }

        if($this->insertStmt === null) {
            $tableName = $this->identifierQuote . $metadata->tableName . $this->identifierQuote;
            $query = "INSERT INTO $tableName (" . implode(', ', $queryColumns) . ") VALUES (" .
                implode(', ', $placeholders) .
            ")";
            $stmt = $this->pdo->prepare($query);
            if($this->insertStmt === false) {
                $errInfo = $this->pdo->errorInfo();
                throw new \Exception('Cannot prepare a statement for an insert query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
            }
            $this->insertStmt = $stmt;
        }

        $r = $this->insertStmt->execute($queryValues);
        if(!$r) {
            $errInfo = $this->insertStmt->errorInfo();
            /**
             * @todo create a class for Exception that will contain an additional info
             * class SqlException
             * + query
             * + params
             */
            throw new \Exception('Execution an insert query returns false. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        if($metadata->getPkValue($entity) === null) {
            $insertedId = $this->pdo->lastInsertId();
            if(!$insertedId) {
                throw new \Exception('Cannot retrieve a value from PDO->lastInsertId()');
            }
            $metadata->setPkValue($entity, $insertedId);
        }

        $repository = $this->getRepository(get_class($entity));
        $repository->attach($entity);
    }

    public function update(object $entity): void
    {
        $metadata = $this->getMetadata(get_class($entity));

        $id = $metadata->getPkValue($entity);
        if($id === null) {
            throw new \Exception('An entity for update ' . get_class($entity) . ' has not set primaryKey value.');
        }

        /**
         * @var $repository AbstractRepository
         */
        $repository = $this->getRepository(get_class($entity));

        $currentValues = $metadata->getValues($entity);
        $storedValues = $repository->getStoredValues($entity);

        $diffValues = null;
        // If the repository has not stored values for $entity, update the all of fields.
        if($storedValues === null) {
            $diffValues = $currentValues;
        } else {
            $diffValues = array_diff_assoc($currentValues, $storedValues);
        }
        if(empty($diffValues)) {
            // nothing to update
            return;
        }

        $q = $this->identifierQuote;

        $setExpressions = [];
        $params = [];
        foreach($diffValues as $columnName => $columnValue) {
            $columnName = $q. $columnName . $q;
            $setExpressions[] = "$columnName = ?";
            $params[] = $columnValue;
        }

        $tableName = $q . $metadata->tableName . $q;
        $pkFieldName = $q . $metadata->getMapping()[$metadata->primaryKey]->field . $q;

        $query = "UPDATE $tableName SET " . implode(', ', $setExpressions) .
            " WHERE $pkFieldName = ?";

        $stmt = $this->pdo->prepare($query);
        if($stmt === false) {
            /**
             * @todo Create and use SqlException and pass pdo->errorInfo() to it
             */
            $errInfo = $this->pdo->errorInfo();
            throw new \Exception('Cannot prepare a statement for an update query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $params[] = $id;
        $res = $stmt->execute($params);
        if($res === false) {
            unset($stmt);
            $errInfo = $this->pdo->errorInfo();
            throw new \Exception('Execution an update query returns false. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        unset($stmt);

        $repository->storeValues($entity);
    }

    public function delete(object $entity): void
    {
        $metadata = $this->getMetadata(get_class($entity));
        $id = $metadata->getPkValue($entity);

        if($this->deleteStmt === null) {
            $q = $this->identifierQuote;
            $tableName = $q . $metadata->tableName . $q;
            $pkField = $q . ($metadata->getMapping()[$metadata->primaryKey]->field) . $q;
            $query = "DELETE FROM $tableName WHERE $pkField = ?";
            $stmt = $this->pdo->prepare($query);
            if($stmt === false) {
                $errInfo = $this->pdo->errorInfo();
                throw new \Exception('Cannot prepare a statement for an update query. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
            }
            $this->deleteStmt = $stmt;
        }

        $res = $this->deleteStmt->execute([
            $id
        ]);
        if($res === false) {
            $errInfo = $this->pdo->errorInfo();
            throw new \Exception('Execution a delete query returns false. SQLSTATE error code: ' . $errInfo[0] . '; error code: ' . $errInfo[1] . '; message: ' . $errInfo[2]);
        }

        $repository = $this->getRepository(get_class($entity));
        $repository->detach($entity);

        unset($entity);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}

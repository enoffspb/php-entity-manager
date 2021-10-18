<?php

namespace EnoffSpb\EntityManager\Driver;

use EnoffSpb\EntityManager\Driver\MySql\MySqlGenericRepository;
use EnoffSpb\EntityManager\Interfaces\DriverInterface;

abstract class SqlAbstractDriver extends BaseDriver implements DriverInterface
{
    protected string $identifierQuote = '"';
    protected string $valueQuote = "'";

    protected \PDO $pdo;

    private ?\PDOStatement $insertStmt = null;

    public function __construct(?string $dsn = null, ?string $user = null, ?string $password = null, array $options = [])
    {
        if($dsn === null) {
            throw new \Exception('Parameter $dsn of ' . get_class($this) . ' cannot be null.');
        }

        $this->pdo = new \PDO($dsn, $user, $password);
    }

    public function getGenericRepositoryClass(): string
    {
        return MySqlGenericRepository::class;
    }

    public function save(object $entity): bool
    {
        $metadata = $this->getMetadata(get_class($entity));

        $fields = $metadata->getValues($entity);

        $queryColumns = [];
        $queryValues = [];
        $placeholders = [];
        foreach($fields as $columnName => $columnValue) {
            $queryColumns[] = $this->identifierQuote . $columnName . $this->identifierQuote;
            $queryValues[] = $columnValue;
            $placeholders[] = '?';
        }

        if($this->insertStmt === null) {
            $tableName = $this->identifierQuote . $metadata->tableName . $this->identifierQuote;
            $query = "INSERT INTO $tableName (" . implode(', ', $queryColumns) . ") VALUES (" .
                implode(', ', $placeholders) .
            ")";
            $this->insertStmt = $this->pdo->prepare($query);
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

        // @TODO execute the query

        if($metadata->getPkValue($entity) === null) {
            $insertedId = $this->pdo->lastInsertId();
            if(!$insertedId) {
                throw new \Exception('Cannot retrieve a value from PDO->lastInsertId()');
            }
            $metadata->setPkValue($entity, $insertedId);
        }

        $repository = $this->getRepository(get_class($entity));
        $repository->attach($entity);

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

<?php

namespace EnoffSpb\EntityManager\Driver;

use EnoffSpb\EntityManager\Interfaces\DriverInterface;
use EnoffSpb\EntityManager\Repository\SqlGenericRepository;

use PDO;
use PDOStatement;

class SqlBaseDriver extends BaseDriver implements DriverInterface
{
    public string $identifierQuote = '"';
    public string $valueQuote = "'";

    private PDO $pdo;

    private ?PDOStatement $insertStmt = null;

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
        throw new \Exception('@TODO: Implement ' . __METHOD__ . ' method.');
    }

    public function delete(object $entity): void
    {
        throw new \Exception('@TODO: Implement ' . __METHOD__ . ' method.');
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}

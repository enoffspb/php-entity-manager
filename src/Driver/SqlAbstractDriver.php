<?php

namespace EnoffSpb\EntityManager\Driver;

use EnoffSpb\EntityManager\Interfaces\DriverInterface;

abstract class SqlAbstractDriver extends BaseDriver implements DriverInterface
{
    protected string $identifierQuote = '"';
    protected string $valueQuote = "'";

    protected \PDO $pdo;

    public function __construct(?string $dsn = null, ?string $user = null, ?string $password = null, array $options = [])
    {
        if($dsn === null) {
            throw new \Exception('Parameter $dsn of ' . get_class($this) . ' cannot be null.');
        }

        $this->pdo = new \PDO($dsn, $user, $password);
    }

    public function save(object $entity): bool
    {
        $metadata = $this->getMetadata(get_class($entity));

        $fields = $metadata->getValues($entity);

        $columns = $metadata->getMapping();

        $fields = [];
        $attribute = null;
        foreach($columns as $column) {
            $attribute = $column->attribute;

            if(isset($entity->$attribute)) {
                $fields[$column->field] = $entity->$attribute;
            }
        }

        $pk = $metadata->primaryKey;

        $tableName = $this->identifierQuote . $metadata->tableName . $this->identifierQuote;

        $queryColumns = [];
        $queryValues = [];
        foreach($fields as $columnName => $columnValue) {
            $queryColumns[] = $this->identifierQuote . $columnName . $this->identifierQuote;
//            $queryValues[] =
        }

        // @TODO build a query
        $sql = "INSERT INTO $tableName (" . implode(', ', $queryColumns) . ") VALUES ()";

        // @TODO execute the query

        throw new \Exception('@TODO WIP: implement ' . __METHOD__);

        $insertedId = null; // @TODO get inserted id
        if(!$insertedId) {
            return false;
        }

        if(!isset($entity->$pk)) {
            $entity->$pk = $insertedId;
        }

        $repository = $this->getRepository(get_class($entity));
        $repository->attach($entity);
    }

    public function update(object $entity): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(object $entity): bool
    {
        // TODO: Implement delete() method.
    }

    public function getGenericRepositoryClass(): string
    {
        // TODO: Implement getGenericRepositoryClass() method.
    }
}

<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Throwable;

/**
 * Class AbstractImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
abstract class AbstractImport implements ImportInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * TimeZoneImport constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return PromiseInterface
     */
    public function import(string $filePath, ?callable $progress = null): PromiseInterface
    {
        $self = $this;
        /** @var Promise $promise */
        $promise = (new Promise(function () use ($filePath, $progress, $self, &$promise) {
            $promise->resolve(
                $self->importData($filePath, $progress)
            );
        }));

        return $promise;
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    abstract protected function importData(string $filePath, ?callable $progress = null): bool;


    /**
     * @param string $className
     * @return string
     */
    protected function getTableName(string $className): string
    {
        return $this->em
            ->getClassMetadata($className)
            ->getTableName();
    }


    /**
     * @param string|null $val
     * @return string
     */
    protected function escape(?string $val): string
    {
        if ($val === null || strlen($val) === 0) {
            return 'NULL';
        }
        return $this->em->getConnection()->quote($val);
    }

    /**
     * @param string[] $queries
     * @return $this
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(array $queries): self
    {
        $this->em->getConnection()->executeStatement(
            implode("; \n", $queries)
        );
        return $this;
    }


    protected function isMySQL(): bool
    {
        return $this->em->getConnection()->getDatabasePlatform() instanceof MySQLPlatform;
    }


    protected function isPSQL(): bool
    {
        return $this->em->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform;
    }

    /**
     * @param QueryBuilder $insertSQL
     * @param string|null $pimaryKey
     * @return string
     * @throws \Doctrine\DBAL\Exception
     */
    protected function insertToReplace(QueryBuilder $insertSQL, ?string $primaryKey = null): string
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();
        if ($this->isMySQL()) {
            $sql = $insertSQL->getSQL();
            return preg_replace('/' . preg_quote('INSERT ', '/') . '/', 'REPLACE ', $sql, 1);
        }

        if ($this->isPSQL()) {
            $values = $insertSQL->getQueryPart("values");
            $sql = $insertSQL->getSQL();
            if (!$primaryKey) {
                reset($values);
                $primaryKey = key($values);
            }
            array_shift($values);
            $parts = [];
            foreach ($values as $column => $val) {
                $parts[] = "{$column} = {$val}";
            }
            $sql .= " ON CONFLICT ({$primaryKey}) DO UPDATE  SET " . implode(", ", $parts);
            return $sql;
        }

        throw $this->createUnsupportedDatabaseException();
    }


    /**
     * @throws Exception
     */
    protected function createUnsupportedDatabaseException(): Throwable
    {
        return new Exception("Unsupported database type");
    }

    /**
     * @param string $className
     * @return string[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getFieldNames(string $className): array
    {
        $metaData = $this->em->getClassMetadata($className);
        $result = [];
        foreach ($metaData->getFieldNames() as $name) {
            $result[$name] = $metaData->getColumnName($name);
        }
        foreach ($metaData->getAssociationNames() as $name) {
            if ($metaData->isSingleValuedAssociation($name)) {
                $result[$name] = $metaData->getSingleAssociationJoinColumnName($name);
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getTestValue(): ?string
    {
        return $this->getDefaultValue();
    }
}

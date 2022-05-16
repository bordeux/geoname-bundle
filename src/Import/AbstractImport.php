<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException;
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
     * @param string $sql
     * @param array $tableMap
     * @return string
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function parseQuery(string $sql, array $tableMap): string
    {
        $connection = $this->em->getConnection();
        $sql = preg_replace_callback('/({([a-zA-Z0-9_\-]+)})/', function ($matches) use ($tableMap) {
            $name = $matches[2] ?? null;
            $entity = $tableMap[$name] ?? null;
            if (empty($entity)) {
                throw new InvalidArgumentException("Unable to find map to `{$name}` table");
            }
            return $this->getTableName($entity);
        }, $sql);


        $sql = preg_replace_callback('/({([a-zA-Z0-9_\-]+):([a-zA-Z0-9_\-]+)})/', function ($matches) use ($tableMap, $connection) {
            $name = $matches[2] ?? null;
            $fieldName = $matches[3] ?? null;
            $entity = $tableMap[$name] ?? null;
            if (empty($entity) || empty($fieldName)) {
                throw new InvalidArgumentException("Unable to find map to `{$name}` table");
            }
            $fields = $this->getFieldNames($entity);
            return $connection->quoteIdentifier($fields[$fieldName]);
        }, $sql);

        return $sql;
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

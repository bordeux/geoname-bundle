<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use Generator;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use SplFileObject;

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
     * @param string $filePath
     * @return SplFileObject
     */
    protected function readTSV(string $filePath): SplFileObject
    {
        $file = new SplFileObject("animals.csv");
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl("\t");
        return $file;
    }

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
}

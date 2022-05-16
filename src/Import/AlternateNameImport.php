<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\AlternateName;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;

/**
 * Class AlternateNameImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class AlternateNameImport extends AbstractImport
{
    const BULK_SIZE = 1000;

    protected function getHeaders(): array
    {
        return [
            new Header(0, 'id', Header::TYPE_INT),
            new Header(1, 'geoname_id', Header::TYPE_INT),
            new Header(2, 'type'),
            new Header(3, 'value')
        ];
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders($this->getHeaders());

        $fieldsNames = $this->getFieldNames(AlternateName::class);
        $tableName = $this->getTableName(AlternateName::class);
        $geonamesTable = $this->getTableName(AlternateName::class);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            $buffer = [];
            foreach ($bulk as $item) {
                $id = (int)$item['id'];
                $geoNameId = (int)$item['geoname_id'];
                $type = $this->escape($item['type'] ?: AlternateName::TYPE_NONE);
                $value = $this->escape($item['value']);
                $buffer[] = $this->prepareSQL("
                                INSERT INTO {$tableName} ({$id}, {$fieldsNames['geoName']}, {$fieldsNames['type']}, {$fieldsNames['value']})
                                SELECT {$item['id']}, g.id, {$type}, {$value}
                                FROM {$geonamesTable} g
                                WHERE g.id = {$geoNameId}
                            ", $fieldsNames);
            }
            $this->save($buffer);
        }

        $connection->commit();

        return true;
    }

    /**
     * @param string $sql
     * @return string
     * @throws \Throwable
     */
    private function prepareSQL(string $sql, array $fieldsNames): string
    {
        $sql = trim($sql);
        if ($this->isMySQL()) {
            return preg_replace('/' . preg_quote('INSERT ', '/') . '/', 'REPLACE ', $sql, 1);
        }

        if ($this->isPSQL()) {
            return $sql . "
                ON CONFLICT ({$fieldsNames['id']})
                DO UPDATE SET {$fieldsNames['value']} = EXCLUDED.{$fieldsNames['value']},
                {$fieldsNames['type']} = EXCLUDED.{$fieldsNames['type']}";
        }

        throw $this->createUnsupportedDatabaseException();
    }


    public function getName(): string
    {
        return "AlternateNames";
    }

    public function getOptionName(): string
    {
        return "alternate-names";
    }

    public function getDescription(): string
    {
        return "Alternate names file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/alternateNamesV2.zip#alternateNamesV2.txt";
    }
}

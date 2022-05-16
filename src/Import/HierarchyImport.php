<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Hierarchy;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;

/**
 * Class HierarchyImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 *
 * @todo: Improve it. Right now blocker is not unique id in CSV file, so is hard to do REPLACE query on ysqml & psql
 */
class HierarchyImport extends AbstractImport
{
    const BULK_SIZE = 1000;

    protected function getHeaders(): array
    {
        return [
            new Header(0, 'parent_id', Header::TYPE_INT),
            new Header(1, 'child_id', Header::TYPE_INT),
            new Header(2, 'type', Header::TYPE_STRING)
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

        $fieldsNames = $this->getFieldNames(Hierarchy::class);
        $tableName = $this->getTableName(Hierarchy::class);
        $geoNamesTable = $this->getTableName(GeoName::class);
        $connection = $this->em->getConnection();

        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            $buffer = [];
            foreach ($bulk as $item) {
                $parentId = (int)$item['parent_id'];
                $childId = (int)$item['child_id'];
                $buffer[] = "
                    INSERT INTO {$tableName} ({$fieldsNames['parent']}, {$fieldsNames['child']}, {$fieldsNames['type']})
                    SELECT g.id, g2.id, {$this->escape($item['type'])}
                    FROM {$geoNamesTable} g, {$geoNamesTable} g2
                    WHERE g.id = {$parentId} AND g2.id = {$childId}
                    LIMIT 1;
                ";
            }
            $this->save($buffer);
        }
        $connection->commit();
        return true;
    }


    public function getName(): string
    {
        return "Hierarchy";
    }

    public function getOptionName(): string
    {
        return "hierarchy";
    }

    public function getDescription(): string
    {
        return "Hierarchy file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/hierarchy.zip#hierarchy.txt";
    }
}

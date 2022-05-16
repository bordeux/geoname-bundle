<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

/**
 * Class HierarchyImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class HierarchyImport extends GeoNameImport
{
    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {

        $avrOneLineSize = 29.4;
        $connection = $this->em->getConnection();
        $fileInside = basename($filePath, ".zip") . '.txt';
        $filePath = "zip://{$filePath}#{$fileInside}";
        $tsvFile = $this->readTSV($filePath);
        $max = (int)$tsvFile->getSize() / $avrOneLineSize;
        $tableName = $this->getTableName(HierarchyImport::class);

        $connection->beginTransaction();

        $pos = 0;

        $buffer = [];

        $queryBuilder = $connection->createQueryBuilder()
            ->insert($tableName);

        foreach ($tsvFile as $row) {
            $row = array_map('trim', $row);
            if (!is_numeric($row[0] ?? null)) {
                continue;
            }

            $query = $queryBuilder->values([

            ]);


            $buffer[] = $this->insertToReplace($query);

            $pos++;

            if ($pos % static::BATCH_SIZE) {
                $this->save($buffer);
                $buffer = [];
                is_callable($progress) && $progress(($pos) / $max);
            }
        }
        !empty($buffer) && $this->save($buffer);
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

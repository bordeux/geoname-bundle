<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\AlternateName;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

class AlternateNameImport extends GeoNameImport
{
    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders([
            new TextFileReader\Header(0, 'code'),
            new TextFileReader\Header(1, 'name'),
            new TextFileReader\Header(2, 'asci_name')
        ]);


        $avrOneLineSize = 29.4;

        $connection = $this->em->getConnection();

        $fileInside = basename($filePath, ".zip") . '.txt';
        $filePath = "zip://{$filePath}#{$fileInside}";
        $tsvFile = $this->readTSV($filePath);
        $max = (int)($tsvFile->getSize() / $avrOneLineSize);

        $fieldsNames = $this->getFieldNames(AlternateName::class);

        $tableName = $this->em
            ->getClassMetadata(GeoName::class)
            ->getTableName();

        $connection->beginTransaction();

        $buffer = [];

        $queryBuilder = $connection->createQueryBuilder()
            ->insert($tableName);


        !empty($buffer) && $this->save($buffer);
        $connection->commit();

        return true;
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

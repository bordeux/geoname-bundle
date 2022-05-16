<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class GeoNameImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class GeoNameImport extends AbstractImport
{
    const BATCH_SIZE = 10000;

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
        $max = (int)($tsvFile->getSize() / $avrOneLineSize);

        $fieldsNames = $this->getFieldNames(GeoName::class);
        $geoNameTableName = $this->getTableName(GeoName::class);
        $timezoneTableName = $this->getTableName(Timezone::class);
        $administrativeTableName = $this->getTableName(Administrative::class);

        $connection->beginTransaction();

        $pos = 0;

        $buffer = [];

        $queryBuilder = $connection->createQueryBuilder()
            ->insert($geoNameTableName);

        foreach ($tsvFile as $csv) {
            if (!is_numeric($csv[0] ?? null)) {
                continue;
            }

            list(
                $geoNameId,
                $name,
                $asciiName,
                $alternateNames,
                $latitude,
                $longitude,
                $featureClass,
                $featureCode,
                $countryCode,
                $cc2,
                $admin1Code,
                $admin2Code,
                $admin3Code,
                $admin4Code,
                $population,
                $elevation,
                $dem,
                $timezone,
                $modificationDate
                ) = array_map('trim', $csv);

            if (!preg_match('/^\d{4}\-\d{2}-\d{2}$/', $modificationDate)) {
                continue;
            }

            $data = [
                $fieldsNames['id'] => (int)$geoNameId, //must be as first!
                $fieldsNames['name'] => $this->escape($name),
                $fieldsNames['asciiName'] => $this->escape($asciiName),
                $fieldsNames['latitude'] => $this->escape($latitude),
                $fieldsNames['longitude'] => $this->escape($longitude),
                $fieldsNames['featureClass'] => $this->escape($featureClass),
                $fieldsNames['featureCode'] => $this->escape($featureCode),
                $fieldsNames['countryCode'] => $this->escape($countryCode),
                $fieldsNames['cc2'] => $this->escape($cc2),
                $fieldsNames['population'] => $this->escape($population),
                $fieldsNames['elevation'] => $this->escape($elevation),
                $fieldsNames['dem'] => $this->escape($dem),
                $fieldsNames['modificationDate'] => $this->escape($modificationDate),
                $fieldsNames['timezone'] => $timezone ? "(SELECT id FROM {$timezoneTableName} WHERE timezone  =  " . $this->escape($timezone) . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin1'] => $admin1Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin2'] => $admin2Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$admin2Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin3'] => $admin3Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$admin3Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin4'] => $admin4Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$admin4Code}") . " LIMIT 1)" : 'NULL',
            ];


            $query = $queryBuilder->values($data);
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


    /**
     * @param QueryBuilder $insertSQL
     * @return string
     * @throws \Doctrine\DBAL\Exception
     */
    protected function insertToReplace(QueryBuilder $insertSQL): string
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();
        if ($platform instanceof MySQLPlatform) {
            $sql = $insertSQL->getSQL();
            return preg_replace('/' . preg_quote('INSERT ', '/') . '/', 'REPLACE ', $sql, 1);
        }

        if ($platform instanceof PostgreSQLPlatform) {
            $values = $insertSQL->getQueryPart("values");
            $sql = $insertSQL->getSQL();
            reset($values);
            $index = key($values);
            array_shift($values);
            $parts = [];
            foreach ($values as $column => $val) {
                $parts[] = "{$column} = {$val}";
            }
            $sql .= " ON CONFLICT ({$index}) DO UPDATE  SET " . implode(", ", $parts);
            return $sql;
        }

        throw new \Exception("Unsupported database type");
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


    /**
     * @return string[]
     * @author Chris Bednarczyk <chris@tourradar.com>
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

    public function getName(): string
    {
        return "GeoNames";
    }

    public function getOptionName(): string
    {
        return "geonames";
    }

    public function getDescription(): string
    {
        return "Geonames file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/allCountries.zip#allCountries.txt";
    }
}

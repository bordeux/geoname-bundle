<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
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
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders([
            new TextFileReader\Header(0, 'geoname_id'),
            new TextFileReader\Header(1, 'name'),
            new TextFileReader\Header(2, 'asci_name'),
            new TextFileReader\Header(3, 'alternate_names'),
            new TextFileReader\Header(4, 'latitude'),
            new TextFileReader\Header(5, 'longitude'),
            new TextFileReader\Header(6, 'feature_class'),
            new TextFileReader\Header(7, 'feature_code'),
            new TextFileReader\Header(8, 'country_code'),
            new TextFileReader\Header(9, 'cc2'),
            new TextFileReader\Header(10, 'admin1_code'),
            new TextFileReader\Header(11, 'admin2_code'),
            new TextFileReader\Header(12, 'admin3_code'),
            new TextFileReader\Header(13, 'admin4_code'),
            new TextFileReader\Header(14, 'population'),
            new TextFileReader\Header(15, 'elevation'),
            new TextFileReader\Header(16, 'dem'),
            new TextFileReader\Header(17, 'timezone'),
            new TextFileReader\Header(18, 'modification_date'),
        ]);

        $fieldsNames = $this->getFieldNames(GeoName::class);
        $geoNameTableName = $this->getTableName(GeoName::class);
        $timezoneTableName = $this->getTableName(Timezone::class);
        $administrativeTableName = $this->getTableName(Administrative::class);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        foreach ($reader->process(static::BATCH_SIZE) as $bulk) {
            $buffer = [];
            foreach ($bulk as $item) {
                $countryCode = $item['country_code'];
                $admin1Code = $item['admin1_code'];
                $modificationDate = $item['modification_date'];

                if (!preg_match('/^\d{4}\-\d{2}-\d{2}$/', $modificationDate)) {
                    continue;
                }

                $data = [
                    $fieldsNames['id'] => $item['geoname_id'], //must be as first!
                    $fieldsNames['name'] => $this->escape($item['name']),
                    $fieldsNames['asciiName'] => $this->escape($item['asci_name']),
                    $fieldsNames['latitude'] => $this->escape($item['latitude']),
                    $fieldsNames['longitude'] => $this->escape($item['longitude']),
                    $fieldsNames['featureClass'] => $this->escape($item['feature_class']),
                    $fieldsNames['featureCode'] => $this->escape($item['feature_code']),
                    $fieldsNames['countryCode'] => $this->escape($countryCode),
                    $fieldsNames['cc2'] => $this->escape($item['cc2']),
                    $fieldsNames['population'] => $this->escape($item['population']),
                    $fieldsNames['elevation'] => $this->escape($item['elevation']),
                    $fieldsNames['dem'] => $this->escape($item['dem']),
                    $fieldsNames['modificationDate'] => $this->escape($modificationDate),
                    $fieldsNames['timezone'] => $item['timezone'] ? "(SELECT id FROM {$timezoneTableName} WHERE timezone  =  " . $this->escape($item['timezone']) . " LIMIT 1)" : 'NULL',
                    $fieldsNames['admin1'] => $item['admin1_code'] ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}") . " LIMIT 1)" : 'NULL',
                    $fieldsNames['admin2'] => $item['admin2_code'] ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$item['admin2_code']}") . " LIMIT 1)" : 'NULL',
                    $fieldsNames['admin3'] => $item['admin3_code'] ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$item['admin3_code']}") . " LIMIT 1)" : 'NULL',
                    $fieldsNames['admin4'] => $item['admin4_code'] ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->escape("{$countryCode}.{$admin1Code}.{$item['admin4_code']}") . " LIMIT 1)" : 'NULL',
                ];

                $query = $connection->createQueryBuilder()
                    ->insert($geoNameTableName)->values($data);
                $buffer[] = $this->insertToReplace($query);
            }
            $this->save($buffer);
        }

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

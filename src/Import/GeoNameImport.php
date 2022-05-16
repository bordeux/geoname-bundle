<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;

/**
 * Class GeoNameImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class GeoNameImport extends AbstractImport
{
    const BULK_SIZE = 10000;

    /**
     * @return Header[]
     */
    protected function getHeaders(): array
    {
        return [
            new Header(0, 'geoname_id', Header::TYPE_INT),
            new Header(1, 'name'),
            new Header(2, 'asci_name'),
            new Header(3, 'alternate_names'),
            new Header(4, 'latitude', Header::TYPE_FLOAT),
            new Header(5, 'longitude', Header::TYPE_FLOAT),
            new Header(6, 'feature_class'),
            new Header(7, 'feature_code'),
            new Header(8, 'country_code'),
            new Header(9, 'cc2'),
            new Header(10, 'admin1_code'),
            new Header(11, 'admin2_code'),
            new Header(12, 'admin3_code'),
            new Header(13, 'admin4_code'),
            new Header(14, 'population', Header::TYPE_INT),
            new Header(15, 'elevation', Header::TYPE_INT),
            new Header(16, 'dem', Header::TYPE_INT),
            new Header(17, 'timezone'),
            new Header(18, 'modification_date'),
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

        $fieldsNames = $this->getFieldNames(GeoName::class);
        $geoNameTableName = $this->getTableName(GeoName::class);
        $timezoneTableName = $this->getTableName(Timezone::class);
        $administrativeTableName = $this->getTableName(Administrative::class);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            $buffer = [];
            foreach ($bulk as $item) {
                $countryCode = $item['country_code'];
                $admin1Code = $item['admin1_code'];
                $modificationDate = $item['modification_date'];

                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $modificationDate)) {
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

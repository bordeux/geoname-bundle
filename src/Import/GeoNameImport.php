<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Entity\AlternateName;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;
use Doctrine\DBAL\ParameterType;

/**
 * Class GeoNameImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class GeoNameImport extends AbstractImport
{
    const BULK_SIZE = 5000;

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

        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            $buffer = [];
            foreach ($bulk as $item) {
                $countryCode = $item['country_code'];
                $admin1Code = $item['admin1_code'];
                $item['admin1'] = "{$countryCode}.{$admin1Code}";
                $item['admin2'] = "{$countryCode}.{$admin1Code}.{$item['admin2_code']}";
                $item['admin3'] = "{$countryCode}.{$admin1Code}.{$item['admin3_code']}";
                $item['admin4'] = "{$countryCode}.{$admin1Code}.{$item['admin4_code']}";
                $buffer[] = $item;
            }

            $this->insert($buffer);
        }

        $connection->commit();

        return true;
    }

    protected function insert(array $buffer): void
    {
        $pseudoSql = "
            INSERT INTO {geoname} (
                {geoname:id},
                {geoname:name},
                {geoname:asciiName},
                {geoname:latitude},
                {geoname:longitude},
                {geoname:featureClass},
                {geoname:featureCode},
                {geoname:countryCode},
                {geoname:cc2},
                {geoname:admin1},
                {geoname:admin2},
                {geoname:admin3},
                {geoname:admin4},
                {geoname:population},
                {geoname:elevation},
                {geoname:dem},
                {geoname:timezone},
                {geoname:modificationDate}
            )
            SELECT
                (_v.value->>'geoname_id')::integer,
                (_v.value->>'name'),
                (_v.value->>'ascii_name'),
                (_v.value->>'latitude')::double precision,
                (_v.value->>'longitude')::double precision,
                (_v.value->>'feature_class'),
                (_v.value->>'feature_code'),
                (_v.value->>'country_code'),
                (_v.value->>'cc2'),
                a1.id,
                a2.id,
                a3.id,
                a4.id,
                (_v.value->>'population')::bigint,
                (_v.value->>'elevation')::integer,
                (_v.value->>'dem')::integer,
                t.{timezone:id},
                (_v.value->>'modification_date')::date

            FROM json_array_elements( (:data)::json ) _v
            LEFT JOIN {administrative} a1 ON a1.{administrative:code} = (_v.value->>'admin1')
            LEFT JOIN {administrative} a2 ON a2.{administrative:code} = (_v.value->>'admin2')
            LEFT JOIN {administrative} a3 ON a3.{administrative:code} = (_v.value->>'admin3')
            LEFT JOIN {administrative} a4 ON a4.{administrative:code} = (_v.value->>'admin4')
            LEFT JOIN {timezone} t ON t.{timezone:timezone} = (_v.value->>'timezone')
            ON CONFLICT ({geoname:id}) DO UPDATE  SET
                {geoname:name} = EXCLUDED.{geoname:name},
                {geoname:asciiName} = EXCLUDED.{geoname:asciiName},
                {geoname:latitude} = EXCLUDED.{geoname:latitude},
                {geoname:longitude} = EXCLUDED.{geoname:longitude},
                {geoname:featureClass} = EXCLUDED.{geoname:featureClass},
                {geoname:featureCode} = EXCLUDED.{geoname:featureCode},
                {geoname:countryCode} = EXCLUDED.{geoname:countryCode},
                {geoname:cc2} = EXCLUDED.{geoname:cc2},
                {geoname:admin1} = EXCLUDED.{geoname:admin1},
                {geoname:admin2} = EXCLUDED.{geoname:admin2},
                {geoname:admin3} = EXCLUDED.{geoname:admin3},
                {geoname:admin4} = EXCLUDED.{geoname:admin4},
                {geoname:population} = EXCLUDED.{geoname:population},
                {geoname:elevation} = EXCLUDED.{geoname:elevation},
                {geoname:dem} = EXCLUDED.{geoname:dem},
                {geoname:timezone} = EXCLUDED.{geoname:timezone},
                {geoname:modificationDate} = EXCLUDED.{geoname:modificationDate}
        ";

        $sql = $this->parseQuery($pseudoSql, [
            "geoname" => GeoName::class,
            "administrative" => Administrative::class,
            "timezone" => Timezone::class,
        ]);

        $this->em->getConnection()->executeStatement(
            $sql,
            [
                'data' => json_encode($buffer)
            ],
            [
                'data' =>  ParameterType::STRING
            ]
        );
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

    public function getTestValue(): ?string
    {
        return "https://download.geonames.org/export/dump/PL.zip#PL.txt";
    }
}

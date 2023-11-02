<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\AlternateName;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;
use Doctrine\DBAL\ParameterType;

/**
 * Class AlternateNameImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class AlternateNameImport extends AbstractImport
{
    const BULK_SIZE = 5000;

    protected function getHeaders(): array
    {
        return [
            new Header(0, 'id', Header::TYPE_INT),
            new Header(1, 'geoname_id', Header::TYPE_INT),
            new Header(2, 'type'),
            new Header(3, 'value'),
            new Header(4, 'prefered', Header::TYPE_STRING_OR_NAN)
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
            $this->insert(array_map(function (array $item) {
                $item['type'] = $item['type'] ?: AlternateName::TYPE_NONE;
                return $item;
            }, $bulk));
        }

        $connection->commit();

        return true;
    }

    protected function insert(array $buffer): void
    {
        $pseudoSql = "
            INSERT INTO {alternate_name} (
                {alternate_name:id},
                {alternate_name:geoName},
                {alternate_name:type},
                {alternate_name:value},
                {alternate_name:prefered}
            )
            SELECT
                (_v.value->>'id')::integer,
                g.{geoname:id},
                (_v.value->>'type'),
                (_v.value->>'value'),
                (_v.value->>'prefered')
            FROM json_array_elements( (:data)::json ) _v
            JOIN {geoname} g ON g.{geoname:id} = (_v.value->>'geoname_id')::integer
            ON CONFLICT ({geoname:id}) DO UPDATE  SET
                {alternate_name:geoName} = EXCLUDED.{alternate_name:geoName},
                {alternate_name:type} = EXCLUDED.{alternate_name:type},
                {alternate_name:value} = EXCLUDED.{alternate_name:value},
                {alternate_name:prefered} = EXCLUDED.{alternate_name:prefered}
        ";

        $sql = $this->parseQuery($pseudoSql, [
            "geoname" => GeoName::class,
            "alternate_name" => AlternateName::class
        ]);

        $this->em->getConnection()->executeStatement(
            $sql,
            [
                'data' => json_encode($buffer)
            ],
            [
                'data' => ParameterType::STRING
            ]
        );
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

    public function getTestValue(): ?string
    {
        return "http://download.geonames.org/export/dump/alternatenames/PL.zip#PL.txt";
    }
}

<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Country;
use Bordeux\Bundle\GeoNameBundle\Entity\GeoName;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;

/**
 * Class CountryImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class CountryImport extends AbstractImport
{
    protected const BULK_SIZE = 1000;

    /**
     * @return Header[]
     */
    protected function getHeaders(): array
    {
        return [
            new Header(0, 'iso'),
            new Header(1, 'iso3'),
            new Header(2, 'iso_numeric'),
            new Header(3, 'fips'),
            new Header(4, 'name'),
            new Header(5, 'capital'),
            new Header(6, 'area', Header::TYPE_INT),
            new Header(7, 'population', Header::TYPE_INT),
            new Header(8, 'continent'),
            new Header(9, 'tld'),
            new Header(10, 'currency_code'),
            new Header(11, 'currency_name'),
            new Header(12, 'phone'),
            new Header(13, 'postal_format'),
            new Header(14, 'postal_regex'),
            new Header(15, 'languages'),
            new Header(16, 'geoname_id', Header::TYPE_INT)
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

        $countryRepo = $this->em->getRepository(Country::class);
        $connection = $this->em->getConnection();

        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            foreach ($bulk as $item) {
                $id = $item['geoname_id'];
                $object = $countryRepo->find($id);
                if (!$object) {
                    $object = new Country($id);
                    $this->em->persist($object);
                }
                $object->setIso($item['iso']);
                $object->setIso3($item['iso3']);
                $object->setIsoNumeric((int)$item['iso_numeric']);
                $object->setFips($item['fips']);
                $object->setName($item['name']);
                $object->setCapital($item['capital']);
                $object->setArea($item['area']);
                $object->setPopulation($item['population']);
                $object->setTld($item['tld']);
                $object->setCurrency($item['currency_code']);
                $object->setCurrencyName($item['currency_name']);
                $phone = explode(" and ", $item['phone'] ?? '');
                $phone = reset($phone);
                $phone = preg_replace('/\D/', '', $phone);
                $object->setPhonePrefix(((int)$phone) ?: null);
                $object->setPostalFormat($item['postal_format'] ?: null);
                $object->setPostalRegex($item['postal_regex'] ?: null);
                $object->setLanguages(explode(",", $item['languages']) ?: null);
                $object->setGeoName(
                    $this->em->getRepository(GeoName::class)
                        ->find($id)
                );
            }
            $this->em->flush();
            $this->em->clear();
        }
        $connection->commit();

        $geoNameTableName = $this->em
            ->getClassMetadata(GeoName::class)
            ->getTableName();

        $countryTableName = $this->em
            ->getClassMetadata(Country::class)
            ->getTableName();

        $sql = <<<UpdateSelect
            UPDATE
                {$geoNameTableName}
            SET
                country_id = (
                    SELECT
                        id
                    FROM
                        {$countryTableName} _c
                    WHERE
                       _c.iso = {$geoNameTableName}.country_code
                    LIMIT 1
                )
UpdateSelect;

        $this->em
            ->getConnection()
            ->executeStatement($sql);
        return true;
    }

    public function getName(): string
    {
        return "Countries";
    }

    public function getOptionName(): string
    {
        return "countries";
    }

    public function getDescription(): string
    {
        return "Countries file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/countryInfo.txt";
    }
}

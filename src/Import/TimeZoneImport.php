<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader\Header;

/**
 * Class TimeZoneImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class TimeZoneImport extends AbstractImport
{
    protected const BULK_SIZE = 1000;

    /**
     * @return Header[]
     */
    protected function getHeaders(): array
    {
        return [
            new Header(0, 'country_code'),
            new Header(1, 'timezone'),
            new Header(2, 'gmt_offset', Header::TYPE_FLOAT),
            new Header(3, 'dst_offset', Header::TYPE_FLOAT),
            new Header(4, 'raw_offset', Header::TYPE_FLOAT),
        ];
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    protected function importData($filePath, callable $progress = null): bool
    {
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders($this->getHeaders())
            ->skipLines(1);

        $timezoneRepository = $this->em->getRepository(Timezone::class);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            foreach ($bulk as $item) {
                $timezone = $item['timezone'];
                $object = $timezoneRepository->findOneBy(['timezone' => $timezone]) ?: new Timezone();
                $object->setTimezone($timezone);
                $object->setCountryCode($item['country_code']);
                $object->setGmtOffset($item['gmt_offset']);
                $object->setDstOffset($item['dst_offset']);
                $object->setRawOffset($item['raw_offset']);
                !$object->getId() && $this->em->persist($object);
            }

            $this->em->flush();
            $this->em->clear();
        }
        $connection->commit();
        return true;
    }

    public function getName(): string
    {
        return "TimeZones";
    }

    public function getOptionName(): string
    {
        return "timezones";
    }

    public function getDescription(): string
    {
        return "Timezones file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/timeZones.txt";
    }
}

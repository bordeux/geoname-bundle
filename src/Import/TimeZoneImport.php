<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;

/**
 * Class TimeZoneImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class TimeZoneImport extends AbstractImport
{
    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    protected function importData($filePath, callable $progress = null): bool
    {
        $tsvFile = $this->readTSV($filePath);
        $max = $tsvFile->getSize();
        $timezoneRepository = $this->em->getRepository(Timezone::class);
        $pos = 0;
        foreach ($tsvFile as $row) {
            $pos++;
            if ($pos <= 1) {
                continue;
            }
            $row = array_map('trim', $row);
            list(
                $countryCode,
                $timezone,
                $gmtOffset,
                $dstOffset,
                $rawOffset
                ) = $row;


            $object = $timezoneRepository->findOneBy(['timezone' => $timezone]) ?: new Timezone();
            $object->setTimezone($timezone);
            $object->setCountryCode($countryCode);
            $object->setGmtOffset((float)$gmtOffset);
            $object->setDstOffset((float)$dstOffset);
            $object->setRawOffset((float)$rawOffset);
            !$object->getId() && $this->em->persist($object);
            is_callable($progress) && $progress(($pos++) / $max);
        }

        $this->em->flush();
        $this->em->clear();

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

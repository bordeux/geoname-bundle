<?php


namespace Bordeux\Bundle\GeoNameBundle\Import;


use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use SplFileObject;

/**
 * Class TimeZoneImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class TimeZoneImport implements ImportInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * TimeZoneImport constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return PromiseInterface
     */
    public function import(string $filePath, ?callable $progress = null): PromiseInterface
    {
        $self = $this;
        /** @var Promise $promise */
        $promise = (new Promise(function () use ($filePath, $progress, $self, &$promise) {
            $promise->resolve(
                $self->_import($filePath, $progress)
            );
        }));

        return $promise;
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    protected function _import($filePath, callable $progress = null)
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl("\t");
        $file->seek(PHP_INT_MAX);
        $max = $file->key();
        $file->seek(1); //skip header

        $timezoneRepository = $this->em->getRepository("BordeuxGeoNameBundle:Timezone");

        $pos = -1;

        foreach ($file as $row) {
            if ($pos == -1) {
                $pos++;
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

}

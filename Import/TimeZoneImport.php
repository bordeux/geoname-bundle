<?php


namespace Bordeux\Bundle\GeoNameBundle\Import;


use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Promise\Promise;
use SplFileObject;

/**
 * Class TimeZoneImport
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class TimeZoneImport implements ImportInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TimeZoneImport constructor.
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @param  string $filePath
     * @param callable|null $progress
     * @return Promise|\GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function import($filePath, callable $progress = null)
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
            if($pos == -1){
                $pos++;
                continue;
            }
            $row = array_map('trim',$row);
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
            $object->setGmtOffset((float) $gmtOffset);
            $object->setDstOffset((float) $dstOffset);
            $object->setRawOffset((float) $rawOffset);

            !$object->getId() && $this->em->persist($object);
            is_callable($progress) && $progress(($pos++) / $max);
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }

}
